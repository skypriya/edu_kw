<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ClassAttend[]|\Cake\Collection\CollectionInterface $classAttends
 */
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

$this->Sclasses = TableRegistry::get('Sclasses'); 
?>
<div class="staff-list-page">
    <?php if($session_user['usertype'] == 'Student' || $session_user['usertype'] == 'Admin'){ ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="button-group text-right">
                        <?php echo $this->Html->link(__('<i class="fas fa-plus mr-1"></i> Add Class'), ['action' => 'add'], ['class' => 'btn waves-effect waves-light btn-primary', 'escape' => false]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title"><i class="fal fa-clipboard-user mr-1"></i><?= $page_title ?></h4> 
                    </div>
                    <div class="table-responsive m-t-10">
                        <table id="student-attends" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th class="text-center"><?= __('Actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                          $k = 0;
                          foreach ($classAttends as $classAttend): ?>
                                <?php $classAttendid = ConnectionManager::userIdEncode($classAttend->id); ?>
                                <tr>
                                    <td><?= $this->Number->format($classAttend->id) ?></td>
                                    <?php  $sclass = $this->Sclasses->find()->where(['id' => $classAttend->classId])->first(); ?>
                                    <?php $sclassid = ConnectionManager::userIdEncode($sclass->id); ?>
                                    <td><?= h($sclass->name) ?></td>
                                    <td class="actions">
                                        <div class="actions-div">
                                            <?= $this->Html->link(__('<i class="fa fa-eye" aria-hidden="true"></i>'), ['controller' => 'ClassAttends', 'action' => 'view', $sclassid], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'View']) ?>

                                            <?php if($session_user['usertype'] == 'Admin'){ ?>

                                                <?= $this->Html->link(__('<i class="fa fa-trash red-txt" aria-hidden="true"></i>'), ['action' => 'delete', $classAttendid], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Remove', 'class' => 'delete_btn', 'data-link' => 'remove_btn'.$classAttendid] ) ?>

                                                <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $classAttendid], ['escape' => false, 'id' => 'remove_btn'.$classAttendid, 'style' => 'display:none;']  ) ?>

                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                $k++;
                              endforeach;  ?>
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
                <p class="text-center">Are you sure you want to delete this class?</p>
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