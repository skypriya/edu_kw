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

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="btn-section">
                    <?= $this->Html->link(__('<i class="fas fa-arrow-circle-left"></i> '),$this->request->referer(),  ['class' => 'btn waves-effect waves-light btn-info','escape' => false, 'data-toggle' => "tooltip", 'title'=>'Back']) ?>
                </div>
                <div class="table-responsive m-t-10">
                    <table id="auditTrail" class="table table-hover table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Name</th>
                                                    <th scope="col">Control Type</th>
                                                     <?php if(!in_array('Student',$session_usertype)) {
                                                                                        echo '<th scope="col">Student Name</th>';
                                                                                     }?>
                                                    <th scope="col">Attendance Date</th>
                                                    <th scope="col">Check-in</th>
                                                    <th scope="col">Check-in Date</th>
                                                    <th scope="col">Check-out</th>
                                                    <th scope="col">Check-out Date</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($auditArray as $n): ?>
                                                    <tr>
                                                        <td><?php echo $n['id']; ?></td>
                                                        <td><?= $n['classname'] ?></td>
                                                        <td><?= $n['label_type'] ?></td>
                                                        <?php if(!in_array('Student',$session_usertype)) { ?>
                                                                                                <td><?= $n['username'] ?></td>
                                                                                            <?php
                                                                                            }?>
                                                        <td><?= date("d/m/Y H:i:s", strtotime($n['attendance_date_time'])) ?></td>
                                                        <td><?= $n['checkin'] ?></td>
                                                        <td><?php
                                                        if (isset($n['checkin_date_time']) && $n['checkin_date_time'] != null) {
                                                            echo date("d/m/Y H:i:s", strtotime($n['checkin_date_time']));
                                                        } ?></td>
                                                        <td><?= $n['checkout'] ?></td>
                                                        <td><?php
                                                        if (isset($n['checkout_date_time']) && $n['checkout_date_time'] != null) {
                                                            echo date("d/m/Y H:i:s", strtotime($n['checkout_date_time']));
                                                        } ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                </div>
            </div>
        </div>
    </div>
</div>


