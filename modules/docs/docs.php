<?php
/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license      MIT, See LICENSE file
 * @since      Dec 21, 2012
 */

class docs_ctrl extends Controller
{
  public function read()
  {
    $file = $this->get['param'][0];

    if (file_exists('docs/' . $file . '.md'))
    {
      echo Parsedown::instance()->text(file_get_contents('docs/' . $file . '.md'));

      echo '<hr>'
        . '<p class="text-muted"><big><i class="icon big ion-edit"></i></big> Enhance this documentation file: '
        . '<a href="https://github.com/jbogdani/BraDyCMS/edit/dev/docs/' . $file . '.md" target="_blank">edit this page on Github (you must sign in to make or propose changes)</a>'
        . ' or <a href="https://github.com/jbogdani/BraDyCMS/raw/dev/docs/' . $file . '.md" target="_blank">download the raw file</a>, edit it and send it by email to '
        . '<a href="mailto:developer@bradypus.net">developer@bradypus.net</a>'
        . "<script>$('.active a').each(function(i, el){ if ($(el).attr('href').indexOf('http:') > -1 ){ $(el).attr('target', '_blank'); } });</script>"
      ;
    }
  }
}
