<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			05/mar/2011
 */
if (!$root)
{
	$root = './';
}
ini_set('error_log', './error.log');

ini_set('display_errors', 'off');

if (get_magic_quotes_gpc()) {

	$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);

	while (list($key, $val) = each($process)) {

		foreach ($val as $k => $v) {
				
			unset($process[$key][$k]);
				
			if (is_array($v)) {

				$process[$key][stripslashes($k)] = $v;

				$process[] = &$process[$key][stripslashes($k)];
					
			} else {

				$process[$key][stripslashes($k)] = stripslashes($v);
			}
		}
	}
	unset($process);
}

$cfg = parse_ini_file($root . 'sites/default/cfg/config.ini', 1);


//set default date-zone
if ($cfg['timezone'])
{
	date_default_timezone_set($cfg['timezone']);
}
else
{
	date_default_timezone_set('Europe/Rome');
}

define ( 'PREFIX',		$cfg['prefix'] );
define ( 'SYS_LANG',	$cfg['default_lang'] );
define ( 'ROOT', 		$root);
define ( 'ERR_LOG', 	$root . 'error.log');
define ( 'TMP_DIR', 	$root . 'tmp/');


require_once $root . 'lib/class.MyExc.inc';
require_once $root . 'lib/ClassAutoloader.inc';

new ClassAutoloader();

$lang_file = utils::getLang();

if ($lang_file AND file_exists('langs/' . $lang_file . '.php'))
	require_once 'langs/' . $lang_file . '.php';

