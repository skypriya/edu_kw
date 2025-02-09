<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Gate $gate
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
                  <?= $this->Form->create($gate) ?>
                    <div class="row">
                      
                        <div class="form-group col-lg-6">
                          <?php echo $this->Form->input( 'name',  array('label' => 'Name', 'class' => 'form-control')); ?>
                        </div>

                        <div class="form-group col-lg-6">
                          
                           <?php echo $this->Form->input( 'userAllow',  array('label' => 'Maximum number of students', 'class' => 'form-control')); ?>
                         </div>
                      
                         <div class="form-group col-lg-6">
                          
                           <?php echo $this->Form->input( 'openFrom',  array('type' => 'text', 'label' => 'Open From', 'class' => 'form-control picktime', 'style' => 'width: 50%;')); ?>

                           
                           
                         </div>

                         <div class="form-group col-lg-6">
                          
                           <?php echo $this->Form->input( 'openTo',  array('type' => 'text', 'label' => 'Open To', 'class' => 'form-control picktime', 'style' => 'width: 50%;')); ?>
                         </div>

                         
                         
                         

                         <div class="form-group col-lg-6">
                          
                           <?php echo $this->Form->input( 'location',  array('type' => 'text', 'label' => 'Location', 'class' => 'form-control')); ?>
                         </div>
                         
                        

                    </div>
                    <?= $this->Form->button(__('Submit'), array('class' => 'btn btn-primary pull-right', 'id'=> 'submit_btn' )) ?>
                    
                    <div class="clearfix"></div>
                  <?= $this->Form->end() ?>
                </div>
              </div>
            </div>
            
          </div>


