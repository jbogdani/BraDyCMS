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

  if (defined('CREATE_SITE')) {
    header('location: ./admin');
  }

  Router::run();

} catch (Exception $e) {

  header('Content-Type: text/html; charset=utf-8');

  if ($_SESSION['debug']) {
    var_dump($e);
  }
  
  echo tr::get('error_check_log');
  error_log($e->getMessage());
}
