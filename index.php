<?php
try
{
	session_start();
	
	require_once 'lib/globals.inc';
	
	$html = new publicHtml();

	$html->mainSetter($_GET, $_SESSION['lang']);
	
	$browser = new Browser();
	
	if ($cfg['only_mobile'] OR ($browser->isMobile() AND file_exists('./sites/default/mobile.php')))
	{
		require_once './sites/default/mobile.php';
	}
	else
	{
		require_once './sites/default/index.php';
	}
}
catch (MyExc $e)
{
	echo utils::tr('Qualcosa è andato storto. Controllare contattare l\'amministrattore per maggiori informazioni');
	$e->log();
}
?>