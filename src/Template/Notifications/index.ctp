<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

$this->Users = TableRegistry::get('Users'); 

$url = BASE_ORIGIN_URL;

$session_usertype = explode(",", $session_user['usertype']);
?>
<div class="staff-list-page">
    <?php /*if(in_array('Admin',$session_usertype)) { ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="button-group text-right">
                        <?php echo $this->Html->link(__('<i class="fas fa-plus mr-1"></i> Push Notification'), ['action' => 'notify'], ['class' => 'btn waves-effect waves-light btn-primary', 'escape' => false]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php }*/ ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive m-t-10">
                        <table id="example1" class="table table-hover table-striped table-bordered notification-list">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>User name</th>
                                    <th>AKcess ID</th>
                                    <th>Type</th>
                                    <th>Date Time</th>
                                    <th><?= __('Actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>


                                <?php
                                $no=0;
                                foreach ($notify as $n):
                                $badge = $n['status'] == 0 ? '<span class="badge">New</span>' : '';
                                ?>
                                <?php $id = ConnectionManager::userIdEncode($n['id']); ?>
                                <tr>
                                    <td><?= ++$no; ?></td>
                                    <td><?= $n['name'].$badge; ?></td>
                                    <td><?= $n['akcessId']; ?></td>
                                    <td><b><?= $n['action'] == 'register' ? 'Registration request' : 'eForm submitted'; ?></b></td>
                                    <td><?= $n['created_at'] ?></td>
                                    <td class="actions">
                                        <?php
                                        if($n['status'] == 0){
                                            echo '<span class="label label-primary change-notification-status" data-id="'.$id.'">RECEIVED</span>';
                                        }
                                        else
                                        {
                                            echo '<span class="label label-primary" style="background-color: green;">SEEN</span>';
                                        }

                                        ?>
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
</div>

<div id="delete_modal" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <p class="text-center">Are you sure you want to delete this notification?</p>
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