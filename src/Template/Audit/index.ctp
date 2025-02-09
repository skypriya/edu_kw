<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
use Cake\ORM\TableRegistry;
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive m-t-10">
                    <table id="auditTrail" class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Transaction</th>
                                <th scope="col">Action</th>
                                <th scope="col">Member Name</th>
                                <th scope="col">Updated By</th>
                                <th scope="col">Date</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($auditArray as $n): ?>
                                <tr>
                                    <td><?php echo $n['id']; ?></td>
                                    <td><?= $n['table_name'] ?></td> 
                                    <td><?= $n['action'] ?></td> 
                                    <td><?= $n['user_id'] ?></td> 
                                    <td><?= $n['by_user_id'] ?></td> 
                                    <td data-order="<?php echo date("Y-m-d h:i:s", strtotime($n['created_on'])); ?>"><?= date("d/m/Y h:i:s", strtotime($n['created_on'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>