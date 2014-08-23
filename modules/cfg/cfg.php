<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license			MIT, See LICENSE file
 * @since			Feb 23, 2013
 */
 
class cfg_ctrl extends Controller
{
	public function edit()
	{
		$data = cfg::get();
		
		$this->render('cfg', 'form', array( 'data' => $data, 'current_user' => $_SESSION['user_confirmed'], 'is_admin' => $_SESSION['user_admin'] ));
	}
	
	public function save()
	{
		$post = utils::recursiveFilter($this->post);
		if (cfg::save($post))
		{
			$resp = array('status' => 'success', 'text' => tr::get('ok_cfg_update'));
		}
		else
		{
			$resp = array('status' => 'success', 'text' => tr::get('error_cfg_update'));
		}
		
		echo json_encode($resp);
	}
	
	public function empty_cache()
	{
		$error = utils::recursive_delete(CACHE_DIR, true);
		
		if(count($error) > 0)
		{
			$ret = array('status' => 'error', 'text' => tr::get('cache_not_emptied') . '. ' . implode('; ', $error));
		}
		else
		{
			$ret = array('status' => 'success', 'text' => tr::get('cache_emptied'));
		}
		
		echo json_encode($ret);
	}
  
  
  public function empty_trash()
	{
		$error = utils::recursive_delete(TMP_DIR, true);
		
		if(count($error) > 0)
		{
			$ret = array('status' => 'error', 'text' => tr::get('trash_not_emptied') . '. ' . implode('; ', $error));
		}
		else
		{
			$ret = array('status' => 'success', 'text' => tr::get('trash_emptied'));
		}
		
		echo json_encode($ret);
	}
  
  
  public function update_htaccess()
  {
    if(utils::update_htaccess())
    {
      $ret = array('status' => 'success', 'text' => tr::get('htaccess_updated'));
    }
    else
    {
      $ret = array('status' => 'error', 'text' => tr::get('htaccess_not_updated'));
    }
  }
}