<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Oct 31, 2011
 */

session_start();
$root = '../';

require_once $root . 'includes/globals.inc';


if(array_key_exists($_POST['username'], $cfg['users']) AND $cfg['users'][$_POST['username']] == $_POST['password']){

	$_SESSION['user_confirmed'] = true;
}

if ( $_GET['logout'] ) {
	$_SESSION['user_confirmed'] = false;
}
if ( !$_SESSION['user_confirmed'] ) :

require_once $root . 'admin/login_form.php';
exit;
endif;

?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="robots" content="index, follow" />

<title>Admin</title>

<link rel="stylesheet"
	href="../css/ui-lightness/jquery-ui-1.8.10.custom.css" type="text/css" />
<link rel="stylesheet" href="../css/fileuploader.css" type="text/css" />
<link rel="stylesheet" href="../css/jquery.toastmessage.css"
	type="text/css" />
<link rel="stylesheet" href="../css/admin.css" type="text/css" />

<script src="../js/jquery-1.4.4.min.js" type="text/javascript"></script>
<script src="../js/jquery-ui-1.8.10.custom.min.js"
	type="text/javascript"></script>
<script src="../js/fileuploader.js" type="text/javascript"></script>
<script src="../js/jquery.toastmessage.js" type="text/javascript"></script>
<script src="../tiny_mce/jquery.tinymce.js" type="text/javascript"></script>
<script src="../js/jquery.tablesorter.min.js" type="text/javascript"></script>
<script src="../js/admin.js" type="text/javascript"></script>
<script type="text/javascript">
		$(document).ready(function(){
			$('#tabs').tabs({
				cache: true,
				tabTemplate: "<li><a href='#{href}'>#{label}</a> <span class='ui-icon ui-icon-close'>Remove Tab</span></li>",
				spinner: '<img src="./css/arrows-loader.gif"  alt="loading" />',
				add: function(event, ui) {
					$('#tabs').tabs('select', '#' + ui.panel.id);
		 		}
			});
			$( "#tabs span.ui-icon-close" ).live( "click", function() {
				var tabs = $('#tabs');
				var index = $( "li", tabs ).index( $( this ).parent() );
				tabs.tabs( "remove", index );
			});
	
		});
	
	</script>
</head>

<body>
	<div id="tabs">
		<ul>
			<li><a href="loader.php?mod=main">Home</a></li>
		</ul>
	</div>

	<!-- 
<div id="topmenu">
	<ul id="nav">
		<li class="current"><a href="./admin.php">Inizio</a></li>
		<li><a href="javascript:void(0)">Articoli</a>
			<ul>
				<li><a href="javascript:void(0)" onclick="add_new_article()">Nuovo</a></li>
				<li><a href="javascript:void(0)" onclick="show_art_list();">Mostra tutti</a></li>
			</ul>
		</li>
		<li><a href="javascript:void(0)" onclick="show_img_list()">Immagini e files</a>
		</li>
		<li><a href="javascript:void(0)">Menu</a>
			<ul>
				<li><a href="javascript:void(0)" onclick="add_new_menu();">Aggiungi nuova voce</a></li>
				<li><a href="javascript:void(0)">Modifica menu: </a>
					<ul id="menu_ul"></ul>
				</li>
			</ul>
		</li>
		<li><a href="javascript:void(0)">Traduzioni</a>
			<ul id="trad_ul"></ul>
		</li>
		<li><a href="./" target="_blank">Vedi sito</a></li>
		<li><a href="admin.php?logout=1">Esci</a></li>
	</ul>
</div>
<div id="main"></div>
 -->
</body>
</html>
