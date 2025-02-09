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
                                    <th>Message</th>
                                    <th>DateTime</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($message as $m):
                                $name = $m['username'];
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
                                                 		        }

                                ?>
                                <tr>
                                    <td><?= $this->Number->format($m['id']) ?></td>
                                    <td><?= $name ?></td>
                                    <td><?= $m['ackessID'] ?></td>
                                    <td><?= $m['message'] ?></td>
                                    <td><?= date('d/m/y H:i',strtotime($m['createdDate'])); ?></td>
                                    <td></td>
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