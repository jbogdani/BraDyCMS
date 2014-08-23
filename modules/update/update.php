<?php

/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013 
 * @license			MIT, See LICENSE file
 * @since				Sep 27, 2013
 */

class update_ctrl extends Controller
{
  
  private function getPath($what = false)
  {
    $channel = cfg::get('updatechannel');
    
    if (!$what || $what === 'zip')
    {
      return !$channel || $channel === 'master' ?
        'https://github.com/jbogdani/BraDyCMS/archive/master.zip'
        :
        'https://github.com/jbogdani/BraDyCMS/archive/dev.zip';
    }
    else if ($what === 'ini')
    {
      return !$channel || $channel === 'master' ?
        'https://raw.github.com/jbogdani/BraDyCMS/master/version'
        :
        'https://raw.github.com/jbogdani/BraDyCMS/dev/version';
    }
    else if ($what === 'package')
    {
      return !$channel || $channel === 'master' ?
        'BraDyCMS-master'
        :
        'BraDyCMS-dev';
    }
  }
  
  public function main()
  {
    $this->render('update', 'update', array(
      'curr_v' => version::current()
    ));
  }
  
  public function check()
  {
    try
    {
      $update = new Update();
      $res = $update->checkUpdate(version::current(), $this->getPath('ini'));
      $res['branch'] = $this->getPath('package');
      $this->render('update', 'check_result', $res);
     
    }
    catch (Exception $e)
    {
      echo '<div class="alert alert-danger">' . 
        '<p class="lead">' . tr::get('error_version_check') . '</p>' .
        '</div>';
      error_log(var_export($e, true));
    }
  }
  
  public function stepByStepInstall()
  {
    $basePath = TMP_DIR . md5($this->get['remoteVersion']) . '/';
    $localZipPath = $basePath . $this->get['remoteVersion'] . '.zip';
    
    if (!is_dir($basePath))
    {
      @mkdir($basePath, 0777, true);
    }
    
    try
    {
      $update = new Update();
      
      switch($this->get['step'])
      {
        case 'start':
          
          if (!file_exists($localZipPath) || $this->getPath('package') === 'BraDyCMS-dev')
          {
            $update->downloadZip($this->getPath('zip'), $localZipPath);
          }
          $resp = array('status' => 'success', 'text' => tr::get('update_downloaded'), 'step' => 'unzip', 'remoteVersion' => $this->get['remoteVersion']);
          break;
        
        case 'unzip':
          if (!is_dir($basePath . $this->getPath('package')))
          {
            $update->unzip($localZipPath, $basePath, false, true);
          }
          $resp = array('status' => 'success', 'text' => tr::get('update_unpacked'), 'step' => 'install', 'remoteVersion' => $this->get['remoteVersion']);
        break;
      
        case 'install':
          $update->install($basePath . $this->getPath('package'), '.');
          $resp = array('status' => 'success', 'text' => tr::get('update_installed'), 'step' => 'update_htaccess', 'remoteVersion' => $this->get['remoteVersion']);
          break;
        
        case 'update_htaccess':
          if(utils::update_htaccess())
          {
            $resp = array('status' => 'success', 'text' => tr::get('htaccess_updated'), 'step' => 'empty_cache', 'remoteVersion' => $this->get['remoteVersion']);
          }
          else
          {
            throw new Exception(tr::get('htaccess_not_updated'));
          }
          break;
        case 'empty_cache':
          $error = utils::recursive_delete(CACHE_DIR, true);
		
          if(count($error) > 0)
          {
            throw new Exception(tr::get('cache_not_emptied'));
          }
          else
          {
            $resp = array('status' => 'success', 'text' => tr::get('cache_emptied'), 'step' => 'empty_trash', 'remoteVersion' => $this->get['remoteVersion']);
          }
        case 'emty_trash':
          $error = utils::recursive_delete(TMP_DIR, true);
		
          if(count($error) > 0)
          {
            throw new Exception(tr::get('trash_not_emptied'));
          }
          else
          {
            $resp = array('status' => 'success', 'text' => tr::get('trash_emptied'), 'step' => 'finished', 'remoteVersion' => $this->get['remoteVersion']);
          }
          
          break;
        case false:
        default:
          return;
          break;
      }
    }
    catch(Exception $e)
    {
      $resp = array('status' => 'error', 'text' => tr::get('error_install'), 'step' => $this->get['step']);
      error_log(var_export($e, true));
    }
    
    echo json_encode($resp);
  }
  
}
?>
