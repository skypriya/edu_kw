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
                    <h4 class="m-b-0 text-white"><i class="fal fa-location-circle mr-1"></i>Add Location</h4>
                </div>
                <div class="card-body">
                    <?= $this->Form->create($location) ?>
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?php echo $this->Form->input('name', array('label' => ['text'=>'Campus Name', 'class'=>'required'], 'class' => 'form-control')); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?php echo $this->Form->input( 'openFrom',  array('type' => 'text', 'label' => 'Open From', 'class' => 'form-control picktime')); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?php echo $this->Form->input( 'openTo',  array('type' => 'text', 'label' => 'Open To', 'class' => 'form-control picktime')); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?php echo $this->Form->input( 'address',  array('label' => 'Address', 'class' => 'form-control')); ?>
                                    </div>
                                </div>
                            </div>
                            

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>User Allow</label>
                                        <select name="userAllow" id="userAllow" class="custom-select">
                                            <option value="">Select User Allow</option>
                                            <?php
                                            foreach ($userAllow as $userAllows) {
                                                ?>
                                                    <option value="<?php echo $userAllows->id; ?>" <?php echo $selected; ?>>
                                                        <?php echo $userAllows->name; ?>
                                                    </option>
                                                <?php
                                                }
                                            ?>
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