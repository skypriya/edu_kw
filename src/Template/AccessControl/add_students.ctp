<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Sclass $sclass
 */

?>
<div class="staff-add-page" id="add-global-form">
    <div class="row">
        <div class="col-lg-12 col-xlg-12">
            <div class="card card-outline-info">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="m-b-0 text-white"><i class="fal fa-user-graduate mr-1"></i>Add User</h4>
                        <?= $this->Html->link(__('<i class="fas fa-arrow-circle-left"></i> '), ['action' => 'manage-students', $id],  ['class' => 'btn waves-effect waves-light btn-info','escape' => false, 'data-toggle' => "tooltip", 'title'=>'Back']) ?>
                    </div>
                </div>
                <div class="card-body">
                    <?= $this->Form->create(null, ['id' => 'add_stu']) ?>
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group container mt-5">
                                      <!--  <label class="required">Users</label> -->
                                        <select name="users[]" required="required" class="js-example-placeholder-multiple js-states form-control" multiple="multiple" id="manage_users_multiple_points" size="5" style="height:auto;">
                                            <?php 
                                                foreach ($users as $c){ 
                                                
                                                if(!in_array($c->id , $all)) {
                                                    if(!empty($c->akcessId)) {
                                            ?>
                                                <option value = <?= $c->id ?>>
                                                    <?php echo $c->name . " ( " . $c->akcessId . " )[".$c->usertype."]"; ?>
                                                </option>
                                            <?php }
                                                }
                                            } ?>

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
 <!--   <div class="col-lg-3">
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
        </div>    -->
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