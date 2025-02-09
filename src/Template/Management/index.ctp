<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
use Cake\Datasource\ConnectionManager;

?>
<div class="students-list-page">
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
                            <h4><?php echo $this->Html->link(__('<i class="fas fa-user-plus mr-1"></i> Invitation List'), ['action' => '../invitationlist'], ['class' => 'btn waves-effect waves-light btn-primary mr-3', 'escape' => false]); ?></h4>
                            <h4><?php echo $this->Html->link(__('<i class="fas fa-users-crown mr-1"></i> Admin List'), ['action' => '../users/admin-list'], ['class' => 'btn waves-effect waves-light btn-info', 'escape' => false]) ?></h4>
                        </div>
                        <table id="users-type" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Roles</th>
                                    <th>AKcess ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Date</th>
                                    <th class="text-center"><?= __('Actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                 <?php $k = 0; foreach ($users as $user): ?>
                                    <?php $user_id = ConnectionManager::userIdEncode($user['id']); ?>
                                     <tr>
                                        <td><?php
                                            $usertype = explode(",",$user['usertype']);
                                            if($usertype) {
                                                $count = count($usertype);
                                                foreach($usertype as $key => $value) {
                                                    if(isset($value)) {
                                                        echo "<b>[" . h($value) . "]</b>";
                                                        if($key == 1){
                                                            if($count > 2) {
                                                                echo "<br />";
                                                            }
                                                            
                                                        }
                                                    }
                                                }
                                            }
                                          ?></td>
                                        <td>
                                            <?= $user['akcessId'] ?> 
                                        </td> 
                                        <td>
                                            <?= $user['name'] ?> 
                                        </td>                                       
                                        <td><?= h($user['email']) ?></td>
                                        <td data-order="<?php echo date("Y-m-d", strtotime($user['created'])); ?>"><?php echo date("d/m/Y", strtotime($user['created'])); ?></td>
                                        <td class="actions">
                                            <div class="actions-div">
                                                
                                                <?= $this->Html->link(__('<i class="fa fa-edit green-txt"></i>'), ['action' => 'edit', $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Edit']) ?>
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
    document.location.href = burl + '/management?s=' + strUserType;
}

</script>
