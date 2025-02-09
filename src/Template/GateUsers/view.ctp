<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\GateUser $gateUser
 */
?>




<div class="row">
        
            
                    <div class="col-md-12 text-right">
                    
                        
                        <?= $this->Html->link(__('<i class="fa fa-edit" aria-hidden="true"></i> Edit'), ['action' => 'edit', $gateUser->id], ['escape' => false]) ?>  &nbsp;

                        <?= $this->Html->link(__('<i class="fa fa-remove" aria-hidden="true"></i> Close'), ['action' => 'index'], ['escape' => false]) ?> &nbsp;

                         <?= $this->Html->link(__('<i class="fa fa-trash" aria-hidden="true"></i> Delete'), ['action' => 'delete', $gateUser->id], ['escape' => false, 'class' => 'delete_btn', 'data-link' => 'remove_btn'] ) ?> 

                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $gateUser->id], ['escape' => false, 'id' => 'remove_btn', 'style' => 'display:none;']  ) ?>

                    </div><br>
            
        
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
                            <th scope="row"><?= __('Created') ?></th>
                            <td><?= h($gateUser->created) ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><?= __('Modified') ?></th>
                            <td><?= h($gateUser->modified) ?></td>
                        </tr>
                    </table>
                </div>
              </div>
            </div>
            
          </div>