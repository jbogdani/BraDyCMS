<?php

/**
 * Manages tags
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net, Julian Bogdani <jbogdani@gmail.com>
 * @license			See file LICENSE distributed with this code
 * @since				Jan 2, 2015
 */

class Tag
{
  /**
   * Returns list of available tag titles
   * @return array
   */
  public static function getAllTitles()
  {
    return R::getCol('SELECT `title` FROM `' . PREFIX. 'tag` ORDER BY `title` ASC');
  }
    
  /**
   * Returns complete array of tags (id + title)
   * @return array|false
   */
  public static function getAll()
  {
    return R::getAll('SELECT * FROM `' . PREFIX. 'tag` ORDER BY `title` ASC');
  }
  
  /**
   * Adds new tag
   * @param sting $title
   * @return int|false
   */
  public static function add($title)
  {
    return R::exec( 'INSERT INTO `' . PREFIX . 'tag` (`title`) VALUES (?)', array($title) );
  }
  
  /**
   * Deletes a tag
   * @param int $tagId Tag id
   * @return boolean
   */
  public static function delete($tagId)
  {
    return (R::exec( 'DELETE FROM `' . PREFIX . 'tag` WHERE `id` = ?', array($tagId) ) ||
      Article::deleteUnusedTags());
  }
  
  /**
   * Rename a tag's title
   * @param string $title New title
   * @param int $tagId Tag id to rename
   * @return int|false
   */
  public static function rename($title, $tagId)
  {
    return R::exec( 'UPDATE `' . PREFIX . 'tag` SET `title`= ? WHERE id = ?', array($title, $tagId) );
  }
  
}

?>
