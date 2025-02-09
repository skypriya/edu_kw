<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
use Cake\Datasource\ConnectionManager;

$session_usertype = explode(",", $session_user['usertype']);

$get_roles_user = explode(",", $role_id);


?>
<div class="students-list-page">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">                    
                    <div class="table-responsive m-t-10">
                        <?php 
                        $class = "example1";
                        if(in_array('Student',$session_usertype)) {
                            $class = "student-example1";
                        }
                        ?>
                        <?php if(in_array('Admin',$get_roles_user)) { ?>
                            <div class="other-filter ml-0 mr-2 float-left">
                                <?php
                                    $selecteda = "";
                                    $selecteds = "";
                                    $selectedst = "";
                                    $selectedt = "";

                                    if($search == 'Admin') {
                                        $selecteda = "selected";
                                    } else if($search == 'Staff') {
                                        $selecteds = "selected";
                                    } else if($search == 'Student') {
                                        $selectedst = "selected";
                                    } else if($search == 'Teacher') {
                                        $selectedt = "selected";
                                    }
                                ?>
                                <select name="search_user_type" id="search_user_type" class="form-control custom-select" onchange="searchUserType()">
                                    <option value="" >Select All UserType</option>
                                    <option value="Admin" <?php echo $selecteda; ?>>Admin</option>
                                    <option value="Staff" <?php echo $selecteds; ?>>Staff</option>
                                    <option value="Student" <?php echo $selectedst; ?>>Student</option>
                                    <option value="Teacher" <?php echo $selectedt; ?>>Academic Personnel</option>
                                </select>
                            </div>
                        <?php } ?>
                        <div class="row float-right m-0">
                                                <?php if(in_array('Admin',$session_usertype)) { ?>
                                                    <h4><button type="button" class="btn waves-effect waves-light btn-info mr-3" onclick="sendInvitationModalModule('student');" data-title="staff">Send Invitation</button></h4>
                                                    <h4><?php echo $this->Html->link(__('<i class="fas fa-plus mr-1"></i> Add Student'), ['action' => 'add'], ['class' => 'btn waves-effect waves-light btn-primary mr-3', 'escape' => false]); ?></h4>
                                                <?php } ?>
                                                <?php if(in_array('Admin',$get_roles_user)) { ?>
                                                    <h4><?php echo $this->Html->link(__('<i class="fa fa-recycle"></i>'), ['action' => 'recycle'], ['class' => 'btn waves-effect waves-light btn-warning', 'escape' => false, 'data-toggle' => "tooltip", 'title'=>'Recycle']) ?></h4>
                                                <?php } ?>
                                            </div>
                        <table id="<?php echo $class; ?>" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <?php 
                                    if(in_array('Admin',$session_usertype)) { ?>
                                        <th scope="col">User Type</th>
                                    <?php } ?>
                                    <th>AKcess ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile Number</th>
                                    <th class="text-center"><?= __('Actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $k = 0; foreach ($users as $user):?>
                                    <?php if(in_array('Teacher',$session_usertype) && !in_array('Admin',$session_usertype)) {  ?>
                                        <?php if(in_array($user['id'],$users_teacher)) { ?>
                                            <?php $user_id = ConnectionManager::userIdEncode($user['id']); ?>
                                            <tr>
                                                <td><?= $k+1 ?></td>
                                                <?php if (in_array('Admin', $session_usertype)) { ?>
                                                    <td><?php
                                                    $usertype = explode(",", $user['usertype']);
                                                    if ($usertype) {
                                                        $count = count($usertype);
                                                        foreach ($usertype as $key => $value) {
                                                            if (isset($value)) {
                                                                echo "<b>[" . h($value) . "]</b>";
                                                                if ($key == 1) {
                                                                    if ($count > 2) {
                                                                        echo "<br /><br />";
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                ?></td>
                                                <?php } ?>
                                                <td><?= h($user['akcessId']) ?></td>
                                                <td>
                                                    <?= $this->Html->link(__($user['name']), ['action' => 'view', $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'View']) ?> 
                                                </td>                                       
                                                <td><?= h($user['email']) ?></td>
                                                <td><?= h($user['mobileNumber']) ?></td>
                                                <td class="actions">
                                                    <div class="actions-div">
                                                        
                                                        <?= $this->Html->link(__('<i class="fa fa-eye"></i>'), ['action' => 'view', $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'View']) ?>

                                                        <?php if (in_array('Admin', $session_usertype)) { ?>
                                                        
                                                            <?= $this->Html->link(__('<i class="fa fa-edit green-txt"></i>'), ['action' => 'edit', $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Edit']) ?>

                                                        
                                                            <?= $this->Html->link(__('<i class="fa fa-trash red-txt"></i>'), ['action' => 'delete', $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Remove', 'class' => 'delete_btn', 'data-link' => 'remove_btn' . $user_id]) ?> 
                                                        
                                                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $user_id], ['escape' => false, 'id' => 'remove_btn' . $user_id, 'style' => 'display:none;']) ?>
                                                        <?php } ?>
                                                        
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php $k++;  } ?>                                    
                                    <?php } else { ?>
                                        <?php $user_id = ConnectionManager::userIdEncode($user['id']); ?>
                                            <tr>
                                                <td><?= $k+1 ?></td>
                                                <?php if (in_array('Admin', $session_usertype)) { ?>
                                                    <td><?php
                                                    $usertype = explode(",", $user['usertype']);
                                                    if ($usertype) {
                                                        $count = count($usertype);
                                                        foreach ($usertype as $key => $value) {
                                                            if (isset($value)) {
                                                                echo "<b>[" . h($value) . "]</b>";
                                                                if ($key == 1) {
                                                                    if ($count > 2) {
                                                                        echo "<br /><br />";
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                ?></td>
                                                <?php } ?>
                                                <td><?= h($user['akcessId']) ?></td>
                                                <td>
                                                    <?= $this->Html->link(__($user['name']), ['action' => 'view', $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'View']) ?> 
                                                </td>                                       
                                                <td><?= h($user['email']) ?></td>
                                                <td><?= h($user['mobileNumber']) ?></td>
                                                <td class="actions">
                                                    <div class="actions-div">
                                                        
                                                        <?= $this->Html->link(__('<i class="fa fa-eye"></i>'), ['action' => 'view', $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'View']) ?>

                                                        <?php if (in_array('Admin', $session_usertype)) { ?>
                                                        
                                                            <?= $this->Html->link(__('<i class="fa fa-edit green-txt"></i>'), ['action' => 'edit', $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Edit']) ?>

                                                        
                                                            <?= $this->Html->link(__('<i class="fa fa-trash red-txt"></i>'), ['action' => 'delete', $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Remove', 'class' => 'delete_btn', 'data-link' => 'remove_btn' . $user_id]) ?> 
                                                        
                                                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $user_id], ['escape' => false, 'id' => 'remove_btn' . $user_id, 'style' => 'display:none;']) ?>
                                                        <?php } ?>
                                                        
                                                    </div>
                                                </td>
                                            </tr>
                                    <?php $k++; } ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                    </div>
                </div>
            </div>
        </div>
   </div>
</div>

<!-- Delete Students -->
<div id="delete_modal" class="modal " tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm">
        <div class="modal-content" >
            <div class="modal-body">                
                <p class="text-center">This student will be removed from all classes. Are you sure you want to delete?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary waves-effect" id="yes_btn">Yes</button>
                <button class="btn btn-info waves-effect" id="no_btn" data-dismiss="modal">No</button>
            </div>            
        </div>
    </div>
</div>

<!-- Send Modal -->
<div class="modal " id="sendInvitationModalModule" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form" action="<?php echo $url; ?>messaging/SendInvitationData" id="SendInvitationData" method="POST" class="mb-0">
                <input type="hidden" id="eid" name="eid" value=""/>                
                <div class="modal-header">                
                    <h4 class="modal-title" id="myModalLabel">Send Invitation</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center justify-content-around">    
                        <div>                           
                            <input class="with-gap radio-col-light-blue" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="email" >
                            <label for="inlineRadio1">Email</label>
                        </div>                      
                        <div>
                            <input class="with-gap radio-col-light-blue" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="phone" >
                            <label for="inlineRadio2">Phone</label>
                        </div>
                    </div>
                    <div>
                        <div class="form-group email">      
                            <input type="text" id="email_search" name="email_search" placeholder="Enter Email" class="form-control input_tokenize">                      
                            <input type="hidden" id="email" name="email" value="">                                 
                            <label>Body</label>
                            <textarea class="form-control" id="messagee" name="message" rows="6"></textarea>
                        </div>
                        <div class="form-group phone list_wrapper">
                            <label>Body</label>
                            <textarea class="form-control" id="messagep" name="messagep" rows="6"></textarea>
                            <div class="form-row align-items-center">
                                <div class="col-4 col-md-4">
                                    <select class="custom-select invitation-country-list" name="field[0][country_code]" id="country_code">
                                        <option selected>Select Country Code</option>
                                        <?php foreach ($countries as $countrie) { ?>
                                            <option value="<?php echo $countrie->calling_code; ?>"><?php echo "+" . $countrie->calling_code . " ( " . $countrie->country_name . " ) "; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-7 col-md-7">
                                    <input type="text" class="form-control" id="phone" name="field[0][phone]" placeholder="Enter Phone" />
                                </div>
                                <div class="col-1 text-center">
                                    <a href="javascript:void(0);" class="btn waves-effect waves-light btn-info" onclick="list_add_button()"><i class="fa fa-plus"></i></a>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="send-invitation-eForm-list">
                                        <select class="custom-select invitation-eForm-list" name="eFormId" id="eForm-id">
                                            <option value='' selected>Select eForm</option>
                                            <?php foreach ($eform_list as $el) { ?>
                                                <option value="<?= $el->id; ?>"><?= $el->formName ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                    <button type="button" class="btn btn-primary waves-effect" id="submit" onclick="sendInvitationData()">Send</button>
                    <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                </div>
            </form> 
        </div>
    </div>
</div>

<!-- Send ID Card Modal -->
<div class="modal " id="sendPortalModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form" action="<?php echo $url; ?>messaging/SendPortalData" id="SendPortalData" method="POST" class="mb-0">
                <input type="hidden" id="eid" name="eid" value="student"/>      
                <div class="modal-header">                
                    <h4 class="modal-title" id="myModalLabel">Send To Portal</h4>
                    <a href="javascript:void(0);" class="close" data-dismiss="modal">&times;</a>
                </div>
                <div class="modal-body">                    
                    <div>
                        <div class="form-group ackess">  
                            <select name="portal" id="portal" class="form-control">
                            <option value="">Select Portal</option>
                                <option value="www.techventures.bank">Tech Ventures Bank</option>
                            </select>
                        </div>
                        <div class="row">
                           <div class="col-md-12">
                                <div class="form-group ackess">  
                                    <div class="form-group">
                                        <select name="ackess[]" multiple id="multiple_portal" class="form-control">
                                            <?php
                                                foreach ($usersStudents as $user) {
                                                    $akcessId = '';
                                                    if (isset($user['akcessId']) && $user['akcessId'] != "") {
                                                        $akcessId = $user['akcessId'];
                                                        $name = $user['name'] . " ( " . $user['akcessId'] . " ) ";  
                                                        if (isset($akcessId)) {
                                                            ?>
                                                            <option value="<?php echo $user['akcessId']; ?>"><?php echo $name; ?></option>
                                                        <?php
                                                        }
                                                    }
                                                }
                                                ?>
                                        </select>   
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary waves-effect" id="sendPortalDatasubmit" onclick="sendPortalData()">Send <span class="btn_text"></span></button>
                    <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                </div>
            </form>
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
        html += '<div class="form-row align-items-center mt-1 remove_all remove-' + x + '">';
        html += '<div class="col-4 col-md-4">';
        html += '<select class="custom-select invitation-country-list" name="field[' + x + '][country_code]" id="country_code">';
        html += '<option selected>Select Country Code</option>';
        <?php foreach ($countries as $countrie) { ?>
        html +=
            "<option value='<?php echo $countrie->calling_code; ?>'><?php echo '+' . $countrie->calling_code . ' ( ' . $countrie->country_name . ' ) '; ?></option>";
        <?php } ?>
        html += '</select>';
        html += '</div>';
        html += '<div class="col-7 col-md-7">';
        html += '<input type="text" class="form-control" id="phone" name="field[' + x +
            '][phone]" placeholder="Enter Phone" />';
        html += '</div>';
        html += '<div class="col-1">';
        html +=
            '<a href="javascript:void(0);" class="btn waves-effect waves-light btn-danger" onclick="list_remove_button(' +
            x + ')"><i class="fa fa-minus"></i></a>';
        html += '</div>';
        html += '</div>';

        $('.list_wrapper').append(html);
        x++;

        $('.invitation-country-list').select2({
             placeholder: "Select Country Code",
        });
    }

    function list_remove_button(value) {
        $('.remove-' + value).remove();
    }

    function searchUserType(){
        var e = document.getElementById("search_user_type");
        var strUserType = e.value;
        document.location.href = burl + '/users?s=' + strUserType;
    }
    </script>
