<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Nov 27, 2011
 */

try
{
	require_once 'lib/class.tableAction.inc';

	$tbA = new tableAction();
	
	$filename = TMP_DIR . 'export' . str_replace('.', null, microtime(1)) . '.xls';
	$tbA->export2XLS($_GET['tb'], $filename);
	
	echo $filename;
	
}
catch (MyExc $e)
{
	$e->log();
	echo 'error';
}