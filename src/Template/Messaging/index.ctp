<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */

$session = $this->request->getSession();
$notification_message = "";
$message_show_hide = "hidden";
if($session->read('notification_message') != "") {
    $notification_message = $session->read('notification_message');
    $session->write('notification_message', '');
    $message_show_hide = "";
}


?>

<?php if(isset($notification_message) && $notification_message != "") { ?>
<div class="alert alert-success alert-hover" id="flash_success">
    <button class="close">x</button>
    <?php echo $notification_message; ?>
</div>
<?php } ?>
<style>
    #message-field, #message-field_notification, #subj_notification{
        padding: .75rem .75rem;
        border-width: 2px;
        border-radius: 8px;
    }

    div.Tokenize ul.TokensContainer{
        padding: 0 .75rem .75rem 0;
    }

    div.Tokenize ul.TokensContainer, div.Tokenize ul.Dropdown{
        border-width: 2px;
        border-radius: 8px;
        
    }

    div.Tokenize ul.TokensContainer li.Placeholder{
        padding: .75rem 0 0 .75rem;
    }

    div.Tokenize ul.TokensContainer li.Token{
        border-width: 2px;
        border-radius: 20px;
    }

    .nav-tabs li a .card-header{
        background: #ced4da;
        border-color: #ced4da;
    }

    .nav-tabs li a.active .card-header{
        background: #1976d2;
        border-color: #1976d2;
    }

    .nav-tabs li a .card-header .text-white{
        color: #455a64 !important;
        font-weight: 500;
    }

    .nav-tabs li a.active .card-header .text-white{
        color: #fff !important;
    }

    .global-tokeniz, .Dropdown{
        max-height: 258px !important;
    }

    span{
       cursor: pointer;
    }
</style>
<div class="students-add-page eFormadd" id="messaging-page">
    <div class="row">
        <div class="col-lg-7 col-xlg-7">
            <div class="card card-outline-info">
                
                <ul class="nav nav-tabs">
                    <li class="active mr-2">
                        <a data-toggle="tab" href="#messaging" class="active">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="m-b-0 text-white"><i class="fal fa-comment-alt mr-1"></i><?= $page_title ?></h6>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#send-notification-form">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="m-b-0 text-white"><i class="fal fa-comment-alt mr-1"></i>Push Notification</h6>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div id="messaging" class="tab-pane active">
                        <div class="card-body">         
                            <form role="form" id="SendMessage" method="POST" class="mb-0">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <select name="to_fiter" class="form-control" id="to_filter">
                                                    <option value="">Select bulk action</option>
                                                    <option value="1">Send to students</option>
                                                    <option value="2">Send to staff</option>
                                                    <option value="3">Send to academic personnel</option>
                                                    <option value="4">Send to users present on campus</option>
                                                    <option value="5">Send to everyone </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>   
                                    <div class="row mb-5">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="font-400">To
                                                <span class="ml-2">
                                                <input type="checkbox" name="send_msg_to_all_user" id="send_msg_to_all_user" value="1" class="" style="position: relative;left: 0;opacity: 1;" /> Select All
                                                </span>

                                                </label>
                                                <div class="msg-send">
                                                    <select id="ackess" multiple="multiple" class="global-tokenize custom-select form-control input_tokenize" name="ackess[]"> 
                                                        <?php
                                                        foreach ($users as $user) {
                                                            $akcessId = '';
                                                            if (isset($user['akcessId']) && $user['akcessId'] != "") {
                                                                $akcessId = $user['akcessId'];
                                                                $name = $user['name'] . " ( " . $user['akcessId'] . " ) ";
                                                                if (isset($akcessId)) {
                                                                    ?>
                                                                    <option value="<?php echo $user['akcessId']; ?>"><?php echo $name; ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </select> 
                                                    <input type="hidden" id="ackess_id" name ="ackess_id" value=""/>        
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="row mt-5">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="msg-txt">
                                                    <textarea name="message" id="message-field" rows="5" class="form-control message_form" placeholder="Type your message here."></textarea> 
                                                </div>
                                                <div style="font-size: 13px;background: #ced4da;display: inline-block;padding: 2px 10px;border-radius: 20px;font-weight: 500;">
                                                    <p class="char-txt m-0"><span id="character-remaining">160</span> Char Left</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <p class="small-msg m-0" style="font-size: 14px;font-weight:600;">NOTE: By Clicking Send, you accept and agree to our Terms & Conditions and Privacy Policy</p>
                                
                                <div class="message-footer my-2 text-right">
                                    <button type="submit" class="btn waves-effect waves-light btn-primary send-btn" id="send" onclick="sendMessage()"><i class="fa fa-send mr-2"></i>Send <span class="btn_text"></span></button>
                                </div>
                                <div class="clearfix"></div>
                            </form>                    
                        </div>
                    </div>
                    <div id="send-notification-form" class="tab-pane">
                        <div class="card-body" >
                            <form role="form" action="<?php echo PATH_URL_PREFIX; ?>/messaging/notify" data-attr="<?php echo PATH_URL_PREFIX; ?>/messaging/notify" id="SendNotifications" method="POST" class="mb-0">
                                <div class="form-body">
                                    <?php $token = $this->request->getParam('_csrfToken'); ?>
                                    <input type="hidden" id="_csrfToken" name ="_csrfToken" value="<?php echo $token; ?>"/> 
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <select name="notification_to_fiter" class="form-control" id="notification_to_fiter">
                                                    <option value="">Select bulk action</option>
                                                    <option value="1">Send to students</option>
                                                    <option value="2">Send to staff</option>
                                                    <option value="3">Send to academic personnel</option>
                                                    <option value="4">Send to users present on campus</option>
                                                    <option value="5">Send to everyone </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mb-5">
                                            <label class="font-400 required">To</label>
                                            <div class="form-group msg-send">
                                                <select id="ackess_notification" multiple="multiple" class="global-tokenize global-tokenize_notification custom-select form-control input_tokenize" name="astudents[]"> 
                                                    <?php
                                                    foreach ($users_notification as $user) {
                                                        $akcessId = '';
                                                        $user_type = $user->usertype;
                                                        if (isset($user->akcessId) && $user->akcessId != "") {
                                                            $akcessId = $user->akcessId;
                                                            $name = $user->name . " ( " . $user->akcessId . " ) ";
                                                            if (!empty($akcessId) && !empty($user->email)) {
                                                                ?>
                                                                <option value="<?php echo $user->id.'-'.$user->email; ?>"><?php echo $name . "( " . $user_type . ")"; ?></option>
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
                                                <div style="font-size: 13px;background: #ced4da;display: inline-block;padding: 2px 10px;border-radius: 20px;font-weight: 500;">
                                                    <p class="char-txt m-0"><span id="character-remaining_notification">160</span> Char Left</p>
                                                </div>
                                            </div>
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
            </div>
        </div>
        <!--Right Side-->
        <div class="col-lg-5">
             <div class="card" id="message-history">
                 <div class="card-body">
                 <h4 class="card-title">SENT</h4>
                 <ul class="nav nav-tabs msg-type" role="tablist">
                 	<li class="nav-item">
                 		<a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">Messaging</a>
                 	</li>
                 	<li class="nav-item">
                 		<a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">Push Notification</a>
                 	</li>
                 </ul>
                 <div class="tab-content">
                 	<div class="tab-pane active" id="tabs-1" role="tabpanel">
                 		<ul class="msg-list-ul">

                 		   <?php
                 		   if(!empty($message_res)){
                 		        foreach($message_res as $m){
                 		        $name = $m['username'].' ('.$m['ackessID'].')';
                 		        if(isset($m['group_type']) && $m['group_type'])
                 		        {
                 		           if($m['group_type'] == 1)
                 		           {
                 		              $name = 'All students';
                 		           }
                 		           elseif($m['group_type'] == 2)
                 		           {
                 		              $name = 'All staff';
                 		           }
                 		           elseif($m['group_type'] == 3)
                 		           {
                 		              $name = 'All academic personnel';
                 		           }
                  		           elseif($m['group_type'] == 4)
                  		           {
                  		              $name = 'All users present on campus';
                  		           }
                 		           elseif($m['group_type'] == 5)
                 		           {
                 		              $name = 'Everyone';
                 		           }
                 		        }?>
                 		           <li>
                                        <div>
                                        <span class="m-w-5"><?= $name ?></span>
                                        <span class="msg-date"><?= date('d/m/y H:i',strtotime($m['createdDate']))?></span>
                                                                </div>
                                                                <div><?= $m['message']?></div>
                                                            </li>
                 		        <?php
                 		        } ?>
                 		        <li class="all-data">
                 		            <?= $this->Html->link('See all', array('controller' => 'Messaging', 'action' => 'messages'), array('style'=>'float: right','escape' => false)); ?>
                                </li>
                 		        <?php
                 		   }else
                 		   {
                 		      echo '<p class="text-center">Messaging not found</p>';
                 		   }

                 		   ?>


                        </ul>
                 	</div>
                 	<div class="tab-pane" id="tabs-2" role="tabpanel">
                 		<ul class="msg-list-ul">

                                         		   <?php
                                         		   if(!empty($notify_res)){
                                         		        foreach($notify_res as $n){
                                         		        ?>

                                         		           <li>
                                                                <div>
                                                                <span class="m-w-5"><?= $n['username'].' ('.$n['ackessID'].')' ?></span>
                                                                <span class="msg-date"><?= date('d/m/y H:i',strtotime($n['createdDate']))?></span>
                                                                                        </div>
                                                                                        <div><?= $n['message']?></div>
                                                                                    </li>
                                         		        <?php
                                         		        }?>
                                         		        <li class="all-data">
                                         		            <?= $this->Html->link('See all', array('controller' => 'Messaging', 'action' => 'notification'), array('style'=>'float: right','escape' => false)); ?>
                                                        </li>
                                         		        <?php
                                         		   }else
                                                       {
                                                          echo '<p class="text-center">Push notification not found</p>';
                                                       }
                                         		   ?>
                                                </ul>
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