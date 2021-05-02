<?php

/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Dec 4, 2012
 */

class menu_ctrl extends Controller
{

  public function all()
  {
    $all_menus = Menu::getList();
    if (is_array($all_menus)) {
      $str_menus = array();

      foreach ($all_menus as $m) {
        $html_menus[$m] = $this->strMenu2ul(Menu::get_structured_menu($m));
      }
    }

    if (is_array($str_menus)) {
      $this->render('menu', 'list', array(
        'html_menus' => $html_menus
      ));
    }
  }

  private function strMenu2ul($menu)
  {
    $html = '<ol class="dd-list">';
    foreach ($menu as $item) {
      $html .= '<li class="dd-item" data-id="' . $item['id'] . '">' .
        '<i class="fa fa-arrows dd-handle"></i> ' .
        '<a href="#menu/edit/' . $item['id'] . '">' . $item['item'] . '</a>' .
        '';
      if ($item['sub']) {
        $html .= $this->strMenu2ul($item['sub']);
      }
      $html .= '</li>';
    }
    $html .= '</ol>';
    return $html;
  }


  public function updateNestSort()
  {
    try {
      Menu::updateNestSort($this->request['data']);
    } catch (Exception $e) {
      error_log($e->getMessage());
      echo tr::get('error_update_sort');
    }
  }


  public function edit()
  {
    $id = $this->request['param'][0];

    $menu_item = Menu::getItem($id, true);

    $transls = $menu_item->ownMenutrans;

    if (is_array($transls)) {
      foreach ($transls as $trans) {
        $translation[$trans->lang] = $trans->export();
      }
    }

    $this->render('menu', 'form', array(
      'menu' => $id ? $menu_item->export() : false,
      'tags' => Article::getTags(),
      'articles' => Article::getAllTextid(),
      'menu_list' => Menu::getList(),
      'cfg_langs' => cfg::get('languages'),
      'translated' => $translation

    ));
  }


  public function getMenuItems()
  {
    $menu = $this->get['param'][0];
    echo json_encode(Menu::get_all_items_of_menu($menu));
  }

  /**
   * Alias for edit
   */
  public function addNew()
  {
    $this->edit();
  }


  public function save()
  {
    $post = $this->post;
    try {
      $id = Menu::save($post);

      if (!$id) {
        throw new Exception($post['id'] ? tr::sget('update_menu_error', [$post['id']]) : tr::get('save_menu_error'));
      }

      $out['id'] = $id;

      $out['type'] = 'success';
      $out['text'] = tr::get('save_menu_ok');
    } catch (Exception $e) {
      $out['type'] = 'error';
      $out['text'] = $e->getMessage();
    }
    echo json_encode($out);
  }



  public function delete()
  {
    $id = $this->get['param'][0];

    try {
      Menu::delete($id);
      $out['type'] = 'success';
      $out['text'] = tr::get('delete_menu_ok');
    } catch (Exception $e) {
      error_log($e->getMessage());
      $out['type'] = 'error';
      $out['text'] = tr::sget('delete_menu_error', [$id]);
    }
    echo json_encode($out);
  }


  public function saveTransl()
  {
    $data = $this->post;
    $menu_id = $this->get['param'][0];

    try {
      Menu::translate($menu_id, $data);

      $out['text'] = tr::get('ok_translation_saved');
      $out['status'] = 'success';
    } catch (Exception $e) {
      error_log($e->getMessage());
      $out['text'] = tr::get('error_translation_not_saved');
      $out['status'] = 'error';
    }

    echo json_encode($out);
  }
}
