<?php
/**
 * Abstraction librarry for image manipulation
 * Uses Intervention with gd (default) or imagick php extensions
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDypUS 2007-2013
 * @license      MIT, See LICENSE file
 * @since      May 16, 2013
 * @uses    Intervention
 */

class imgMng
{
    private static $mng;

    /**
     * Checks if Imagick is available and file exists and throws Exception on errors
     * @param  string $file Path to file to be processed
     * @return true on success
     * @throws Exception on errors
     */
    private function checkFile($file)
    {
        $driver =  (cfg::get('img_manager') === 'imagick') ?  ['driver' => 'imagick'] : ['driver' => 'gd'];
        if (is_null(self::$mng)) {
            self::$mng = new Intervention\Image\ImageManager($driver);
        }

        if (!file_exists($file)) {
            throw new Exception('The original image file ' . $file . ' does not exist');
        }
    }


    /**
     * Creates thumbnails from original image
     * @param  string  $oFile  Path to original image
     * @param  string|false $nFile  Path to destination image, $oFile will be used if not provided
     * @param  int  $width  Thumbnail's width
     * @param  int $height Thumbnail's height
     * @return true on success
     */
    public static function thumb($file, $nFile = false, $width, $height = false)
    {
        self::checkFile($file);
        $img = self::$mng->make($file)->fit($width, $height)->save($nFile);
    }

    /**
     * Converts origial image to new file and throws Exception on errors
     * @param  string   full path to old image file
     * @param  string   full path to new image file
     * @return true on succes
     */
    public static function convert($oFile, $nFile)
    {
        self::checkFile($oFile);
        $img = self::$mng->make($oFile)->save($nFile);
    }

    /**
     * Resizes image to figen width x height x resolution and throws Exception on errors
     * @param  string  $file          full path to image to resize
     * @param  int|false $width       new width (if false height's value will be used)
     * @param  int|false $height      new height (if false wisth's value will be used)
     * @param  boolean $onlyDownscale if true image will be scaled only id original dimension is bigger then destination dimension
     * @return true on success
     */
    public static function resize($file, $width = false, $height = false, $onlyDownscale = false)
    {
        if (!$width && !$height) {
            throw new Exception('One of width or height values is required.');
        }

        self::checkFile($file);
        $img = self::$mng->make($file)->resize($width, $height, function ($constraint) use ($onlyDownscale) {
          $constraint->aspectRatio();

          if ($onlyDownscale) {
              $constraint->upsize();
          }
        })->save();
    }

    /**
     * Crops image file to given values and throws Exception on errors
     * @param  string     full path to image to crop
     * @param  int  new image width
     * @param  int  new image height
     * @param  int|false  The X coordinate of the cropped region's top left corner
     * @param  int|false  The Y coordinate of the cropped region's top left corner
     * @return true on success
     */
    public static function crop($file, $width, $height, $x = false, $y = false)
    {
        self::checkFile($file);
        $img = self::$mng->make($file)->crop($width, $height, $x, $y)->save();
    }
}
