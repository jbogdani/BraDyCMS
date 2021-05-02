<?php
/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Dec 22, 2012
 */

class tr
{
    /**
     *
     * Returns lang variable using:
     * 1) $_SESSION['pref']['lang']
     * 2) system default language
     * 3) browser default language
     * 4) en
     */
    public static function load_file($get_lang = false, $silencemode = false)
    {
        require_once './locale/en.php';

        if (file_exists(SITE_DIR . 'locale/en.php')) {
            require_once SITE_DIR . 'locale/en.php';
        }

        $en = $_lang;

        // If silence mode is on (no site is defined yet) english is the only available language!
        if ($silencemode) {
            // do nothing
        } elseif ($get_lang && $get_lang == cfg::get('sys_lang')) {
            $_SESSION['adm_lang'] = $get_lang;
        } elseif ($get_lang && cfg::get('languages')) {
            foreach (cfg::get('languages') as $ll) {
                if ($ll['id'] == $get_lang) {
                    $_SESSION['adm_lang'] = $get_lang;
                }
            }
        }

        /**
         * Load Admin language
         */
        if (!$_SESSION['adm_lang']) {
            // If silence mode is on (no site is defined yet) english is the only available language!
            $_SESSION['adm_lang'] = $silencemode ? 'en' : cfg::get('sys_lang');
        }

        if (file_exists('locale/' . $_SESSION['adm_lang'] . '.php')) {
            require_once 'locale/' . $_SESSION['adm_lang'] . '.php';
        }

        if (file_exists(SITE_DIR . 'locale/' . $_SESSION['adm_lang'] . '.php')) {
            require_once SITE_DIR . 'locale/' . $_SESSION['adm_lang'] . '.php';
        }

        $_SESSION['language_strings'] = array_merge($en, $_lang);
    }

    /**
     *
     * Returns formatted string
     * @param string $string  input string
     * @param array $args    array of arguments for formatting
     * @param boolean $escape  boolean escape
     */
    public static function sget(string $string, array $args, bool $escape = false)
    {
        if (!is_array($args)) {
            $args = array($args);
        }
        $ret = vsprintf(self::get($string), $args);

        return $escape ? str_replace("'", "\'", $ret) : $ret;
    }

    /**
     *
     * Translates $string
     * @param string $string string to translate
     * @param boolean $escape boolean controle if string mus be escaped or not
     */
    public static function get($string, $escape = false)
    {
        $lang = $_SESSION['language_strings'];

        if ($lang[$string]) {
            $ret = $lang[$string];
        } else {
            error_log('Missing translation for {' . $string . '}');
            $ret = $string;
        }

        return $escape ? str_replace("'", "\'", $ret) : $ret;
    }

    /**
     *
     * Returns current language as js 
     * @param boolean|array $post post data
     * @param boolean|array $get get data
     */
    public static function lang2js($post = false, $get = false)
    {
        $lang = $_SESSION['language_strings'];

        header('Content-Type: application/javascript');
        echo 'var lang = ' . json_encode($lang) . ';';
    }

    /**
     *
     * Uses self::get and echoes translated string
     * @param string $string
     * @param boolean $escape boolean controle if string mus be escaped or not
     */
    public static function show($string, $escape = false)
    {
        echo self::get($string, $escape);
    }
}
