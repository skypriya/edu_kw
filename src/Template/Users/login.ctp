

<?php
if (!function_exists('base_url')) {

    function base_url($atRoot = false, $atCore = false, $parse = false) {
        if (isset($_SERVER['HTTP_HOST'])) {
            $http = isset($_SERVER['HTTPS']) &&
                    strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
            $hostname = $_SERVER['HTTP_HOST'];
            $dir = str_replace(
                    basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']
            );

            $core = preg_split(
                    '@/@', str_replace(
                            $_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))
                    ), null, PREG_SPLIT_NO_EMPTY
            );
            $core = $core[0];

            $tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
            $end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
            $base_url = sprintf($tmplt, $http, $hostname, $end);
        } else {
            $base_url = 'http://localhost/';
        }

        if ($parse) {
            $base_url = parse_url($base_url);
            if (isset($base_url['path'])) {
                if ($base_url['path'] == '/') {
                    $base_url['path'] = '';
                }
            }
        }

        return $base_url;
    }

}
?>

<div class="login-register" style="background-image:url(../new-assets/images/background/login-register.jpg);">
    <div class="login-box card">
        <div class="card-body">
            <div class="image-section text-center mb-3">
                <?= $this->Html->image('logo.png', ['alt' => 'EDU']) ?>
            </div>
            <div class="form-horizontal form-material">
                <h2 class="box-title m-b-20 text-center">Welcome</h3>                        
                <div class="qr-section">                            
                    <?php $url = base_url(true); ?>
                    <div id="qrdiv">
                         <h5 class="box-title m-b-20 text-center">Scan QR code using AKcess application</h3>
                         <input id="text" type="hidden" value="" />
                         <div id="qrcode" class="qr-code m-b-20  d-flex justify-content-center">
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
                         </div>
                         <form method="post" accept-charset="utf-8" id="loginform" class="loginform" action="<?php echo $url; ?>">
                            <input type="hidden" name="qrdata" id="qrdata" class="qrdata" value="" />
                            <input type="hidden" name="logindata" id="logindata" class="logindata" value="" />
                            <input type="hidden" name="akcessid" id="akcessid" class="akcessid" />
                            <input type="hidden" name="email" id="email" class="email"  />
                            <input type="hidden" name="firstName" id="firstName" class="firstName" />
                            <input type="hidden" name="lastName" id="lastName" class="lastName" />
                            <input type="hidden" name="phone" id="phone"  class="phone" />
                            <input type="hidden" name="atoken" id="atoken"  class="atoken" />
                         <?= $this->Form->end() ?>
                    </div>
                    <div id="pindiv">
                        <h5 class="box-title m-b-20 text-center">Login</h5>
                        <?=
	        $this->Form->create(null, [
	         'id' => 'loginform',
	         'class' => 'loginform',
	        ])
	        ?>
                        <div class="form-group">
                            <div class="col-12">
                                <input type="password" class="form-control" id="passCode" placeholder="Enter 5 Digit Pin"  minlength=5 maxlength=5>                             
                                <small class="form-text text-danger"><b>PIN is required. PIN must be 5 digit.</b></small>
                            </div>
                        </div>
                        <input type="hidden" name="qrdata" id="qrdata" class="qrdata" value="" />
                        <input type="hidden" name="logindata" id="logindata" class="logindata" value="" />
                        <input type="hidden" name="akcessid" id="akcessid" class="akcessid" />
                        <input type="hidden" name="email" id="email" class="email"  />
                        <input type="hidden" name="firstName" id="firstName" class="firstName" />
                        <input type="hidden" name="lastName" id="lastName" class="lastName" />
                        <input type="hidden" name="phone" id="phone"  class="phone" />
                        <input type="hidden" name="atoken" id="atoken"  class="atoken" />
                        <div class="form-group text-center m-t-20">
                            <div class="col-12">
                                <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit" id="submit_pin">Log In</button>
                            </div>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>                    
        </div>
    </div>
</div> 
<!--

<?php
if (!function_exists('base_url')) {

    function base_url($atRoot = false, $atCore = false, $parse = false) {
        if (isset($_SERVER['HTTP_HOST'])) {
            $http = isset($_SERVER['HTTPS']) &&
                    strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
            $hostname = $_SERVER['HTTP_HOST'];
            $dir = str_replace(
                    basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']
            );

            $core = preg_split(
                    '@/@', str_replace(
                            $_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))
                    ), null, PREG_SPLIT_NO_EMPTY
            );
            $core = $core[0];

            $tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
            $end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
            $base_url = sprintf($tmplt, $http, $hostname, $end);
        } else {
            $base_url = 'http://localhost/';
        }

        if ($parse) {
            $base_url = parse_url($base_url);
            if (isset($base_url['path'])) {
                if ($base_url['path'] == '/') {
                    $base_url['path'] = '';
                }
            }
        }

        return $base_url;
    }

}
?>

<div class="container login-page">
    <div class="login-outer-box d-flex align-items-center">
        <div class="login-inner-box w-100">
            <div class="image-section mb-3">
<?= $this->Html->image('logo.png', ['alt' => 'EDU']) ?>
            </div>
            <div class="logo text-uppercase">Welcome</div>
            <p class="mb-0">Sign in with QR code</p>
            <div class="qr-section">
<?php $url = base_url(true); ?>
                <div id="qrdiv">
                    <input id="text" type="hidden" value="" /><br />
                    <div id="qrcode" class="qr-code"></div>
                    <form method="post" accept-charset="utf-8" id="loginform" class="loginform" action="<?php echo $url; ?>">
                        <input type="hidden" name="qrdata" id="qrdata" class="qrdata" value="" />
                        <input type="hidden" name="logindata" id="logindata" class="logindata" value="" />
                        <input type="hidden" name="akcessid" id="akcessid" class="akcessid" />
                        <input type="hidden" name="email" id="email" class="email"  />
                        <input type="hidden" name="firstName" id="firstName" class="firstName" />
                        <input type="hidden" name="lastName" id="lastName" class="lastName" />
                        <input type="hidden" name="phone" id="phone"  class="phone" />
                        <input type="hidden" name="atoken" id="atoken"  class="atoken" />
<?= $this->Form->end() ?>
                </div>

                <div id="pindiv">
                    <h3>Login</h3>
<?=
$this->Form->create(null, [
    'id' => 'loginform',
    'class' => 'loginform',
])
?>
                    <div class="form-group text-left">
                        <input type="password" class="form-control" id="passCode" placeholder="Enter 5 Digit Pin"  minlength=5 maxlength=5>                             
                        <small class="form-text text-danger"><b>PIN is required. PIN must be 5 digit.</b></small>
                    </div>
                    <input type="hidden" name="qrdata" id="qrdata" class="qrdata" value="" />
                    <input type="hidden" name="logindata" id="logindata" class="logindata" value="" />
                    <input type="hidden" name="akcessid" id="akcessid" class="akcessid" />
                    <input type="hidden" name="email" id="email" class="email"  />
                    <input type="hidden" name="firstName" id="firstName" class="firstName" />
                    <input type="hidden" name="lastName" id="lastName" class="lastName" />
                    <input type="hidden" name="phone" id="phone"  class="phone" />
                    <input type="hidden" name="atoken" id="atoken"  class="atoken" />
                    <button type="submit" class="btn btn-primary " id="submit_pin">SUBMIT</button>
                    <?= $this->Form->end() ?>
                </div>
            </div>   
        </div>
        <div class="copyrights text-center">
            <p> Copyright <i class="far fa-copyright"></i>
                <script>document.write(new Date().getFullYear())</script> AKcess Labs. All rights reserved </p>
        </div>
    </div>
</div>-->