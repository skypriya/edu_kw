<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
use Cake\ORM\TableRegistry;
?>
<div class="view-staff-page">
    <div class="row">
        <div class="col-lg-9">
            <div class="card card-outline-info">
                <div class="card-header">
                    <h4 class="m-b-0 text-white d-flex align-items-center"><?= $page_title ?></h4>
                    <p class="card-category"> &nbsp;</p>
                </div>
                <div class="card-body">
                    <div class="h4">Enter Your Details</div>
                    <!-- Static HTML start here -->
                    <div class="row">
                        <div class="col-md-8 py-4 border-top">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label>First name</label>
                                        <input class="form-control" placeholder="Enter first name" />
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>Last name</label>
                                        <input class="form-control" placeholder="Enter last name" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group my-4">
                                <label>ID No.</label>
                                <input class="form-control" placeholder="Enter ID No." />
                            </div>

                            <div class="row my-4">
                                <div class="col">
                                    <div class="form-group">
                                        <label>DOB</label>
                                        <input class="form-control" placeholder="Invalid date" />
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>ID Card Expiry Date</label>
                                        <input class="form-control" placeholder="Select date" />
                                    </div>
                                </div>
                            </div>

                            <div class="custom-file border w-50">
                                <input type="file" class="custom-file-input" id="customFile">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>

                            <div class="text-right mt-4">
                                <button class="btn btn-success">Save</button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="px-3">
                                <div class="border shadow">
                                    <?= $this->Html->image('EDU-ID-CARD.jpg', ['class' => 'img-fluid w-100', 'alt' => '_ID']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Static HTML end here -->
                </div>
            </div>
        </div>
    </div>