<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Sclass $sclass
 */
?>
<div class="edit-staff-page">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline-info">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="m-b-0 text-white d-flex align-items-center"><i class="fal fa-users-class mr-1"></i><?= $page_title ?></h4>
                        <?= $this->Html->link(__('<i class="fas fa-arrow-circle-left"></i> '),$this->request->referer(),  ['class' => 'btn waves-effect waves-light btn-info','escape' => false, 'data-toggle' => "tooltip", 'title'=>'Back']) ?>
                    </div>
                </div>
                <div class="card-body">
                    <?= $this->Form->create($sclass) ?>
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?php echo $this->Form->input('name', array('label' => ['text'=>'Name', 'class'=>'required'], 'class' => 'form-control', 'value' => $sclasses->name)); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <button type="button" class="btn waves-effect waves-light btn-info"
                                        onclick="list_add_button()"><i class="fa fa-plus"></i> Add Days</button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>openFrom </label>
                                    <input type="text" name="openepeattimefrom" class="form-control picktime"
                                        id="openepeattimefrom">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>openTo </label>
                                    <input type="text" name="openepeattimeto" class="form-control picktime"
                                        id="openepeattimeto">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <input type="checkbox" name="repeat_time" id="repeat_time" value="1" class=""
                                        style="position: relative;left: 0;opacity: 1;" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="required">Select Days </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="required">openFrom </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="required">openTo </label>
                                </div>
                            </div> 
                            <div class="col-md-1">
                                <div class="form-group">
                                </div>
                            </div>                            
                        </div>
                        <div class="list_wrapper">
                            <?php
                                if($sclasses->merge) {
                                    foreach($sclasses->merge as $key => $mergevalue) {?>
                                        <div class="row remove_all remove-<?php echo $key; ?>">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <select name="classes[days][]" class="form-control days">
                                                        <option value="">Select Days</option>
                                                        <option value="<?php echo $mergevalue[0]; ?>" selected>
                                                            <?php echo ucfirst($mergevalue[0]); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">

                                                <div class="form-group">
                                                    <input type="text" name="classes[openFrom][]"
                                                        class="form-control picktime openfrom-picktime" id="openfrom"
                                                        value="<?php echo $mergevalue[1]; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3">

                                                <div class="form-group">
                                                    <input type="text" name="classes[openTo][]"
                                                        class="form-control picktime opento-picktime" id="opento"
                                                        value="<?php echo $mergevalue[2]; ?>">
                                                </div>
                                            </div>
                                            <?php if($key > 0) { ?>
                                            <div class="col-md-1 ">
                                                <div class="form-group">
                                                    <button type="button" class="btn waves-effect waves-light btn-danger"
                                                        onclick="list_remove_button('<?php echo $key; ?>')"><i
                                                            class="fa fa-minus"></i></button>

                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    <?php } 
                                } ?>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Details</label>
                                        <textarea name="details" id="details" col="3"
                                            class="form-control"><?php echo $sclasses->details; ?></textarea>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="required">Locations</label>
                                        <select name="location" id="location" class="custom-select input-md blurclass">
                                            <option value="">Select Locations</option>
                                            <?php

                                            foreach ($locations as $location) {
                                                $selected = "";
                                                if($sclasses->location == $location->id) {
                                                    $selected = "selected";
                                                }
                                                ?>
                                            <option value="<?php echo $location->id; ?>" <?php echo $selected; ?>>
                                                <?php echo $location->name; ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions text-right">
                            <?= $this->Form->button(__('Update'), array('class' => 'btn waves-effect waves-light btn-primary', 'id' => 'submit_btn')) ?>
                            <?= $this->Html->link(__('<i class="fas fa-times mr-1"></i> Close'), ['action' => 'index'], ['escape' => false, 'class' => 'btn waves-effect waves-light btn-danger']) ?>
                        </div>
                        <div class="clearfix"></div>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
       <!--     <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Checked In</h4>
                        <div class="row">
                            <div class="col-6">
                                <h2 class="m-0"><i class="fal fa-users"></i> <?= $count_staff ?></h2>
                                <h6>Staff</h6>
                            </div>
                            <div class="col-6 text-right p-l-0">
                                <h2 class="m-0"><i class="fal fa-chalkboard-teacher"></i> <?= $count_teacher ?></h2>
                                <h6>Teachers</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Checked In</h4>
                        <div class="row">
                            <div class="col-6">
                                <h2 class="m-0"><i class="fal fa-user-graduate"></i> <?= $count_students ?></h2>
                                <h6>Students</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
    </div>

    <script>    

    var x = <?php echo $total_days; ?>;

    function removeItemOnce(arr, value) {
        var index = arr.indexOf(value);
        if (index > -1) {
            arr.splice(index, 1);
        }
        return arr;
    }

    function removeItemAll(arr, value) {
        var i = 0;
        while (i < arr.length) {
            if (arr[i] === value) {
                arr.splice(i, 1);
            } else {
                ++i;
            }
        }
        return arr;
    }

    function list_add_button() {
        toastr.remove();
        var selected = '<option value="">Select Days</option>';

        var days = [
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
            'sunday'
        ];

        var result = [];
        var check = "";
        $('.days :selected').each(function(i, sel) {
            check = $(sel).val();
            result = removeItemOnce(days, $(sel).val());
        });
        if (check != "" && result != "") {

            for (var i = 0; i < result.length; i++) {
                selected += '<option value="' + result[i] + '">' + result[i][0].toUpperCase() + result[i].slice(1); +
                '</option>';
            }
            var html = "";
            html += '<div class="row align-items-center mt-1 remove_all remove-' + x + '">';
            html += '<div class="col-md-5">';
            html += '<div class="form-group">';
            html += '<select name="classes[days][' + x + ']" class="form-control days">';
            html += selected;
            html += '</select>';
            html += '</div>';
            html += '</div>';
            html += '<div class="col-md-3">';
            html += '<div class="form-group">';
            html += '<input type="text" name="classes[openFrom][' + x +
                ']" class="form-control picktime openfrom-picktime" id="openfrom">';
            html += '</div>';
            html += '</div>';
            html += '<div class="col-md-3">';
            html += '<div class="form-group">';
            html += '<input type="text" name="classes[openTo][' + x +
                ']" class="form-control picktime opento-picktime" id="opento">';
            html += '</div>';
            html += '</div>';
            html += '<div class="col-md-1">';
            html += '<div class="form-group">';
            html +=
                '<button type="button" class="btn waves-effect waves-light btn-danger" onclick="list_remove_button(' +
                x +
                ')"><i class="fa fa-minus"></i></button>';
            html += '</div>';
            html += '</div>';
            html += '</div>';

            $('.list_wrapper').append(html);
            $('.picktime').datetimepicker({
                format: 'LT',
                //inline: true,
            });
            x++;

            if ($("#repeat_time").prop('checked') == true) {
                var openepeattimefrom = $("#openepeattimefrom").val();
                var openepeattimeto = $("#openepeattimeto").val();
                $(".openfrom-picktime").val(openepeattimefrom);
                $(".opento-picktime").val(openepeattimeto);
            }
        } else {
            if (result == "") {
                toastr.error("Sorry no day found.");
                return false;
            }
            toastr.error("Please select day.");
            return false;
        }
    }
    $(document).ready(function() {
        $("#repeat_time").click(function() {
            if (this.checked) {
                alert('checked');
            }
            if (!this.checked) {
                alert('Unchecked');
            }
        });
    });

    function list_remove_button(value) {
        $('.remove-' + value).remove();
    }
    </script>