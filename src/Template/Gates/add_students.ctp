
<?= $this->Form->create(null, ['id' => 'add_stu']) ?>
<h4>Add Students</h4>
<fieldset>

    <div class="form-group col-lg-6">
        <label for="classId">Students </label>
          <select name="users[]" required="required" class="form-control" multiple="multiple" id="users" size="5" style="height:auto;">
              <?php foreach ($users as $c): ?>
              <option value = <?= $c->id ?>><?= $c->name ?></option>
              <?php endforeach; ?>

          </select>
      </div>
    
    
</fieldset><br>
<div class="text-center">
    <?= $this->Form->button(__('Submit'), array('class' => 'btn btn-primary' )) ?>
</div>

<?= $this->Form->end() ?>
