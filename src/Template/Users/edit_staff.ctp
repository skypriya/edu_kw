<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

$this->Docs = TableRegistry::get('Docs');
$this->IDCard = TableRegistry::get('IDCard');

$session_usertype = explode(",", $session_user['usertype']);
$get_usertype = explode(",", $user->usertype);

$label = "Staff";

$name = isset($user->name) ? $user->name : '';
$id = isset($user->id) ? $user->id : 0;
$flname = $id;
$fname = $id . "/";

$user_id = ConnectionManager::userIdEncode($user->id);

$check_fields = 0;
if($user->firstname == "" || $user->lastname == ""  || $user->email == ""  || $user->dob == "" || $user->photo == "" || $user->akcessId == ""){
    $check_fields = 1;
}
?>
<input type="hidden" id="check_fields_id" value="<?php echo $check_fields; ?>" />
<div class="edit-staff-page">
    <div class="row">
        <div class="col-lg-9">
            <div class="card card-outline-info">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="m-b-0 text-white d-flex align-items-center"><i class="fal fa-users mr-1"></i>Edit <?php echo $label; ?> ( <?php echo $user->name; ?> ) </h4>
                        <?= $this->Html->link(__('<i class="fas fa-arrow-circle-left"></i> '),'users/staff-list',  ['class' => 'btn waves-effect waves-light btn-info','escape' => false, 'data-toggle' => "tooltip", 'title'=>'Back']) ?>
                    </div>
                </div>
                <div class="card-body">
                    <?= $this->Form->create($user) ?>
                    <div class="form-body">
                        <div class="row p-t-20">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?php echo $this->Form->input('akcessId', array('label' => 'AKcess ID', 'class' => 'form-control', 'value' => $user->akcessId)); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?php echo $this->Form->input('firstname', array('label' => 'First Name', 'class' => 'form-control', 'value' => $user->firstname)); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?php echo $this->Form->input('lastname', array('label' => 'Last Name', 'class' => 'form-control', 'value' => $user->lastname)); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?php echo $this->Form->input('email', array('label' => ['text'=>'Email', 'class'=>'required'], 'class' => 'form-control', 'value' => $user->email)); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?php echo $this->Form->input('mobileNumber', array('label' => ['text'=>'Mobile Number', 'class'=>'required'], 'class' => 'form-control', 'value' => $user->mobileNumber)); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required">Date of Birth</label>
                                    <input type="text" id="student_dob" name="dob" class="form-control" placeholder="Date of Birth" value="<?php echo $user->dob; ?>"  max="<?php echo date("Y-m-d"); ?>" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?php echo $this->Form->input('city', array('label' => ['text'=>'Place of Birth', 'class'=>'required'], 'class' => 'form-control', 'value' => $user->city)); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?php
                                    $type = ['' => 'Select gender', 'Male' => 'Male', 'Female' => 'Female'];
                                    echo $this->Form->input('gender', array('type' => 'select', 'options' => $type, 'label' => ['text'=>'Gender', 'class'=>'required'], 'class' => 'form-control'));
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nationality</label>
                                    <select name="nationality" id="nationality" class="form-control custom-select" data-placeholder="Select Country" tabindex="1">
                                        <option value="">Select Country</option>
                                        <?php
                                        foreach ($countries as $countrie) {
                                            $selected = '';
                                            if ($countrie->id == $user->nationality) {
                                                $selected = 'selected';
                                            }
                                            ?>
                                            <option value="<?php echo $countrie->id; ?>" <?php echo $selected; ?>><?php echo $countrie->country_name; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea name="address" id="address" col="3" class="form-control"><?php echo $user->address; ?></textarea>  
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Staff Type</label>
                                    <select class="custom-select" id="staff_type" name="staff_type" class="form-control custom-select" data-placeholder="Staff Type" tabindex="1">
                                        <option value="">Staff Type</option>
                                        <?php
                                        foreach ($staffList as $key => $staffLists) {
                                            $selected = '';
                                            if ($staffLists->id == $user->staff_type) {
                                                $selected = 'selected';
                                            }
                                            ?>
                                            <option value="<?php echo $staffLists->id; ?>" <?php echo $selected; ?>><?php echo $staffLists->name; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>                                
                        </div>
                        <div class="row">
                            <label class="col-12 col-md-2 col-form-label">Active</label>
                            <div class="col-10 d-flex align-items-center">
                                <?php
                                    $checked_yes = "";
                                    $checked_no = "";
                                    if($user->active == "yes") {
                                        $checked_yes = 'checked';
                                    } else if($user->active == "no") {
                                        $checked_no = 'checked';
                                    }
                                ?>
                                <div>
                                    <input class="with-gap radio-col-light-blue" type="radio" name="active" id="active_yes" value="yes" <?php echo $checked_yes; ?> />
                                    <label for="active_yes">Yes</label>
                                </div>
                                <div class="ml-2">
                                    <input class="with-gap radio-col-light-blue" type="radio" name="active" id="active_no"  value="no" <?php echo $checked_no; ?> />
                                    <label for="active_no">No</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <?php if(in_array('Admin',$session_usertype)) { ?>
                    <div class="form-actions text-right">

                        <?= $this->Form->button(__('<i class="fas fa-save mr-1" aria-hidden="true"></i> Update'), array('class' => 'btn waves-effect waves-light btn-primary', 'id' => 'submit_btn', 'type' => 'hidden')) ?>
                        <?php if (in_array('Admin',$get_usertype)) { ?>

                        <?= $this->Html->link(__('<i class="fas fa-times mr-1"></i> Close'), ['action' => 'staffList', $user_id], ['escape' => false, 'class'=>'btn waves-effect waves-light btn-danger']) ?>

                        <?php } else if (in_array('Staff',$get_usertype)) { ?>       

                        <?= $this->Html->link(__('<i class="fas fa-times mr-1"></i> Close'), ['action' => 'staffList'], ['escape' => false, 'class'=>'btn waves-effect waves-light btn-danger']) ?>              

                        <?php } ?>

                        <?= $this->Form->postLink(__('<i class="far fa-trash-alt mr-1"></i> Delete'), ['action' => 'delete', $user_id], ['escape' => false, 'id' => 'remove_btn', 'class' => 'btn waves-effect waves-light btn-danger d-none']) ?>

                        <!-- <?php $this->Html->link(__('<i class="fa fa-eye mr-1"></i> View'), ['action' => 'view-staff', $user_id], ['escape' => false, 'class'=>'btn waves-effect waves-light btn-info']) ?> -->

                    </div>
                    <?php } ?>
                    <div class="clearfix"></div>
                    <?= $this->Form->end() ?>
                    <hr>
                    
                    <div class="row">
                        <label class="col-2 col-form-label font-400"><?= __('Documents') ?></label>
                        <?php if(in_array('Admin',$session_usertype)) { ?>
                        <div class="col-10 d-flex align-items-center">                            
                            <?= $this->Html->link('<i class="fas fa-plus fa-2 mr-1"></i>Add Document', ['controller' => 'Docs', 'action' => 'add', $user->id], ['class' => 'adddoc btn waves-effect waves-light btn-info', 'escape' => false]); ?>
                         </div>
                         <?php } ?>
                    </div>
                    
                    <div class="table-responsive m-t-10">
                    <?php if ($docs) { ?>
                        <table id="formInner" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Link</th>
                                    <?php if(in_array('Admin',$session_usertype)) { ?>
                                    <th class="text-center">Actions</th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($docs as $t) { ?>
                                    <?php
                                    $tid = ConnectionManager::userIdEncode($t['id']); 
                                    $tids = ConnectionManager::userIdEncode($t['ids']); 
                                    ?>
                                    <tr>
                                        <td id="<?php echo "namedoc-" . $tid; ?>" data-dtype="<?php echo $t['fk_documenttype_id']; ?>" data-date="<?php echo $t['idCardExpiyDate']; ?>"><?= h($t['name']) ?></td>
                                        <td><a target="_blank" href="<?php echo Router::url('/', true); ?>uploads/attachs/<?php echo $fname . $t['fileName']; ?>"><i class="fa fa-eye"></i></a></td>
                                        <?php if(in_array('Admin',$session_usertype)) { ?>
                                        <td class="actions">
                                            <div class="actions-div">

                                                <?= $this->Html->link('<i class="fa fa-edit green-txt"></i>', ['controller' => 'Docs', 'action' => 'edit', $tids], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Edit', 'class' => 'adddoc', 'data-doc' => $tid]); ?>

                                                <?= $this->Html->link(__('<i class="fa fa-trash red-txt mx-1"></i>'), ['controller' => 'Docs', 'action' => 'delete', $tids, $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Remove', 'class' => 'delete_btn', 'data-link' => 'remove_btn' . $tids]) ?> 

                                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Docs', 'action' => 'delete', $tids, $user_id], ['escape' => false, 'id' => 'remove_btn' . $tids, 'style' => 'display:none;']) ?>

                                                <button type="button" class="btn btn-sm waves-effect waves-light btn-info mx-1" data-type="document" data-title="<?php echo $tids; ?>" onclick="sendModalModule('<?php echo $tids; ?>');">Send</button>

                                                <button type="button" class="btn btn-sm waves-effect waves-light btn-info mx-1" data-title="<?php echo $tids; ?>" onclick="viewSendDocumentModalModule('<?php echo $tids; ?>');">View Sent</button>

                                                <button type="button" class="btn btn-sm waves-effect waves-light btn-info viewReceivedDocumentModalModule" data-title="<?php echo $tids; ?>" onclick="viewReceivedDocumentModalModule('<?php echo $tids; ?>', 'document');">View Received</button>

                                            </div>
                                        </td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>    
                            </tbody>
                        </table>
                    <?php } ?>
                    </div>
                    <hr>
                    <?php if($cid == '') { ?>
                        
                        <div class="row">
                            <label class="col-2 col-form-label font-400"><?= __('IDs') ?></label>       
                            <?php if(in_array('Admin',$session_usertype)) { ?>                 
                                <div class="col-10 d-flex align-items-center">
                                <?php if($check_fields == 0) { ?>
                                    <?= $this->Html->link(__('<i class="fas fa-plus fa-2 mr-1"></i> Create ID Card'), ['controller' => 'Docs', 'action' => 'addidcard', $user_id], ['class' => 'addidcard btn waves-effect waves-light btn-info', 'escape' => false]); ?>
                                <?php } else { ?>
                                    <a href="#scroll" id="checkidcard" class="btn waves-effect waves-light btn-info" >
                                        <i class="fas fa-plus fa-2 mr-1"></i> Create ID Card
                                    </a>                         
                                <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                        
                    <?php } if ($idcard &&  $cid != '') { ?>
                    <div class="table-responsive m-t-10">
                        <table id="formInner" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                <th>ID No</th>
                                <th>Link</th>
                                <?php if(in_array('Admin',$session_usertype)) { ?>
                                <th class="text-center">Actions</th>  
                                <?php } ?>                                
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($idcard as $t) { ?>
                            <?php
                                $td = ConnectionManager::userIdEncode($t->id);
                            ?>
                            <tr>
                                <td><?= h($t->idNo) ?></td>
                                <td><a target="_blank" href="<?php echo Router::url('/', true); ?>uploads/attachs/<?php echo $fname . $t->fileName; ?>"><i class="fa fa-eye"></i></a></td>
                                <?php if(in_array('Admin',$session_usertype)) { ?>
                                <td class="actions">
                                    <div class="actions-div">
                                        <?= $this->Html->link('<i class="fa fa-edit green-txt"></i>', ['controller' => 'Docs', 'action' => 'addidcard', $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Edit', 'class' => 'addidcard']); ?>

                                        <?= $this->Html->link(__('<i class="fa fa-trash red-txt mx-1"></i>'), ['controller' => 'IDCard', 'action' => 'delete', $td, $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Remove', 'class' => 'delete_btn', 'data-link' => 'remove_btn_idcard' . $td]) ?>

                                        <?= $this->Form->postLink(__('Delete'), ['controller' => 'IDCard', 'action' => 'delete', $td, $user_id], ['escape' => false, 'id' => 'remove_btn_idcard' . $td, 'style' => 'display:none;']) ?>

                                        <button type="button" class="btn btn-sm waves-effect waves-primary btn-info mx-1 sendIDCardModalModule" data-title="<?php echo $td; ?>" onclick="sendIDCardModalModule('<?php echo $td; ?>');">Send</button>

                                        <button type="button" class="btn btn-sm waves-effect waves-primary btn-info mx-1 viewSendIDCardModalModule" data-title="<?php echo $td; ?>" onclick="viewSendIDCardModalModule('<?php echo $td; ?>');">View Sent</button>

                                        <button type="button" class="btn btn-sm waves-effect waves-primary btn-info mx-1 viewReceivedIDCardModalModule" data-title="<?php echo $td; ?>" onclick="viewReceivedIDCardModalModule('<?php echo $td; ?>', 'idcard');">View Received</button>
                                    </div>
                                </td>
                                <?php } ?>       
                            </tr>
                            <?php } ?>    
                            </tbody>
                        </table>                    
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card upload-profile-box">
                <div class="card-body">
                    <div class="img-box text-center">
                    <?php
                    if ($user->photo) {
                        $image_src = $this->Url->build('/uploads/attachs/' . $fname . $user->photo);
                        ?>
                        <img src="<?= $image_src ?>" class="img-circle"/>
                    <?php
                    } else {
                        echo $this->Html->image('user.png', array('class' => 'img-circle'));
                    }?>
                    </div>
                    <?php if(in_array('Admin',$session_usertype)) { ?>
                    <div class="img-msg">
                        <p>
                            <?php
                            if ($user->photo)
                                $ptitle = 'Click here to Change Photo';
                            else
                                $ptitle = 'Click here to Upload Photo';
                            ?>
                        </p>
                    </div>
                    <div class="image-control text-center">
                            <?= $this->Form->create($user, ['type' => 'file', 'id' => 'fileInput']) ?>
                        <div class="form-group">
                            <?php
                            echo $this->Form->input('photo', array('type' => 'file', 'label' => $ptitle, 'class' => 'btn btn-success'));
                            ?>
                        </div>
                            <?= $this->Form->end() ?>
                    </div>
                    <?php } ?>    
                </div>
            </div>
        </div>
    </div>
</div>

<!--Upload Modal -->
<div id="myModalaplusDocument" class="modal " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">           
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Upload Document</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form method="post" accept-charset="utf-8" id="adddoc" controller="Doc" action="<?php echo PATH_URL_PREFIX; ?>/docs/doc-genrate">
                <div class="modal-body">
                    <div class="form-body">
                         <div class="form-group">
                            <?php echo $this->Form->input('name', array('label' => ['text'=>'Document Name', 'class'=>'required'], 'class' => 'form-control', 'required' => 'true')); ?>
                        </div>
                    
                        <div class="form-group">
                        <label class="required">Document Type</label>
                            <select class="custom-select" name="fk_documenttype_id" id="fk_documenttype_id" required>
                                <option value="">Select Document Type</option>
                                <?php
                                foreach ($documentList as $documentLists) {
                                    ?>
                                    <option value="<?php echo $documentLists['id']; ?>"><?php echo $documentLists['name']; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="option">
                                <label>Document Expiry Date</label>
                                <input type="date" id="expiry_date_popup_doc" name="idCardExpiyDateDoc" class="form-control" format="YYYY-MM-DD" placeholder="Date of Birth" min="<?php echo date("Y-m-d"); ?>" />
                            </div>
                        </div>
                        <div>
                            <?php echo $this->Form->input('attachs', array('type' => 'file', 'class' => 'form-control', 'label' => ['text'=>'Attach a file', 'class'=>'required'], 'required' => 'true')); ?>
                        </div>
                        <input type="hidden" id="a" name="a" value="<?php echo $userID; ?>"/>
                        <input type="hidden" id="b" name="b" value=""/>
                    </div>
                </div>
                <div class="modal-footer">
                    <?= $this->Form->button(__('Upload'), array('class' => 'btn btn-primary waves-effect')) ?>
                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>     
                </div>
            <?= $this->Form->end() ?>
        </div>        
    </div>
</div>


<!-- University ID Modal -->
<div class="modal student-upload-modal " id="myModalaplusIDCard" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create University ID</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" accept-charset="utf-8" id="addidcard" controller="IDCard" action="<?php echo PATH_URL_PREFIX; ?>/i-d-card/pdf-genrate">
                <div class="modal-body" id="contentBodyIDCard">
                    <div class="form-body">
                        <div class="form-group">
                            <?php echo $this->Form->input('idNo', array('label' => ['text'=>'ID No.', 'class'=>'required'], 'class' => 'form-control', 'value' => $user->idcardno, 'required' => 'true', 'disabled' => "disabled")); ?>
                        </div>
                        <div class="form-group">
                            <label class="required">ID Card Expiry Date</label>
                            <input type="date" id="expiry_date_popup_doc" name="idCardExpiyDateDoc" class="form-control" format="YYYY-MM-DD" placeholder="Date of Birth" min="<?php echo date("Y-m-d"); ?>"  value="<?php echo $idCardExpiyDate; ?>" required />
                        </div>
                        <input type="hidden" id="a" name="a" value="<?php echo $userID; ?>"/>
                        <input type="hidden" id="b" name="b" value="<?php echo $cid; ?>"/> 
                        <input type="hidden" id="c" name="c" value="<?php echo ConnectionManager::userIdEncode('Staff'); ?>"/>                         
                    </div>
                </div>
                <div class="modal-footer">
                    <?= $this->Form->button(__('Submit'), array('class' => 'btn btn-primary waves-effect')) ?>
                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>        
                </div>
                <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<!-- Send Modal -->
<div class="modal " id="sendModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form"  id="SendDoc" method="POST" class="mb-0">
                <input type="hidden" id="idcardid" name ="idcardid" value="Ackess ID"/>                
                <div class="modal-header">                
                    <h4 class="modal-title" id="myModalLabel">Send Document</h4>
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
                                foreach ($userss as $user) {
                                    $akcessId = '';
                                    if (isset($user->akcessId) && $user->akcessId != "") {
                                        $akcessId = $user->akcessId;
                                        $name = $user->name . " ( " . $user->akcessId . " ) ";  
                                        if (isset($akcessId)) {
                                            ?>
                                            <option value="<?php echo $user->akcessId; ?>"><?php echo $name; ?></option>
                                        <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group email">                            
                            <input type="text" id="email_search" name="email_search" placeholder="Enter Email" class="form-control input_tokenize">                      
                            <input type="hidden" id="email" name="email" value="">                         
                        </div>
                        <div class="form-group phone list_wrapper">
                            <div class="form-row align-items-center">
                                <div class="col-4 col-md-4">
                                    <select class="custom-select" name="field[0][country_code]" id="country_code">
                                        <option selected>Country Code</option>
                                        <?php foreach ($countries as $countrie) { ?>
                                            <option value="<?php echo $countrie->calling_code; ?>"><?php echo "+" . $countrie->calling_code . " ( " . $countrie->country_name . " ) "; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-7 col-md-7">
                                    <input type="text" class="form-control" id="phone" name="field[0][phone]" placeholder="Enter Phone" />
                                </div>
                                <div class="col-1 text-center">
                                    <button type="button" class="btn waves-effect waves-light btn-info" onclick="list_add_button()"><i class="fa fa-plus"></i></a>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary waves-effect" id="send" onclick="sendData('document')">Send <span class="btn_text"></span></button>
                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal " id="viewSendDocumentModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form"  id="ViewSendDocumentData" method="POST" class="mb-0">
                <input type="hidden" id="vieweid" name="vieweid" value=""/>
                <input type="hidden" id="viewType" name="viewType" value="document"/>
                <div class="modal-header">                
                    <h4 class="modal-title" id="myModalLabel">View Sent</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center justify-content-around"> 
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewSendDocumentinlineRadio3" value="viewackess" class="with-gap radio-col-light-blue"/>
                            <label for="viewSendDocumentinlineRadio3">AKcess ID</label>
                        </div>
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewSendDocumentinlineRadio1" value="viewemail" class="with-gap radio-col-light-blue"/>
                            <label for="viewSendDocumentinlineRadio1">Email</label>
                        </div>
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewSendDocumentinlineRadio2" value="viewphone" class="with-gap radio-col-light-blue"/>
                            <label for="viewSendDocumentinlineRadio2">Phone</label>
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

<!-- viewReceived Modal -->
<div class="modal " id="viewReceivedDocumentModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form"  id="viewReceivedDocumentData" method="POST" class="mb-0">
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

<!-- Send ID Card Modal -->
<div class="modal " id="sendIDCardModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form"  id="SendDocIDCard" method="POST" class="mb-0">
                <input type="hidden" id="idcardid" name ="idcardid" value="Ackess ID"/>                
                <div class="modal-header">                
                    <h4 class="modal-title" id="myModalLabel">Send ID Card</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center justify-content-around">
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewReceivedInlineRadio3" value="ackess" class="with-gap radio-col-light-blue"/>
                            <label for="viewReceivedInlineRadio3">AKcess ID</label>
                        </div>
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewReceivedInlineRadio1" value="email" class="with-gap radio-col-light-blue"/>
                            <label for="viewReceivedInlineRadio1">Email</label>
                        </div>
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewReceivedInlineRadio2" value="phone" class="with-gap radio-col-light-blue"/>
                            <label for="viewReceivedInlineRadio2">Phone</label>
                        </div>
                    </div>
                    <div>
                        <div class="form-group ackess">
                            <!-- <select id="ackess_id" multiple="multiple" class="global-tokenize input_tokenize select2 m-b-10 select2-multiple" name="ackess[]">
                                <?php
                                foreach ($userss as $user) {
                                    $akcessId = '';
                                    if (isset($user->akcessId) && $user->akcessId != "") {
                                        $akcessId = $user->akcessId;
                                        $name = $user->name . " ( " . $user->akcessId . " ) ";  
                                        if (isset($akcessId)) {
                                            ?>
                                            <option value="<?php echo $user->akcessId; ?>"><?php echo $name; ?></option>
                                        <?php
                                        }
                                    }
                                }
                                ?>
                            </select>                             -->
                        </div> 
                        <div class="form-group email">                            
                            <!-- <input type="text" id="email_search_id" name="email_search" placeholder="Enter Email" class="form-control input_tokenize">                      
                            <input type="hidden" id="email_id" name="email" value="">                           -->
                        </div>
                        <div class="form-group phone list_wrapper">
                            <!-- <div class="form-row align-items-center">
                                <div class="col-4 col-md-4">
                                    <select class="custom-select" name="field[0][country_code]" id="country_code_id">
                                        <option selected>Country Code</option>
                                        <?php foreach ($countries as $countrie) { ?>
                                            <option value="<?php echo $countrie->calling_code; ?>"><?php echo "+" . $countrie->calling_code . " ( " . $countrie->country_name . " ) "; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-7 col-md-7">
                                    <input type="text" class="form-control" id="phone_id" name="field[0][phone]" placeholder="Enter Phone" />
                                </div>
                                <div class="col-1 text-center">
                                    <button type="button" class="btn waves-effect waves-light btn-info" onclick="list_add_button()"><i class="fa fa-plus"></i></button>
                                </div> 
                            </div> -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary waves-effect" id="send" onclick="sendDataIDCard('idcard')">Send</button>
                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View ID Card Modal -->
<div class="modal " id="viewSendIDCardModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form"  id="ViewSendIDCardData" method="POST" class="mb-0">
                <input type="hidden" id="vieweid" name="vieweid" value=""/>
                <input type="hidden" id="viewType" name="viewType" value="idcard"/>
                <div class="modal-header">                
                    <h4 class="modal-title" id="myModalLabel">View Sent</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center justify-content-around"> 
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewSendIDCardinlineRadio3" value="viewackess" class="with-gap radio-col-light-blue"/>
                            <label for="viewSendIDCardinlineRadio3">AKcess ID</label>
                        </div>
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewSendIDCardinlineRadio1" value="viewemail" class="with-gap radio-col-light-blue"/>
                            <label for="viewSendIDCardinlineRadio1">Email</label>
                        </div>
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewSendIDCardinlineRadio2" value="viewphone" class="with-gap radio-col-light-blue"/>
                            <label for="viewSendIDCardinlineRadio2">Phone</label>
                        </div>                                     
                    </div>
                    <div>
                        <div class="form-group viewackess">                            
                            <div class="table-responsive">
                                <table class="table table-hover table-striped table-bordered" id="viewSentIDCardAkcess">
                                    <thead>
                                        <tr>
                                            <th scope="col">AKcess ID</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Date</th>                                
                                        </tr>
                                    </thead>
                                </table>
                            </div>              
                        </div>
                        <div class="form-group viewemail">                            
                            <div class="table-responsive">
                                <table class="table table-hover table-striped table-bordered" id="viewSentIDCardEmail">
                                    <thead>
                                        <tr>
                                            <th scope="col">Email ID</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Date</th>                                
                                        </tr>
                                    </thead>
                                </table>
                            </div>                            
                        </div>
                        <div class="form-group viewphone">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped table-bordered" id="viewSentIDCardPhone">
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

<!-- viewReceived ID CARD Modal -->
<div class="modal " id="viewReceivedIDCardModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form"  id="viewReceivedIDCardtData" method="POST" class="mb-0">
                <input type="hidden" id="viewReceivedid" name="viewReceivedid" value=""/>
                <input type="hidden" id="viewType" name="viewType" value=""/>
                <div class="modal-header">                
                    <h4 class="modal-title" id="myModalLabel">View Received</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">                    
                    <div class="form-group">                            
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered" id="viewReceivedIDCardAkcess">
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


<!-- Delete Staff -->
<div id="delete_modal" class="modal " tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm">
        <div class="modal-content" >
            <div class="modal-body">                
                <p class="text-center">Are you sure you want to delete?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary waves-effect" id="yes_btn">Yes</button>
                <button class="btn btn-danger waves-effect" id="no_btn" data-dismiss="modal">No</button>
            </div>            
        </div>
    </div>
</div>

<!-- Loader-->
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

<script>
var x = 1;
function list_add_button() {
    var html = "";
    html += '<div class="form-row align-items-center mt-1 remove_all remove-'+x+'">';
    html += '<div class="col-4 col-md-4">';
    html += '<select class="custom-select" name="field['+x+'][country_code]" id="country_code">';
    html += '<option selected>Select Country Code</option>';
        <?php foreach ($countries as $countrie) { ?>
             html += "<option value='<?php echo $countrie->calling_code; ?>'><?php echo '+' . $countrie->calling_code . ' ( ' . $countrie->country_name . ' ) '; ?></option>";
        <?php } ?>
    html += '</select>';
    html += '</div>';
    html += '<div class="col-7 col-md-7">';
    html += '<input type="text" class="form-control" id="phone" name="field['+x+'][phone]" placeholder="Enter Phone" />';
    html += '</div>';
    html += '<div class="col-1">';
    html += '<button type="button" class="btn waves-effect waves-light btn-danger" onclick="list_remove_button('+x+')"><i class="fa fa-minus"></i></button>';
    html += '</div>';
    html += '</div>';

    $('.list_wrapper').append(html);
    x++;
}
function list_remove_button(value) {
    $('.remove-'+value).remove();
}
</script>
