<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license			MIT, See LICENSE file
 * @since			Dec 1, 2012
 */
try
{
	require_once 'lib/globals.inc';
  
  if (defined('CREATE_SITE'))
  {
    header('location: ./admin');
  }

	$outHtml = new OutHtml($_GET, $_SESSION['lang']);
	
	$settings = unserialize(CACHE);
	$settings['autoescape'] = false; 
	
	$twig = new Twig_Environment(new Twig_Loader_Filesystem('./sites/default'), $settings);
	
	if ($_SESSION['debug'])
	{
		$twig->addExtension(new Twig_Extension_Debug());
	}
	
	$function = new Twig_SimpleFunction('file_exists', function ($file) {
		return file_exists($file);
	});
	
	$twig->addFunction($function);
  
  
  $filter = new Twig_SimpleFilter('parseTags', function ($string)
    {
      return customTags::parseContent($string);
    });
  
  $twig->addFilter($filter);
	
	echo $twig->render('index.twig', array(
			'html'=>$outHtml
	));
}
catch (Exception $e)
{
	if ($_SESSION['debug'])
	{
		var_dump($e);
	}
	echo tr::get('error_check_log');
	error_log($e->getMessage());
}