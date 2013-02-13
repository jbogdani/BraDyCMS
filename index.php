<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 1, 2012
 */
 
try
{
	session_start();

	require_once 'lib/globals.inc';

	$str = './?art_title=comunismo-negato';
	
	$html = new publicHtml($_GET, $_SESSION['lang']);
	
	$dress = new dressHtml($html);
	
	$twig = new Twig_Environment(new Twig_Loader_Filesystem('./sites/default'), unserialize(CACHE));
	if ($__debug)
	{
		$twig->addExtension(new Twig_Extension_Debug());
	}
	echo $twig->render('index.html', array(
			'html'=>$html,
			'dress'=>$dress
	));
}
catch (Exception $e)
{
	echo tr::get('error_check_log');
	error_log($e->getMessage());
}