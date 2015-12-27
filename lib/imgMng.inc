<?php
/**
 * Main image manupilation class.
 * Uses Imagemagick binary covert of Imagick php extension, depending on system cfg.
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDypUS 2007-2013
 * @license      MIT, See LICENSE file
 * @since      May 16, 2013
 * @uses cfg main configuration class
 */


class imgMng
{

  /**
   * Alias for self::process_convert($ofile, $nfile, 'thumb', $details)
   * @param string $ofile
   * @param string $nfile
   * @param string $details  thumb dimensions: eg. 200x200
   * @return true;
   */
  public static function thumb($ofile, $nfile = false, $details)
  {
    return self::process_convert($ofile, $nfile, 'thumb', $details);
  }


  /**
   * Main image manipulation method
   * @param string $ofile path to original file
   * @param string $nfile path to destination file
   * @param string $type  action to perform. One of: convert (default)|crop|resize|thumb
   * @param string $details transformation details (sizes, offsets, etc.)
   * @return string String with successfull message
   * @throws Exception
   */
  public static function process_convert($ofile, $nfile = false, $type = false, $details = false)
  {
    // Default type is <convert>
    $type = $type ? $type : 'convert';

    // If no new file is provided a temporary file will be created and original
    // file will be overwritten
    if (!$nfile)
    {
      $nfile = TMP_DIR . uniqid('file');
      $overwriteOriginal = true;
    }

    // Get system config paths
    $sys_paths = cfg::get('paths');

    // Choose conversion engine/system depending on cfg value
    switch ($sys_paths['convert'])
    {
      case 'imagick':
        if(!class_exists('Imagick'))
        {
          throw new Exception('imagick_not_available');
        }
        self::process_convert_imagick($ofile, $nfile, $type, $details);
        break;

      case '':
      case false:
        throw new Exception('no_image_manipulation_setting_found');
        break;

      default:
        self::process_convert_binary($ofile, $nfile, $type, $details);
        break;
    }

    if (!file_exists($nfile))
    {
      throw new Exception(self::getMsg($type, 'error'));
    }

    if ($overwriteOriginal)
    {
      @rename($nfile, $ofile);
    }
    return self::getMsg($type, 'ok');
  }


  /**
   * Returns error/success messages
   * @param string $type maniputaltion type: convert|crop|resize|thumb
   * @param string $status message type: ok|error
   * @return string message
   */
  private static function getMsg($type, $status)
  {
    $msg['convert']['ok'] = 'ok_converting_file';
    $msg['convert']['error'] = 'error_converting_file';

    $msg['crop']['ok'] = 'ok_cropping_file';
    $msg['crop']['error'] = 'error_cropping_file';

    $msg['resize']['ok'] = 'ok_resizing_file';
    $msg['resize']['error'] = 'error_resizing_file';

    $msg['thumb']['ok'] = 'thumbnail_created';
    $msg['thumb']['error'] = 'thumbnail_not_created';

    return $msg[$type][$status];
  }

  /**
   * Uses binary convert executable to convert files
   * @param string $ofile path to original file
   * @param string $nfile path to destination file
   * @param string $type  action to perform. One of: convert|crop|resize|thumb
   * @param string $details transformation details (sizes, offsets, etc.)
   * @throws Exception
   */
  private static function process_convert_binary($ofile, $nfile = false, $type, $details = false)
  {
    $exec_path = cfg::get('paths');

    switch($type)
    {
      case 'convert':
        $convert = $exec_path['convert'] . ' "' . $ofile . '" "' . $nfile . '"';
        break;

      case 'crop':
        $convert = $exec_path['convert'] . " -crop '" . $details . "' " . ' "' . $ofile . '" "' . $nfile . '"';
        break;

      case 'resize':
        $convert = $exec_path['convert'] . " -resize '" . $details . '\'  "' . $ofile . '" "' . $nfile . '"';
        break;

      case 'thumb':
        $convert = $exec_path['convert'] . ' -resize ' . $details . '^ "' . $ofile . '" -background none -gravity center -extent ' . $details . ' "' . $nfile . '"';
        break;

      default:
        return;
        break;
    }

    @exec($convert, $a, $b);
  }

  /**
   * Uses imagick extension to convert files
   * @param string $ofile path to original file
   * @param string $nfile path to destination file
   * @param string $type  action to perform. One of: convert|crop|resize|thumb
   * @param string $details transformation details (sizes, offsets, etc.)
   * @throws Exception
   */

  private static function process_convert_imagick($ofile, $nfile = false, $type, $details = false)
  {
    $img = new Imagick($ofile);

    switch($type)
    {
      case 'convert':
        $img->setImageFormat(pathinfo($nfile, PATHINFO_EXTENSION));
        break;

      case 'crop':
        $dims = explode(' ', $details);
        $size = explode('x', $dims[0]);

        $img->cropimage($size[0], $size[1], $dims[1], $dims[2]);
        break;

      case 'resize':
        $size = explode('x', $details);

        $img->scaleimage($size[0], $size[1], true);

        $convert = $exec_path['convert'] . " -resize '" . $details . '\'  "' . $ofile . '" "' . $nfile . '"';
        break;

      case 'thumb':
        $size = explode('x', $details);

        $img->cropThumbnailImage($size[0], $size[1]);
        break;

      default:
        return;
        break;
    }

    $img->writeimage($nfile);
  }

}
