<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
use Cake\Datasource\ConnectionManager;

$session_usertype = explode(",", $session_user['usertype']);

?>
<div class="staff-list-page">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">                              
                    <div class="table-responsive m-t-10">
                        
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

                        <div class="row float-right m-0">
                                                <?php if(in_array('Admin',$session_usertype)) { ?>
                                                <h4><?php echo $this->Html->link(__('<i class="fas fa-plus mr-1"></i> Add Admin'), ['action' => 'addAdmin'], ['class' => 'btn waves-effect waves-light btn-primary mr-3', 'escape' => false]); ?></h4>
                                                <?php } ?>
                                                <h4><?php echo $this->Html->link(__('<i class="fa fa-recycle"></i>'), ['action' => 'adminRecycle'], ['class' => 'btn waves-effect waves-light btn-warning', 'escape' => false, 'data-toggle' => "tooltip", 'title'=>'Recycle']) ?>
                                                </h4>
                                                <h4>
                                                    <?= $this->Html->link(__('<i class="fas fa-arrow-circle-left"></i> '),'management',  ['class' => 'btn waves-effect waves-light btn-info ml-3','escape' => false, 'data-toggle' => "tooltip", 'title'=>'Back']) ?>
                                                </h4>
                                            </div>
                        <table id="example1" class="table table-hover table-striped table-bordered responsive nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <?php if(in_array('Admin',$session_usertype)) { ?>
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
                                    <td><?= $k+1; ?></td>
                                    <?php if(in_array('Admin',$session_usertype)) { ?>
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
                                        <?= $this->Html->link(__($user['name']), ['action' => 'view-admin', $user_id], ['escape' => false]) ?>
                                    </td>
                                    <td><?= h($user['email']) ?></td>
                                    <td><?= h($user['mobileNumber']) ?></td>
                                    <td class="actions">
                                        <div class="actions-div">

                                            <?= $this->Html->link(__('<i class="fa fa-eye"></i>'), ['action' => 'view-admin', $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'View']) ?>

                                            <?php if(in_array('Admin',$session_usertype)) { ?>
                                            <?= $this->Html->link(__('<i class="fa fa-edit green-txt"></i>'), ['action' => 'edit-admin', $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Edit']) ?>

                                            <?= $this->Html->link(__('<i class="fa fa-trash red-txt"></i>'), ['action' => 'delete', $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Remove', 'class' => 'delete_btn', 'data-link' => 'remove_btn' . $user_id]) ?>

                                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $user_id], ['escape' => false, 'id' => 'remove_btn' . $user_id, 'style' => 'display:none;']) ?>
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
</div>

<!-- Delete Admin -->
<div id="delete_modal" class="modal " tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <p class="text-center">Are you sure you want to delete this admin?</p>
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
    document.location.href = burl + '/users/admin-list?s=' + strUserType;
}

</script>