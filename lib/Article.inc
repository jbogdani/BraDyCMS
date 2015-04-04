<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license			MIT, See LICENSE file
 * @since			Dec 2, 2012
 * @uses			R (rb.php)
 */
 
class Article
{
	
	/**
	 * Valid article SQL statement
	 * @var string
	 */
	private static $valid_art = " ( `status` = 1 AND ( `expires` = '0000-00-00 00:00:00' OR `expires` = '0000-00-00' OR `expires` = `publish` OR `expires` > DATE('now') ) ) ";
	
	
	/**
	 * Return array with all textid used in table articles
	 * @return array
	 */
	public static  function getAllTextid()
	{
		return R::getCol('SELECT `textid` FROM  `' . self::art_tb() . '`');
	}
	
	/**
	 * Compatibilty function. To be removed
	 * @param string $id
	 * @param string $textid
	 * @param string $lang
	 * @param string $admin
	 * @return Ambigous <object, false, boolean, string, string, multitype:, array, multitype:multitype: >|Ambigous <boolean, string, string, multitype:, array, multitype:multitype: >|boolean
	 */
	public static function getArticle($id = false, $textid = false, $lang = false, $admin = false)
	{
		if ($id)
		{
			return self::getById($id, $lang);
		}
		
		if ($textid)
		{
			return self::getByTextid($textid, $lang);
		}
		
		return false;
	}
	
	
	
	/**
	 * Returns articles table name
	 * @return string
	 */
	private static function art_tb()
	{
		return PREFIX . 'articles';
	}
	
	
	/**
	 * gets bean object or array of bean objects:
	 * 		adds url, full_url, and art_img parameters
	 * 		get translationf for $lang and replaces original text with translated text
	 * 
	 * @param object|array $article
	 * @param string $lang
	 * @param boolean $return_bean if true beans will be converted to arrays
	 * @return bean object or array of bean objects
	 */
	private static function parseArt($article, $lang = false, $return_bean = false)
	{
    if (is_array($article))
		{
			foreach ($article as &$art)
			{
				$art = self::parseArt($art, $lang, $return_bean);
			}
			return $article;
		}
		else if (is_object($article))
		{
			// get Translations
			if ($lang)
			{
				$raw_translation = $article->withCondition(' lang = ?', array($lang))->ownArttrans;
				
				$trans_values = array_values($raw_translation);
				$translation = array_shift($trans_values);
				
				if ($translation)
				{
					$translation->title		? $article->title		= $translation->title	: '';
					$translation->summary	? $article->summary	= $translation->summary	: '';
					$translation->text		? $article->text		= $translation->text		: '';
					$translation->keywords	? $article->keywords	= $translation->keywords	: '';
          
          $custom_flds = cfg::get('custom_fields');
          if (is_array($custom_flds))
          {
            foreach ($custom_flds as $fld)
            {
              if ($fld['translate'])
              {
                if ($translation->$fld['translate'])
                {
                  $article->$fld['translate']	= $translation->$fld['translate'];
                }
              }
            }
          }
				}
			}
			
			// add URL
			$article->url = link::to_article($article->textid, $lang);
			
			// add FULL URL
			$article->full_url = utils::getBaseUrl() . str_replace('./', null, $article->url);
			
			// add article image link
			$image_steps = cfg::get('art_img');
			if (is_array($image_steps))
			{
				foreach ($image_steps as $x => $step)
				{
          if (file_exists('./sites/default/images/articles/orig/' . $article->id . '.jpg'))
					{
						$art_img['orig'] = utils::getBaseUrl() . 'sites/default/images/articles/orig/' . $article->id . '.jpg';
						$art_img[0] = utils::getBaseUrl() . 'sites/default/images/articles/orig/' . $article->id . '.jpg';
					}
          
					if (file_exists('./sites/default/images/articles/' . $step . '/' . $article->id . '.jpg'))
					{
						$art_img[$step] = utils::getBaseUrl() . 'sites/default/images/articles/' . $step . '/' . $article->id . '.jpg';
						$art_img[$x+1] = utils::getBaseUrl() . 'sites/default/images/articles/' . $step . '/' . $article->id . '.jpg';
					}
				}
			}
      
      //add article media link
      $media_dir = 'sites/default/images/articles/media/' . $article->id;
      if (file_exists($media_dir))
      {
        $files = utils::dirContent($media_dir);
        
        if (is_array($files))
        {
          $article->media = array();
          foreach($files as $file)
          {
            //$article->media = 'ciao';
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $media[$ext][] = utils::getBaseUrl() . $media_dir . '/' . $file;
          }
        }
      }
      
      // export to array
			if (!$return_bean)
			{
				$art_array = $article->export();
        
        // get tags
        $tags = R::tag($article);
			
				//add tags
				$art_array['tags'] = $tags;
        
        //add art_img
				$art_img ? $art_array['art_img'] = $art_img : '';
        
        //add media
        $media ? $art_array['media'] = $media : '';
			}
			else
			{
				$art_array = $article;
			}
			
			return $art_array;
		}
	}
	
	
	/**
	 * Returns list of all articles, with href value. Articles will be translated if lang is defined.
	 * @param string $lang
	 * @return array
	 */
	public static function getAll($sql = false, $lang = false)
	{
		$articles = R::findAll(self::art_tb(), $sql);
		
		return self::parseArt($articles, $lang);
	}
	
	
	/**
	 * Returns list of all valid articles, with href value. Articles will be translated if lang is defined.
	 * @param string $lang
   * @param boolean $dontparse if true articles will not be parsed
	 * @return array
	 */
	public static function getAllValid($lang = false, $dontparse = false, $limit = false)
	{
		$articles = R::find(
      self::art_tb(), 
      ' ' . self::$valid_art . ' ORDER BY `sort` DESC ' . ($limit ? ' LIMIT ' . $limit : '')
      );
		
    if ($dontparse)
    {
      return $articles;
    }
    else
    {
      return self::parseArt($articles, $lang);
    }
	}
	
  /**
   * Returns total number of valid articles
   * @return int
   */
  public static function countAllValid()
  {
    $res = R::getCol( 'SELECT count(*) FROM ' . self::art_tb() . ' WHERE ' . self::$valid_art);
    return $res[0];
  }
  
	/**
	 * Returns array of available tags
	 * @return array
	 */
	public static function getTags()
	{
		return Tag::getAllTitles();
	}
	
	/**
	 * Deletes tag from tags table; does not delete reference from articles_tag table!
	 * @param string $tag tag tite to delete
	 * @return type
	 */
	public static function deleteTag($tag)
	{
		return R::exec('DELETE FROM `' . PREFIX . 'tag` WHERE title = ?', array($tag));
	}
	
	/**
	 * Deletes from database all tags that are not linked to articles
	 * @return type
	 */
	public static function deleteUnusedTags()
	{
    $one = R::exec('DELETE FROM `' . PREFIX . 'articles_tag` WHERE ' .
      '`articles_id` NOT IN( SELECT `id` FROM `' . PREFIX . 'articles`)');
    
		$two = R::exec('DELETE FROM `' . PREFIX . 'tag` WHERE ' .
      '`id` NOT IN( SELECT `tag_id` FROM `' . PREFIX . 'articles_tag`)');
    
    return ($one || $two);
	}
	
	
	/**
	 * Return article bean
	 * @param int $id		article id
	 * @param string $lang language
	 * @param boolean $dontparse if true original bean will be returned
	 * @return boolean
	 */
	public static function getById($id, $lang = false, $dontparse = false)
	{
		$article = R::load(self::art_tb(), $id);
		
		if (!$article->id)
		{
			return false;
		}
		return $dontparse ? $article : self::parseArt($article, $lang);
	}
	
	/**
	 * Adds translation for certain article
	 * @param int $art_id	articles.id
	 * @param array $data	array of translation data
	 */
	public static function translate($art_id, $data)
	{
		if ($data['id'])
		{
			$id = $data['id'];
			unset($data['id']);
		}
		
		$article = R::load(self::art_tb(), $art_id);
		
		$transl = R::dispense(PREFIX . 'arttrans');
		
		$transl->import($data);
		
		$article->ownArttrans[$id] = $transl;
		
		R::store($article);
			
	}
	
	
	/**
	 * Returns One article beam by textid 
	 * @param string $textid
	 * @param string $lang
	 * @param boolean $admin
	 * @return beam object or false
	 */
	public static function getByTextid($textid, $lang = false, $admin = false)
	{
		$article = R::findOne(self::art_tb(), ' textid = ? ' . ($admin ? '' : ' AND ' . self::$valid_art) . ' ', array($textid));

		if (!$article->id)
		{
			return false;
		}
		
		return self::parseArt($article, $lang);
	}
  
  /**
   * Returns array of articles matching query parameters contained in $filter of false if nothing is matched
   * @param array $filter (Multidimensional) array with query parameters:
   *      [field, value, operator] or
   *      [[field, value, operator], [field, value, operator], ...]
   * @param string $lang  Current language
   * @param string|false $connector Logical connector to use for query parts; Default AND
   * @param integer $start Start id for LIMIT statement
   * @param integer $max Max number of records for LIMIT statement
   * @param boolean $returnCount If true te total records will be returned   * @return array|false array of articles of false
   * @return mixed
   */
  public static function getByFilter($filter, $lang, $connector = 'AND', $start = false, $max = false, $returnCount = false)
  {
    if (!is_array($filter))
    {
      return false;
    }
    
    if (is_string($filter[0]))
    {
      $filter = array($filter);
    }
    
    $sql_parts = array();
    $values = array();
    
    foreach($filter as $el)
    {
      $fld = $el[0];
      $val = $el[1];
      $op = $el[2] ? $el[2] : '=';
      
      array_push($sql_parts, " `{$fld}` {$op} :{$fld} ");
      
      $values[':' . $fld] = $val;
    }
    
    $sql = implode(' ' . $connector . ' ', $sql_parts);
    
    if ($returnCount)
    {
      return R::count( self::art_tb(), $sql, $values );
    }
    
    if ($start !== false && $max)
    {
      $sql .= " ORDER BY `articles`.publish DESC "
      . " LIMIT " . (int)$start . ", " . (int)$max;
    }
    
    
    $articles = R::find(
      self::art_tb(),
      $sql,
      $values
		);
		
		if (!is_array($articles))
		{
			return false;
		}
		
		return self::parseArt($articles, $lang);
  }
	
	
	/**
	 * Return articles array of beams filtered by tags array
	 * @param array $tags
	 * @param string $lang
	 * @param boolean $lax if false, default option, articles must have all tags in tags array
   * @param boolean $admin  if true all articles will be returned else onvly valid (published) articles
   * @param integer $start Start id for LIMIT statement
   * @param integer $max Max number of records for LIMIT statement
   * @param boolean $returnCount If true te total records will be returned
	 * @return array of beams objects or false
	 */
	public static function getByTag($tags, $lang = false, $lax = false, $admin = false, $start = false, $max = false, $returnCount = false)
	{
    $sql = "SELECT `articles`." . ($returnCount ? 'id' : '*') . " FROM `articles` "
        . "INNER JOIN `articles_tag` ON `articles_id` = `articles`.`id` "
        . "INNER JOIN `tag` ON `articles_tag`.`tag_id` = `tag`.`id` "
        . "WHERE `tag`.`title` IN ('" . implode("', '", $tags). "') ";
    if (!$admin)
    {
      $sql .= " AND `articles`.status = 1 AND ("
        . "`articles`.expires = '0000-00-00 00:00:00' OR "
        . "`articles`.expires = '0000-00-00' OR "
        . "`articles`.expires = '0000-00-00' OR "
        . "`articles`.expires = `articles`.publish OR "
        . "`articles`.expires > '" . date("Y-m-d H:i:s") . "' "
        . ") ";
    }
    $sql .= "GROUP BY `articles`.`id` "
        . "HAVING count(`articles`.`id`) >= " . ($lax ? 1 : count($tags));
    
    if ($returnCount)
    {
      return count(R::getAll($sql));
    }
    
    $values = array();
    
    if (!$admin && $start !== false && $max)
    {
      $sql .= " ORDER BY `articles`.sort DESC, `articles`.publish DESC";
    }
    
    if ($start !== false && $max)
    {
      $sql .= ' LIMIT ?, ?';
      array_push($values, (int)$start, (int)$max);
    }
    
    $articles = R::getAll($sql, $values);
    
    foreach($articles as &$art)
    {
      $article = R::dispense(self::art_tb());
      $article->import($art);
      $art = $article;
    }
    
		if (!is_array($articles) || empty($articles))
		{
			return false;
		}

		$art_list = self::parseArt($articles, $lang);
    if ($start !== false && $max)
    {
      return $art_list;
    }
		
		usort($art_list, function($a, $b){
      if ($a['sort'] && $b['sort'])
      {
        if ($a['sort'] == $b['sort'])
        {
          return 0;
        }
        return ($a['sort'] > $b['sort']) ? -1 : 1;
      }
      else if ($a['created'] && $b['created'])
      {
        if ($a['created'] == $b['created'])
        {
          return 0;
        }
        return (strtotime($a['created']) > strtotime($b['created'])) ? -1 : 1;
      }
      else
      {
        if ($a['id'] == $b['id'])
        {
          return 0;
        }
        return ($a['id'] > $b['id']) ? -1 : 1;
      }
        
		});
    
    return $art_list;
	}
	
	
  /**
   * Returns array of beams matching string in title, summary, text or keywords
   * @param string $string String to search for
   * @param string $lang Two-character langiage code
   * @param boolean $dontseparate If true the whole string will be searched, otherwize the single words will be splitted using spaces and searched separately, using AND operator
   * @param integer $start Start id for LIMIT statement
   * @param integer $max Max number of records for LIMIT statement
   * @param boolean $returnCount If true te total records will be returned
   * @return boolean
   */
  public static function search($string, $lang = false, $dontseparate = false, $start = false, $max = false, $returnCount = false)
	{
    $searcheableFields = array('title', 'textid', 'author', 'summary', 'text', 'keywords');
  
    if ($dontseparate)
    {
      $string_arr[] = $string;
    } else
    {
      $string_arr = utils::csv_explode($string, " ");
    }
    
    if (!is_array($string_arr) || empty($string_arr))
    {
      return false;
    }
    
    $sql_part = false;
    $values = array();
    
    foreach ($searcheableFields as $fld)
    {
      $sql_micro_part = false;
      
      foreach ($string_arr as $str)
      {
        $sql_micro_part[] = "`" . $fld . "` LIKE ?";
        array_push($values, "%" . $str . "%");
      }
      $sql_part[] = ' (' . implode(' AND ', $sql_micro_part) . ') ';
    }
    
    $sql = implode(' OR ', $sql_part);
    
    if ($returnCount)
    {
      return R::count(self::art_tb(), $sql, $values);
    }
    
    if ($start !== false && $max)
    {
      $sql .= " LIMIT ?, ?";
      array_push($values, (int)$start, (int)$max);
    }
    
		$articles = R::find(
				self::art_tb(),
      $sql,
      $values
		);
		
		if (!is_array($articles))
		{
			return false;
		}
		
		return self::parseArt($articles, $lang);
		
	}
	
	
	/**
	 * Updates or inserts a new Article
	 * @param int $id
	 * @param array $data
	 * @return boolean
	 */
	public static function save($id = false, $data)
	{
    if(is_string($data['tags']))
    {
      $tags = utils::csv_explode($data['tags']);
      unset($data['tags']);
    }
    elseif (is_array($data['tags']))
    {
      $tags = $data['tags'];
      unset($data['tags']);
    }
		
		
		$article = $id ? R::load(self::art_tb(), $id) :  R::dispense(self::art_tb());

		$article->import($data);
		
		$article->setAttr('updated', date("Y-m-d H:i:s"));
		
    if ($tags)
    {
      R::tag($article, $tags);
    }
		
		return R::store($article);
	}
	
	
	/**
	 * Deletes article and all translations
	 * @param int $id article.id
	 */
	public static function delete($id)
	{
		$article = R::load(self::art_tb(), $id);
		
		if ($article->id)
		{
			// TODO check if necessary
			unset($article->ownArttrans);
			R::store($article);
			R::trash($article);
		}
	}
	
	/**
	 * Returns array of parsed beans (or false) with articles similar to $textid
	 * @param array $tags array of tags to check for similar
	 * @param string $textid articles text id not to be returned. if $tags is false, then the tags of $textid will be used
	 * @param string $lang language
   * @param int $max maximum number of array elements to return
	 * @return array|false
	 */
	public static function getSimilar($tags = false, $textid = false, $lang = false, $max = false)
	{
		if (!$tags && $textid)
		{
      $art = self::getByTextid($textid);
      $tags = $art['tags'];
		}
		
		
		if (!$tags)
		{
			return false;
		}
		
		
		$list = R::tagged(self::art_tb(), $tags);
    
    $list = self::parseArt($list);
    
		if (!$list)
		{
			return false;
		}

		foreach ($list as $article)
		{
			if ($textid && $article['textid'] !== $textid && $article['status'] == 1 )
			{
				$new[count(array_intersect($tags, $article['tags'] ))][] = $article;
			}
			
		}
		
    if (is_array($new))
    {
      asort($new);
		
      $new_values = array_values($new);

      // http://stackoverflow.com/questions/1921421/get-first-element-of-an-array
      $new_values2 = array_shift($new_values); 
      return $max ? array_slice($new_values2, 0, $max) : $new_values2;
    }
		else
    {
      return false;
    }
	}
  
	
	
}
