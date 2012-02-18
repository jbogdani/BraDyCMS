<?php
/**
 *
 * Manages all read-only actions to article tables (main and translations)
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Oct 30, 2011
 */

class ArticleRead extends Article{


	/**
	 *
	 * Returns SQL portion with valid article check
	 */
	private function valid_art_stat()
	{
		return " ( `status` = 1 AND ( `expires` = '0000-00-00 00:00:00' OR `expires` < DATE('now') ) ) ";
	}


	/**
	 *
	 * Searches string in article tables
	 * @param string $string	string to look for
	 */
	public function search($string) {

		$statement = "SELECT * FROM `" . PREFIX . "__articles` WHERE "
		. " 	`title`		LIKE '%" . $string . "%' "
		. " OR	`summary`	LIKE '%" . $string . "%' "
		. " OR	`text`		LIKE '%" . $string . "%' "
		. " OR	`keywords`	LIKE '%" . $string . "%' ";
		return $this->executeQuery($statement, false, 'read');
	}

	/**
	 *
	 * Returna array of sibling articles for given art id
	 * @param int $id	article id to search siblings for
	 * @param string $lang	language
	 */
	public function getSiblings( $id, $lang = false )
	{
		if (!$lang)
		{
			$statement = 'SELECT '
				. '`id`, '
				. '`title`, '
				. '`text_id`, '
				. '`summary` '
			. ' FROM `' . PREFIX . '__articles` WHERE '
				. ' `id` <> ' . $id 
				. ' AND '
				. ' `section` = (SELECT `section` FROM `' . PREFIX . '__articles` WHERE `id`= ' . $id . ')'
				. ' AND ' . $this->valid_art_stat()
			. ' ORDER BY `sort`';

			return $this->executeQuery($statement, false, 'read');
		}
		else
		{
			$main_tb = PREFIX . '__articles';
			$tr_tb =   PREFIX . '__articles_' . $lang;
			
			$statement = 'SELECT '
				. "`{$main_tb}`.`id` as `id`, "
				. "`{$main_tb}`.`title` as `title`, "
				. "`{$main_tb}`.`text_id` as `text_id`, "
				. "`{$main_tb}`.`summary` as `summary`, "
				
				. "`{$tr_tb}`.`id_art` as `id_art`, "
				. "`{$tr_tb}`.`title` as `tr_title`, "
				. "`{$tr_tb}`.`summary` as `tr_summary` "
				
			. ' FROM '
				. " `{$main_tb}` LEFT JOIN `{$tr_tb}` "
				. " ON `{$main_tb}`.`id` = `{$tr_tb}`.`id_art` "
				
			. ' WHERE '
				. " `{$main_tb}`.`id` <> " . $id 
				. ' AND '
				. ' `section` = (SELECT `section` FROM `' . PREFIX . '__articles` WHERE `' . PREFIX . '__articles`.`id`= ' . $id . ')'
				. ' AND ' . $this->valid_art_stat()
			. ' ORDER BY `sort`';
			$res = $this->executeQuery($statement, false, 'read');
			
			if (count($res) == 0)
			{
				return false;
			}
			else
			{
				foreach($res as &$arr)
				{
					if ($arr['tr_title'])
					{
						$arr['title'] = $arr['tr_title'];
						$arr['tr_title'] = false;
					}
					if ($arr['tr_summary'])
					{
						$arr['summary'] = $arr['tr_summary'];
						$arr['tr_summary'] = false;
					}
				}
			}
			return $res;
		}
	}


	/**
	 * 
	 * Searches article in database using id or text_id and returns array with article content
	 * @param int $art_id	id of article to retrieve
	 * @param string $art_text_id	text_id of article to retrieve
	 * @param string $lang		language
	 * @param boolean $admin flg to controle if I'using this method in admin form
	 */
	public function get_article_by_id ( $art_id = false , $art_text_id = false, $lang = false, $admin = false )
	{
		if ( !$art_id AND !$art_text_id )
		{
			die('No article is defined');
		}

		$query = 'SELECT * FROM `' . PREFIX . '__articles` WHERE '
			
		. ( $art_id ? '`id` = '. $art_id : '`text_id` =\'' .$art_text_id . "'");
		
		if (!$admin)
		{
			$query .=' AND ' . $this->valid_art_stat();
		}
		$query .= " LIMIT 0, 1";
		

			
		$res = $this->executeQuery($query, false, 'read');

		if ( count($res) == 0 )
		{
			return false;
		}
		else
		{
			if ( $lang )
			{
				$query = 'SELECT * FROM `' . PREFIX . '__articles_' . $lang . '` WHERE '
				
				. ' `id_art` = ' . $res[0]['id'] . ' LIMIT 0, 1';

				$res_tr = $this->executeQuery($query, false, 'read');

				$res_tr[0]['title'] ?  $res[0]['title'] = $res_tr[0]['title'] : '';

				$res_tr[0]['summary'] ?  $res[0]['summary'] = $res_tr[0]['summary'] : '';

				$res_tr[0]['text'] ?  $res[0]['text'] = $res_tr[0]['text'] : '';

				$res_tr[0]['keywords'] ?  $res[0]['keywords'] = $res_tr[0]['keywords'] : '';

			}

			return $res[0];
		}
	}


	/**
	 *
	 * Searches in DB for articles of a given section. Returns array with general results + detailed article list
	 * @param string $section	section to search for
	 */
	public function get_articles_by_section ( $section )
	{

		$query = 'SELECT `id`, `title`, `text_id`, `summary` '
		. 'FROM `' . PREFIX . '__articles` '
		. " WHERE `section` = '" . $section . "' ";

		$query .= ' AND ' . $this->valid_art_stat();

		$query .= 'ORDER BY `sort`';

		$res = $this->executeQuery($query, false, 'read');

		if ( count($res) == 0 )
		{
			return false;
		}
		else
		{
			return array('title'=>$section, 'article' => $res);
		}
	}

	/**
	 *
	 * Returns array of used sections
	 */
	public function get_used_sections(){

		$query = "SELECT `section` FROM `" . PREFIX . "__articles` WHERE 1 GROUP BY ( `section` ) ORDER BY `section`";

		$arr = $this->executeQuery($query, false, 'read');

		foreach ( $arr as $res )
		{
			$final[]=$res['section'];
		}

		return $final;
	}

	/**
	 *
	 * Returna array of all articles
	 */
	public function get_all_articles(){

		$query = "SELECT * FROM `" . PREFIX . "__articles` WHERE 1 ORDER BY `section`, `sort`";

		return $this->executeQuery($query, false, 'read');
	}


}