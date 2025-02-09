<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;


$this->IDCard = TableRegistry::get('IDCard');
$this->Users = TableRegistry::get('Users');
?>
<div class="id-card-page">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    
                    <div class="table-responsive m-t-10">
                        
                        <div class="other-filter ml-0 mr-2 float-left">
                            <?php
                                $selecteda = "";
                                $selecteds = "";
                                $selectedst = "";
                                $selectedt = "";

                                if($search == 'Admin') {
                                    $selecteda = "selected";
                                } else if($search == 'Staff') {
                                    $selecteds = "selected";
                                } else if($search == 'Student') {
                                    $selectedst = "selected";
                                } else if($search == 'Teacher') {
                                    $selectedt = "selected";
                                }
                            ?>
                            <select name="search_user_type" id="search_user_type" class="form-control custom-select" onchange="searchUserType()">
                                <option value="" >Select All UserType</option>
                                <option value="Admin" <?php echo $selecteda; ?>>Admin</option>
                                <option value="Staff" <?php echo $selecteds; ?>>Staff</option>
                                <option value="Student" <?php echo $selectedst; ?>>Student</option>
                                <option value="Teacher" <?php echo $selectedt; ?>>Teacher</option>
                            </select>
                        </div>
                        <table id="idcard_table" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>ID#</th>
                                    <th>Expiry Date</th>
                                    <th>Created Date</th>
                                    <th class="text-center"><?= __('Actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach ($idcard as $n): ?>
                                <?php $nid = ConnectionManager::userIdEncode($n['id']); ?>
                                <tr>
                                    <td>
                                        <?php
                                            //$emp = $this->Users->find()->where(['id' => $n->fk_users_id])->first();
                                            $name = $n['name'];
                                            echo $this->Html->link(__($name), ['controller' => 'IDCard', 'action' => 'edit', $nid], ['escape' => false]);
                                            ?>
                                    </td>
                                    <td>
                                        <?php
                                            echo $n['email'];
                                        ?>
                                    </td>
                                    <td><?php
                                            $usertype = explode(",",$n['usertype']);
                                            if($usertype) {
                                                $count = count($usertype);
                                                foreach($usertype as $key => $value) {
                                                    if(isset($value)) {
                                                        echo "<b>[" . h($value) . "]</b>";
                                                        if($key == 1){                                                            
                                                            if($count > 2) {
                                                                echo "<br /><br />";
                                                            }
                                                            
                                                        }
                                                    }
                                                }
                                            }
                                          ?></td>
                                    <td><?= $this->Number->format($n['idNo']) ?></td>
                                    <td><?= date("d/m/Y", strtotime($n['idCardExpiyDate'])) ?></td>
                                    <td data-order="<?php echo date("Y-m-d H:i:s", strtotime($n['created'])); ?>"><?php echo date("d/m/Y", strtotime($n['created'])); ?></td>
                                    <td class="actions">
                                        <div class="actions-div">

                                            <a target="_blank"
                                                href="<?php echo Router::url('/', true); ?>uploads/attachs/<?php echo    $n['fk_users_id'] . '/' . $n['fileName']; ?>"><i
                                                    class="fa fa-eye" data-toggle="tooltip" title='View'></i></a>

                                            <?= $this->Html->link(__('<i class="fa fa-edit green-txt btn-action" aria-hidden="true"></i>'), ['controller' => 'IDCard', 'action' => 'edit', $nid], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Edit']) ?>

                                            <button href="javascript:void(0);" data-title="<?php echo $nid; ?>"
                                                onclick="sendModalModule('<?php echo $nid; ?>');"
                                                class="sendModalModule btn btn-sm waves-effect waves-light btn-info mx-1">Send</button>

                                            <button href="javascript:void(0);" data-title="<?php echo $nid; ?>"
                                                onclick="viewSendDocumentModalModule('<?php echo $nid; ?>');"
                                                class="viewSendDocumentModalModule btn btn-sm waves-effect waves-light btn-info mx-1">View
                                                Sent</button>

                                            <button href="javascript:void(0);" data-title="<?php echo $nid; ?>"
                                                onclick="viewReceivedDocumentModalModule('<?php echo $nid; ?>', 'idcard');"
                                                class="viewReceivedDocumentModalModule btn btn-sm waves-effect waves-light btn-info mx-1">View
                                                Received</button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>

                            </tbody>
                        </table>
                </div>
            </div>
        </div>
        <!--Right Side-->
    </div>
</div>

<!-- Send Modal -->
<div class="modal " id="sendModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form" action="" id="SendDoc" method="POST" class="mb-0">
                <input type="hidden" id="idcardid" name="idcardid" value="AKcess ID" />

                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Send ID Card</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center justify-content-around">
                        <div>
                            <input class="with-gap radio-col-light-blue" type="radio" name="inlineRadioOptions"
                                id="inlineRadio3" value="ackess">

                            <label for="inlineRadio3">AKcess ID</label>
                        </div>
                        <div>

                            <input class="with-gap radio-col-light-blue" type="radio" name="inlineRadioOptions"
                                id="inlineRadio2" value="phone">

                            <label for="inlineRadio2">Phone</label>

                        </div>
                        <div>

                            <input class="with-gap radio-col-light-blue" type="radio" name="inlineRadioOptions"
                                id="inlineRadio1" value="email">

                            <label for="inlineRadio1">Email</label>

                        </div>
                    </div>
                    <div>
                        <div class="form-group ackess">
                            <!-- <select id="ackess" multiple="multiple"
                                class="global-tokenize input_tokenize select2 m-b-10 select2-multiple" name="ackess[]"> -->
                                <?php
                                foreach ($users as $user) {
                                    $akcessId = '';
                                    if (isset($user->akcessId) && $user->akcessId != "") {
                                        $akcessId = $user->akcessId;
                                        $name = $user->name . " ( " . $user->akcessId . " ) ";
                                        if (isset($akcessId)) {
                                            ?>
                                <!-- <option value="<?php echo $user->akcessId; ?>"><?php echo $name; ?></option> -->
                                <?php
                                        }
                                    }
                                }
                                ?>
                            <!-- </select>  -->
                        </div>

                        <div class="form-group email">
                            <!-- <input type="text" id="email_search" name="email_search" placeholder="Enter Email"
                                class="form-control input_tokenize"> -->
                            <input type="hidden" id="email" name="email" value=""> 
                        </div>

                        <div class="form-group phone list_wrapper">
                            <div class="form-row align-items-center">
                                <div class="col-4 col-md-4">
                                    <!-- <select class="custom-select" name="field[0][country_code]" id="country_code">
                                        <option selected>Select Country Code</option>
                                        <?php foreach ($countries as $countrie) { ?>
                                        <option value="<?php echo $countrie->calling_code; ?>">
                                            <?php echo "+" . $countrie->calling_code . " ( " . $countrie->country_name . " ) "; ?>
                                        </option>
                                        <?php } ?>
                                    </select> -->
                                </div>
                                <div class="col-7 col-md-7">
                                    <!-- <input type="text" class="form-control" id="phone" name="field[0][phone]"
                                        placeholder="Enter Phone" /> -->
                                </div>
                                <div class="col-1 text-center">
                                    <!-- <button type="button" class="btn waves-effect waves-light btn-info"
                                        onclick="list_add_button()"><i class="fa fa-plus"></i></button> -->
                                </div>
                            </div> 
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary waves-effect" id="send"
                        onclick="sendData('idcard')">Send</button>
                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal  long-modal" id="viewSendDocumentModalModule" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form role="form" action="" id="ViewSendDocumentData" method="POST" class="mb-0">
                <input type="hidden" id="vieweid" name="vieweid" value="" />
                <input type="hidden" id="viewType" name="viewType" value="idcard" />
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
                            <div class="table-responsive m-t-10">
                                <table id="viewSentAkcess" class="table table-hover table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">AKcess ID</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Date</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="form-group viewemail">
                            <div class="table-responsive m-t-10">
                                <table id="viewSentEmail" class="table table-hover table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">Email ID</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Date</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="form-group viewphone">
                            <div class="table-responsive m-t-10">
                                <table id="viewSentPhone" class="table table-hover table-striped table-bordered">
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
            <form role="form" action="" id="viewReceivedDocumentData" method="POST" class="mb-0">
                <input type="hidden" id="viewReceivedid" name="viewReceivedid" value="" />
                <input type="hidden" id="viewType" name="viewType" value="" />
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">View Received</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive m-t-10">
                        <table id="viewReceivedAkcess" class="table table-hover table-striped table-bordered">
                            <thead>
                            </thead>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="qrcode" class="qr-code"></div>
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
    html += '<button type="button" class="btn waves-effect waves-light btn-danger" onclick="list_remove_button(' + x +
        ')"><i class="fa fa-minus"></i></button>';
    html += '</div>';
    html += '</div>';

    $('.list_wrapper').append(html);
    x++;
}

function list_remove_button(value) {
    $('.remove-' + value).remove();
}

function searchUserType(){
    var e = document.getElementById("search_user_type");
    var strUserType = e.value;
    document.location.href = burl + '/i-d-card?s=' + strUserType;
}

</script>
