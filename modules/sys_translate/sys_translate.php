<?php
/**
 * 
 * 
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Aug 12, 2012
 */

class sys_translate_ctrl extends Controller
{
  
  public $adminRequired = array('addLine', 'add_locale', 'save',
    'showForm', 'showList');
  
  public function showList($opened_lang=false)
  {
    $context = $this->get['param'][0];
    $lang = $this->get['param'][1];
    
    switch($context)
    {
      case 'admin':
        $langs = utils::dirContent(LOCALE_DIR);
        $add_lang = true;
        break;
      
      case 'front':
      default :
        foreach(cfg::get('languages') as $l_arr)
        {
          $langs[] = $l_arr['id'];
        }
        $langs[] = cfg::get('sys_lang');
        break;
    }
    
    $this->render('sys_translate', 'list', array(
      'opened_lang' => $opened_lang,
      'langs' => $langs,
      'add_lang' => $add_lang,
      'context' => $context,
      'lang' => $lang
    ));
  }
  
  
  public function showForm()
  {
    $context = $this->get['param'][0];
    $lng = $this->get['param'][1];
    
    if($context === 'admin')
    {
      require LOCALE_DIR . 'en.php';
      $en = $_lang;
      unset($_lang);
    
      require LOCALE_DIR . $lng . '.php';
      $edit_lang = $_lang;
      unset($_lang);
      
      if ($lng === 'en')
      {
        $can_add = true;
      }
    }
    else
    {
      if (file_exists(SITE_DIR . 'locale/' . cfg::get('sys_lang') . '.php'))
      {
        require SITE_DIR . 'locale/' . cfg::get('sys_lang') . '.php';
        $en = $_lang;
        unset($_lang);
      }
      else
      {
        $en = array();
      }
      
      if (file_exists(SITE_DIR . 'locale/' . $lng . '.php'))
      {
        require SITE_DIR . 'locale/' . $lng . '.php';
        $edit_lang = $_lang;
        unset($_lang);
      }
      else
      {
        $edit_lang = array();
      }
      
      if ($lng === cfg::get('sys_lang'))
      {
        $can_add = true;
      }
    }
    
    $this->render('sys_translate', 'form', array(
      'lng' => $lng,
      'en' => $en,
      'edit_lang' => $edit_lang,
      'context' => $context,
      'can_add' => $can_add
    ));
  }
  
  
  public function add_locale()
  {
    $lang = $this->get['param'][0];
    
    if (!file_exists(LOCALE_DIR . $lang . '.php') && utils::write_in_file(LOCALE_DIR . $lang . '.php', ''))
    {
      $msg['text'] = tr::get('ok_lang_create');
      $msg['status'] = 'success';
    }
    else
    {
      $msg['text'] = tr::get('error_lang_create');
      $msg['status'] = 'error';
    }
    
    echo json_encode($msg);
  }
  
  
  public function save()
  {
    $context = $this->get['param'][0];
    $post = $this->post;
    
    if ($context === 'admin')
    {
      $file = LOCALE_DIR . $post['edit_lang'] .'.php';
    }
    else
    {
      $file = SITE_DIR . 'locale/' . $post['edit_lang'] .'.php';
    }
    
    unset($post['edit_lang']);
    
    echo $this->array2file($post, $file);
  }
  
  public function addLine()
  {
    $context = $this->get['param'][0];
    $lng = $this->get['param'][1];
    $key = $this->get['param'][2];
    $val = $this->get['param'][3];
    
    if ($context === 'admin')
    {
      $file = LOCALE_DIR . $lng .'.php';
    }
    else
    {
      $file = SITE_DIR . 'locale/' . $lng .'.php';
    }
    if(file_exists($file))
    {
      require $file;
    }
    if (is_array($_lang) && array_key_exists($key, $_lang))
    {
      echo json_encode(array(
        'status' => 'error',
        'message' => tr::sget('key_exists', [$key])
        ));
      return;
    }
    
    $_lang[$key] = $val;
    
    echo $this->array2file($_lang, $file);
  }
  
  
  
  private function array2file($array, $file)
  {
    if (!is_dir(SITE_DIR . 'locale'))
    {
      @mkdir(SITE_DIR . 'locale');
    }
      
    foreach ($array as $k => $v)
    {
      $text[]='$_lang[\'' . $k . '\'] = "' . str_replace(array('"', "\r\n"), array('\'', '\\n'), $v) . '";'; 
    }
    
    if(utils::write_in_file($file, '<?php' . "\n" . implode("\n", $text)))
    {
      return json_encode(array('text'=>tr::get('ok_language_update'), 'status'=>'success'));
    }
    else
    {
      return json_encode(array('text'=>tr::get('error_language_update'), 'status'=>'error'));
    }
  }
    
}