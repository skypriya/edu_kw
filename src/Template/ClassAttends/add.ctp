<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ClassAttend $classAttend
 */
?>
<div class="staff-add-page" id="add-global-form">
    <div class="row">
        <div class="col-lg-9 col-xlg-9">            
            <div class="card card-outline-info">
                <div class="card-header">
                    <h4 class="m-b-0 text-white"><i class="fal fa-clipboard-user mr-1"></i>Add Class</h4>
                </div>
                <div class="card-body">
                    <?= $this->Form->create($classAttend) ?>
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="classId">Name </label>
                                        <select name="classId" required="required" id="classId" class="custom-select">
                                            <?php foreach ($classes as $c): ?>
                                            <option value=<?= $c->id ?> <?php if($c->id == $classAttend->classId){ echo "selected"; }?>>
                                                <?= $c->name ?></option>
                                            <?php endforeach; ?>

                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-actions text-right">
                            <?= $this->Form->button(__('<i class="fas fa-save mr-1"></i> Save'), array('class' => 'btn waves-effect waves-light btn-primary', 'id' => 'submit_btn')) ?>
                            <?= $this->Html->link(__('<i class="fas fa-times mr-1"></i> Close'), ['action' => 'index'], ['class' => 'btn waves-effect waves-light btn-danger', 'escape' => false]) ?>
                        </div>                        
                        <div class="clearfix"></div>
                    <?= $this->Form->end() ?>
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