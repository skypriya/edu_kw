<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\GateUser[]|\Cake\Collection\CollectionInterface $gateUsers
 */
use Cake\ORM\TableRegistry;

$this->Gates = TableRegistry::get('Gates');
?>

<div class="row">


    <div class="col-md-12 text-right">

<?= $this->Html->link(__('<i class="fa fa-plus" aria-hidden="true"></i> Add Gate'), ['action' => 'add'], ['escape' => false]) ?> &nbsp;


    </div><br>


    <div class="col-md-12">
        <div class="card">

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="example1">
                        <thead class=" text-primary">

                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Gate</th>
                                <th scope="col" class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $k = 0;
                            foreach ($gateUsers as $gateUser):
                                ?>
                                <tr>
                                    <td><?= $this->Number->format($gateUser->id) ?></td>
    <?php
    $gate = $this->Gates->find()->where(['id' => $gateUser->gateId])->first();
    ?>
                                    <td><?= h($gate->name) ?></td>
                                    <td class="actions">

    <?= $this->Html->link(__('<i class="fa fa-eye" aria-hidden="true"></i> Gate Information'), ['controller' => 'Gates', 'action' => 'view', $gateUser->gateId], ['escape' => false]) ?> <br>

                                        <?= $this->Html->link(__('<i class="fa fa-edit" aria-hidden="true"></i> Edit'), ['action' => 'edit', $gateUser->id], ['escape' => false]) ?> <br>



    <?= $this->Html->link(__('<i class="fa fa-trash" aria-hidden="true"></i> Delete'), ['action' => 'delete', $gateUser->id], ['escape' => false, 'class' => 'delete_btn', 'data-link' => 'remove_btn' . $gateUser->id]) ?> 

                                <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $gateUser->id], ['escape' => false, 'id' => 'remove_btn' . $gateUser->id, 'style' => 'display:none;']) ?>
                                    </td>
                                </tr>
    <?php
    $k++;
endforeach;
?>

                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    </div>

</div>

