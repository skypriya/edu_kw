<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\IDCard $idcard
 */
use Cake\ORM\TableRegistry; ?>

<div class="edit-eform-page">    
    <div class="row">
        <div class="col-12 text-right">
        <?= $this->Html->link(
            __(
                '<span><i class="fas fa-times"></i> Close</span>'
            ),
            ['action' => 'index'],
            ['escape' => false]
        ) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0"><i class="fab fa-wpforms mr-2"></i><?= $page_title ?></h4>
                </div>
                <hr/>
                <div class="card-body">
                    <?= $this->Form->create($id, [
                        'enctype' => 'multipart/form-data',
                        'id' => 'eformEdit',
                    ]) ?>
                    <input type="hidden" id="eid" value="<?php echo $eid; ?>">
                    <div class="tab-container">
                        <div class="nav-tabs-navigation">
                            <div class="nav-tabs-wrapper">
                                <ul class="nav nav-tabs nav-fill pr-0" data-tabs="tabs">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#eform-1" data-toggle="tab">Settings</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#eform-2" data-toggle="tab">Fields</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#eform-3" data-toggle="tab">Notifications</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#eform-4" data-toggle="tab">Preview</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane active" id="eform-1">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-12 col-lg-6">
                                                <div class="form-group">   
                                                    <?php echo $this->Form->input(
                                                        'formName',
                                                        [
                                                            'label' =>
                                                                'eForm Name',
                                                            'class' =>
                                                                'form-control',
                                                            'value' =>
                                                                $eform->formName,
                                                        ]
                                                    ); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-lg-6">
                                                <div class="form-group"> 
                                                    <?php echo $this->Form->input(
                                                        'description',
                                                        [
                                                            'label' =>
                                                                'eForm Description',
                                                            'class' =>
                                                                'form-control',
                                                            'value' =>
                                                                $eform->description,
                                                        ]
                                                    ); ?>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-6">
                                                <div class="form-group">                                               
                                                    <?php echo $this->Form->input(
                                                        'instruction',
                                                        [
                                                            'label' =>
                                                                'eForm Instruction',
                                                            'class' =>
                                                                'form-control',
                                                            'value' =>
                                                                $eform->instruction,
                                                        ]
                                                    ); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <!--<div class="row">                                
                                            <div class="col col-lg-4">
                                                <div class="form-group">   
                                                    <label>Publish this eForm?</label>
                                                </div>
                                            </div>
                                            <div class="col col-lg-8">
                                                <?php
                                                $publish_yes = "";
                                                $publish_no = "";
                                                if ($eform->publish == "yes") {
                                                    $publish_yes = 'checked';
                                                } elseif (
                                                    $eform->publish == "no"
                                                ) {
                                                    $publish_no = 'checked';
                                                }
                                                ?>
                                                <div class="form-group"> 
                                                    <input type="radio" name="publish" value="yes" <?php echo $publish_yes; ?> /> Yes
                                                    <input type="radio" name="publish" value="no" <?php echo $publish_no; ?>/> No
                                                </div>
                                            </div>
                                        </div>-->
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label pl-0">Publish this eForm?</label>
                                            <div class="col-md-8 d-flex align-items-center">
                                                <?php
                                                $publish_yes = "";
                                                $publish_no = "";
                                                if ($eform->publish == "yes") {
                                                    $publish_yes = 'checked';
                                                } elseif (
                                                    $eform->publish == "no"
                                                ) {
                                                    $publish_no = 'checked';
                                                }
                                                ?>
                                                <div class="form-check form-check-radio">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="radio" name="publish" id="publish_yes" value="yes" <?php echo $publish_yes; ?> />
                                                        Yes
                                                        <span class="circle">
                                                            <span class="check"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-radio">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="radio" name="publish" id="publish_no" value="no" <?php echo $publish_no; ?> />
                                                        No
                                                        <span class="circle">
                                                            <span class="check"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                         </div>
                                         <div class="form-group row">
                                            <label class="col-md-4 col-form-label pl-0">Signature for eForm?</label>
                                            <div class="col-md-8 d-flex align-items-center">
                                                <?php
                                                $signature_yes = "";
                                                $signature_no = "";
                                                if (
                                                    $eform->signature == "yes"
                                                ) {
                                                    $signature_yes = 'checked';
                                                } elseif (
                                                    $eform->signature == "no"
                                                ) {
                                                    $signature_no = 'checked';
                                                }
                                                ?>
                                                <div class="form-check form-check-radio">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="radio" name="signature" id="signature_yes" value="yes" <?php echo $signature_yes; ?> />
                                                        Yes
                                                        <span class="circle">
                                                            <span class="check"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-radio">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="radio" name="signature" id="signature_no" value="no" <?php echo $signature_no; ?> />
                                                        No
                                                        <span class="circle">
                                                            <span class="check"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                         </div>
                                         <div class="form-group row">
                                            <label class="col-md-4 col-form-label pl-0">Is face match required?</label>
                                            <div class="col-md-8 d-flex align-items-center">
                                                <?php
                                                $facematch_yes = "";
                                                $facematch_no = "";
                                                if (
                                                    $eform->facematch == "yes"
                                                ) {
                                                    $facematch_yes = 'checked';
                                                } elseif (
                                                    $eform->facematch == "no"
                                                ) {
                                                    $facematch_no = 'checked';
                                                }
                                                ?>
                                                <div class="form-check form-check-radio">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="radio" name="facematch" id="facematch_yes" value="yes" <?php echo $facematch_yes; ?> />
                                                        Yes
                                                        <span class="circle">
                                                            <span class="check"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-radio">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="radio" name="facematch" id="facematch_no" value="no" <?php echo $facematch_no; ?> />
                                                        No
                                                        <span class="circle">
                                                            <span class="check"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                         </div>
                                         <div class="form-group row">
                                            <label class="col-md-4 col-form-label pl-0">Pull data from document?</label>
                                            <div class="col-md-8 d-flex align-items-center">
                                                <?php
                                                $pulldata_yes = "";
                                                $pulldata_no = "";
                                                if ($eform->pulldata == "yes") {
                                                    $pulldata_yes = 'checked';
                                                } elseif (
                                                    $eform->pulldata == "no"
                                                ) {
                                                    $pulldata_no = 'checked';
                                                }
                                                ?>
                                                <div class="form-check form-check-radio">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="radio" name="pulldata" id="pulldata_yes" value="yes" <?php echo $pulldata_yes; ?> />
                                                        Yes
                                                        <span class="circle">
                                                            <span class="check"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-radio">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="radio" name="pulldata" id="pulldata_no" value="no" <?php echo $pulldata_no; ?> />
                                                        No
                                                        <span class="circle">
                                                            <span class="check"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                         </div>   
                                         <div class="form-group row">
                                            <label class="col-md-4 col-form-label pl-0">Store in profile?</label>
                                            <div class="col-md-8 d-flex align-items-center">
                                                <?php
                                                $storeinprofile_yes = "";
                                                $storeinprofile_no = "";
                                                if (
                                                    $eform->storeinprofile ==
                                                    "yes"
                                                ) {
                                                    $storeinprofile_yes =
                                                        'checked';
                                                } elseif (
                                                    $eform->storeinprofile ==
                                                    "no"
                                                ) {
                                                    $storeinprofile_no =
                                                        'checked';
                                                }
                                                ?>
                                                <div class="form-check form-check-radio">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="radio" name="storeinprofile" id="storeinprofile_yes" value="yes" <?php echo $storeinprofile_yes; ?> />
                                                        Yes
                                                        <span class="circle">
                                                            <span class="check"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-radio">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="radio" name="storeinprofile" id="storeinprofile_no" value="no" <?php echo $storeinprofile_no; ?> />
                                                        No
                                                        <span class="circle">
                                                            <span class="check"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                         </div>
                                         <div class="form-group row">
                                            <label class="col-md-4 col-form-label pl-0">Is Default Client invitation Eform?</label>
                                            <div class="col-md-8 d-flex align-items-center">
                                                <?php
                                                $isclientInvitationEform_yes =
                                                    "";
                                                $isclientInvitationEform_no =
                                                    "";
                                                if (
                                                    $eform->isclientInvitationEform ==
                                                    "yes"
                                                ) {
                                                    $isclientInvitationEform_yes =
                                                        'checked';
                                                } elseif (
                                                    $eform->isclientInvitationEform ==
                                                    "no"
                                                ) {
                                                    $isclientInvitationEform_no =
                                                        'checked';
                                                }
                                                ?>
                                                <div class="form-check form-check-radio">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="radio" name="isclientInvitationEform" id="isclientInvitationEform_yes" value="yes" <?php echo $isclientInvitationEform_yes; ?> />
                                                        Yes
                                                        <span class="circle">
                                                            <span class="check"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-radio">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="radio" name="isclientInvitationEform" id="isclientInvitationEform_no" value="no" <?php echo $isclientInvitationEform_yes; ?> />
                                                        No
                                                        <span class="circle">
                                                            <span class="check"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                         </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane eform-fields" id="eform-2">
                                <div class="row">
                                    <!-- Select Fields Mobile-->
                                    <div class="col-12 d-lg-none">
                                    </div>
                                    <!-- eform Body-->
                                    <div class="col-12 col-lg-8">
                                        <div class="card" id="notification_display">        
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <div class="input-group" id="addfield">                                                        
                                                            <?php foreach (
                                                                $fields
                                                                as $n
                                                            ) {
                                                                $field_check =
                                                                    '0';
                                                                $field_ids =
                                                                    $n->id;
                                                                $field_name =
                                                                    $n->key;
                                                                $field_label =
                                                                    $n->labelname;
                                                                $field_type =
                                                                    $n->keytype;
                                                                $field_isVisible =
                                                                    $n->isVisible;
                                                                $field_section =
                                                                    $n->section;
                                                                $field_verification_grade =
                                                                    $n->verification_grade;
                                                                $field_verified =
                                                                    $n->file_verified;
                                                                $field_mandate =
                                                                    $n->is_mandatory;
                                                                $field_items =
                                                                    $n->options;
                                                                $field_label_instructions =
                                                                    $n->instructions;
                                                                if (
                                                                    $field_type ==
                                                                    'file'
                                                                ) { ?>
                                                                    <div class="col-12 col-lg-6 <?php echo $field_ids; ?>">
                                                                        <div class="row">
                                                                            <div class="form-group col-12 col-lg-12">
                                                                                <label><?php echo $field_label; ?></label>
                                                                                <a href="javascript:void(0);" class="btn-delete" onclick="removeField('<?php echo $field_ids; ?>')"><i class="fa fa-trash"></i></a>
                                                                                <input type="file" data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" class="form-control custom-file-input custom-file-label">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][instructions]" value="<?php echo $field_label_instructions; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][type]" value="<?php echo $field_type; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][isVisible]" value="<?php echo $field_isVisible; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][section]" value="<?php echo $field_section; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][verification_grade]" value="<?php echo $field_verification_grade; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][fieldver]" value="<?php echo $field_verified; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][field_mandate]" value="<?php echo $field_mandate; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][ids]" value="<?php echo $field_ids; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][items]" value="<?php echo $field_items; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][key]" value="<?php echo $field_name; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][name]" value="<?php echo $field_label; ?>">
                                                                            </div>
                                                                            <div class="form-group col-12 pt-4">
                                                                                <?php if (
                                                                                    $field_mandate ==
                                                                                    'yes'
                                                                                ) { ?>
                                                                                    <label class="d-block">Signature required : <?php echo $field_mandate; ?></label>
                                                                                <?php } ?>
                                                                                <?php if (
                                                                                    $field_verified ==
                                                                                    'yes'
                                                                                ) { ?>
                                                                                    <label class="d-block">File needs to be verified : <?php echo $field_verified; ?></label>
                                                                                    <label class="d-block">  
                                                                                        <?php
                                                                                        $field_verification_grade_text =
                                                                                            '';
                                                                                        if (
                                                                                            $field_verification_grade ==
                                                                                            'G'
                                                                                        ) {
                                                                                            $field_verification_grade_text =
                                                                                                'Verification grade : Government';
                                                                                        } elseif (
                                                                                            $field_verification_grade ==
                                                                                            'F'
                                                                                        ) {
                                                                                            $field_verification_grade_text =
                                                                                                'Verification grade : Financial';
                                                                                        } elseif (
                                                                                            $field_verification_grade ==
                                                                                            'T'
                                                                                        ) {
                                                                                            $field_verification_grade_text =
                                                                                                'Verification grade : Telecom';
                                                                                        } elseif (
                                                                                            $field_verification_grade ==
                                                                                            'A'
                                                                                        ) {
                                                                                            $field_verification_grade_text =
                                                                                                'Verification grade : Akcess';
                                                                                        } elseif (
                                                                                            $field_verification_grade ==
                                                                                            'O'
                                                                                        ) {
                                                                                            $field_verification_grade_text =
                                                                                                'Verification grade : Other';
                                                                                        }
                                                                                        ?>
                                                                                    </label>
                                                                                    <label class="d-block"><?php echo $field_verification_grade_text; ?></label>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <?php $field_check =
                                                                        '1'; ?>
                                                                <?php }
                                                                if (
                                                                    $field_type ==
                                                                        'string' ||
                                                                    $field_type ==
                                                                        'text'
                                                                ) { ?>
                                                                    <div class="col-12 col-lg-6 <?php echo $field_ids; ?>">
                                                                        <div class="row">
                                                                            <div class="form-group col-12 col-lg-12">
                                                                            <label><?php echo $field_label; ?></label>
                                                                            <a href="javascript:void(0);" class="btn-delete" onclick="removeField('<?php echo $field_ids; ?>')"><i class="fa fa-trash"></i></a>
                                                                            <input type="text" data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_label; ?>" class="form-control">
                                                                            <input type="hidden" name="field_name[<?php echo $field_name; ?>][instructions]" value="<?php echo $field_label_instructions; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][type]" value="<?php echo $field_type; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][isVisible]" value="<?php echo $field_isVisible; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][section]" value="<?php echo $field_section; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][verification_grade]" value="<?php echo $field_verification_grade; ?>">                                                                                                                        <input type="hidden" name="field_name[<?php echo $field_name; ?>][fieldver]" value="<?php echo $field_verified; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][field_mandate]" value="<?php echo $field_mandate; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][ids]" value="<?php echo $field_ids; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][items]" value="<?php echo $field_items; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][key]" value="<?php echo $field_name; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][name]" value="<?php echo $field_label; ?>">
                                                                            </div>
                                                                            <div class="form-group col-12 col-lg-12"> 
                                                                                <?php if (
                                                                                    $field_verified ==
                                                                                    'yes'
                                                                                ) { ?>
                                                                                    <label class="d-block">File needs to be verified: <?php echo $field_verified; ?></label>
                                                                                    <label class="d-block">
                                                                                        <?php
                                                                                        $field_verification_grade_text =
                                                                                            '';
                                                                                        if (
                                                                                            $field_verification_grade ==
                                                                                            'G'
                                                                                        ) {
                                                                                            $field_verification_grade_text =
                                                                                                'Verification grade: Government';
                                                                                        } elseif (
                                                                                            $field_verification_grade ==
                                                                                            'F'
                                                                                        ) {
                                                                                            $field_verification_grade_text =
                                                                                                'Verification grade: Financial';
                                                                                        } elseif (
                                                                                            $field_verification_grade ==
                                                                                            'T'
                                                                                        ) {
                                                                                            $field_verification_grade_text =
                                                                                                'Verification grade: Telecom';
                                                                                        } elseif (
                                                                                            $field_verification_grade ==
                                                                                            'A'
                                                                                        ) {
                                                                                            $field_verification_grade_text =
                                                                                                'Verification grade: Akcess';
                                                                                        } elseif (
                                                                                            $field_verification_grade ==
                                                                                            'O'
                                                                                        ) {
                                                                                            $field_verification_grade_text =
                                                                                                'Verification grade: Other';
                                                                                        }
                                                                                        ?>
                                                                                    </label>
                                                                                    <label class="d-block"><?php echo $field_verification_grade_text; ?></label>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <?php $field_check =
                                                                        '1'; ?>
                                                                <?php }
                                                                if (
                                                                    $field_type ==
                                                                        'address' ||
                                                                    $field_type ==
                                                                        'textarea'
                                                                ) { ?>
                                                                    <div class="col-12 col-lg-6 <?php echo $field_ids; ?>">
                                                                        <div class="row">
                                                                            <div class="form-group col-12 col-lg-12">
                                                                            <label><?php echo $field_label; ?></label>
                                                                            <a href="javascript:void(0);" class="btn-delete" onclick="removeField('<?php echo $field_ids; ?>')"><i class="fa fa-trash"></i></a>
                                                                                <textarea data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_name; ?>" col="3" class="form-control"></textarea>  
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][instructions]" value="<?php echo $field_label_instructions; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][type]" value="<?php echo $field_type; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][isVisible]" value="<?php echo $field_isVisible; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][section]" value="<?php echo $field_section; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][verification_grade]" value="<?php echo $field_verification_grade; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][fieldver]" value="<?php echo $field_verified; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][field_mandate]" value="<?php echo $field_mandate; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][ids]" value="<?php echo $field_ids; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][items]" value="<?php echo $field_items; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][key]" value="<?php echo $field_name; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][name]" value="<?php echo $field_label; ?>">
                                                                            </div>
                                                                            <div class="form-group col-12 col-lg-12">
                                                                                <?php if (
                                                                                    $field_verified ==
                                                                                    'yes'
                                                                                ) { ?>
                                                                                    <label class="d-block">File needs to be verified : <?php echo $field_verified; ?></label>
                                                                                    <label class="d-block">
                                                                                    <?php
                                                                                    $field_verification_grade_text =
                                                                                        '';
                                                                                    if (
                                                                                        $field_verification_grade ==
                                                                                        'G'
                                                                                    ) {
                                                                                        $field_verification_grade_text =
                                                                                            'Verification grade: Government';
                                                                                    } elseif (
                                                                                        $field_verification_grade ==
                                                                                        'F'
                                                                                    ) {
                                                                                        $field_verification_grade_text =
                                                                                            'Verification grade: Financial';
                                                                                    } elseif (
                                                                                        $field_verification_grade ==
                                                                                        'T'
                                                                                    ) {
                                                                                        $field_verification_grade_text =
                                                                                            'Verification grade: Telecom';
                                                                                    } elseif (
                                                                                        $field_verification_grade ==
                                                                                        'A'
                                                                                    ) {
                                                                                        $field_verification_grade_text =
                                                                                            'Verification grade: Akcess';
                                                                                    } elseif (
                                                                                        $field_verification_grade ==
                                                                                        'O'
                                                                                    ) {
                                                                                        $field_verification_grade_text =
                                                                                            'Verification grade: Other';
                                                                                    }
                                                                                    ?></label>
                                                                                    <label class="d-block"><?php echo $field_verification_grade_text; ?></label>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <?php $field_check =
                                                                        '1'; ?>
                                                                    <?php }
                                                                if (
                                                                    $field_type ==
                                                                    'list'
                                                                ) { ?>
                                                                    <div class="col-12 col-lg-6 <?php echo $field_ids; ?>">
                                                                        <div class="row">
                                                                            <div class="form-group col-12 col-lg-12">
                                                                                <label><?php echo $field_label; ?></label>
                                                                                <a href="javascript:void(0);" class="btn-delete" onclick="removeField('<?php echo $field_ids; ?>')"><i class="fa fa-trash"></i></a>
                                                                                    <select data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_label; ?>" class="form-control">
                                                                                    <?php $explode = explode(
                                                                                        ",",
                                                                                        $field_items
                                                                                    ); ?>
                                                                                    <option value="">Select <?php echo $field_label; ?></option>
                                                                                    <?php foreach (
                                                                                        $explode
                                                                                        as $key =>
                                                                                            $value
                                                                                    ) { ?>
                                                                                        <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                                                                                    <?php } ?>
                                                                                    </select>  
                                                                                    <input type="hidden" name="field_name[<?php echo $field_name; ?>][instructions]" value="<?php echo $field_label_instructions; ?>">
                                                                                    <input type="hidden" name="field_name[<?php echo $field_name; ?>][type]" value="<?php echo $field_type; ?>">
                                                                                    <input type="hidden" name="field_name[<?php echo $field_name; ?>][isVisible]" value="<?php echo $field_isVisible; ?>">
                                                                                    <input type="hidden" name="field_name[<?php echo $field_name; ?>][section]" value="<?php echo $field_section; ?>">
                                                                                    <input type="hidden" name="field_name[<?php echo $field_name; ?>][verification_grade]" value="<?php echo $field_verification_grade; ?>">
                                                                                    <input type="hidden" name="field_name[<?php echo $field_name; ?>][fieldver]" value="<?php echo $field_verified; ?>">
                                                                                    <input type="hidden" name="field_name[<?php echo $field_name; ?>][field_mandate]" value="<?php echo $field_mandate; ?>">
                                                                                    <input type="hidden" name="field_name[<?php echo $field_name; ?>][ids]" value="<?php echo $field_ids; ?>">
                                                                                    <input type="hidden" name="field_name[<?php echo $field_name; ?>][items]" value="<?php echo $field_items; ?>">
                                                                                    <input type="hidden" name="field_name[<?php echo $field_name; ?>][key]" value="<?php echo $field_name; ?>">
                                                                                    <input type="hidden" name="field_name[<?php echo $field_name; ?>][name]" value="<?php echo $field_label; ?>">
                                                                            </div>
                                                                            <div class="form-group col-12 col-lg-12"> 
                                                                                <?php if (
                                                                                    $field_verified ==
                                                                                    'yes'
                                                                                ) { ?>
                                                                                    <label class="d-block">File needs to be verified: <?php echo $field_verified; ?></label>
                                                                                    <label class="d-block">
                                                                                    <?php
                                                                                    $field_verification_grade_text =
                                                                                        '';
                                                                                    if (
                                                                                        $field_verification_grade ==
                                                                                        'G'
                                                                                    ) {
                                                                                        $field_verification_grade_text =
                                                                                            'Verification grade: Government';
                                                                                    } elseif (
                                                                                        $field_verification_grade ==
                                                                                        'F'
                                                                                    ) {
                                                                                        $field_verification_grade_text =
                                                                                            'Verification grade: Financial';
                                                                                    } elseif (
                                                                                        $field_verification_grade ==
                                                                                        'T'
                                                                                    ) {
                                                                                        $field_verification_grade_text =
                                                                                            'Verification grade: Telecom';
                                                                                    } elseif (
                                                                                        $field_verification_grade ==
                                                                                        'A'
                                                                                    ) {
                                                                                        $field_verification_grade_text =
                                                                                            'Verification grade: Akcess';
                                                                                    } elseif (
                                                                                        $field_verification_grade ==
                                                                                        'O'
                                                                                    ) {
                                                                                        $field_verification_grade_text =
                                                                                            'Verification grade: Other';
                                                                                    }
                                                                                    ?>
                                                                                    </label>
                                                                                    <label class="d-block"><?php echo $field_verification_grade_text; ?></label>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                   <?php $field_check =
                                                                       '1';}
                                                                if (
                                                                    $field_type ==
                                                                    'phone'
                                                                ) { ?>
                                                                    <div class="col-12 col-lg-6 <?php echo $field_ids; ?>">
                                                                        <div class="row">
                                                                            <div class="form-group col-12 col-lg-12">
                                                                                <label><?php echo $field_label; ?></label>
                                                                                <a href="javascript:void(0);" class="btn-delete" onclick="removeField('<?php echo $field_ids; ?>')"><i class="fa fa-trash"></i></a>
                                                                                <input type="tel" data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_label; ?>" class="form-control">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][instructions]" value="<?php echo $field_label_instructions; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][type]" value="<?php echo $field_type; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][isVisible]" value="<?php echo $field_isVisible; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][section]" value="<?php echo $field_section; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][verification_grade]" value="<?php echo $field_verification_grade; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][fieldver]" value="<?php echo $field_verified; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][field_mandate]" value="<?php echo $field_mandate; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][ids]" value="<?php echo $field_ids; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][items]" value="<?php echo $field_items; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][key]" value="<?php echo $field_name; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][name]" value="<?php echo $field_label; ?>">
                                                                            </div>
                                                                            <div class="form-group col-12 col-lg-12">
                                                                                <?php if (
                                                                                    $field_verified ==
                                                                                    'yes'
                                                                                ) { ?>
                                                                                    <label class="d-block">File needs to be verified: <?php echo $field_verified; ?></label>
                                                                                    <label class="d-block">
                                                                                        <?php
                                                                                        $field_verification_grade_text =
                                                                                            '';
                                                                                        if (
                                                                                            $field_verification_grade ==
                                                                                            'G'
                                                                                        ) {
                                                                                            $field_verification_grade_text =
                                                                                                'Verification grade: Government';
                                                                                        } elseif (
                                                                                            $field_verification_grade ==
                                                                                            'F'
                                                                                        ) {
                                                                                            $field_verification_grade_text =
                                                                                                'Verification grade: Financial';
                                                                                        } elseif (
                                                                                            $field_verification_grade ==
                                                                                            'T'
                                                                                        ) {
                                                                                            $field_verification_grade_text =
                                                                                                'Verification grade: Telecom';
                                                                                        } elseif (
                                                                                            $field_verification_grade ==
                                                                                            'A'
                                                                                        ) {
                                                                                            $field_verification_grade_text =
                                                                                                'Verification grade: Akcess';
                                                                                        } elseif (
                                                                                            $field_verification_grade ==
                                                                                            'O'
                                                                                        ) {
                                                                                            $field_verification_grade_text =
                                                                                                'Verification grade: Other';
                                                                                        }
                                                                                        ?>
                                                                                    </label>
                                                                                    <label class="d-block"><?php echo $field_verification_grade_text; ?></label>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <?php $field_check =
                                                                        '1';}
                                                                if (
                                                                    $field_type ==
                                                                    'number'
                                                                ) { ?>
                                                                    <div class="col-12 col-lg-6 <?php echo $field_ids; ?>">
                                                                        <div class="row">
                                                                            <div class="form-group col-12 col-lg-12">
                                                                                <label><?php echo $field_label; ?></label>
                                                                                <a href="javascript:void(0);" class="btn-delete" onclick="removeField('<?php echo $field_ids; ?>')"><i class="fa fa-trash"></i></a>
                                                                                <input type="number" data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_label; ?>" class="form-control">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][instructions]" value="<?php echo $field_label_instructions; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][type]" value="<?php echo $field_type; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][isVisible]" value="<?php echo $field_isVisible; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][section]" value="<?php echo $field_section; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][verification_grade]" value="<?php echo $field_verification_grade; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][fieldver]" value="<?php echo $field_verified; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][field_mandate]" value="<?php echo $field_mandate; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][ids]" value="<?php echo $field_ids; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][items]" value="<?php echo $field_items; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][key]" value="<?php echo $field_name; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][name]" value="<?php echo $field_label; ?>">
                                                                            </div>
                                                                            <div class="form-group col-12 col-lg-12"> 
                                                                                <?php if (
                                                                                    $field_verified ==
                                                                                    'yes'
                                                                                ) { ?>
                                                                                    <label>File needs to be verified: <?php echo $field_verified; ?></label>

                                                                                    <?php
                                                                                    $field_verification_grade_text =
                                                                                        '';
                                                                                    if (
                                                                                        $field_verification_grade ==
                                                                                        'G'
                                                                                    ) {
                                                                                        $field_verification_grade_text =
                                                                                            'Verification grade: Government';
                                                                                    } elseif (
                                                                                        $field_verification_grade ==
                                                                                        'F'
                                                                                    ) {
                                                                                        $field_verification_grade_text =
                                                                                            'Verification grade: Financial';
                                                                                    } elseif (
                                                                                        $field_verification_grade ==
                                                                                        'T'
                                                                                    ) {
                                                                                        $field_verification_grade_text =
                                                                                            'Verification grade: Telecom';
                                                                                    } elseif (
                                                                                        $field_verification_grade ==
                                                                                        'A'
                                                                                    ) {
                                                                                        $field_verification_grade_text =
                                                                                            'Verification grade: Akcess';
                                                                                    } elseif (
                                                                                        $field_verification_grade ==
                                                                                        'O'
                                                                                    ) {
                                                                                        $field_verification_grade_text =
                                                                                            'Verification grade: Other';
                                                                                    }
                                                                                    ?>
                                                                                    <label class="d-block"><?php echo $field_verification_grade_text; ?></label>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <?php $field_check =
                                                                        '1';}
                                                                if (
                                                                    $field_type ==
                                                                    'date'
                                                                ) { ?>
                                                                    <div class="col-12 col-lg-6 <?php echo $field_ids; ?>">
                                                                        <div class="row">
                                                                            <div class="form-group col-12 col-lg-12">
                                                                                <label><?php echo $field_label; ?></label>
                                                                                <a href="javascript:void(0);" class="btn-delete" onclick="removeField('<?php echo $field_ids; ?>')"><i class="fa fa-trash"></i></a>        
                                                                                <input type="text" data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_label; ?> ( YYYY-MM-DD )" class="form-control date_field">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][instructions]" value="<?php echo $field_label_instructions; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][type]" value="<?php echo $field_type; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][isVisible]" value="<?php echo $field_isVisible; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][section]" value="<?php echo $field_section; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][verification_grade]" value="<?php echo $field_verification_grade; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][fieldver]" value="<?php echo $field_verified; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][field_mandate]" value="<?php echo $field_mandate; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][ids]" value="<?php echo $field_ids; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][items]" value="<?php echo $field_items; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][key]" value="<?php echo $field_name; ?>">
                                                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][name]" value="<?php echo $field_label; ?>">
                                                                            </div>
                                                                            <div class="form-group col-12 col-lg-12"> 
                                                                                <?php if (
                                                                                    $field_verified ==
                                                                                    'yes'
                                                                                ) { ?>
                                                                                    <label class="d-block">File needs to be verified: <?php echo $field_verified; ?></label>
                                                                                    <label class="d-block">
                                                                                    <?php
                                                                                    $field_verification_grade_text =
                                                                                        '';
                                                                                    if (
                                                                                        $field_verification_grade ==
                                                                                        'G'
                                                                                    ) {
                                                                                        $field_verification_grade_text =
                                                                                            'Verification grade: Government';
                                                                                    } elseif (
                                                                                        $field_verification_grade ==
                                                                                        'F'
                                                                                    ) {
                                                                                        $field_verification_grade_text =
                                                                                            'Verification grade: Financial';
                                                                                    } elseif (
                                                                                        $field_verification_grade ==
                                                                                        'T'
                                                                                    ) {
                                                                                        $field_verification_grade_text =
                                                                                            'Verification grade: Telecom';
                                                                                    } elseif (
                                                                                        $field_verification_grade ==
                                                                                        'A'
                                                                                    ) {
                                                                                        $field_verification_grade_text =
                                                                                            'Verification grade: Akcess';
                                                                                    } elseif (
                                                                                        $field_verification_grade ==
                                                                                        'O'
                                                                                    ) {
                                                                                        $field_verification_grade_text =
                                                                                            'Verification grade: Other';
                                                                                    }
                                                                                    ?>
                                                                                    </label>
                                                                                    <label class="d-block"><?php echo $field_verification_grade_text; ?></label>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <?php $field_check =
                                                                        '1';}
                                                            } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Select Fields large screen-->
                                    <div class="col-12 col-lg-4 d-none d-lg-block pl-lg-0">
                                        <div class="card">
                                            <input type="hidden" class="field_check" value="<?php echo $field_check; ?>">                                        
                                            <div class="card-body" id="getfield"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane eform-notification" id="eform-3">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label pl-0">Additional Notification ? :</label>
                                    <div class="col-md-8 d-flex align-items-center">
                                        <?php
                                        $additional_notification_yes = "";
                                        $additional_notification_no = "";
                                        if (
                                            $eform->isAdditionalNotification ==
                                            "yes"
                                        ) {
                                            $additional_notification_yes =
                                                'checked';
                                        } elseif (
                                            $eform->isAdditionalNotification ==
                                            "yes"
                                        ) {
                                            $additional_notification_no =
                                                'checked';
                                        }
                                        ?>
                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="radio" name="additional_notification" id="additional_notification_yes" value="yes" <?php echo $additional_notification_yes; ?> />
                                                Yes
                                                <span class="circle">
                                                    <span class="check"></span>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="form-check form-check-radio">
                                             <label class="form-check-label">
                                                  <input class="form-check-input" type="radio" name="additional_notification" id="additional_notification_no" value="no" <?php echo $additional_notification_no; ?> />
                                                  No
                                                  <span class="circle">
                                                      <span class="check"></span>
                                                  </span>
                                             </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group" id="form_multiple_akcessID" >   
                                            <select name="akcessID[]" multiple id="multiple_akcessID" class="form-control">
                                                <?php foreach (
                                                    $users
                                                    as $user
                                                ) {
                                                    if (
                                                        isset(
                                                            $user->akcessId
                                                        ) &&
                                                        $user->akcessId != ""
                                                    ) {
                                                        $akcessId =
                                                            $user->akcessId;
                                                        $name =
                                                            $user->name .
                                                            " ( " .
                                                            $user->akcessId .
                                                            " ) ";
                                                        if (isset($akcessId)) {

                                                            $selectd = '';
                                                            if (
                                                                in_array(
                                                                    $akcessId,
                                                                    $dataAdditional
                                                                )
                                                            ) {
                                                                $selectd =
                                                                    'selected';
                                                            }
                                                            ?>
                                                        <option <?php echo $selectd; ?> value="<?php echo $user->akcessId; ?>"><?php echo $name; ?></option>
                                                        <?php
                                                        }
                                                    }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="tab-pane preview" id="eform-4">
                                <div class="row" id="notification_get">
                                    <div class="form-group col-12">
                                        <div class="input-group" id="addfield">                                                        
                                            <?php foreach ($fields as $n) {
                                                $field_check = '0';
                                                $field_ids = $n->id;
                                                $field_name = $n->key;
                                                $field_label = $n->labelname;
                                                $field_type = $n->keytype;
                                                $field_isVisible =
                                                    $n->isVisible;
                                                $field_section = $n->section;
                                                $field_verification_grade =
                                                    $n->verification_grade;
                                                $field_verified =
                                                    $n->file_verified;
                                                $field_mandate =
                                                    $n->is_mandatory;
                                                $field_items = $n->options;
                                                $field_label_instructions =
                                                    $n->instructions;
                                                if ($field_type == 'file') { ?>
                                                    <div class="col col-lg-6 <?php echo $field_ids; ?>">
                                                        <div class="row">
                                                            <div class="form-group col col-lg-12">
                                                                <label class="form-label"><?php echo $field_label; ?></label>                                                                    
                                                                <input type="file" data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" class="form-control custom-file-input custom-file-label">
                                                            </div>
                                                            <div class="form-group col col-lg-12 pt-4">
                                                                <?php if (
                                                                    $field_mandate ==
                                                                    'yes'
                                                                ) { ?>
                                                                <label class="form-label eformLabel">Signature required: <?php echo $field_mandate; ?></label>
                                                                <?php } ?>
                                                                <?php if (
                                                                    $field_verified ==
                                                                    'yes'
                                                                ) { ?>
                                                                    <label class="form-label eformLabel">File needs to be verified: <?php echo $field_verified; ?></label>

                                                                    <?php
                                                                    $field_verification_grade_text =
                                                                        '';
                                                                    if (
                                                                        $field_verification_grade ==
                                                                        'G'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Government';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'F'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Financial';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'T'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Telecom';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'A'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Akcess';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'O'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Other';
                                                                    }
                                                                    ?>
                                                                    <label class="form-label eformLabel"><?php echo $field_verification_grade_text; ?></label>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php }
                                                if (
                                                    $field_type == 'string' ||
                                                    $field_type == 'text'
                                                ) { ?>
                                                    <div class="col col-lg-6 <?php echo $field_ids; ?>">
                                                        <div class="row">
                                                            <div class="form-group col col-lg-12">
                                                            <label class="form-label"><?php echo $field_label; ?></label>

                                                            <input type="text" data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_label; ?>" class="form-control">
                                                            </div>
                                                            <div class="form-group col col-lg-12">
                                                                <?php if (
                                                                    $field_verified ==
                                                                    'yes'
                                                                ) { ?>
                                                                    <label class="form-label eformLabel">File needs to be verified: <?php echo $field_verified; ?></label>

                                                                    <?php
                                                                    $field_verification_grade_text =
                                                                        '';
                                                                    if (
                                                                        $field_verification_grade ==
                                                                        'G'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Government';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'F'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Financial';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'T'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Telecom';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'A'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Akcess';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'O'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Other';
                                                                    }
                                                                    ?>
                                                                    <label class="form-label eformLabel"><?php echo $field_verification_grade_text; ?></label>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php }
                                                if (
                                                    $field_type == 'address' ||
                                                    $field_type == 'textarea'
                                                ) { ?>
                                                    <div class="col col-lg-6 <?php echo $field_ids; ?>">
                                                        <div class="row">
                                                            <div class="form-group col col-lg-12">
                                                                <label class="form-label"><?php echo $field_label; ?></label>
                                                                <textarea data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_name; ?>" col="3" class="form-control"></textarea>  
                                                            </div>
                                                            <div class="form-group col col-lg-12">
                                                                <?php if (
                                                                    $field_verified ==
                                                                    'yes'
                                                                ) { ?>
                                                                    <label class="form-label eformLabel">File needs to be verified: <?php echo $field_verified; ?></label>

                                                                    <?php
                                                                    $field_verification_grade_text =
                                                                        '';
                                                                    if (
                                                                        $field_verification_grade ==
                                                                        'G'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Government';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'F'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Financial';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'T'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Telecom';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'A'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Akcess';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'O'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Other';
                                                                    }
                                                                    ?>
                                                                    <label class="form-label eformLabel"><?php echo $field_verification_grade_text; ?></label>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php }
                                                if ($field_type == 'list') { ?>
                                                    <div class="col col-lg-6 <?php echo $field_ids; ?>">
                                                        <div class="row">
                                                            <div class="form-group col col-lg-12">
                                                                <label class="form-label"><?php echo $field_label; ?></label>

                                                                    <select data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_label; ?>" class="form-control">
                                                                    <?php $explode = explode(
                                                                        ",",
                                                                        $field_items
                                                                    ); ?>
                                                                    <option value="">Select <?php echo $field_label; ?></option>
                                                                    <?php foreach (
                                                                        $explode
                                                                        as $key =>
                                                                            $value
                                                                    ) { ?>
                                                                        <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                                                                    <?php } ?>
                                                                    </select>  

                                                            </div>
                                                            <div class="form-group col col-lg-12">
                                                                <?php if (
                                                                    $field_verified ==
                                                                    'yes'
                                                                ) { ?>
                                                                    <label class="form-label eformLabel">File needs to be verified: <?php echo $field_verified; ?></label>

                                                                    <?php
                                                                    $field_verification_grade_text =
                                                                        '';
                                                                    if (
                                                                        $field_verification_grade ==
                                                                        'G'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Government';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'F'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Financial';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'T'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Telecom';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'A'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Akcess';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'O'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Other';
                                                                    }
                                                                    ?>
                                                                    <label class="form-label eformLabel"><?php echo $field_verification_grade_text; ?></label>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                   <?php }
                                                if ($field_type == 'phone') { ?>
                                                    <div class="col col-lg-6 <?php echo $field_ids; ?>">
                                                        <div class="row">
                                                            <div class="form-group col col-lg-12">
                                                                <label class="form-label"><?php echo $field_label; ?></label>

                                                                <input type="tel" data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_label; ?>" class="form-control">

                                                            </div>
                                                            <div class="form-group col col-lg-12">
                                                                
                                                                <?php if (
                                                                    $field_verified ==
                                                                    'yes'
                                                                ) { ?>
                                                                    <label class="form-label eformLabel">File needs to be verified: <?php echo $field_verified; ?></label>

                                                                    <?php
                                                                    $field_verification_grade_text =
                                                                        '';
                                                                    if (
                                                                        $field_verification_grade ==
                                                                        'G'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Government';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'F'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Financial';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'T'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Telecom';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'A'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Akcess';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'O'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Other';
                                                                    }
                                                                    ?>
                                                                    <label class="form-label eformLabel"><?php echo $field_verification_grade_text; ?></label>
                                                                <?php } ?>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php }
                                                if (
                                                    $field_type == 'number'
                                                ) { ?>
                                                    <div class="col col-lg-6 <?php echo $field_ids; ?>">
                                                        <div class="row">
                                                            <div class="form-group col col-lg-12">
                                                                <label class="form-label"><?php echo $field_label; ?></label>

                                                                <input type="number" data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_label; ?>" class="form-control">

                                                            </div>
                                                            <div class="form-group col col-lg-12">
                                                                <?php if (
                                                                    $field_verified ==
                                                                    'yes'
                                                                ) { ?>
                                                                    <label class="form-label eformLabel">File needs to be verified: <?php echo $field_verified; ?></label>

                                                                    <?php
                                                                    $field_verification_grade_text =
                                                                        '';
                                                                    if (
                                                                        $field_verification_grade ==
                                                                        'G'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Government';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'F'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Financial';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'T'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Telecom';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'A'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Akcess';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'O'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Other';
                                                                    }
                                                                    ?>
                                                                    <label class="form-label "><?php echo $field_verification_grade_text; ?></label>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php }
                                                if ($field_type == 'date') { ?>
                                                    <div class="col col-lg-6 <?php echo $field_ids; ?>">
                                                        <div class="row">
                                                            <div class="form-group col col-lg-12">
                                                                <label class="form-label"><?php echo $field_label; ?></label>

                                                                <input type="text" data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_label; ?> ( YYYY-MM-DD )" class="form-control date_field">

                                                            </div>
                                                            <div class="form-group col col-lg-12">
                                                                
                                                                <?php if (
                                                                    $field_verified ==
                                                                    'yes'
                                                                ) { ?>
                                                                    <label class="form-label eformLabel">File needs to be verified: <?php echo $field_verified; ?></label>

                                                                    <?php
                                                                    $field_verification_grade_text =
                                                                        '';
                                                                    if (
                                                                        $field_verification_grade ==
                                                                        'G'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Government';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'F'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Financial';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'T'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Telecom';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'A'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Akcess';
                                                                    } elseif (
                                                                        $field_verification_grade ==
                                                                        'O'
                                                                    ) {
                                                                        $field_verification_grade_text =
                                                                            'Verification grade: Other';
                                                                    }
                                                                    ?>
                                                                    <label class="form-label eformLabel"><?php echo $field_verification_grade_text; ?></label>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php }
                                            } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>                           
                            <!-- tab content ends-->
                        </div>
                        <!--tabs ends -->
                    </div>
                    <?= $this->Form->end() ?>
                </div>            
            </div>
        </div>
    </div>
</div>


<div class="modal " id="eFormCreateFieldModalModule" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel_create">Select Field Options</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="mb-0" role="form" action="" id="addFieldForm_create" method="POST">
      <div class="modal-body">
        <div class="form-group">
            <label class="form-label">Label Name</label>
            <input type="text" class="form-control" id="field_label_create" >
        </div>
        <div class="form-group">
            <label class="form-label">Instructions</label>
            <textarea class="form-control" id="field_label_instructions_create" rows="3"></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Field Type</label>
            <select class="form-control" id="field_type_create">
                <option value="">Select Field Type</option>
                <option value="text">String</option>
                <option value="textarea">Text Area</option>
                <option value="number">Number</option>
                <option value="password">Password</option>
                <option value="date">Date</option>
                <option value="radio">Radio</option>
                <option value="select">Select List</option>
                <option value="checkbox">Checkbox</option>
            </select>
        </div>
        <div class="form-group" id="newTag">
            <label class="form-label">Field Options</label>
            <div id="newTagDiv">
                <input type="text" id="tokenize" placeholder="Enter New Label (A-Z,a-z,0-9,_)" class="form-control input_tokenize">
            </div>
        </div> 

        <div class="form-group">
            <div class="input-group row">
                <label class="form-label col-sm-6">Is the field mandatory?</label>
                <div class="col-sm-6">                                    
                    <div class="form-check form-check-radio d-flex justify-content-around">
                        <label class="form-check-label">
                            <input class="form-check-input" type="radio" name="field_mandate_yes" id="field_mandate_yes_create" value="yes" >
                            Yes
                            <span class="circle">
                                <span class="check"></span>
                            </span>
                        </label>
                        <label class="form-check-label">
                            <input class="form-check-input" type="radio" name="field_mandate_no" id="field_mandate_no_create" value="no" >
                            No
                            <span class="circle">
                                <span class="check"></span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="input-group row">
                <label class="form-label col-sm-6">Field must be verified?</label>
                <div class="col-sm-6">                                    
                    <div class="form-check form-check-radio d-flex justify-content-around">
                        <label class="form-check-label">
                            <input class="form-check-input" type="radio" name="fieldver_yes" id="fieldver_yes_create" value="yes" >
                            Yes
                            <span class="circle">
                                <span class="check"></span>
                            </span>
                        </label>
                        <label class="form-check-label">
                            <input class="form-check-input" type="radio" name="fieldver_no" checked id="fieldver_no_create" value="no" >
                            No
                            <span class="circle">
                                <span class="check"></span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div id="fieldverified_yes_no_create">
            <div class="input-group row">
                <label class="form-label col-sm-6">What grade?</label>
                <div class="col-sm-6">
                    <select class="form-control" id="verification_grade_create">
                        <option value="">Select Grade</option>
                        <option value="G">Government</option>
                        <option value="F">Financial</option>
                        <option value="T">Telecom</option>
                        <option value="A">AKcess</option>
                        <option value="O">Other</option>
                    </select>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary waves-effect" id="submit_create" onclick="createForm()">Add</button>
        <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>
  </div>
</div>


<div class="modal " id="eFormFieldModalModule" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel">Select Field Options</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="mb-0" role="form" action="" id="addFieldForm" method="POST">
      <div class="modal-body">
        <div class="form-group">
            <label class="form-label">Label Name</label>
            <input type="text" class="form-control" id="field_label" >
        </div>
        <div class="form-group">
            <label class="form-label">Instructions</label>
            <textarea class="form-control" id="field_label_instructions" rows="3"></textarea>
        </div>
        <div class="form-group">
            <div class="input-group row">
                <label class="form-label col-sm-6">Is the field mandatory?</label>
                <div class="col-sm-6">                                    
                    <div class="form-check form-check-radio d-flex justify-content-around">
                        <label class="form-check-label">
                            <input class="form-check-input" type="radio" name="field_mandate_yes" id="field_mandate_yes" value="yes" >
                            Yes
                            <span class="circle">
                                <span class="check"></span>
                            </span>
                        </label>
                        <label class="form-check-label">
                            <input class="form-check-input" type="radio" name="field_mandate_no" id="field_mandate_no" value="no" >
                            No
                            <span class="circle">
                                <span class="check"></span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="input-group row">
                <label class="form-label col-sm-6">Field must be verified?</label>
                <div class="col-sm-6">                                    
                    <div class="form-check form-check-radio d-flex justify-content-around">
                        <label class="form-check-label">
                            <input class="form-check-input" type="radio" name="fieldver_yes" id="fieldver_yes" value="yes" >
                            Yes
                            <span class="circle">
                                <span class="check"></span>
                            </span>
                        </label>
                        <label class="form-check-label">
                            <input class="form-check-input" type="radio" name="fieldver_no" checked id="fieldver_no" value="no" >
                            No
                            <span class="circle">
                                <span class="check"></span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div id="fieldverified_yes_no_create">
            <div class="input-group row">
                <label class="form-label col-sm-6">What grade?</label>
                <div class="col-sm-6">
                    <select class="form-control" id="verification_grade">
                        <option value="">Select Grade</option>
                        <option value="G">Government</option>
                        <option value="F">Financial</option>
                        <option value="T">Telecom</option>
                        <option value="A">AKcess</option>
                        <option value="O">Other</option>
                    </select>
                </div>
                <input type="hidden" id="field_verification_grade">
                <input type="hidden" id="field_name">
                <input type="hidden" id="field_type">
                <input type="hidden" id="field_isVisible">
                <input type="hidden" id="field_section">
                <input type="hidden" id="field_ids">
                <input type="hidden" id="field_items">
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary waves-effect" id="submit" onclick="addeForm()">Add</button>
        <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
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
