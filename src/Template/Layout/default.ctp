<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = 'AKcess: EDU';
?>
<!DOCTYPE html>
<html>
<head>
<?= $this->Html->charset() ?>
    <!-- <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png"> -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>
    AKcess: EDU
    </title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css"> -->
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
    <!-- CSS Files -->
    <!-- <?= $this->Html->css('style.css') ?> -->
    <?= $this->Html->css('css/material-dashboard.css?v=2.1.1') ?>
    <?= $this->Html->css('custom-style.css') ?>    
    <?= $this->Html->css('demo/datatables.min') ?>
    <?= $this->Html->css('demo/select2.min') ?>
    <?= $this->Html->css('demo/bootstrap.min') ?>
    <?= $this->Html->css('demo/jquery-ui') ?>
    <?= $this->Html->css('demo/multiselect/jquery.multiselect') ?>
    <?= $this->Html->css('jquery.tokenize.css') ?>
    <?= $this->Html->css('toastr.min.css') ?>
</head>
<body>    
    <?= $this->Flash->render() ?>
    <div class="container clearfix">
        <?= $this->fetch('content') ?>
    </div>
    <footer>
    
    </footer>
</body>
</html>
