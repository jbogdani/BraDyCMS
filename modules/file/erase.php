<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Oct 31, 2011
 */

$file = base64_decode($_GET['file']);

try
{
	if ( is_file ( $file ) )
	{
		if ( !@unlink($file) )
		{
			throw new Exception("Nessuna è stato possibile cancellare il file: " . $file . "!");
		}
		else
		{
			$obj->type = 'success';
			$obj->text = "Il file è stato cancellato!";
		}

	}
	else if ( is_dir ( $file ) )
	{
		if ( !@rmdir($file) )
		{
			throw new Exception("Nessuna è stato possibile cancellare la cartella: " . $file . "!<br /> Controllare che la cartella sia vuota!");
		}
		else
		{
			$obj->type = 'success';
			$obj->text = "La cartalla è stata cancellata!";
		}
	}

	echo json_encode($obj);
}
catch(Exception $e)
{
	$obj->type = 'error';
	$obj->text = $e->getMessage();
	echo json_encode($obj);
}


