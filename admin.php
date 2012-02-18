<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Oct 31, 2011
 */
try
{
	session_start();
	$root = './';
	

	require_once $root . 'lib/globals.inc';

	if(array_key_exists($_POST['username'], $cfg['users']) AND $cfg['users'][$_POST['username']] == $_POST['password'])
	{
		$_SESSION['user_confirmed'] = true;
	}
	else
	{
		if ($_POST['username'] AND $_POST['password'])
		{
			$log_message = 'denied';
		}
	}

	if ( $_GET['logout'] )
	{
		$_SESSION['user_confirmed'] = false;
		$log_message = 'out';
	}
	
	utils::emptyTmp();
	?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="robots" content="index, follow" />

	<title>Admin</title>
<?php
utils::css( array(
	'jquery-ui-1.8.16.custom.css',
	'fileuploader.css',
	'jquery.toastmessage.css',
	'admin.css'
), $_GET);

utils::js(array(
	'jquery-1.7.min.js',
	'jquery-ui-1.8.16.custom.min.js',
	'fileuploader.js',
	'jquery.toastmessage.js',
	'tiny_mce/jquery.tinymce.js',
	'jquery.tablesorter.js',
	'jquery.tablesorter.filter.js',
	'jquery.combobox.js',
	'admin.js'
), $_GET);
?>

<?php if ($cfg['modules']['tables']) :?>

	<script src="./js/js/i18n/grid.locale-<?php echo $cfg['sys_lang']; ?>.js" type="text/javascript"></script>
	<script src="./js/jquery.jqGrid.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="./css/ui.jqgrid.css" type="text/css" />

<?php endif; ?>
	<script type="text/javascript">
		$(document).ready(function(){
<?php if ( $_SESSION['user_confirmed'] ): ?>
			layout.content();
<?php else: ?>
			layout.login('<?php echo $log_message; ?>');
<?php endif; ?>
		});
		$('button').button();
	</script>
</head>

<body>

</body>
</html>
<?php
}
catch (MyExc $e)
{
	$e->log();
	echo 'Qualcosa è andato storto: ' . $e->getMessage();
}

?>