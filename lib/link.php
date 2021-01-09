<?php
/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      May 20, 2013
 */

class link
{
    /**
     * Returns path to main domain root
     * @return string
     */
    private static function getPath()
    {
        $url_slashes = substr_count($_SERVER['REQUEST_URI'], '/');
        $base_slashes = substr_count(utils::getbase(), '/');

        return './' . @str_repeat('../', $url_slashes-$base_slashes);
    }

    /**
     * Returns URL safe string
     * @param string $href url to link 2
     * @return string
     */
    private static function formatHref($href)
    {
        return str_replace(' ', '+', $href);
    }

    /**
     * Returns well-formatted URL string
     * @param string $string Searched string or stringified list of tags
     * @param string $lang  Two-digits language code
     * @param int $page Page number
     * @return string
     */
    public static function to_search($string, $lang = false, $page = false)
    {
        $string = 'search:' . $string;

        return self::format($string, $lang, $page);
    }

    /**
     * Return well formatted link string
     * @param string $textid Article's textid to link to
     * @param string $lang two-digits languagae code, default false
     * @param int|false $page Page number, default false.
     * @param array  $parts Array with url parts
     * @return string
     */
    public static function to_article($textid, $lang = false, $page = false, $parts = false)
    {
        return self::format($textid, $lang, $page, $parts);
    }

    /**
     * Return well formatted link string
     * @param string|array $tags string of single tag or array of tags to link to
     * @param string $lang two-digits languagae code, default false
     * @param int|false $page Page number, default false.
     * @return string
     */
    public static function to_tags($tags, $lang = false, $page = false)
    {
        if (!is_array($tags)) {
            $tags = [ $tags ];
        }

        return self::format(implode('/', $tags) . '/', $lang, $page);
    }

    /**
     * Returns formatted href
     * @param string $href unformatted href
     * @param string $lang two-digits language code
     * @param int $page Page number
     * @param array|false  $parts Array with url parts
     * @return string
     */
    public static function format($href, $lang = false, $page = false, $parts = false)
    {
        $href = self::formatHref($href);

        // Absolute URL is not processed
        if (preg_match('/^https?:\/\/(.+)/', $href)) {
            return $href;
        }

        // Anchor is not processed
        if (preg_match('/^#(.+)/', $href)) {
            return $href;
        }

        // Incomplete absolute URL is completed with http:// prefix and not processed further
        if (preg_match('/^www\.(.+)/', $href)) {
            return 'http://' . $href;
        }

        // Ancor inside relatve URL is being added after URL processing
        list($href, $pending) = explode('#', $href);

        // skype  & callto links
        if (preg_match('/^skype(.+)/', $href) || preg_match('/^callto(.+)/', $href)) {
            return $href;
        }

        $ret_parts = [];

        // Add base
        array_push($ret_parts, self::getPath());

        // add language at the begining of href: if href is not provided then a link to the base will be printed, with no language information
        if ($lang && $href && $href !== '') {
            array_unshift($ret_parts, $lang);
        }

        if ($parts) {
            $ret_parts = array_merge($ret_parts, $parts);
        }

        // links to home page is not processed
        if ($href === '' || $href === './' || $href === 'home') {
            $href = false;
        }
        array_push($ret_parts, $href);

        // add page to href
        if ($page && $href) {
            array_push($ret_parts, 'p' . $page);
        }

        return str_replace(['/./', '//'], '/', implode('/', $ret_parts)) .
          ($pending ? '#' . $pending : '');
    }
}
