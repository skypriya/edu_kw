<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Gate[]|\Cake\Collection\CollectionInterface $gates
 */
?>
<div class="row">
    <?php if ($session_user['usertype'] == 'Admin') { ?>

        <div class="col-md-12 text-right">

            <?= $this->Html->link(__('<i class="fa fa-plus" aria-hidden="true"></i> Add Gate'), ['action' => 'add'], ['escape' => false]) ?> &nbsp;


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
                                <th scope="col">ID</th>
                                <th scope="col">Name</th>
                                <th scope="col">Maximum number of students</th>
                                <th scope="col">Open From</th>
                                <th scope="col">Open To</th>

                                <th scope="col" class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $k = 0;
                            foreach ($gates as $gate):
                                ?>
                                <tr>
                                    <td><?= $this->Number->format($gate->id) ?></td>
                                    <td><?= h($gate->name) ?></td>
                                    <td><?= $this->Number->format($gate->userAllow) ?></td>
                                    <td><?= $gate->openFrom ?></td>
                                    <td><?= $gate->openTo ?></td>

                                    <td class="actions">


                                        <?= $this->Html->link(__('<i class="fa fa-user-plus" aria-hidden="true"></i> Manage Students'), ['action' => 'manageStudents', $gate->id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Manage Students']) ?> <br>

                                        <?= $this->Html->link(__('<i class="fa fa-eye" aria-hidden="true"></i> View'), ['action' => 'view', $gate->id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'View']) ?> <br>

    <?= $this->Html->link(__('<i class="fa fa-edit" aria-hidden="true"></i> Edit'), ['action' => 'edit', $gate->id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Edit']) ?> <br>



                                        <?= $this->Html->link(__('<i class="fa fa-trash" aria-hidden="true"></i> Delete'), ['action' => 'delete', $gate->id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Remove', 'class' => 'delete_btn', 'data-link' => 'remove_btn' . $gate->id]) ?> 

    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $gate->id], ['escape' => false, 'id' => 'remove_btn' . $gate->id, 'style' => 'display:none;']) ?>
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




