<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license			MIT, See LICENSE file
 * @since			Dec 1, 2012
 */
try
{
  $root = './';
  require_once $root . 'lib/globals.inc';
  
  $admin = new admin_ctrl;
 // var_dump($admin);
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
 	
	 	<meta charset="utf-8" />
	 	<title>bdus.CMS</title>
	 	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link href="./css/admin.css" rel="stylesheet" />
	    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	    <!--[if lt IE 9]>
	      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	    <![endif]-->
 	</head>

	<body<?php if (!$_SESSION['user_confirmed']) { echo ' class="login" '; }?>>
    
		<?php
    
    if ($stop_error)
    {
      $admin->showError($stop_error);
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
	<script src="./js/jquery-2.1.0.min.js"></script>
	<script src="./js/jquery.nestable.js"></script>
	<script src="./js/bootstrap-3.1.1.min.js"></script>
	<script src="./js/jquery.dataTables.js"></script>
	<script src="./js/dataTable-bootstrap.js"></script>
	
	<script src="./js/admin.js"></script>
	<script src="./js/jquery.pnotify.js"></script>
	<script src="./js/bootstrap-datepicker.js"></script>
	<script src="./js/select2.min.js"></script>
	<script src="./tiny_mce/tiny_mce.js"></script>
	<script src="./js/fileuploader.js"></script>
	<script src="./js/prettify.js"></script>
  <script src="./js/jquery.removeNotValid.js"></script>
	
	<?php if (!$_SESSION['user_confirmed']): ?>
	<script>
	$('#signin').on('submit', function(){
		$('#logerror').hide();
		$.post('controller.php?obj=log_ctrl&method=in', $(this).serialize(), function(data){

			if (data.status == 'success'){
				window.location.href = './admin';
				return false;
			} else {
				$('#logerror .text').html(data.text);
        $('#logerror').show();
			}
		}, 'json');
	});
	</script>
	<?php
	endif;
  
  if (defined('CREATE_SITE')):
  ?>
  <script>
    $('#newsite').on('submit', function(){
      $.post('./controller.php?obj=addsite_ctrl&method=build', $(this).serialize(), function(data){
        if (data.status == 'success'){
          $('#register').fadeOut();
          $('#message').fadeIn();
        } else {
          admin.message(data.text, data.status, false, true);
        }
      }, 'json');
    })
  </script>
  <?php endif; ?>
  
  </body>
</html>