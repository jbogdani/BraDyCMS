<?php
/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license      MIT, See LICENSE file
 * @since      Aug 28, 2013
 */

class migrate_ctrl extends Controller
{
  public function tags()
  {
    echo 'Security issue! Uncomment the return statement in /modules/migrate/migrate.php to continue'; return;
    $art_list = R::findAll('articles');
    
    foreach($art_list as $art)
    {
      $tags = utils::csv_explode($art->tags);
      
      if ($art->section && $art->section != '' && !in_array($art->section, $tags))
      {
        $tags[] = $art->section;
      }
      
      if (is_array($tags) && !empty($tags))
      {
        R::tag($art, $tags);
      }
    }
  }
  
  public function transArt()
  {
    echo 'Security issue! Uncomment the return statement in /modules/migrate/migrate.php to continue'; return;
    $lang = $this->get['param'][0];
    
    $trans = R::findAll('articles_' . $lang);
    
    foreach ($trans as $t)
    {
      $data['lang'] = $lang;
      $data['status'] = $t['translated'];
      $data['title'] = $t['title'];
      $data['summary'] = $t['summary'];
      $data['text'] = $t['text'];
      $data['keywords'] = $t['keywords'];
      Article::translate($t['id_art'], $data);
    }
  }
  
  public function transMenu()
  {
    echo 'Security issue! Uncomment the return statement in /modules/migrate/migrate.php to continue'; return;
    $lang = $this->get['param'][0];
    
    $trans = R::findAll('menu_' . $lang);
    
    foreach ($trans as $t)
    {
      $data['lang'] = $lang;
      $data['status'] = $t['translated'];
      $data['item'] = $t['item'];
      $data['title'] = $t['title'];
      Menu::translate($t['id_menu'], $data);
    }
  }
}
?>

