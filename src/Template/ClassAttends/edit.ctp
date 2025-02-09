<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Sclass $sclass
 */

use Cake\Datasource\ConnectionManager;

$classAttend_id = ConnectionManager::userIdEncode($classAttend->id); 
?>
<div class="students-add-page eFormadd" id="add-global-form">
    <div class="row">
      <div class="col-12 col-md-8 col-xl-9 text-right">
          <a href="javascript:void(0);" onclick="saveForm();" class="btn btn-primary btn-sm"><i class="fas fa-save mr-1"></i>Update</a>
          <span class="ml-2">
            <?= $this->Html->link(__('List Class Attends'), ['action' => 'index'], ['escape' => false, 'class' => 'btn btn-primary btn-sm']) ?>
          </span>
      </div>  
    </div> 
    <div class="row">

    <div class="col-12 col-md-8 col-xl-9">       
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="card-title mb-0"><i class="fas fa-user-graduate mr-2"></i><?= $page_title ?></h4>	
                        </div>
                    </div>
                </div>  
                <?= $this->Form->create($classAttend) ?>
                <div class="card-body">  

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <?php echo $this->Form->input('classId', array('label' => 'classId', 'class' => 'form-control', 'value' => $classAttend->classId)); ?>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <?php echo $this->Form->input('userId', array('label' => 'userId', 'class' => 'form-control', 'value' => $classAttend->userId)); ?>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <?php echo $this->Form->input('attends', array('label' => 'attends', 'class' => 'form-control', 'value' => $classAttend->attends)); ?>
                        </div>
                    </div>
                                    
                </div>
                <div class="card-footer justify-content-center">
                    <?= $this->Form->button(__('Update'), array('class' => 'btn btn-primary btn-sm', 'id' => 'submit_btn')) ?>
                </div>
                <div class="clearfix"></div>             
                <?= $this->Form->end() ?>
            </div>
        </div>
        <div class="col-12 col-md-4 col-xl-3 mt-30 mb-30">
            <div class="info-cards green-card">
                <h5>Checked In</h4>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="text-center">
                                <i class="fas fa-user-friends font-card"></i>
                                <div class="card-text">Staff</div>
                            </div>
                            <div class="ml-2"><h2><?= $count_staff ?></h2></div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="text-center">
                                <i class="fas fa-chalkboard-teacher font-card"></i>
                                <div class="card-text">Teachers</div>
                            </div>
                            <div class="ml-2"><h2><?= $count_teacher ?></h2></div>
                        </div>
                    </div>                    
            </div>
            <div class="info-cards orange-card">
                <h5>Checked In</h4>
                <div class="d-flex align-items-center">
                    <div class="text-center">
                        <i class="fas fa-user-graduate font-card"></i>
                        <div class="card-text">Students</div>
                    </div>
                    <div class="ml-2"><h2><?= $count_students; ?></h2></div>
                </div>
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