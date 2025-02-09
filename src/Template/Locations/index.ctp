<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */

use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

$url = BASE_ORIGIN_URL;

$session_usertype = explode(",", $session_user['usertype']);
?>
<div class="staff-list-page">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                    <div class="row float-right m-0">
                        <?php if(in_array('Admin',$session_usertype)) { ?>
                            <h4><?php echo $this->Html->link(__('<i class="fas fa-plus mr-1"></i> Add Location'), ['action' => 'add'], ['class' => 'btn waves-effect waves-light btn-primary', 'escape' => false]); ?></h4>
                        <?php } ?>
                    </div>
                        <table id="locationTable" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Campus Name</th>
                                    <th>User Allow</th>
                                    <th>Open From</th>
                                    <th>Open To</th>
                                    <th>Address</th>
                                    <th class="text-center"><?= __('Actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $k = 0;
                            foreach ($locations as $key => $locations): ?>
                            <?php $id = ConnectionManager::userIdEncode($locations['id']); ?>
                                <tr>
                                    <td><?= $this->Number->format($key+1) ?></td>
                                    <td><?= h($locations['lname']) ?></td>

                                    <td><?= $locations['uname'] ?></td>

                                    <td><?php echo $locations['openFrom']; ?></td>
                                    <td><?php echo $locations['openTo']; ?></td>

                                    <td><?= h($locations['address']) ?></td>
                                    <td class="actions">
                                        <div class="actions-div">
                                            <?= $this->Html->link(__('<i class="fa fa-edit green-txt" aria-hidden="true"></i>'), ['action' => 'edit', $id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Edit']) ?>

                                            <?php

                                            $conn = ConnectionManager::get("default"); // name of your database connection

                                            $query = 'SELECT * FROM sclasses where soft_delete = 0 AND location = '.$locations["id"];

                                            $querySql = $conn->execute($query);

                                            $query_response = $querySql->fetch('assoc');

                                            if(isset($query_response['id']) && $query_response['id'] != "") {
                                            ?>
                                            <a href="javascript:void(0);" class="not_delete_btn" data-link="remove_btn2"><i class="fa fa-trash red-txt" aria-hidden="true" data-toggle="tooltip" title='You can not delete this location because its being used in the Class.'></i></a>

                                            <?php } else { ?>
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


<div id="not_delete_modal" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <p class="text-center">You can not delete this location because its being used in the Class.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-info waves-effect" id="ok_btn" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>
</div>

<div id="delete_modal" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <p class="text-center">Are you sure you want to delete this location?</p>
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