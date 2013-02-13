<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 15, 2012
 */
 
class Translate
{
	private $db;
	
	
	public function __construct()
	{
		$this->db = new DB();
	}
	
	
	public function get_menu_list($lang)
	{
		$main_tb = PREFIX . '__menu';
		$tr_tb = PREFIX . "__menu_" . $lang;
		
		$query = 'SELECT'
			. " `{$main_tb}`.`id` as `o_id`,"
			. " `{$main_tb}`.`item` as `o_title`,"
			. " `{$tr_tb}`.`id` as `tr_id`,"
			. " `{$tr_tb}`.`item` as `tr_title`,"
			. " `{$tr_tb}`.`translated` as `tr_translated`"
			. " FROM `{$main_tb}` LEFT JOIN `{$tr_tb}` "
			. " ON `{$main_tb}`.`id` = `{$tr_tb}`.`id_menu`"
			. " WHERE 1";
		
		return $this->db->executeQuery($query, false, 'read');
	}
	 
	public function get_article_list($lang)
	{
		$main_tb = PREFIX . '__articles';
		$tr_tb = PREFIX . "__articles_" . $lang;
		
		$query = 'SELECT'
			. " `{$main_tb}`.`id` as `o_id`,"
			. " `{$main_tb}`.`title` as `o_title`,"
			. " `{$tr_tb}`.`id` as `tr_id`,"
			. " `{$tr_tb}`.`title` as `tr_title`,"
			. " `{$tr_tb}`.`translated` as `tr_translated`"
			. " FROM `{$main_tb}` LEFT JOIN `{$tr_tb}` "
			. " ON `{$main_tb}`.`id` = `{$tr_tb}`.`id_art`"
			. " WHERE 1";
		
		return $this->db->executeQuery($query, false, 'read');
	}
	
	public function get_menu_translation($id, $lang)
	{
		$main_tb = PREFIX . '__menu';
		$tr_tb = PREFIX . "__menu_" . $lang;
		
		$query = 'SELECT'
			//articles
			. " `{$main_tb}`.`id` as `o_id`,"
			. " `{$main_tb}`.`item` as `o_item`,"
			. " `{$main_tb}`.`title` as `o_title`,"
			//translations
			. " `{$tr_tb}`.`id` as `tr_id`,"
			. " `{$tr_tb}`.`item` as `tr_item`,"
			. " `{$tr_tb}`.`title` as `tr_title`,"
			. " `{$tr_tb}`.`translated` as `tr_translated`"
			// tb names
			. " FROM `{$main_tb}` LEFT JOIN `{$tr_tb}` "
			// on
			. " ON `{$main_tb}`.`id` = `{$tr_tb}`.`id_menu`"
			// where
			. " WHERE `" . $main_tb . "`.`id` = " . $id;
		
		return $this->db->executeQuery($query, false, 'read');
	}
	
	public function get_article_translation($id, $lang)
	{
		$main_tb = PREFIX . '__articles';
		$tr_tb = PREFIX . "__articles_" . $lang;
		
		$query = 'SELECT'
			//articles
			. " `{$main_tb}`.`id` as `o_id`,"
			. " `{$main_tb}`.`title` as `o_title`,"
			. " `{$main_tb}`.`summary` as `o_summary`,"
			. " `{$main_tb}`.`text` as `o_text`,"
			. " `{$main_tb}`.`keywords` as `o_keywords`,"
			//translations
			. " `{$tr_tb}`.`id` as `t_id`,"
			. " `{$tr_tb}`.`translated` as `t_translated`,"
			. " `{$tr_tb}`.`title` as `t_title`,"
			. " `{$tr_tb}`.`summary` as `t_summary`,"
			. " `{$tr_tb}`.`text` as `t_text`,"
			. " `{$tr_tb}`.`keywords` as `t_keywords`"
			// tb names
			. " FROM `{$main_tb}` LEFT JOIN `{$tr_tb}` "
			// on
			. " ON `{$main_tb}`.`id` = `{$tr_tb}`.`id_art`"
			// where
			. " WHERE `" . $main_tb . "`.`id` = " . $id;
		
		return $this->db->executeQuery($query, false, 'read');
	}
	
	
	public function save_translation($context, $lang, $o_id, $post)
	{
		if ($context == 'menu')
		{
			$ref = 'id_menu';
			$tb = PREFIX . '__menu_' . $lang;
		}
		else if ($context == 'article')
		{
			$ref = 'id_art';
			$tb = PREFIX . '__articles_' . $lang;
		}
		
		$val[':' . $ref] = $o_id;
		
		// save can be update or insert:
		
		// update
		if ($post['id'])
		{
			foreach ($post as $k=>$v)
			{
				$tmp[] = " `{$k}` = :{$k} ";
				$val[':' . $k] = $v;
			}
				
			$tmp[] = " `{$ref}` = :{$ref} ";
				
			$query = "UPDATE `" . $tb . "` SET "
			. implode(", ", $tmp)
			. "WHERE `id` = " . $post['id'];
		}
		// insert
		else
		{
			$apices[] = " `{$ref}` ";
				
			foreach ($post as $k=>$v)
			{
				$apices[] = " `{$k}` ";
				$val[':' . $k] = $v;
			}
				
			$query = "INSERT INTO `" . $tb . "` "
			. "(" . implode(',', $apices). ")"
			. " VALUES "
			. " (" . implode(',', array_keys($val)). ") ";
			$res = 'id';
		}
		return $this->db->executeQuery($query, $val, $res);
	}
}