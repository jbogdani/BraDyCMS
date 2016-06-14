<?php
/**
 * Performs CRUD operatins on Seo table. The field url will be considered main key field
 * Structure of seo table:
 * 	- id
 * 	- url (unique)
 * 	- title
 * 	- description
 * 	- keywords
 * 	The class requires php >= 5.4 because of the use of the short array syntax
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2016
 * @license      MIT, See LICENSE file
 * @since      April 10, 2016
 * @uses      Redbean
 */



class Seo
{
  /**
   * Edits existing record (url) or adds new one
   * @param  int $id  id to edit
   * @param  array $data array of metadata
   * @return boolean       true on success, false on failure. Syntax errors will be logged
   */
  public static function edit($id = false, $data)
  {
    try
    {
      if ($id)
      {
        $seo = R::findOne('seo', 'id = ?', [$id]);
      }
      else
      {
        $seo = R::dispense('seo');
      }

      $seo->url = $data['url'];
      $seo->title = $data['title'];
      $seo->description = $data['description'];
      $seo->keywords = $data['keywords'];

      R::store($seo);
      return true;
    }
    catch(RedException\SQL $e)
    {
      error_log($e->getMessage());
      return false;
    }
    catch(Exception $e)
    {
      error_log($e->getMessage());
      return false;
    }
  }

  /**
   * Deletes metadata for existing record (url)
   * @param  int $id  Id to edit
   * @return boolean       true on success, false on failure. Syntax errors will be logged
   */
  public static function delete($id)
  {
    try
    {
      $seo = R::findOne('seo', 'id = ?', [$id]);
      if (!$seo)
      {
        return true;
      }
      R::trash($seo);
      return true;
    }
    catch(RedException\SQL $e)
    {
      error_log($e->getMessage);
      return false;
    }

  }

  /**
   * Returns array of data for existing record (url)
   * @param  string $url  url to edit
   * @return array|false       array of data, false on failure. Syntax errors will be logged
   */
  public static function get($url)
  {
    try
    {
      $seo = R::findOne('seo', 'url = ?', [$url]);
      if(!$seo)
      {
        return false;
      }
      return $seo->export();
    }
    catch(RedException\SQL $e)
    {
      error_log($e->getMessage);
      return false;
    }
  }

  /**
   * Returns array of data for existing record (url)
   * @param  int $id  id to edit
   * @return array|false       array of data, false on failure. Syntax errors will be logged
   */
  public static function getById($id)
  {
    try
    {
      $seo = R::findOne('seo', 'id = ?', [$id]);
      if(!$seo)
      {
        return false;
      }
      return $seo->export();
    }
    catch(RedException\SQL $e)
    {
      error_log($e->getMessage);
      return false;
    }
  }
}
?>
