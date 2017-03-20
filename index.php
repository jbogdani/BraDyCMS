<?php
/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license      MIT, See LICENSE file
 * @since      Dec 1, 2012
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

  $twig = new Twig_Environment(
    new Twig_Loader_Filesystem('./sites/default' . ($_SESSION['sandbox'] ? '/' . $_SESSION['sandbox'] : '')),
    $settings
  );

  if ($_SESSION['debug'])
  {
    $twig->addExtension(new Twig_Extension_Debug());
  }
  // TODO: document intersect
  $intersect = new Twig_SimpleFunction('intersect', function () {
    return array_values(call_user_func_array('array_intersect',func_get_args()));
  });
  $twig->addFunction($intersect);

  $fn_file_exists = new Twig_SimpleFunction('file_exists', function ($file) {
    return file_exists($file);
  });
  $twig->addFunction($fn_file_exists);

  $filter = new Twig_SimpleFilter('parseTags', function ($string)
    {
      return customTags::parseContent($string, $outHtml);
    });

  $twig->addFilter($filter);

  echo $twig->render('index.twig', array(
      'html'=>$outHtml
  ));
}
catch (Exception $e)
{
  header('Content-Type: text/html; charset=utf-8');
  if ($_SESSION['debug'])
  {
    var_dump($e);
  }
  echo tr::get('error_check_log');
  error_log($e->getMessage());
}
