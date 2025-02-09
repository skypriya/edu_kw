<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Sclass[]|\Cake\Collection\CollectionInterface $sclasses
 */
?>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-primary">
        <h4 class="card-title "><?= $page_title ?></h4>
        <p class="card-category"> &nbsp;</p>
      </div>
      <div class="card-body">
      <?= $this->Form->create($idcard, array('enctype' => 'multipart/form-data')) ?>
              <div class="form-body">
              
                  <div class="row">
                      <div class="col-md-12">
                          <div class="form-group">
                              <label class="font-400">Timezone</label>
                              <select name="selectedZone">
                                <?php foreach($timezone as $key=>$value){
                                    $selected = '';

                                    if($key == $this->Session->read('Auth.User.timezone')){
                                        $selected = 'selected';
                                    }
                                    echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                } ?>
                              </select>
                          </div>
                      </div>
                  </div>
                  <?= $this->Form->button(__('Submit'), array('class' => 'btn btn-primary pull-right', 'id'=> 'submit_btn' )) ?>
              </div>
          <?= $this->Form->end() ?>
      </div>
    </div>
  </div>
</div>

