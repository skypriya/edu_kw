<?php
if (!function_exists('base_url')) {
    function base_url($atRoot = false, $atCore = false, $parse = false)
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $http =
                isset($_SERVER['HTTPS']) &&
                strtolower($_SERVER['HTTPS']) !== 'off'
                    ? 'https'
                    : 'http';
            $hostname = $_SERVER['HTTP_HOST'];
            $dir = str_replace(
                basename($_SERVER['SCRIPT_NAME']),
                '',
                $_SERVER['SCRIPT_NAME']
            );

            $core = preg_split(
                '@/@',
                str_replace(
                    $_SERVER['DOCUMENT_ROOT'],
                    '',
                    realpath(dirname(__FILE__))
                ),
                null,
                PREG_SPLIT_NO_EMPTY
            );
            $core = $core[0];

            $tmplt = $atRoot
                ? ($atCore
                    ? "%s://%s/%s/"
                    : "%s://%s/")
                : ($atCore
                    ? "%s://%s/%s/"
                    : "%s://%s%s");
            $end = $atRoot
                ? ($atCore
                    ? $core
                    : $hostname)
                : ($atCore
                    ? $core
                    : $dir);
            $base_url = sprintf($tmplt, $http, $hostname, $end);
        } else {
            $base_url = BASE_ORIGIN_URL;
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
} ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?= $this->Html->charset() ?>  
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>
    <?php echo COMP_NAME_TITLE; ?>
    </title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <!-- Fonts and icons -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
    <!-- CSS Files -->
    <!-- <?= $this->Html->css('style.css') ?> -->
    <!--<?= $this->Html->css('css/material-dashboard.css?v=2.1.1') ?>
    <?= $this->Html->css('custom-style.css') ?>    
    <?= $this->Html->css('demo/datatables.min') ?>
    <?= $this->Html->css('demo/select2.min') ?>
    <?= $this->Html->css('demo/bootstrap.min') ?>
    <?= $this->Html->css('demo/jquery-ui') ?>
    <?= $this->Html->css('demo/multiselect/jquery.multiselect') ?>
    <?= $this->Html->css('jquery.tokenize.css') ?>
    <?= $this->Html->css('toastr.min.css') ?>-->
    <!-- Bootstrap Core CSS -->
    <?= $this->Html->css('../new-assets/plugins/bootstrap/css/bootstrap.min.css') ?>
    <!-- Custom CSS -->
    <?= $this->Html->css('../new-css/style.css') ?>
    <!-- You can change the theme colors from here -->
    <?= $this->Html->css('../new-css/colors/blue.css') ?>
    <!--Custom Style CSS -->
    <?= $this->Html->css('new-custom-style.css') ?>    
    <script>
    var csrfToken = <?php echo json_encode(
        $this->request->getParam('_csrfToken')
    ); ?>;
    var baseurl = '<?php echo $this->Url->build(null, true); ?>';
    var burl = '<?php $url = base_url(true); ?>';
  
    </script>
  </head>
  
  <body>
     

      <section id="wrapper">        
          <?= $this->Flash->render() ?>
          <?= $this->fetch('content') ?>
      </div>
      
  <!--   Core JS Files   -->
  <!--<?= $this->Html->script('js/core/jquery.min') ?>
  <?= $this->Html->script('js/core/popper.min') ?>-->
  <!--jQuery sidebar -->
  <?= $this->Html->script('../new-assets/plugins/jquery/jquery.min.js') ?>
  <!-- Bootstrap tether Core JavaScript -->
  <?= $this->Html->script('../new-assets/plugins/popper/popper.min.js') ?>
  <?= $this->Html->script('../new-assets/plugins/bootstrap/js/bootstrap.min.js') ?>
  <!-- slimscrollbar scrollbar JavaScript -->
  <?= $this->Html->script('../new-js/jquery.slimscroll.js') ?>
  <!--Wave sidebar -->
  <?= $this->Html->script('../new-js/waves.js') ?>
  <!--Menu sidebar -->
  <?= $this->Html->script('../new-js/sidebarmenu.js') ?>
  <!--stickey kit -->
  <?= $this->Html->script('../new-assets/plugins/sticky-kit-master/dist/sticky-kit.min.js') ?>
  <?= $this->Html->script('../new-assets/plugins/sparkline/jquery.sparkline.min.js') ?>
  <!--Custom JavaScript -->
  <?= $this->Html->script('../new-js/custom.min.js') ?>
  <!-- Style switcher -->
  <?= $this->Html->script('../new-assets/plugins/styleswitcher/jQuery.style.switcher.js') ?>

  <?php
  $contr = $this->request->getParam('controller');
  $act = $this->request->getParam('action');
  if ($contr == 'Users' && $act == 'login') { ?>
    <?= $this->Html->script('js/qrcode/qrcode') ?>
<?php }
  ?>

    
     <?= $this->fetch('script') ?>
     
     <script type="text/javascript">
        
 <?php if ($contr == 'Users' && $act == 'login') { ?>
        const API_KEY = '<?php echo SITE_API_KEY_URL; ?>';   
        const ORIGIN_URL = '<?php echo ORIGIN_URL; ?>';       
        
        let qrcode = new QRCode(document.getElementById("qrcode"), {
            width : 200,
            height : 200
        });

        function makeCode (code) {
            if (!code) {
                alert("Unable to Ggenerate QR Code");
                return;
            }            
            qrcode.makeCode(code);
        }

        let getToken = () => {
          return new Promise((resolve, reject) => {
            $.ajax({             
              url:'<?php echo AK_ORIGIN_URL; ?>auth/generate-token',
              //https://ak-api-v2.akcess.dev/auth/generate-token
              type: 'POST',
              dataType: 'json',
              headers: {
                  apikey: API_KEY,
                  origin:ORIGIN_URL
              },
              beforeSend: function (request) {
                  request.withCredentials = false;
              },
              success: (res) => {
                if (res.status)
                  return resolve(res.data.token)
                return reject(false)
              },
              error: (err) => {
                return reject(err)
              }
            })
          })
        }

        let getLoginUUID = (token) => {
          return new Promise((resolve, reject) => {         
            $.ajax({
              url: '<?php echo AK_API_BASE_URL; ?>auth/generate-uuid',
              //https://ak-api-v2.akcess.dev/generateQrCode OLD
              //https://ak-api-v2.akcess.dev/auth/generate-uuid NEW
              type: 'POST',
              dataType: 'json',
              headers: {
                apikey: API_KEY,
                authorization:token,
                origin:ORIGIN_URL
              },
              data: {apikey: API_KEY, authorization: token},
              success: (res) => {
                $("#load").hide();
                if (res.status)
                  return resolve(res.data)
                  return reject(false);
              },
              error: (err) => {
                return reject(err)
              }
            })
          })
        }

        let checkQRCodeScanStatus = (token) => {
          
          poll = function() {
            
            $(".atoken").val(token);
            $.ajax({
              //url: '<?php echo AK_API_BASE_URL; ?>scanLoginStatus',               
              url: '<?php echo AK_API_BASE_URL; ?>auth/login', 
              //https://ak-api-v2.akcess.dev/auth/login
              type: 'post',
              headers: {
                apikey: API_KEY,
                authorization:token,
                origin: ORIGIN_URL,
                uuid: document.getElementById("text").value
              },
              data: {uuid: document.getElementById("text").value, api: API_KEY, authorization:token},
              crossDomain: true,
              success: function(response) { // check if available
               
                if ( response.status ) {
                  clearInterval(pollInterval); // optional: stop poll function
                  console.log(response.data.user);
                  $.ajax({
                    type: 'POST',
                    url: burl + 'getDetails?akcessid='+response.data.user.akcessId,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    processData: false,
                    contentType: false,
                    success: function(responseData) {
                        
                        if (responseData.result == 'success') {
                            $(".akcessid").val(response.data.user.akcessId);
                            $(".email").val(response.data.user.email);
                            $(".firstName").val(response.data.user.firstName);  
                            $(".lastName").val(response.data.user.lastName);
                            $(".phone").val(response.data.user.phone);
                            
                            $('.logindata').val(JSON.stringify(response.data.user));
                                    
                            $('.loginform').submit();
                            return false;
                            
                        } else {                            
                            $(".akcessid").val(response.data.user.akcessId);
                            $(".email").val(response.data.user.email);
                            $(".firstName").val(response.data.user.firstName);  
                            $(".lastName").val(response.data.user.lastName);
                            $(".phone").val(response.data.user.phone);
                            $('.logindata').val(JSON.stringify(response.data.user));
                                    
                            $('.loginform').submit();
                            return false;
                        }
                        return false;
                    },
                });
                    
                }
              },
              error: function() { // error logging
                console.log('Error!');
              }
            });
          },
          pollInterval = setInterval(function() { // run function every 2000 ms
            poll();
          }, 5000);
          poll(); // also run function on init
        }

        $("#submit_pin").click(function(e){
          e.preventDefault();
          //console.log(this.href)
              $.ajax({
                  url: 'https://mobile.akcess.dev:3000/do3rdPartyLogin', //
                  type: 'post',
                  data: {uuid: document.getElementById("text").value, 'passCode': $("#passCode").val(), api: API_KEY}, 
                  success: function(rsult) { // check if available
                    //console.log(data)
                    if ( rsult.status ) { // get and check data value
                      $('#err').text('');

                      $('.logindata').val(JSON.stringify(rsult.data));
                      
                        $('.loginform').submit();

                    }else{
                      $('#err').text('Wrong PIN entered.');
                    }
                  },
                  error: function() { // error logging
                    $('#err').text('Something went Wrong.');
                  }
              });
        });

        (function() {
          getToken().then(token => {
            getLoginUUID(token).then(uuid => {
              document.getElementById("text").value = uuid.webUUID
              document.getElementById("qrdata").value = uuid.webUUID;
              makeCode(JSON.stringify(uuid))
              checkQRCodeScanStatus(token)
            }).catch(err => {
              setTimeOut(() => {location.reload()}, 3000)
            })
          }).catch(err => {
            setTimeOut(() => {location.reload()}, 3000)
          })
        }())
      <?php } ?>    
          
        $(document).ready(function() {

            $('#flash_success').addClass('animated fadeInDown');
            $( ".close" ).click(function() {
                jQuery('#flash_success').removeClass('fadeInDown');
                jQuery('#flash_success').addClass('fadeOutUp');
            });
            setTimeout(function() {
                jQuery('#flash_success').removeClass('fadeInDown');
                jQuery('#flash_success').addClass('fadeOutUp');
            }, 3000);
        });
      </script>

  </body>
</html>
