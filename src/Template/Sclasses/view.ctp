<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Sclass $sclasses
 */
use Cake\ORM\TableRegistry;

use Cake\Datasource\ConnectionManager;

$conn = ConnectionManager::get("default"); // name of your database connection  

$this->Users = TableRegistry::get('Users');


$sclasses_id = ConnectionManager::userIdEncode($sclasses->id); 

$session_usertype = explode(",", $session_user['usertype']);

$soft_delete = $sclasses->soft_delete;

?>
<style>
   fieldset {
            padding: 0px 12px 0px 26px !important;
            border: 1px solid #cdd1d5 !important;
            margin-bottom: 12px;
            border-radius: 6px;
        }
        legend {
            width: auto !important;
            padding: 0px 10px 0px 10px !important;
            font-size: 18px !important;
            color: #1976d2;
        }
</style>

<div class="view-staff-page">
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <?php if(in_array('Admin',$session_usertype)){ ?>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="button-group text-left">                        
                            <?= $this->Html->link(__('<i class="fas fa-arrow-circle-left"></i> '), ['action' => 'index'],  ['class' => 'btn waves-effect waves-light btn-info','escape' => false, 'data-toggle' => "tooltip", 'title'=>'Back']) ?>
                        </div>  
                        <div class="button-group text-right">
                            <?php if($soft_delete == 0) { ?>

                            <?= $this->Html->link(__('<i class="fas fa-edit mr-1"></i> Edit'), ['action' => 'edit', $sclasses_id], ['escape' => false, 'class' => 'btn waves-effect waves-light btn-success']) ?>

                            <?= $this->Html->link(__('<i class="fas fa-trash mr-1"></i> Delete'), ['action' => 'delete', $sclasses_id], ['escape' => false, 'class' => 'delete_btn btn waves-effect waves-light btn-warning', 'data-link' => 'remove_btn']) ?>

                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $sclasses_id], ['escape' => false, 'id' => 'remove_btn', 'style' => 'display:none;']) ?>

                            <?php } ?>

                            <?= $this->Html->link(__('<i class="fas fa-times mr-1"></i> Close'), ['action' => 'index'], ['escape' => false, 'class' => 'btn waves-effect waves-light btn-danger']) ?>

                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-9"> 
            <div class="card card-outline-info">
                <div class="card-header">
                    <h4 class="m-b-0 text-white d-flex align-items-center"><i class="fal fa-users-class mr-1"></i><?= $page_title ?></h4>
                </div>
                <div class="card-body">
                    <div class="form-body">
                        <div class="row p-t-20">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Name') ?></label>
                                    <div><?= h($sclasses->name) ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Maximum number of students') ?></label>
                                    <div><?= $this->Number->format($sclasses->userallow) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Days') ?></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Open From') ?></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Open To') ?></label>
                                </div>
                            </div>
                        </div>
                        <?php
                        $days = explode(",", $sclasses->days);
                        $openFrom = explode(",", $sclasses->openFrom);
                        $openTo = explode(",", $sclasses->openTo);
                
                        $result_array = array();
                        foreach ($days as $key=>$val)
                        {
                            $result_array[$key] = array($days[$key],$openFrom[$key],$openTo[$key]);
                        }
                        $merge = $result_array;
                        foreach ($merge as $value) {
                            ?>

                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <input type="text" class="form-control" disabled  value="<?= h($value[0]) ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" class="form-control" disabled  value="<?= h($value[1]) ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" class="form-control" disabled  value="<?= h($value[2]) ?>">
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Details') ?></label>
                                    <div><?= $this->Text->autoParagraph(h($sclasses->details)); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Location') ?></label>
                                    <div><?= $locations->name; ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Created') ?></label>
                                    <div>
                                        <?php
                                            if(isset($sclasses->created) && $sclasses->created != "") {
                                                echo  h(date("d/m/Y H:i:s", strtotime($sclasses->created)));
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-400"><?= __('Modified') ?></label>
                                    <div>
                                        <?php
                                            if(isset($sclasses->modified) && $sclasses->modified != "") {
                                                echo  h(date("d/m/Y H:i:s", strtotime($sclasses->modified)));
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                         <fieldset>
                            <legend>Students : </legend>
                            <table class="table">
                                <thead>
                                  <tr>
                                    <th>AKcess ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                  </tr>
                                </thead>
                                <tbody>
                                <?php
                                   foreach ($student as $a):
                                   $user = $this->Users->find()->where(['id' => $a->userId])->first();
                                ?>
                                  <tr>
                                    <td><?= h($user->akcessId) ?></td>
                                    <td><?= $user->name; ?></td>
                                    <td><?= h($user->email) ?></td>
                                    <td><?= h($user->mobileNumber) ?></td>
                                  </tr>
                                 <?php
                                    endforeach;
                                 ?>
                                </tbody>
                              </table>

                         </fieldset>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card upload-profile-box">
                <div class="card-header">
                    <?php
                        $dataArray = array(
                            'portal' => ORIGIN_URL,
                            'type' => 'attendence',
                            'classID' => $sclasses->id,
                            'className' => $sclasses->name,
                            'label_type' => 'classes',
                        );
                
                        $data = json_encode($dataArray);
                        //echo date('Y-m-d H:i:s');
                    ?>
                    <div id="qrdiv">
                        <input id="qrcodeclassestext" type="hidden" value='<?= $data ?>' /><br />
                        <div id="qrcodeclasses" style="width:200px; height:200px; margin:auto; "></div>
                    </div>

                </div>
                <div class="card-body">
                    <h4 class="card-title">&nbsp;</h4>
                    <div class="text-center">
                        <?php echo 'QR for '.$sclasses->name; ?>
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