<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */

use Cake\Datasource\ConnectionManager;

$session_usertype = explode(",", $session_user['usertype']);

?>
<?php if(in_array('Admin',$session_usertype)) { ?>
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-4">Today total Check In / Check Out Users On campus</h5>
                    <div class="row">
                        <div class="col-6 col-sm-3 text-center">
                            <div class="border border-primary p-2 rounded mb-2">
                                <h2 class="m-0 text-primary">
                                    <i class="fal fa-user-graduate"></i>
                                </h2>
                                <h5 class="m-0">
                                    <?= $count_students_checkin_today . " / " . $count_students_checkin_today ?>
                                </h5>
                            </div>
                            <p class="counter-subtitle">Student</p>
                        </div>
                        <div class="col-6 col-sm-3 text-center">
                            <div class="border border-primary p-2 rounded mb-2">
                                <h2 class="m-0 text-primary">
                                    <i class="fal fa-users-crown"></i> 
                                </h2>
                                <h5 class="m-0">
                                    <?= $count_admin_checkin_today . " / " . $count_admin_checkout_today ?>
                                </h5>
                            </div>
                            <p class="counter-subtitle">Admin</p>
                        </div>                
                        <div class="col-6 col-sm-3 text-center">
                            <div class="border border-primary p-2 rounded mb-2">
                                <h2 class="m-0 text-primary">
                                    <i class="fal fa-users"></i> 
                                </h2>
                                <h5 class="m-0">
                                    <?= $count_staff_checkin_today . " / " . $count_staff_checkout_today ?>
                                </h5>
                            </div>
                            <p class="counter-subtitle">Staff</p>
                        </div>
                        <div class="col-6 col-sm-3 text-center">
                            <div class="border border-primary p-2 rounded mb-2">
                                <h2 class="m-0 text-primary">
                                    <i class="fal fa-chalkboard-teacher"></i> 
                                </h2>
                                <h5 class="m-0">
                                    <?= $count_teacher_checkin_today . " / " . $count_teacher_checkout_today ?>
                                </h5>
                            </div>
                            <p class="counter-subtitle">Academic personnel</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-4">Today total eForms sent/received</h5>
                    <div class="row">
                        <div class="col-6 text-center">
                            <div class="border border-primary p-2 rounded mb-2">
                                <h2 class="m-0 text-primary">
                                    <i class="fab fa-wpforms"></i>
                                </h2>
                                <h5 class="m-0">
                                    <?= $count_eform_sent_today ?>
                                </h5>
                            </div>
                            <p class="counter-subtitle">Sent</p>
                        </div>

                        <div class="col-6 text-center">
                            <div class="border border-primary p-2 rounded mb-2">
                                <h2 class="m-0 text-primary">
                                    <i class="fab fa-wpforms"></i>
                                </h2>
                                <h5 class="m-0">
                                    <?= $count_eform_received_today ?>
                                </h5>
                            </div>
                            <p class="counter-subtitle">Received</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-xlg-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Students waiting for approval.</h6>
                    <div class="table-responsive m-t-10">
                        <table id="example1" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <?php if(in_array('Admin',$session_usertype)) { ?>
                                    <th scope="col">User Type</th>
                                    <?php } ?>
                                    <th>AKcess ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile Number</th>
                                    <th class="text-center"><?= __('Actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $k = 0; foreach ($users as $user): ?>
                                    <?php $user_id = ConnectionManager::userIdEncode($user->id); ?>
                                        <tr>
                                            <td><?= $this->Number->format($user->id) ?></td>
                                            <?php if(in_array('Admin',$session_usertype)) { ?>
                                            <td><?= h($user->usertype) ?></td>
                                            <?php } ?>
                                            <td>
                                                <?= $this->Html->link(__($user->akcessId), ['action' => 'view', $user_id], ['escape' => false]) ?> 
                                            </td> 
                                            <td>
                                                <?= $this->Html->link(__($user->name), ['action' => 'view', $user_id], ['escape' => false]) ?> 
                                            </td>                                       
                                            <td><?= h($user->email) ?></td>
                                            <td><?= h($user->mobileNumber) ?></td>
                                            <td class="actions">
                                                <div class="actions-div">

                                                <?= $this->Html->link(__('<i class="fa fa-thumbs-up purple-txt"></i>'), ['controller' => 'users', 'action' => 'approve', $user_id], ['escape' => false , 'class' => 'approve_btn', 'data-link' => 'approve_btn' . $user_id,'data-attr' => $user->name, 'data-toggle' => "tooltip", 'title'=>'Approve']) ?>

                                                <?= $this->Form->postLink(__('Approve'), ['controller' => 'users', 'action' => 'approve', $user_id], ['escape' => false, 'id' => 'approve_btn' . $user_id, 'style' => 'display:none;']) ?>

                                                <?= $this->Html->link(__('<i class="fa fa-eye"></i>'), ['controller' => 'users', 'action' => 'view', $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'View']) ?>

                                                <?php if(in_array('Admin',$session_usertype)) { ?>
                                                    <?= $this->Html->link(__('<i class="fa fa-edit green-txt"></i>'), ['controller' => 'users', 'action' => 'edit', $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Edit']) ?>

                                                    <?= $this->Html->link(__('<i class="fa fa-trash red-txt"></i>'), ['controller' => 'users', 'action' => 'delete', $user_id], ['escape' => false, 'class' => 'delete_btn', 'data-link' => 'remove_btn' . $user_id, 'data-toggle' => "tooltip", 'title'=>'Reject','data-attr' => $user->name]) ?> 

                                                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'users', 'action' => 'delete', $user_id], ['escape' => false, 'id' => 'remove_btn' . $user_id, 'style' => 'display:none;']) ?>
                                                    <?php } ?>    

                                                </div>
                                            </td>
                                    </tr>
                                    <?php $k++; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
<div class="row">
    <div class="col-md-4">
        <div class="card card-chart">
            <div class="card-header card-header-success">
                <div class="ct-chart" id="dailySalesChart"></div>
            </div>
            <div class="card-body">
                <h4 class="card-title">Class Attendance</h4>
                <p>This is your class attendance history</p>
            </div>
            <div class="card-footer">
                <div class="stats">
                    <i class="material-icons">access_time</i> Last Updated <?php echo date('m/d/Y'); ?>
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
                <p>This is your gate entries history.</p>
            </div>
            <div class="card-footer">
                <div class="stats">
                    <i class="material-icons">access_time</i> Last Updated <?php echo date('m/d/Y'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<div id="approve_modal" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <p class="text-center textname"></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary waves-effect" id="yes_approve_btn">Yes</button>
                <button class="btn btn-info waves-effect" id="no_approve_btn" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<div id="delete_modal" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <p class="text-center reject_div">Are you sure you want to reject this user?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary waves-effect" id="yes_btn">Yes</button>
                <button class="btn btn-info waves-effect" id="no_btn" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<!-- Loader -->
<div id="load" class="ajax-loader">
    <div class="ajax-loader-box">
        <div class="row">
            <div class="col-12">
                <div class="fa-3x">
                    <i class="fa fa-spinner fa-spin"></i>          
                </div>          
            </div>
        </div>
    </div>
</div> 

<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<script>
$('#main-wrapper').on('click', 'a.approve_btn', function(e) {
        e.preventDefault();
        //alert(1);return false;
        $('.textname').html('');
        $("#approve_modal").modal('show');
        var hl = $(this).attr('data-link');
        var hname = $(this).attr('data-attr');
        //alert(hl);
        $('.textname').html('are you sure you want to approve '+hname+'?');
        $("#yes_approve_btn").click(function() {
            //$('#remove_btn_form').attr('action', hl);
            $('#' + hl).click();
        });
        $("#no_approve_btn").click(function(e) {
            $("#approve_modal").modal('hide');
            return false;

        });

    });
    
    $('#main-wrapper').on('click', 'a.delete_btn', function(e) {
        e.preventDefault();
        //alert(1);
        var hl = $(this).attr('data-link');
        var hname = $(this).attr('data-attr');
        $('.reject_div').html('Are you sure you want to reject '+hname+'?');
        $("#delete_modal").modal('show');
        
        //alert(hl);
        $("#yes_btn").click(function() {
            
            $('#' + hl).click();
        });
        $("#no_btn").click(function(e) {
            $("#delete_modal").modal('hide');
            return false;

        });

    });
</script>
