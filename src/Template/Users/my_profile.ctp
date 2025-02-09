<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;
use Cake\Datasource\ConnectionManager;

$conn = ConnectionManager::get("default"); // name of your database connection  

$this->Docs = TableRegistry::get('Docs');
$this->IDCard = TableRegistry::get('IDCard');

$country = '';
if (isset($user->country) && $user->country != "") {
    $query_data = $conn->execute('SELECT * FROM countries where id=' . $user->country);
    $query_country = $query_data->fetch('assoc');
    $country = $query_country['country_name'];
}

$session_usertype = explode(",", $session_user['usertype']);
$get_usertype = explode(",", $user->usertype);
$get_role_id = explode(",", $role_id);

$label = "Student";

$name = isset($user->name) ? $user->name : '';
$id = isset($user->id) ? $user->id : 0;
$flname = $id;
$fname = $id . "/";

$check_fields = 0;
if($user->firstname == "" || $user->lastname == ""  || $user->email == ""  || $user->dob == ""  || $user->photo == "" || $user->akcessId == ""){
    $check_fields = 1;
}

$soft_delete = $user->soft_delete;


$user_id = ConnectionManager::userIdEncode($user->id); 

?>

<input type="hidden" id="check_fields_id" value="<?php echo $check_fields; ?>" />

<div class="view-staff-page">

    <div class="row">
        <div class="col-lg-9">
            <div class="card card-outline-info">

                <div class="card-body">
                    <?php $name = explode(" ", $user->name); ?>
                    <div class="form-body">
                        <div class="row p-t-20">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('AKcess ID') ?></label>
                                    <div><?= h($user->akcessId) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('First Name') ?></label>
                                    <div><?= h($name[0]) ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Last Name') ?></label>
                                    <div><?= h($name[1]) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Email') ?></label>
                                    <div><?= h($user->email) ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('MobileNumber') ?></label>
                                    <div><?= h($user->mobileNumber) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Date of Birth') ?></label>
                                    <div><?php echo h($user->dob); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Place of Birth') ?></label>
                                    <div><?= h($user->city) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Gender') ?></label>
                                    <div><?= h($user->gender) ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Nationality') ?></label>
                                    <div><?= h($country) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Address') ?></label>
                                    <div><?= h($user->address) ?></div>
                                </div>
                            </div>
                        </div>

                        <?php if (in_array('Student',$get_usertype)) { ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Admission Date') ?></label>
                                    <div><?= h(strtoupper($user->adminssion_date)) ?></div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <?php if (in_array('Student',$get_usertype)) { ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Faculty') ?></label>
                                    <div><?= h($user->faculty) ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Courses') ?></label>
                                    <div><?= h($user->courses) ?></div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>


                        <div class="row">
                            <?php if (in_array('Student',$get_usertype)) { ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Active') ?></label>
                                    <div><?= h(strtoupper($user->active)) ?></div>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('ID') ?></label>
                                    <div><?= h($user->idcardno) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Created') ?></label>
                                    <div><?= h($user->created) ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Modified') ?></label>
                                    <div><?= h($user->modified) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (in_array('Student',$get_usertype)) { ?>
                    <hr>
                    <div class="row">
                        <label class="col-2 col-form-label font-400"><?= __('Documents') ?></label>
                        <?php if (in_array('Admin',$get_role_id)) { ?>
                        <?php if($soft_delete == 0) { ?>
                        <div class="col-10 d-flex align-items-center">
                            <?= $this->Html->link('<i class="fa fa-plus fa-2 mr-1"></i>Add Document', ['controller' => 'Docs', 'action' => 'add', $user_id], ['class' => 'adddoc btn waves-effect waves-light btn-info', 'escape' => false]); ?>
                        </div>
                        <?php } ?>
                        <?php } ?>
                    </div>
                    <div class="table-responsive m-t-10">
                        <?php if ($docs) { ?>
                        <table class="table table-hover table-striped table-bordered" id="formInner">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Link</th>
                                    <?php if (in_array('Admin',$get_role_id)) { ?>
                                    <th class="text-center">Actions</th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($docs as $t) { ?>
                                <?php
                                        $tid = ConnectionManager::userIdEncode($t['id']); 
                                        $tids = ConnectionManager::userIdEncode($t['ids']); 
                                        ?>
                                <tr>
                                    <td id="<?php echo "namedoc-" . $tid; ?>"
                                        data-dtype="<?php echo $t['fk_documenttype_id']; ?>"
                                        data-date="<?php echo $t['idCardExpiyDate']; ?>"><?= h($t['name']) ?></td>
                                    <td><a target="_blank"
                                            href="<?php echo Router::url('/', true); ?>uploads/attachs/<?php echo $fname . $t['fileName']; ?>"><i
                                                class="fa fa-eye"></i></a></td>
                                    <?php if (in_array('Admin',$get_role_id)) { ?>
                                    <td class="actions">
                                        <div class="actions-div">
                                            <?php if($soft_delete == 0) { ?>

                                            <?= $this->Html->link('<i class="fa fa-edit green-txt"></i>', ['controller' => 'Docs', 'action' => 'edit', $tids], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Edit', 'class' => 'adddoc', 'data-doc' => $tid]); ?>

                                            <?= $this->Html->link(__('<i class="fa fa-trash red-txt mx-1"></i>'), ['controller' => 'Docs', 'action' => 'delete', $tids, $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Remove', 'class' => 'delete_btn', 'data-link' => 'remove_btn' . $tids]) ?>

                                            <?= $this->Form->postLink(__('Delete'), ['controller' => 'Docs', 'action' => 'delete', $tids, $user_id], ['escape' => false, 'id' => 'remove_btn' . $tids, 'style' => 'display:none;']) ?>

                                            <button type="button" data-type="document" data-title="<?php echo $tids; ?>"
                                                onclick="sendModalModule('<?php echo $tids; ?>');"
                                                class="sendModalModule btn btn-sm waves-effect waves-light btn-info mx-1">Send</button>
                                            <?php } ?>

                                            <button type="button" data-title="<?php echo $tids; ?>"
                                                class="btn btn-sm waves-effect waves-light btn-info mx-1"
                                                onclick="viewSendDocumentModalModule('<?php echo $tids; ?>');">View
                                                Sent</button>

                                            <button type="button" data-title="<?php echo $tids; ?>"
                                                onclick="viewReceivedDocumentModalModule('<?php echo $tids; ?>', 'document');"
                                                class="viewReceivedDocumentModalModule btn btn-sm waves-effect waves-light btn-info">View
                                                Received</button>
                                        </div>
                                    </td>
                                    <?php } ?>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php } ?>
                    </div>
                    <?php } ?>
                    
                    <?php if (in_array('Student',$get_usertype)) { ?>
                    <hr>
                    <div class="row">
                        <label class="col-2 col-form-label font-400"><?= __('IDs') ?></label>
                        <div class="col-10 d-flex align-items-center">
                        <?php if (in_array('Admin',$get_role_id)) { ?>
                            <?php if($soft_delete == 0) { ?>
                            <?php if($cid == '') { ?>
                            <?php if($check_fields == 0) { ?>

                            <?= $this->Html->link(__('<i class="fa fa-plus fa-2 mr-1"></i> Create ID Card'), ['controller' => 'Docs', 'action' => 'addidcard', $user_id], ['escape' => false, 'class' => 'addidcard btn waves-effect waves-light btn-info']); ?>
                            <?php } else { ?>

                            <a href="#scroll" id="checkidcard" class="btn waves-effect waves-light btn-info"> <i
                                    class="fa fa-plus fa-2 mr-1"></i> Create ID Card</a>
                            <?php } ?>
                            <?php } ?>
                            <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if ($idcard &&  $cid != '') { ?>
                    <div class="table-responsive m-t-10">
                        <table id="formInner" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID No</th>
                                    <th>Link</th>
                                    <?php if (in_array('Admin',$get_role_id)) { ?>
                                    <?php if($soft_delete == 0) { ?>
                                    <th class="text-center">Actions</th>
                                    <?php } ?>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($idcard as $t) { ?>
                                <?php
                                        $td = ConnectionManager::userIdEncode($t->id);
                                        ?>
                                <tr>
                                    <td><?= h($t->idNo) ?></td>
                                    <td><a target="_blank"
                                            href="<?php echo Router::url('/', true); ?>uploads/attachs/<?php echo $fname . $t->fileName; ?>"><i
                                                class="fa fa-eye"></i></a></td>
                                    </td>
                                    <?php if (in_array('Admin',$get_role_id)) { ?>
                                    <?php if($soft_delete == 0) { ?>
                                    <td class="actions">
                                        <div class="actions-div">

                                            <?= $this->Html->link('<i class="fa fa-edit green-txt"></i>', ['controller' => 'Docs', 'action' => 'addidcard', $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Edit', 'class' => 'addidcard']); ?>

                                            <?= $this->Html->link(__('<i class="fa fa-trash red-txt mx-1"></i>'), ['controller' => 'IDCard', 'action' => 'delete', $td, $user_id], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Remove', 'class' => 'delete_btn', 'data-link' => 'remove_btn_idcard' . $td]) ?>

                                            <?= $this->Form->postLink(__('Delete'), ['controller' => 'IDCard', 'action' => 'delete', $td, $user_id], ['escape' => false, 'id' => 'remove_btn_idcard' . $td, 'style' => 'display:none;']) ?>

                                            <button type="button" data-title="<?php echo $td; ?>"
                                                onclick="sendIDCardModalModule('<?php echo $td; ?>');"
                                                class="sendIDCardModalModule btn btn-sm waves-effect waves-primary btn-info mx-1">Send</button>

                                            <button type="button" data-title="<?php echo $td; ?>"
                                                onclick="viewSendIDCardModalModule('<?php echo $td; ?>');"
                                                class="viewSendIDCardModalModule btn btn-sm waves-effect waves-primary btn-info mx-1">View Sent</button>

                                            <button type="button" data-title="<?php echo $td; ?>"
                                                onclick="viewReceivedIDCardModalModule('<?php echo $td; ?>', 'idcard');"
                                                class="viewReceivedIDCardModalModule btn btn-sm waves-effect waves-primary btn-info">View Received</button>

                                        </div>
                                    </td>
                                    <?php } ?>
                                    <?php } ?>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card upload-profile-box">
                <div class="card-body">
                    <div class="img-box text-center">
                        <?php
                    if ($user->photo) {
                        $image_src = $this->Url->build('/uploads/attachs/' . $fname . $user->photo);
                    ?>
                        <img src="<?= $image_src ?>" class="img-circle" />
                        <?php
                    } else
                        echo $this->Html->image('user.png', array('class' => 'img-circle'));
                    ?>
                    </div>
                    <?php if (in_array('Admin',$session_usertype)) { ?>
                        <?php if($soft_delete == 0) { ?>
                        <div class="img-msg">
                            <p>
                                <?php
                                if ($user->photo)
                                    $ptitle = 'Click here to Change Photo';
                                else
                                    $ptitle = 'Click here to Upload Photo';
                                ?>
                            </p>
                        </div>
                        <div class="image-control text-center">
                            <?= $this->Form->create($user, ['type' => 'file', 'id' => 'fileInput']) ?>
                            <div class="form-group">
                                <?php
                                echo $this->Form->input('photo', array('type' => 'file', 'label' => $ptitle, 'class' => 'btn btn-success'));
                                ?>
                            </div>
                            <?= $this->Form->end() ?>
                        </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal student-upload-modal" id="myModalaplusDocument" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Upload Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form method="post" accept-charset="utf-8" id="adddoc" controller="Doc" action="<?php echo PATH_URL_PREFIX; ?>/docs/doc-genrate">
                <div class="modal-body">
                    <div class="form-body">
                        <div class="form-group">
                            <?php echo $this->Form->input('name', array('label' => 'Document', 'class' => 'form-control', 'required' => 'true')); ?>
                        </div>
                        <div class="form-group">
                            <select name="fk_documenttype_id" id="fk_documenttype_id" class="custom-select" required>
                                <option value="">Select Document Type</option>
                                <?php
                                foreach ($documentList as $documentLists) {
                                    ?>
                                <option value="<?php echo $documentLists['id']; ?>">
                                    <?php echo $documentLists['name']; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                        <div class="required">
                            <label>Document Expiry Date</label>
                            <input type="date" id="expiry_date_popup_doc" name="idCardExpiyDateDoc" class="form-control"
                                format="YYYY-MM-DD" placeholder="Date of Birth" min="<?php echo date("Y-m-d"); ?>"
                                value="<?php echo $idCardExpiyDate; ?>" required /></div>
                        </div>
                        <div>
                            <?php echo $this->Form->input('attachs', array('type' => 'file', 'label' => 'Attach a file', 'class' => 'form-control','required' => 'true')); ?>
                        </div>
                        <input type="hidden" id="a" name="a" value="<?php echo $userID; ?>" />
                        <input type="hidden" id="b" name="b" value="" />
                    </div>
                </div>
                <div class="modal-footer">
                    <?= $this->Form->button(__('Submit'), array('class' => 'btn btn-primary waves-effect')) ?>
                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
                </div>
                <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal student-upload-modal" id="myModalaplusIDCard" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create University ID</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form method="post" accept-charset="utf-8" id="addidcard" controller="IDCard"
                action="<?php echo PATH_URL_PREFIX; ?>/i-d-card/pdf-genrate">
                <div class="modal-body" id="contentBodyIDCard">
                    <div class="form-body">
                        <div class="form-group">
                            <?php echo $this->Form->input('idNo', array('label' => 'ID No.', 'class' => 'form-control', 'value' => $user->idcardno, 'required' => 'true', 'disabled' => "disabled")); ?>
                        </div>
                        <div class="form-group">
                            <label class="font-400">ID Card Expiry Date</label>
                            <input type="date" id="expiry_date_popup_doc" name="idCardExpiyDateDoc" class="form-control"
                                format="YYYY-MM-DD" placeholder="Date of Birth" value="<?php echo $idCardExpiyDate; ?>"
                                required />
                        </div>
                        <input type="hidden" id="a" name="a" value="<?php echo $userID; ?>" />
                        <input type="hidden" id="b" name="b" value="<?php echo $cid; ?>" />
                        <input type="hidden" id="c" name="c"
                            value="<?php echo ConnectionManager::userIdEncode('Student'); ?>" />
                        </fieldset>
                    </div>
                    <div class="modal-footer">
                        <?= $this->Form->button(__('Submit'), array('class' => 'btn btn-primary waves-effect')) ?>
                        <button type="button" class="btn btn-primary waves-effect" data-dismiss="modal">Close</button>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
        </div>
    </div>
</div>

<!-- Send Modal -->
<div class="modal " id="sendModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form"  id="SendDoc" method="POST" class="mb-0">
                <input type="hidden" id="idcardid" name="idcardid" value="Ackess ID" />

                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Send Document</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center justify-content-around">
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="inlineRadio3" value="ackess"
                                class="with-gap radio-col-light-blue" />
                            <label for="inlineRadio3">AKcess ID</label>
                        </div>
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="email"
                                class="with-gap radio-col-light-blue" />
                            <label for="inlineRadio1">Email</label>
                        </div>
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="phone"
                                class="with-gap radio-col-light-blue" />
                            <label for="inlineRadio2">Phone</label>
                        </div>
                    </div>
                    <div>
                        <div class="form-group ackess">
                            <select id="ackess" multiple="multiple"
                                class="global-tokenize input_tokenize select2 m-b-10 select2-multiple" name="ackess[]">
                                <?php
                                foreach ($users as $user) {
                                    $akcessId = '';
                                    if (isset($user->akcessId) && $user->akcessId != "") {
                                        $akcessId = $user->akcessId;
                                        $name = $user->name . " ( " . $user->akcessId . " ) ";  
                                        if (isset($akcessId)) {
                                            ?>
                                <option value="<?php echo $user->akcessId; ?>"><?php echo $name; ?></option>
                                <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group email">
                            <input type="text" id="email_search" name="email_search" placeholder="Enter Email"
                                class="form-control input_tokenize">
                            <input type="hidden" id="email" name="email" value="">
                        </div>

                        <div class="form-group phone list_wrapper">
                            <div class="form-row align-items-center">
                                <div class="col-4 col-md-4">
                                    <select class="custom-select" name="field[0][country_code]" id="country_code">
                                        <option selected>Select Country Code</option>
                                        <?php foreach ($countries as $countrie) { ?>
                                        <option value="<?php echo $countrie->calling_code; ?>">
                                            <?php echo "+" . $countrie->calling_code . " ( " . $countrie->country_name . " ) "; ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-7 col-md-7">
                                    <input type="text" class="form-control" id="phone" name="field[0][phone]"
                                        placeholder="Enter Phone" />
                                </div>
                                <div class="col-1 text-center">
                                    <button type="button" class="btn waves-effect waves-light btn-info"
                                        onclick="list_add_button()"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary waves-effect" id="send"
                        onclick="sendData('document')">Send <span class="btn_text"></span></button>
                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Send ID Card Modal -->
<div class="modal" id="sendIDCardModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form"  id="SendDocIDCard" method="POST" class="mb-0">
                <input type="hidden" id="idcardid" name="idcardid" value="Ackess ID" />
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Send ID Card</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center justify-content-around">
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewReceivedInlineRadio3" value="ackess"
                                class="with-gap radio-col-light-blue" />
                            <label for="viewReceivedinlineRadio3">AKcess ID</label>
                        </div>
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewReceivedInlineRadio1" value="email"
                                class="with-gap radio-col-light-blue" />
                            <label for="viewReceivedInlineRadio1">Email</label>
                        </div>
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewReceivedInlineRadio2" value="phone"
                                class="with-gap radio-col-light-blue" />
                            <label for="viewReceivedInlineRadio2">Phone</label>
                        </div>
                    </div>
                    <div>
                        <div class="form-group ackess">
                            <!-- <select id="ackess_id" multiple="multiple"
                                class="global-tokenize input_tokenize select2 m-b-10 select2-multiple" name="ackess[]">
                                <?php
                                foreach ($users as $user) {
                                    $akcessId = '';
                                    if (isset($user->akcessId) && $user->akcessId != "") {
                                        $akcessId = $user->akcessId;
                                        $name = $user->name . " ( " . $user->akcessId . " ) ";  
                                        if (isset($akcessId)) {
                                            ?>
                                <option value="<?php echo $user->akcessId; ?>"><?php echo $name; ?></option>
                                <?php
                                        }
                                    }
                                }
                                ?>
                            </select> -->
                        </div>

                        <div class="form-group email">
                            <!-- <input type="text" id="email_search_id" name="email_search" placeholder="Enter Email"
                                class="form-control input_tokenize">
                            <input type="hidden" id="email_id" name="email" value=""> -->
                        </div>

                        <div class="form-group phone list_wrapper">
                            <!-- <div class="form-row align-items-center">
                                <div class="col-4 col-md-4">
                                    <select class="custom-select" name="field[0][country_code]" id="country_code_id">
                                        <option selected>Select Country Code</option>
                                        <?php foreach ($countries as $countrie) { ?>
                                        <option value="<?php echo $countrie->calling_code; ?>">
                                            <?php echo "+" . $countrie->calling_code . " ( " . $countrie->country_name . " ) "; ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-7 col-md-7">
                                    <input type="text" class="form-control" id="phone_id" name="field[0][phone]"
                                        placeholder="Enter Phone" />
                                </div>
                                <div class="col-1 text-center">
                                    <button type="button" class="btn waves-effect waves-light btn-info"
                                        onclick="list_add_button()"><i class="fa fa-plus"></i></button>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary waves-effect" id="send"
                        onclick="sendDataIDCard('idcard')">Send <span class="btn_text"></span></button>
                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View ID Card Modal -->
<div class="modal" id="viewSendIDCardModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form"  id="ViewSendIDCardData" method="POST" class="mb-0">
                <input type="hidden" id="vieweid" name="vieweid" value="" />
                <input type="hidden" id="viewType" name="viewType" value="idcard" />
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">View Sent</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center justify-content-around">
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewSendIDCardinlineRadio3"
                                value="viewackess" class="with-gap radio-col-light-blue" />
                            <label for="viewSendIDCardinlineRadio3">AKcess ID</label>
                        </div>
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewSendIDCardinlineRadio1"
                                value="viewemail" class="with-gap radio-col-light-blue" />
                            <label for="viewSendIDCardinlineRadio1">Email</label>
                        </div>
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewSendIDCardinlineRadio2"
                                value="viewphone" class="with-gap radio-col-light-blue" />
                            <label for="viewSendIDCardinlineRadio2">Phone</label>
                        </div>
                    </div>
                    <div>
                        <div class="form-group viewackess">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped table-bordered" id="viewSentIDCardAkcess">
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
                                <table class="table table-hover table-striped table-bordered" id="viewSentIDCardEmail">
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
                                <table class="table table-hover table-striped table-bordered" id="viewSentIDCardPhone">
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

<!-- viewReceived ID CARD Modal -->
<div class="modal " id="viewReceivedIDCardModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form"  id="viewReceivedIDCardtData" method="POST" class="mb-0">
                <input type="hidden" id="viewReceivedid" name="viewReceivedid" value="" />
                <input type="hidden" id="viewType" name="viewType" value="" />
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">View Received</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered" id="viewReceivedIDCardAkcess">
                                <thead>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal " id="viewSendDocumentModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form"  id="ViewSendDocumentData" method="POST" class="mb-0">
                <input type="hidden" id="vieweid" name="vieweid" value="" />
                <input type="hidden" id="viewType" name="viewType" value="document" />
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">View Sent</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center justify-content-around">
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewSendDocumentinlineRadio3"
                                value="viewackess" class="with-gap radio-col-light-blue" />
                            <label for="viewSendDocumentinlineRadio3">AKcess ID</label>
                        </div>
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewSendDocumentinlineRadio1"
                                value="viewemail" class="with-gap radio-col-light-blue" />
                            <label for="viewSendDocumentinlineRadio1">Email</label>
                        </div>
                        <div>
                            <input type="radio" name="inlineRadioOptions" id="viewSendDocumentinlineRadio2"
                                value="viewphone" class="with-gap radio-col-light-blue" />
                            <label for="viewSendDocumentinlineRadio2">Phone</label>
                        </div>
                    </div>
                    <div>
                        <div class="form-group viewackess">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped table-bordered" id="viewSentAkcess">
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
                                <table class="table table-hover table-striped table-bordered" id="viewSentEmail">
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
                                <table class="table table-hover table-striped table-bordered" id="viewSentPhone">
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

<!-- viewReceived Modal -->
<div class="modal " id="viewReceivedDocumentModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form"  id="viewReceivedDocumentData" method="POST" class="mb-0">
                <input type="hidden" id="viewReceivedid" name="viewReceivedid" value="" />
                <input type="hidden" id="viewType" name="viewType" value="" />
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">View Received</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered" style="width: 100%;"
                                id="viewReceivedAkcess">
                                <thead>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<div id="delete_modal" class="modal " tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <p class="text-center">Are you sure you want to delete?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary waves-effect" id="yes_btn">Yes</button>
                <button class="btn btn-danger waves-effect" id="no_btn" data-dismiss="modal">No</button>
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

<script>
var x = 1;

function list_add_button() {
    var html = "";
    html += '<div class="form-row align-items-center mt-1 remove_all remove-' + x + '">';
    html += '<div class="col-4 col-md-4">';
    html += '<select class="custom-select" name="field[' + x + '][country_code]" id="country_code">';
    html += '<option selected>Select Country Code</option>';
    <?php foreach ($countries as $countrie) { ?>
    html +=
        "<option value='<?php echo $countrie->calling_code; ?>'><?php echo '+' . $countrie->calling_code . ' ( ' . $countrie->country_name . ' ) '; ?></option>";
    <?php } ?>
    html += '</select>';
    html += '</div>';
    html += '<div class="col-7 col-md-7">';
    html += '<input type="text" class="form-control" id="phone" name="field[' + x +
        '][phone]" placeholder="Enter Phone" />';
    html += '</div>';
    html += '<div class="col-1">';
    html += '<button type="button" class="btn waves-effect waves-light btn-danger" onclick="list_remove_button(' +
        x + ')"><i class="fa fa-minus"></i></button>';
    html += '</div>';
    html += '</div>';

    $('.list_wrapper').append(html);
    x++;
}

function list_remove_button(value) {
    $('.remove-' + value).remove();
}
</script>