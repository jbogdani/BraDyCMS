<?php

/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net, Julian Bogdani <jbogdani@gmail.com>
 * @license      See file LICENSE distributed with this code
 * @since        Jun 23, 2014
 */

class Gallery{

  public static function getGallery($gal)
  {
    $lang = utils::getCurrLang();

    if (file_exists(GALLERY_DIR . $gal  . '/data' . ($lang ? '_' . $lang : '') . '.json'))
    {
      $file = GALLERY_DIR . $gal  . '/data' . ($lang ? '_' . $lang : '') . '.json';
    }
    else if (file_exists(GALLERY_DIR . $gal  . '/data' . '.json'))
    {
      $file = GALLERY_DIR . $gal  . '/data' . '.json';
    }
    else
    {
      throw new Exception('Gallery ' . $gal . ' does not exist');
    }

    $captions = @json_decode(file_get_contents($file), 1);

    if (!is_array($captions))
    {
      throw new Exception('Gallery ' . $gal . ' is not well formatted');
    }
    krsort($captions);

    $gal_data = array();

    foreach ($captions as $img => $cap)
    {
      $img = str_replace('__x__', '.', $img);

      $gal_data[] = array(
        'img' => file_exists('./sites/default/images/galleries/' .$gal . '/' . $img) ? utils::getBaseUrl() . 'sites/default/images/galleries/' .$gal . '/' . $img : false,
        'thumb' => file_exists('./sites/default/images/galleries/' .$gal . '/thumbs/' . $img) ? utils::getBaseUrl() . 'sites/default/images/galleries/' .$gal . '/thumbs/' . $img : false,
        'caption' => $cap
      );
    }

    return $gal_data;
  }

}
?>
