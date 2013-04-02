<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Mar 25, 2013
 */
 
class Controller
{
	protected $get, $post, $request;

	public function __construct($get, $post, $request)
	{
		$this->get = $get;
		$this->post = $post;
		$this->request = $request;
	}

	public function render($module, $template, $data = false)
	{
		$twig = new Twig_Environment(new Twig_Loader_Filesystem(MOD_DIR . $module . '/tmpl'), unserialize(CACHE));
		echo $twig->render($template . '.html', $data ? $data : array() );
	}

	public function route()
	{
		if ($this->request['obj'])
		{
			$obj = $this->request['obj'];
			$method = $this->request['method'];
			
			$param = array_merge((array)$this->get['param'], array('post'=>$this->post));
	
			if (get_parent_class($obj) == 'Controller')
			{
				$_aa = new $obj($this->get, $this->post, $this->request);
				$_aa->$method();
			}
			else
			{
				call_user_func_array(array($obj, $method), $param);
			}
		}
		else
		{
			throw new Exception('No data to load');
		}
	}
}