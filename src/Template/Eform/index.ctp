<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

$this->Eform = TableRegistry::get('Eform');
$this->Users = TableRegistry::get('Users');

$session = $this->request->getSession();
$eform_message = "";
$message_show_hide = "hidden";
if($session->read('eform_message') != "") {
    $eform_message = $session->read('eform_message');
    $session->write('eform_message', '');
    $message_show_hide = "";
}

$session_usertype = explode(",", $session_user['usertype']);
?>
<?php if(isset($eform_message) && $eform_message != "") { ?>
<div class="alert alert-success alert-hover" id="flash_success">
    <button class="close">x</button>
    <?php echo $eform_message; ?>
</div>
<?php } ?>

<div class="eform-list-page">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive m-t-10">
                        <div class="row float-right m-0">
                        <?php if(in_array('Admin',$session_usertype)) { ?>
                        <h4><button type="button" class="btn waves-effect waves-light btn-primary eFormAddModule mr-3" data-title="">Add  Eform</button></h4>
                        <?php } ?>
                        <h4><?php echo $this->Html->link(__('<i class="fab fa-wpforms mr-1"></i> eForms Responses'), ['action' => '../eform-response'], ['class' => 'btn waves-effect waves-light btn-info', 'escape' => false]) ?></h4>
                    </div>                                        
                        <table id="eformTable" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>      
                                    <th style="display: none;">#</th>                                   
                                    <th>Name</th>                                    
                                    <th>Date</th>
                                    <th class="text-center"><?= __('Actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($eform as $key => $n): ?>
                                    <?php $nid = ConnectionManager::userIdEncode($n->id); ?>
                                    <tr>
                                        <td style="display: none;">
                                       <?php
                                            echo $key+1;
                                       ?>
                                       </td>
                                       <td>
                                       <?php
                                            $name = $n->formName;
                                            echo $this->Html->link(__($name), ['controller' => 'Eform', 'action' => 'edit', $nid], ['escape' => false]);
                                       ?>
                                       </td>
                                       <td data-order="<?php echo date("Y-m-d h:i:s", strtotime($n->date)); ?>"><?php echo date("d/m/Y h:i:s", strtotime($n->date)) ?></td>
                                       <td class="actions">
                                           <div class="actions-div">

                                               <a href="javascript:void(0);" data-title="<?php echo $nid; ?>" class="viewEform1" onclick="viewEformModalModule('<?php echo $nid; ?>','<?php echo $name; ?>');" data-backdrop="static" data-keyboard="false" data-toggle="tooltip" title='View'><i class="fa fa-eye"></i></a>

                                               <?= $this->Html->link(__('<i class="fa fa-edit green-txt"></i>'), ['controller' => 'Eform', 'action' => 'edit', $nid], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Edit']) ?> 

                                               <?= $this->Html->link(__('<i class="fa fa-trash red-txt"></i>'), ['action' => 'delete', $nid], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Remove', 'class' => 'delete_btn', 'data-link' => 'remove_btn' . $nid]) ?> 

                                               <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $nid], ['escape' => false, 'id' => 'remove_btn' . $nid, 'style' => 'display:none;']) ?>

                                               <?= $this->Html->link(__('<i class="fa fa-copy purple-txt"></i>'), ['action' => 'copy', $nid], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Copy', 'class' => 'copy_btn', 'data-link' => 'copy_btn' . $nid]) ?> 

                                               <?= $this->Form->postLink(__('Copy'), ['action' => 'copy', $nid], ['escape' => false, 'id' => 'copy_btn' . $nid, 'style' => 'display:none;']) ?>

                                               <button type="button" class="btn btn-sm waves-effect waves-light btn-info mx-1 sendEformModalModule" data-title="<?php echo $nid; ?>" onclick="sendEformModalModule('<?php echo $nid; ?>', 'eform');">Send</a>    
                                               
                                               <button type="button" class="btn btn-sm waves-effect waves-light btn-info mx-1 viewSendEformModalModule" data-title="<?php echo $nid; ?>" onclick="viewSendEformModalModule('<?php echo $nid; ?>', 'eform');">View Sent</a>   
                                                
                                               <button type="button" class="btn btn-sm waves-effect waves-light btn-info mx-1 viewReceivedDocumentModalModule" data-title="<?php echo $nid; ?>" onclick="viewReceivedDocumentModalModule('<?php echo $nid; ?>', 'eform');">View Received</a>    
                                           </div>
                                       </td>
                                    </tr>
                                <?php $k++; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>    
    </div>
</div>   

<!--Copy Modal-->
<div id="copy_modal" class="modal " tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm">
        <div class="modal-content" >
            <div class="modal-body">                
                <p class="text-center">Are you sure you want to create copy of this Eform?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary waves-effect" id="copy_yes_btn">Yes</button>
                <button class="btn btn-danger waves-effect" id="copy_no_btn" data-dismiss="modal">No</button>
            </div>            
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div id="delete_modal" class="modal " tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm">
        <div class="modal-content" >
            <div class="modal-body">                
                <p class="text-center">Are you sure you want to delete this Eform?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary waves-effect" id="yes_btn">Yes</button>
                <button class="btn btn-danger waves-effect" id="no_btn" data-dismiss="modal">No</button>
            </div>            
        </div>
    </div>
</div>

<!-- View eForm modal -->
<div class="modal " id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">                
                <h4 class="modal-title headname" id="myModalLabel">View eForm</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">                    
                <div class="form-group">                            
                    <div id="qrCodeviewtext" style="display:none;"></div> 
                    <div id="qrCodeview" class="d-flex justify-content-center"></div>
                 </div>
                 <div class="form-group" id="contentBody"></div>                 
            </div>
            <div class="modal-footer">                
                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!--View Send Modal -->
<div class="modal " id="viewSendEformModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form" action="" id="ViewSendEformData" method="POST" class="mb-0">
                <input type="hidden" id="vieweid" name="vieweid" value=""/>
                <input type="hidden" id="viewType" name="viewType" value="eform"/>
                <div class="modal-header">                
                    <h4 class="modal-title" id="myModalLabel">View Sent</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center justify-content-around"> 
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewSendEforminlineRadio3" value="viewackess" class="with-gap radio-col-light-blue"/>
                            <label for="viewSendEforminlineRadio3">AKcess ID</label>
                        </div>
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewSendEforminlineRadio1" value="viewemail" class="with-gap radio-col-light-blue"/>
                            <label for="viewSendEforminlineRadio1">Email</label>
                        </div>
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewSendEforminlineRadio2" value="viewphone" class="with-gap radio-col-light-blue"/>
                            <label for="viewSendEforminlineRadio2">Phone</label>
                        </div>
                    </div>
                    <div>
                        <div class="form-group viewackess">                            
                            <div class="table-responsive">
                               <table class="table table-hover table-striped table-bordered" id="viewSentAkcess">
                                   <thead>
                                       <tr>
                                           <th>AKcess ID</th>
                                           <th>Status</th>
                                           <th>Date</th>                                
                                       </tr>
                                   </thead>
                               </table>
                            </div>             
                        </div>
                        <div class="form-group viewemail">                            
                            <div class="table-responsive">
                               <table class="table table-hover table-striped table-bordered" id="viewSentEmail">
                                   <thead>
                                       <tr>
                                           <th>Email ID</th>
                                           <th>Status</th>
                                           <th>Date</th>                                
                                       </tr>
                                   </thead>
                               </table>
                            </div>                           
                        </div>
                        <div class="form-group viewphone">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped table-bordered" id="viewSentPhone">
                                    <thead>
                                        <tr>
                                            <th scope="col">Phone No</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Date</th>                                
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>                        
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Send eform Modal -->
<div class="modal " id="sendEformModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form" action="" id="SendEformData" method="POST" class="mb-0">
                <input type="hidden" id="eid" name="eid" value=""/>               
                <div class="modal-header">                
                    <h4 class="modal-title" id="myModalLabel">Send eForm</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center justify-content-around">
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="inlineRadio3" value="ackess" class="with-gap radio-col-light-blue"/>
                            <label for="inlineRadio3">AKcess ID</label>
                        </div>
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="email" class="with-gap radio-col-light-blue"/>
                            <label for="inlineRadio1">Email</label>
                        </div>
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="phone" class="with-gap radio-col-light-blue"/>
                            <label for="inlineRadio2">Phone</label>
                        </div>
                    </div>
                    <div>
                        <div class="form-group ackess">
                            <select id="ackess" multiple="multiple" class="global-tokenize input_tokenize select2 m-b-10 select2-multiple" name="ackess[]">
                                <?php                                
                                foreach ($users as $user) {
                                    $akcessId = '';
                                    if (isset($user->akcessId) && $user->akcessId != "") {                                        
                                        $name = isset($user->name) ? $user->name : "";
                                        $akcessId = isset($user->akcessId) ? $user->akcessId : "";
                                        $name = $name . " ( " . $akcessId . " ) ";  
                                        if (!empty($akcessId)) { ?>
                                            <option value="<?php echo $akcessId; ?>"><?php echo $name; ?></option>
                                        <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                            <p style="color:green">Type AKcess ID and press Enter to validate.</p>
                        </div>
                        <div class="form-group email">      
                            <select   id="email_search" name="email_search[]" placeholder="Enter Email" class="global-tokenize select2-multiple form-control input_tokenize"> 
			     <?php
                                foreach ($users as $user) {
                                    $email = '';
                                    if (isset($user->email) && $user->email != "") {
                                        $email = $user->email;
                                        $name = $user->name . " ( " . $user->email . " ) ";  
                                        if (isset($email)) {
                                            ?>
                                            <option value="<?php echo $user->email; ?>"><?php echo $name; ?></option>
                                        <?php
                                        }
                                    }
                                }
                                ?>
			    </select>  
			    <p style="color:green">Type the email address and press Enter to validate.</p>        
                            <input type="hidden" id="email" name="email" value="">
                        </div>
                        <div class="form-group phone list_wrapper">
							 <div class="form-row align-items-center">
                           <div class="actions d-flex align-items-center justify-content-around">
                                    <button type="button" class="btn waves-effect waves-light btn-info" onclick="list_add_button()" ><i class="fa fa-address-book-o"></i> Add from directory</a>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-around">
                                    <button type="button" class="btn waves-effect waves-light btn-primary custom_phone" id="custom_phone" value="custom_phone"  onclick="list_add_button('custom_phone')"><i class="fa fa-plus"></i> Add Custom Phone Number</a>
                                </div>      
									
                              
                                     </div>
                        </div>       
                        
                    </div>
                    <div class="message success hidden modalpopup_message" onclick="this.classList.add('hidden')"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary waves-effect" id="send" onclick="sendEformData()">Send <span class="btn_text"></span></button>
                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--Add eForm Modal-->
<div class="modal  add-eform-modal" id="eFormAddModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form" action="./" id="eFormAddPopup" method="POST" class="mb-0">
                <input type="hidden" id="idcardid" name ="idcardid" value="Ackess ID"/>                              
                <div class="modal-header">                
                    <h4 class="modal-title" id="myModalLabel">Add Eform</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="required">Form Name</label>
                        <input type="text" class="form-control" id="formName" name="formName" placeholder="Enter Form Name">
                    </div>
                    <div class="form-group">
                        <label class="required">Descriptions</label>
                        <textarea class="form-control" id="description" name="description" placeholder="Enter Descriptions" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Instructions</label>
                        <textarea class="form-control" id="instruction" name="instruction" placeholder="Enter Instructions" rows="3"></textarea>
                    </div>                    
                    <div class="message success hidden modalpopup_message" onclick="this.classList.add('hidden')"></div> 
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary waves-effect" id="submit" onclick="submitEform()">Create</button>
                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Select Add Eform -->
<div class="modal  add-eform-modal" id="eFormModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form" action="" id="eFormPopup" method="POST" class="mb-0">
                <input type="hidden" id="idcardid" name ="idcardid" value="Ackess ID"/>                              
                <div class="modal-header">                
                    <h4 class="modal-title" id="myModalLabel">Select Eform Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group d-flex align-items-center justify-content-around">
                       <div class="box">
                           <a class="eForm-add-box eFormAddModule m-auto">
                               <i class="fas fa-plus"></i>
                           </a>
                           <p class="eform-add-type">Start from Scratch</p>
                       </div>
                    </div>  
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- viewReceived Modal -->
<div class="modal " id="viewReceivedDocumentModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form" action="" id="viewReceivedDocumentData" method="POST" class="mb-0">
                <input type="hidden" id="viewReceivedid" name="viewReceivedid" value=""/>
                <input type="hidden" id="viewType" name="viewType" value=""/>
                <div class="modal-header">                
                    <h4 class="modal-title" id="myModalLabel">View Received</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">                    
                    <div class="form-group">                            
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered" id="viewReceivedAkcess">
                                <thead>                                                        
                                </thead>
                            </table>
                        </div>              
                     </div>
                </div>                
            </form>
        </div>
    </div>
</div>

<div id="load" class="ajax-loader">
    <div class="ajax-loader-box">
        <div class="row">
            <div class="col-12">
                <div class="fa-3x">
                    <i class="fa fa-spinner fa-spin"></i>          
                </div>          
            </div>
        </div>
    </div>
</div> 
<?= $this->Html->script('js/qrcode/qrcode') ?>
<script>
var x = 1;
function list_add_button(a) {
    var html = "";
   
    html += '<div class="form-row align-items-center mt-1 remove_all remove-'+x+'">';
    html += '<div class="col-4 col-md-4">';
    html += '<select class="custom-select" name="field['+x+'][country_code]" id="country_code">';
    html += '<option selected>Country Code</option>';
        <?php foreach ($countries as $countrie) { ?>
             html += "<option value='<?php echo $countrie->calling_code; ?>'><?php echo $countrie->country_name . ' ( +' . $countrie->calling_code . ' ) '; ?></option>";
        <?php } ?>
    html += '</select>';
    html += '</div>';
    html += '<div class="col-7 col-md-7">';
     if(a=="custom_phone"){
		html += '<input type="text" class="form-control" id="phone" name="field['+x+'][phone]" placeholder="Enter Phone" />';
	}else{
		html += '<select class="custom-select" name="field['+x+'][phone]" id="phone">';
    html += '<option selected>select  phone</option>';
        <?php foreach ($users as $user) { 
			$mobile= 
 preg_replace(	 '/\+(?:998|996|995|994|993|992|977|976|975|974|973|972|971|970|968|967|966|965|964|963|962|961|960|886|880|856|855|853|852|850|692|691|690|689|688|687|686|685|683|682|681|680|679|678|677|676|675|674|673|672|670|599|598|597|595|593|592|591|590|509|508|507|506|505|504|503|502|501|500|423|421|420|389|387|386|385|383|382|381|380|379|378|377|376|375|374|373|372|371|370|359|358|357|356|355|354|353|352|351|350|299|298|297|291|290|269|268|267|266|265|264|263|262|261|260|258|257|256|255|254|253|252|251|250|249|248|246|245|244|243|242|241|240|239|238|237|236|235|234|233|232|231|230|229|228|227|226|225|224|223|222|221|220|218|216|213|212|211|98|95|94|93|92|91|90|86|84|82|81|66|65|64|63|62|61|60|58|57|56|55|54|53|52|51|49|48|47|46|45|44\D?1624|44\D?1534|44\D?1481|44|43|41|40|39|36|34|33|32|31|30|27|20|7|1\D?939|1\D?876|1\D?869|1\D?868|1\D?849|1\D?829|1\D?809|1\D?787|1\D?784|1\D?767|1\D?758|1\D?721|1\D?684|1\D?671|1\D?670|1\D?664|1\D?649|1\D?473|1\D?441|1\D?345|1\D?340|1\D?284|1\D?268|1\D?264|1\D?246|1\D?242|1)\D?/', ''	, $user->mobileNumber	);
			
			?>
             html += "<option value='<?php echo $mobile; ?>'><?php echo $user->name . ' ( ' . $mobile . ' ) '; ?></option>";
        <?php } ?>
    html += '</select>';
	}
	
    html += '</div>';
    html += '<div class="col-1">';
    html += '<button type="button" class="btn waves-effect waves-light btn-danger" onclick="list_remove_button('+x+')"><i class="fa fa-minus"></i></a>';
    html += '</div>';
    html += '</div>';

    $('.list_wrapper').append(html);
    x++;
}
function list_remove_button(value) {
    $('.remove-'+value).remove();
}
</script>

