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

$label = "";
if ($user->usertype == 'Student') {
    $label = "Student";
} else if ($user->usertype == 'Staff') {
    $label = "Staff";
} else if ($user->usertype == 'Teacher') {
    $label = "Teacher";
}

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
        <div class="col-lg-12">
            <div class="card">               
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-9">
            <div class="card card-outline-info">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="m-b-0 text-white d-flex align-items-center"><i class="fal fa-user-graduate mr-1"></i>View Profile <?php echo $label; ?> ( <?php echo $user->name; ?> ) </h4>
                        <?= $this->Html->link(__('<i class="fas fa-arrow-circle-left"></i> '),$this->request->referer(),  ['class' => 'btn waves-effect waves-light btn-info','escape' => false, 'data-toggle' => "tooltip", 'title'=>'Back']) ?>
                    </div>
                </div>
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

                        <?php if ($user->usertype == 'Student') { ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Admission Date') ?></label>
                                    <div><?= h(strtoupper($user->adminssion_date)) ?></div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <?php if ($user->usertype == 'Student') { ?>
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
                            <?php if ($user->usertype == 'Student') { ?>
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
                    <?php if ($session_user['usertype'] == 'Admin') { ?>
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
