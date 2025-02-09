<div class="container login-page">
    <div class="login-outer-box d-flex align-items-center">
        <div class="login-inner-box w-100">
            <div class="image-section mb-3">
            <?= $this->Html->image('logo.png', ['alt' => 'EDU']) ?>
            </div>
            <div class="logo text-uppercase">Register</div>
            <!--<p class="mb-0">Sign in with QR code</p>-->
            <?php ?>
            <?= $this->Form->create($user) ?>
                <div class="register-section text-left">
                    <div class="form-group">
                        <label>AKcess ID</label>
                        <?php
                        $aid = '';

                        if (isset($data_rerponse['d'])) {
                            $aid .= $data_rerponse['d'];
                            $d = $data_rerponse_encode['d'];
                        }
                        ?>
                        <input type="text" class="form-control" value="<?php echo $aid; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Name</label>
                        <?php
                        $name = '';
                        if (isset($data_rerponse['b'])) {
                            $name .= $data_rerponse['b'];
                            $b = $data_rerponse_encode['b'];
                        }
                        if (isset($data_rerponse['c'])) {
                            $name .= " " . $data_rerponse['c'];
                            $c = $data_rerponse_encode['c'];
                        }
                        ?>
                        <input type="text" class="form-control" value="<?php echo $name; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <?php
                        $email = '';

                        if (isset($data_rerponse['a'])) {
                            $email .= $data_rerponse['a'];
                            $a = $data_rerponse_encode['a'];
                        }
                        ?>
                        <input type="email" class="form-control" value="<?php echo $email; ?>" readonly>
                    </div>
                  
                       <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                            <select class="custom-select" name="country" id="country">
                                                
                                            <option selected>Country Code</option>;
                                                <?php foreach ($countries as $countrie) { ?>
                                                    <option value="<?php echo $countrie->id ; ?>"><?php echo $countrie->country_name . ' ( +' . $countrie->calling_code . ' ) '; ?></option>
                                                <?php } ?>
                                            </select>
                                            </div>
                                        </div>
                                    </div>
                    <!-- Countries ENds-->

                    <div class="form-group">
                        <label>Phone</label>
                        <?php
                        $phone = '';

                        if (isset($data_rerponse['e'])) {
                            $phone .= $data_rerponse['e'];
                            $e = $data_rerponse_encode['e'];
                        }
                        ?>
                        <input type="text" class="form-control" value="<?php echo $phone; ?>" readonly>
                    </div>
                    <div class="form-group">
                    <?php
                        $dob = '';

                        if (isset($data_rerponse['g'])) {
                            $dob .= $data_rerponse['g'];
                            $g = $data_rerponse_encode['g'];
                        }
                        ?>
                        <label>Date of Birth</label><span style="color:red"> *</span>
                                <input type="date" id="dob" name="dob" class="form-control" format="YYYY-MM-DD"
                                    placeholder="Date of Birth" max="<?php echo date("Y-m-d"); ?>" required
                                    value="<?php echo $dob; ?>" />
                    </div>
                    <div class="form-group">
                        <?php 
                        $city = '';

                        if (isset($data_rerponse['h'])) {
                            $city .= $data_rerponse['h'];
                            $h = $data_rerponse_encode['h'];
                        }
                        echo $this->Form->input('city', [
                            'label' => 'City Name',
                            'class' => 'form-control',
                            'value' => $city,
                            
                        ]); ?>
                    </div>
                    <div class="form-group">
                        <?php
                        $type = ['Male' => 'Male', 'Female' => 'Female'];
                        echo $this->Form->input('gender', [
                            'type' => 'select',
                            'options' => $type,
                            'label' => 'Gender',
                            'class' => 'form-control',
                        ]);
                        ?> 
                    </div>
                    <div>
                        <input type="hidden" name="a" value="<?php echo $d; ?>">
                        <input type="hidden" name="b" value="<?php echo $b . " " . $c; ?>">
                        <input type="hidden" name="c" value="<?php echo $a; ?>">
                        <input type="hidden" name="d" value="<?php echo $e; ?>">
                        <input type="hidden" name="i" value="<?php echo $i; ?>">
                     
                    </div>
                    <div class="text-center">
                        <?= $this->Form->button(__('Submit'), [
                            'class' => 'btn btn-info',
                            'id' => 'submit_btn',
                        ]) ?>
                    </div>
                </div>
            <?= $this->Form->end() ?>
        </div>
        <div class="copyrights text-center">
                <p> Copyright <i class="far fa-copyright"></i>
                <script>document.write(new Date().getFullYear())</script> AKcess Labs. All rights reserved </p>
         </div>
    </div>
</div>
