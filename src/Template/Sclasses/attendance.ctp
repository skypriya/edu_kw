<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Sclass[]|\Cake\Collection\CollectionInterface $sclasses
 */
use Cake\Datasource\ConnectionManager;

$url = BASE_ORIGIN_URL;

$session_usertype = explode(",", $session_user['usertype']);
$session_role_id = explode(",", $role_id);
?>
<div class="staff-list-page">
    <?php if(in_array('Admin',$session_usertype) || in_array('Teacher',$session_usertype)){ ?>
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
                        <?php if(in_array('Admin',$session_role_id)){ ?>
                            <h4><?= $this->Html->link(__('<i class="fa fa-recycle"></i>'), ['action' => 'classes-recycle'], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Recycle']) ?></h4>
                        <?php } ?>
                    </div>
                    <div class="table-responsive m-t-10">
                        <table id="class-example1" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>No.Students</th>
                                    <th>Days</th>
                                    <th>Open From</th>
                                    <th>Open To</th>
                                    <th>Location</th>
                                    <th>Teacher</th>
                                    <th class="text-center"><?= __('Actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $k = 0;
                                foreach ($sclasses as $key => $sclass): ?>
                                <?php $id = ConnectionManager::userIdEncode($sclass['id']); ?>
                                <tr>
                                    <td><?= $this->Number->format($key+1) ?></td>
                                    <td>
                                      <?= $this->Html->link(__(h($sclass['sname'])), ['action' => 'view', $id], ['escape' => false]) ?>
                                    </td>
                                    <td><?= $this->Number->format($sclass['userallow']) ?></td>
                                    <?php
                                        $iCount = 0;
                                        $days = "";
                                        $from = explode(',', $sclass['days']);
                                        foreach ($from as $iNum) {
                                            $days .= $iNum . '<br />';
                                        }
                                    ?>
                                    <td><?= $days ?></td>
                                    <?php
                                        $iCount = 0;
                                        $openFrom = "";
                                        $from = explode(',', $sclass['openFrom']);
                                        foreach ($from as $iNum) {
                                            $openFrom .= $iNum . '<br />';
                                        }
                                    ?>
                                    <td><?php echo $openFrom; ?></td>
                                    <?php
                                        $iCount = 0;
                                        $openTo = "";
                                        $from = explode(',', $sclass['openTo']);
                                        foreach ($from as $iNum) {
                                            $openTo .= $iNum . '<br />';
                                        }
                                    ?>
                                    <td><?= $openTo ?></td>
                                    <td><?= h($sclass['lname']) ?></td>
                                    <td><?= h($sclass['teacher']) ?></td>
                                    <td class="actions">
                                        <div class="actions-div">

                                            <?php if(in_array('Admin',$session_usertype) || in_array('Teacher',$session_usertype)){ ?>

                                            <?= $this->Html->link(__('<i class="fa fa-user-plus" aria-hidden="true"></i>'), ['action' => 'manageStudents', $id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Manage Students']) ?>

                                            <?= $this->Html->link(__('<i class="fal fa-chart-line" aria-hidden="true"></i>'), ['action' => 'attendanceReport', $id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Attendance Report']) ?>

                                            <?php } ?>

                                            <?= $this->Html->link(__('<i class="fa fa-eye" aria-hidden="true"></i>'), ['action' => 'view', $id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'View']) ?>

                                            <?php if(in_array('Admin',$session_usertype) || in_array('Teacher',$session_usertype)){ ?>

                                            <?= $this->Html->link(__('<i class="fa fa-edit green-txt" aria-hidden="true"></i>'), ['action' => 'edit', $id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Edit']) ?>

                                            <?= $this->Html->link(__('<i class="fa fa-trash red-txt" aria-hidden="true"></i>'), ['action' => 'delete', $id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Remove', 'class' => 'delete_btn', 'data-link' => 'remove_btn'.$id] ) ?>

                                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $id], ['escape' => false, 'id' => 'remove_btn'.$id, 'style' => 'display:none;']  ) ?>

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