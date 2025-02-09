<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\IDCard $idcard
 */
use Cake\ORM\TableRegistry;

$this->Users = TableRegistry::get('Users');

$emp = $this->Users->find()->where(['id' => $idcard->fk_users_id])->first();
$name = explode(" ", $emp->name);
$firstname = isset($name[0]) ? $name[0] : '';
$lastname = isset($name[1]) ? $name[1] : '';
$photo = isset($emp->photo) ? $emp->photo : 'user.png';
$faculty = isset($emp->faculty) ? $emp->faculty : '';
$dob = isset($emp->dob) ? $emp->dob : '';
$idcardno = isset($emp->idcardno) ? $emp->idcardno : '';

$name = $emp->name;
$id = $emp->id;
$flname = $id;
$fname = $id . "/";
?>
<div class="view-idcard-page">  
    <div class="row">
        <div class="col-lg-8 col-xlg-8">
            <div class="card card-outline-info">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-6">
                        <h4 class="m-b-0 text-white d-flex align-items-center"><i class="fal fa-id-card mr-1"></i><?= $page_title ?></h4>
                        </div>
                        <div class="col-6 text-right">
                            <?php echo $this->Html->link(__('<i class="fa fa-arrow-circle-left"></i>'), ['action' => 'index'], ['class' => 'waves-effect waves-light btn-info', 'escape' => false]) ?>
                        </div>
                    </div>  
                </div> 
                <div class="card-body">                
                    <?= $this->Form->create($idcard, array('enctype' => 'multipart/form-data')) ?>
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-400">First Name</label>
                                        <input type="text" name="firstname" disabled="disabled" class="form-control" id="firstname" value="<?php echo $firstname; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-400">Last Name</label>
                                        <input type="text" name="lastName" disabled="disabled" class="form-control" id="lastName" value="<?php echo $lastname; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-400">ID No.</label>
                                        <input type="text" name="idNo" disabled="disabled" class="form-control" id="idNo" value="<?php echo $idcardno; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-400">Date of Birth</label>
                                        <input type="text" id="expiry_date" name="idCardExpiyDate" class="form-control" placeholder="Date of Birth" value="<?php echo date("d/m/Y", strtotime($dob)); ?>"  disabled />
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card-box">
                <div class="card-uni-img">
                    <?php
                $image_src = $this->Url->build('/uploads/attachs/' . $fname . $idcard->image_fileName);
                ?>
                    <img src="<?php echo $image_src; ?>" class="user-img" />
                </div>
                <div class="user-details">
                    <p class="name"><?php echo h($firstname); ?> <?php echo h($lastname); ?></p>
                    <div class="other-details d-flex align-items-center">
                        <div class="title id-number"><b>ID Number :</b></div>
                        <div class="info id-number"><b><?php echo h($idcard->idNo); ?></b></div>
                    </div>
                    <div class="other-details d-flex align-items-center">
                        <div class="title">DOB :</div>
                        <div class="info"><?php echo h(date("F d, Y", strtotime($dob))); ?></div>
                    </div>
                    <div class="other-details d-flex align-items-center">
                        <div class="title">VALID THRU :</div>
                        <div class="info"><?php echo h(date("F d, Y", strtotime($idcard->idCardExpiyDate))); ?>
                        </div>
                    </div>
                    <?php if(isset($faculty) && $faculty != "") { ?>
                    <div class="other-details d-flex align-items-center">
                        <div class="title">FACULTY :</div>
                        <div class="info"><?php echo h($faculty); ?></div>
                    </div>
                    <?php } ?>
                </div>
                <div>
                    <img src="<?php echo $this->Url->build('/img/University-Logo.jpg'); ?>"
                        class="user-university" />
                </div>
                <div class="border-red-div">
                    <div class="border-red"></div>
                </div>
                <div class="border-red-div-2">
                    <div class="border-red-2"></div>
                </div>
            </div>
        </div>
    </div>
</div>