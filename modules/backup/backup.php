<?php
/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Jun 9, 2014
 */

class backup_ctrl extends Controller
{
  
  public $adminRequired = array('deleteBackup', 'restoreBackup');
  
  public function listAll()
  {
    if (!file_exists(SITE_DIR . 'cfg/database.sqlite'))
    {
      $html = '<div class="bg-danger" style="padding: 20px;">'
        . '<p class="lead"><i class="fa fa-exclamation-triangle"></i> ' . tr::get('function_available_only_for_sqlite_database'). '</p>'
        . '</div>';
      
      echo $html;
      return false;
    }
    
    $bup_dir = SITE_DIR . 'backups';
    
    if (!is_dir($bup_dir))
    {
      @mkdir($bup_dir, 0777, true);
      
      if (!is_dir($bup_dir))
      {
        error_log('Cano not create directory: ' . $bup_dir);
      }
      
    }
    
    $files = utils::dirContent($bup_dir);
    
    foreach ($files as $f)
    {
      $all_files[] = array(
        'name' => $f,
        'date'=> date('Y-m-d H:i:s', $f),
        'size' => round((filesize(SITE_DIR . 'backups/' . $f))/1024/1024, 2)
      ); 
    }
    $this->render('backup', 'list', array(
      'list' => $all_files,
      'is_admin' => $_SESSION['user_admin']
    ));
    
  }
  
  
  
  public function createNew()
  {
    try
    {
      $filename = SITE_DIR . 'backups/' . microtime(true);
      @copy(SITE_DIR . 'cfg/database.sqlite', $filename);
      
      if (!file_exists($filename))
      {
        throw new Exception('Can not create backup!');
      }
    }
    catch (Exception $e)
    {
      error_log($e->getMessage());
      
      $resp['status'] = 'error';
      $resp['text'] = tr::get('error_create_backup');
    }
    
    $resp['status'] = 'success';
    $resp['text'] = tr::get('ok_create_backup');
    
    echo json_encode($resp);
  }
  
  
  
  
  public function deleteBackup()
  {
    $file = SITE_DIR . 'backups/' . $this->get['param'][0];
    
    $resp['status'] = 'success';
    $resp['text'] = tr::get('ok_deleting_backup');
    
    if (file_exists($file))
    {
      @unlink($file);
      
      if (file_exists($file))
      {
        $resp['status'] = 'error';
        $resp['text'] = tr::get('error_deleting_backup');
      }
    }
    
    echo json_encode($resp);
  }
  
  
  
  
  public function restoreBackup()
  {
    $file = SITE_DIR . 'backups/' . $this->get['param'][0];
    
    try
    {
      $filename = SITE_DIR . 'backups/' . microtime(true);
      @copy(SITE_DIR . 'cfg/database.sqlite', $filename);
      
      if (!file_exists($filename))
      {
        throw new Exception('Can not create backup!');
      }
      
      @unlink(SITE_DIR . 'cfg/database.sqlite');
      
      if (file_exists(SITE_DIR . 'cfg/database.sqlite'))
      {
        throw new Exception('Can not erase present database');
      }
      
      @copy($file, SITE_DIR . 'cfg/database.sqlite');
      
      if (file_exists(SITE_DIR . 'cfg/database.sqlite'))
      {
        throw new Exception('Can not copy backup file to database location');
      }
      
      
    }
    catch (Exception $e)
    {
      error_log($e->getMessage());
      $resp['status'] = 'error';
      $resp['text'] = tr::get('error_restore_backup');
    }
    
    $resp['status'] = 'success';
    $resp['text'] = tr::get('ok_restore_backup');
    
    echo json_encode($resp);
  }
  
}

?>
