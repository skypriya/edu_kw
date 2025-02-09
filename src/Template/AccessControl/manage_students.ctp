<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

$this->Users = TableRegistry::get('Users'); 

$sclassid = ConnectionManager::userIdEncode($sclass->id);

$session_usertype = explode(",", $session_user['usertype']);

$url = rtrim(ConnectionManager::base_url(TRUE), '/');
?>

<div class="staff-list-page">

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive m-t-10">
                    <div class="row float-right m-0">
                                            <?php
                                             if((in_array('Admin',$session_usertype) || in_array('Teacher',$session_usertype))){
                                             if(isset($sclassid)){ ?>
                        <div class="button-group text-left">                        
                            <?= $this->Html->link(__('<i class="fas fa-arrow-circle-left"></i> '),['action' => 'index'],  ['class' => 'btn waves-effect waves-light btn-info','escape' => false, 'data-toggle' => "tooltip", 'title'=>'Back']) ?>
                        </div>
                        <?php } ?>
                        <div class="button-group text-right">
                            <?= $this->Html->link(__('<i class="fas fa-plus mr-1"></i> Add User'), ['action' => 'addStudents', $sclassid], ['class' => 'btn waves-effect waves-light btn-primary','escape' => false]) ?>
                        </div>
    <?php } ?>    
                    </div>                  
                        <table id="manage_students_table" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>AKcess ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile Number</th>
                                    <th class="text-center"><?= __('Actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>

                            <?php 
                              $k = 0;
                              foreach ($all as $a): 
                                    $id = ConnectionManager::userIdEncode($a->id);
                                    $user = $this->Users->find()->where(['id' => $a->userId])->first();
                                    $userid = ConnectionManager::userIdEncode( $user->id );
                                    $sclassid = ConnectionManager::userIdEncode( $sclass->id );
                                    
                                    ?>
                                <tr>
                                    <td><?= h($user->akcessId) ?></td>
                                    <td>
                                        <input type="hidden" name="userId[]" id="<?= $userid ?>" value="<?= $userid ?>" />
                                        <a href="<?php echo $url; ?>/users/view/<?php echo $userid; ?>?type=sclasses"><?php echo $user->name; ?></a>
                                    </td>
                                    <td><?= h($user->email) ?></td>
                                    <td><?= h($user->mobileNumber) ?></td>
                                    <td class="actions">
                                        <div class="actions-div">
                                            <a href="<?php echo $url; ?>/users/view/<?php echo $userid; ?>?type=sclasses" data-toggle="tooltip" title="" data-original-title="View"><i class="fa fa-eye" aria-hidden="true"></i></a>

                                            <?= $this->Html->link(__('<i class="fa fa-trash red-txt" aria-hidden="true"></i>'), ['action' => 'deleteStudent', $id, $sclassid], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Remove', 'class' => 'delete_btn', 'data-link' => 'remove_btn'.$id] ) ?>

                                            <?= $this->Form->postLink(__('Delete'), ['action' => 'deleteStudent', $id, $sclassid], ['escape' => false, 'id' => 'remove_btn'.$id, 'style' => 'display:none;']  ) ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                      $k++;
                                    endforeach;   
                              ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="delete_modal" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <p class="text-center">Are you sure you want to delete this user?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary waves-effect" id="yes_btn">Yes</button>
                <button class="btn btn-info waves-effect" id="no_btn" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="myModalaplus" class="modal " role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>

            </div>
            <div class="modal-body" id="contentBody">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
