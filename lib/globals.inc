<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			05/mar/2011
 */

session_start();

if (@!$root)
{
	$root = './';
}

define('MAIN_DIR', $root);


if (!is_dir(MAIN_DIR . 'sites/default'))
{
  define('CREATE_SITE', true);
}


if (!is_dir(MAIN_DIR . 'tmp'))
{
  @mkdir(MAIN_DIR . 'tmp', 0777, true);
}

if (!is_dir(MAIN_DIR . 'cache'))
{
  @mkdir(MAIN_DIR . 'cache', 0777, true);
}

if (!is_dir(MAIN_DIR . 'logs'))
{
  @mkdir(MAIN_DIR . 'logs', 0777, true);
}

if (!file_exists(MAIN_DIR . 'logs/error.log'))
{
  touch(MAIN_DIR . 'logs/error.log');
}

// SETS ERROR REPORTING
( ini_get('error_reporting') <> 6135 )	?	ini_set('error_reporting', 6135) : '';

ini_set('error_log', './logs/error.log');

ini_set('display_errors', 'off');


if (@$_GET['lang'])
{
	$_SESSION['lang'] = $_GET['lang'];
}

if (get_magic_quotes_gpc())
{
	$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);

	while (list($key, $val) = each($process))
	{
		foreach ($val as $k => $v)
		{
			unset($process[$key][$k]);
				
			if (is_array($v))
			{
				$process[$key][stripslashes($k)] = $v;

				$process[] = &$process[$key][stripslashes($k)];
			}
			else
			{
				$process[$key][stripslashes($k)] = stripslashes($v);
			}
		}
	}
	unset($process);
}

require_once $root . 'lib/Autoloader.inc';
Autoloader::start();

require $root . 'vendor/autoload.php';



class_alias('RedBean_Facade', 'R');

//set default date-zone
if (defined('CREATE_SITE') || !cfg::get('timezone'))
{
  
  date_default_timezone_set('Europe/Rome');
}
else
{
	date_default_timezone_set(cfg::get('timezone'));
}

!defined('CREATE_SITE') ? define ( 'PREFIX',	cfg::get('prefix') ) : '';
!defined('CREATE_SITE') ? define ( 'SYS_LANG',cfg::get('default_lang') ) : '';
define ( 'MOD_DIR', MAIN_DIR . 'modules/');
define ( 'LIB_DIR', MAIN_DIR . 'lib/');
define ( 'ERR_LOG',	MAIN_DIR . 'logs/error.log');
define ( 'TMP_DIR', MAIN_DIR . 'tmp/');
define ( 'LOCALE_DIR', MAIN_DIR . 'locale/');
define ( 'SITE_DIR', MAIN_DIR . 'sites/default/');
define ( 'IMG_DIR', SITE_DIR . 'images/');

define ( 'GALLERY_DIR', IMG_DIR . 'galleries/' );
define ( 'CACHE_DIR', MAIN_DIR . 'cache/');

!defined('CREATE_SITE') ? $_SESSION['debug'] = cfg::get('debug') : '';

if (defined('CREATE_SITE') || $_SESSION['debug'])
{
	define('CACHE', serialize(array('debug' => true)));
}
else
{
	define('CACHE', serialize(array('cache' => CACHE_DIR)));
}
tr::load_file(@$_GET['lang'], defined('CREATE_SITE'));

if (defined('CREATE_SITE') || !cfg::get('db_connection'))
{
  R::setup('sqlite:./sites/default/cfg/database.sqlite');
}
else if (cfg::get('db_connection') && cfg::get('db_user') && cfg::get('db_pass'))
{
  R::setup(cfg::get('db_connection'), cfg::get('db_user'), cfg::get('db_pass'));
}

R::setup( ( defined('CREATE_SITE')|| !cfg::get('db_connection')) ? 'sqlite:./sites/default/cfg/database.sqlite' : cfg::get('db_connection') );
R::freeze( true );
R::dependencies(array('arttrans'=>array('articles')));
R::dependencies(array('menutrans'=>array('menu')));

