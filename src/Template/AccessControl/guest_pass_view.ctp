<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Sclass $sclasses
 */

use Cake\Datasource\ConnectionManager;

$conn = ConnectionManager::get("default"); // name of your database connection

//$sclasses_id = ConnectionManager::userIdEncode($sclasses->id);

$session_usertype = explode(",", $session_user['usertype']);

//$soft_delete = $sclasses->soft_delete;

?>
<style>
  #guest-pass-view-section .upload-profile-box .card-header{
      background: none !important;
      border-color: #d8d8d8 !important;
      border: 1px solid;
  }
  #guest-pass-view-section .upload-profile-box #qrdiv{
      margin: -10px 0px 10px 0px !important;
  }

  #guest-pass-view-section .row{
        border-radius: 12px;
        padding: 24px 10px 18px 6px;
        border: 1px solid #d8d8d8;
        box-shadow: 0px 5px 20px rgb(0 0 0 / 42%);
  }

  #guest-pass-view-section .pass-box{
         margin-left: 25%;
  }

  #guest-pass-view-section hr{
         margin-top: 10px !important;
  }

  #guest-pass-view-section .form-group {
      margin-bottom: 0 !important;
  }

  #guest-pass-view-section label {
      margin-bottom: 0 !important;
  }

  #guest-pass-view-section .date-time{
      display: inline-flex !important;
      width: 100%;
  }

  #guest-pass-view-section .date-time .col-lg-6{
      padding: 0 !important;
  }
  #guest-pass-view-section .card-title{
      background: #c8cfd3;
      margin: -36px 0px 10px 0;
      padding: 2px 14px 2px 14px;
      border-radius: 8px;
      width: fit-content;
  }
  .btn-info, .btn-info.disabled
    {
        background: #ffffff;
        border: 1px solid #ffffff;
    }

    .btn-info, .btn-info.disabled
      {
          background: #ffffff;
          border: 1px solid #ffffff;
      }

    .btn-info {
        color: #1976d2;
    }

    .button-group .btn {
        margin: 0;
        padding: 2px 8px 2px 8px;
    }
    .btn {
        font-size: 20px;
    }

    #qrcodeclasses img{
        width: 100% !important;
        height: 100% !important;
    }
</style>

<div class="view-staff-page">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline-info">
                <div class="card-header">
<div class="button-group text-left">
                            <?= $this->Html->link(__('<i class="fas fa-arrow-circle-left"></i> '),$this->request->referer(),  ['class' => 'btn waves-effect waves-light btn-info','escape' => false, 'data-toggle' => "tooltip", 'title'=>'Back']) ?>
                        </div>
                </div>
                <div id="guest-pass-view-section" class="card-body mt-4 mb-5">
                   <div class="col-lg-6 pass-box">
                        <div class="row">
                            <div class="col-lg-6">

                                <div class="form-group">
                                                                    <label class="font-400">University name</label>
                                                                    <div><?= strtoupper(COMP_NAME); ?></div>
                                                                </div>
                                                                <hr>

                                <div class="form-group">
                                    <label class="font-400">Name</label>
                                    <div><?= isset($res['first_name']) ? $res['first_name'].' '.$res['last_name'] : '-'; ?></div>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label class="font-400">Invitation from</label>
                                    <div><?= isset($res['invitee_name']) && $res['invitee_name'] ? $res['invitee_name'] : '-'; ?></div>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label class="font-400">Mobile</label>
                                    <?php
                                     $country_code = '';
                                     if(isset($res['country_code']) && $res['country_code'])
                                     {
                                        $country_code = '+'.$res['country_code'];
                                     }
                                    ?>
                                    <div><?= isset($res['mobile']) ? $country_code.$res['mobile'] : '-'; ?></div>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label class="font-400">Location</label>
                                    <div><?= isset($res['l']['name']) ? $res['l']['name'] : '-'; ?></div>
                                </div>
                                <hr>
                                <div class="date-time">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="font-400">Date</label>
                                            <div><?= isset($res['guest_pass_date']) && $res['guest_pass_date'] ? date('d/m/Y',strtotime($res['guest_pass_date'])) : '-'; ?></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="font-400">Time</label>
                                            <div><?= isset($res['guest_pass_date']) && $res['guest_pass_date'] ? $res['guest_pass_time'] : '-'; ?></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-lg-6">
                                <div class="card upload-profile-box mb-0" style="top: 10%;">
                                    <div class="card-header">
                                        <?php
                                            $dataArray = array(
                                                'portal' => ORIGIN_URL,
                                                'type' => 'guest_pass',
                                                'id'  => isset($res['id']) ? $res['id'] : '',
                                                'user_name' => isset($res['first_name']) ? $res['first_name'].' '.$res['last_name'] : '',
                                                'label_type' => 'guestpass',
                                            );
                                            $data = json_encode($dataArray);
                                        ?>
                                        <div id="qrdiv">
                                            <input id="qrcodeclassestext" type="hidden" value='<?= $data ?>' /><br />
                                            <div id="qrcodeclasses"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
