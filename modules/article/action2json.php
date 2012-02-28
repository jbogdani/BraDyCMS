<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Oct 31, 2011
 */

try
{
	$article_edit = new ArticleEdit();

	switch($_GET['a'])
	{
		case 'erase':
			if (!$_REQUEST['id'])
			{
				throw new MyExc('Nessun articolo indicato per la cancellazione');
			}
			try
			{
				if (!$article_edit->delete( $_REQUEST['id'] ))
				{
					throw new MyExc("Non è stato possibile cancellare l'articolo " . $_REQUEST['id']);
				}
			}
			catch (MyExc $e)
			{
				$e->log();
				throw new Exception("Non è stato possibile cancellare l'articolo " . $_REQUEST['id']);
			}

			
				
			$out->type = 'success';
			$out->text = "L'articolo è stato cancellato!";
			break;
				
		case 'edit':
			try
			{
				if (!$article_edit->update($_GET['id'], $_POST))
				{
					throw new MyExc("Errore! L'articolo non è stato salvato!");
				}
			}
			catch (MyExc $e)
			{
				$e->log();
				throw new MyExc("Errore! L'articolo non è stato salvato!");
			}
			
				
			$out->type = 'success';
			$out->text = "L'articolo è stato salvato!";
			$out->id = $_GET['id'];
			break;
				
		case 'add':
			if ( !$_POST['title'] OR !$_POST['text_id'] )
			{
				throw new MyExc("Attenzione! I campi 'TITOLO' e 'ID TESTUALE' sono obbligatori<br />L'articolo <strong>NON</strong> è stato salvato");
			}
			
			try
			{
				if (!$article_edit->add($_POST))
				{
					throw new MyExc("Errore! L'articolo non è stato salvato!");
				}
			}
			catch (MyExc $e)
			{
				$e->log();
				throw new MyExc("Errore! L'articolo non è stato salvato!");
			}
				
			$out->type = 'success';
			$out->text = "L'articolo è stato salvato!";
			$out->id = $res;
			break;
				
		default:
			throw new MyExc('Nessuna azione da eseguire!');
	}
	echo json_encode($out);

}
catch (MyExc $e)
{
	$out->type = 'error';
	$out->text = $e->getMessage();
	echo json_encode($out);
}