<?php
try
{
	session_start();
	require_once 'lib/globals.inc';
	
	$html = new publicHtml();

	$html->mainSetter($_GET, $_SESSION['lang']);
	
	require_once './sites/default/index.php';
	
}
catch (MyExc $e)
{
	echo utils::tr('Qualcosa è andato storto. Controllare contattare l\'amministrattore per maggiori informazioni');
	$e->log();
}
?>