<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
use Cake\ORM\TableRegistry;
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title"><i class="fal fa-chart-line mr-1"></i><?= $page_title ?></h4>
                    <h4><?php echo $this->Html->link(__('<i class="fas fa-arrow-circle-left"></i>'), ['action' => 'index'], ['class' => 'btn waves-effect waves-light btn-info', 'escape' => false]) ?></h4>
                </div>
                <div class="table-responsive m-t-10">
                    <table id="auditTrail" class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Name</th>
                                <th scope="col">User Name</th>
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
                                    <td><?= $n['username'] ?></td> 
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