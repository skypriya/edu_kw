<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */

use Cake\Datasource\ConnectionManager;

$session_usertype = explode(",", $session_user['usertype']);

?>
<div class="staff-delete-page">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive m-t-10">
                        <div class="form-group d-flex align-items-center">
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
                        <div class="row float-right m-0">
                            <h4><?php echo $this->Html->link(__('<i class="fas fa-arrow-circle-left"></i>'), ['action' => 'staffList'], ['class' => 'btn waves-effect waves-light btn-info', 'escape' => false, 'data-toggle' => "tooltip", 'title'=>'Back']) ?></h4>
                        </div>
                        <table id="staff_recyle" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <?php if (in_array('Admin',$session_usertype)) { ?>
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
                                <?php $k = 0; foreach ($users as $user): ?>
                                    <?php $user_id = ConnectionManager::userIdEncode($user['id']); ?>
                                    <tr>
                                       <td><?= $k+1 ?></td>
                                       <?php if (in_array('Admin',$session_usertype)) { ?>
                                        <td><?php
                                            $usertype = explode(",",$user['usertype']);
                                            if($usertype) {
                                                $count = count($usertype);
                                                foreach($usertype as $key => $value) {
                                                    if(isset($value)) {
                                                        echo "<b>[" . h($value) . "]</b>";
                                                        if($key == 1){
                                                            if($count > 2) {
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
                                           <?= $this->Html->link(__($user['name']), ['action' => 'view-staff', $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'View']) ?> 
                                       </td>                                        
                                       <td><?= h($user['email']) ?></td>
                                       <td><?= h($user['mobileNumber']) ?></td>
                                       <td class="actions">
                                           <div class="actions-div">
                                               <?= $this->Html->link(__('<i class="fa fa-eye"></i>'), ['action' => 'view-staff', $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'View']) ?>

                                               <?php if (in_array('Admin',$session_usertype)) { ?>  
                                               <?= $this->Html->link(__('<i class="fa fa-undo green-txt"></i>'), ['action' => 'restore', $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Restore','class' => 'delete_btn', 'data-link' => 'remove_btn' . $user_id, 'style' => 'display:none;']) ?> 

                                               <?= $this->Form->postLink(__('Restore'), ['action' => 'restore', $user_id], ['escape' => false, 'id' => 'remove_btn' . $user_id, 'style' => 'display:none;']) ?>
                                               <?php } ?>
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

<!-- Restore Staff -->
<div id="delete_modal" class="modal " tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <p class="text-center">Are you sure you want to restore this staff user?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary waves-effect" id="yes_btn">Yes</button>
                <button class="btn btn-info waves-effect" id="no_btn" data-dismiss="modal">No</button>
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
function searchUserType(){
    var e = document.getElementById("search_user_type");
    var strUserType = e.value;
    document.location.href = burl + '/users/staff-recycle?s=' + strUserType;
}
</script>

