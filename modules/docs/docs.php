<?php
/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
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

    if (file_exists('docs/' . $file . '.md')) {

      $contents = file_get_contents('docs/' . $file . '.md');
      $contents = str_replace(['{% raw %}', '{% endraw %}'], null, $contents);

      $html = Parsedown::instance()->text($contents);

      echo preg_replace('/href="(?!http)/', 'href="#docs/read/', $html);

      echo '<hr>'
        . '<p class="text-muted"><big><i class="big fa fa-edit"></i></big> Enhance this documentation file: '
        . '<a href="' . $remote . '/edit/dev/docs/' . $file . '.md" target="_blank">edit this page on Github (you must sign in to make or propose changes)</a>'
        . ' or <a href="' . $remote . '/raw/dev/docs/' . $file . '.md" target="_blank">download the raw file</a>, edit it and send it by email to '
        . '<a href="mailto:developer@bradypus.net">developer@bradypus.net</a>'
        . "<script>$('.active a').each(function(i, el){ if ($(el).attr('href').indexOf('http:') > -1 ){ $(el).attr('target', '_blank'); } });</script>"
      ;
    }
  }
}
