<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Nov 1, 2011
 */

class translate extends DB{

	/**
	 *
	 * Returns a well formatted and context-free array of data for main & translation table
	 * @param string $context	article | menu
	 * @param string $lang		2 digits languale
	 * @throws MyExc
	 */
	public function get_list($context, $lang)
	{
		switch ($context)
		{
			case 'article':
				$main_tb = PREFIX . '__articles';
				$tr_tb = PREFIX . "__articles_" . $lang;

				$statement = 'SELECT'
				. " `{$main_tb}`.`id` as `o_id`,"
				. " `{$main_tb}`.`title` as `o_title`,"
				. " `{$tr_tb}`.`id` as `tr_id`,"
				. " `{$tr_tb}`.`title` as `tr_title`,"
				. " `{$tr_tb}`.`translated` as `tr_translated`"
				. " FROM `{$main_tb}` LEFT JOIN `{$tr_tb}` "
				. " ON `{$main_tb}`.`id` = `{$tr_tb}`.`id_art`"
				. " WHERE 1";
				break;

			case 'menu':
				$main_tb = PREFIX . '__menu';
				$tr_tb = PREFIX . "__menu_" . $lang;

				$statement = 'SELECT'
				. " `{$main_tb}`.`id` as `o_id`,"
				. " `{$main_tb}`.`item` as `o_title`,"
				. " `{$tr_tb}`.`id` as `tr_id`,"
				. " `{$tr_tb}`.`item` as `tr_title`,"
				. " `{$tr_tb}`.`translated` as `tr_translated`"
				. " FROM `{$main_tb}` LEFT JOIN `{$tr_tb}` "
				. " ON `{$main_tb}`.`id` = `{$tr_tb}`.`id_menu`"
				. " WHERE 1";
				break;

			default:
				throw new MyExc('Il contesto non è specificato');
				break;
		}
		return $this->executeQuery($statement, false, 'read');
	}

	/**
	 *
	 * Returns a well formatted  array of data containing main and translation data
	 * @param string $context		aricle | menu
	 * @param int $id		article uid
	 * @param string $lang	language
	 */
	public function get_translation($context, $id, $lang)
	{
		switch ($context)
		{
			case 'article':
				$main_tb = PREFIX . '__articles';
				$tr_tb = PREFIX . "__articles_" . $lang;

				$statement = 'SELECT'
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
				break;

			case 'menu':
				$main_tb = PREFIX . '__menu';
				$tr_tb = PREFIX . "__menu_" . $lang;

				$statement = 'SELECT'
				//articles
				. " `{$main_tb}`.`id` as `o_id`,"
				. " `{$main_tb}`.`item` as `o_item`,"
				. " `{$main_tb}`.`title` as `o_title`,"
				//translations
				. " `{$tr_tb}`.`id` as `t_id`,"
				. " `{$tr_tb}`.`item` as `t_item`,"
				. " `{$tr_tb}`.`title` as `t_title`"
				// tb names
				. " FROM `{$main_tb}` LEFT JOIN `{$tr_tb}` "
				// on
				. " ON `{$main_tb}`.`id` = `{$tr_tb}`.`id_menu`"
				// where
				. " WHERE `" . $main_tb . "`.`id` = " . $id;
				break;
					
			default:
				throw new MyExc('Il contesto ' . $context. ' non è prevvisto');
		}
			
		return $this->executeQuery($statement, false, 'read');
	}



	/**
	 *
	 * Writes POST data in transaltion table using INSERT or UPDATE
	 * @param sting	$context	article | menu
	 * @param string $lang		2 digits language
	 * @param int $id			article uid
	 * @param array $post		post data
	 * @param int $t_id			transletion uid, if available
	 */
	public function save_translation($context, $lang, $id, $post, $t_id = false)
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

		$val[':' . $ref] = $id;
		
		// save can be update or insert:
		
		// update
		if ($t_id)
		{
			foreach ($post as $k=>$v)
			{
				$tmp[] = " `{$k}` = :{$k} ";
				$val[':' . $k] = $v;
			}
				
			$tmp[] = " `{$ref}` = :{$ref} ";
				
			$query = "UPDATE `" . $tb . "` SET "
			. implode(", ", $tmp)
			. "WHERE `id` = " . $t_id;
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
		return $this->executeQuery($query, $val, $res);
	}

}