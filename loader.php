<?php

$root = './';

try
{
	require_once $root . 'lib/globals.inc';

	if ($_REQUEST['mod'])
	{
		$path = 'modules/' . $_REQUEST['mod'] . '.php';

		if (file_exists($path))
		{
			if (!$_GET['nw'])
			{
				echo '<div id="mod' . str_replace('/', null, $_REQUEST['mod']) . '">';
			}
				
			require_once $path;
				
			if (!$_GET['nw'])
			{
				echo '<div>';
			}
		}
		else
		{
			throw new MyExc('Il modulo ' . $path . ' non è stato trovato');
		}
	}
	else
	{
		throw new MyExc('Nessun module specificato!');
	}

}
catch (MyExc $e)
{
	$e->log();
	echo 'Qualcosa è andato storto. Controllare il log degli errori per maggiori informazioni';
}

?>
