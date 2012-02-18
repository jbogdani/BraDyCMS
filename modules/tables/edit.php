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
	
	$oper = $_POST['oper'];
	
	unset($_POST['oper']);
	
	switch ($oper)
	{
		case 'edit':
			$tbA->updateRow($_GET['tb'], $_POST);
			break;
			
		case 'add':
			$tbA->insertRow($_GET['tb'], $_POST);
			break;
			
		case 'del':
			$tbA->deleteRow($_GET['tb'], $_POST['id']);
			break;
			
	}
	
	$resp = 'success';
	
}
catch (MyExc $e)
{
	$e->log();
	$resp= 'error';
}

echo $resp;