<?php
/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Jan 10, 2012
 */

class Autoloader
{
    public static function start()
    {
        spl_autoload_register([__CLASS__, 'loader']);
    }

    private static function loader($className)
    {
        if (class_exists($className)) {
            return true;
        }

        // Manually installed external libraries
        switch ($className) {
          case 'Less_Parser':
            require_once LIB_DIR . 'vendor/lessphp/Less.php';
            return true;
            break;

          case 'R':
            require_once LIB_DIR . 'vendor/redbean/rb.php';
            return true;
            break;

          case 'PHPMailer':
            require_once LIB_DIR . 'vendor/phpmailer/class.phpmailer.php';
            require_once LIB_DIR . 'vendor/phpmailer/class.smtp.php';
            return true;
            break;

          case 'FeedWriter\Atom':
            require_once LIB_DIR . 'vendor/FeedWriter/Feed.php';
            require_once LIB_DIR . 'vendor/FeedWriter/Item.php';
            require_once LIB_DIR . 'vendor/FeedWriter/ATOM.php';
            return true;
            break;

          case 'FeedWriter\RSS2':
            require_once LIB_DIR . 'vendor/FeedWriter/Feed.php';
            require_once LIB_DIR . 'vendor/FeedWriter/Item.php';
            require_once LIB_DIR . 'vendor/FeedWriter/RSS2.php';
            return true;
            break;

          case 'Parsedown':
            require_once LIB_DIR . 'vendor/Parsedown/Parsedown.php';
            break;
        }

        if (
          strpos($className, "Curl") !== false
          ||
          strpos($className, "Intervention\\Image\\") !== false
          ||
          strpos($className, "Twig") !== false
          ) {
            $f = LIB_DIR . 'vendor/' . str_replace("\\", "/", $className) . '.php';
            if (file_exists($f)) {
                require_once $f;
                return true;
            }
        }

        if (preg_match('/_ctrl/', $className)) {
            $mod = str_replace('_ctrl', null, $className);
            if (file_exists(MOD_DIR . $mod . '/' . $mod . '.php')) {
                require_once MOD_DIR . $mod . '/' . $mod . '.php';
                return true;
            } else {
                return false;
            }
        }

        if (file_exists('lib/' . $className . '.php')) {
            require_once 'lib/' . $className . '.php';
            return true;
        } elseif (file_exists('lib/' . $className . '.inc')) {
            require_once 'lib/' . $className . '.inc';
            return true;
        } elseif (file_exists('lib/vendor/' . $className . '/' . $className . '.php')) {
            require_once 'lib/vendor/' . $className . '/' . $className . '.php';
            return true;
        } elseif (file_exists('./sites/default/modules/' . $className . '/' . $className . '.php')) {
            require_once './sites/default/modules/' . $className . '/' . $className . '.php';
            return true;
        } else {
            return false;
        }
    }
}
