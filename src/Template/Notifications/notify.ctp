<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Sclass $sclass
 */
?>
<div class="staff-add-page" id="send-notification-form">
    <div class="row">
        <div class="col-lg-9 col-xlg-9">            
            <div class="card card-outline-info">
                <div class="card-header">
                    <h4 class="m-b-0 text-white"><i class="fal fa-bell mr-1"></i>Sent Notification</h4>
                </div>
                <div class="card-body">
                <form role="form" action="<?php echo PATH_URL_PREFIX; ?>/notifications/notify" data-attr="<?php echo PATH_URL_PREFIX; ?>/notifications/notify" id="SendNotifications" method="POST" class="mb-0">
                                <div class="form-body">
                                    <?php $token = $this->request->getParam('_csrfToken'); ?>
                                    <input type="hidden" id="_csrfToken" name ="_csrfToken" value="<?php echo $token; ?>"/> 
                                    <div class="row">
                                        <div class="col-md-12 mb-5">
                                            <div class="form-group msg-send">
                                                <select id="ackess_notification" multiple="multiple" class="global-tokenize global-tokenize_notification custom-select form-control input_tokenize" name="astudents[]"> 
                                                    <?php
                                                    foreach ($users as $user) {
                                                        $akcessId = '';
                                                        if (isset($user->akcessId) && $user->akcessId != "") {
                                                            $akcessId = $user->akcessId;
                                                            $name = $user->name . " ( " . $user->akcessId . " ) ";
                                                            if (isset($akcessId)) {
                                                                ?>
                                                                <option value="<?php echo $user->id.'-'.$user->email; ?>"><?php echo $name; ?></option>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select> 
                                                <input type="hidden" id="ackess_id_notification" name ="ackess_id" value=""/>        
                                            </div> 
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="font-400 required">Subject </label>
                                                <input type="text" name="subject" class="form-control" id="subj_notification">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="font-400 required">Message </label>
                                                <div class="msg-txt">
                                                    <textarea name="message" id="message-field_notification" rows="5" class="form-control message_form" placeholder="Type your message here."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="message-footer d-flex align-items-center justify-content-between my-2">
                                        <div>
                                            <p class="char-txt m-0"><span id="character-remaining_notification">160</span> Char Left</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions text-right">
                                    <button type="button" class="btn waves-effect waves-light btn-primary send-btn ml-2" id="send"  onclick="sendNotification()">Submit <span class="btn_text"></span></button>
                                    
                                    <?= $this->Html->link(__('<i class="fas fa-times mr-1"></i> Close'), ['action' => 'index'], ['class' => 'btn waves-effect waves-light btn-danger', 'escape' => false]) ?>
                                </div>                        
                                <div class="clearfix"></div>
                            </form>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Checked In</h4>
                    <div class="row">
                        <div class="col-6">
                            <h2 class="m-0"><i class="fal fa-users"></i> <?= $count_staff ?></h2>
                            <h6>Staff</h6>
                        </div>
                        <div class="col-6 text-right p-l-0">
                            <h2 class="m-0"><i class="fal fa-chalkboard-teacher"></i> <?= $count_teacher ?></h2>
                            <h6>Teachers</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Checked In</h4>
                    <div class="row">
                        <div class="col-6">
                            <h2 class="m-0"><i class="fal fa-user-graduate"></i> <?= $count_students ?></h2>
                            <h6>Students</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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