<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
use Cake\ORM\TableRegistry;
$this->Users = TableRegistry::get('Users'); 
?>
<div class="row">
   <?php if($session_user['usertype'] == 'Admin' && $gate->userAllow > $allcount){ ?>
            
                    <div class="col-md-12 text-right">
                    
                        <?= $this->Html->link(__('<i class="fa fa-plus" aria-hidden="true"></i> Add Student'), ['action' => 'addStudents', $gate->id], ['escape' => false, 'id' => 'addStudents']) ?> &nbsp;


                    </div><br>
            
        <?php } ?>
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title "><?= $page_title ?></h4>
                  <p class="card-category"> &nbsp;</p>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="example1">
                      <thead class=" text-primary">
                         <tr>
                            
                            <th scope="col">Name</th>
                            
                            <th scope="col">University Name</th>
                            
                            <th scope="col">Email</th>
                            
                            <th scope="col">Mobile Number</th>
                            
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
                        </tr>
                      </thead>
                      <tbody>

                        <?php 
                        $k = 0;
                        foreach ($all as $a): 

                                $user = $this->Users->find()->where(['id' => $a->userId])->first();
                                 ?>
                              <tr>
                                  
                                  <td>
                                    <input type="hidden" name="userId[]" id="<?= $user->id ?>" value="<?= $user->id ?>" />
                                     <?= $this->Html->link(__($user->name), ['action' => 'view', $user->id], ['escape' => false]) ?> 
                                  </td>
                                  <td><?= h($user->companyName) ?></td>
                                  
                                  
                                  <td><?= h($user->email) ?></td>
                                  
                                  <td><?= h($user->mobileNumber) ?></td>
                                  
                                  <td class="actions">
                                                  
                                      <?= $this->Html->link(__('<i class="fa fa-eye" aria-hidden="true"></i> View'), ['controller' => 'Users', 'action' => 'view', $user->id], ['escape' => false]) ?> <br>

                                      <?= $this->Html->link(__('<i class="fa fa-trash" aria-hidden="true"></i> Remove Student'), ['action' => 'deleteStudent', $a->id, $gate->id], ['escape' => false, 'class' => 'delete_btn', 'data-link' => 'remove_btn'.$a->id] ) ?> 

                                      <?= $this->Form->postLink(__('Delete'), ['action' => 'deleteStudent', $a->id, $gate->id], ['escape' => false, 'id' => 'remove_btn'.$a->id, 'style' => 'display:none;']  ) ?>

                                      
                                      
                                  </td>
                              </tr>
                               <?php 
                                  $k++;
                                endforeach;  ?>

                      </tbody>
                    </table>
                    

                  </div>
                </div>
              </div>
            </div>
            
          </div>




<!-- Modal -->
<div id="myModalaplus" class="modal " role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        
      </div>
      <div class="modal-body" id="contentBody">
      
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>