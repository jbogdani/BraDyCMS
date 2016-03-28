<?php
/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license      MIT, See LICENSE file
 * @since      Dec 1, 2012
 */
try
{
  $root = './';
  require_once $root . 'lib/globals.inc';

  $admin = new admin_ctrl();
}
catch (Exception $e)
{
  error_log($e->getTraceAsString());

  $stop_error = $e->getMessage() ;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>

    <meta charset="utf-8">
    <title>bdus.CMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="./bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="./bower_components/Ionicons/css/ionicons.min.css" rel="stylesheet">
    <link href="./bower_components/pnotify/dist/pnotify.css" rel="stylesheet">
    <link href="./bower_components/select2/select2.css" rel="stylesheet">
    <link href="./bower_components/select2/select2-bootstrap.css" rel="stylesheet">
    <link href="./bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link href="./bower_components/google-code-prettify/bin/prettify.min.css" rel="stylesheet">
    <link href="./bower_components/datatables/media/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="./bower_components/fineuploader-dist/dist/fine-uploader.min.css" rel="stylesheet">
    <link href="./css/admin.css?v=<?php echo version::current(); ?>" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>
  <body<?php if (!$_SESSION['user_confirmed']) { echo ' class="login" '; }?>>

    <?php

    if ($stop_error)
    {

      if ($admin)
      {
        $admin->showError($stop_error);
      }
      else
      {
        echo '<div class="container">'
          . '<div class="alert alert-danger text-center">Something went wrong! '
            . $stop_error
          . '</div>'
        . '</div>';
      }
    }
    else if (defined('CREATE_SITE'))
    {
      $admin->showCreateInstallForm();
    }
    else if (!$_SESSION['user_confirmed'])
    {
      $admin->showLoginForm();
    }
    else
    {
      $admin->showBody();
    }
    ?>

  <script src="controller.php?obj=tr&method=lang2json&param[]=true"></script>
  <script src="./bower_components/tinymce/tinymce.min.js"></script>
  <script src="./js/admin.min.js?v=<?php echo version::current(); ?>"></script>

  <?php
  if (defined('CREATE_SITE')):
  ?>
  <script>
    $('#newsite').on('submit', function(){
      $.post('./controller.php?obj=addsite_ctrl&method=build', $(this).serialize(), function(data){
        if (data.status === 'success'){
          $('#register').fadeOut();
          $('#message').fadeIn();
        } else {
          admin.message(data.text, data.status, false, true);
        }
      }, 'json');
    });
    </script>
  <?php
  else :
    if (!$_SESSION['user_confirmed']):
      if (cfg::get('grc_sitekey')):
        ?>
  <script src="https://www.google.com/recaptcha/api.js?onload=recaptchaCallback&render=explicit" async defer></script>
        <?php
      endif;
      ?>
  <script>
  <?php if (cfg::get('grc_sitekey')): ?>
  if (typeof recaptchaCallback !== 'function') {
    var called = false;
    function recaptchaCallback(){
      if (called) return;
      $('.g-recaptcha').each(function(i, el){
        grecaptcha.render(el, {
          'sitekey' : $(el).data('sitekey'),
          'callback': function(response) {
            if (response.length > 0){
              $('#login-button').prop('disabled', false);
            }
          }
        });
      });
      called = true;
    }
  }

  <?php endif; ?>
    $('#signin').on('submit', function(){
      $('#logerror').hide();
      $.post('controller.php?obj=log_ctrl&method=in', $(this).serialize(), function(data){
      if (data.status === 'success'){
          location.reload();
          $(window).trigger('hashchange');
          return false;
        } else {
          <?php if (cfg::get('grc_sitekey')): ?>
          grecaptcha.reset();
          <?php endif; ?>
          $('#logerror .text').html(data.text);
          $('#logerror').show();
          $('#login-button').button('reset');
        }
      }, 'json');
    });
  </script>
<?php endif; ?>
<?php endif; ?>

  </body>
</html>
