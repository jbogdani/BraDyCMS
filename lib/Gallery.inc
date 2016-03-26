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

  public static function deleteItem($gal, $file)
  {
    // TODO
  }

  public static function deleteGallery($gal)
  {
    // TODO
  }
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

  /**
   * Saves aray of data to metadata.json file. Keys are escaped
   * @param  string $gal  gallery name
   * @param  array $post array of data to save. Dots in array keys are replaced with double hyphren
   * @return true       True on success, throws Exception on error
   */
  public static function save($gal, $post)
  {
    $newpost = array();
    foreach ($post as $key => &$value)
    {
      $newpost[str_replace('~~', '.', $key)] = $post[$key];
    }
    $file = self::$path . '' . $gal . '/metadata.json';

    if (!utils::write_in_file($file, $newpost, 'json'))
    {
      throw new Exception("Error writing in file " . $file);
    }
    return true;
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

    // Get metadata, if available
    try
    {
      $metadata = self::get($gal, $lang, true);
    }
    catch (Exception $e)
    {
      $metadata = array();
    }

    // Get all files
    $files = utils::dirContent(self::$path . $gal);

    if (!$files || !is_array($files))
    {
      return array();
    }
    $ret = array();

    // Add ampty arrays for files missin in metadata array
    foreach ($files as $file)
    {
      if (!in_array($file, array_keys($metadata)) && $file != 'thumbs' && !preg_match('/\.json/', $file))
      {
        $metadata[$file] = array();
      }
    }

    foreach($metadata as $file=>$data)
    {
      array_push($ret, array(
        'name' => $file,
        'safe_name' => str_replace('.', '~~', $file),
        'caption' => $data['caption'],
        'sort' => $data['sort'],
        'fullpath' => self::$path . $gal . '/' . $file,
        'thumb' => (file_exists(self::$path . $gal . '/thumbs/' . $file) ? self::$path . $gal . '/thumbs/' . $file : ''),
        'finfo' => getimagesize(self::$path . $gal . '/' . $file)
      ));
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
   * Backwards compatibility alias for get
   * @param  string $gal       Gallery id (folder name)
   * @return array            Array of array for galley items:
   */
  public static function getGallery($gal)
  {
    return self::get($gal);
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
