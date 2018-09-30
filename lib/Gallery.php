<?php

/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net, Julian Bogdani <jbogdani@gmail.com>
 * @license      See file LICENSE distributed with this code
 * @since        Jun 23, 2014
 * @uses        utils
 */

class Gallery
{
    private static $path = GALLERY_DIR;

    /**
     * Backward compatibility function
     * Gets old-style data.json file, for main language and translations
     * and converts everything in new metadata.json file
     * @param  string $gal gallery id
     * @return boolean      true on success
     */
    private static function convertMetadata($gal)
    {
        if (file_exists(self::$path . $gal . '/metadata.json')) {
            return true;
        }

        $delete_files = array();

        $data = self::$path . $gal . '/data.json';

        if (!file_exists($data)) {
            return true;
        }

        array_push($delete_files, $data);

        $data_arr = json_decode(file_get_contents($data), true);
        $tr = array();

        foreach ((array)cfg::get('languages') as $lng) {
            $lng_file = self::$path . $gal . '/data_' . $lng['id'] . '.json';
            if (file_exists($lng_file)) {
                array_push($tr, array($lng['id'], json_decode(file_get_contents($lng_file), true)));

                array_push($delete_files, $lng_file);
            }
        }

        $metadata = array();

        foreach ($data_arr as $f => $c) {
            $pf = str_replace('__x__', '.', $f);

            $caption[cfg::get('sys_lang')] = $c;

            foreach ($tr as $l => $a) {
                $caption[$l] = $a[$f];
            }
            $metadata[$pf] = array( 'caption' => $caption);
        }

        try {
            self::save($gal, $metadata);
            // var_dump($delete_files);
            return true;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }


    /**
     * Completely removes a gallery item, deleting metadata record and original file
     * 		Throws Exception on error. The following error codes will be thrown:
     * 				1: Can not delete original file
     * 				2: Can not update metadata file (original anf thumbnail file deleted)
     * @param  string $gal  gallery id
     * @param  string $file file name to delete
     * @return true       true on success
     */
    public static function deleteItem($gal, $file)
    {
        // 1. Delete original file
        $orig_file = self::$path . '' . $gal . '/' . $file;
        if (file_exists($orig_file)) {
            @unlink($orig_file);
            if (file_exists($orig_file)) {
                throw new Exception("Error deleting original file " . $file . ' in gallery ' . $gal, 1);
            }
        }

        // 2. update metadata file
        try {
            $metadata = self::get($gal, false, true);
        } catch (Exception $e) {
            $metadata = array();
        }

        try {
            foreach ($metadata as $f => &$data) {
                if ($file === $f) {
                    unset($metadata[$f]);
                }
            }
            self::save($gal, $metadata);
        } catch (Exception $e) {
            $error_metadata = true;
        }

        if ($error_metadata) {
            throw new Exception("Error updating metadata file for  " . $file . ' for gallery ' . $gal, 2);
        }

        return true;
    }

    /**
     * Creates a new gallery. Returns true on success, throws Exception on error, with the following codes
     * 		1: Gallery directory already exists
     * 		2: Can not create gallery directory
     * @param string $gal gallery id
     * @return boolean    true on success
     */
    public static function addNew($gal)
    {
        $gal = self::$path . $gal;

        if (is_dir($gal)) {
            throw new Exception("Gallery $gal already exists", 1);
        }
        @mkdir($gal, 0777, true);

        if (!is_dir($gal)) {
            throw new Exception("Error creating gallery $gal", 2);
        }
        return true;
    }

    /**
     * Renames gallery from $old to $new. Return true on success, throws Exception on error with the following codes:
     * 		1: Old gallery does not exists
     * 		2: New gallery already exists
     * 		3: Can nor rename
     * @param  string $old old gallery id (must exist)
     * @param  string $new new gallery id (must not exist)
     * @return boolean      true on success
     */
    public static function rename($old, $new)
    {
        if (!file_exists(self::$path . $old)) {
            throw new Exception('Gallery ' . $old . ' does not exist', 1);
        }

        if (file_exists(self::$path . $new)) {
            throw new Exception('Gallery ' . $new . ' already exists. Can not overwrite', 2);
        }

        @rename(self::$path . $old, self::$path . $new);

        if (file_exists(self::$path. $old) || !file_exists(self::$path . $new)) {
            throw new Exception('Can not rename gallery ' . $old . ' to ' . $new, 3);
        }
        return true;
    }

    /**
     * Completely removes a gallery directory
     * 	Throws Exception on error
     * @param  string $gal gallery id
     * @return true      true on success
     */
    public static function deleteGallery($gal)
    {
        $error = utils::recursive_delete($gal);

        if ($error) {
            throw new Exception(implode("\n", $error));
        }

        return true;
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
        foreach ($post as $key => &$value) {
            $newpost[str_replace('~~', '.', $key)] = $post[$key];
        }
        $file = self::$path . '' . $gal . '/metadata.json';

        if (!utils::write_in_file($file, $newpost, 'json')) {
            throw new Exception("Error writing in file " . $file);
        }
        return true;
    }

    /**
     * Gets a language or throw an Exception. Argument, current langue or system langue will be used.
     * @param  string $lang Two-digits language code
     * @return string       Two-digits language code
     */
    private static function getLang($lang = false)
    {
        if (!$lang) {
            $lang = utils::getCurrLang();
        }

        if (!$lang) {
            $lang = cfg::get('sys_lang');
        }

        if (!$lang) {
            throw new Exception("Can not determine current/system language");
        }
        return $lang;
    }


    /**
     * Returns all content of gallery folder, even if no metadata file is available
     * or metadata file is not well formatted. Used onlu in admin cp
     * @param  string $gal       Gallery id (folder name)
     * @return array       Array of detailed data for each image:
     *                           name: filename, with extension
     *                           caption: caption in $lang, if available
     *                           href: destination url of the link
     *                           orig_caption: if $lang is not system language, the orig_caption will receive the system language caption
     *                           fullpath: full path to original image
     *                           thumb: full path to thumbnail, if available
     *                           finfo: array with main image data (getimagesize function)
     */
    public static function getAllContent($gal)
    {
        self::convertMetadata($gal);

        // Get metadata, if available
        try {
            $metadata = self::get($gal, false, false, true);
        } catch (Exception $e) {
            $metadata = array();
        }

        // Get all files
        $files = utils::dirContent(self::$path . $gal);

        if (!$files || !is_array($files)) {
            return [];
        }
        $ret =[];

        // Add ampty arrays for files missing in metadata array
        foreach ($files as $file) {
            if (!in_array($file, array_keys($metadata)) && $file !== 'thumbs' && !preg_match('/\.json/', $file)) {
                $metadata[$file] = array();
            }
        }

        foreach ($metadata as $file=>$data) {
            // Remove from metadata array missing files
            if (!file_exists(self::$path . $gal . '/' . $file)) {
                continue;
            }
            array_push($ret, [
              'name' => $file,
              'safe_name' => str_replace('.', '~~', $file),
              'caption' => $data['caption'],
              'sort' => $data['sort'],
              'href' => $data['href'],
              'fullpath' => self::$path . $gal . '/' . $file,
              'finfo' => getimagesize(self::$path . $gal . '/' . $file)
            ]);
        }

        ksort($ret);

        uasort($ret, function ($a, $b) {
            if ($a['sort'] == $b['sort']) {
                return 0;
            }
            return ($a['sort'] < $b['sort']) ? -1 : 1;
        });

        return $ret;
    }

    /**
     * Returns parsed array of gallery data or throws Exceptionon error
     * @param  string $gal       Gallery id (folder name)
     * @param  string $thumb_dim Thumbnail dimensions, string: ^([0-9]{1,4})x([0-9]{1,4})$
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
    public static function get($gal, $thumb_dim = false, $lang = false, $dontparse = false)
    {
        self::convertMetadata($gal);

        $lang = self::getLang($lang);

        // Check for metadata file
        $file = self::$path . '' . $gal . '/metadata.json';

        if (!file_exists($file)) {
            //  check for old-style gallery definition and convert it to new style
            throw new Exception('No metadata file found for gallery ' . $gal);
        }

        if (!$thumb_dim || !preg_match('/^([0-9]{1,4})x([0-9]{1,4})$/', $thumb_dim)){
          $thumb_dim = '200x200';
        }

        // Parse metadata file
        $metadata = json_decode(file_get_contents($file), 1);

        if (!$metadata || !is_array($metadata)) {
            throw new Exception("Gallery metadata file for gallery " . $gal . "is not well formatted");
        }

        // Sort by 1. filename
        ksort($metadata);

        // Sort by 2. metadata
        uasort($metadata, function ($a, $b) {
            if ($a['sort'] == $b['sort']) {
                return 0;
            }
            return ($a['sort'] < $b['sort']) ? -1 : 1;
        });

        if ($dontparse) {
            return $metadata;
        }

        $data = array();
        foreach ($metadata as $id => $el) {
            $image_file = 'sites/default/images/galleries/' .$gal . '/' . $id;
            if (!file_exists($image_file)) {
                // Metadata entries without original images are considered to be  orphans
                // and will be ignored
                continue;
            }

            $md5 = md5_file($image_file);
            $cached_path = SITE_DIR . "cache/galleries/$thumb_dim/" . substr($md5, 0, 2) . '/' . substr($md5, 2, 2);
            // try to create $cahced_path if it does not exist!
            if (!is_dir($cached_path)){
              @mkdir($cached_path, 0777, true);
              if (!is_dir($cached_path)){
                throw new Exception("Can not create thumbnail cache directory: $cached_path");
              }
            }
            $cached_file = $cached_path . '/' . $md5 . '.jpg';
            if (!file_exists($cached_file)){
              $dims_arr = explode('x', $thumb_dim);
              imgMng::thumb($image_file, $cached_file, $dims_arr[0], $dims_arr[1]);
            }

            if (file_exists($cached_file)){
              $thumb = $cached_file;
            }

            $caption = $el['caption'][$lang];


            array_push($data, [
              'img' => utils::getBaseUrl() . $image_file,
              'thumb' =>utils::getBaseUrl() . $thumb,
              'caption' => $caption,
              'href' => $el['href']
            ]);
        }
        return $data;
    }
}
