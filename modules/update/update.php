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
  
  
  public function install()
  {
    try
    {
      $update = new Update();
      
      $localZipPath = TMP_DIR . uniqid() . '.zip';
      
      $update->downloadZip($this->getPath('zip'), $localZipPath);
      echo '<p class="lead text-success"><i class="glyphicon glyphicon-ok"></i> ' . tr::get('update_downloaded') . '</p>';
      
      $update->unzip($localZipPath, false, false, true);
      echo '<p class="lead text-success"><i class="glyphicon glyphicon-ok"></i> ' . tr::get('update_unpacked') . '</p>';
      
      $update->install(TMP_DIR . $this->getPath('package'), '.');
      echo '<p class="lead text-success"><i class="glyphicon glyphicon-ok"></i> ' . tr::get('update_installed') . '</p>';
      
      echo '<p class="lead text-warning">The update was successfully installed. You ' .
        'should consider emptying the cache, the trash and eventually updating ' .
        'the .htaccess file. Follow <a href="#cfg/edit">this link</a> to ' .
        'perform all these post-installation actions.</p>';
      
    }
    catch (Exception $e)
    {
      echo '<div class="alert alert-danger">' . 
        '<p class="lead">' . tr::get('error_install') . '</p>' .
        '</div>';
      error_log(var_export($e, true));
    }
  }
  
  public function stepByStepInstall()
  {
    try
    {
      $update = new Update();
      
      switch($this->get['step'])
      {
        case 'start':
          $localZipPath = TMP_DIR . uniqid() . '.zip';
          $update->downloadZip($this->getPath('zip'), $localZipPath);
          $resp = array('status' => 'success', 'text' => tr::get('update_downloaded'), 'step' => 'unzip', 'localZipPath' => $localZipPath);
          break;
        
        case 'unzip':
          $update->unzip($this->get['localZipPath'], false, false, true);
          $resp = array('status' => 'success', 'text' => tr::get('update_unpacked'), 'step' => 'install');
        break;
      
        case 'install':
          $update->install(TMP_DIR . $this->getPath('package'), '.');
          $resp = array('status' => 'success', 'text' => tr::get('update_installed'), 'step' => 'finished');
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
