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
	
	$users_log = './sites/default/users.log';
	
	if (!file_exists($users_log))
	{
		$fh = @fopen($users_log, 'w');
		@fclose($fh);
	}

	if(array_key_exists($_POST['username'], $cfg['users']) AND $cfg['users'][$_POST['username']] == $_POST['password'])
	{
		$_SESSION['user_confirmed'] = true;
		
		$json = json_decode(file_get_contents("http://api.easyjquery.com/ips/?ip=" . $_SERVER['REMOTE_ADDR'] . "&full=true"));
		
		error_log('user:' . $_POST['username'] . ' logged IN on ' . date('r') . ' using IP :' . $_SERVER['REMOTE_ADDR'] . (is_object($json) ? ' from ' .$json->countryName . ', ' . $json->cityName : '') . "\n", 3, $users_log);
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
		
		error_log('user:' . $_POST['username'] . ' logged OUT on ' . date('r') . ' using IP :' . $_SERVER['REMOTE_ADDR'] . "\n", 3, $users_log);
		
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
	'jquery.tablesorter.js',
	'jquery.tablesorter.filter.js',
	'jquery.combobox.js',
	'admin.js'
), $_GET);
?>

	<script src="./tiny_mce/tiny_mce.js" type="text/javascript"></script>
	<script type="text/javascript">
	tinyMCE.init({
	    // General options
	    mode : "exact",
		theme : 'advanced',
		plugins : 'pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist',

	    // Theme options
		theme_advanced_toolbar_location : 'top',
		theme_advanced_toolbar_align : 'left',
		extended_valid_elements : 'script[language|type]',
		theme_advanced_resizing : true,
	    content_css : './sites/default/css/styles.css',
	    
	    theme_advanced_statusbar_location : 'bottom',

		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect",
	    theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,preview,|,forecolor,backcolor",
	    theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,|,ltr,rtl,|,fullscreen,|,attribs"
	});
	</script>

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
			$('button').button();
		});
	</script>
<script type="text/javascript">
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