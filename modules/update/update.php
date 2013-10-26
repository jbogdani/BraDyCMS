<?php

/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013 
 * @license			MIT, See LICENSE file
 * @since				Sep 27, 2013
 */

class update_ctrl extends Controller
{
  
  private $path2ZIP = 'https://github.com/jbogdani/BraDyCMS/archive/master.zip';
  
  private $path2ini = 'https://raw.github.com/jbogdani/BraDyCMS/master/version';
  
  
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
      
      $res = $update->checkUpdate(version::current(), $this->path2ini);
      
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
      
      $update->downloadZip($this->path2ZIP, $localZipPath);
      echo '<p class="lead text-success"><i class="glyphicon glyphicon-ok"></i> ' . tr::get('update_downloaded') . '</p>';
      
      $update->unzip($localZipPath, false, false, true);
      echo '<p class="lead text-success"><i class="glyphicon glyphicon-ok"></i> ' . tr::get('update_unpacked') . '</p>';
      
      $update->install(TMP_DIR . 'BraDyCMS-master', '.');
      echo '<p class="lead text-success"><i class="glyphicon glyphicon-ok"></i> ' . tr::get('update_installed') . '</p>';
      
      echo '<p class="lead">The update was successfully installed. You ' .
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
  
}
?>
