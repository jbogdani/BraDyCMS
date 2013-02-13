<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 1, 2012
 */
 
$root = './';

try
{
	require_once $root . 'lib/globals.inc';

	if ($_REQUEST['obj'])
	{
		$param = array_merge((array)$_GET['param'], array('post'=>$_POST));
		
		call_user_func_array(array($_GET['obj'], $_GET['method']), $param);
	}
	else
	{
		throw new Exception('No data to load');
	}

}
catch (Exception $e)
{
	error_log($e->getMessage());
	echo 'Something went wrong. Please check error log for details';
}