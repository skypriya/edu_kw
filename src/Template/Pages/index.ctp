<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Sclass[]|\Cake\Collection\CollectionInterface $sclasses
 */
?>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      
      <div class="card-body">
      <?= $this->Form->create() ?>
              <div class="form-body">
                  <?php foreach($settings as $key => $settings_data) { 
                    if(isset($settings_data->key_name) && $settings_data->key_name == 'timezone') {?>
                      <div class="row">
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label class="font-400">Timezone</label>
                                  <select name="data[timezone]" class="form-control custom-select">
                                    <?php foreach($timezone as $key=>$value){
                                        $selected = '';
                                        if($key == $settings_data->key_value){
                                            $selected = 'selected';
                                        }
                                        echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                    } ?>
                                  </select>
                              </div>
                          </div>
                      </div>
                    <?php } 
                     } ?>
                  <?= $this->Form->button(__('Save'), array('class' => 'btn btn-primary pull-right', 'id'=> 'submit_btn' )) ?>
              </div>
          <?= $this->Form->end() ?>
      </div>
    </div>
  </div>
</div>

