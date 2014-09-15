<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license			MIT, See LICENSE file
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
  error_log($e->getMessage()  . '; trace: ' . var_export($e->getTrace(), 1));
	echo 'Something went wrong. Please check error log for details';
}