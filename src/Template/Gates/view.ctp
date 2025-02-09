<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Gate $gate
 */
?>



<div class="row">
        <?php if($session_user['usertype'] == 'Admin'){ ?>
            
                    <div class="col-md-12 text-right">
                    
                        
                        <?= $this->Html->link(__('<i class="fa fa-edit" aria-hidden="true"></i> Edit'), ['action' => 'edit', $gate->id], ['escape' => false]) ?>  &nbsp;

                        <?= $this->Html->link(__('<i class="fa fa-remove" aria-hidden="true"></i> Close'), ['action' => 'index'], ['escape' => false]) ?> &nbsp;

                         <?= $this->Html->link(__('<i class="fa fa-trash" aria-hidden="true"></i> Delete'), ['action' => 'delete', $gate->id], ['escape' => false, 'class' => 'delete_btn', 'data-link' => 'remove_btn'] ) ?> 

                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $gate->id], ['escape' => false, 'id' => 'remove_btn', 'style' => 'display:none;']  ) ?>

                    </div><br>
            
        <?php } ?>
            <div class="col-md-8">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title"><?php echo $gate->name; ?></h4>
                  <p class="card-category">&nbsp;</p>
                </div>
                <div class="card-body">
                  <table class="table">
                        <tr>
                            <td scope="row" style="font-weight:bold;"><?= __('Name') ?></td>
                            <td><?= h($gate->name) ?></td>
                        </tr>

                        <tr>
                            <th scope="row"><?= __('Maximum number of students') ?></th>
                            <td><?= $this->Number->format($gate->userAllow) ?></td>
                        </tr>

                        <tr>
                            <th scope="row"><?= __('Open From') ?></th>
                            <td><?= h($gate->openFrom) ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><?= __('Open To') ?></th>
                            <td><?= h($gate->openTo) ?></td>
                        </tr>

                        
                        <tr>
                            <th scope="row"><?= __('Location') ?></th>
                            <td><?= $this->Text->autoParagraph(h($gate->location)); ?></td>
                        </tr>


                        <tr>
                            <th scope="row"><?= __('Created') ?></th>
                            <td><?= h($gate->created) ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><?= __('Modified') ?></th>
                            <td><?= h($gate->modified) ?></td>
                        </tr>
                    </table>
                </div>
              </div>
            </div>


            <div class="col-md-3">
              <div class="card card-chart  text-center">
                <div class="card-header">
                    <div id="qrdiv">
                    <input id="text" type="hidden" value="<?= $gate->qrno.'-'.$gate->id ?>" /><br />
                    <div id="qrcode" style="width:200px; height:200px; margin:auto; "></div>
                </div>
                  
                </div>
                <div class="card-body">
                  <h4 class="card-title">&nbsp;</h4>
                  <div class="text-center">
                    <?php echo 'QR for '.$gate->name; ?>
                  </div>
                  
                </div>
                
              </div>
            </div>
            
          </div>


