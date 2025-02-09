<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

$this->IDCard = TableRegistry::get('IDCard');
$this->Users = TableRegistry::get('Users');
?>


<div class="id-card-page">
    <div class="row">
        <div class="col-12">
            <?= $this->Html->link(__('<i class="fas fa-arrow-circle-left back-btn"></i>'), ['action' => '/'], ['escape' => false, 'data-toggle' => "tooltip", 'title'=>'Back']) ?> &nbsp;
        </div>
    </div>
    <div class="row">    
        <div class="col-12 col-md-8 col-xl-9">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between mb-0">
                    <h4 class="card-title"><i class="fal fa-id-card mr-1"></i><?= $page_title ?></h4>    
                </div>
                <hr class="mt-0"/>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="idcard_recycle_table">
                            <thead>
                                <tr>
                                    <th>ID</th>      
                                    <th>Name</th>
                                    <th>ID#</th>
                                    <th>Expiry Date</th>
                                    <th class="text-center"><?= __('Actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach ($idcard as $n): ?>
                                    <?php $nid = ConnectionManager::userIdEncode($n->id); ?>
                                    <tr>
                                        <td><?= $n->id ?></td>
                                        <td>
                                            <?php
                                            $emp = $this->Users->find()->where(['id' => $n->fk_users_id])->first();
                                            $name = $emp->name;
                                            echo $this->Html->link(__($name), ['controller' => 'IDCard', 'action' => 'edit', $nid], ['escape' => false]);
                                            ?>
                                        </td>
                                        <td><?= $this->Number->format($n->idNo) ?></td>
                                        <td><?= date("F d, Y", strtotime($n->idCardExpiyDate)) ?></td>
                                        
                                        <td class="actions">
                                            <div class="actions-div">
                                                <?= $this->Html->link(__('<i class="fa fa-eye btn-action" aria-hidden="true"></i>'), ['controller' => 'IDCard', 'action' => 'edit', $nid], ['escape' => false]) ?> 
                                                <a href="javascript:void(0);" onclick="viewSendDocumentModalModule('<?php echo $nid; ?>');" data-title="<?php echo $nid; ?>" class="btn btn-primary btn-sm">View Sent</a>

                                                <a href="javascript:void(0);" onclick="viewReceivedDocumentModalModule('<?php echo $nid; ?>', 'idcard');" data-title="<?php echo $nid; ?>" class="btn btn-primary btn-sm">View Received</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
        <!--Right Side-->
        <div class="col-12 col-md-4 col-xl-3 mt-30 mb-30">
            <div class="info-cards green-card">
                <h5>Checked In</h4>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="text-center">
                                <i class="fas fa-user-friends font-card"></i>
                                <div class="card-text">Staff</div>
                            </div>
                            <div class="ml-2"><h2><?= $count_staff ?></h2></div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="text-center">
                                <i class="fas fa-chalkboard-teacher font-card"></i>
                                <div class="card-text">Teachers</div>
                            </div>
                            <div class="ml-2"><h2><?= $count_teacher ?></h2></div>
                        </div>
                    </div>                    
            </div>
            <div class="info-cards orange-card">
                <h5>Checked In</h4>
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fas fa-user-graduate font-card"></i>
                            <div class="card-text">Students</div>
                        </div>
                        <div class="ml-2"><h2><?= $count_students; ?></h2></div>
                    </div>
            </div>   
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal " id="viewSendDocumentModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" action="" id="ViewSendDocumentData" method="POST" class="mb-0">
                <input type="hidden" id="vieweid" name="vieweid" value=""/>
                <input type="hidden" id="viewType" name="viewType" value="idcard"/>
                <div class="modal-header">                
                    <h4 class="modal-title" id="myModalLabel">View Sent</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center justify-content-around"> 
                        <div class="form-check form-check-radio">
                            <label class="form-check-label black-txt">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="viewackess" >
                                Ackess ID
                                <span class="circle">
                                    <span class="check"></span>
                                </span>
                            </label>
                        </div>
                        <div class="form-check form-check-radio">
                            <label class="form-check-label black-txt">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="viewemail" >
                                Email
                                <span class="circle">
                                    <span class="check"></span>
                                </span>
                            </label>
                        </div>
                        <div class="form-check form-check-radio">
                            <label class="form-check-label black-txt">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="viewphone" >
                                Phone
                                <span class="circle">
                                    <span class="check"></span>
                                </span>
                            </label>
                        </div>               
                    </div>
                    <div>
                        <div class="form-group viewackess mb-0">                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table" id="viewSentAkcess">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Akcess ID</th>
                                                            <th scope="col">Status</th>
                                                            <th scope="col">Date</th>                                
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>              
                        </div>
                        <div class="form-group viewemail mb-0">                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table" id="viewSentEmail">
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
                                    </div>
                                </div>
                            </div>                            
                        </div>
                        <div class="form-group viewphone mb-0">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table" id="viewSentPhone">
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
                            </div>
                        </div>

                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<!-- viewReceived Modal -->
<div class="modal " id="viewReceivedDocumentModalModule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:800px;">
        <div class="modal-content">
            <form role="form" action="" id="viewReceivedDocumentData" method="POST" class="mb-0">
                <input type="hidden" id="viewReceivedid" name="viewReceivedid" value=""/>
                <input type="hidden" id="viewType" name="viewType" value="idcard"/>
                <div class="modal-header">                
                    <h4 class="modal-title" id="myModalLabel">View Received</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">                    
                    <div>
                        <div class="form-group mb-0">                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table" id="viewReceivedAkcess" style="width: 100%;">
                                                    <thead>                                                        
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>              
                        </div>                      
                    </div>
                </div>
            </form>
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