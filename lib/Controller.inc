<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license			MIT, See LICENSE file
 * @since			Mar 25, 2013
 */
 
class Controller
{
	/**
	 *
	 * @var array arrau of $_GET data
	 */
	protected $get;
	
	/**
	 *
	 * @var array array of $_POST data
	 */
	protected $post;
	
	/**
	 *
	 * @var array array of $_REQUEST data 
	 */
	protected $request;

	/**
	 * Starts object and sets $get, $post and $request
	 */
	public function __construct()
	{
		$this->get = $_GET;
		$this->post = $_POST;
		$this->request = $_REQUEST;
	}

	/**
	 * Renders a TWIG template in a module folder
	 * @param string $module module name
	 * @param string $template template (no extension) file name
	 * @param array $data array of data to inject in template (uid and tr object are available by default)
	 */
	public function render($module, $template, $data = false)
	{
		$twig = new Twig_Environment(new Twig_Loader_Filesystem(MOD_DIR . $module . '/tmpl'), unserialize(CACHE));
		if ($_SESSION['debug'])
		{
			$twig->addExtension(new Twig_Extension_Debug());
		}
		
		$data['tr'] = new tr();
		$data['uid'] = uniqid();
		 
		echo $twig->render($template . '.twig', $data );
	}
  
  private function nonNeed2Auth($object, $method)
  {
    $permitted = [
      'tr'=> '*',
      'log_ctrl' => ['in'],
      'addsite_ctrl' => '*',
      'admin_ctrl' => '*'
    ];
    
    return (
      $permitted[$object] === '*' ||
      in_array($method, $permitted[$object])
      );
  }

	/**
	 * Main routing method
	 * @throws Exception
	 */
	public function route()
	{
		if ($this->request['obj'])
		{
			$obj = $this->request['obj'];
			$method = $this->request['method'];
      
      if (!$_SESSION['user_confirmed'] && !$this->nonNeed2Auth($obj, $method))
      {
        throw new Exception('Permission denied');
      }
			
			unset($this->request['obj']);
			unset($this->request['method']);
			unset($this->get['obj']);
			unset($this->get['method']);
			unset($this->post['obj']);
			unset($this->post['method']);
			
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