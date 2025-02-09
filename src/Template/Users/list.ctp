<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title "><?= $page_title ?></h4>
                <p class="card-category">List and update User role</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="example1">
                        <thead class=" text-primary">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Name</th>
                                <th scope="col">Role</th>                    
                                <th scope="col">Email</th>                    
                                <th scope="col">Mobile Number</th>                    
                                <th scope="col" class="actions"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $this->Number->format($user->id) ?></td>
                                <td>
                                    <?= $this->Html->link(__($user->name), ['action' => 'view', $user->id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'View']) ?> 
                                </td>
                                <td><?= h($user->usertype) ?></td>                            
                                <td><?= h($user->email) ?></td>                            
                                <td><?= h($user->mobileNumber) ?></td>                                
                                <td class="actions">
                                    <span class="btn-link" data-toggle="modal" data-target="#staticBackdrop" onClick="setUpdateUser(<?= $user->id ?>, '<?= $user->name ?>', '<?= $user->email ?>', '<?= $user->usertype ?>')">
                                        <i class="fa fa-edit" aria-hidden="true"></i>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal " id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Update Role</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= $this->Form->create('Post', ['action' => 'roleupdate']) ?>
                <div class="modal-body">
                    <input type="hidden" name="id" id="update_role_user_id"/>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Name</label>
                        <div class="col-sm-8">
                            <span class="form-control bg-light" id="update_role_user_name"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Email</label>
                        <div class="col-sm-8">
                            <span class="form-control bg-light" id="update_role_user_email"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Role (User Type)</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="usertype" id="update_role_user_type">
                                <option value="Admin">Admin</option>
                                <option value="Student">Student</option>
                                <option value="Teacher">Academic Personnel</option>
                                <option value="Staff">Staff</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">cancel</button>
                    <button class="btn btn-primary">Update Role</button>
                </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<script>
    function setUpdateUser(id, name, email, usertype) {
        document.getElementById('update_role_user_id').value = id
        document.getElementById('update_role_user_name').innerHTML = name
        document.getElementById('update_role_user_email').innerHTML = email
        document.getElementById('update_role_user_type').value = usertype
    }
</script>