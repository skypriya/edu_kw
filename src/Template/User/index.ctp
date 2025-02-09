<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
?>
<?php if($session_user['usertype'] == 'Admin'){ ?>
<div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title "><?= $page_title ?></h4>
                  <p class="card-category"> These all students are waiting for your approval.</p>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="example1">
                      <thead class=" text-primary"><tr>
                        <th>Name</th>
                        
                        <th>University Name</th>
                        
                        <th>Email</th>
                        
                        <th>Gender</th>

                        <th>Mobile Number</th>
                        
                        <th><?= __('Actions') ?></th></tr>
                      </thead>
                      <tbody>

                        <?php 
                        $k  = 0;
                        foreach ($users as $user): ?>
                            <tr>
                                
                                <td>
                                   <?= $this->Html->link(__($user->name), ['controller' => 'Users', 'action' => 'view', $user->id], ['escape' => false]) ?> 
                                </td>
                                <td><?= h($user->companyName) ?></td>
                                
                                <td><?= h($user->email) ?></td>
                                
                                <td><?= h($user->gender) ?></td>
                                
                                <td><?= h($user->mobileNumber) ?></td>
                                
                                <td class="actions">
                                                
                                    <?= $this->Html->link(__('<i class="fa fa-thumbs-up" aria-hidden="true"></i> Approve'), ['controller' => 'users', 'action' => 'approve', $user->id], ['escape' => false]) ?> <br>

                                    <?= $this->Html->link(__('<i class="fa fa-eye" aria-hidden="true"></i> View'), ['controller' => 'users', 'action' => 'view', $user->id], ['escape' => false]) ?> <br>

                                    <?= $this->Html->link(__('<i class="fa fa-edit" aria-hidden="true"></i> Edit'), ['controller' => 'users', 'action' => 'edit', $user->id], ['escape' => false]) ?> <br>
                                    
                                     

                                     <?= $this->Html->link(__('<i class="fa fa-trash" aria-hidden="true"></i> Delete'), ['controller' => 'users', 'action' => 'delete', $user->id], ['escape' => false, 'class' => 'delete_btn', 'data-link' => 'remove_btn'.$user->id] ) ?> 

                                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'users', 'action' => 'delete', $user->id], ['escape' => false, 'id' => 'remove_btn'.$user->id, 'style' => 'display:none;']  ) ?>
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

<?php } else{ ?>
<div class="row">
            <div class="col-md-4">
              <div class="card card-chart">
                <div class="card-header card-header-success">
                  <div class="ct-chart" id="dailySalesChart"></div>
                </div>
                <div class="card-body">
                  <h4 class="card-title">Class Attendance</h4>
                  <p>Here will be class attendance chart</p>
                  
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons">access_time</i> last considered <?php echo date('m/d/Y'); ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card card-chart">
                <div class="card-header card-header-warning">
                  <div class="ct-chart" id="websiteViewsChart"></div>
                </div>
                <div class="card-body">
                  <h4 class="card-title">Gate Entries</h4>
                  <p>Here will be gate entries chart</p>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons">access_time</i> last considered <?php echo date('m/d/Y'); ?>
                  </div>
                </div>
              </div>
            </div>
            
          </div>
          <?php } ?>