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
            <div class="card-body report-section">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h3 class="text-themecolor">Today's Classes</h3>
                    </div>
                    <div class="col-md-6">
                        <a href="javascript:void(0)" data-style="list" data-for="today" class="btn waves-effect waves-light btn-secondary report-data-style-btn list-view pull-right" style="margin-right: 8px;"><i class="fa fa-bars"></i> List view</a>
                        <a href="javascript:void(0)" data-style="grid" data-for="today" class="btn waves-effect waves-light btn-info report-data-style-btn grid-view pull-right" style="margin-right: 8px;"><i class="fa fa-th-large"></i> Grid view</a>
                    </div>
                </div>

                <div class="row" id="grid-section">
                   <?php
                   if(!empty($res))
                   {
                       foreach($today_class_res as $r){
                       $class_id = ConnectionManager::userIdEncode($r['id']);
                       $class_btn = isset($r['class_is_open']) && $r['class_is_open'] ? 'c-open' : 'c-close';
                       ?>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header text-white">
                                        <?= $this->Html->link(__(isset($r['class_name']) && $r['class_name'] ? $r['class_name'] : ''), ['action' => 'class-attendance', $class_id], ['escape' => false,'class'=>'text-white']) ?>
                                        <span class="pull-right <?= $class_btn ?>"></span>
                                    </div>
                                    <div class="card-body">
                                        <h6><span class="r-title">Campus :</span> <?= isset($r['location_name']) && $r['location_name'] ? $r['location_name'] : '-' ?></h6>
                                        <h6><span class="r-title">Teacher :</span> <?= isset($r['teacher_name']) && $r['teacher_name'] ? $r['teacher_name'] : '-' ?></h6>
                                        <h6><span class="r-title">No of student checked-in :</span> <?= isset($r['total_user']) && $r['total_user'] ? $r['total_user'] : '-' ?></h6>
                                    </div>
                                </div>
                            </div>
                       <?php }
                   }
                   else
                   {
                       echo "<div class='col-md-12 text-center'><h3>Today all classes closed.</h3></div>";
                   }
                   ?>
                </div>

                <div class="table-responsive" id="list-section" style="display:none;">
                    <table id="attendance-report-tbl" class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Class</th>
                                <th>Campus</th>
                                <th>Teacher</th>
                                <th>No of student checked-in</th>
                                <th>Current status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            if(!empty($res))
                            {
                               $no = 0;
                                foreach($today_class_res as $r){
                                $class_id = ConnectionManager::userIdEncode($r['id']);
                                ?>
                                     <tr>
                                        <td><?= ++$no; ?></td>
                                        <td>
                                            <?= $this->Html->link(__(isset($r['class_name']) && $r['class_name'] ? $r['class_name'] : ''), ['action' => 'class-attendance', $class_id], ['escape' => false]) ?>
                                        </td>
                                        <td><?= isset($r['location_name']) && $r['location_name'] ? $r['location_name'] : '-' ?></td>
                                        <td><?= isset($r['teacher_name']) && $r['teacher_name'] ? $r['teacher_name'] : '-' ?></td>
                                        <td><?= isset($r['total_user']) && $r['total_user'] ? $r['total_user'] : '-' ?></td>
                                        <td><?= isset($r['class_is_open']) && $r['class_is_open'] ? '<span class="label label-primary" style="background-color: green;">Open</span>' : '<span class="label label-danger">Closed</span>'; ?></td>
                                     </tr>
                                <?php }
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body report-section">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h3 class="text-themecolor">All Classes</h3>
                    </div>
                    <div class="col-md-6">
                        <a href="javascript:void(0)" data-style="list" data-for="all" class="btn waves-effect waves-light btn-secondary report-data-style-btn all-list-view pull-right" style="margin-right: 8px;"><i class="fa fa-bars"></i> List view</a>
                        <a href="javascript:void(0)" data-style="grid" data-for="all" class="btn waves-effect waves-light btn-info report-data-style-btn all-grid-view pull-right" style="margin-right: 8px;"><i class="fa fa-th-large"></i> Grid view</a>
                    </div>
                </div>

                <div class="row" id="all-grid-section">
                   <?php
                   if(!empty($res))
                   {
                       foreach($res as $r){
                       $class_id = ConnectionManager::userIdEncode($r['id']);
                       $class_btn = isset($r['class_is_open']) && $r['class_is_open'] ? 'c-open' : 'c-close';
                       ?>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header text-white">
                                        <?= $this->Html->link(__(isset($r['class_name']) && $r['class_name'] ? $r['class_name'] : ''), ['action' => 'class-attendance', $class_id], ['escape' => false,'class'=>'text-white']) ?>
                                        <span class="pull-right <?= $class_btn ?>"></span>
                                    </div>
                                    <div class="card-body">
                                        <h6><span class="r-title">Campus :</span> <?= isset($r['location_name']) && $r['location_name'] ? $r['location_name'] : '-' ?></h6>
                                        <h6><span class="r-title">Teacher :</span> <?= isset($r['teacher_name']) && $r['teacher_name'] ? $r['teacher_name'] : '-' ?></h6>
                                        <h6><span class="r-title">No of student checked-in last class :</span> <?= isset($r['total_user']) && $r['total_user'] ? $r['total_user'] : '-' ?></h6>
                                    </div>
                                </div>
                            </div>
                       <?php }
                   }
                   else
                   {
                       echo "<div class='col-md-12 text-center'><h3>Today all classes closed.</h3></div>";
                   }
                   ?>
                </div>

                <div class="table-responsive" id="all-list-section" style="display:none;">
                    <table id="attendance-report-tbl" class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Class</th>
                                <th>Campus</th>
                                <th>Teacher</th>
                                <th>No of student checked-in last class</th>
                                <th>Current status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            if(!empty($res))
                            {
                               $no = 0;
                                foreach($res as $r){
                                $class_id = ConnectionManager::userIdEncode($r['id']);
                                ?>
                                     <tr>
                                        <td><?= ++$no; ?></td>
                                        <td>
                                            <?= $this->Html->link(__(isset($r['class_name']) && $r['class_name'] ? $r['class_name'] : ''), ['action' => 'class-attendance', $class_id], ['escape' => false]) ?>
                                        </td>
                                        <td><?= isset($r['location_name']) && $r['location_name'] ? $r['location_name'] : '-' ?></td>
                                        <td><?= isset($r['teacher_name']) && $r['teacher_name'] ? $r['teacher_name'] : '-' ?></td>
                                        <td><?= isset($r['total_user']) && $r['total_user'] ? $r['total_user'] : '-' ?></td>
                                        <td><?= isset($r['class_is_open']) && $r['class_is_open'] ? '<span class="label label-primary" style="background-color: green;">Open</span>' : '<span class="label label-danger">Closed</span>'; ?></td>
                                     </tr>
                                <?php }
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>