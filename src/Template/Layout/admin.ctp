<?php
use Cake\Datasource\ConnectionManager;

$session_usertype = explode(",", $session_user['usertype']);

if (!function_exists('base_url')) {

    function base_url($atRoot = FALSE, $atCore = FALSE, $parse = FALSE) {
        if (isset($_SERVER['HTTP_HOST'])) {
            $http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
            $hostname = $_SERVER['HTTP_HOST'];
            $dir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

            $core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))), NULL, PREG_SPLIT_NO_EMPTY);
            $core = $core[0];

            $tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
            $end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
            $base_url = sprintf($tmplt, $http, $hostname, $end);
        } else
            $base_url = 'http://localhost/';

        if ($parse) {
            $base_url = parse_url($base_url);
            if (isset($base_url['path']))
                if ($base_url['path'] == '/')
                    $base_url['path'] = '';
        }

        return $base_url;
    }

}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?= $this->Html->charset() ?>       
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <title>
            <?php echo COMP_NAME_TITLE; ?>
        </title>
        <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
        <!--     Fonts and icons     -->
        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
        <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
        
        <!-- CSS Files -->       
        <!--<?= $this->Html->css('css/material-dashboard.css?v=2.1.1') ?>
        <?= $this->Html->css('style.css') ?>
        <?= $this->Html->css('custom-style.css') ?> -->
      
        <!--<?= $this->Html->css('demo/demo.css') ?>-->
        <!-- <?= $this->Html->css('demo/datatables.min') ?>
        <?= $this->Html->css('demo/select2.min') ?>
        <?= $this->Html->css('demo/bootstrap.min') ?>
        <?= $this->Html->css('demo/jquery-ui') ?>
        <?= $this->Html->css('demo/multiselect/jquery.multiselect') ?> -->

        <!-- Bootstrap Core CSS -->
        <?= $this->Html->css('../new-assets/plugins/bootstrap/css/bootstrap.min.css') ?>
        <!-- Datatables css -->
        <!-- <?= $this->Html->css('https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css') ?>
        <?= $this->Html->css('https://cdn.datatables.net/buttons/2.3.2/css/buttons.dataTables.min.css') ?> -->
        <?= $this->Html->css('https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css') ?>
        <!-- Editable CSS -->
        <?= $this->Html->css('../new-assets/plugins/jsgrid/jsgrid.min.css') ?>
        <?= $this->Html->css('../new-assets/plugins/jsgrid/jsgrid-theme.min.css') ?>
        <!-- Custom CSS -->
        <?= $this->Html->css('../new-css/style.css') ?>
        <!-- You can change the theme colors from here -->
        <?= $this->Html->css('../new-css/colors/blue.css') ?>
        <!-- Tokenize -->
        <?= $this->Html->css('jquery.tokenize.css') ?>
        <!-- Datepicker -->
        <?= $this->Html->css('css/datetimepicker') ?>
        <!--Toaster-->        
        <?= $this->Html->css('toastr.min.css') ?>

        <?= $this->Html->css('../new-assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>
        <?= $this->Html->css('../new-assets/plugins/select2/dist/css/select2.min.css') ?>
        <?= $this->Html->css('../new-assets/plugins/switchery/dist/switchery.min.css') ?>
        <?= $this->Html->css('../new-assets/plugins/bootstrap-select/bootstrap-select.min.css') ?>
        <?= $this->Html->css('../new-assets/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') ?>
        <?= $this->Html->css('../new-assets/plugins/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css') ?>
        <?= $this->Html->css('../new-assets/plugins/multiselect/css/multi-select.css') ?>
        <!--Custom Style CSS -->
        <?= $this->Html->css('new-custom-style.css') ?>

        <?php
            $cururl = rtrim($this->Url->build(null, true), '/');
            $spliturl = explode("?",$cururl);
            $curmodule = $spliturl[0];
        ?>
        <script>
            var csrfToken = <?= json_encode($this->request->getParam('_csrfToken')) ?>;
            var baseurl = '<?php echo $curmodule; ?>';
            var burl = '<?php echo $url = rtrim(base_url(TRUE), '/'); ?>';
        </script>

    </head>
    <body class="fix-header fix-sidebar card-no-border">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> </svg>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar w-100">
        <?php
                $contr = $this->request->getParam('controller');
                $act = $this->request->getParam('action');
        ?>
            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                <!-- ============================================================== -->
                <!-- Logo -->
                <!-- ============================================================== -->
                <div class="navbar-header">
                    
                    <a class="navbar-brand" href="/dashboard">
                        <!-- Logo icon -->
                        <b>
                           
                             <?= $this->Html->image('small-logo.png', ['alt' => 'AK', 'class' => 'd-none dark-logo']); ?>
                          
                        </b>
                        <span>
                       
                         <?= $this->Html->image('logo.png', ['alt' => 'EDU', 'class' => 'navbar-logo']); ?>
                         </span> 
                    </a>
                </div>
               
                <div class="navbar-collapse">
                 
                    <ul class="navbar-nav mr-auto mt-md-0">
                        <!-- This is  -->
                        <li class="nav-item"> <a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="mdi mdi-menu"></i></a> </li>
                        <li class="nav-item m-l-10"> <a class="nav-link sidebartoggler hidden-sm-down text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
                      
                    </ul>
                  
                    <ul class="navbar-nav my-lg-0">
                       
                        <?php 
                            $user_id = ConnectionManager::userIdEncode($session_user['id']);  
                        ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?= $this->Html->image('user.png', ['alt' => 'user', 'class' => 'profile-pic']); ?>    
                            </a>
                            <div class="dropdown-menu dropdown-menu-right scale-up">
                                <ul class="dropdown-user">                                    
                                    <li>
                                        <div class="dw-user-box">  
                                            <div class="u-img">
                                            <?= $this->Html->image('user.png', ['alt' => 'user', 'class' => 'profile-pic']); ?>
                                            </div>
                                            <div class="u-text">
                                                <h4><?php echo isset($session_user['name']) ? $session_user['name'] : ""; ?></h4>
                                                <p class="text-muted"><?php echo isset($session_user['email']) ? $session_user['email'] : ""; ?></p>
                                                <?php if((in_array('Student',$session_usertype) || in_array('Staff',$session_usertype) || in_array('Teacher',$session_usertype))&& (!in_array('Admin',$session_usertype))) { ?>
                                                    <?php if(in_array('Student',$session_usertype)) { ?>
                                                        <?= $this->Html->link('View Profile', array('controller' => 'Users', 'action' => 'view', $user_id), array('escape' => false, 'class' => 'btn btn-rounded btn-danger btn-sm')); ?>             
                                                    <?php } else if(in_array('Staff',$session_usertype)) { ?>    
                                                        <?= $this->Html->link('View Profile', array('controller' => 'Users', 'action' => 'staffList', $user_id), array('escape' => false, 'class' => 'btn btn-rounded btn-danger btn-sm')); ?>  
                                                    <?php } else if(in_array('Admin',$session_usertype)) { ?>    
                                                        <?= $this->Html->link('View Profile', array('controller' => 'Users', 'action' => 'adminList', $user_id), array('escape' => false, 'class' => 'btn btn-rounded btn-danger btn-sm')); ?> 
                                                    <?php } else if(in_array('Teacher',$session_usertype)) { ?>    
                                                        <?= $this->Html->link('View Profile', array('controller' => 'Users', 'action' => 'teacherList', $user_id), array('escape' => false, 'class' => 'btn btn-rounded btn-danger btn-sm')); ?> 
                                                    <?php } ?>     
                                                <?php } ?>                                  
                                            </div>
                                        </div>
                                    </li>
                                    <li role="separator" class="divider"></li>
                                    
                                    <?php if(in_array('Admin',$session_usertype)) { ?>
                                    <li>                                        
                                        <?= $this->Html->link('<i class="ti-settings"></i> View Profile', array('controller' => 'Users', 'action' => 'view_profile', $user_id), array('escape' => false)); ?>
                                    </li>
                                    <?php } ?>
                                    <!-- <li><a href="#"><i class="ti-wallet"></i> My Balance</a></li>
                                    <li><a href="#"><i class="ti-email"></i> Inbox</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="#"><i class="ti-settings"></i> Account Setting</a></li>-->
                                    <li role="separator" class="divider"></li>
                                    <!--<li><a href="#"><i class="fa fa-power-off"></i> Logout</a></li>-->
                                    <li>
                                        <?= $this->Html->link('<i class="ti-power-off"></i> Logout', array('controller' => 'Users', 'action' => 'logout'), array('escape' => false)); ?>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar">        
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar1">
                <!-- User profile -->
                
                <!-- End User profile text-->
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="nav-devider"></li>
                        <?php if((in_array('Student',$session_usertype) || in_array('Staff',$session_usertype) || in_array('Teacher',$session_usertype))&& (!in_array('Admin',$session_usertype))) { ?>
                        
                        <li class="nav-item <?php
                                if ($contr == 'Dashboard') {
                                    echo 'active';
                                }
                                ?>">            
                            <?= $this->Html->link('<i class="fal fa-home"></i><span class="hide-menu">Home</span>', array('controller' => 'Dashboard', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                        </li>
                        <?php if(in_array('Student',$session_usertype)) { ?>
                        <li class="nav-item <?php
                            if ($contr == 'Users') {
                                echo 'active';
                            }
                            ?>">            
                                <?= $this->Html->link('<i class="fal fa-user-graduate"></i><span class="hide-menu">My Profile</span>', array('controller' => 'Users', 'action' => 'myProfile'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                        </li>
                        <?php } else if(in_array('Staff',$session_usertype)) { ?>
                        <li class="nav-item  <?php
                            if ($act == 'staffList' || $act == 'editStaff' || $act == 'viewStaff' || $act == 'addStaff' || $act == 'staffRecycle') {
                                echo 'active';
                            }
                            ?>">            
                        <?= $this->Html->link('<i class="fal fa-users"></i><span class="hide-menu">Staff</span>', array('controller' => 'Users', 'action' => 'staffList'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                        </li>
                        <?php } else if(in_array('Teacher',$session_usertype)) { ?>
                            <li class="nav-item <?php
                                if (($contr == 'Users' && $act == 'index') || ($contr == 'Users' && $act == 'add') || ($contr == 'Users' && $act == 'edit') || ($contr == 'Users' && $act == 'view') || ($contr == 'Users' && $act == 'recycle')) {
                                    echo 'active';
                                }
                                ?>">            
                                    <?= $this->Html->link('<i class="fal fa-user-graduate"></i><span class="hide-menu">Students</span>', array('controller' => 'Users', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                            </li>
                            <li class="nav-item  <?php
                            if ($act == 'teacherList' || $act == 'editTeacher' || $act == 'viewTeacher' || $act == 'addTeacher' || $act == 'teacherRecycle') {
                                echo 'active';
                            }
                            ?>">            
                                <?= $this->Html->link('<i class="fal fa-chalkboard-teacher"></i><span class="hide-menu">Academic Personnel</span>', array('controller' => 'Users', 'action' => 'teacherList'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                            </li>
                        <?php } ?>
                        <li class="nav-item <?php
                            if ($contr == 'Audit') {
                                echo 'active';
                            }
                            ?>">            
                            <?= $this->Html->link('<i class="fal fa-chart-line"></i><span class="hide-menu">Activities</span>', array('controller' => 'Audit', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                        </li>
                        <li class="nav-item  <?php
                            if ($contr == 'Sclasses') {
                                echo 'active';
                            }
                                ?>">
                                  <?php if(in_array('Student',$session_usertype))
                                  {
                                     echo $this->Html->link('<i class="fal fa-users-class"></i><span class="hide-menu">Classes</span>', array('controller' => 'Sclasses', 'action' => 'attendance'), array('class' => 'waves-effect waves-dark', 'escape' => false));

                                  }else
                                  {
                                     echo $this->Html->link('<i class="fal fa-users-class"></i><span class="hide-menu">Class Attendance</span>', array('controller' => 'Sclasses', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false));
                                  }?>
                        </li>
                        <li class="nav-item <?php
                            if ($contr == 'AttendanceReport') {
                                echo 'active';
                            }
                            ?>">
                            <?= $this->Html->link('<i class="fal fa-clipboard-user"></i><span class="hide-menu">Attendance Report</span>', array('controller' => 'AttendanceReport', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                        </li>
                        <?php if(in_array('Student',$session_usertype) || in_array('Teacher',$session_usertype)) { ?>
                        <!-- <li class="nav-item <?php
                            //if ($contr == 'ClassAttends') {
                            //    echo 'active';
                            //}
                            ?>">            
                            <?php  //echo $this->Html->link('<i class="fal fa-clipboard-user"></i><span class="hide-menu">Class Attendance</span>', array('controller' => 'ClassAttends', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                        </li> -->
                        <li class="nav-item <?php
                            if ($contr == 'GateUsers') {
                                echo 'active';
                            }
                            ?>">            
                            <?= $this->Html->link('<i class="fal fa-torii-gate"></i><span class="hide-menu">Gates</span>', array('controller' => 'GateUsers', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                        </li>
                        <li class="nav-item  <?php
                            if ($contr == 'Messaging') {
                                echo 'active';
                            }
                            ?>">            
                            <?= $this->Html->link('<i class="fal fa-comment-alt"></i><span class="hide-menu">Messaging</span>', array('controller' => 'Messaging', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                        </li>
                        <?php } ?>

                        <?php } ?>
                        <?php if(in_array('Admin',$session_usertype)) { ?>

                        <li class="nav-item  <?php
                                if ($contr == 'Dashboard') {
                                    echo 'active';
                                }
                                    ?>">            
                                <?= $this->Html->link('<i class="fal fa-columns"></i><span class="hide-menu">Dashboard</span>', array('controller' => 'Dashboard', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                             </li>

                             <li class="nav-item  <?php
                                if ($contr == 'Management') {
                                    echo 'active';
                                }
                                    ?>">            
                                <?= $this->Html->link('<i class="fal fa-user"></i><span class="hide-menu">Users</span>', array('controller' => 'Management', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                             </li>

                            <!--<li class="nav-item  <?php
                                if ($act == 'adminList' || $act == 'editAdmin' || $act == 'viewAdmin' || $act == 'addAdmin' || $act == 'adminRecycle') {
                                    echo 'active';
                                }
                                ?>">            
                            <?= $this->Html->link('<i class="fal fa-users-crown"></i><span class="hide-menu">Admin List</span>', array('controller' => 'Users', 'action' => 'adminList'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                            </li>-->

                           
                            <li class="nav-item  <?php
                                if ($act == 'staffList' || $act == 'editStaff' || $act == 'viewStaff' || $act == 'addStaff' || $act == 'staffRecycle') {
                                    echo 'active';
                                }
                                ?>">            
                            <?= $this->Html->link('<i class="fal fa-users"></i><span class="hide-menu">Staff</span>', array('controller' => 'Users', 'action' => 'staffList'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                            </li>
                            

                            <li class="nav-item  <?php
                            if ($act == 'teacherList' || $act == 'editTeacher' || $act == 'viewTeacher' || $act == 'addTeacher' || $act == 'teacherRecycle') {
                                echo 'active';
                            }
                            ?>">            
                                <?= $this->Html->link('<i class="fal fa-chalkboard-teacher"></i><span class="hide-menu">Academic Personnel</span>', array('controller' => 'Users', 'action' => 'teacherList'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                            </li>

                            

                            <li class="nav-item  <?php
                            
                            if (($contr == 'Users' && $act == 'index') || ($contr == 'Users' && $act == 'add') || ($contr == 'Users' && $act == 'edit') || ($contr == 'Users' && $act == 'view') || ($contr == 'Users' && $act == 'recycle')) {
                                echo 'active';
                            }
                                ?>">            
                        <?= $this->Html->link('<i class="fal fa-user-graduate"></i><span class="hide-menu">Students</span>', array('controller' => 'Users', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                            </li>

                            <!--<li class="nav-item  <?php
                            
                            if ($contr == 'Invitationlist' && $act == 'index') {
                                echo 'active';
                            }
                                ?>">            
                        <?= $this->Html->link('<i class="fal fa-user-plus"></i><span class="hide-menu">Invitation List</span>', array('controller' => 'Invitationlist', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                            </li>-->

                            <li class="nav-item  <?php
                            if ($contr == 'IDCard') {
                                echo 'active';
                            }
                            ?>">
                            <?= $this->Html->link('<i class="fal fa-id-card"></i><span class="hide-menu">IDs</span>', array('controller' => 'IDCard', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                            </li>

                            <li class="nav-item  <?php
                                if ($contr == 'Eform') {
                                    echo 'active';
                                }
                                ?>">            
                            <?= $this->Html->link('<i class="fab fa-wpforms"></i><span class="hide-menu">eForms</span>', array('controller' => 'Eform', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                            </li>
                            
                            <!--<li class="nav-item  <?php
                                if ($contr == 'EformResponse') { 
                                    echo 'active';
                                }
                                ?>">            
                            <?= $this->Html->link('<i class="fab fa-wpforms"></i><span class="hide-menu">eForms Responses</span>', array('controller' => 'EformResponse', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                            </li>-->

                            <!--<li class="nav-item  <?php
                            if ($contr == 'Locations') {
                                echo 'active';
                            }
                            ?>">
                                <?= $this->Html->link('<i class="fal fa-location-circle"></i><span class="hide-menu">Locations</span>', array('controller' => 'Locations', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                            </li>-->

                            <li class="nav-item  <?php
                            if ($contr == 'Sclasses') {
                                echo 'active';
                            }
                                ?>">            
    <?= $this->Html->link('<i class="fal fa-users-class"></i><span class="hide-menu">Classes</span>', array('controller' => 'Sclasses', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                            </li>

                            <li class="nav-item <?php
                                if ($contr == 'AttendanceReport') {
                                    echo 'active';
                                }
                                ?>">            
                                <?= $this->Html->link('<i class="fal fa-clipboard-user"></i><span class="hide-menu">Attendance Report</span>', array('controller' => 'AttendanceReport', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                            </li>

                            <li class="nav-item  <?php
                                                        if ($contr == 'AccessControl') {
                                                            echo 'active';
                                                        }
                                                        ?>">
                                                        <?= $this->Html->link('<i class="fal fa-id-card"></i><span class="hide-menu">Access Control</span>', array('controller' => 'AccessControl', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                                                        </li>
                            
                            <li class="nav-item  <?php
                                if ($contr == 'Messaging') {
                                    echo 'active';
                                }
                                ?>">            
                                <?= $this->Html->link('<i class="fal fa-comment-alt"></i><span class="hide-menu">Messaging</span>', array('controller' => 'Messaging', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                            </li>

                            <li class="nav-item  <?php
                                if ($contr == 'Notifications') {
                                    echo 'active';
                                }
                                ?>">            
                            <?= $this->Html->link('<i class="fal fa-bell"></i><span class="hide-menu">Notifications</span>', array('controller' => 'Notifications', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                            </li>

                            <li class="nav-item  <?php
                        if ($contr == 'Audit') {
                            echo 'active';
                        }
                            ?>">            
                            <?= $this->Html->link('<i class="fal fa-chart-line"></i><span class="hide-menu">Logs</span>', array('controller' => 'Audit', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                            </li>





                            <li class="nav-item  <?php
                        if ($contr == '#') {
                            echo 'active';
                        }
                            ?>">            
                            <?= $this->Html->link('<i class="fal fa-cogs"></i><span class="hide-menu">Settings</span>', array('controller' => 'Pages', 'action' => 'display', 'Settings'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                            </li>

                            <!--<li class="nav-item  <?php
                        if ($contr == 'Users' && $act == 'list') {
                            echo 'active';
                        }
                        ?>">            
                        <?= $this->Html->link('<i class="material-icons">home</i><span class="hide-menu">Users Management</span>', array('controller' => 'Users', 'action' => 'list'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                                                </li>

                                                <li class="nav-item  <?php
                        if ($contr == 'Gates') {
                            echo 'active';
                        }
                        ?>">            
                        <?= $this->Html->link('<i class="material-icons">vpn_key</i><span class="hide-menu">Gates</span>', array('controller' => 'Gates', 'action' => 'index'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                                                </li>

                                                <li class="nav-item  <?php
                        if ($contr == '#') {
                            echo 'active';
                        }
                        ?>">            
                        <?= $this->Html->link('<i class="material-icons">bar_chart</i><span class="hide-menu">Reports</span>', array('controller' => 'Pages', 'action' => 'display', 'Reports'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                                                </li>

                                                <li class="nav-item  <?php
                        if ($contr == '#') {
                            echo 'active';
                        }
                        ?>">            
                        <?= $this->Html->link('<i class="material-icons">meeting_room</i><span class="hide-menu">CLS</span>', array('controller' => 'Pages', 'action' => 'display', 'CLS'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                                                </li>
                                                <li class="nav-item  <?php
                        if ($contr == '#') {
                            echo 'active';
                        }
                        ?>">            
                        <?= $this->Html->link('<i class="material-icons">people_outline</i><span class="hide-menu">Moodle</span>', array('controller' => 'Pages', 'action' => 'display', 'Moodle'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                                                </li>

                                                <li class="nav-item  <?php
                        if ($contr == '#') {
                            echo 'active';
                        }
                        ?>">            
                        <?= $this->Html->link('<i class="material-icons">flag</i><span class="hide-menu">Banner</span>', array('controller' => 'Pages', 'action' => 'display', 'Banner'), array('class' => 'waves-effect waves-dark', 'escape' => false)); ?>
                            </li>-->
                        
                        <?php } ?>                        
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="row page-titles">
                <div class="col-md-5 align-self-center">
                    <!--<h3 class="text-themecolor">Dashboard</h3>-->
                    <h3 class="text-themecolor"><?= $page_icon ?><?= $page_title ?></h3>
                </div>
                <div class="col-md-7 align-self-center">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                        <!--<li class="breadcrumb-item active">Dashboard</li>-->
                        <li class="breadcrumb-item active"><?= $page_title ?></li>
                    </ol>
                </div>
                <!--<div>
                    <button class="right-side-toggle waves-effect waves-light btn-inverse btn btn-circle btn-sm pull-right m-l-10"><i class="ti-settings text-white"></i></button>
                </div>-->
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <?= $this->Flash->render() ?>
                <?= $this->fetch('content') ?>
                <!-- ============================================================== -->
                <!-- End PAge Content -->
                <!-- ============================================================== -->
                
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <footer class="footer">
                <span class="copy-right">&#169;</span>
                <script>document.write(new Date().getFullYear())</script>
                <span>AKcess Labs Ltd.</span>

		<span class="version" style="margin-left: 73%;"><?php echo VERSION;?></span>

               
            </footer>
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->

    <!-- Role confirmation modal start -->
    <div id="role_confirm_modal" class="modal " tabindex="-1" role="dialog" aria-hidden="true" style="z-index:9999 !important">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm">
            <div class="modal-content" >
                <div class="modal-body">
                    <p class="text-center">This ID has another role, are you sure you want to add a new role and update?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary waves-effect" id="yes_role_confirm_btn">Yes</button>
                    <button class="btn btn-info waves-effect" id="no_role_confirm_btn" data-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Role confirmation modal end -->
    
    <!-- ============================================================== -->
    <!-- This page plugins -->
    <!-- ============================================================== -->
    <!--morris JavaScript -->
    <!--<script src="../new-assets/plugins/raphael/raphael-min.js"></script>
    <script src="../new-assets/plugins/morrisjs/morris.min.js"></script>-->
    <!-- Chart JS -->
    <!--<script src="../new-js/dashboard1.js"></script>-->
    <!-- ============================================================== -->
    <!-- Style switcher -->
    <!-- ============================================================== -->
    <!--<script src="../new-assets/plugins/styleswitcher/jQuery.style.switcher.js"></script>-->

    <!--   Core JS Files   -->
  <!--<?= $this->Html->script('js/core/jquery.min') ?>
  <?= $this->Html->script('js/core/popper.min') ?>-->
  <!--jQuery sidebar -->
  <?= $this->Html->script('../new-assets/plugins/jquery/jquery.min.js') ?>
  <!-- Bootstrap tether Core JavaScript -->
  <?= $this->Html->script('../new-assets/plugins/popper/popper.min.js') ?>
  <?= $this->Html->script('../new-assets/plugins/bootstrap/js/bootstrap.min.js') ?>
  <!-- slimscrollbar scrollbar JavaScript -->
  <?= $this->Html->script('../new-js/jquery.slimscroll.js') ?>
  <!--Wave sidebar -->
  <?= $this->Html->script('../new-js/waves.js') ?>
  <!--Menu sidebar -->
  <?= $this->Html->script('../new-js/sidebarmenu.js') ?>
  <!--stickey kit -->
  <?= $this->Html->script('../new-assets/plugins/sticky-kit-master/dist/sticky-kit.min.js') ?> 
  <!--sparkline JavaScript -->
  <?= $this->Html->script('../new-assets/plugins/sparkline/jquery.sparkline.min.js') ?>  
   <!--Custom JavaScript -->
  <?= $this->Html->script('../new-js/custom.min.js') ?>   
   <!-- Editable -->
   <?= $this->Html->script('../new-assets/plugins/jsgrid/db.js') ?>
   <?= $this->Html->script('../new-assets/plugins/jsgrid/jsgrid.min.js') ?>
   <?= $this->Html->script('../new-js/jsgrid-init.js') ?>
   <!-- Chart JS -->
   <!-- <script src="../new-js/dashboard1.js"></script>     -->
   <!-- This is data table -->    
   <!-- <?= $this->Html->script('../new-assets/plugins/datatables/jquery.dataTables.min.js') ?> -->
   <?= $this->Html->script('https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js') ?>
   <?= $this->Html->script('https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js') ?>
   <?= $this->Html->script('https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js') ?>
   <?= $this->Html->script('https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js') ?>

   <?= $this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js') ?>
   <?= $this->Html->script('https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js') ?>

    <?php if ($act == 'classAttendance') { ?>
        <?= $this->Html->script('../new-assets/plugins/datatables/js/buttons.html5.styles.js') ?>
        <?= $this->Html->script('../new-assets/plugins/datatables/js/buttons.html5.styles.templates.js') ?>
    <?php } ?>

   <!-- Style switcher -->
   <?= $this->Html->script('../new-assets/plugins/styleswitcher/jQuery.style.switcher.js') ?>
   <!-- Theme JS  -->
   <?= $this->Html->script('../new-assets/plugins/switchery/dist/switchery.min.js') ?>
   <?= $this->Html->script('../new-assets/plugins/select2/dist/js/select2.full.min.js') ?>
   <?= $this->Html->script('../new-assets/plugins/bootstrap-select/bootstrap-select.min.js') ?>
   <?= $this->Html->script('../new-assets/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') ?>
   <?= $this->Html->script('../new-assets/plugins/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.js') ?>
   <?= $this->Html->script('../new-assets/plugins/dff/dff.js') ?>
   <?= $this->Html->script('../new-assets/plugins/multiselect/js/jquery.multi-select.js') ?>


  <!--Custom Created JS -->
    <!-- Plugin for the momentJs  -->
    <?= $this->Html->script('js/plugins/moment.min') ?>
    <!--  Plugin for Sweet Alert -->
    <!-- <?= $this->Html->script('js/plugins/sweetalert2') ?> -->

    <!-- Forms Validations Plugin -->
    <?= $this->Html->script('js/plugins/jquery.validate.min') ?>
    <!--  Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
    <?= $this->Html->script('js/plugins/bootstrap-selectpicker') ?>
    <!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
    <?= $this->Html->script('js/plugins/bootstrap-datetimepicker.min') ?>
    <!--  DataTables.net Plugin, full documentation here: https://datatables.net/  -->
    <!-- <?= $this->Html->script('js/plugins/jquery.dataTables.min') ?> -->
    <!--  Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
    <?= $this->Html->script('js/plugins/bootstrap-tagsinput') ?>

    <!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
    <?= $this->Html->script('js/plugins/jasny-bootstrap.min') ?>

    <!-- Library for adding dinamically elements -->
    <?= $this->Html->script('js/plugins/arrive.min') ?>

    <!-- Chartist JS -->
    <?= $this->Html->script('js/plugins/chartist.min') ?>

    <!--  Notifications Plugin    -->
    <?= $this->Html->script('js/plugins/bootstrap-notify') ?>

        <!--<?= $this->Html->script('demo/demo.js') ?> -->

    <!-- <?= $this->Html->script('demo/datatables.min') ?> -->

    <?= $this->Html->script('demo/multiselect/jquery.multiselect') ?>
    
    <?= $this->Html->script('demo/jquery.tokenize') ?>
    
    <?= $this->Html->script('demo/toastr.min') ?>
    
    <?= $this->Html->script('demo/toastr') ?>
    
    <?= $this->Html->script('demo/jquery-ui') ?>
    <!-- On Load jquery custom file -->
    <?= $this->Html->script('demo/custom.js') ?>
    <?= $this->Html->script('demo/api.js') ?>
    <?php if ($contr == 'Sclasses') { ?>
    <?= $this->Html->script('demo/sclass.js') ?>
    <?php } ?>

    <!--Morris JavaScript -->
   <?= $this->Html->script('../new-assets/plugins/raphael/raphael-min.js') ?>
   <?= $this->Html->script('../new-assets/plugins/morrisjs/morris.min.js') ?>

    <?php if (($contr == 'Sclasses' || $contr == 'Gates' || $contr == 'AccessControl') && $act == 'view' || $act == 'guestPassView') { ?>
    <?= $this->Html->script('js/qrcode/qrcode') ?>
    <script type="text/javascript">
        if (typeof (document.getElementById("text")) != 'undefined' && document.getElementById("text") != null) {
            var qrcode = new QRCode(document.getElementById("qrcode"), {
                width: 200,
                height: 200
            });
            qrcode.makeCode($('#text').val());
        }

        if (typeof (document.getElementById("qrcodeclassestext")) != 'undefined' && document.getElementById("qrcodeclassestext") != null) {
            var qrcodeclasses = new QRCode(document.getElementById("qrcodeclasses"), {
                width: 200,
                height: 200
            });
            qrcodeclasses.makeCode($('#qrcodeclassestext').val());
        }
    </script>
  <?php } ?>
  <script type="text/javascript">
      $(function () {
            if (typeof (document.getElementById("student_dob")) != 'undefined' && document.getElementById("student_dob") != null) {
                $('#student_dob').datetimepicker({
                    format: 'DD/MM/YYYY',
                    maxDate: 'now'
                });
            }

            if (typeof (document.getElementById("adminssion_date")) != 'undefined' && document.getElementById("adminssion_date") != null) {
                $('#adminssion_date').datetimepicker({
                    format: 'DD/MM/YYYY'
                });
            }

            if (typeof (document.getElementById("manage_users_multiple")) != 'undefined' && document.getElementById("manage_users_multiple") != null) {
                $('#manage_users_multiple').select2({
                    placeholder: "Select a student"
                });
            }

            if (typeof (document.getElementById("manage_users_multiple_points")) != 'undefined' && document.getElementById("manage_users_multiple_points") != null) {
                $('#manage_users_multiple_points').select2({
                    placeholder: "Select a user"
                });
            }

 if (typeof (document.getElementById("inlineRadio2")) != 'undefined' && document.getElementById("inlineRadio2") != null) {
               $(document).on('click','#inlineRadio2',function()
               {
                  $('.invitation-country-list').select2({
                                                   placeholder: "Select Country Code",
                                               });

               });
            }

      $('.invitation-eForm-list').select2({
                          placeholder: "Select eForm",
                   });

            if (typeof (document.getElementById("guest-pass-date")) != 'undefined' && document.getElementById("guest-pass-date") != null) {
                $('#guest-pass-date').datetimepicker({
                    format: 'DD/MM/YYYY',
                    minDate:new Date()
                });
            }
            
      });
   </script>
</body>
</html>
