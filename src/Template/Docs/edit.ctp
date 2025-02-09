<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Doc $doc
 */
?>

<form method="post" accept-charset="utf-8" id="adddoc" controller="Doc" action="<?php echo PATH_URL_PREFIX; ?>/docs/doc-genrate">
    <h4>Edit Document</h4>
    <fieldset>

        <div class="form-group input-group">
            <div class="form-group col-lg-12">
                <?php echo $this->Form->input('name', array('label' => 'Name', 'class' => 'form-control', 'required' => 'true', 'value' => $name)); ?>
            </div>                        
        </div>
        <div class="col-lg-12">
            <?php echo $this->Form->input('attachs', array('type' => 'file', 'label' => 'Attachment', 'required' => 'true')); ?>
        </div>

        <input type="hidden" id="a" name="a" value="<?php echo $userID; ?>"/>
        <input type="hidden" id="b" name="b" value="<?php echo $did; ?>"/>
    </fieldset><br>
    <div class="text-center">
        <?= $this->Form->button(__('Submit'), array('class' => 'btn btn-primary')) ?>
    </div>

    <?= $this->Form->end() ?>