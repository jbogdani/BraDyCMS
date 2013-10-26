<?php

/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013 
 * @license			MIT, See LICENSE file
 * @since				Sep 26, 2013
 * @example     try
 *              {
 *                $update = new Update();
 *                $res = $update->checkUpdate(version::current(), 'https://raw.github.com/jbogdani/BraDyCMS/master/version');
 *                if ($res['status'] == 'updateable_warning' || $res['status'] == 'updateable')
 *                {
 *                  $zipPath = TMP_DIR . uniqid() . '.zip' ;
 *                  $zip = $update->downloadZip('https://github.com/jbogdani/BraDyCMS/archive/master.zip', $zipPath);
 *                  $update->unzip($zipPath, false, false, true);
 *                  $update->install(TMP_DIR . 'BraDyCMS-master', './ciao');
 *                }
 *                else
 *                {
 *                  echo $res['text'];
 *                }
 *              }
 *              catch(Exception $e)
 *              {
 *                var_dump($e);
 *              }
 */

class Update
{

  public function __construct()
  {
  }
  
  /**
   * Parses remote version ini file and compares versions
   * @param type $localVersion  semver formatted local current version
   * @param type $iniPath       full path to remote ini version file
   * @return array $respo       array with response:
   *                              remote_vers: remote version
   *                              local_vers: local version
   *                              status: not_updateable, updateable_warning, already_updated, updateable
   * @throws Exception
   */
  public function checkUpdate($localVersion, $iniPath)
  {
    $iniContent = @file_get_contents($iniPath);
    
    if (!$iniContent)
    {
      throw new Exception('Can not get content of remote ini file');
    }
    
    
    $va = parse_ini_string($iniContent);
		$remote_version = max(array_keys($va));
    
    $remote_version_arr = explode('.', $remote_version);
    $local_version_arr = explode('.', $localVersion);
    
    $resp['remote_vers'] = $remote_version;
    $resp['local_vers'] = $localVersion;
    
    try
    {
      if ($remote_version_arr[0] > $local_version_arr[0])
      {
        throw new Exception('not_updateable');
      }
      
      if ($remote_version_arr[0] < $local_version_arr[0])
      {
        throw new Exception('local_dev');
      }
      
      if ($remote_version_arr[1] > $local_version_arr[1])
      {
        throw new Exception('updateable_warning');
      }
      
      if ($remote_version_arr[1] < $local_version_arr[1])
      {
        throw new Exception('local_dev');
      }
      
      if ($remote_version_arr[2] === $local_version_arr[2])
      {
        throw new Exception('already_updated');
      }
      
      if ($remote_version_arr[2] > $local_version_arr[2])
      {
        throw new Exception('updateable');
      }
      
      if ($remote_version_arr[2] < $local_version_arr[2])
      {
        throw new Exception('local_dev');
      }
      
      throw new Exception('...');
      
    }
    catch (Exception $e)
    {
      $resp['status'] = $e->getMessage();
    }

    
    return $resp;
  }
  
  /**
   * Recursively copies files and forlders from $path to $dest
   * @param string $path  full path to root main path
   * @param string $dest  full path to destination root dir
   * @return boolean
   * @throws Exception
   */
  public function install($path, $dest)
  {
    if( is_dir($path) )
    {
      @mkdir( $dest );
      
      if (!is_dir($dest))
      {
        throw new Exception('Can not create dire: ' . $dest);
      }
      
      $objects = scandir($path);
      
      if( count($objects) > 0 )
      {
        foreach( $objects as $file )
        {
          if( $file == "." || $file == ".." )
              continue;
          // go on
          if( is_dir( $path . '/' . $file ) )
          {
              $this->install( $path . '/' . $file, $dest . '/' . $file );
          }
          else
          {
              @copy( $path . '/' . $file, $dest . '/' . $file );
              if(!file_exists($dest . '/' . $file))
              {
                throw new Exception('Can not copy file: ' . $dest . '/' . $file);
              }
          }
        }
      }
      return true;
    }
    else if ( is_file($path) )
    {
        @copy($path, $dest);
        
        if (!file_exists($dest))
        {
          throw new Exception('Can not copy file: ' . $dest);
        }
        
        return true;
    }
    else
    {
        return false;
    }
    
  }
  
  /**
   * Download ZIP file from  remore to localpath
   * @param string $gitZip  full path to remote ZIP archive
   * @param string $localZipPath  full path to local archive where to save the ZIP file
   * @return boolean
   * @throws Exception
   */
  public function downloadZip($gitZip, $localZipPath)
  {
    $update = @file_get_contents($gitZip);
    
    if (!$update)
    {
      throw new Exception('Can not download zip file: ' . $gitZip);
    }
    
    @file_put_contents($localZipPath, $update);
    
    if (!file_exists($localZipPath))
    {
      throw new Exception('Can not write to temporary file');
    }
    
    return true;
  }
  
  /**
   * Recursively unzips main ZIP archive (http://php.net/manual/en/ref.zip.php)
   * @param string $src_file  full oath to zip archive
   * @param string $dest_dir  relative path to destination dir, or false to unzip in the same dir where Zip archove is found
   * @param boolean $create_zip_name_dir  Indicates if the files will be unpacked in a directory with the name of the zip-file (true) or not (false) (only if the destination directory is set to false!)
   * @param boolean $overwrite Overwrite existing files (true) or not (false)
   * @return boolean Successfull or not
   */
  function unzip($src_file, $dest_dir = false, $create_zip_name_dir = true, $overwrite = true) 
  {
    if ($zip = zip_open($src_file)) 
    {
      if ($zip) 
      {
        $splitter = ($create_zip_name_dir === true) ? "." : "/";
        if ($dest_dir === false) $dest_dir = substr($src_file, 0, strrpos($src_file, $splitter))."/";
        
        // Create the directories to the destination dir if they don't already exist
        @mkdir($dest_dir, 0777, true);
        
        if (!is_dir($dest_dir))
        {
          throw new Exception('Can not create directory: ' . $dest_dir);
        }

        // For every file in the zip-packet
        while ($zip_entry = zip_read($zip)) 
        {
          // Now we're going to create the directories in the destination directories

          // If the file is not in the root dir
          $pos_last_slash = strrpos(zip_entry_name($zip_entry), "/");
          if ($pos_last_slash !== false)
          {
            // Create the directory where the zip-entry should be saved (with a "/" at the end)
            @mkdir($dest_dir.substr(zip_entry_name($zip_entry), 0, $pos_last_slash+1), 0777, true);
            if (!is_dir($dest_dir))
            {
              throw new Exception('Can not create directory: ' . $dest_dir);
            }
          }

          // Open the entry
          if (zip_entry_open($zip,$zip_entry,"r")) 
          {

            // The name of the file to save on the disk
            $file_name = $dest_dir.zip_entry_name($zip_entry);

            // Check if the files should be overwritten or not
            if ($overwrite === true || $overwrite === false && !is_file($file_name))
            {
              // Get the content of the zip entry
              $fstream = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

              @file_put_contents($file_name, $fstream );
              if (!file_exists($file_name))
              {
                throw new Exception('Can not write in file: ' . $file_name);
              }
              // Set the rights
              chmod($file_name, 0777);
            }

            // Close the entry
            zip_entry_close($zip_entry);
          }       
        }
        // Close the zip-file
        zip_close($zip);
      }
    } 
    else
    {
      return false;
    }

    return true;
  }
}
?>
