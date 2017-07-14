<?php
/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license      MIT, See LICENSE file
 * @since      Dec 21, 2012
 */

class docs_ctrl extends Controller
{
  /**
   * Path to Github repository
   * @var string
   */
  private $remote = 'https://github.com/jbogdani/BraDyCMS';

  public function read()
  {
    $file = $this->get['param'][0];

    if (file_exists('docs/' . $file . '.md'))
    {
      $text = Parsedown::instance()->text(file_get_contents('docs/' . $file . '.md'));
      echo preg_replace(
        '/href="([a-zA-Z_]+)\.md"/',
         'href="#docs/read/$1"',
         $text);

      echo '<hr>'
        . '<p class="text-muted"><big><i class="icon big ion-edit"></i></big> Enhance this documentation file: '
        . '<a href="' . $remote . '/edit/dev/docs/' . $file . '.md" target="_blank">edit this page on Github (you must sign in to make or propose changes)</a>'
        . ' or <a href="' . $remote . '/raw/dev/docs/' . $file . '.md" target="_blank">download the raw file</a>, edit it and send it by email to '
        . '<a href="mailto:developer@bradypus.net">developer@bradypus.net</a>'
        . "<script>$('.active a').each(function(i, el){ if ($(el).attr('href').indexOf('http:') > -1 ){ $(el).attr('target', '_blank'); } });</script>"
      ;
    }
  }
}
