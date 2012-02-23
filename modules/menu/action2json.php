<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Nov 1, 2011
 */

try
{
	$menu = new Menu();

	switch ($_GET['a'])
	{
		case 'edit':
			if ($menu->update($_POST))
			{
				$obj->type = 'success';
				$obj->text = 'La voce del menu è stata aggiornata';
				echo json_encode($obj);
			}
			else
			{
				throw new MyExc('Non è stato possibile aggiornare la voce del menu.');
			}
			break;
				
		case 'add':
			if ($menu->add($_POST))
			{
				$obj->type = 'success';
				$obj->text = 'La voce del menu è stata aggiornata';
				echo json_encode($obj);
			}
			else
			{
				throw new MyExc('Non è stato possibile aggiungere la voce del menu. Controllare che le voci testo, link e menu non siano vuote!');
			}
			break;
				
		case 'erase':
			if ($menu->delete($_GET['id']))
			{
				$obj->type = 'success';
				$obj->text = 'La voce del menu è stata cancellata';
				echo json_encode($obj);
			}
			else
			{
				throw new MyExc('Non è stato possibile cancellare la voce del menu!');
			}
			break;
				
		default:
			throw new MyExc('Nessuna azione sa eseguire');
			break;
	}



		
}
catch (MyExc $e)
{
	$e->log();
	$obj->type='error';
	$obj->text = $e->getMessage();
	echo json_encode($obj);
}