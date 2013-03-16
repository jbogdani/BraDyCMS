<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 1, 2012
 */
 
try
{
	require_once 'lib/globals.inc';

	$out = new Out($_GET, $_SESSION['lang']);
	
	$htmlOut = new htmlOut($out);
	
	$twig = new Twig_Environment(new Twig_Loader_Filesystem('./sites/default'), unserialize(CACHE));
	if ($__debug)
	{
		$twig->addExtension(new Twig_Extension_Debug());
	}
	echo $twig->render('index.html', array(
			'out'=>$out,
			'html'=>$htmlOut
	));
}
catch (Exception $e)
{
	if ($__debug)
	{
		var_dump($e);
	}
	echo tr::get('error_check_log');
	error_log($e->getMessage());
}