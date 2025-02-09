<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Doc $doc
 */

?>
<form method="post" accept-charset="utf-8" id="addidcard" controller="IDCard" action="<?php echo PATH_URL_PREFIX; ?>/i-d-card/pdf-genrate">
                <h4>Add Document</h4>
                <fieldset>
                    
                    <div class="form-group input-group">
                        <div class="form-group col-lg-6">
                            <?php echo $this->Form->input('idNo', array('label' => 'ID No.', 'class' => 'form-control', 'value' => $user->id, 'required' => 'true', 'disabled' => "disabled")); ?>
                        </div>
                        <div class="form-group col-lg-6">
                            <?php echo $this->Form->input('idCardExpiyDate', ['required' => 'true', 'class' => 'form-control expiry_date_popup', 'type' => 'text', 'label' => 'ID Card Expiry Date', 'data-format' => "YYYY-MM-DD", 'id' => 'expiry_date_popup', 'value' => $idCardExpiyDate]); ?>  
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <?php echo $this->Form->input('attachs', array('type' => 'file', 'label' => 'Attachment'));?>
                    </div>

                    <input type="hidden" id="a" name="a" value="<?php echo $userID; ?>"/>
                    <input type="hidden" id="b" name="b" value=""/>
                </fieldset><br>
                <div class="text-center">
                    <?= $this->Form->button(__('Submit'), array('class' => 'btn btn-primary')) ?>
                </div>

                <?= $this->Form->end() ?>