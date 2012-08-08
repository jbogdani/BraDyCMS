<?php
/**
 *
 * Manages all action to menu tables (main & translation)
 * All methods return query result!
 *
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			26/mag/2011
 */

class Menu extends DB
{
	/**
	 * 
	 * Returns array of menu item by id.
	 * Used in admin module to show / edit a single menu item
	 * @param int $id		menu item UID
	 */
	public function get_menu_by_id($id)
	{
		$query = "SELECT * FROM `" . PREFIX . "__menu` WHERE `id`	= " . $id . " LIMIT 0, 1";

		$res = $this->executeQuery($query, false, 'read');

		if ( count ( $res ) == 0 )
		{
			return false;
		}
		else
		{
			return $res;
		}

	}
	
	/**
	 * 
	 * Returns all menu items, main and subof
	 * @param string $menu_name		menu name
	 */
	public function get_all_menu_items($menu_name)
	{
		$query = "SELECT * FROM `" . PREFIX . "__menu` WHERE `menu` = '{$menu_name}' ORDER BY `sort`";
			
		return $this->executeQuery($query, false, 'read');
	}
	
	
	/**
	 * 
	 * Returns array of items for a menu ($menu) or for an item menu ($item)
	 * @param string $menu		main menu name
	 * @param int $item			main menu item ID
	 * @param string $lang		languiage
	 * @throws MyExc
	 */
	public function get_menu_items($menu, $item = false, $lang = false)
	{
		$where = " WHERE `menu`	= '{$menu}' AND ";
		
		if ($item)
		{
			$where .= " `subof`= {$item} ";
		}
		else 
		{
			$where .= " ( `subof` = '' OR `subof` IS NULL)";
		}
		
		if (!$lang)
		{
			$query = "SELECT * FROM `" . PREFIX . "__menu` {$where} ORDER BY `sort`";
			
			return $this->executeQuery($query, false, 'read');
		}
		else
		{
			$main_tb = PREFIX . '__menu';
			$tr_tb =   PREFIX . '__menu_' . $lang;
			
			$query = 'SELECT '
					. "`{$main_tb}`.`id` as `id`, "
					. "`{$main_tb}`.`item` as `item`, "
					. "`{$main_tb}`.`href` as `href`, "
					. "`{$main_tb}`.`target` as `target`, "
					. "`{$main_tb}`.`title` as `title`, "
					. "`{$main_tb}`.`menu` as `menu`, "
					. "`{$main_tb}`.`sort` as `sort,` ,"

					. "`{$tr_tb}`.`item` as `tr_item`, "
					. "`{$tr_tb}`.`title` as `tr_title` "
					
				. ' FROM '
				. " `{$main_tb}` LEFT JOIN `{$tr_tb}` "
				. " ON `{$main_tb}`.`id` = `{$tr_tb}`.`id_menu` "
				. $where
				. ' ORDER BY `sort`';
				
			$res = $this->executeQuery($query, false, 'read');
			
			if (count($res) == 0)
			{
				return false;
			}
			else
			{
				foreach ($res as &$arr)
				{
					if ($arr['tr_item'])
					{
						$arr['item'] = $arr['tr_item'];
						$arr['tr_item'] = false;
					}
					
					if ($arr['tr_title'])
					{
						$arr['title'] = $arr['tr_title'];
						$arr['tr_title'] = false;
					}
				}
				return $res;
			}
		}
	}
	
	/**
	 *
	 * Returns array with used menu list
	 */
	public function getList()
	{
		$query = "SELECT menu FROM `" . PREFIX . "__menu` WHERE 1 GROUP BY (`menu`) ORDER BY `menu`";

		$res = $this->executeQuery($query, false, 'read');

		if (is_array($res))
		{
			foreach ($res as &$r)
			{
				$t[] = $r['menu'];
			}
			return $t;
		}
		else
		{
			return false;
		}
	}

	/**
	 *
	 * Adds a new menu
	 * @param array $post array of data to write in DB
	 */
	public function add($post)
	{
		if ( !$post['item'] OR !$post['href'] OR !$post['menu'] )
		{
			return false;
		}
		else
		{
				
			$statement = "INSERT INTO `" . PREFIX . "__menu` "
			. " (`item`,	`href`,	`target`,	`title`,	`menu`,	`sort`,	`subof`) "
			. " VALUES "
			. " (:item,		:href,	:target,	:title,		:menu,	:sort,	:subof)";
				
			$values = array(
				':item'		=> $post['item'],
				':href'		=> $post['href'],
				':target'	=> $post['target'],
				':title'	=> $post['title'],
				':menu'		=> $post['menu'],
				':sort'		=> $post['sort'],
				':subof'	=> $post['subof']
			
			);
				
			return $this->executeQuery($statement, $values, 'lastId');
		}
	}

	/**
	 *
	 * Updates menu
	 * @param array $post	array of data to write in database
	 */
	public function update($post)
	{
		$statement = "UPDATE `" . PREFIX . "__menu` SET "
		. " `item` 	= :item, "
		. " `href`	= :href, "
		. " `target`= :target, "
		. " `title`	= :title, "
		. " `menu`	= :menu, "
		. " `sort`	= :sort, "
		. " `subof` = :subof "
		. " WHERE `id`	= :id";

		$values = array(
			':item'		=> $post['item'],
			':href'		=> $post['href'],
			':target'	=> $post['target'],
			':title'	=> $post['title'],
			':menu'		=> $post['menu'],
			':sort'		=> $post['sort'],
			':subof'	=> $post['subof'],
			':id'		=> $post['id']
		);

		return $this->executeQuery( $statement, $values );

	}

	/**
	 *
	 * Deletes menu voice from database
	 * @param int $id	id of row to delete
	 */
	public function delete($id){

		$statement = "DELETE FROM `" . PREFIX . "__menu` WHERE `id` = :id";

		return $this->executeQuery( $statement, array(':id' => $id) );
	}

}