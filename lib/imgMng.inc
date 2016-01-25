<?php
/**
 * Main image manupilation class.
 * Uses Imagemagick Imagick php extension
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDypUS 2007-2013
 * @license      MIT, See LICENSE file
 * @since      May 16, 2013
 * @uses    Imagick
 */


class imgMng
{
  
  /**
   * Checks if Imagick is available and file exists and throws Exception on errors
   * @param  string $file Path to file to be processed
   * @return true on success
   * @throws Exception on errors
   */
  private function checkFile($file)
  {
    if (!class_exists('Imagick') && (!cfg::get('im_convert') || !file_exists(cfg::get('im_convert'))))
    {
      throw new Exception('Class Imagick or ImageMacik binary is required for image manipulation');
    }

    if (!file_exists($file))
    {
      throw new Exception('The original image file ' . $file . ' does not exist');
    }
  }

  /**
   * Checks if Imagick is installed and available
   * @return boolean 
   */
  private function isImagick()
  {
    return class_exists('Imagick');
  }

  /**
   * Creates thumbnails fro original image
   * @param  string  $oFile  Path to original image
   * @param  string|false $nFile  Path to destination image, $oFile will be used if not provided
   * @param  int  $width  Thumbnail's width
   * @param  int $height Thumbnail's height
   * @return true on success
   */
  public static function thumb($oFile, $nFile = false, $width, $height = false)
  {
    self::checkFile($oFile);

    $tmp_file = TMP_DIR . uniqid('file');

    if (self::isImagick())
    {
      $img = new Imagick($oFile);

      if (!$img->cropThumbnailImage($width, $height))
      {
        throw new Exception('Error in creating thumbnail');
      }

      if (!$img->writeimage($tmp_file))
      {
        throw new Exception('Error in saving new thumbnail');
      }
    }
    else
    {
      $cmd = cfg::get('im_convert') . 
        ' -resize ' . $width . ($height ? 'x' . $height : '') . '^ ' .
        ' "' . $oFile . '" ' .
        '-background none ' .
        '-gravity center ' .
        '-extent ' . $width . ($height ? 'x' . $height : '') .
        ' "' . $tmp_file . '"';

      @exec($cmd, $a, $b);
    }

    if (!file_exists($tmp_file))
    {
      throw new Exception('Error: new thumbnail file does not exist');
    }

    if (!@rename($tmp_file, ($nFile ? $nFile : $oFile)))
    {
      throw new Exception('Error in moving thumbnail');
    }

    return true;
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

    if (self::isImagick())
    {
      $img = new Imagick($oFile);

      if (!$img->setImageFormat(pathinfo($nFile, PATHINFO_EXTENSION)))
      {
        throw new Exception('Error in setting image format');
      }
      if (!$img->writeimage($nFile))
      {
        throw new Exception('Error in saving new image');
      }
    }
    else
    {
      $cmd = cfg::get('im_convert') . 
        ' "' . $oFile . '" ' .
        ' "' . $nFile . '"';

      @exec($cmd, $a, $b);
    }

    if (!file_exists($nFile))
    {
      throw new Exception('Error: new image file does not exist');
    }
    
    return true;
  }

  /**
   * Resizes image to figen width x height x resolution and throws Exception on errors
   * @param  string  $file          full path to image to resize
   * @param  int|false $width       new width (if false height's value will be used)
   * @param  int|false $height      new height (if false wisth's value will be used)
   * @param  int|false $resolution  new height (if false wisth's value will be used)
   * @param  boolean $onlyDownscale if true image will be scaled only id original dimension is bigger then destination dimension
   * @return true on success
   */
  public static function resize($file, $width = false, $height = false, $resolution = false, $onlyDownscale = false)
  {
    self::checkFile($file);

    if (!$width && !$height)
    {
      throw new Exception('One of width or height values is required.');
    }

    if ($width && $height)
    {
      $bestfit = true;
    }

    $tmp_file = TMP_DIR . uniqid('file');

    if (self::isImagick())
    {
      $img = new Imagick($file);

      if ($img->getImageWidth() > $width || $img->getImageHeight > $height)
      {
        if (!$img->scaleImage($width, $height, $bestfit))
        {
          throw new Exception("Error in scaling image");
        }
      }

      if (!$img->writeimage($tmp_file))
      {
        throw new Exception('Error in saving new image');
      }
    }
    else
    {
      $cmd = cfg::get('im_convert') . 
        ' -resize ' . $width . ($height ? 'x' . $height : '') . ($onlyDownscale ? '\>' : '') .
        ($resolution ? ' -density ' . $resolution : '') .
        ' "' . $file . '" ' .
        ' "' . $tmp_file . '"';

      @exec($cmd, $a, $b);
    }

    if (!file_exists($tmp_file))
    {
      throw new Exception('Error: new image file does not exist');
    }

    if (!@rename($tmp_file, $file))
    {
      throw new Exception('Error in moving resized image');
    }

    return true;
  }

  /**
   * Crops image file to given values and throws Exception on errors
   * @param  string     full path to image to crop
   * @param  int|false  new image width 
   * @param  int|false  new image height
   * @param  int|false  The X coordinate of the cropped region's top left corner
   * @param  int|false  The Y coordinate of the cropped region's top left corner
   * @return true on success
   */
  public static function crop($file, $width = false, $height = false, $x = false, $y = false)
  {
    self::checkFile($file);

    if (!$width && !$height)
    {
      throw new Exception('One of width or height values is required.');
    }

    $tmp_file = TMP_DIR . uniqid('file');

    if (self::isImagick())
    {
      $img = new Imagick($file);

      if (!$img->cropImage($width, $height, $x, $y))
      {
        throw new Exception("Error in cropping image");
      }

      if (!$img->writeimage($tmp_file))
      {
        throw new Exception('Error in saving new image');
      }
    }
    else
    {
      $cmd = cfg::get('im_convert') . 
        ' -crop ' . $width . ($height ? 'x' . $height : '') .
        ' "' . $file . '" ' .
        ' "' . $tmp_file . '"';

      @exec($cmd, $a, $b);
    }

    if (!file_exists($tmp_file))
    {
      throw new Exception('Error: new image file does not exist');
    }

    if (!@rename($tmp_file, $file))
    {
      throw new Exception('Error in moving cropped image');
    }

    return true;
  }


}
