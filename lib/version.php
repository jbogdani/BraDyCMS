<?php
/**
 * 
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Dec 11, 2012
 */

class version
{
    private static function parse()
    {
        $va = parse_ini_file('version', 1);
        if (!$va) {
            throw new Exception('File `version` can not be parsed ' . __FILE__ . ', ' . __LINE__);
        } else {
            return $va;
        }
    }

    public static function changelog()
    {
        return self::parse();
    }

    public static function current()
    {
        $keys = array_keys(self::parse());
        return $keys[0];
    }
}
