<div class="staff-list-page">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title"><i class="fal fa-bell mr-1"></i><?= $page_title ?></h4>
                    </div>
                    <div class="table-responsive m-t-10">
                        <table id="example1" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>AKcess ID</th>
                                    <th>Subject</th>
                                    <th>Message</th>
                                    <th>DateTime</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($notify as $n): ?>
                                <tr>
                                    <td><?= $this->Number->format($n['id']) ?></td>
                                    <td><?= $n['username'] ?></td>
                                    <td><?= $n['ackessID'] ?></td>
                                    <td><?= $n['subj'] ?></td>
                                    <td><?= $n['message'] ?></td>
                                    <td><?= date('d/m/y H:i',strtotime($n['createdDate'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
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