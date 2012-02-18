<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Nov 27, 2011
 */

try
{
	require_once 'includes/class.tableAction.inc';

	$tbA = new tableAction();
	
	$tbA->importXLS($_GET['tb'], $_GET['file']);
	
	$ret['status'] = 'success';
	$ret['verbose'] = 'Il file è stato processato con successo';
}
catch (MyExc $e)
{
	$ret['status'] = 'error';
	$ret['verbose'] = 'Errore nel processare il file. ' . $e->getMessage();
	
	$e->log();
}

echo json_encode($ret);