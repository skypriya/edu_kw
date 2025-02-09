<!DOCTYPE html>
<html lang="en">
  <head>
    <?= $this->Html->charset() ?>
    <!-- <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png"> -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>
    AKcess: EDU
    </title>   
    <?= $this->Html->css('style.css') ?>
  </head>

  <body>
      <div class="content">
        <div class="container-fluid">        
            <?php echo $data; ?>
            <div id="qrcode" class="qr-code"></div>
        </div>
      </div>
  </body>
</html>