<?php
/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license      MIT, See LICENSE file
 * @since      May 30, 2013
 *
 * Freely adapted from: http://core.trac.wordpress.org/browser/tags/3.5.1/wp-includes/shortcodes.php#L0
 */

class customTagParser
{
  private static $pending = array();

  public static function do_shortcode($content)
  {
    $pattern = self::get_shortcode_regex();

    $html = preg_replace_callback( "/$pattern/s", array(__CLASS__, 'do_shortcode_tag'), $content );

    if (!empty(self::$pending))
    {
      foreach (self::$pending as $obj)
      {
        if (method_exists($obj, 'end'))
        {
          $html .= call_user_func(array($obj, 'end'));
        }
      }
    }
    return $html;
  }

  private static function get_shortcode_regex()
  {
    // WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag()
    return
    '\\['                              // Opening bracket
    . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
    . "(\w+)"                            // 2: Shortcode name
    . '(?![\\w-])'                       // Not followed by word character or hyphen
    . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
    .     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
    .     '(?:'
    .         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
      .         '[^\\]\\/]*'               // Not a closing bracket or forward slash
      .     ')*?'
    . ')'
        . '(?:'
    .     '(\\/)'                        // 4: Self closing tag ...
      .     '\\]'                          // ... and closing bracket
      . '|'
    .     '\\]'                          // Closing bracket
      .     '(?:'
    .         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
      .             '[^\\[]*+'             // Not an opening bracket
      .             '(?:'
    .                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
      .                 '[^\\[]*+'         // Not an opening bracket
      .             ')*+'
    .         ')'
    .         '\\[\\/\\2\\]'             // Closing shortcode tag
      .     ')?'
    . ')'
    . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
  }



  private static function do_shortcode_tag( $m )
  {
    $html = '';
    // allow [[foo]] syntax for escaping a tag
    if ( $m[1] == '[' && $m[6] == ']' )
    {
      $html .= ' stop ';
      return substr($m[0], 1, -1);
    }

    $html .= ' continue ';
    $tag = $m[2];
    $attr = self::shortcode_parse_atts( $m[3] );

    //TODO CAMBIARE QUI I METODI DA CHIAMARE
    if ( isset( $m[5] ) )
    {
      $attr['content'] = $m[5];
    }

    $attr['tag'] = $tag;

    $html .= $m[1] . call_user_func( array(__CLASS__, checkAndRunMethod), $tag, $attr) . $m[6];

    return $html;
  }

  private static function checkAndRunMethod($tag, $formatedAttributes = false)
  {
    if (method_exists('customTags', $tag))
    {
      return call_user_func(array('customTags', $tag), $formatedAttributes);

    }
    else
    {
      if (method_exists($tag, 'init'))
      {
        self::$pending[] = $tag;

        return call_user_func(array($tag, 'init'), $formatedAttributes);
      }
    }
  }

  private static function shortcode_parse_atts($text)
  {
    $atts = array();
    $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
    $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
    if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) )
    {
      foreach ($match as $m)
      {
        if (!empty($m[1]))
          $atts[strtolower($m[1])] = stripcslashes($m[2]);
        elseif (!empty($m[3]))
        $atts[strtolower($m[3])] = stripcslashes($m[4]);
        elseif (!empty($m[5]))
        $atts[strtolower($m[5])] = stripcslashes($m[6]);
        elseif (isset($m[7]) and strlen($m[7]))
        $atts[] = stripcslashes($m[7]);
        elseif (isset($m[8]))
        $atts[] = stripcslashes($m[8]);
      }
    } else {
      $atts = ltrim($text);
    }
    return $atts;
  }

}
