<style>
.eform-field:after
{
    content: "*" !important;
    color: #dc3545 !important;
}

.select2-container--default .select2-selection--multiple {
   min-height: 38px !important;
       padding: 3px 10px;
       font-size: 15px;
       line-height: 1.33;
       border-color: #ced4da;
       border-radius: 4px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow b {
    top: 85% !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 28px !important;
}
.select2-container--default .select2-selection--single {
    border: 1px solid #CCC !important;
    box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset;
    transition: border-color 0.15s ease-in-out 0s, box-shadow 0.15s ease-in-out 0s;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 22px !important;
}
.select2-dropdown--above{
    width: 426px !important;
}
.select2-dropdown--below{
    width: 426px !important;
}
.select2-container{
 width: 100% !important;
}
</style>
<?php
   /**
    * @var \App\View\AppView $this
    * @var \App\Model\Entity\IDCard $idcard
    */
   use Cake\ORM\TableRegistry; ?>
<div class="edit-eform-page">
   <div class="row">
      <div class="col-12">
         <div class="card">
            <div class="card-body">
               <h4 class="card-title"><i class="fab fa-wpforms mr-1"></i><?= $page_title ?></h4>
               <hr/>
               <?= $this->Form->create($id, [
                  "enctype" => "multipart/form-data",
                  "id" => "eformEdit",
                  ]) ?>
               <input type="hidden" id="eid" value="<?php echo $eid; ?>">
               <!-- Nav tabs -->
               <ul class="nav nav-tabs" role="tablist" id="myTabs">
                  <li class="nav-item font-400"><a class="nav-link active" data-toggle="tab" href="#eform-1" role="tab">Settings</a> </li>
                  <li class="nav-item font-400"><a class="nav-link eform-field" data-toggle="tab" href="#eform-2" role="tab">Fields</a> </li>
                  <li class="nav-item font-400"><a class="nav-link" data-toggle="tab" href="#eform-5" role="tab" onclick="previewForm();">Process</a></li>
                  <li class="nav-item font-400"><a class="nav-link" data-toggle="tab" href="#eform-3" role="tab" onclick="previewForm();">Notifications</a></li>
                  <li class="nav-item font-400"><a class="nav-link" data-toggle="tab" href="#eform-4" role="tab" onclick="previewForm();">Preview</a></li>
               </ul>
               <!-- Tab panes -->
               <div class="tab-content tabcontent-border">
                  <div class="tab-pane active" id="eform-1" role="tabpanel">
                     <div class="p-20">
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <?php echo $this->Form->input(
                                    "formName",
                                    [
                                        'label' => [
                                            'text' => 'eForm Name', 
                                            'class' => 'required'
                                        ],
                                        "class" => "form-control",
                                        "value" => $eform->formName,
                                    ]
                                    ); ?>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <?php echo $this->Form->input(
                                    "description",
                                    [
                                        "label" => "eForm Description",
                                        "class" => "form-control",
                                        "value" => $eform->description,
                                    ]
                                    ); ?>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <?php echo $this->Form->input(
                                    "instruction",
                                    [
                                        "label" => "eForm Instruction",
                                        "class" => "form-control",
                                        "value" => $eform->instruction,
                                    ]
                                    ); ?>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-12">
                              <div class="form-group row">
                                 <label class="control-label col-md-3">Publish this eForm?</label>
                                 <div class="col-md-9 d-flex align-items-center">
                                    <?php
                                       $publish_yes = "";
                                       $publish_no = "";
                                       if ($eform->publish == "yes") {
                                           $publish_yes = "checked";
                                       } elseif ($eform->publish == "no") {
                                           $publish_no = "checked";
                                       }
                                       ?>
                                    <label class="custom-control custom-radio mr-5">
                                    <input id="publish_yes" name="publish" type="radio" class="custom-control-input" value="yes" <?php echo $publish_yes; ?>>
                                    <span class="custom-control-label">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio">
                                    <input id="publish_no" name="publish" type="radio" class="custom-control-input" value="no" <?php echo $publish_no; ?>>
                                    <span class="custom-control-label">No</span>
                                    </label>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-12">
                              <div class="form-group row">
                                 <label class="control-label col-md-3">Signature for eForm?</label>
                                 <div class="col-md-9 d-flex align-items-center">
                                    <?php
                                       $signature_yes = "";
                                       $signature_no = "";
                                       if ($eform->signature == "yes") {
                                           $signature_yes = "checked";
                                       } elseif (
                                           $eform->signature == "no"
                                       ) {
                                           $signature_no = "checked";
                                       }
                                       ?>
                                    <label class="custom-control custom-radio mr-5">
                                    <input class="custom-control-input" type="radio" name="signature" id="signature_yes" value="yes" <?php echo $signature_yes; ?>>
                                    <span class="custom-control-label">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" name="signature" id="signature_no" value="no" <?php echo $signature_no; ?>>
                                    <span class="custom-control-label">No</span>
                                    </label>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-12">
                              <div class="form-group row">
                                 <label class="control-label col-md-3">Is face match required?</label>
                                 <div class="col-md-9 d-flex align-items-center">
                                    <?php
                                       $facematch_yes = "";
                                       $facematch_no = "";
                                       if ($eform->facematch == "yes") {
                                           $facematch_yes = "checked";
                                       } elseif (
                                           $eform->facematch == "no"
                                       ) {
                                           $facematch_no = "checked";
                                       }
                                       ?>
                                    <label class="custom-control custom-radio mr-5">
                                    <input class="custom-control-input" type="radio" name="facematch" id="facematch_yes" value="yes" <?php echo $facematch_yes; ?>>
                                    <span class="custom-control-label">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" name="facematch" id="facematch_no" value="no" <?php echo $facematch_no; ?>>
                                    <span class="custom-control-label">No</span>
                                    </label>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-12">
                              <div class="form-group row">
                                 <label class="control-label col-md-3">Pull data from document?</label>
                                 <div class="col-md-9 d-flex align-items-center">
                                    <?php
                                       $pulldata_yes = "";
                                       $pulldata_no = "";
                                       if ($eform->pulldata == "yes") {
                                           $pulldata_yes = "checked";
                                       } elseif (
                                           $eform->pulldata == "no"
                                       ) {
                                           $pulldata_no = "checked";
                                       }
                                       ?>
                                    <label class="custom-control custom-radio mr-5">
                                    <input class="custom-control-input" type="radio" name="pulldata" id="pulldata_yes" value="yes" <?php echo $pulldata_yes; ?> >
                                    <span class="custom-control-label">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" name="pulldata" id="pulldata_no" value="no" <?php echo $pulldata_no; ?> >
                                    <span class="custom-control-label">No</span>
                                    </label>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-12">
                              <div class="form-group row">
                                 <label class="control-label col-md-3">Store in profile?</label>
                                 <div class="col-md-9 d-flex align-items-center">
                                    <?php
                                       $storeinprofile_yes = "";
                                       $storeinprofile_no = "";
                                       if (
                                           $eform->storeinprofile == "yes"
                                       ) {
                                           $storeinprofile_yes = "checked";
                                       } elseif (
                                           $eform->storeinprofile == "no"
                                       ) {
                                           $storeinprofile_no = "checked";
                                       }
                                       ?>
                                    <label class="custom-control custom-radio mr-5">
                                    <input class="custom-control-input" type="radio" name="storeinprofile" id="storeinprofile_yes" value="yes" <?php echo $storeinprofile_yes; ?> >
                                    <span class="custom-control-label">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" name="storeinprofile" id="storeinprofile_no" value="no" <?php echo $storeinprofile_no; ?> >
                                    <span class="custom-control-label">No</span>
                                    </label>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-12">
                              <div class="form-group row">
                                 <label class="control-label col-md-3">Is Default invitation Eform?</label>
                                 <div class="col-md-9 d-flex align-items-center">
                                    <?php
                                       $isclientInvitationEform_yes = "";
                                       $isclientInvitationEform_no = "";
                                       if (
                                           $eform->isclientInvitationEform ==
                                           "yes"
                                       ) {
                                           $isclientInvitationEform_yes =
                                               "checked";
                                       } elseif (
                                           $eform->isclientInvitationEform ==
                                           "no"
                                       ) {
                                           $isclientInvitationEform_no =
                                               "checked";
                                       }
                                       ?>
                                    <label class="custom-control custom-radio mr-5">
                                    <input class="custom-control-input isclientInvitationEform" type="radio" name="isclientInvitationEform" id="isclientInvitationEform_yes" value="yes" <?php echo $isclientInvitationEform_yes; ?> >
                                    <span class="custom-control-label">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio">
                                    <input class="custom-control-input isclientInvitationEform" type="radio" name="isclientInvitationEform" id="isclientInvitationEform_no" value="no" <?php echo $isclientInvitationEform_no; ?> >
                                    <span class="custom-control-label">No</span>
                                    </label>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-12">
                              <div class="form-group row" id="form_send_invite">
                                 <label class="control-label col-md-3">Default invitation for</label>
                                 <div class="col-md-9 d-flex align-items-center">
                                    <select id="send_invitation_eform" class="global-tokenize form-control custom-select" name="send_invitation_eform">
                                       <option value="">Select Send invitation</option>
                                       <option <?php if (
                                          isset(
                                              $eform->send_invitation_eform
                                          ) &&
                                          $eform->send_invitation_eform ==
                                              "Staff"
                                          ) {
                                          echo "selected";
                                          } ?> value="Staff">Staff</option>
                                       <option <?php if (
                                          isset(
                                              $eform->send_invitation_eform
                                          ) &&
                                          $eform->send_invitation_eform ==
                                              "Teacher"
                                          ) {
                                          echo "selected";
                                          } ?> value="Teacher">Academic Personnel</option>
                                       <option <?php if (
                                          isset(
                                              $eform->send_invitation_eform
                                          ) &&
                                          $eform->send_invitation_eform ==
                                              "Students"
                                          ) {
                                          echo "selected";
                                          } ?> value="Students">Students</option>
                                    </select>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr/>
                        <div class="form-actions text-right">
                           <button type="button" class="btn waves-effect waves-light btn-success next-btn mx-1">Next</button>
                           <?= $this->Html->link(
                              __(
                                  '<i class="fas fa-times mr-1"></i> Close'
                              ),
                              ["action" => "index"],
                              [
                                  "escape" => false,
                                  "class" =>
                                      "btn waves-effect waves-light btn-danger",
                              ]
                              ) ?>                        
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane eform-fields" id="eform-2" role="tabpanel">
                     <div class="p-20">
                        <div class="row">
                           <!-- Select Fields Mobile-->
                           <div class="d-lg-none">
                           </div>
                           <!-- eform Body-->
                           <div class="col-lg-7 col-xl-8">
                              <div class="card">
                                 <div class="card-body">
                                    <div id="notification_display">
                                       <div class="input-group" id="addfield">
                                          <?php foreach (
                                             $fields
                                             as $n
                                             ) {
                                             $field_check =
                                                 "0";
                                             $field_ids =
                                                 $n->id;
                                             $field_name =
                                                 $n->keyfields;
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
                                                 "file"
                                             ) { ?>
                                          <div class="col-lg-6 <?php echo $field_ids; ?>">
                                             <div class="form-group">
                                                <label class="<?= isset($field_mandate) && $field_mandate == 'yes' ? 'required' : ''; ?>"><?php echo $field_label; ?></label>
                                                <a href="javascript:void(0);" class="btn-delete red-txt fielda" onclick="removeField('<?php echo $field_ids; ?>')"><i class="fa fa-trash"></i></a>
                                                <div class="custom-file">
                                                   <input type="file" class="custom-file-input"
                                                      data-instructions="<?php echo $field_label_instructions; ?>" 
                                                      data-name="<?php echo $field_name; ?>" 
                                                      data-type="<?php echo $field_type; ?>" 
                                                      data-isVisible="<?php echo $field_isVisible; ?>" 
                                                      data-section="<?php echo $field_section; ?>" 
                                                      data-verification-grade="<?php echo $field_verification_grade; ?>" 
                                                      data-fieldver="<?php echo $field_verified; ?>" 
                                                      data-field_mandate="<?php echo $field_mandate; ?>" 
                                                      id="field_<?php echo $field_name; ?>" 
                                                      name="field_name[<?php echo $field_name; ?>][]" 
                                                      data-ids="<?php echo $field_ids; ?>" 
                                                      data-items="<?php echo $field_items; ?>"
                                                      disabled="disabled" 
                                                      >
                                                   <label class="custom-file-label" for="field_<?php echo $field_name; ?>">Choose file</label>
                                                </div>
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][instructions]" value="<?php echo $field_label_instructions; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][type]" value="<?php echo $field_type; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][isVisible]" value="<?php echo $field_isVisible; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][section]" value="<?php echo $field_section; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][verification_grade]" value="<?php echo $field_verification_grade; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][fieldver]" value="<?php echo $field_verified; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][field_mandate]" value="<?php echo $field_mandate; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][ids]" value="<?php echo $field_ids; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][items]" value="<?php echo $field_items; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][keyfields]" value="<?php echo $field_name; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][name]" value="<?php echo $field_label; ?>">
                                             </div>
                                             <div class="form-group">
                                                <?php if (
                                                   $field_mandate ==
                                                   "yes"
                                                   ) { ?>
                                                <label class="d-block">Signature required : <?php echo $field_mandate; ?></label>
                                                <?php } ?>
                                                <?php if (
                                                   $field_verified ==
                                                   "yes"
                                                   ) { ?>
                                                <label class="d-block">File needs to be verified : <?php echo $field_verified; ?></label>
                                                <label class="d-block">  
                                                <?php
                                                   $field_verification_grade_text =
                                                       "";
                                                   if (
                                                       $field_verification_grade ==
                                                       "G"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade : Government";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "F"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade : Financial";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "T"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade : Telecom";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "A"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade : Akcess";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "O"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade : Other";
                                                   }
                                                   ?>
                                                </label>
                                                <label class="d-block"><?php echo $field_verification_grade_text; ?></label>
                                                <?php } ?>
                                             </div>
                                          </div>
                                          <?php $field_check =
                                             "1"; ?>
                                          <?php }
                                             if (
                                                 $field_type ==
                                                 "password"
                                             ) { ?>
                                          <div class="col-lg-6 <?php echo $field_ids; ?>">
                                             <div class="form-group">
                                                <label class="<?= isset($field_mandate) && $field_mandate == 'yes' ? 'required' : ''; ?>"><?php echo $field_label; ?></label>
                                                <a href="javascript:void(0);" class="btn-delete red-txt fielda" onclick="removeField('<?php echo $field_ids; ?>')"><i class="fa fa-trash"></i></a>
                                                <input type="password" class="form-control" data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_label; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][instructions]" value="<?php echo $field_label_instructions; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][type]" value="<?php echo $field_type; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][isVisible]" value="<?php echo $field_isVisible; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][section]" value="<?php echo $field_section; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][verification_grade]" value="<?php echo $field_verification_grade; ?>">                                                                                                                        <input type="hidden" name="field_name[<?php echo $field_name; ?>][fieldver]" value="<?php echo $field_verified; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][field_mandate]" value="<?php echo $field_mandate; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][ids]" value="<?php echo $field_ids; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][items]" value="<?php echo $field_items; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][keyfields]" value="<?php echo $field_name; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][name]" value="<?php echo $field_label; ?>">
                                             </div>
                                             <div class="form-group"> 
                                                <?php if (
                                                   $field_verified ==
                                                   "yes"
                                                   ) { ?>
                                                <label class="d-block">File needs to be verified: <?php echo $field_verified; ?></label>
                                                <label class="d-block">
                                                <?php
                                                   $field_verification_grade_text =
                                                       "";
                                                   if (
                                                       $field_verification_grade ==
                                                       "G"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Government";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "F"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Financial";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "T"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Telecom";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "A"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Akcess";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "O"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Other";
                                                   }
                                                   ?>
                                                </label>
                                                <label class="d-block"><?php echo $field_verification_grade_text; ?></label>
                                                <?php } ?>
                                             </div>
                                          </div>
                                          <?php $field_check =
                                             "1"; ?>
                                          <?php }
                                             if (
                                                 $field_type ==
                                                 "radio"
                                             ) { ?>
                                          <div class="col-lg-6 <?php echo $field_ids; ?>">
                                             <div class="form-group">
                                                <label class="<?= isset($field_mandate) && $field_mandate == 'yes' ? 'required' : ''; ?>"><?php echo $field_label; ?></label>
                                                <a href="javascript:void(0);" class="btn-delete red-txt fielda txt-red" onclick="removeField('<?php echo $field_ids; ?>')"><i class="fa fa-trash"></i></a>
                                                <?php
                                                   $explode = explode(
                                                       ",",
                                                       $field_items
                                                   );
                                                   
                                                   foreach (
                                                       $explode
                                                       as $key =>
                                                           $value
                                                   ) { ?>
                                                <div class="form-check form-check-radio">
                                                   <label class="form-check-label">
                                                   <input class="form-check-input" type="radio" 
                                                      data-instructions="<?php echo $field_label_instructions; ?>"
                                                      data-name="<?php echo $field_name; ?>" 
                                                      data-type="<?php echo $field_type; ?>"
                                                      data-isVisible="<?php echo $field_isVisible; ?>" 
                                                      data-section="<?php echo $field_section; ?>" 
                                                      data-verification-grade="<?php echo $field_verification_grade; ?>" 
                                                      data-fieldver="<?php echo $field_verified; ?>" 
                                                      data-field_mandate="<?php echo $field_mandate; ?>" 
                                                      id="field_<?php echo $field_name; ?>" 
                                                      name="field_name[<?php echo $field_name; ?>][]" 
                                                      data-ids="<?php echo $field_ids; ?>" 
                                                      data-items="<?php echo $value; ?>" 
                                                      placeholder="<?php echo $field_label; ?>">
                                                   <?php echo $value; ?>  
                                                   <span class="circle">
                                                   <span class="check"></span>
                                                   </span>
                                                   </label>
                                                </div>
                                                <?php }
                                                   ?>
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][instructions]" value="<?php echo $field_label_instructions; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][type]" value="<?php echo $field_type; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][isVisible]" value="<?php echo $field_isVisible; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][section]" value="<?php echo $field_section; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][verification_grade]" value="<?php echo $field_verification_grade; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][fieldver]" value="<?php echo $field_verified; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][field_mandate]" value="<?php echo $field_mandate; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][ids]" value="<?php echo $field_ids; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][items]" value="<?php echo $field_items; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][keyfields]" value="<?php echo $field_name; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][name]" value="<?php echo $field_label; ?>">
                                             </div>
                                             <div class="form-group"> 
                                                <?php if (
                                                   $field_verified ==
                                                   "yes"
                                                   ) { ?>
                                                <label class="d-block">File needs to be verified: <?php echo $field_verified; ?></label>
                                                <label class="d-block">
                                                <?php
                                                   $field_verification_grade_text =
                                                       "";
                                                   if (
                                                       $field_verification_grade ==
                                                       "G"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Government";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "F"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Financial";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "T"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Telecom";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "A"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Akcess";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "O"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Other";
                                                   }
                                                   ?>
                                                </label>
                                                <label class="d-block"><?php echo $field_verification_grade_text; ?></label>
                                                <?php } ?>
                                             </div>
                                          </div>
                                          <?php $field_check =
                                             "1"; ?>
                                          <?php }
                                             if (
                                                 $field_type ==
                                                 "checkbox"
                                             ) { ?>
                                          <div class="col-lg-6 <?php echo $field_ids; ?>">
                                             <div class="form-group">
                                                <label class="<?= isset($field_mandate) && $field_mandate == 'yes' ? 'required' : ''; ?>"><?php echo $field_label; ?></label>
                                                <a href="javascript:void(0);" class="btn-delete red-txt fielda" onclick="removeField('<?php echo $field_ids; ?>')"><i class="fa fa-trash"></i></a>
                                                <?php
                                                   $explode = explode(
                                                       ",",
                                                       $field_items
                                                   );
                                                   
                                                   foreach (
                                                       $explode
                                                       as $key =>
                                                           $value
                                                   ) { ?>
                                                <div class="form-check">
                                                   <label class="form-check-label">
                                                   <input class="form-check-input" type="checkbox"
                                                      data-instructions="<?php echo $field_label_instructions; ?>" 
                                                      data-name="<?php echo $field_name; ?>" 
                                                      data-type="<?php echo $field_type; ?>"
                                                      data-isVisible="<?php echo $field_isVisible; ?>" 
                                                      data-section="<?php echo $field_section; ?>" 
                                                      data-verification-grade="<?php echo $field_verification_grade; ?>" 
                                                      data-fieldver="<?php echo $field_verified; ?>" 
                                                      data-field_mandate="<?php echo $field_mandate; ?>" 
                                                      id="field_<?php echo $field_name; ?>" 
                                                      name="field_name[<?php echo $field_name; ?>][]" 
                                                      data-ids="<?php echo $field_ids; ?>" 
                                                      data-items="<?php echo $value; ?>" 
                                                      placeholder="<?php echo $field_label; ?>" 
                                                      />
                                                   <?php echo $value; ?>   
                                                   <span class="form-check-sign">
                                                   <span class="check"></span>
                                                   </span>
                                                   </label>
                                                </div>
                                                <?php }
                                                   ?>
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][instructions]" value="<?php echo $field_label_instructions; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][type]" value="<?php echo $field_type; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][isVisible]" value="<?php echo $field_isVisible; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][section]" value="<?php echo $field_section; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][verification_grade]" value="<?php echo $field_verification_grade; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][fieldver]" value="<?php echo $field_verified; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][field_mandate]" value="<?php echo $field_mandate; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][ids]" value="<?php echo $field_ids; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][items]" value="<?php echo $field_items; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][keyfields]" value="<?php echo $field_name; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][name]" value="<?php echo $field_label; ?>">
                                             </div>
                                             <div class="form-group"> 
                                                <?php if (
                                                   $field_verified ==
                                                   "yes"
                                                   ) { ?>
                                                <label class="d-block">File needs to be verified: <?php echo $field_verified; ?></label>
                                                <label class="d-block">
                                                <?php
                                                   $field_verification_grade_text =
                                                       "";
                                                   if (
                                                       $field_verification_grade ==
                                                       "G"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Government";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "F"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Financial";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "T"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Telecom";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "A"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Akcess";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "O"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Other";
                                                   }
                                                   ?>
                                                </label>
                                                <label class="d-block"><?php echo $field_verification_grade_text; ?></label>
                                                <?php } ?>
                                             </div>
                                          </div>
                                          <?php $field_check =
                                             "1"; ?>
                                          <?php }
                                             if (
                                                 $field_type ==
                                                     "string" ||
                                                 $field_type ==
                                                     "text"
                                             ) { ?>
                                          <div class="col-lg-6 <?php echo $field_ids; ?>">
                                             <div class="form-group">
                                                <label class="<?= isset($field_mandate) && $field_mandate == 'yes' ? 'required' : ''; ?>"><?php echo $field_label; ?></label>
                                                <a href="javascript:void(0);" class="btn-delete red-txt fielda" onclick="removeField('<?php echo $field_ids; ?>')"><i class="fa fa-trash"></i></a>
                                                <input type="text" class="form-control" data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_label; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][instructions]" value="<?php echo $field_label_instructions; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][type]" value="<?php echo $field_type; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][isVisible]" value="<?php echo $field_isVisible; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][section]" value="<?php echo $field_section; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][verification_grade]" value="<?php echo $field_verification_grade; ?>">                                                                                                                        
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][fieldver]" value="<?php echo $field_verified; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][field_mandate]" value="<?php echo $field_mandate; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][ids]" value="<?php echo $field_ids; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][items]" value="<?php echo $field_items; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][keyfields]" value="<?php echo $field_name; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][name]" value="<?php echo $field_label; ?>">
                                             </div>
                                             <div class="form-group"> 
                                                <?php if (
                                                   $field_verified ==
                                                   "yes"
                                                   ) { ?>
                                                <label class="d-block">File needs to be verified: <?php echo $field_verified; ?></label>
                                                <label class="d-block">
                                                <?php
                                                   $field_verification_grade_text =
                                                       "";
                                                   if (
                                                       $field_verification_grade ==
                                                       "G"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Government";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "F"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Financial";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "T"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Telecom";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "A"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Akcess";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "O"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Other";
                                                   }
                                                   ?>
                                                </label>
                                                <label class="d-block"><?php echo $field_verification_grade_text; ?></label>
                                                <?php } ?>
                                             </div>
                                          </div>
                                          <?php $field_check =
                                             "1"; ?>
                                          <?php }
                                             if (
                                                 $field_type ==
                                                     "address" ||
                                                 $field_type ==
                                                     "textarea"
                                             ) { ?>
                                          <div class="col-lg-6 <?php echo $field_ids; ?>">
                                             <div class="form-group">
                                                <label class="<?= isset($field_mandate) && $field_mandate == 'yes' ? 'required' : ''; ?>"><?php echo $field_label; ?></label>
                                                <a href="javascript:void(0);" class="btn-delete red-txt fielda" onclick="removeField('<?php echo $field_ids; ?>')"><i class="fa fa-trash"></i></a>
                                                <textarea rows="3" class="form-control" data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_name; ?>"></textarea>  
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][instructions]" value="<?php echo $field_label_instructions; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][type]" value="<?php echo $field_type; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][isVisible]" value="<?php echo $field_isVisible; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][section]" value="<?php echo $field_section; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][verification_grade]" value="<?php echo $field_verification_grade; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][fieldver]" value="<?php echo $field_verified; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][field_mandate]" value="<?php echo $field_mandate; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][ids]" value="<?php echo $field_ids; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][items]" value="<?php echo $field_items; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][keyfields]" value="<?php echo $field_name; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][name]" value="<?php echo $field_label; ?>">
                                             </div>
                                             <div class="form-group">
                                                <?php if (
                                                   $field_verified ==
                                                   "yes"
                                                   ) { ?>
                                                <label class="d-block">File needs to be verified : <?php echo $field_verified; ?></label>
                                                <label class="d-block">
                                                <?php
                                                   $field_verification_grade_text =
                                                       "";
                                                   if (
                                                       $field_verification_grade ==
                                                       "G"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Government";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "F"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Financial";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "T"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Telecom";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "A"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Akcess";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "O"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Other";
                                                   }
                                                   ?></label>
                                                <label class="d-block"><?php echo $field_verification_grade_text; ?></label>
                                                <?php } ?>
                                             </div>
                                          </div>
                                          <?php $field_check =
                                             "1"; ?>
                                          <?php }
                                             if (
                                                 $field_type ==
                                                     "list" ||
                                                 $field_type ==
                                                     "select"
                                             ) { ?>
                                          <div class="col-lg-6 <?php echo $field_ids; ?>">
                                             <div class="form-group">
                                                <label class="<?= isset($field_mandate) && $field_mandate == 'yes' ? 'required' : ''; ?>"><?php echo $field_label; ?></label>
                                                <a href="javascript:void(0);" class="btn-delete red-txt fielda" onclick="removeField('<?php echo $field_ids; ?>')"><i class="fa fa-trash"></i></a>
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
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][keyfields]" value="<?php echo $field_name; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][name]" value="<?php echo $field_label; ?>">
                                             </div>
                                             <div class="form-group"> 
                                                <?php if (
                                                   $field_verified ==
                                                   "yes"
                                                   ) { ?>
                                                <label class="d-block">File needs to be verified: <?php echo $field_verified; ?></label>
                                                <label class="d-block">
                                                <?php
                                                   $field_verification_grade_text =
                                                       "";
                                                   if (
                                                       $field_verification_grade ==
                                                       "G"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Government";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "F"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Financial";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "T"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Telecom";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "A"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Akcess";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "O"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Other";
                                                   }
                                                   ?>
                                                </label>
                                                <label class="d-block"><?php echo $field_verification_grade_text; ?></label>
                                                <?php } ?>
                                             </div>
                                          </div>
                                          <?php $field_check =
                                             "1";}
                                             if (
                                             $field_type ==
                                             "phone"
                                             ) { ?>
                                          <div class="col-lg-6 <?php echo $field_ids; ?>">
                                             <div class="form-group">
                                                <label class="<?= isset($field_mandate) && $field_mandate == 'yes' ? 'required' : ''; ?>"><?php echo $field_label; ?></label>
                                                <a href="javascript:void(0);" class="btn-delete red-txt fielda" onclick="removeField('<?php echo $field_ids; ?>')"><i class="fa fa-trash"></i></a>
                                                <input type="tel" class="form-control" data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_label; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][instructions]" value="<?php echo $field_label_instructions; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][type]" value="<?php echo $field_type; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][isVisible]" value="<?php echo $field_isVisible; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][section]" value="<?php echo $field_section; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][verification_grade]" value="<?php echo $field_verification_grade; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][fieldver]" value="<?php echo $field_verified; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][field_mandate]" value="<?php echo $field_mandate; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][ids]" value="<?php echo $field_ids; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][items]" value="<?php echo $field_items; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][keyfields]" value="<?php echo $field_name; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][name]" value="<?php echo $field_label; ?>">
                                             </div>
                                             <div class="form-group">
                                                <?php if (
                                                   $field_verified ==
                                                   "yes"
                                                   ) { ?>
                                                <label class="d-block">File needs to be verified: <?php echo $field_verified; ?></label>
                                                <label class="d-block">
                                                <?php
                                                   $field_verification_grade_text =
                                                       "";
                                                   if (
                                                       $field_verification_grade ==
                                                       "G"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Government";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "F"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Financial";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "T"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Telecom";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "A"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Akcess";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "O"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Other";
                                                   }
                                                   ?>
                                                </label>
                                                <label class="d-block"><?php echo $field_verification_grade_text; ?></label>
                                                <?php } ?>
                                             </div>
                                          </div>
                                          <?php $field_check =
                                             "1";}
                                             if (
                                             $field_type ==
                                             "number"
                                             ) { ?>
                                          <div class="col-lg-6 <?php echo $field_ids; ?>">
                                             <div class="form-group">
                                                <label class="<?= isset($field_mandate) && $field_mandate == 'yes' ? 'required' : ''; ?>"><?php echo $field_label; ?></label>
                                                <a href="javascript:void(0);" class="btn-delete red-txt fielda" onclick="removeField('<?php echo $field_ids; ?>')"><i class="fa fa-trash"></i></a>
                                                <input type="number" class="form-control" data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_label; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][instructions]" value="<?php echo $field_label_instructions; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][type]" value="<?php echo $field_type; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][isVisible]" value="<?php echo $field_isVisible; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][section]" value="<?php echo $field_section; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][verification_grade]" value="<?php echo $field_verification_grade; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][fieldver]" value="<?php echo $field_verified; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][field_mandate]" value="<?php echo $field_mandate; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][ids]" value="<?php echo $field_ids; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][items]" value="<?php echo $field_items; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][keyfields]" value="<?php echo $field_name; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][name]" value="<?php echo $field_label; ?>">
                                             </div>
                                             <div class="form-group"> 
                                                <?php if (
                                                   $field_verified ==
                                                   "yes"
                                                   ) { ?>
                                                <label>File needs to be verified: <?php echo $field_verified; ?></label>
                                                <?php
                                                   $field_verification_grade_text =
                                                       "";
                                                   if (
                                                       $field_verification_grade ==
                                                       "G"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Government";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "F"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Financial";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "T"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Telecom";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "A"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Akcess";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "O"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Other";
                                                   }
                                                   ?>
                                                <label class="d-block"><?php echo $field_verification_grade_text; ?></label>
                                                <?php } ?>
                                             </div>
                                          </div>
                                          <?php $field_check =
                                             "1";}
                                             if (
                                             $field_type ==
                                             "date"
                                             ) { ?>
                                          <div class="col-lg-6 <?php echo $field_ids; ?>">
                                             <div class="form-group">
                                                <label class="<?= isset($field_mandate) && $field_mandate == 'yes' ? 'required' : ''; ?>"><?php echo $field_label; ?></label>
                                                <a href="javascript:void(0);" class="btn-delete red-txt fielda" onclick="removeField('<?php echo $field_ids; ?>')"><i class="fa fa-trash"></i></a>        
                                                <input type="date" class="form-control" data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_label; ?> ( YYYY-MM-DD )">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][instructions]" value="<?php echo $field_label_instructions; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][type]" value="<?php echo $field_type; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][isVisible]" value="<?php echo $field_isVisible; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][section]" value="<?php echo $field_section; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][verification_grade]" value="<?php echo $field_verification_grade; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][fieldver]" value="<?php echo $field_verified; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][field_mandate]" value="<?php echo $field_mandate; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][ids]" value="<?php echo $field_ids; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][items]" value="<?php echo $field_items; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][keyfields]" value="<?php echo $field_name; ?>">
                                                <input type="hidden" name="field_name[<?php echo $field_name; ?>][name]" value="<?php echo $field_label; ?>">
                                             </div>
                                             <div class="form-group"> 
                                                <?php if (
                                                   $field_verified ==
                                                   "yes"
                                                   ) { ?>
                                                <label class="d-block">File needs to be verified: <?php echo $field_verified; ?></label>
                                                <label class="d-block">
                                                <?php
                                                   $field_verification_grade_text =
                                                       "";
                                                   if (
                                                       $field_verification_grade ==
                                                       "G"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Government";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "F"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Financial";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "T"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Telecom";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "A"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Akcess";
                                                   } elseif (
                                                       $field_verification_grade ==
                                                       "O"
                                                   ) {
                                                       $field_verification_grade_text =
                                                           "Verification grade: Other";
                                                   }
                                                   ?>
                                                </label>
                                                <label class="d-block"><?php echo $field_verification_grade_text; ?></label>
                                                <?php } ?>
                                             </div>
                                          </div>
                                          <?php $field_check =
                                             "1";}
                                             } ?>
                                       </div>
                                    </div>
                                    <hr>
                                    <div class="form-actions text-right">
                                       <button type="button" class="btn waves-effect waves-light btn-success previous-btn mx-1">Previous</button>
                                       <button type="submit" onclick="saveEform();" class="btn waves-effect waves-light btn-primary mx-1">Save</button>
                                       <button type="button" class="btn waves-effect waves-light btn-inverse mx-1 reset-filed-eform-btn">Reset</button>
                                       <button type="button" onclick="previewForm();" class="btn waves-effect waves-light btn-success next-btn">Next</button>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <!-- Select Fields large screen-->
                           <div class="col-lg-4 d-none d-lg-block pl-lg-0">
                              <div class="card">
                                 <input type="hidden" class="field_check" value="<?php echo $field_check; ?>">                                        
                                 <div class="card-body" id="getfield"></div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                 
                        <div class="tab-pane " id="eform-5" role="tabpanel">
                     <div class="p-20">
                        <div class="row">
                           <div class="col-md-12">
                              <div class="form-group row">
                                 <label class="control-label col-md-3">Require approval ? </label>
                                 <div class="col-md-9 d-flex align-items-center">
                                    <?php
                                       $is_approval_yes = "";
                                       $is_approval_no = "";
                                       if ( $eform->is_approval == 1) {
                                           $is_approval_yes = "checked";
                                       } elseif ( $eform->is_approval == 0) {
                                           $is_approval_no = "checked";
                                       }
                                       ?>
                                    <label class="custom-control custom-radio mr-5">
                                    <input id="process_yes" name="process" type="radio" class="custom-control-input process" value="yes" <?php echo $is_approval_yes; ?>>
                                    <span class="custom-control-label">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio">
                                    <input id="process_no" name="process" type="radio" class="custom-control-input process" value="no" <?php echo $is_approval_no; ?>>
                                    <span class="custom-control-label">No</span>
                                    </label>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="div_process_wrapper">
                            <?php $count = !empty($eform_approval) ? count($eform_approval) : 1 ; ?>
                            <?php $count_custom = !empty($eform_approval) ? count($eform_approval) : 0 ; ?>
                            <div class="form-group process_wrapper"  id="count_check_approval" data-attr="<?php echo $count; ?>">
                                <?php 
                                if (!empty($eform_approval)) {
                                    
                                    foreach ($eform_approval as $key => $value) { ?>
                                        <div class="row remove_all remove-<?php echo $key; ?>">
                                            <?php 
                                            $main_key_value = $key;
                                            $key_value = $key;
                                            
                                            if($key == 1) {
                                                $label_input = 'First';
                                            } else if($key == 2) {
                                                $label_input = 'Second';
                                            } else if($key == 3) {
                                                $label_input = 'Third';
                                            } else if($key == 4) {
                                                $label_input = 'Forth';
                                            }
                                            $approval_akcess_id = $value['approval_akcess_id'];
                                           
                                            $approval_array = [];
                                            foreach($value['approve'] as $value_approval) {
                                                $approval_array[] = $value_approval['is_notify_akcess_id'];
                                            }

                                            $reject_array = [];
                                            foreach($value['approve'] as $value_reject) {
                                                $reject_array[] = $value_reject['is_notify_akcess_id'];
                                            }

                                            $both_array = [];
                                            foreach($value['approve'] as $value_both) {
                                                $both_array[] = $value_both['is_notify_akcess_id'];
                                            }
                                            
                                            ?>
                           <div class="col-md-4">
                              <div class="form-group" id="" >
                                                    <label for="process"><?php echo $label_input; ?> Approval <i data-toggle="tooltip" data-placement="top" title="This AKcess ID will approve or reject the eForm response. If approved, it will be sent to the next AKcess ID if there is any." class="fa fa-info-circle" aria-hidden="true"></i></label>
                                                    <input type="text" name="field[<?php echo $key_value; ?>][process]" class="form-control" id="process" placeholder="Enter AKcess ID" value="<?php echo $approval_akcess_id; ?>">
                             
                              </div>
                            </div>
                            <div class="col-md-6">
                                                <div class="form-group">
                                <label for="process">Notify If <i data-toggle="tooltip" data-placement="top" title="These IDs will be notified on the application, about this specific action taken on the response.
The eform response’s owner will be notified about all the actions taken." class="fa fa-info-circle" aria-hidden="true"></i></label>
                                                    <?php
                                                        $approve_selected = isset($approval_array[0]) ? "selected" : "";
                                                        $approve_display = isset($approval_array[0]) ? "display:block" : "display:none";
                                                    ?>
                                                    <select class="form-control fields_approve_section" onchange="changeApproveRejectedStatus(<?php echo $key_value; ?>);" id="field_<?php echo $key_value; ?>" name="field[<?php echo $key_value; ?>][notify]">
                                                        <option value="">Select Any</option>
                                                        <option <?php echo $approve_selected; ?> value="approve">Approved</option>
                                                        <option value="reject">Rejected</option>
                                                        <option value="both">Approved/Rejected</option>
                                </select>
                                </div>

                                <div class="clearfix"></div>
                                                <select class="form-control select2 approve-box" multiple="multiple" id="akcessID_approve_<?php echo $key_value; ?>" name="field[<?php echo $key_value; ?>][notify_approve][]" style="<?php echo $approve_display; ?>">
                                    <?php
                                       foreach ($users_process as $user) {
                                                            if (isset($user->akcessId) && $user->akcessId != "") {
                                                   $akcessId = $user->akcessId;
                                               $name = $user->name ." ( " .$user->akcessId ." ) [".$user->usertype."]";
                                                                                               if (isset($akcessId)) {
                                                                    $selectd = '';
                                                                    if (in_array($user->id, $approval_array)) {
                                                                        $selectd = 'selected';
                                                                    }
                                                                    ?>
                                                                    <option <?php echo $selectd; ?> value="<?php echo $user->id; ?>">
                                                                        <?php echo $name; ?>
                                                                    </option>
                                                                    <?php
                                                                }
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                       
                                                <select class="form-control select2 approve-box" multiple="multiple" id="akcessID_reject_<?php echo $key_value; ?>" name="field[<?php echo $key_value; ?>][notify_reject][]" style="display:none;">
                                                    <?php
                                                    foreach ($users_process as $user) {
                                                        if (isset($user->akcessId) && $user->akcessId != "") {
                                                            $akcessId = $user->akcessId;
                                                            $name = $user->name ." ( " .$user->akcessId ." ) [".$user->usertype."]";
                                                            if (isset($akcessId)) {
                                                                                                   $selectd = '';
                                                                if (in_array($user->id, $reject_array)) {
                                                                                                       $selectd = 'selected';
                                                                                                   }
                                                                                                   ?>
                                                        <option <?php echo $selectd; ?> value="<?php echo $user->id; ?>">
                                                            <?php echo $name; ?>
                                                        </option>
                                    <?php
                                       }
                                       }
                                       }
                                       ?>
                                 </select>

                                                <select class="form-control select2 approve-box" multiple="multiple" id="akcessID_both_<?php echo $key_value; ?>" name="field[<?php echo $key_value; ?>][notify_both][]" style="display:none;">
                                                    <?php
                                                    foreach ($users_process as $user) {
                                                        if (isset($user->akcessId) && $user->akcessId != "") {
                                                            $akcessId = $user->akcessId;
                                                            $name = $user->name ." ( " .$user->akcessId ." ) [".$user->usertype."]";
                                                            if (isset($akcessId)) {
                                                                $selectd = '';
                                                                if (in_array($user->id, $both_array)) {
                                                                    $selectd = 'selected';
                                                                }
                                                                ?>
                                                                <option <?php echo $selectd; ?> value="<?php echo $user->id; ?>">
                                                                    <?php echo $name; ?>
                                                                </option>
                                                    <?php
                                                                }
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <?php if($key > 1) { ?>
                                                <div class="clearfix"></div>
                                                <div class="form-group" id="">
                                                    <label></label>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn waves-effect waves-light btn-danger" onclick="process_remove_button(<?php echo $key; ?>)">
                                                        <i class="fa fa-minus"></i>
                                                        </button>
                            </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                <?php 
                                    }
                                } else { ?>
                                    <div class="row ">
                                        <div class="col-md-4">
                                            <div class="form-group" id="" >
                                                <label for="process">First Approval <i data-toggle="tooltip" data-placement="top" title="This AKcess ID will approve or reject the eForm response. If approved, it will be sent to the next AKcess ID if there is any." class="fa fa-info-circle" aria-hidden="true"></i></label>
                                                <input type="text" name="field[0][process]" class="form-control" id="process" placeholder="Enter AKcess ID">
                                            
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="process">Notify If <i data-toggle="tooltip" data-placement="top" title="These IDs will be notified on the application, about this specific action taken on the response.
                                                The eform response’s owner will be notified about all the actions taken." class="fa fa-info-circle" aria-hidden="true"></i></label>
                                                    
                                                <select class="form-control fields_approve_section" onchange="changeApproveRejectedStatus(0);" id="field_0" name="field[0][notify]">
                                                    <option value="">Select Any</option>
                                                    <option value="approve">Approved</option>
                                                    <option value="reject">Rejected</option>
                                                    <option value="both">Both</option>
                                                </select>
                                            </div>
                                            <div class="clearfix"></div>
                                            <select class="custom-select select2 approve-box" multiple="multiple" id="akcessID_approve_0" name="field[0][notify_approve][]" style="display:none;">
                                                                                <?php
                                                foreach ($users_process as $user) {
                                                    if (isset($user->akcessId) && $user->akcessId != "") {
                                                        $akcessId = $user->akcessId;
                                                        $name = $user->name ." ( " .$user->akcessId ." ) [".$user->usertype."]";
                                                        if (isset($akcessId)) {
                                                            $selectd = '';
                                                            ?>
                                                            <option <?php echo $selectd; ?> value="<?php echo $user->id; ?>">
                                                                <?php echo $name; ?>
                                                            </option>
                                                <?php
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </select>

                                            <select class="custom-select select2 approve-box" multiple="multiple" id="akcessID_reject_0" name="field[0][notify_reject][]" style="display:none;">
                                                <?php
                                                foreach ($users_process as $user) {
                                                    if (isset($user->akcessId) && $user->akcessId != "") {
                                                        $akcessId = $user->akcessId;
                                                        $name = $user->name ." ( " .$user->akcessId ." ) [".$user->usertype."]";
                                                        if (isset($akcessId)) {
                                                            $selectd = '';
                                                            ?>
                                                    <option <?php echo $selectd; ?> value="<?php echo $user->id; ?>">
                                                        <?php echo $name; ?>
                                                    </option>
                                                <?php
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>
   
                                            <select class="custom-select select2 approve-box" multiple="multiple" id="akcessID_both_0" name="field[0][notify_both][]" style="display:none;">
                                                <?php
                                                foreach ($users_process as $user) {
                                                    if (isset($user->akcessId) && $user->akcessId != "") {
                                                        $akcessId = $user->akcessId;
                                                        $name = $user->name ." ( " .$user->akcessId ." ) [".$user->usertype."]";
                                                        if (isset($akcessId)) {
                                                            $selectd = '';
                                                            ?>
                                                            <option <?php echo $selectd; ?> value="<?php echo $user->id; ?>">
                                                                <?php echo $name; ?>
                                                            </option>
                                                <?php
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </select>
                        </div>
                        </div>
                                <?php } ?>
                            </div>
                        <div class="row">
                           <div class="col-md-2">
                           <div class="actions d-flex align-items-center justify-content-around">
    <button type="button" class="btn waves-effect waves-light btn-info" onclick="process_add_button()" ><i class="fa fa-plus"></i> Add approval</a>
</div> 
                           </div>
                        </div>
                        <div class="clearfix"></div><br/>
                        <div class="form-group">
                        <div class="row">
                        <div class="col-md-12">
                              <div class="form-group row">
                                 <label class="control-label col-md-3">Send eForm response in PDF view ? <i data-toggle="tooltip" data-placement="top" title="After the process is completed, send the eForm response in PDF view by email and on the application." class="fa fa-info-circle" aria-hidden="true"></i></label>
                                 <div class="col-md-9 d-flex align-items-center">
                                    <?php
                                        
                                    $eform_response_process_pdf = !empty($eform->eform_response_process_pdf) ? json_decode($eform->eform_response_process_pdf) : "";
                                       $processpdf_yes = "";
                                       $processpdf_no = "";
                                       if ( 
                                           $eform->isProcesspdf ==
                                           "yes"
                                       ) {
                                           $processpdf_yes =
                                               "checked";
                                       } elseif (
                                           $eform->isProcesspdf ==
                                           "no"
                                       ) {
                                           $processpdf_no =
                                               "checked";
                                       }
                                       ?>
                                    <label class="custom-control custom-radio mr-5">
                                    <input id="processpdf_yes" name="processpdf" type="radio" class="custom-control-input processpdf" value="yes" <?php echo $processpdf_yes; ?>>
                                    <span class="custom-control-label">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio">
                                    <input id="processpdf_no" name="processpdf" type="radio" class="custom-control-input processpdf" value="no" <?php echo $processpdf_no; ?>>
                                    <span class="custom-control-label">No</span>
                                    </label>
                                    <div class="clearfix"></div>
                                    <div class="col-md-7 div_process_wrapper_pdf">
                                        <select class="form-control select2 eform_response_process_pdf" multiple="multiple" name="eform_response_process_pdf[]" >
                                    <?php
                                       foreach ($users_process as $user) {
                                                    if (  isset($user->akcessId) &&  $user->akcessId != ""  ) {
                                                   $akcessId = $user->akcessId;
                                               $name = $user->name ." ( " .$user->akcessId ." ) [".$user->usertype."]";
                                                                                               if (isset($akcessId)) {
                                       
                                                                                                   $selectd = '';
                                                            if (!empty($eform_response_process_pdf) &&  in_array( $user->id, $eform_response_process_pdf )) {
                                                                                                       $selectd = 'selected';
                                                                                                   }
                                                                                                   ?>
                                                                <option <?php echo $selectd; ?> value="<?php echo $user->id; ?>"><?php echo $name; ?></option>
                                    <?php
                                       }
                                       }
                                       }
                                       ?>
                                 </select>
                                 </div>
                                 </div>
                                 
                              </div>
                           </div>
                        </div>
                        </div>
                        </div>
                        <hr>
                        <div class="form-actions text-right">
                           <button type="button" class="btn waves-effect waves-light btn-success previous-btn mx-1">Previous</button>
                           <button type="button" onclick="previewForm();" class="btn waves-effect waves-light btn-success next-btn mx-1">Next</button>
                        </div>
                        <div class="clearfix"></div>
                     </div>
                  </div>

                  <div class="tab-pane " id="eform-3" role="tabpanel">
                     <div class="p-20">
                        <div class="row">
                           <div class="col-md-12">
                              <div class="form-group row">
                                 <label class="control-label col-md-3">Additional Notification ? <i data-toggle="tooltip" data-placement="top" title="The selected users will be notified on AKcess app and   by email when this eForm is submitted." class="fa fa-info-circle" aria-hidden="true"></i></label>
                                 <div class="col-md-9 d-flex align-items-center">
                                    <?php
                                       $additional_notification_yes = "";
                                       $additional_notification_no = "";
                                       if (
                                           $eform->isAdditionalNotification ==
                                           "yes"
                                       ) {
                                           $additional_notification_yes =
                                               "checked";
                                       } elseif (
                                           $eform->isAdditionalNotification ==
                                           "yes"
                                       ) {
                                           $additional_notification_no =
                                               "checked";
                                       }
                                       ?>
                                    <label class="custom-control custom-radio mr-5">
                                    <input id="additional_notification_yes" name="additional_notification" type="radio" class="custom-control-input additional_notification" value="yes" <?php echo $additional_notification_yes; ?>>
                                    <span class="custom-control-label">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio">
                                    <input id="additional_notification_no" name="additional_notification" type="radio" class="custom-control-input additional_notification" checked value="no" <?php echo $additional_notification_no; ?>>
                                    <span class="custom-control-label">No</span>
                                    </label>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-12">
                              <div class="form-group" id="form_multiple_akcessID" >
                                 <select class="form-control" multiple="multiple" name="akcessID[]" >
                                    <?php
                                       foreach ($users as $user) {
                                                                                           if (
                                                   isset($user['akcessId']) &&
                                                                                                   $user['akcessId'] != ""
                                                                                           ) {
                                                                                               $akcessId = $user['akcessId'];
                                                                                               $name = $user['name'] .
                                                                                                       " ( " .
                                                                                                       $user['akcessId'] .
                                                                                                       " ) ";
                                                                                               if (isset($akcessId)) {
                                       
                                                                                                   $selectd = '';
                                                                                                   if (
                                                                                                           in_array(
                                                                                                                   $akcessId, $dataAdditional
                                                                                                           )
                                                                                                   ) {
                                                                                                       $selectd = 'selected';
                                                                                                   }
                                                                                                   ?>
                                    <option <?php echo $selectd; ?> value="<?php echo $user['akcessId']; ?>"><?php echo $name; ?></option>
                                    <?php
                                       }
                                       }
                                       }
                                       ?>
                                 </select>
                              </div>
                           </div>
                        </div>
                        <hr>
                        <div class="form-actions text-right">
                           <button type="button" class="btn waves-effect waves-light btn-success previous-btn mx-1">Previous</button>
                           <button type="button" onclick="previewForm();" class="btn waves-effect waves-light btn-success next-btn mx-1">Next</button>
                        </div>
                        <div class="clearfix"></div>
                     </div>
                  </div>
                  <div class="tab-pane" id="eform-4" role="tabpanel">
                     <div class="p-20">
                        <div id="notification_get">
                           <div class="input-group" id="addfield">
                              <?php foreach ($fields as $n) {
                                 $field_check = "0";
                                 $field_ids = $n->id;
                                 $field_name = $n->keyfields;
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
                                 if ($field_type == "file") { ?>
                              <div class="col-lg-6 <?php echo $field_ids; ?>">
                                 <div class="form-group">
                                    <label class="<?= isset($field_mandate) && $field_mandate == 'yes' ? 'required' : ''; ?>"><?php echo $field_label; ?></label>
                                    <input type="file" 
                                       class="form-control custom-file-input custom-file-label"
                                       >
                                    <div class="custom-file">
                                       <input type="file" class="custom-file-input"
                                          data-instructions="<?php echo $field_label_instructions; ?>" 
                                          data-name="<?php echo $field_name; ?>" 
                                          data-type="<?php echo $field_type; ?>" 
                                          data-isVisible="<?php echo $field_isVisible; ?>" 
                                          data-section="<?php echo $field_section; ?>" 
                                          data-verification-grade="<?php echo $field_verification_grade; ?>" 
                                          data-fieldver="<?php echo $field_verified; ?>" 
                                          data-field_mandate="<?php echo $field_mandate; ?>" 
                                          id="field_<?php echo $field_name; ?>" 
                                          name="field_name[<?php echo $field_name; ?>][]" 
                                          data-ids="<?php echo $field_ids; ?>" 
                                          data-items="<?php echo $field_items; ?>"
                                          >
                                       <label class="custom-file-label" for="field_<?php echo $field_name; ?>">Choose file</label>
                                    </div>
                                 </div>
                                 <div class="form-group">
                                    <?php if (
                                       $field_mandate ==
                                       "yes"
                                       ) { ?>
                                    <label class="form-label eformLabel">Signature required: <?php echo $field_mandate; ?></label>
                                    <?php } ?>
                                    <?php if (
                                       $field_verified ==
                                       "yes"
                                       ) { ?>
                                    <label class="form-label eformLabel">File needs to be verified: <?php echo $field_verified; ?></label>
                                    <?php
                                       $field_verification_grade_text =
                                           "";
                                       if (
                                           $field_verification_grade ==
                                           "G"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Government";
                                       } elseif (
                                           $field_verification_grade ==
                                           "F"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Financial";
                                       } elseif (
                                           $field_verification_grade ==
                                           "T"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Telecom";
                                       } elseif (
                                           $field_verification_grade ==
                                           "A"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Akcess";
                                       } elseif (
                                           $field_verification_grade ==
                                           "O"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Other";
                                       }
                                       ?>
                                    <label class="form-label eformLabel"><?php echo $field_verification_grade_text; ?></label>
                                    <?php } ?>
                                 </div>
                              </div>
                              <?php }
                                 if (
                                     $field_type == "string" ||
                                     $field_type == "text"
                                 ) { ?>
                              <div class="col-lg-6 <?php echo $field_ids; ?>">
                                 <div class="form-group">
                                    <label class="<?= isset($field_mandate) && $field_mandate == 'yes' ? 'required' : ''; ?>"><?php echo $field_label; ?></label>
                                    <input type="text" data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_label; ?>" class="form-control">
                                 </div>
                                 <div class="form-group">
                                    <?php if (
                                       $field_verified ==
                                       "yes"
                                       ) { ?>
                                    <label class="form-label eformLabel">File needs to be verified: <?php echo $field_verified; ?></label>
                                    <?php
                                       $field_verification_grade_text =
                                           "";
                                       if (
                                           $field_verification_grade ==
                                           "G"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Government";
                                       } elseif (
                                           $field_verification_grade ==
                                           "F"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Financial";
                                       } elseif (
                                           $field_verification_grade ==
                                           "T"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Telecom";
                                       } elseif (
                                           $field_verification_grade ==
                                           "A"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Akcess";
                                       } elseif (
                                           $field_verification_grade ==
                                           "O"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Other";
                                       }
                                       ?>
                                    <label class="form-label eformLabel"><?php echo $field_verification_grade_text; ?></label>
                                    <?php } ?>
                                 </div>
                              </div>
                              <?php }
                                 if (
                                     $field_type == "address" ||
                                     $field_type == "textarea"
                                 ) { ?>
                              <div class="col-lg-6 <?php echo $field_ids; ?>">
                                 <div class="form-group">
                                    <label class="<?= isset($field_mandate) && $field_mandate == 'yes' ? 'required' : ''; ?>"><?php echo $field_label; ?></label>
                                    <textarea data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_name; ?>" col="3" class="form-control"></textarea>  
                                 </div>
                                 <div class="form-group">
                                    <?php if (
                                       $field_verified ==
                                       "yes"
                                       ) { ?>
                                    <label class="form-label eformLabel">File needs to be verified: <?php echo $field_verified; ?></label>
                                    <?php
                                       $field_verification_grade_text =
                                           "";
                                       if (
                                           $field_verification_grade ==
                                           "G"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Government";
                                       } elseif (
                                           $field_verification_grade ==
                                           "F"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Financial";
                                       } elseif (
                                           $field_verification_grade ==
                                           "T"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Telecom";
                                       } elseif (
                                           $field_verification_grade ==
                                           "A"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Akcess";
                                       } elseif (
                                           $field_verification_grade ==
                                           "O"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Other";
                                       }
                                       ?>
                                    <label class="form-label eformLabel"><?php echo $field_verification_grade_text; ?></label>
                                    <?php } ?>
                                 </div>
                              </div>
                              <?php }
                                 if ($field_type == "radio") { ?>
                              <div class="col-lg-6 <?php echo $field_ids; ?>">
                                 <div class="form-group">
                                    <label class="<?= isset($field_mandate) && $field_mandate == 'yes' ? 'required' : ''; ?>"><?php echo $field_label; ?></label>
                                    <a href="javascript:void(0);" class="btn-delete fielda" onclick="removeField('<?php echo $field_ids; ?>')"><i class="fa fa-trash"></i></a>
                                    <?php
                                       $explode = explode(
                                           ",",
                                           $field_items
                                       );
                                       
                                       foreach (
                                           $explode
                                           as $key =>
                                               $value
                                       ) { ?>
                                    <div class="form-check form-check-radio">
                                       <label class="form-check-label">
                                       <input class="form-check-input" type="radio"
                                          data-instructions="<?php echo $field_label_instructions; ?>" 
                                          data-name="<?php echo $field_name; ?>" 
                                          data-type="<?php echo $field_type; ?>" 
                                          data-isVisible="<?php echo $field_isVisible; ?>" 
                                          data-section="<?php echo $field_section; ?>" 
                                          data-verification-grade="<?php echo $field_verification_grade; ?>" 
                                          data-fieldver="<?php echo $field_verified; ?>" 
                                          data-field_mandate="<?php echo $field_mandate; ?>" 
                                          id="field_<?php echo $field_name; ?>" 
                                          name="field_name[<?php echo $field_name; ?>][]" 
                                          data-ids="<?php echo $field_ids; ?>" 
                                          data-items="<?php echo $value; ?>" 
                                          placeholder="<?php echo $field_label; ?>"
                                          />
                                       <?php echo $value; ?> 
                                       <span class="circle">
                                       <span class="check"></span>
                                       </span>
                                       </label>
                                    </div>
                                    <?php }
                                       ?>
                                 </div>
                                 <div class="form-group"> 
                                    <?php if (
                                       $field_verified ==
                                       "yes"
                                       ) { ?>
                                    <label class="d-block">File needs to be verified: <?php echo $field_verified; ?></label>
                                    <label class="d-block">
                                    <?php
                                       $field_verification_grade_text =
                                           "";
                                       if (
                                           $field_verification_grade ==
                                           "G"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Government";
                                       } elseif (
                                           $field_verification_grade ==
                                           "F"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Financial";
                                       } elseif (
                                           $field_verification_grade ==
                                           "T"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Telecom";
                                       } elseif (
                                           $field_verification_grade ==
                                           "A"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Akcess";
                                       } elseif (
                                           $field_verification_grade ==
                                           "O"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Other";
                                       }
                                       ?>
                                    </label>
                                    <label class="d-block"><?php echo $field_verification_grade_text; ?></label>
                                    <?php } ?>
                                 </div>
                              </div>
                              <?php }
                                 if (
                                     $field_type == "checkbox"
                                 ) { ?>
                              <div class="col-lg-6 <?php echo $field_ids; ?>">
                                 <div class="form-group">
                                    <label class="<?= isset($field_mandate) && $field_mandate == 'yes' ? 'required' : ''; ?>"><?php echo $field_label; ?></label>
                                    <a href="javascript:void(0);" class="btn-delete fielda" onclick="removeField('<?php echo $field_ids; ?>')"><i class="fa fa-trash"></i></a>
                                    <?php
                                       $explode = explode(
                                           ",",
                                           $field_items
                                       );
                                       
                                       foreach (
                                           $explode
                                           as $key =>
                                               $value
                                       ) { ?>
                                    <div class="form-check">
                                       <label class="form-check-label">
                                       <input class="form-check-input" type="checkbox" 
                                          data-instructions="<?php echo $field_label_instructions; ?>" 
                                          data-name="<?php echo $field_name; ?>" 
                                          data-type="<?php echo $field_type; ?>" 
                                          data-isVisible="<?php echo $field_isVisible; ?>" 
                                          data-section="<?php echo $field_section; ?>" 
                                          data-verification-grade="<?php echo $field_verification_grade; ?>" 
                                          data-fieldver="<?php echo $field_verified; ?>" 
                                          data-field_mandate="<?php echo $field_mandate; ?>" 
                                          id="field_<?php echo $field_name; ?>" 
                                          name="field_name[<?php echo $field_name; ?>][]" 
                                          data-ids="<?php echo $field_ids; ?>" 
                                          data-items="<?php echo $value; ?>" 
                                          placeholder="<?php echo $field_label; ?>" 
                                          />
                                       <?php echo $value; ?>
                                       <span class="form-check-sign">
                                       <span class="check"></span>
                                       </span>
                                       </label>
                                    </div>
                                    <?php }
                                       ?>
                                 </div>
                                 <div class="form-group"> 
                                    <?php if (
                                       $field_verified ==
                                       "yes"
                                       ) { ?>
                                    <label class="d-block">File needs to be verified: <?php echo $field_verified; ?></label>
                                    <label class="d-block">
                                    <?php
                                       $field_verification_grade_text =
                                           "";
                                       if (
                                           $field_verification_grade ==
                                           "G"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Government";
                                       } elseif (
                                           $field_verification_grade ==
                                           "F"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Financial";
                                       } elseif (
                                           $field_verification_grade ==
                                           "T"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Telecom";
                                       } elseif (
                                           $field_verification_grade ==
                                           "A"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Akcess";
                                       } elseif (
                                           $field_verification_grade ==
                                           "O"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Other";
                                       }
                                       ?>
                                    </label>
                                    <label class="d-block"><?php echo $field_verification_grade_text; ?></label>
                                    <?php } ?>
                                 </div>
                              </div>
                              <?php }
                                 if ($field_type == "list") { ?>
                              <div class="col-lg-6 <?php echo $field_ids; ?>">
                                 <div class="form-group">
                                    <label class="<?= isset($field_mandate) && $field_mandate == 'yes' ? 'required' : ''; ?>"><?php echo $field_label; ?></label>
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
                                 <div class="form-group">
                                    <?php if (
                                       $field_verified ==
                                       "yes"
                                       ) { ?>
                                    <label>File needs to be verified: <?php echo $field_verified; ?></label>
                                    <?php
                                       $field_verification_grade_text =
                                           "";
                                       if (
                                           $field_verification_grade ==
                                           "G"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Government";
                                       } elseif (
                                           $field_verification_grade ==
                                           "F"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Financial";
                                       } elseif (
                                           $field_verification_grade ==
                                           "T"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Telecom";
                                       } elseif (
                                           $field_verification_grade ==
                                           "A"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Akcess";
                                       } elseif (
                                           $field_verification_grade ==
                                           "O"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Other";
                                       }
                                       ?>
                                    <label><?php echo $field_verification_grade_text; ?></label>
                                    <?php } ?>
                                 </div>
                              </div>
                              <?php }
                                 if ($field_type == "phone") { ?>
                              <div class="col-lg-6 <?php echo $field_ids; ?>">
                                 <div class="form-group">
                                    <label class="<?= isset($field_mandate) && $field_mandate == 'yes' ? 'required' : ''; ?>"><?php echo $field_label; ?></label>
                                    <input type="tel" data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_label; ?>" class="form-control">
                                 </div>
                                 <div class="form-group">
                                    <?php if (
                                       $field_verified ==
                                       "yes"
                                       ) { ?>
                                    <label class="form-label eformLabel">File needs to be verified: <?php echo $field_verified; ?></label>
                                    <?php
                                       $field_verification_grade_text =
                                           "";
                                       if (
                                           $field_verification_grade ==
                                           "G"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Government";
                                       } elseif (
                                           $field_verification_grade ==
                                           "F"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Financial";
                                       } elseif (
                                           $field_verification_grade ==
                                           "T"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Telecom";
                                       } elseif (
                                           $field_verification_grade ==
                                           "A"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Akcess";
                                       } elseif (
                                           $field_verification_grade ==
                                           "O"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Other";
                                       }
                                       ?>
                                    <label class="form-label eformLabel"><?php echo $field_verification_grade_text; ?></label>
                                    <?php } ?>
                                 </div>
                              </div>
                              <?php }
                                 if (
                                     $field_type == "number"
                                 ) { ?>
                              <div class="col-lg-6 <?php echo $field_ids; ?>">
                                 <div class="form-group">
                                    <label class="<?= isset($field_mandate) && $field_mandate == 'yes' ? 'required' : ''; ?>"><?php echo $field_label; ?></label>
                                    <input type="number" data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_label; ?>" class="form-control">
                                 </div>
                                 <div class="form-group">
                                    <?php if (
                                       $field_verified ==
                                       "yes"
                                       ) { ?>
                                    <label class="form-label eformLabel">File needs to be verified: <?php echo $field_verified; ?></label>
                                    <?php
                                       $field_verification_grade_text =
                                           "";
                                       if (
                                           $field_verification_grade ==
                                           "G"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Government";
                                       } elseif (
                                           $field_verification_grade ==
                                           "F"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Financial";
                                       } elseif (
                                           $field_verification_grade ==
                                           "T"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Telecom";
                                       } elseif (
                                           $field_verification_grade ==
                                           "A"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Akcess";
                                       } elseif (
                                           $field_verification_grade ==
                                           "O"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Other";
                                       }
                                       ?>
                                    <label class="form-label "><?php echo $field_verification_grade_text; ?></label>
                                    <?php } ?>
                                 </div>
                              </div>
                              <?php }
                                 if ($field_type == "date") { ?>
                              <div class="col-lg-6 <?php echo $field_ids; ?>">
                                 <div class="form-group">
                                    <label class="<?= isset($field_mandate) && $field_mandate == 'yes' ? 'required' : ''; ?>"><?php echo $field_label; ?></label>
                                    <input type="date" data-instructions="<?php echo $field_label_instructions; ?>" data-name="<?php echo $field_name; ?>" data-type="<?php echo $field_type; ?>" data-isVisible="<?php echo $field_isVisible; ?>" data-section="<?php echo $field_section; ?>" data-verification-grade="<?php echo $field_verification_grade; ?>" data-fieldver="<?php echo $field_verified; ?>" data-field_mandate="<?php echo $field_mandate; ?>" id="field_<?php echo $field_name; ?>" name="field_name[<?php echo $field_name; ?>][]" data-ids="<?php echo $field_ids; ?>" data-items="<?php echo $field_items; ?>" placeholder="<?php echo $field_label; ?> ( YYYY-MM-DD )" class="form-control">
                                 </div>
                                 <div class="form-group">
                                    <?php if (
                                       $field_verified ==
                                       "yes"
                                       ) { ?>
                                    <label class="form-label eformLabel">File needs to be verified: <?php echo $field_verified; ?></label>
                                    <?php
                                       $field_verification_grade_text =
                                           "";
                                       if (
                                           $field_verification_grade ==
                                           "G"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Government";
                                       } elseif (
                                           $field_verification_grade ==
                                           "F"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Financial";
                                       } elseif (
                                           $field_verification_grade ==
                                           "T"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Telecom";
                                       } elseif (
                                           $field_verification_grade ==
                                           "A"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Akcess";
                                       } elseif (
                                           $field_verification_grade ==
                                           "O"
                                       ) {
                                           $field_verification_grade_text =
                                               "Verification grade: Other";
                                       }
                                       ?>
                                    <label class="form-label eformLabel"><?php echo $field_verification_grade_text; ?></label>
                                    <?php } ?>
                                 </div>
                              </div>
                              <?php }
                                 } ?>
                           </div>
                        </div>
                        <hr>
                        <div class="form-actions text-right">
                           <button type="button" class="btn waves-effect waves-light btn-success previous-btn mx-1">Previous</button>
                           <button type="submit" onclick="editEform();" class="btn waves-effect waves-light btn-primary">Save</button>                              
                        </div>
                     </div>
                  </div>
               </div>
               <?= $this->Form->end() ?>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- Create New Field Modal -->
<div class="modal " id="eFormCreateFieldModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel_create" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel_create">Create new field</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
         </div>
         <form class="mb-0" role="form" action="" id="addFieldForm_create" method="POST">
            <div class="modal-body">
               <div class="form-group">
                  <label class="required">Label Name</label>
                  <input type="text" class="form-control" id="field_label_create" >
               </div>
               <div class="form-group">
                  <label>Instructions</label>
                  <textarea class="form-control" id="field_label_instructions_create" rows="3"></textarea>
               </div>
               <div class="form-group">
                  <label class="required">Field Type</label>
                  <select class="custom-select" id="field_type_create">
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
                  <label>Field Options</label>
                  <div id="newTagDiv">
                     <input type="text" id="tokenize" placeholder="Enter New Label (A-Z,a-z,0-9,_)" class="form-control input_tokenize">
                  </div>
               </div>
               <div class="form-group row">
                  <label class="control-label col-md-6">Is the field mandatory?</label>
                  <div class="col-md-6 d-flex align-items-center">                                    
                     <label class="custom-control custom-radio mr-3">
                     <input id="field_mandate_yes_create" name="field_mandate_yes" type="radio" class="custom-control-input" value="yes">
                     <span class="custom-control-label">Yes</span>
                     </label>
                     <label class="custom-control custom-radio">
                     <input id="field_mandate_no_create" name="field_mandate_no" type="radio" class="custom-control-input" value="no" checked>
                     <span class="custom-control-label">No</span>
                     </label>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="control-label col-md-6">Field must be verified?</label>
                  <div class="col-md-6 d-flex align-items-center">                                    
                     <label class="custom-control custom-radio mr-3">
                     <input id="fieldver_yes_create" name="fieldver_yes" type="radio" class="custom-control-input fieldver_create" value="yes">
                     <span class="custom-control-label">Yes</span>
                     </label>
                     <label class="custom-control custom-radio">
                     <input id="fieldver_no_create" name="fieldver_no" type="radio" class="custom-control-input fieldver_create" value="no" checked>
                     <span class="custom-control-label">No</span>
                     </label>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="control-label col-md-6">Do you want to store this custom field?</label>
                  <div class="col-md-6 d-flex align-items-center">                                    
                     <label class="custom-control custom-radio mr-3">
                     <input id="fieldstore_yes_create" name="fieldstore" type="radio" class="custom-control-input" value="yes">
                     <span class="custom-control-label">Yes</span>
                     </label>
                     <label class="custom-control custom-radio">
                     <input id="fieldstore_no_create" name="fieldstore" type="radio" class="custom-control-input" value="no" checked>
                     <span class="custom-control-label">No</span>
                     </label>
                  </div>
               </div>
               <div id="fieldverified_yes_no_create">
                  <div class="input-group row">
                     <label class="form-label col-md-6">What grade?</label>
                     <div class="col-md-6">
                        <select class="custom-select" id="verification_grade_create">
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
               <input type="hidden" id="field_verification_grade_create">
               <input type="hidden" id="field_name_create">
               <input type="hidden" id="field_type_create">
               <input type="hidden" id="field_isVisible_create">
               <input type="hidden" id="field_section_create">
               <input type="hidden" id="field_ids_create">
               <input type="hidden" id="field_items_create">
               <input type="hidden" id="id_create">
            </div>
            <div class="modal-footer">
               <button type="button" class="btn waves-effect waves-light btn-primary" id="submit_create" onclick="createForm()">Add</button>
               <button type="button" class="btn waves-effect waves-light btn-danger" data-dismiss="modal">Close</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!-- Add Fields Modal-->
<div class="modal " id="eFormFieldModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Select field options</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
         </div>
         <form class="mb-0" role="form" action="" id="addFieldForm" method="POST">
            <div class="modal-body">
               <div class="form-group">
                  <label>Label Name</label>
                  <input type="text" class="form-control" id="field_label" >
               </div>
               <div class="form-group">
                  <label>Instructions</label>
                  <textarea class="form-control" id="field_label_instructions" rows="3"></textarea>
               </div>
               <div class="form-group row">
                  <label class="control-label col-md-6">Is the field mandatory?</label>
                  <div class="col-md-6 d-flex align-items-center">                                    
                     <label class="custom-control custom-radio mr-3">
                     <input id="field_mandate_yes" name="field_mandate_yes" type="radio" class="custom-control-input" value="yes">
                     <span class="custom-control-label">Yes</span>
                     </label>
                     <label class="custom-control custom-radio">
                     <input id="field_mandate_no" name="field_mandate_no" type="radio" class="custom-control-input" value="no" checked>
                     <span class="custom-control-label">No</span>
                     </label>
                  </div>
               </div>
               <div class="form-group row signature_file">
                  <label class="control-label col-md-6">Is the Signature required?</label>
                  <div class="col-md-6 d-flex align-items-center">                                    
                     <label class="custom-control custom-radio mr-3">
                     <input id="field_signature_yes" name="field_signature_yes" type="radio" class="custom-control-input" value="yes">
                     <span class="custom-control-label">Yes</span>
                     </label>
                     <label class="custom-control custom-radio">
                     <input id="field_signature_no" name="field_signature_no" type="radio" class="custom-control-input" value="no" checked>
                     <span class="custom-control-label">No</span>
                     </label>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="control-label col-md-6">Field must be verified?</label>
                  <div class="col-md-6 d-flex align-items-center">                                    
                     <label class="custom-control custom-radio mr-3">
                     <input id="fieldver_yes" name="fieldver_yes" type="radio" class="custom-control-input fieldver" value="yes">
                     <span class="custom-control-label">Yes</span>
                     </label>
                     <label class="custom-control custom-radio">
                     <input id="fieldver_no" name="fieldver_no" type="radio" class="custom-control-input fieldver" value="no" checked>
                     <span class="custom-control-label">No</span>
                     </label>
                  </div>
               </div>
               <div id="fieldverified_yes_no">
                  <div class="input-group row">
                     <label class="form-label col-md-6">What grade?</label>
                     <div class="col-md-6">
                        <select class="custom-select" id="verification_grade" data-default="">
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
               <input type="hidden" id="field_verification_grade">
               <input type="hidden" id="field_name">
               <input type="hidden" id="field_type">
               <input type="hidden" id="field_isVisible">
               <input type="hidden" id="field_section">
               <input type="hidden" id="field_ids">
               <input type="hidden" id="field_items">
            </div>
            <div class="modal-footer">
               <button type="button" class="btn waves-effect waves-light btn-primary" id="submit" onclick="addeForm()">Add</button>
               <button type="button" class="btn waves-effect waves-light btn-danger" data-dismiss="modal">Close</button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Add file Fields Modal-->
<div class="modal " id="eFormfileFieldModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel"> Create File Field </h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
         </div>
         <form class="mb-0" role="form" action="" id="addfileFieldForm" method="POST">
            <div class="modal-body">
               <div class="form-group">
                  <label class="required">Label Name</label>
                  <input type="text" class="form-control" id="filefield_label" >
               </div>
               <div class="form-group row">
                  <label class="control-label col-md-6">Is the field mandatory?</label>
                  <div class="col-md-6 d-flex align-items-center">                                    
                     <label class="custom-control custom-radio mr-3">
                     <input id="field_mandate_yes_create" name="field_mandate" type="radio" class="custom-control-input" value="yes">
                     <span class="custom-control-label">Yes</span>
                     </label>
                     <label class="custom-control custom-radio">
                     <input id="field_mandate_no_create" name="field_mandate" type="radio" class="custom-control-input" value="no" checked>
                     <span class="custom-control-label">No</span>
                     </label>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="control-label col-md-6">Field must be verified?</label>
                  <div class="col-md-6 d-flex align-items-center">                                    
                     <label class="custom-control custom-radio mr-3">
                     <input id="filefieldver_yes" name="fieldver_yes" type="radio" class="custom-control-input fieldver" value="yes">
                     <span class="custom-control-label">Yes</span>
                     </label>
                     <label class="custom-control custom-radio">
                     <input id="filefieldver_no" name="fieldver_no" type="radio" class="custom-control-input fieldver" value="no" checked>
                     <span class="custom-control-label">No</span>
                     </label>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="control-label col-md-6">Do you want to store this custom field?</label>
                  <div class="col-md-6 d-flex align-items-center">                                    
                     <label class="custom-control custom-radio mr-3">
                     <input id="filefield_mandate_yes" name="field_mandate_yes" type="radio" class="custom-control-input" value="yes">
                     <span class="custom-control-label">Yes</span>
                     </label>
                     <label class="custom-control custom-radio">
                     <input id="filefield_mandate_no" name="field_mandate_no" type="radio" class="custom-control-input" value="no" checked>
                     <span class="custom-control-label">No</span>
                     </label>
                  </div>
               </div>
               <div class="form-group row signature_file">
                  <label class="control-label col-md-6">Required Digital Signature ?</label>
                  <div class="col-md-6 d-flex align-items-center">                                    
                     <label class="custom-control custom-radio mr-3">
                     <input id="filefield_signature_yes" name="field_signature_yes" type="radio" class="custom-control-input" value="yes">
                     <span class="custom-control-label">Yes</span>
                     </label>
                     <label class="custom-control custom-radio">
                     <input id="filefield_signature_no" name="field_signature_no" type="radio" class="custom-control-input" value="no" checked>
                     <span class="custom-control-label">No</span>
                     </label>
                  </div>
               </div>
               
               
               <input type="hidden" id="filefield_name">
               <input type="hidden" id="filefield_type">
               <input type="hidden" id="filefield_isVisible">
               <input type="hidden" id="filefield_section">
               <input type="hidden" id="filefield_ids">
               <input type="hidden" id="filefield_items">
            </div>
            <div class="modal-footer">
               <button type="button" class="btn waves-effect waves-light btn-primary" id="submit" onclick="addFileFieldeForm()">Add</button>
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

<!-- Reset confirmation modal start -->
    <div id="reset_eform_confirm_modal" class="modal " tabindex="-1" role="dialog" aria-hidden="true" style="z-index:9999 !important">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm">
            <div class="modal-content" >
                <div class="modal-body">
                    <p class="text-center">Are you sure you want to reset this eForm?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary waves-effect" id="yes_reset_eform_btn">Yes</button>
                    <button class="btn btn-info waves-effect" id="no_reset_eform_btn" data-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Reset confirmation modal end -->

<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<!-- JQuery  -->
<script>
   /*jQuery('.edit-eform-page').on('click','.next-btn', function(){
    console.log("Next");
    var next = jQuery('.nav-tabs > .active').next('li');
    console.log("NXT:", next);
    if(next.length){
    console.log("NEXT-LENGTH:",next.length);
    //next.find('a').trigger('click');
    next.find('a').tab('show');
    }else{
    jQuery('#myTabs a:first').tab('show');
    } 
    });
    
    jQuery('.edit-eform-page').on('click','.previous-btn', function(){
    console.log("Previous");
    var prev = jQuery('.nav-tabs > .active').prev('li');
    console.log("PRE:", prev);
    if(prev.length){
    //prev.find('a').trigger('click');
    prev.find('a').tab('show');
    }else{
    jQuery('#myTabs a:last').tab('show');
    } 
    });*/

    $('.next-btn').click(function() {

        /*var pulldata = 0;
                var facematch = 0;
                if ($('input[name="pulldata"]:checked').val() == 'yes' && $('input[name="facematch"]:checked').val() == 'yes') {
                $('#addfield .custom-file-input').each(function() {
                var check = $(this).attr('data-type');
                if (check == 'file') {
                    pulldata = 1;
                    facematch=1;
                }
    
                });
                if (pulldata == 0 && facematch==0) {
                toastr.error('This eForm should include a file for face matching feature and pull data from document feature ');
                //return false;
                }
                }
                if ($('input[name="facematch"]:checked').val() == 'yes') {
    
                $('#addfield .custom-file-input').each(function() {
                var check_facematch = $(this).attr('data-type');
                if (check_facematch == 'file') {
                facematch = 1;
                }
    
                });
                if (facematch == 0) {
    
                toastr.error('This eForm should include a file for face matching feature from document feature');
                //return false;
                }
                }
                if ($('input[name="pulldata"]:checked').val() == 'yes') {
    
                $('#addfield .custom-file-input').each(function() {
                var check_pulldata = $(this).attr('data-type');
                if (check_pulldata == 'file') {
                    pulldata = 1;
                }
    
                });
                if (pulldata == 0) {
                toastr.error('This eForm should include a file for pull data from document feature ');
                //return false;
                }
                }*/
        // var pulldata = 0;
        // if ($('input[name="pulldata"]:checked').val() == 'yes') {

        //     $('#addfield .custom-file-input').each(function() {
        //         var check_pulldata = $(this).attr('data-type');
        //         if (check_pulldata == 'file') {
        //             pulldata = 1;
        //         }

        //     });
        //     if (pulldata == 0) {
        //         toastr.error('File field is required.');
        //         return false;
        //     }
        // }

        $('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
    });
   
   $('.previous-btn').click(function() {
       $('.nav-tabs > .nav-item > .active').parent().prev('li').find('a').trigger('click');
   });
   
   
   //for process tab
   var x=1+<?php echo $count_custom; ?>;
   function process_add_button() {
    
    var html = "";
    var txt='';

    var data_attr = parseInt($("#count_check_approval").attr('data-attr'));
    
    if(data_attr > 3){
        toastr.remove();
        toastr.error('You can not add  more than four approvals.');
        return false;
    }
    var n1 = data_attr;
    var n2 = 1;
    var r = n1 + n2;
    $("#count_check_approval").attr('data-attr', r);
    html += '<div class="row remove_all remove-'+x+'">';
    html += '<div class="col-md-4"><div class="form-group" id="" ><label for="process">'+txt+' Approval <i data-toggle="tooltip" data-placement="top" title="This AKcess ID will approve or reject the eForm response. If approved, it will be sent to the next AKcess ID if there is any." class="fa fa-info-circle" aria-hidden="true"></i></label><input type="text" name="field['+x+'][process]" class="form-control" id="process" placeholder="Enter AKcess ID"></div></div>';
    html += '<div class="col-md-6"><div class="form-group" id="" ><label for="process">Notify If <i data-toggle="tooltip" data-placement="top" title="These IDs will be notified on the application, about this specific action taken on the response.The eform response’s owner will be notified about all the actions taken." class="fa fa-info-circle" aria-hidden="true"></i></label>';

    html += '<select class="form-control fields_approve_section" onchange="changeApproveRejectedStatus('+x+');" id="field_'+x+'" name="field['+x+'][notify]"> <option value="">Select Any</option><option value="approve">Approved</option><option value="reject">Rejected</option><option value="both" value="both">Both</option></select></div>';
    html += '<div class="clearfix"></div>';
    html += '<select class="form-control select2" multiple="multiple" id="akcessID_approve_'+x+'" name="field['+x+'][notify_approve][]" style="display:none;">';

        <?php foreach ($users_process as $user) { ?>
             html += "<option value='<?php echo $user->id; ?>'><?php echo $name = $user->name.'('.$user->akcessId.')['.$user->usertype.']'; ?></option>";
        <?php } ?>
    html += '</select>';

    html += '<select class="form-control select2" multiple="multiple" id="akcessID_reject_'+x+'" name="field['+x+'][notify_reject][]" style="display:none;">';

        <?php foreach ($users_process as $user) { ?>
             html += "<option value='<?php echo $user->id; ?>'><?php echo $name = $user->name.'('.$user->akcessId.')['.$user->usertype.']'; ?></option>";
        <?php } ?>
    html += '</select>';

    html += '<select class="form-control select2" multiple="multiple" id="akcessID_both_'+x+'" name="field['+x+'][notify_both][]" style="display:none;">';

        <?php foreach ($users_process as $user) { ?>
             html += "<option value='<?php echo $user->id; ?>'><?php echo $name = $user->name.'('.$user->akcessId.')['.$user->usertype.']'; ?></option>";
        <?php } ?>
    html += '</select>';
    html += '</div><div class="clearfix"></div><div class="form-group" id=""><label></label><div class="col-md-2"><button type="button" class="btn waves-effect waves-light btn-danger" onclick="process_remove_button('+x+')"><i class="fa fa-minus"></i></button></div></div>';
    html += '</div>';
   // html += '</div>';
    $('.process_wrapper').append(html);
    x++;
}
function process_remove_button(value) {
    var data_attr = $("#count_check_approval").attr('data-attr');
    $("#count_check_approval").attr('data-attr', data_attr-1);
    $('.remove-'+value).remove();
}
   
function changeApproveRejectedStatus(key) {

     $('#akcessID_approve_'+key).next().hide();
        $('#akcessID_reject_'+key).next().hide();
        $('#akcessID_both_'+key).next().hide();

    $('#akcessID_approve_'+key).hide();
    $('#akcessID_reject_'+key).hide();
    $('#akcessID_both_'+key).hide();
    var value = $('#field_'+key+' option:selected').val();
    $('#akcessID_'+value+'_'+key).show();

    $('#akcessID_'+value+'_'+key).select2({
        placeholder: "Please select",
    });


}

$( document ).ready(function() {
   $('.eform_response_process_pdf').select2({
               placeholder: "Please select",
           });
});


</script>
