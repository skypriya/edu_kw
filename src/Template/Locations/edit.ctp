<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Sclass $sclass
 */
?>
<div class="edit-staff-page">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline-info">
                <div class="card-header">
                    <h4 class="m-b-0 text-white d-flex align-items-center"><i class="fal fa-location-circle mr-1"></i>Edit Location</h4>
                </div>
                <div class="card-body">
                    <?= $this->Form->create($location) ?>
                        <div class="form-body">
                            <div class="row p-t-20">
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
                                        <label>Address</label>
                                        <textarea name="address" id="address" col="3" class="form-control"><?php echo $address_view; ?></textarea>  
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>User Allow</label>
                                        <select name="userAllow" id="userAllow" class="custom-select input-md blurclass">
                                            <option value="">Select User Allow</option>
                                            <?php
                                            foreach ($userAllow as $userAllows) {
                                                $selected = "";
                                                if($location->userAllow == $userAllows->id) {
                                                    $selected = "selected";
                                                }
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
                        <div class="form-actions text-right">
                            <?= $this->Form->button(__('Update'), array('class' => 'btn waves-effect waves-light btn-primary', 'id' => 'submit_btn')) ?>
                            <?= $this->Html->link(__('<i class="fas fa-times mr-1"></i> Close'), ['action' => 'index'], ['escape' => false, 'class' => 'btn waves-effect waves-light btn-danger']) ?>
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