<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Doc $doc
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Doc'), ['action' => 'edit', $doc->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Doc'), ['action' => 'delete', $doc->id], ['confirm' => __('Are you sure you want to delete # {0}?', $doc->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Docs'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Doc'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="docs view large-9 medium-8 columns content">
    <h3><?= h($doc->name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Name') ?></th>
            <td><?= h($doc->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Attachs') ?></th>
            <td><?= h($doc->attachs) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($doc->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Size') ?></th>
            <td><?= $this->Number->format($doc->size) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('UserId') ?></th>
            <td><?= $this->Number->format($doc->userId) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($doc->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($doc->modified) ?></td>
        </tr>
    </table>
</div>
