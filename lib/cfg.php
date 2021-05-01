<?php
/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Dec 1, 2012
 */

class cfg
{
    private static $data = array();

    public static function get($el = false)
    {
        if (!self::$data) {
            if (!is_dir(MAIN_DIR . 'sites/default')) {
                throw new Exception('missing_site_dir');
            }

            if (!file_exists(MAIN_DIR . 'sites/default/cfg/config.json')) {
                throw new Exception('Missing configuration file for site');
            }

            self::$data = json_decode(file_get_contents(MAIN_DIR . 'sites/default/cfg/config.json'), true);

            if (!self::$data) {
                throw new Exception('Error in reading config file! Maybe your JSON is is using invalid syntax');
            }
        }

        if ($el) {
            return self::$data[$el];
        } else {
            return self::$data;
        }
    }

    public static function save($data)
    {
        // sys lang string always lower-case
        if ($data['sys_lang_string']) {
            $data['sys_lang_string'] = strtolower($data['sys_lang_string']);
        }

        // sys lang should be no longer then 2 characters
        if ($data['sys_lang'] && strlen($data['sys_lang']) > 2) {
            $data['sys_lang'] = strtolower(substr($data['sys_lang'], 0, 2));
        }

        // custom field names always lowercase and anly word characters are allowed
        if (is_array($data['custom_fields'])) {
            foreach ($data['custom_fields'] as &$array) {
                $array['name'] = strtolower(preg_replace('/\W/', '', $array['name']));
            }
        }

        self::$data = $data;
        return utils::write_in_file(MAIN_DIR . 'sites/default/cfg/config.json', self::$data, 'json');
    }
}
