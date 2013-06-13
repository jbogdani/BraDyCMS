<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 2, 2012
 */
 
class Article
{
	
	private $db;
	public $flds;
	
	/**
	 * Starts DB object
	 */
	public function __construct(DB $db)
	{
		$this->db = $db;
	}
	
	private function previewFlds()
	{
		if (!$this->flds)
		{
			$this->flds = '`id`, `title`, `sort`, `text_id`, `author`, `summary`, `section`, `tags`';
		}
		return $this->flds;
		
		//return '`id`, `title`, `sort`, `text_id`, `author`, `summary`, `section`, `tags`';
	}
	
	/**
	 * Returns SQl part for valid (user-read) article
	 * @return string
	 */
	private function valid_art_stat()
	{
		return " ( `status` = 1 AND ( `expires` = '0000-00-00 00:00:00' OR `expires` = '0000-00-00' OR `expires` = `publish_on` OR `expires` > DATE('now') ) ) ";
	}
	
	/**
	 * Returna array of user sections
	 * @return array
	 */
	public function getSections()
	{
		$query = "SELECT `section` FROM `" . PREFIX . "__articles` WHERE 1 GROUP BY ( `section` ) ORDER BY `section`";
		
		$arr = $this->db->executeQuery($query, false, 'read');
		
		if (is_array($arr))
		{
			foreach ( $arr as $res )
			{
				$final[] = $res['section'];
			}
		}
		
		return $final;
	}
	
	public function getTags()
	{
		$query = "SELECT `tags` FROM `" . PREFIX . "__articles` WHERE 1";
		
		$arr = $this->db->executeQuery($query, false, 'read');
		
		$final = array();
		if (is_array($arr))
		{
			foreach ( $arr as $res )
			{
				$tags = utils::csv_explode($res['tags']);
				$final = array_merge($final, (is_array($tags) ? $tags : array($tags)));
			}
		}
		return array_unique($final);
	}
	
	/**
	 * 
	 * Updates existing article using $data array
	 * @param int $id
	 * @param array $data
	 */
	public function update($id, $data)
	{
		foreach ( $data as $fld => $val )
		{
			$stat[] = "`{$fld}` = :{$fld}";
		
			$values[':' . $fld] = $val;
		}
		$sql = "UPDATE `" . PREFIX . "__articles` SET "
				. implode(', ', $stat)
				. ", `updated` = date('now')"
				. " WHERE `id`	= :id";
		
		
		$values[':id'] = $id;
		return $this->db->executeQuery( $sql, $values );
	}
	/**
	 * 
	 * Inserts new article using $data array
	 * @param array $data
	 */
	public function add ($data)
	{
		foreach ( $data as $fld=>$val )
		{
			$apices[] = '`' . $fld . '`';
			$values[':' . $fld] = $val;
		}
		
		
		$statement = "INSERT INTO `" . PREFIX . "__articles` "
				. "(" . implode(',', $apices). ")"
				. " VALUES "
				. "(" . implode ( ',', array_keys ( $values ) ) . ")";
		
		return $this->db->executeQuery($statement, $values, 'id' );
		
	}
	
	/**
	 * 
	 * Deletes article
	 * @param int $id
	 * @return boolean
	 */
	public function delete($id)
	{
		$sys_langs = cfg::get('languages');
		
		$statement = "DELETE FROM `" . PREFIX . "__articles` WHERE `id` = " . $id;
		$ret = $this->db->executeQuery($statement);
		
		if (is_array($sys_langs))
		{
			foreach ($sys_langs as $lang)
			{
				$this->db->executeQuery("DELETE FROM `" . PREFIX . "__articles_" . $lang['id'] . "` WHERE `id_art` = " . $id);
			}
		}
			
		return $ret;
	}
	
	/**
	 * Returns array containg all articles
	 */
	public function getAll($lang = false)
	{
		$query = "SELECT * FROM `" . PREFIX . "__articles` WHERE 1 ORDER BY `section` ASC, `sort` DESC";
		
		$res = $this->db->executeQuery($query, false, 'read');
		
		$res = $this->addHref2List($res, $lang);
		
		return $lang ? $this->translateList($res, $lang) : $res;
	}
	
	/**
	 * Returns array containg all articles
	 */
	public function getAllValid($lang = false)
	{
		$query = "SELECT * FROM `" . PREFIX . "__articles` WHERE " . $this->valid_art_stat() . " ORDER BY `section` ASC, `sort` DESC";
	
		$res = $this->db->executeQuery($query, false, 'read');
	
		$res = $this->addHref2List($res, $lang);
	
		return $lang ? $this->translateList($res, $lang) : $res;
	}
	
	/**
	 * Returns array witha rticle data
	 * @param int $art_id
	 * @param string $art_text_id
	 * @param string $lang
	 * @param string $admin
	 * @throws Exception
	 * @return boolean|array
	 */
	public function getArticle ( $art_id = false , $art_text_id = false, $lang = false, $admin = false )
	{
		
		if ( !$art_id AND !$art_text_id )
		{
			throw new Exception('No article is defined');
		}
	
		$query = 'SELECT * FROM `' . PREFIX . '__articles` WHERE '
	
			. ( $art_id ? '`id` = '. $art_id : '`text_id` =\'' .$art_text_id . "'")
			
			. ($admin ? '' : ' AND ' . $this->valid_art_stat() )
			
			. ' LIMIT 0, 1';
	
		$res = $this->db->executeQuery($query, false, 'read');
		
		$res = $this->addHref2List($res, $lang);
		
		if (count($res) == 0)
		{
			return false;
		}
		
		return $lang ? $this->translateList($res[0], $lang) : $res[0];

	}
	
	public function getArticlesBySection ($section, $lang = false)
	{
		$query = 'SELECT ' . $this->previewFlds()
			. ' FROM `' . PREFIX . '__articles` '
			. " WHERE `section` = '" . $section . "' "
			. ' AND ' . $this->valid_art_stat()
			. ' ORDER BY `sort` DESC';
	
		$res = $this->db->executeQuery($query, false, 'read');
		
		$head = $this->getArticle(false, $section, $lang);
		
		$list = $lang ? $this->translateList($res, $lang) : $res;
		
		$list = $this->addHref2List($list, $lang);
		
		return array('list' => $list, 'head' => $head);
		
	}
	
	public function getArticlesBySectionAndTag($section, $tags, $lang = false)
	{
		foreach ($tags as $tag)
		{
			$part[] = " `tags` LIKE '%{$tag}%' ";
		}
		
		$query = 'SELECT ' . $this->previewFlds() .
			' FROM `' . PREFIX . '__articles` ' .
			" WHERE `section` = '" . $section . "' " .
			' AND (' . implode(' AND ', $part) . ')' .
			' AND ' . $this->valid_art_stat() .
			' ORDER BY `sort` DESC';
		
		$res = $this->db->executeQuery($query, false, 'read');
		
		$res = $this->addHref2List($res, $lang);
		
		return $lang ? $this->translateList($res, $lang) : $res;
	}
	
	/**
	 * 
	 * @param array $tags
	 * @param string $lang
	 * @param boolean $lax if true articles must share all articles!
	 * @return Ambigous <boolean, multitype:, array, multitype:multitype: , unknown>
	 */
	public function getArticlesByTag($tags, $lang = false, $lax = false)
	{
		if (!is_array($tags))
		{
			return false;
		}
		
		foreach ($tags as $tag)
		{
			$part[] = " `tags` LIKE '%{$tag}%' ";
		}
		
		$query = 'SELECT ' . $this->previewFlds()
				. ' FROM `' . PREFIX . '__articles` '
				. " WHERE "
				. ' (' . implode(' ' . ($lax ? 'OR' : 'AND') . ' ', $part) . ')'
				. ' AND ' . $this->valid_art_stat()
				. ' ORDER BY `sort` DESC';
		
		$res = $this->db->executeQuery($query, false, 'read');
		
		$res = $this->addHref2List($res, $lang);
		
		return $lang ? $this->translateList($res, $lang) : $res;
	}
	
	
	public function getTagSiblings($tags, $text_id = false, $lang = false)
	{
		
		$res = $this->getArticlesByTag($tags, $lang, true);
		
		foreach ($res as $r)
		{
			if ($text_id && $r['text_id'] != $text_id)
			{
				$new[count(array_intersect($tags, utils::csv_explode($r['tags'])))][] = $r;
			}
		}
		asort($new);
		
		// http://stackoverflow.com/questions/1921421/get-first-element-of-an-array
		return array_shift(array_values($new));
	}
	
	
	public function getSiblings($text_id, $lang = false)
	{
		$query = 'SELECT ' . $this->previewFlds()
			. ' FROM `' . PREFIX . '__articles` WHERE '
			. " `section` = (SELECT `section` FROM `" . PREFIX . "__articles` WHERE `text_id` = '" . $text_id . "')"
			. " AND `text_id` != '" . $text_id . "'"
			. ' AND ' . $this->valid_art_stat()
			. ' ORDER BY `sort` DESC';
		
		$res = $this->db->executeQuery($query, false, 'read');
		
		$res = $this->addHref2List($res, $lang);
		
		return $lang ? $this->translateList($res, $lang) : $res;
	}
	
	public function search($string, $lang = false)
	{
		$statement = "SELECT " . $this->previewFlds() . " FROM `" . PREFIX . "__articles` WHERE "
				. " 	`title`		LIKE '%" . $string . "%' "
				. " OR	`summary`	LIKE '%" . $string . "%' "
				. " OR	`text`		LIKE '%" . $string . "%' "
				. " OR	`keywords`	LIKE '%" . $string . "%' "
				. " OR	`tags`		LIKE '%" . $string . "%'";
		
		$res = $this->db->executeQuery($statement, false, 'read');
		
		$res = $this->addHref2List($res, $lang);
		
		return $lang ? $this->translateList($res, $lang) : $res;
	}
	
	private function addHref2List($art_list = false, $lang = false)
	{
		if (is_array($art_list))
		{
			foreach ($art_list as &$art)
			{
				$art['url'] = link::to_article($art['text_id'], $lang);
				$art['full_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . str_replace('./', null, $art['url']);
			}
		}
		
		return $art_list;
	}
	
	/**
	 * 
	 * Translates article or article list (1/2D article array)
	 * 
	 * @param array $art_array		original articles array (1 or 2D)
	 * @param string $lang			translation language
	 * @return boolean|array		translated articles array (1 or 2D)
	 */
	public function translateList($art_array, $lang)
	{
		if(!is_array($art_array))
		{
			return $art_array;
		}
		if (!is_array($art_array[0]) && $art_array['id'])
		{
			$art_array = array($art_array);
		}
		
		foreach($art_array as $art)
		{
			$id_array[] = $art['id']; 
		}
		
		$query = 'SELECT * FROM `' . PREFIX . '__articles_' . $lang . '` WHERE `id_art` IN (' . implode(',', $id_array). ')';
		
		$translated = $this->db->executeQuery($query, false, 'read');
		
		
		if (is_array($translated))
		{
			foreach($art_array as &$art)
			{
				foreach ($translated as $tr_art)
				{
					if ($art['id'] == $tr_art['id_art'])
					{
						if($tr_art['title'] != '')
						{
							$art['title'] = $tr_art['title'];
						}
						
						if($tr_art['summary'] != '')
						{
							$art['summary'] = $tr_art['summary'];
						}
						
						if($tr_art['text'] != '')
						{
							$art['text'] = $tr_art['text'];
						}
						
						if($tr_art['keywords'] != '')
						{
							$art['keywords'] = $tr_art['keywords'];
						}
					}
				}
			}
		}
		
		return (count($art_array) == 1) ? $art_array[0] : $art_array;
	}
	
	
}