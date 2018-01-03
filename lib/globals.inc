<?php
/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license      MIT, See LICENSE file
 * @since      05/mar/2011
 */

// start session
session_start();

// set $root variable
if (@!$root) {
    $root = './';
}

// Define MAIN_DIR constant, to be used in all system paths
define('MAIN_DIR', $root);

// if MAIN_DIR directory does not exist, then this is a new site installation process
if (!is_dir(MAIN_DIR . 'sites/default')) {
    define('CREATE_SITE', true);
}

// Create main system directories, if they do not exist
foreach (['tmp', 'cache', 'logs'] as $d) {
    if (!is_dir(MAIN_DIR . $d)) {
        @mkdir(MAIN_DIR . $d, 0777, true);
    }
}

// Create main system log files if they do not exist
foreach (['logs/error.log', 'logs/users.log', 'logs/logAttempts.log'] as $f) {
    if (!file_exists(MAIN_DIR . $f)) {
        @touch(MAIN_DIR . $f);
    }
}

// SETS ERROR REPORTING
$err_code = 6135; // E_ALL & ~E_NOTICE, i.e.: 6143 (E_ALL) - 8 (E_NOTICE)

(ini_get('error_reporting') <> $err_code)  ?  ini_set('error_reporting', $err_code) : '';

ini_set('error_log', './logs/error.log');

ini_set('display_errors', 'off');

// Set Session language
if (@$_GET['lang']) {
    $_SESSION['lang'] = $_GET['lang'];
}

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

// Include and start main Autoloader
require_once $root . 'lib/Autoloader.inc';
Autoloader::start();

// Include and start Twig
require_once $root . 'lib/vendor/Twig/Autoloader.php';
Twig_Autoloader::register();

//set default date-zone
if (defined('CREATE_SITE') || !cfg::get('timezone')) {
    date_default_timezone_set('Europe/Rome');
} else {
    date_default_timezone_set(cfg::get('timezone'));
}

!defined('CREATE_SITE') ? define('PREFIX', cfg::get('prefix')) : '';
!defined('CREATE_SITE') ? define('SYS_LANG', cfg::get('default_lang')) : '';
define('MOD_DIR', MAIN_DIR . 'modules/');
define('LIB_DIR', MAIN_DIR . 'lib/');
define('ERR_LOG', MAIN_DIR . 'logs/error.log');
define('TMP_DIR', MAIN_DIR . 'tmp/');
define('LOCALE_DIR', MAIN_DIR . 'locale/');
define('SITE_DIR', MAIN_DIR . 'sites/default/');
define('IMG_DIR', SITE_DIR . 'images/');

define('GALLERY_DIR', IMG_DIR . 'galleries/');
define('DOWNLOADS_DIR', IMG_DIR . 'downloads/');
define('CACHE_DIR', MAIN_DIR . 'cache/');

!defined('CREATE_SITE') ? $_SESSION['debug'] = cfg::get('debug') : '';

if (defined('CREATE_SITE') || $_SESSION['debug']) {
    define('CACHE', serialize(array('debug' => true)));
} else {
    define('CACHE', serialize(array('cache' => CACHE_DIR)));
}

tr::load_file(@$_GET['lang'], defined('CREATE_SITE'));

if (defined('CREATE_SITE') || !cfg::get('db_connection')) {
    R::setup('sqlite:./sites/default/cfg/database.sqlite');
} elseif (cfg::get('db_connection')) {
    R::setup(cfg::get('db_connection'), cfg::get('db_user'), cfg::get('db_pass'));
}

if (defined('CREATE_SITE') || (!defined('CREATE_SITE') && !cfg::get('debug'))) {
    R::freeze(true);
}


$writeables = array(
  MAIN_DIR . 'logs',
  ERR_LOG,
  MAIN_DIR . 'logs/users.log',
  MAIN_DIR . 'logs/logAttempts.log',
  TMP_DIR,
  IMG_DIR,
  LOCALE_DIR,
  CACHE_DIR
);

if (!defined('MAIN_DIR')) {
    foreach ($writeables as $f) {
        if (!is_writable($f)) {
            throw new Exception(tr::sget('permission_error', $f));
        }
    }
}


// Sandbox templates
if ($_GET['sandbox'] && file_exists('./sites/default/' . $_GET['sandbox'])) {
    $_SESSION['sandbox'] = $_GET['sandbox'];
}

if ($_GET['sandbox'] === 'stop') {
    unset($_SESSION['sandbox']);
}
