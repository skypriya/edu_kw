<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;

$this->Docs = TableRegistry::get('Docs');
$this->IDCard = TableRegistry::get('IDCard');

$session_usertype = explode(",", $session_user['usertype']);
?>


<div class="staff-add-page" id="add-global-form">
    <div class="row">
        <div class="col-lg-9 col-xlg-9">            
            <div class="card card-outline-info">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="m-b-0 text-white"><i class="fal fa-users-crown mr-1"></i>Add Staff</h4>
                        </div>
                        <div class="col-6 text-right">
                            <button href="javascript:void(0);" class="btn btn-info btn-sm text-right"
                                id="getDataFROM" onclick="getDataFROM()">Get Data From AKcess ID</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!--<form>-->
                    <?= $this->Form->create($user,array('id'=>'add-user')) ?>
                        <div class="form-body">
                            <input type="hidden" id="ut" name="ut" value="Staff"/>
                            <input type="hidden" id="force_add_user" name="force_add_user" value="0" />

                            <div class="row p-t-20">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?php echo $this->Form->input('akcessId', array('label' => 'AKcess ID', 'class' => 'form-control')); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?php echo $this->Form->input('firstname', array('label' => 'First Name', 'class' => 'form-control')); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?php echo $this->Form->input('lastname', array('label' => 'Last Name', 'class' => 'form-control')); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?php echo $this->Form->input('email', array('label' => ['text'=>'Email', 'class'=>'required'], 'class' => 'form-control')); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?php echo $this->Form->input('mobileNumber', array('label' => ['text'=>'Mobile Number', 'class'=>'required'], 'class' => 'form-control')); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="required">Date of Birth</label>
                                        <input type="text" id="student_dob" name="dob" class="form-control" placeholder="Date of Birth" max="<?php echo date("d-m-Y"); ?>" required value="<?php echo $user->dob; ?>" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?php echo $this->Form->input('city', array('label' => ['text'=>'Place of Birth', 'class'=>'required'], 'class' => 'form-control', 'value' => $user->city)); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?php
                                        $type = ['' => 'Select gender', 'Male' => 'Male', 'Female' => 'Female'];
                                        echo $this->Form->input('gender', array('type' => 'select', 'options' => $type, 'label' => ['text'=>'Gender', 'class'=>'required'], 'class' => 'form-control custom-select', 'value' => $user->gender));
                                        ?> 
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nationality</label>
                                        <select name="nationality" id="nationality" class="form-control custom-select" data-placeholder="Select Country" tabindex="1">
                                            <option value="">Select Country</option>
                                            <?php
                                            foreach ($countries as $countrie) {
                                                $selected = '';
                                                if($user->country == $countrie->id) {
                                                    $selected = 'selected';
                                                }
                                                ?>
                                                <option value="<?php echo $countrie->id; ?>" <?php echo $selected; ?>><?php echo $countrie->country_name; ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Address</label>
                                        <textarea name="address" id="address" col="3" class="form-control"><?php echo $user->address; ?></textarea>  
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Staff Type</label>
                                        <select class="custom-select" id="staff_type" name="staff_type" class="form-control custom-select" data-placeholder="Staff Type" tabindex="1">
                                            <option value="">Staff Type</option>
                                            <?php
                                            foreach ($staffList as $key => $staffLists) {
                                                $selected = '';
                                                if($user->staff_type == $staffLists->id) {
                                                    $selected = 'selected';
                                                }
                                                ?>
                                                <option value="<?php echo $staffLists->id; ?>" <?php echo $selected; ?>><?php echo $staffLists->name; ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>                                
                            </div>
                            <div class="row">
                                <label class="col-12 col-md-2 col-form-label">Active</label>
                                <div class="col-10 d-flex align-items-center">
                                    <div>
                                        <?php
                                            $radio_yes = '';
                                            if($user->active == 'yes') {
                                                $radio_yes = 'checked';
                                            }
                                        ?>
                                        <input type="radio" name="active" id="active_yes" value="yes" class="with-gap radio-col-light-blue" <?php echo $radio_yes; ?>/>
                                        <label for="active_yes">Yes</label>
                                    </div>
                                    <div class="ml-2">
                                        <?php
                                            $radio_no = '';
                                            if($user->active == 'no') {
                                                $radio_no = 'checked';
                                            }
                                        ?>
                                        <input type="radio" name="active" id="active_no" value="no" class="with-gap radio-col-light-blue" <?php echo $radio_no; ?>/>
                                        <label for="active_no">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <?php if(in_array('Admin',$session_usertype)) { ?>
                        <div class="form-actions text-right">
                            <?= $this->Form->button(__('<i class="fas fa-save mr-1"></i> Save'), array('class' => 'btn waves-effect waves-light btn-primary user-form-submit-btn', 'id' => 'submit_btn')) ?>
                            <?= $this->Html->link(__('<i class="fas fa-times mr-1"></i> Close'), ['action' => 'staffList'], ['class' => 'btn waves-effect waves-light btn-danger', 'escape' => false]) ?>
                        </div>
                        <?php } ?>
                        <div class="clearfix"></div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
        <!-- <div class="col-lg-3">
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
        </div> -->
    </div>
</div>


<!-- Send Modal -->
<div class="modal " id="getDataFROMModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="getDataFROMData" method="POST" class="mb-0">
                <div class="modal-header">                
                    <h4 class="modal-title" id="myModalLabel">Get Data From AKcess ID</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div>
                        <div class="form-group mb-0">      
                            <input type="text" id="from_akcess_id" name="faid" placeholder="Enter AKcess ID" class="form-control">
                        </div>
                    </div>
                    <div class="message error hidden modalpopup_message" onclick="this.classList.add('hidden')"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn waves-effect waves-light btn-primary" onclick="getDataFROMData('staff')">Request Data</button>
                    <button type="button" class="btn waves-effect waves-light btn-danger" data-dismiss="modal">Close</button>
                </div>
            </form> 
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
