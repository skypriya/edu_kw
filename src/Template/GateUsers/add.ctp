<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\GateUser $gateUser
 */
?>

<div class="row">

            
                    <div class="col-md-12 text-right">
                    
                        
                        <a href="javascript:void(0);" onclick="saveForm();"><i class="fa fa-save" aria-hidden="true"></i> Save</a>


                        <?= $this->Html->link(__('<i class="fa fa-remove" aria-hidden="true"></i> Close'), ['action' => 'index'], ['escape' => false]) ?> &nbsp;

                    </div><br>
            
   
            <div class="col-md-8">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Add Gate</h4>
                  <p class="card-category">&nbsp;</p>
                </div>
                <div class="card-body">
                  <?= $this->Form->create($gateUser) ?>
                    <div class="row">
                      
                        <div class="form-group col-lg-6">
                          <label for="classId">Name </label>
                            <select name="gateId" required="required" id="gateId" class="form-control">
                                <?php foreach ($gates as $c): ?>
                                <option value = <?= $c->id ?> <?php if($c->id == $gateUser->gateId){ echo "selected"; }?> ><?= $c->name ?></option>
                                <?php endforeach; ?>

                            </select>
                        </div>
                      
                         
                    </div>
                    <?= $this->Form->button(__('Submit'), array('class' => 'btn btn-primary pull-right', 'id'=> 'submit_btn' )) ?>
                    
                    <div class="clearfix"></div>
                  <?= $this->Form->end() ?>
                </div>
              </div>
            </div>
            
          </div>

