<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 4, 2012
 */
 
class menu_ctrl
{
	public static function nestSort($data)
	{
		$menu = new Menu();
		if (!$menu->updateSortNest($data['menu']))
		{
			echo tr::get('error_update_sort');
		}
	}
	
	public static function all()
	{
		$menu = new Menu();
		
		$all_menus = $menu->getList();
		
		if (is_array($all_menus))
		{
			$str_menus = array();
			
			foreach ($all_menus as $m)
			{
				$str_menus[$m] = $menu->get_structured_menu($m);
			}
			
		}
		
		if (is_array($str_menus))
		{
			$twig = new Twig_Environment(new Twig_Loader_Filesystem(MOD_DIR . 'menu/tmpl'), unserialize(CACHE));
			echo $twig->render('list.html', array(
					'all_menus'=>$str_menus,
					'uid' => uniqid(),
					'tr' => new tr()
			));
		}
	}
	
	public static function edit($id)
	{
		$menu = new Menu();
		$art = new Article(new DB());
		
		$menu_data = $menu->getItem($id);
		
		$menu_list = $menu->getList();
		
		$twig = new Twig_Environment(new Twig_Loader_Filesystem(MOD_DIR . 'menu/tmpl'), unserialize(CACHE));
		echo $twig->render('form.html', array(
				'menu'=>$menu_data[0],
				'menu_list' => $menu_list,
				'uid' => uniqid('id'),
				'tr' => new tr(),
				'arts' => $art->getAll(),
				'secs' => $art->getSections()
		));
	}
	
	public static function addNew()
	{
		$menu = new Menu();
		$art = new Article(new DB());
		
		$menu_list = $menu->getList();
		
		$twig = new Twig_Environment(new Twig_Loader_Filesystem(MOD_DIR . 'menu/tmpl'), unserialize(CACHE));
		echo $twig->render('form.html', array(
				'menu_list' => $menu_list,
				'menu_list_array' => '["' . ( is_array($menu_list) ? implode('","', $menu_list) : '' ) . '"]',
				'uid' => uniqid('id'),
				'tr' => new tr(),
				'arts' => $art->getAll(),
				'secs' => $art->getSections()
		));
		
	}
	
	public static function getMenuItems($menu)
	{
		$menuObj = new Menu();

		$items = $menuObj->get_all_items_of_menu($menu);
		
		echo json_encode($items);
		
	}
	
	public static function save($post)
	{
		try
		{
			$menu = new Menu();
			
			if ($post['id'])
			{
				if (!$menu->update($post))
				{
					throw new Exception(tr::sget('update_menu_error', $post_id));
				}
			}
			else
			{
				$id = $menu->add($post); 
				if (!$id)
				{
					throw new Exception(tr::get('save_menu_error'));
				}
				$out['id'] = $id;
			}
			
			
			$out['type'] = 'success';
			$out['text'] = tr::get('save_menu_ok');
		}
		catch (Exception $e)
		{
			$out['type'] = 'error';
			$out['text'] = $e->getMessage();
		}
		echo json_encode($out);
	}
	
	
	
	public static function delete($id)
	{
		$menu = new Menu();
	
		try
		{
			if (!$menu->delete($id))
			{
				throw new Exception(tr::sget('delete_menu_error', $id));
			}
			$out['type'] = 'success';
			$out['text'] = tr::get('delete_menu_ok');
		}
		catch (Exception $e)
		{
			error_log($e->getMessage());
			$out['type'] = 'error';
			$out['text'] = $e->getMessage();
		}
		echo json_encode($out);
	}
	
}