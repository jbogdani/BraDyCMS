<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 1, 2012
 */
 
try
{
	$root = './';
	require_once $root . 'lib/globals.inc';
	
	$controller = new Controller();
	
	$controller->route();
}
catch (Exception $e)
{
	error_log($e->getMessage());
	echo 'Something went wrong. Please check error log for details';
}