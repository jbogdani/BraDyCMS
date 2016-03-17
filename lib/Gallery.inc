<?php

/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net, Julian Bogdani <jbogdani@gmail.com>
 * @license      See file LICENSE distributed with this code
 * @since        Jun 23, 2014
 * @uses        utils
 */

class Gallery{

  private static $path = GALLERY_DIR;

  // DELETED
  // TODO: search & replace for Gallery::getGallery instances
  // public static function getGallery($gal)

  /**
   * Return array will all available galleries (folders)
   * @return array Array of galleries
   */
  public static function getAll()
  {
    $all_galls = (array)utils::dirContent(self::$path);

    asort($all_galls);

    return $all_galls;
  }


  public static function edit($gal, $post, $lang = false)
  {
    // TODO
    //
    //
    // Data Post
    // [
    // image1name=>[caption=>"", sorting => ""],
    // image2name => [...],
    // ...
    // ]
    //
    // data.json:
    // [
    //  'filename1' => [sort => 'sort', caption => [ it=> 'it caption', en=> 'en caption', ...]],
    //  [...],
    //  ...
    // ]
    //
    // }

    $lang = self::getLang($lang);

    // Get Gallery data
    try
    {
      $metadata = self::getGallery2($gal, $lang, true);
    }
    catch (Exception $e)
    {
      $metadata = array();
    }

    $final_data = array();

    foreach ($post as $el)
    {
      $tmp = array();

    }

  }

  /**
   * Gets a language or throw an Exception. Argument, current langue or system langue will be used.
   * @param  string $lang Two-digits language code
   * @return string       Two-digits language code
   */
  public function getLang($lang = false)
  {
    if (!$lang)
    {
      $lang = utils::getCurrLang();
    }

    if (!$lang)
    {
      $lang = cfg::get('sys_lang');
    }

    if (!$lang)
    {
      throw new Exception("Can not determine current/system language");
    }
    return $lang;
  }


  /**
   * Returns all content of gallery folder, even if no metadata file is available
   * or metadata file is not well formatted. Used onlu in admin cp
   * @param  string $gal       Gallery id (folder name)
   * @param  string $lang      Two-digits language code, if false, current or system language will be used
   * @return array       Array of detailed data for each image:
   *                           name: filename, with extension
   *                           caption: caption in $lang, if available
   *                           orig_caption: if $lang is not system language, the orig_caption will receive the system language caption
   *                           fullpath: full path to original image
   *                           thumb: full path to thumbnail, if available
   *                           finfo: array with main image data (getimagesize function)
   */
  public static function getAllContent($gal, $lang = false)
  {
    $lang = self::getLang($lang);

    try
    {
      $metadata = self::get($gal, $lang, true);
    }
    catch (Exception $e)
    {
      $metadata = array();
    }

    $files = utils::dirContent(self::$path . $gal);

    if (!$files || !is_array($files))
    {
      return array();
    }
    $ret = array();

    foreach ($files as $file)
    {
      if ($file != 'thumbs' && !preg_match('/\.json/', $file))
      {
        array_push($ret, array(
          'name' => $file,
          'caption' => $metadata[$file]['caption'][$lang],
          'sort' => $metadata[$file]['sort'],
          'orig_caption' =>($lang != cfg::get('sys_lang') ? $metadata[$file]['caption'][cfg::get('sys_lang')] : ''),
          'fullpath' => self::$path . $gal . '/' . $file,
          'thumb' => (file_exists(self::$path . $gal . '/thumbs/' . $file) ? self::$path . $gal . '/thumbs/' . $file : ''),
          'finfo' => getimagesize(self::$path . $gal . '/' . $file)
        ));
      }
    }

    uasort($ret, function($a, $b){
      if ($a['sort'] && $b['sort'])
      {
        if ($a['sort'] == $b['sort'])
        {
          return 0;
        }
        return ($a['sort'] > $b['sort']) ? -1 : 1;
      }
      else
      {
        if ($a['name'] == $b['name'])
        {
          return 0;
        }
        return ($a['name'] > $b['name']) ? -1 : 1;
      }
    });

    return $ret;
  }

  /**
   * Returns parsed array of gallery data or throws Exceptionon error
   * @param  string $gal       Gallery id (folder name)
   * @param  string $lang      Two-digits language code, if false, current or system language will be used
   * @param  boolean $dontparse If true raw gallery array data will be returned, if false (default) parsed result will be returned
   * @return array            Array of array for galley items. For each element will be returned, if $dontparse is false:
   *                                img: full path to main image
   *                                thumb: full path to thumbnail image
   *                                caption: translated (custom, current or system language) caption
   *                          of, if $dontparse is true:
   *                          			img: main image file name
   *                          			caption: array of different captions in different languages
   *                          				{lang_id}: caption translated in lang_id
   */
  public static function get($gal, $lang = false, $dontparse = false)
  {
    $lang = self::getLang($lang);

    // Check for metadata file
    $file = self::$path . '' . $gal . '/metadata.json';

    if (!file_exists($file))
    {
      //  check for old-style gallery definition and convert it to new style
      throw new Exception('No metadata file found for gallery ' . $gal);
    }

    // Parse metadata file
    $metadata = json_decode(file_get_contents($file), 1);

    if (!$metadata || !is_array($metadata))
    {
      throw new Exception("Gallery metadata file for gallery " . $gal . "is nor well formatted");
    }

    // Sort by 1. filename
    ksort($metadata);

    // Sort by 2. metadata
    uasort($metadata, function($a, $b){
      if ($a['sort'] && $b['sort'])
      {
        if ($a['sort'] == $b['sort'])
        {
          return 0;
        }
        return ($a['sort'] > $b['sort']) ? -1 : 1;
      }
    });

    if ($dontparse)
    {
      return $metadata;
    }

    $data = array();
    foreach ($metadata as $id => $el)
    {
      if (!file_exists('./sites/default/images/galleries/' .$gal . '/' . $id))
      {
        // Metadata entries without original images are consiered to be  orphans
        // and will be ignored
        continue;
      }
      $img = utils::getBaseUrl() . 'sites/default/images/galleries/' .$gal . '/' . $id;

      if (file_exists('./sites/default/images/galleries/' .$gal . '/thumbs/' . $id))
      {
        $thumb = utils::getBaseUrl() . 'sites/default/images/galleries/' .$gal . '/thumbs/' . $id;
      }

      $caption = $el['caption'][$lang];


      array_push($data, array(
        'img' => $img,
        'thumb' => $thumb,
        'caption' => $caption
      ));
    }
    return $data;
  }

}
?>
