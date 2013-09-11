<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Feb 23, 2013
 */
 
class cfg_ctrl extends Controller
{
	public function edit()
	{
		$data = cfg::get();
		
		$this->render('cfg', 'form', array( 'data' => $data ));
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
}