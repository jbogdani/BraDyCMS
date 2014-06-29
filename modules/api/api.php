<?php

/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2014 
 * @license			All rights reserved
 * @since				Feb 7, 2014
 * 
 * GET:
 *  action: string, required. Actually only read is supported
 *  lang: string, optional. User language id.
 *  menu: string|array, optional. Menuid for menus to get from database
 *  tag:  string|array, optional. Tag or tags to use to filter articles
 *  artid: string|array, optional. Textid or array of textids of articles that will be returned
 *  metadata: boolean, optional, default false. If true an array with page metadata will be returned
 * 
 * RESPONSE
 *  menu: [
 *          menuid: [],
 *        ]
 *  tags:[
 *        {article data...}, 
 *        ]
 *  metadata:[
 *            ..., 
 *            ]
 *  article: [
 *              textid: [],
 *            ]
 *  galleries: true|false
 * 
 */

class api_ctrl extends Controller
{
  public function run()
  {
    $array = $this->process($this->get);
    
    header("Access-Control-Allow-Origin: *");
    header('Content-type: text/json');
    header('Content-type: application/json');
    
    echo json_encode($array, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
  }
  
  
  private function process($get)
  {
    $response = array();
    
    switch($get['action'])
    {
      case 'read':
        try
        {
          $response = $this->read($get);
        }
        catch(Exception $e)
        {
          $response['status'] = 'success';
          $response['verbose'] = 'Unknown action: ' . $e->getMessage();
        }
        break;
      
      
      case false:
      default:
        $response['status'] = 'error';
        $response['verbose'] = 'Unknown action: ' . $get['action'];
        break;
    }
    return $response;
  }
  
  
  private function read($get)
  {
    
    $response = array('status' => 'success');
    $out = new Out($get, $get['lang']);
    
    /**
     * Menus
     * menu=menuID or
     * menu[]=firstMenuId&menu[]=secondMenuId...
     */
    if ($get['menu'])
    {
      if (is_string($get['menu']))
      {
        $menus[] = $get['menu'];
      }
      else if (is_array($get['menu']))
      {
        $menus = $get['menu'];
      }
      
      if (is_array($menus))
      {
        foreach ($menus as $menu)
        {
          $response['menu'][$menu] = $out->getMenu($menu);
        }
      }
    }
    
    /**
     * Tags
     * tag=tagID or
     * tag[]=firstTagId&tag[]=secondTagId...
     */
    
    if ($get['tag'])
    {
      if (is_string($get['tag']))
      {
        $tags[] = $get['tag'];
      }
      else if (is_array($get['tag']))
      {
        $tags = $get['tag'];
      }
      if (is_array($tags))
      {
        $arts = $out->getArticlesByTagArray($tags);
        if (is_array($arts) && !empty($arts))
        {
          $response['tags'] = array_values($arts);
        }
        
        if ($get['galleries'] && !empty($response['tags']))
        {
          foreach($response['tags'] as &$art)
          {
            try
            {
              $art['gallery'] = $out->getGallery($art['textid']);
            }
            catch (Exception $e)
            {
              error_log($e->getMessage());
            }
          }
            
        }
      }
    }
    
    /**
     * Page metadata
     */
    if ($get['metadata'])
    {
      $response['metadata'] = $out->getPageData();
    }
    
    /**
     * Article
     */
    if ($get['artid'])
    {
      if (is_string($get['artid']))
      {
        $artids[] = $get['artid'];
      }
      else if (is_array($get['artid']))
      {
        $artids = $get['artid'];
      }
      
      if (is_array($artids))
      {
        foreach($artids as $artid)
        {
          $art = false;
          $art = $out->getArticle($artid);
      
          if($art instanceof RedBean_OODBBean)
          {
            $art = $art->export();
          }
          
          $response['article'][$artid] = $art;
          
          if ($get['galleries'])
          {
            try
            {
              $response['article'][$artid]['gallery'] = $out->getGallery($artid);
            }
            catch (Exception $e)
            {
              error_log($e->getMessage());
            }
            
          }
        }
      }
    }
    
    return $response;
  }
}

?>
