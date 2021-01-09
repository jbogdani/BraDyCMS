<?php
/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Dec 1, 2012
 */
 ob_start();

try {

  require_once 'lib/Bootstrap.php';

  // with PHP higher or equal 7 run Phpfastcache
  if (version_compare(phpversion(), '7.0.0') > -1){
    require_once 'lib/vendor/phpfastcache/lib/Phpfastcache/Autoload/Autoload.php';

    \Phpfastcache\CacheManager::setDefaultConfig(new \Phpfastcache\Config\Config([
      "path" => sys_get_temp_dir(),
      "itemDetailedDate" => false
    ]));

    $InstanceCache = \Phpfastcache\CacheManager::getInstance('files');
    $key = md5($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$_SERVER['QUERY_STRING']);
    $CachedString = $InstanceCache->getItem($key);
    
    if ( $_SESSION['debug']
      || preg_match('/\.map/', $_SERVER['REQUEST_URI'])
      || preg_match('/\.json/', $_SERVER['REQUEST_URI'])
      || preg_match('/controller\.php/', $_SERVER['REQUEST_URI'])
      || $_SERVER['REQUEST_URI'] === '/admin'
      || preg_match('/\.draft$/', $_SERVER['REQUEST_URI'])
      || is_null($CachedString->get())
    ) {

      Router::run();

      $html = ob_get_contents();
      ob_end_clean();

      $CachedString->set($html)->expiresAfter(60);
      $InstanceCache->save($CachedString);

    } else {

      $html = $CachedString->get();
    }

    echo $html;

  } else {
    Router::run();

  }



} catch (Throwable $e) {

  error_log($e);

  header('Content-Type: text/html; charset=utf-8');

  if ($_SESSION['debug'] || defined('CREATE_SITE')) {
    // var_dump($e->getTraceAsString());
    echo '<h2>' . $e->getMessage() . '</h2>'
    . "<p>File: " . $e->getFile()
    . "<br>Line: " . $e->getLine()
    . "<br>Code: " . $e->getCode()
    . "</p><hr>"
    .'<pre>' 
    . $e->getTraceAsString()
    . '</pre>';
  } else {
    echo tr::get('error_check_log');
  }

}
