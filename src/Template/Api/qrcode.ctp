
<!-- Login Page -->

<div class="container login-page">

    <div class="qrcode-outer-box login-outer-box d-flex align-items-center">
    
        <div class="login-inner-box w-100">
        <div class="image-section mb-3">
        <h3 class="qrcodeh3"><?php echo $eformName; ?></h3>
            
            </div>
            <div class="qr-section">
                <div id="document_qrcode" class="qr-code"></div>
            </div>   
            
        </div>
        <div class="copyrights text-center">
            <p> Copyright <i class="far fa-copyright"></i>
                <script>document.write(new Date().getFullYear())</script> AKcess Labs. All rights reserved </p>
        </div>
    </div>
</div>
<?= $this->Html->script('js/qrcode/qrcode') ?>
<script type="text/javascript">

    
    var qrcode = new QRCode(document.getElementById("document_qrcode"), {
        width: 200,
        height: 200
    });

    qrcode.makeCode(<?php echo $data; ?>);

</script>