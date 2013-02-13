<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 4, 2012
 */
 
class Menu
{
	private $db;
	
	public function __construct()
	{
		$this->db = new DB();
	}
	
	public function updateSortNest($arr)
	{
		$this->db->beginTransaction();
		try
		{
			$sort = 0;
			foreach ($arr as $id => $sub)
			{
				$q = 'UPDATE `' . PREFIX . '__menu` SET `sort` = :sort, `subof` = :subof  WHERE `id` = :id';
				
				$values = array(':sort'=>$sort, ':id' => $id, ':subof' => ($sub == 'null' ? null : $sub));
				
				if (!$this->db->executeQuery($q, $values))
				{
					throw new Exception();
				}
				$sort++;
			}
			$this->db->commit();
			return true;
		}
		catch (Exception $e)
		{
			$this->db->rollBack();
			return false;
		}
	}
	
	public function updateSort($arr)
	{
		$this->db->beginTransaction();
		
		try
		{
			foreach ($arr as $sort=>$id)
			{
				$q = 'UPDATE `' . PREFIX . '__menu` SET `sort` = :sort WHERE `id` = :id';
	
				if (!$this->db->executeQuery($q, array(':sort'=>$sort, ':id' => $id)))
				{
					throw new Exception();
				}
			}
			$this->db->commit();
			return true;
		}
		catch (Exception $e)
		{
			$this->db->rollBack();
			return false;
		}
	}
	
	public function getAll()
	{
		$query = "SELECT * FROM `" . PREFIX . "__menu` WHERE 1 ORDER BY `menu`, `sort`";
			
		$menus = $this->db->executeQuery($query, false, 'read');
		
		if(is_array($menus))
		{
			$x = 0;
			
			foreach ($menus as $menu)
			{
				$tmp[$menu['menu']][] = $menu;
			}
		}
		else
		{
			return false;
		}
		
		return $tmp;
	}
	
	public function getItem($id)
	{
		$query = "SELECT * FROM `" . PREFIX . "__menu` WHERE `id`	= " . $id . " LIMIT 0, 1";
		
		$res = $this->db->executeQuery($query, false, 'read');
		
		if ( count ( $res ) == 0 )
		{
			return false;
		}
		else
		{
			return $res;
		}
	}
	
	public function getList()
	{
		$query = "SELECT menu FROM `" . PREFIX . "__menu` WHERE 1 GROUP BY (`menu`) ORDER BY `menu`";
	
		$res = $this->db->executeQuery($query, false, 'read');
	
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
	 * Returns array with menu items
	 * @param string $menu_name		menu name
	 * @param string $lang			language
	 * @return array
	 */
	public function get_all_items_of_menu($menu_name, $lang = false)
	{
		$query = "SELECT * FROM `" . PREFIX . "__menu` WHERE `menu` = '{$menu_name}' ORDER BY `sort`";
			
		$res = $this->db->executeQuery($query, false, 'read');
		
		return $lang ? $this->translate($res, $lang) : $res;
	}
	
	
	public function get_structured_menu($menu_name, $lang = false)
	{
		$not_structured_menu = $this->get_all_items_of_menu($menu_name, $lang);
		
		$structured = array();
		
		foreach($not_structured_menu as $item)
		{
			if (empty($item['subof']))
			{
				if (is_array($structured[$item['id']]))
				{
					$structured[$item['id']] = array_merge($structured[$item['id']], $item);
				}
				else
				{
					$structured[$item['id']] = $item;
				}
			}
			else
			{
				$structured[$item['subof']]['sub'][] = $item;
			}
		}
		
		return $structured;
	}
	
	private function translate($menu_arr, $lang)
	{
		if(!is_array($menu_arr))
		{
			return $menu_arr;
		}
		if (!is_array($menu_arr[0]) && $menu_arr['id'])
		{
			$menu_arr = array($menu_arr);
		}
		
		foreach($menu_arr as $menu)
		{
			$id_array[] = $menu['id'];
		}
		
		$query = 'SELECT * from `' . PREFIX . '__menu_' . $lang . '` WHERE `id_menu` IN (' . implode(',', $id_array). ')';
		
		$translated = $this->db->executeQuery($query, false, 'read');
		
		if (!is_array($translated))
		{
			foreach($menu_arr as &$menu)
			{
				foreach ($translated as $tr_menu)
				{
					if ($menu['id'] == $tr_menu['menu_id'])
					{
						if($tr_menu['item'] != '')
						{
							$menu['item'] = $tr_menu['item'];
						}
		
						if($tr_menu['title'] != '')
						{
							$menu['title'] = $tr_menu['title'];
						}
					}
				}
			}
		}
		
		return (count($menu_arr) == 1) ? $menu_arr[0] : $menu_arr;
	}
	
	public function update($post)
	{
		if ( !$post['item'] || !$post['href'] || !$post['menu'] )
		{
			echo '1';
			return false;
		}
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
		
		return $this->db->executeQuery( $statement, $values);
	
	}
	
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
	
			return $this->db->executeQuery($statement, $values, 'id');
		}
	}
	
	public function delete($id)
	{
		$sys_langs = cfg::get('languages');
		
		$sql = "DELETE FROM `" . PREFIX . "__menu` WHERE `id` = " . $id;
		
		$ret = $this->db->executeQuery($sql);
		
		if (is_array($sys_langs))
		{
			foreach ($sys_langs as $l=>$lang)
			{
				$this->db->executeQuery("DELETE FROM `" . PREFIX . "__menu_" . $l . "` WHERE `id_menu` = " . $id);
			}
		}
		
		return $ret;
	}
	
}