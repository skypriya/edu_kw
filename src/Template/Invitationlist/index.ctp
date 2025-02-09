<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

$session = $this->request->getSession();
$notification_message = "";
$message_show_hide = "hidden";
if($session->read('notification_message') != "") {
    $notification_message = $session->read('notification_message');
    $session->write('notification_message', '');
    $message_show_hide = "";
}

?>
<style>
#invitation_list_filter{
    margin-left: 50px !important;
}
</style>

<?php if(isset($notification_message) && $notification_message != "") { ?>
<div class="alert alert-success alert-hover" id="flash_success">
    <button class="close">x</button>
    <?php echo $notification_message; ?>
</div>
<?php } ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive m-t-10">
                    <div class="row m-0" style="width: 50px;height: 0px;">
                        <h4>
                            <?= $this->Html->link(__('<i class="fas fa-arrow-circle-left"></i> '),'management',  ['class' => 'btn waves-effect waves-light btn-info','style'=>'margin-top: 1px;' ,'escape' => false, 'data-toggle' => "tooltip", 'title'=>'Back']) ?>
                        </h4>
                    </div>
                    <table id="invitation_list" class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Type</th>
                                <th scope="col">By AKcess ID</th>
                                <th scope="col">By Email Id</th>
                                <th scope="col">By Phone No</th>
                                <th scope="col">User Type</th>
                                <th scope="col">Date</th>   
                                <th scope="col">Status</th>       
                                <th scope="col" class="actions"><?= __('Actions') ?></th>                                         
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($Invitationlist as $n): ?>
                                <tr>
                                
                                    <td><?= $n['id'] ?></td>   
                                    <td><?= isset($n['akcessId']) && !empty($n['akcessId']) ? 'Request Data From AKcess ID' : 'Invitation'; ?></td>
                                    <td><?= $n['akcessId'] ?></td>                      
                                    <td><?= $n['email'] ?></td>      
                                    <td><?= $n['phoneno'] ?></td>            
                                    <td class="text-uppercase"><?= $n['type'] ?></td>                                    
                                    <td data-order="<?php echo date("Y-m-d h:i:s", strtotime($n['created'])); ?>"><?= date("d/m/Y h:i:s", strtotime($n['created'])) ?></td>
                                    <td class="text-uppercase">
                                        <?php  if($n['status'] == 1) { ?>
                                            <button type="button" class="btn btn-sm btn-info">Pending</button>
                                        <?php } else if($n['status'] == 0) { ?>
                                            <button type="button" class="btn btn-sm btn-warning">Submitted</button>
                                        <?php } else if($n['status'] == 2) { ?>
                                            <button type="button" class="btn btn-sm btn-success">Accepted</button>
                                        <?php } else if($n['status'] == 3) { ?>
                                            <button type="button" class="btn btn-sm btn-danger">Rejected</button>
                                        <?php } ?>
                                    </td>
                                    <td class="actions">
                                        <div class="actions-div">
                                            <?php if(empty($n['fk_senddata_id'])) {  ?>
                                                <?php if($n['status'] == 0 || $n['status'] == 2  || $n['status'] == 3) { ?>
                                                    <a href="javascript:void(0);" data-title="<?php ConnectionManager::userIdEncode($n['id']); ?>" class="viewInvitation" onclick="getInvitationResponse('<?php echo ConnectionManager::userIdEncode($n['id']); ?>','<?php echo $n['status']; ?>');" data-backdrop="static" data-keyboard="false" data-toggle="tooltip" title='View'><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                <?php } else { ?>
                                                    <a href="javascript:void(0);" data-title="<?php ConnectionManager::userIdEncode($n['id']); ?>" class="viewInvitation" data-backdrop="static" data-keyboard="false" data-toggle="tooltip" title='User submitted Eform is pending'><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <a href="javascript:void(0);" data-title="<?php echo $n['fk_senddata_id']; ?>" class="viewEform" onclick="getEformResponse('<?php echo $n['fk_senddata_id']; ?>');" data-backdrop="static" data-keyboard="false" data-toggle="tooltip" title='View'><i class="fa fa-eye" aria-hidden="true"></i></a>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View eForm modal -->
<div class="modal " id="viewInvitation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form" action="" id="EformInvitationResponseData" method="POST" class="mb-0">
                <div class="modal-header">                
                    <h4 class="modal-title headname" id="myModalLabel">View Submitted Invitation Details - <b id="contentTitle"></b></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <input name="sd" id="sd" value="" type="hidden" />
                </div>
                <div class="modal-body"> 
                    
                    <div class="form-group" id="contentBody"></div>                 
                </div>
                <div class="modal-footer">  
                    <select class="custom-select" name="customInvitation" id="customInvitation">
                        
                    </select>

                    <div id=submit_invitation></div>
                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
                </div>
            </form>
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