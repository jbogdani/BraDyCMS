<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license			MIT, See LICENSE file
 * @since			Dec 4, 2012
 */
 
class Menu
{
	
	/**
	 * Returns name of menu table
	 * @return string
	 */
	private static function menu_tb()
	{
		return PREFIX . 'menu';
	}
	
	
	/**
	 * Returns list of available menus
	 * @return array|false
	 */
	public static function getList()
	{
		return R::getCol("SELECT menu FROM " . self::menu_tb() . " WHERE 1 GROUP BY `menu` ORDER BY `menu`");
	}
	
	/**
	 * Gets bean or array of beans and return array data, with translation
	 * @param object|array $menu menu daat
	 * @param string $lang output language
	 * @param boolean $return_bean if true beans will be returned
	 * @return object|array
	 */
	public static function parseMenu($menu, $lang = false, $return_bean = false)
	{
		if (is_array($menu))
		{
			foreach ($menu as &$m)
			{
				$m = self::parseMenu($m, $lang);
			}
			return $menu;
		}
		else if (is_object($menu))
		{
			if ($lang)
			{
				$raw_translation = $menu->withCondition(' lang = ?', array($lang))->ownMenutrans;
				
				$trans_values = array_values($raw_translation);
				$translation = array_shift($trans_values);
			
				if ($translation)
				{
					$translation->title	? $menu->title	= $translation->title	: '';
					$translation->item	? $menu->item	= $translation->item		: '';
				}
			}
			
			return $return_bean ? $menu : $menu->export();
		}
	}
	
	/**
	 * Returns array with menu items
	 * @param string $menu_name		menu name
	 * @param string $lang			language
	 * @return array
	 */
	public static function get_all_items_of_menu($menu_name, $lang = false)
	{
		
		$menus = R::find(self::menu_tb(), ' menu = ? ORDER BY sort ', array($menu_name));
		
		return self::parseMenu($menus, $lang);
	}
	
	
	/**
	 * Returns structured array with menu data
	 * @param string $menu_name
	 * @param string $lang
	 * @return array
	 */
	public static function get_structured_menu($menu_name, $lang = false)
	{
		$not_structured_menu = self::get_all_items_of_menu($menu_name, $lang);
		
		$structured = $not_structured_menu;
		
		foreach($not_structured_menu as $key => $item)
		{
			if (!empty($item['subof']))
			{
				unset($structured[$key]);
				
				$structured = self::recursiveNest($structured, $item);
			}
		}
	
		return $structured;
	}
	
	
	/**
	 * Returns a nested array of menu data
	 * @param array $array	array of plain menu items
	 * @param menu item $item
	 * @return array
	 */
	private static function recursiveNest($array, $item)
	{
		foreach($array as $k=>&$v)
		{
			if ($v['id'] == $item['subof'])
			{
				$v['sub'][] = $item;
				break;
			}
			if (is_array($v['sub']))
			{
				$v['sub'] = self::recursiveNest($v['sub'], $item);
			}
		}
		return $array;
	}
	
	
	/**
	 * Returns a flat/plain array of nested data
	 * @param array $data array of nested menu items
	 * @param number $sort
	 * @param string $subof
	 * @return array
	 */
	private static function recursiveFlat($data, $sort = 0, $subof = false)
	{
		foreach ($data as $row)
		{
			$new_arr[] =  array(
					'id' => $row['id'],
					'sort' => $sort,
					'subof' => $subof
			);
			$sort++;
			if ($row['children'])
			{
				$new_arr = array_merge($new_arr, self::recursiveFlat($row['children'], $sort, $row['id']));
			}
		}
		return $new_arr;
	}
	
	
	/**
	 * Saves in DB menu item updated sorting and nesting level 
	 * @param unknown $data
	 */
	public static function updateNestSort($data)
	{
		$new_data = self::recursiveFlat($data);
		
		foreach ($new_data as $d)
		{
			$menu = R::load(self::menu_tb(), $d['id']);
			$menu->sort = $d['sort'];
			$menu->subof = $d['subof'];
			R::store($menu);
		}
	}
	
	
	/**
	 * Returns array of menu item's data
	 * @param int $id
	 * @param boolean $dontexport if true bean will be returned, otherwise array
	 * @return array|false
	 */
	public static function getItem($id, $dontexport = false)
	{
		$menu = R::load(self::menu_tb(), $id);
		
		if ($menu->id){
			return $dontexport ? $menu : $menu->export();
		}
		else
		{
			return false;
		}
		
	}
	
	
	/**
	 * Deletes menu item and dependencies (ie. translations)
	 * @param int $id
	 */
	public static function delete($id)
	{
		$menu = R::load(self::menu_tb(), $id);
		
		if ($menu->id)
		{
			// TODO check if necessary
			unset($menu->ownMenutrans);
			R::store($menu);
			R::trash($menu);
			
			
		}
	}
	
	
	/**
	 * Saves menu data in DB
	 * @param array $data
	 * @return integer|false
	 */
	public static function save($data)
	{
		if ( !$data['item'] || !$data['href'] || !$data['menu'] )
		{
			return false;
		}
		
		$menu = $data['id'] ? R::load(self::menu_tb(), $data['id']) : R::dispense(self::menu_tb());
		
		$menu->import($data);
		
		return R::store($menu);
	}
	
	/**
	 * Adds translation for certain menu item
	 * @param int $menu_id id of menu item to translate
	 * @param array $data array of tranlated data
	 */
	public static function translate($menu_id, $data)
	{
		if ($data['id'])
		{
			$id = $data['id'];
			unset($data['id']);
		}
		$menuItem = R::load(self::menu_tb(), $menu_id);

		$transl = R::dispense(PREFIX . 'menutrans');

		$transl->import($data);
		
		$menuItem->ownMenutrans[$id] = $transl;
		
		R::store($menuItem);

	}

	
}