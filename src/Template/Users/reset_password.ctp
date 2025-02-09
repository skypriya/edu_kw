
<div class="login-box">
  <div class="login-logo">
    <?= $this->Html->image('logo.png', ['alt' => 'EDU', 'width' => '200']); ?>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Reset Password</p>

    <?= $this->Form->create($user) ?>
      <div class="form-group has-feedback">
      <input type="password" name="password" id="password" class="form-control" placeholder="Password" required="true">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
      <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password" required="true">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      
          
        
      <div class="row">
        
        <!-- /.col -->
        <div class="col-xs-12 text-center">
        	<input type="submit" name="Submit" class="btn btn-primary btn-flat" value="Submit" />
        </div>
        <!-- /.col -->
      </div>
    <?= $this->Form->end() ?>


  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->