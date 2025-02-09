<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */

use Cake\Routing\Router;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

$conn = ConnectionManager::get("default"); // name of your database connection  

$name = isset($user->name) ? $user->name : '';
$id = isset($user->id) ? $user->id : 0;
$flname = $id;
$fname = $id . "/";

$soft_delete = $user->soft_delete;

$user_id = ConnectionManager::userIdEncode($user->id); 

$usertype = explode(",",trim($user->usertype));

?>
<div class="edit-staff-page">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline-info">
                <div class="card-header">
                    <h4 class="m-b-0 text-white d-flex align-items-center"><i class="fal fa-users-crown mr-1"></i>Edit <?php echo $label; ?> ( <?php echo $user->name; ?> ) </h4>
                </div>
                <div class="card-body">
                    <?= $this->Form->create($user) ?>
                    <div class="form-body">
                        <div class="row p-t-20">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="required">Roles </label>
                                    <select name="usertype[]" required="required" class="form-control select2" multiple="multiple" id="usertype" size="5" style="height:auto;">
                                        <?php 
                                       
                                        foreach ($rolesDetails as $key => $value){ 
                                            $selected = "";
                                            if(in_array($value['name'], $usertype)) {
                                                $selected = "selected";
                                            }
                                               
                                        ?>
                                       
                                            <option <?php echo $selected; ?> value="<?php echo $value['name']; ?>">
                                                <?php echo $value['name']; ?>
                                            </option>
                                        <?php      
                                        } 
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('AKcess ID') ?></label>
                                    <div><?= h($user->akcessId) ?></div>
                                </div>
                            </div>
                        </div>
                        <?php $name = explode(" ", $user->name); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('First Name') ?></label>
                                    <div><?= h($name[0]) ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Last Name') ?></label>
                                    <div><?= h($name[1]) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Email') ?></label>
                                    <div><?= h($user->email) ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('MobileNumber') ?></label>
                                    <div><?= h($user->mobileNumber) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions text-right">

                        <?= $this->Form->button(__('<i class="fas fa-save mr-1" aria-hidden="true"></i> Update'), array('class' => 'btn waves-effect waves-light btn-primary', 'id' => 'submit_btn', 'type' => 'hidden')) ?>
                        
                        <?= $this->Html->link(__('<i class="fas fa-times mr-1"></i> Close'), ['action' => 'index'], ['escape' => false, 'class' => 'btn waves-effect waves-light btn-danger']) ?>

                    </div>
                    <div class="clearfix"></div>
                    <?= $this->Form->end() ?>
                   
                </div>
            </div>
        </div> 
  <!--  <div class="col-lg-3">
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
        </div>  -->
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
  <?= $this->Html->script('../new-assets/plugins/jquery/jquery.min.js') ?>
<?= $this->Html->script('../new-assets/plugins/select2/dist/js/select2.js') ?>

<script>
$('.select2').select2();
</script>
