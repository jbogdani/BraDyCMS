<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Nov 1, 2011
 */

try
{
	switch ($_GET['a'])
	{
		case 'save':
			try
			{
				$translate = new translate();
					
				if ($translate->save_translation($_GET['context'], $_GET['lang'], $_GET['id'], $_POST, $_GET['t_id']))
				{
					$out->text = "La traduzione è stata salvata";
					$out->type = 'success';
					echo json_encode($out);
				}
				else
				{
					throw new MyExc('Non è stato possibile salvare i dati');
				}
			}
			catch (MyExc $e)
			{
				$e->log();
				throw new MyExc('Non è stato possibile salvare i dati');
			}
			
			break;
	}
}
catch (MyExc $e)
{
	$out->text = $e->getMessage();
	$out->type = 'error';
	$e->log();
	echo json_encode($out);
}