<?php
/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Dec 11, 2012
 */
 
class changelog_ctrl extends Controller
{
  public function show()
  {
    $html = '<h1>BraDyCMS '
        . '<small>Current version: ' . version::current() .'</small></h1>'
        . '<h3>Changelog</h3>'
        .'<ul>';
    $chl = version::changelog();
    
    if (is_array($chl))
    {
      foreach ($chl as $v=>$arr)
      {
        $html .= '<li>' . $v
        . '<ul>'
          . '<li>' . implode('</li><li>', $arr) . '</li>'
        . '</ul>' 
        . '</li>';
      }
    }
    
    $html .= '</ul>';
    
    echo $html;
  } 
}