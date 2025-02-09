<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\IDCard $idcard
 */
?>


<div class="row">


    <div class="col-md-12 text-right">
        <?= $this->Html->link(__('<i class="fa fa-remove" aria-hidden="true"></i> Close'), ['action' => 'index'], ['escape' => false]) ?> &nbsp;

    </div><br>


    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title"><?= $page_title ?></h4>
                <p class="card-category">&nbsp;</p>
            </div>
            <div class="card-body">
                <?= $this->Form->create($idcard, ['type' => 'file', 'id' => 'fileInput']) ?>
                <div class="row">
                    <div class="col-md-8 py-4 border-top">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">                                   
                                    <?php echo $this->Form->input('firstName', array('label' => 'FirstName', 'class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <?php echo $this->Form->input('lastName', array('label' => 'LastName', 'class' => 'form-control')); ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group my-4">
                            <?php echo $this->Form->input('idNo', array('label' => 'ID No.', 'class' => 'form-control')); ?>
                        </div>

                        <div class="row my-4">
                            <div class="col">
                                <div class="form-group">
                                    <?php echo $this->Form->input('DOB', ['class' => 'form-control date_of_birth', 'type' => 'text', 'label' => 'Date of birth', 'data-format' => "Y-m-d", 'id' => 'date_of_birth']); ?>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <?php echo $this->Form->input('idCardExpiyDate', ['class' => 'form-control expiry_date', 'type' => 'text', 'label' => 'ID Card Expiry Date', 'data-format' => "F d, Y", 'id' => 'expiry_date']); ?>                                    
                                </div>
                            </div>
                        </div>

                        <div class="custom-file border w-50">
                            <div class="input file">
                                <input type="file" name="photo" id="customFile" class="custom-file-input">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                        </div>

                        <?= $this->Form->button(__('Save'), array('class' => 'btn btn-success', 'id' => 'submit_btn')) ?>

                        <div class="clearfix"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="px-3">
                            <div class="border shadow">
                                <?php echo $this->Html->image('user.png', array('class' => 'card-img-top image1')); ?>
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th>FullName :</th>
                                            <td><label class="firstname_label"></label>&nbsp;<label class="lastname_label"></label></td>
                                        </tr>
                                        <tr>
                                            <th>ID NUMBER :</th>
                                            <td><label class="idno_label"></label></td>
                                        </tr>
                                        <tr>
                                            <th>DOB :</th>
                                            <td><label class="date_of_birth_label"></label></td>
                                        </tr>
                                        <tr>
                                            <th>Valid THRU :</th>
                                            <td><label class="expiry_date_label"></label></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>