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
	 * @var array array of $_GET data
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
	 * Initializes object and sets $get, $post and $request variables
	 */
	public function __construct()
	{
		$this->get = $_GET;
		$this->post = $_POST;
		$this->request = $_REQUEST;
	}

	/**
	 * Renders a TWIG template in a module folder, setting default tr and uid variables
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
  
  /**
   * Checks if $object and $method are present in the whitelist of objects&method
   * that can be run even if no session variables are set
   * @param string $object object name
   * @param string $method method name
   * @return boolean
   */
  private function nonNeed2Auth($object, $method)
  {
    $permitted = [
      'tr'=> '*',
      'log_ctrl' => ['in'],
      'addsite_ctrl' => '*',
      'admin_ctrl' => '*',
      'api_ctrl' => ['run'],
      'userform_ctrl' => ['process'],
      'seo_ctrl' => '*',
      'feeds_ctrl' => '*',
      'OAI_ctrl' => '*'
    ];
    
    return (
      $permitted[$object] === '*' ||
      ( is_array($permitted[$object]) && in_array($method, $permitted[$object]))
      );
  }

	/**
	 * Main routing system. Uses the GET/POST/REQUEST parameters to run modules.
   * If no valid session data are present the safe whitelist will be checked
   * Also, foreach object, the admin blacklist will be checked
	 * @throws Exception
	 */
	public function route()
	{
		if ($this->request['obj'])
		{
			$obj = $this->request['obj'];
			$method = $this->request['method'];
      
      unset($this->request['obj']);
			unset($this->request['method']);
			unset($this->get['obj']);
			unset($this->get['method']);
			unset($this->post['obj']);
			unset($this->post['method']);
			
			$param = array_merge((array)$this->get['param'], array('post'=>$this->post));
      
      $trace_params = $obj . '::' . $method . '(' . var_export($param, true) . ')';
      
      
      if (!$_SESSION['user_confirmed'] && !$this->nonNeed2Auth($obj, $method))
      {
        throw new Exception('Permission denied. Line: ' . __LINE__ . '; details: ' . $trace_params);
      }
	
			if (get_parent_class($obj) === 'Controller')
			{
				$_aa = new $obj($this->get, $this->post, $this->request);
        
        if($_aa->adminRequired 
          && is_array($_aa->adminRequired) 
          && in_array($method, $_aa->adminRequired)
          && !$_SESSION['user_admin'])
        {
          throw new Exception('Permission denied. Line: ' . __LINE__ . '; details: ' . $trace_params);
        }
        
				$_aa->$method();
			}
			else
			{
        if (property_exists($obj, 'adminRequired'))
        {
          $ref = new ReflectionClass($obj);
          $def_prop = $ref->getDefaultProperties();
          $admin_methods = $def_prop['adminRequired'];
          if (is_array($admin_methods) && in_array($method, $admin_methods) && !$_SESSION['user_admin'])
          {
            throw new Exception('Permission denied. Line: ' . __LINE__ . '; details: ' . $trace_params);
          }
        }
				call_user_func_array(array($obj, $method), $param);
			}
		}
		else
		{
			throw new Exception('No data to load');
		}
	}
  
  protected function responseJson($status, $text)
  {
    return json_encode(array(
      'status' => $status,
      'text' => $text
    ));
  }
}
