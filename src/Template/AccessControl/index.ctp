<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Sclass[]|\Cake\Collection\CollectionInterface $sclasses
 */
use Cake\Datasource\ConnectionManager;

$url = BASE_ORIGIN_URL;

$session_usertype = explode(",", $session_user['usertype']);
$session_role_id = explode(",", $role_id);
?>
<div class="staff-list-page">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive m-t-10">
                    <div class="row float-right m-0">
                        <?php if(in_array('Admin',$session_usertype) || in_array('Teacher',$session_usertype)){ ?>
                            <h4><?php echo $this->Html->link(__('<i class="fas fa-plus mr-1"></i> Add'), ['action' => 'add'], ['class' => 'btn waves-effect waves-light btn-primary', 'escape' => false]); ?></h4>
                        <?php } ?>
                        <h4><?php echo $this->Html->link(__('<i class="fas fa-location-circle mr-1"></i> Locations'), ['action' => '../locations'], ['class' => 'btn waves-effect waves-light btn-info ml-3', 'escape' => false]) ?></h4>
                        <?php if(in_array('Admin',$session_role_id)){ ?>
                            <h4><?= $this->Html->link(__('<i class="fa fa-recycle"></i>'), ['action' => 'classes-recycle'], ['class' => 'ml-3', 'escape' => false, 'data-toggle' => "tooltip", 'title'=>'Recycle']) ?></h4>
                        <?php } ?>
                    </div>
                        <table id="class-example1" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Days</th>
                                    <th>Open From</th>
                                    <th>Open To</th>
                                    <th>Location</th>
                                    <th class="text-center"><?= __('Actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $k = 0;
                                foreach ($sclasses as $key => $sclass): ?>
                                <?php $id = ConnectionManager::userIdEncode($sclass['id']); ?>
                                <tr>
                                    <td><?= $this->Number->format($key+1) ?></td>
                                    <td><?= h($sclass['sname']) ?></td>
                                    <?php
                                        $iCount = 0;
                                        $days = "";
                                        $from = explode(',', $sclass['days']);
                                        foreach ($from as $iNum) {
                                            $days .= $iNum . '<br />';
                                        }
                                    ?>
                                    <td><?= $days ?></td>
                                    <?php
                                        $iCount = 0;
                                        $openFrom = "";
                                        $from = explode(',', $sclass['openFrom']);
                                        foreach ($from as $iNum) {
                                            $openFrom .= $iNum . '<br />';
                                        }
                                    ?>
                                    <td><?php echo $openFrom; ?></td>
                                    <?php
                                        $iCount = 0;
                                        $openTo = "";
                                        $from = explode(',', $sclass['openTo']);
                                        foreach ($from as $iNum) {
                                            $openTo .= $iNum . '<br />';
                                        }
                                    ?>
                                    <td><?= $openTo ?></td>
                                    <td><?= h($sclass['lname']) ?></td>
                                    <td class="actions">
                                        <div class="actions-div">

                                            <?php if(in_array('Admin',$session_usertype)){ ?>

                                                <?= $this->Html->link(__('<i class="fa fa-user-plus" aria-hidden="true"></i>'), ['action' => 'manageStudents', $id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Manage Users']) ?>

                                                <?= $this->Html->link(__('<i class="fal fa-chart-line" aria-hidden="true"></i>'), ['action' => 'attendanceReport', $id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Attendance Report']) ?>

                                            <?php } ?>

                                            <?= $this->Html->link(__('<i class="fa fa-eye" aria-hidden="true"></i>'), ['action' => 'view', $id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'View']) ?>

                                            <?php if(in_array('Admin',$session_usertype)){ ?>

                                            <?= $this->Html->link(__('<i class="fa fa-edit green-txt" aria-hidden="true"></i>'), ['action' => 'edit', $id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Edit']) ?>

                                            <?= $this->Html->link(__('<i class="fa fa-trash red-txt" aria-hidden="true"></i>'), ['action' => 'delete', $id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Remove', 'class' => 'delete_btn', 'data-link' => 'remove_btn'.$id] ) ?>

                                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $id], ['escape' => false, 'id' => 'remove_btn'.$id, 'style' => 'display:none;']  ) ?>

                                            <?php } ?>
                                        </div>
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

    <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>Guest Pass</h4>
                        <div class="table-responsive">
                            <div class="row float-right m-0">
                                <?php if(in_array('Admin',$session_usertype)) { ?>
                                    <h4><a href="javascript:void(0)" class="btn waves-effect waves-light btn-primary" id="add-guest-pass"><i class="fas fa-plus mr-1"></i> Generate Guest Pass</a></h4>
                                <?php } ?>
                            </div>
                            <table id="guestPassTable" class="table table-hover table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Email</th>
                                        <th>Location</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Purpose</th>
                                        <th class="text-center"><?= __('Actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $k = 0;
                                if(isset($guest_pass_list) && !empty($guest_pass_list))
                                {
                                foreach ($guest_pass_list as $key => $g):

                                $country_code = '';
                                if(isset($g['country_code']) && $g['country_code'])
                                {
                                    $country_code = '+'.$g['country_code'];
                                }
                                ?>
                                <?php $id = ConnectionManager::userIdEncode($g['id']); ?>
                                    <tr>
                                        <td><?= $this->Number->format($key+1) ?></td>
                                        <td><?= h($g['first_name'].' '.$g['last_name']) ?></td>

                                        <td><?= isset($g['mobile']) ? $country_code.$g['mobile'] : '-';?></td>
                                        <td><?= isset($g['email']) ? $g['email'] : '-';?></td>
                                        <td><?= isset($g['location_name']) ? $g['location_name'] : '-';?></td>
                                        <td><?= isset($g['guest_pass_date']) && $g['guest_pass_date'] ? date('d/m/Y',strtotime($g['guest_pass_date'])) : '-';?></td>
                                        <td><?= isset($g['guest_pass_time']) && $g['guest_pass_time'] ? $g['guest_pass_time']  : '-';?></td>
                                        <td><?= isset($g['purpose']) ? $g['purpose'] : '-';?></td>
                                        <td class="actions">
                                            <div class="actions-div">
                                                <?= $this->Html->link(__('<i class="fa fa-eye" aria-hidden="true"></i>'), ['action' => 'guestPassView', $id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'View']) ?>
                                                <a href="javascript:void(0);" data-toggle = "tooltip" title='Edit' data-id="<?= $id ?>" class="edit-guest-pass"><i class="fa fa-edit green-txt" aria-hidden="true"></i></a>
                                                <?= $this->Html->link(__('<i class="fa fa-trash red-txt" aria-hidden="true"></i>'), ['action' => 'guest_pass_delete', $id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Remove', 'class' => 'delete_guest_pass_btn', 'data-link' => 'remove_btn'.$id] ) ?>
                                                <?= $this->Form->postLink(__('Delete'), ['action' => 'guest_pass_delete', $id], ['escape' => false, 'id' => 'remove_btn'.$id, 'style' => 'display:none;']  ) ?>
                                                <button href="javascript:void(0);" data-toggle = "tooltip" title='Send guest pass' data-id="<?= $id ?>" data-email="<?= $g['email'] ?>" data-akcessId="<?= $g['akcessId'] ?>" class="sendGuestPassModalModuleBtn btn btn-sm waves-effect waves-primary btn-info mx-1">Send</button>
                                                <button type="button" data-toggle = "tooltip" title='Send guest pass'  data-title="<?php echo $id; ?>" onclick="viewSendGuestPassModalModule('<?php echo $id; ?>');" class="viewSendGuestPassModalModule btn btn-sm waves-effect waves-primary btn-info mx-1">View Sent</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                      $k++;
                                    endforeach;
                                } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>


<!-- Send Guest Pass Modal -->
<div class="modal " id="sendGuestPassModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form" id="SendGuestPassForm" method="POST" class="mb-0">
                <input type="hidden" id="guestPassId" name="idcardid" value="" />

                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Send Guest Pass</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center justify-content-around">
                        <div class="akcessId-option" style="display:none">
                            <input type="radio" name="inlineRadioOptions" id="viewReceivedInlineRadio3" value="ackess" class="with-gap radio-col-light-blue"/>
                            <label for="viewReceivedInlineRadio3">AKcess ID</label>
                        </div>
                        <div>
                            <input type="radio" checked name="inlineRadioOptions" id="viewReceivedInlineRadio1" value="email" class="with-gap radio-col-light-blue"/>
                            <label for="viewReceivedInlineRadio1">Email</label>
                        </div>
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewReceivedInlineRadio2" value="phone" class="with-gap radio-col-light-blue"/>
                            <label for="viewReceivedInlineRadio2">Phone</label>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="email_id" name="email" value="">
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary waves-effect sendGuestPassBtn" id="send1" >Send <span class="btn_text"></span></button>
                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Add Modal -->

 <div id="addGuestPassModal" class="modal " tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" >
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Generate Guest Pass</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                </div>
                <?= $this->Form->create($guestPass,
                                     array(
                                       'class'=>'mb-0',
                                       'data-toggle'=>'validator',
                                       'id' => 'guest-pass-form',
                                       'action'=> 'addGuestPass'
                                     )) ?>
                <div class="modal-body">
                          <div class="form-body">
                              <div class="row">

                               <div class="col-md-6">
                                  <div class="form-group">
                                        <?php echo $this->Form->input('akcessid', array('label' => ['text'=>'AKcess ID'], 'class' => 'form-control','placeholder'=>'Enter AKcess ID')); ?>
                                                                   </div>
                                                                </div>
                                                                                                  <div class="col-md-6">
                                                                                                      <div class="form-group">
                                                                                                          <?php echo $this->Form->input('invitee_name', array('label' => ['text'=>'Invitation from','class'=>'required'], 'class' => 'form-control','placeholder'=>'Enter invitation from')); ?>
                                                                                                      </div>
                                                                                                  </div>
                                  <div class="col-md-6">
                                     <div class="form-group">
                                         <?php echo $this->Form->input('first_name', array('label' => ['text'=>'First Name', 'class'=>'required'], 'class' => 'form-control','placeholder'=>'Enter first name')); ?>
                                     </div>
                                  </div>
                                  <div class="col-md-6">
                                      <div class="form-group">
                                          <?php echo $this->Form->input('last_name', array('label' => ['text'=>'Last Name', 'class'=>'required'], 'class' => 'form-control','placeholder'=>'Enter last name')); ?>
                                      </div>
                                  </div>

                                  <div class="col-md-6">
                                      <div class="form-group">
                                      <label class="required">Date</label>
                                         <div class='input-group date'>
                                             <input type='text' name="guestPassDate" id="guest-pass-date" placeholder="Please select date" class="form-control" />
                                             <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                             </span>
                                         </div>
                                         <label style="display:none" id="guest-pass-date-error" class="error" for="guest-pass-date"></label>
                                      </div>
                                  </div>
                                  <div class="col-md-6">
                                      <div class="form-group">
                                      <label class="required">Time</label>
                                          <div class='input-group date'>
                                              <input type='text' name="guestPassTime" id="guest-pass-time" placeholder="Please select time" class="form-control picktime openfrom-picktime" />
                                              <span class="input-group-addon">
                                                 <span class="glyphicon glyphicon-time"></span>
                                              </span>
                                          </div>
                                          <label style="display:none" id="guest-pass-error" class="error" for="guest-pass-time"></label>
                                      </div>
                                  </div>


                                  <div class="col-md-6">
                                      <div class="form-group">
                                          <label class='required'>Mobile</label>
                                          <div class='input-group date'>
                                             <select name="country" id="country" class="form-control" style="max-width: 150px;">
                                             <?php
                                             foreach($countries_list as $c){
                                                echo "<option value=".$c->calling_code.">".$c->country_name."</option>";
                                             } ?>
                                             <input type='text' name="mobile" id="mobile" value="" placeholder="Enter mobile number" class="form-control" />
                                          </div>
                                          <label style="display:none" id="guest-pass-date-error" class="error" for="guest-pass-date"></label>
                                      </div>
                                  </div>
                                  <div class="col-md-6">
                                      <div class="form-group">
                                          <?php echo $this->Form->input('email', array('label' => ['text'=>'Email','class'=>'required'], 'class' => 'form-control','placeholder'=>'Enter email address')); ?>
                                      </div>
                                  </div>
                                  <div class="col-md-12">
                                      <div class="form-group">
                                          <label class="required">Location of meeting</label>
                                          <select name="location" id="location" class="custom-select input-md blurclass">
                                              <option value="">Select Location</option>
                                              <?php
                                              foreach ($locations as $location) { ?>
                                                  <option value="<?= $location->id; ?>"><?php echo $location->name; ?></option>
                                              <?php } ?>
                                          </select>
                                      </div>
                                  </div>

                                  <div class="col-md-12">
                                      <div class="form-group">
                                      <label>Purpose of Invitation</label>
                                      <input type="text" name="purpose" id="purpose" class="form-control" value="" placeholder="Enter purpose of invitation">
                                      </div>
                                  </div>
                                  <div class="col-md-12">
                                      <div class="form-group">
                                          <label>Note</label>
                                          <textarea class="form-control" name="note" id="note" placeholder='Enter note for guest pass'></textarea>
                                      </div>
                                  </div>
                              </div>
                          </div>
                </div>
                <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                 <?= $this->Form->end() ?>
            </div>
        </div>
</div>



<div id="delete_modal" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <p class="text-center">Are you sure you want to delete this class?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary waves-effect" id="yes_btn">Yes</button>
                <button class="btn btn-info waves-effect" id="no_btn" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<div id="guest_pass_delete_modal" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <p class="text-center">Are you sure you want to delete this Guest Pass?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary waves-effect" id="g_yes_btn">Yes</button>
                <button class="btn btn-info waves-effect" id="g_no_btn" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<!-- Loader-->
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

<!-- View Guest Pass Modal -->
<div class="modal" id="viewSendGuestPassModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form"  id="ViewSendGuestPassData" method="POST" class="mb-0">
                <input type="hidden" id="vieweid" name="vieweid" value="" />
                <input type="hidden" id="viewType" name="viewType" value="guestpass" />
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">View Sent</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center justify-content-around">
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewSendGuestPassinlineRadio3"
                                value="viewackess" class="with-gap radio-col-light-blue" />
                            <label for="viewSendGuestPassinlineRadio3">AKcess ID</label>
                        </div>
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewSendGuestPassinlineRadio1"
                                value="viewemail" class="with-gap radio-col-light-blue" />
                            <label for="viewSendGuestPassinlineRadio1">Email</label>
                        </div>
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewSendGuestPassinlineRadio2"
                                value="viewphone" class="with-gap radio-col-light-blue" />
                            <label for="viewSendGuestPassinlineRadio2">Phone</label>
                        </div>
                    </div>
                    <div>
                        <div class="form-group viewackess">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped table-bordered" id="viewSentGuestPassAkcess">
                                    <thead>
                                        <tr>
                                            <th>AKcess ID</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="form-group viewemail">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped table-bordered" id="viewSentGuestPassEmail">
                                    <thead>
                                        <tr>
                                            <th>Email ID</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="form-group viewphone">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped table-bordered" id="viewSentGuestPassPhone">
                                    <thead>
                                        <tr>
                                            <th scope="col">Phone No</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Date</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>