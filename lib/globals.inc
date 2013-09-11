<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			05/mar/2011
 */

session_start();

if ($_GET['lang'])
{
	$_SESSION['lang'] = $_GET['lang'];
}


if (@!$root)
{
	$root = './';
}
define('MAIN_DIR', $root);

ini_set('error_log', './logs/error.log');

ini_set('display_errors', 'off');

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
if (cfg::get('timezone'))
{
	date_default_timezone_set(cfg::get('timezone'));
}
else
{
	date_default_timezone_set('Europe/Rome');
}

define ( 'PREFIX',	cfg::get('prefix') );
define ( 'SYS_LANG',cfg::get('default_lang') );
define ( 'MOD_DIR', MAIN_DIR . 'modules/');
define ( 'LIB_DIR', MAIN_DIR . 'lib/');
define ( 'ERR_LOG',	MAIN_DIR . 'logs/error.log');
define ( 'TMP_DIR', MAIN_DIR . 'tmp/');
define ( 'LOCALE_DIR', MAIN_DIR . 'locale/');
define ( 'SITE_DIR', MAIN_DIR . 'sites/default/');
define ( 'IMG_DIR', SITE_DIR . 'images/');

define ( 'GALLERY_DIR', IMG_DIR . 'galleries/' );
define ( 'CACHE_DIR', MAIN_DIR . 'cache/');

$_SESSION['debug'] = cfg::get('debug');

if ($_SESSION['debug'])
{
	define('CACHE', serialize(array('debug' => true)));
}
else
{
	define('CACHE', serialize(array('cache' => CACHE_DIR)));
}
tr::load_file($_GET['lang']);

R::setup(cfg::get('db_connection') ? cfg::get('db_connection') : 'sqlite:./sites/default/cfg/database.sqlite');
R::freeze( true );
R::dependencies(array('arttrans'=>array('articles')));
R::dependencies(array('menutrans'=>array('menu')));
