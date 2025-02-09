<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ClassAttend $classAttend
 */

use Cake\Datasource\ConnectionManager;

$conn = ConnectionManager::get("default"); // name of your database connection  

$classAttend_id = ConnectionManager::userIdEncode($classAttend->id); 

$soft_delete = $sclasses->soft_delete;
?>

<div class="view-staff-page">
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <?php if($session_user['usertype'] == 'Admin'){ ?>
                <div class="card-body">
                    <div class="button-group text-right">
                        <?php if($soft_delete == 0) { ?>

                        <?= $this->Html->link(__('<i class="fas fa-edit mr-1"></i> Edit'), ['action' => 'edit', $classAttend_id], ['escape' => false, 'class' => 'btn waves-effect waves-light btn-success', 'data-toggle' => "tooltip", 'title'=>'Edit']) ?>
                        
                        <?php } ?>

                        <?= $this->Html->link(__('<i class="fas fa-times mr-1"></i> Close'), ['action' => 'index'], ['escape' => false, 'class' => 'btn waves-effect waves-light btn-danger', 'data-toggle' => "tooltip", 'title'=>'Close']) ?>

                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-9"> 
            <div class="card card-outline-info">
                <div class="card-header">
                    <h4 class="m-b-0 text-white d-flex align-items-center"><i class="fal fa-clipboard-user mr-1"></i><?= $class->name ?> Class</h4>
                </div>
                <div class="card-body">
                    <div class="form-body">
                        <div class="row p-t-20">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Name') ?></label>
                                    <div><?= h($class->name) ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Attends') ?></label>
                                    <div><?= $this->Number->format($classAttend->attends) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Created') ?></label>
                                    <div><?= h($gate->created) ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Modified') ?></label>
                                    <div><?= h($gate->modified) ?></div>
                                </div>
                            </div>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Checked In</h4>
                    <div class="row">
                        <div class="col-6">
                            <h2 class="m-0"><i class="fal fa-users"></i> <?= $count_staff ?></h2>
                            <h6>Staff</h6>
                        </div>
                        <div class="col-6 text-right p-l-0">
                            <h2 class="m-0"><i class="fal fa-chalkboard-teacher"></i> <?= $count_teacher ?></h2>
                            <h6>Teachers</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Checked In</h4>
                    <div class="row">
                        <div class="col-6">
                            <h2 class="m-0"><i class="fal fa-user-graduate"></i> <?= $count_students ?></h2>
                            <h6>Students</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>
</div>

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