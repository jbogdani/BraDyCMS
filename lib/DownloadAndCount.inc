<?php

class DownloadAndCount
{

/**
 * Creates table downloads in the database if it does not exist
 */
  private static function createIfDoesNotExist()
  {
    R::exec('CREATE IF NOT EXISTS TABLE downloads (id INTEGER PRIMARY KEY, file TEXT UNIQUE, tot INTEGER);');
  }

  /**
   * Returns valid path to existing file or false if path could not be found
   * @param  string $file relative or absolute filename
   * @return string|false absolute path to  existing file or false
   */
  private function resolvePath($file)
  {
    $full_file = false;
    $pre_paths = [
      '',
      'sites/default/',
      'sites/default/images/',
      'sites/default/images/downloads/',
      'sites/default/images/articles/',
      'sites/default/images/articles/media/'
    ];

    foreach($pre_paths as $p)
    {
      if (file_exists($p . $file))
      {
        $full_file = $p . $file;
        break;
      }
    }
    return $full_file;
  }

  /**
   * Gets total number of downloads for file
   * @param  string $file filename
   * @return int       total number of downloads
   */
  public static function getCount($file)
  {
    self::createIfDoesNotExist();
    $full_file = self::resolvePath($file);
    if (!$full_file)
    {
      return 0;
    }
    $tot =  R::getCell('SELECT tot FROM downloads WHERE file = ?', [$full_file]);
    return $tot ? (int)$tot : 0;
  }

  /**
   * Adds one hit to the download count for file
   * @param string $file filename
   */
  public static function addOne($file)
  {
    self::createIfDoesNotExist();
    // http://stackoverflow.com/a/2718352/586449
    R::exec('INSERT OR IGNORE INTO downloads (`file`, `tot`) VALUES (?, ?)', [$file, 0]);
    R::exec('UPDATE downloads SET tot = tot + 1 WHERE `file` = ?', [$file]);
  }

  /**
   * Forces download of $file and adds a Heritage
   * If file does not exist all hits will be deleted and an Exception will be thrown
   * @param  string $file filename
   */
  public static function file($file)
  {
    self::createIfDoesNotExist();
    $full_file = self::resolvePath($file);

    if(!$full_file)
    {
      // Reset count
      R::exec('DELETE FROM downloads WHERE `file` = ?', [$full_file]);
      throw new Exception("File $file does not exist");
    }
    self::addOne($full_file);
    echo $full_file;


    $mime = mime_content_type($full_file);

    header("Content-Transfer-Encoding: binary");
    header('Content-type: ' . $mime);
    header('Content-Length: ' . filesize($full_file));
    header("Content-disposition: attachment; filename=" . pathinfo($file, PATHINFO_BASENAME));
    ob_clean();
    flush();
    readfile($full_file);
    exit(0);
  }

  /**
   * Forces download text as plain text file
   * @param  string $text file content (plain text)
   * @param  string $name file name
   */
  public static function text($text, $name)
  {
    self::createIfDoesNotExist();
    self::addOne($name);
    header("Content-Transfer-Encoding: binary");
    header('Content-type: text/plain');
    header("Content-disposition: attachment; filename=" . $name);
    ob_clean();
    flush();
    echo $text;
    exit(0);

  }

  /**
   * Deletes filename from downloads count table (resets count)
   * @param int $id Row id to delete
   */
  public static function resetCount($id)
  {
    R::exec('DELETE FROM `downloads` WHERE `id` = ?', [$id]);
  }
}
?>
