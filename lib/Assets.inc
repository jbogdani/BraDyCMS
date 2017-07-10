<?php
/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license      MIT, See LICENSE file
 * @since      October 16, 2015
 */

class Assets
{

  /**
   * Returns array with assets data
   * @return array Array with assets data
   */
  private static function allAssets()
  {

    $path = link::to_article('home') . '/';

    $assets = array(

      'frontend' => [
        'js' => [
          'local' => $path . 'sites/default/js/frontend.js?' . md5_file('sites/default/css/styles.css')
        ],
        'css' => [
          'local' => $path . 'sites/default/css/styles.css?' . md5_file('sites/default/css/styles.css')
        ]
      ],

      'jquery' => [
        'version' => '1.11.3',
        'js' => [
          'local' => $path . 'bower_components/jquery/dist/jquery.min.js',
          'cdn' => '//ajax.googleapis.com/ajax/libs/jquery/--version--/jquery.min.js'
        ]
      ],

      'bootstrap' => [
        'version' => '3.3.5',
        'js' => [
          'local' => $path . 'bower_components/bootstrap/dist/js/bootstrap.min.js',
          'cdn' => 'https://maxcdn.bootstrapcdn.com/bootstrap/--version--/js/bootstrap.min.js'
        ],
        'css' => [
          'local' => $path . 'bower_components/bootstrap/dist/css/bootstrap.min.css',
          'cdn' => 'https://maxcdn.bootstrapcdn.com/bootstrap/--version--/css/bootstrap.min.css'
        ]
      ],

      'fancybox' => [
        'version' => '2.1.5',
        'js' => [
          'local' => $path . 'bower_components/fancyBox/source/jquery.fancybox.pack.js',
          'cdn' => 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/--version--/jquery.fancybox.pack.js'
        ],
        'css' => [
          'local' => $path . 'bower_components/fancyBox/source/jquery.fancybox.css',
          'cdn' => 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/--version--/jquery.fancybox.min.css'
        ]
      ],

      'fancybox3' => [
        'version' => '3.1.20',
        'js' => [
          'local' => $path . 'bower_components/fancyBox3/dist/jquery.fancybox.min.js',
          'cdn' => 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.1.20/jquery.fancybox.min.js'
        ],
        'css' => [
          'local' => $path . 'bower_components/fancyBox3/dist/jquery.fancybox.min.css',
          'cdn' => 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.1.20/jquery.fancybox.min.css'
        ]
      ],

      'modernizr' => [
        'version' => '2.8.3',
        'js' => [
          'cdn' => 'https://cdnjs.cloudflare.com/ajax/libs/modernizr/--version--/modernizr.min.js'
        ]
      ],

      'font-awesome' => [
        'version' => '4.4.0',
        'css' => [
          'cdn' => 'https://maxcdn.bootstrapcdn.com/font-awesome/--version--/css/font-awesome.min.css',
          'local' => $path . 'bower_components/font-awesome/css/font-awesome.min.css'
        ]
      ]

    );
    /*
    'name' => [
      'version' => '',
      'js' => [
        'local' => '',
        'cdn' => ''
      ],
      'css' => [
        'local' => '',
        'cdn' => ''
      ]
    ]
     */

    return $assets;
  }

  /**
   * Returns array with names of available assets
   * @return array arry with available assets
   */
  public static function getNames()
  {
    return array_keys(self::allAssets($path));
  }

  /**
   * returns array with asset data
   * @param  string $name    Asset's name
   * @param  string|false $type    Asset's type, one of: js|css. If missing and asset has both types js will be used, otherwise available type will be returned
   * @param  string|false $source    One of: cdn|local. If missing and asset has both sources cdn will be used, otherwise available source will be returned
   * @param  string|false $version Asset's version. If missing defaultversion will be used
   * @return array|false        Asset's data array
   */
  public static function resolve($name, $type = false, $source = false, $version = false)
  {
    // Return false if type is defined but is not css or js
    if ($type && $type !== 'css' && $type !== 'js')
    {
      return false;
    }

    // Return false if dest is defined but is not cdn or local
    if ($source && $source !== 'local' && $source !== 'cdn')
    {
      return false;
    }

    $assets = self::allAssets();

    // Return false on no asset found
    if (!isset($assets[$name]))
    {
      return false;
    }

    $asset = $assets[$name];


    // Set type if not setted
    if (!$type && isset($asset['css']) && isset($asset['js']))
    {
      $type = 'js';
    }
    elseif (!$type && isset($asset['css']) && !isset($asset['js']))
    {
      $type = 'css';
    }
    elseif (!$type && !isset($asset['css']) && isset($asset['js']))
    {
      $type = 'js';
    }
    elseif (!isset($asset[$type]))
    {
      return false;
    }

    // Set $source
    // 1. No custom source supplied: use default value: CDN, if available
    if (!$source && isset($asset[$type]['cdn']))
    {
      $source = 'cdn';
    }
    // 2. No custom source supplied, no available CDN value: use LOCAL if available
    elseif (!$source && isset($asset[$type]['local']))
    {
      $source = 'local';
    }
    // 3. Source supplied and is one of cdn|local.
    // If custom value is not available check for alternative value or return false
    elseif ($source && !isset($asset[$type][$source]))
    {
      if (isset($asset[$type]['cdn']))
      {
        $source = 'cdn';
      }
      elseif (isset($asset[$type]['local']))
      {
        $source = 'local';
      }
      else
      {
        return false;
      }
    }

    if ($version)
    {
      return array(
        'type' =>$type,
        'source' => $ource,
        'path' => str_replace('--version--', $version, $asset[$type][$source])
      );
    }
    elseif (isset($asset['version']))
    {
      return array(
        'type' =>$type,
        'source' => $ource,
        'path' => str_replace('--version--', $asset['version'], $asset[$type][$source])
      );
    }
    else
    {
      return array(
        'type' =>$type,
        'source' => $ource,
        'path' => $asset[$type][$source]
      );
    }
  }

}
