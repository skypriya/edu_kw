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
<style>
   fieldset {
            padding: 0px 12px 0px 26px !important;
            border: 1px solid #cdd1d5 !important;
            margin-bottom: 12px;
            border-radius: 6px;
        }
        legend {
            width: auto !important;
            padding: 0px 10px 0px 10px !important;
            font-size: 18px !important;
            color: #1976d2;
        }
        .btn-section {
            margin-top: 0px;
        }
        .dt-buttons{
            padding: 0px !important;
            float: right !important;
        }
        .dt-buttons .dt-button {
            padding: 8px 14px !important;
                font-size: 12px;
            }
        .input-group-addon {
            border-top-right-radius: 0px;
            border-bottom-right-radius: 0px;
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }
        .excel-btn, .tbl-print-btn{
           display:none;
        }
        .page-titles{
        display:none;
        }
</style>
<div class="row">
    <div class="col-12">
        <input type="hidden" value="<?= isset($idEncode) ? $idEncode : '' ?>" id="idEncode">
        <div class="card card-outline-info mt-4">
           <div class="card-header">
               <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-b-0 text-white d-flex align-items-center"><i class="fal fa-users-class mr-1"></i>Class Report

                        <?= $this->Html->link(__('<i class="fas fa-arrow-circle-left"></i> Back '),'attendance-report',  ['class' => 'text-white float-right ml-3','escape' => false, 'data-toggle' => "tooltip", 'title'=>'Back']) ?>
                    </div>
                    <div class="col-md-6">
                        <a href="javascript:void(0)" id="export-to-excel-btn" style="" class="text-white float-right">EXPORT TO EXCEL</a>
                        <a href="javascript:void(0)" id="print-btn" style="" class="text-white float-right mr-3">PRINT</a>
                    </div>
               </div>



                        </div>
            <div class="card-body report-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-400">Name</label>
                        <div id="class-name"><?= isset($class_data['class_name']) ? $class_data['class_name'] : '-'; ?></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-400">Number of students</label>
                        <div id="no-of-student"><?= isset($last_class_atten_total_student) ? $last_class_atten_total_student : '-'; ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-400">Location</label>
                        <div id="location-name"><?= isset($class_data['location_name']) ? $class_data['location_name'] : '-'; ?></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-400">Teacher's name</label>
                        <div id="teacher-name"><?= isset($class_data['teacher_name']) ? $class_data['teacher_name'] : '-'; ?></div>
                    </div>
                </div>
            </div>



                <fieldset>
                    <legend>Logs :</legend>
                    <div class="table-responsive">
                    <div class="btn-section text-right">
 <div class='input-group date' style="width: 180px;float: left;margin-right: 10px;">
                                             <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                             </span>
                                                                                          <input type='text' name="attendanceReportDate" value="<?= isset($attendance_date) ? $attendance_date : ''; ?>"  data-val="<?= isset($attendance_date) ? $attendance_date : '' ?>" class="form-control attendance-report-date" />
                                         </div>                                                    </div>
                                        <table id="class-attendance-report-tbl" class="table table-hover table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Student name</th>
                                                    <th>AKcess ID</th>
                                                    <th>Check-in date</th>
                                                    <th>Check-in time</th>
                                                    <th>Check-out time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                if(!empty($res))
                                                {
                                                   $no = 0;
                                                    foreach($res as $r){ ?>
                                                         <tr>
                                                            <td><?= ++$no; ?></td>
                                                            <td><?= isset($r['student_name']) && $r['student_name'] ? $r['student_name'] : '' ?></td>
                                                            <td><?= isset($r['akcess_id']) && $r['akcess_id'] ? $r['akcess_id'] : '-' ?></td>
                                                            <td><?= isset($r['checkin_date_time']) && $r['checkin_date_time'] ? date('d/m/Y',strtotime($r['checkin_date_time'])) : '-' ?></td>
                                                            <td><?= isset($r['checkin_date_time']) && $r['checkin_date_time'] ? date('h:i A',strtotime($r['checkin_date_time'])) : '-' ?></td>
                                                            <td><?= isset($r['checkout_date_time']) && $r['checkout_date_time'] ? date('h:i A',strtotime($r['checkout_date_time'])) : '-' ?></td>
                                                         </tr>
                                                    <?php }
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                </fieldset>


            </div>
        </div>
    </div>
</div>