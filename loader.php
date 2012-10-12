<?php

$root = './';

try
{
	require_once $root . 'lib/globals.inc';

	if ($_REQUEST['obj'])
	{
		try
		{
			// get or post single (string) param
			if ($_REQUEST['param'] AND is_string($_REQUEST['param']))
			{
				$param = array($_REQUEST['param']);
			}
			// no post and no no post/get param
			else if (!$_REQUEST['param'] AND empty($_POST))
			{
				$param = array();
			}
			// no post/get param, but post data
			else if (!$_REQUEST['param'] AND !empty($_POST))
			{
				$param = array($_POST);
			}
			else if ($_REQUEST['param'] && !empty($_POST))
			{
				$param = array( array_merge($_POST, $_REQUEST['param']));
			}
			else
			{
				$param = $_REQUEST['param'];
			}
				
			call_user_func_array(array($_REQUEST['obj'], $_REQUEST['method']), $param);
		}
		catch(myException $e)
		{
			echo gui::message( sprintf(tr::get('error_in_method'), $_REQUEST['obj'] . '::' . $_REQUEST['method']), 'error', true);
			$e->log();
		}
	}
	else if ($_REQUEST['mod'])
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
