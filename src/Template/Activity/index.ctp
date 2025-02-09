<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
use Cake\ORM\TableRegistry;
?>

<div class="row">
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
                                <th scope="col">Transaction</th>
                                <th scope="col">ID</th>
                                <th scope="col">Date</th>                                
                            </tr>
                        </thead>
                        <tbody>

<?php foreach ($activity as $n): ?>
                                <tr>
                                    <td><?= $n->transaction ?></td>                            
                                    <td class="text-uppercase"><?= $n->id ?></td>
                                    <td><?= date_format($n->date, 'd M Y H:i') ?></td>
                                </tr>
<?php endforeach; ?>

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

</div>
