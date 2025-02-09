<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Sclass $sclass
 */
?>
<div class="staff-add-page" id="add-global-form">
    <div class="row">
        <div class="col-lg-12 col-xlg-12">
            <div class="card card-outline-info">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="m-b-0 text-white"><i class="fal fa-users-class mr-1"></i>Add Class</h4>
                        <?= $this->Html->link(__('<i class="fas fa-arrow-circle-left"></i> '), ['action' => 'index'],  ['class' => 'btn waves-effect waves-light btn-info','escape' => false, 'data-toggle' => "tooltip", 'title'=>'Back']) ?>
                    </div>
                </div>
                <div class="card-body">
                    <?= $this->Form->create($sclass,
                     array(
                       'data-toggle'=>'validator',
                       'id' => 'postAddClassForm'
                     )) ?>
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?php echo $this->Form->input('name', array('label' =>['text'=>'Name', 'class'=>'required'], 'class' => 'form-control')); ?>
                                    </div>
                                    <?php

                                        if ($this->Form->isFieldError('name')) {
                                            echo $this->Form->error('name');
                                        }
                                    ?>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?php echo $this->Form->input('userallow', array('label' =>['text'=>'Maximum number of students', 'class'=>'required'], 'type'=>'number', 'class' => 'form-control',"oninput"=>"this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');")); ?>
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
                                        <input type="text" name="openepeattimefrom" class="form-control picktime" id="openepeattimefrom">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>openTo </label>
                                        <input type="text" name="openepeattimeto" class="form-control picktime" id="openepeattimeto">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <input type="checkbox" name="repeat_time" id="repeat_time" value="1" class="" style="position: relative;left: 0;opacity: 1;" />
                                    </div>
                                </div>
                            </div>
                            <div class="list_wrapper">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label class="required">Select Days </label>
                                            <select name="classes[days][]" class="form-control days" required>
                                                <option value="">Select Days</option>
                                                <option value="monday">Monday</option>
                                                <option value="tuesday">Tuesday</option>
                                                <option value="wednesday">Wednesday</option>
                                                <option value="thursday">Thursday</option>
                                                <option value="friday">Friday</option>
                                                <option value="saturday">Saturday</option>
                                                <option value="sunday">Sunday</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="required">openFrom </label>
                                            <input required type="text" name="classes[openFrom][]" class="form-control picktime openfrom-picktime" id="openfrom">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="required">openTo </label>
                                            <input required type="text" name="classes[openTo][]" class="form-control picktime opento-picktime" id="opento">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?php echo $this->Form->input( 'details',  array('label' => 'Details', 'class' => 'form-control')); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="required">Locations</label>
                                        <select name="location" id="location" class="custom-select input-md blurclass">
                                            <option value="">Select Locations</option>
                                            <?php
                                            foreach ($locations as $location) {
                                                ?>
                                                <option value="<?php echo $location->id; ?>"><?php echo $location->name; ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="required">Academic Personnel</label>
                                        <select name="fk_user_id" id="fk_user_id" class="custom-select input-md blurclass">
                                            <option value="">Select Academic Personnel</option>
                                            <?php

                                            foreach ($users as $user) {
                                                ?>
                                            <option value="<?php echo $user['id']; ?>">
                                                <?php echo $user['name']; ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-actions text-right">
                            <?= $this->Form->button(__('<i class="fas fa-save mr-1"></i> Save'), array('class' => 'btn waves-effect waves-light btn-primary', 'id' => 'submit_btn')) ?>
                            <?= $this->Html->link(__('<i class="fas fa-times mr-1"></i> Close'), ['action' => 'index'], ['class' => 'btn waves-effect waves-light btn-danger', 'escape' => false]) ?>
                        </div>
                        <div class="clearfix"></div>
                    <?= $this->Form->end() ?>
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
    $('.days :selected').each(function(i, sel){
        check = $(sel).val();
        result = removeItemOnce(days, $(sel).val());
    });
    if(check != "" && result != "") {

        for (var i = 0; i < result.length; i++) {
            selected += '<option value="'+result[i]+'">'+result[i][0].toUpperCase() + result[i].slice(1);+'</option>';
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
        html += '<input type="text" name="classes[openFrom][' + x + ']" class="form-control picktime openfrom-picktime" id="openfrom">';
        html += '</div>';
        html += '</div>';
        html += '<div class="col-md-3">';
        html += '<div class="form-group">';
        html += '<input type="text" name="classes[openTo][' + x + ']" class="form-control picktime opento-picktime" id="opento">';
        html += '</div>';
        html += '</div>';
        html += '<div class="col-md-1">';
        html += '<div class="form-group">';
        html += '<button type="button" class="btn waves-effect waves-light btn-danger" onclick="list_remove_button(' + x +
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

        if($("#repeat_time").prop('checked') == true){
            var openepeattimefrom = $("#openepeattimefrom").val();
            var openepeattimeto = $("#openepeattimeto").val();
            $(".openfrom-picktime").val(openepeattimefrom);
            $(".opento-picktime").val(openepeattimeto);
        }
    } else {
        if(result == ""){
            toastr.error("Sorry no day found.");
            return false;
        }
        toastr.error("Please select day.");
        return false;
    }
}
function list_remove_button(value) {
    $('.remove-' + value).remove();
}

</script>
