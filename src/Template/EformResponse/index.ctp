<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

$this->FieldsResponse = TableRegistry::get('FieldsResponse');

$session = $this->request->getSession();
$eform_message = "";
$message_show_hide = "hidden";
if($session->read('eform_message') != "") {
    $eform_message = $session->read('eform_message');
    $session->write('eform_message', '');
    $message_show_hide = "";
}


?>
<style>
    .dt-buttons{
        position: absolute;
        right: 0;
        top: 0;
        margin-right: 15px;
        padding-top: 15px;
    }
</style>
<?php if(isset($eform_message) && $eform_message != "") { ?>
<div class="alert alert-success alert-hover" id="flash_success">
    <button class="close">x</button>
    <?php echo $eform_message; ?>
</div>
<?php } ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                    <div class="button-group text-right" style="margin-top: -20px;">
                                                <?= $this->Html->link(__('<i class="fas fa-arrow-circle-left"></i> '),$this->request->referer(),  ['class' => 'btn waves-effect waves-light btn-info','escape' => false, 'data-toggle' => "tooltip", 'title'=>'Back','style'=> 'padding: 5px 10px 5px 10px;margin: 0 132px -50px 0']) ?>
                                            </div>
                        <table id="eform_response" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">AKcessID</th>
                                    <th scope="col">Eform Name</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Date</th>
                                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Eform Response Modal -->
<div class="modal " id="EformVerifyResponseModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form" action="" id="EformVerifyResponseData" method="POST" class="mb-0">
                <input type="hidden" id="erid" name="erid" value=""/>
                <div class="message success hidden modalpopup_message_verify" style="right:0px;" onclick="this.classList.add('hidden')"></div>
                <div class="modal-header">                
                    <h4 class="modal-title" id="myModalLabelTitle"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">  
                    <div class="row">
                        <div class="col-12">
                            <table class="table dataTable no-footer w-100">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th class="text-center">Check To Verify</th>
                                        <th class="text-center">Expiry Date</th>
                                        <th class="text-center">Grade Verification</th>
                                    </tr>
                                </thead>
                                <tbody id="getData">
                                </tbody>                                
                            </table>
                        </div>  
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-12 col-lg-4">
                                    <select class="custom-select" name="customVerify" id="customVerify">
                                        <option value="">Choose your Eform Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="alive">Alive</option>
                                        <option value="accept">Accept</option>
                                        <option value="verify and accept">Verify and Accept</option>
                                        <option value="create admin">Create & Update Admin</option>
                                        <option value="create staff">Create & Update Staff</option>
                                        <option value="create student">Create & Update Student</option>
                                        <option value="create teacher">Create & Update Academic Personnel</option>
                                        <option value="return">Return</option>  
                                        <option value="reject">Reject</option>  
                                    </select>
                                </div>                                
                            </div> 
                        </div>  
                    </div>
                </div>
                <div class="modal-footer">
                    <a target="_blank" class="btn btn-secondary waves-effect" href="" id="view_as_pdf">View As PDF</a>
                    <button type="submit" class="btn btn-primary waves-effect" onclick="submitVerifyEformResponse()">Submit <span class="btn_text"></span></button> 
                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="EformResponseExpiryDateModalModule" class="ajax-loader">    
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form" action="" id="EformResponseExpiryDateData" method="POST" class="mb-0">                
                <div class="modal-header">                
                    <h4 class="modal-title" id="myModalLabel">Expiry Date</h4>
                    <input type="hidden" id="key_expiry_date" value="" /> 
                </div>
                <div class="modal-body">  
                    <div class="row">
                        <div class="col-12 text-center">
                            <input type="date" id="expiry_date" name="expiry_date" class="form-control" placeholder="Expiry Date" /> 
                        </div>  
                    </div>
                </div>
                <div class="modal-footer text-center">

                    <button type="button" class="btn btn-primary waves-effect" onclick="getEformResponseExpiryDateAdd();">Add</button>
                    <button type="button" id="close_expiry" onclick="getEformResponseExpiryDateClose();" class="btn btn-danger waves-effect">Close</button>
                </div>
            </form>
        </div>
    </div>
</div> 

<div id="viewEformResponseModalModule" class="ajax-loader">    
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form" action="" id="viewEformResponseData" method="POST" class="mb-0">                
                <div class="modal-header">                
                    <h4 class="modal-title" id="myModalLabel">Grade Verification</h4>
                    <button type="button" class="close" id="close_expiry" onclick="getEformResponseClose();">&times;</button>
                </div>
                <div class="modal-body">  
                    <div class="table-responsive">
                        <table id="viewEformResponse" class="table table-hover table-striped table-bordered">
                            <thead>                                                        
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger waves-effect" id="close_expiry" onclick="getEformResponseClose();">Close</button>
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

