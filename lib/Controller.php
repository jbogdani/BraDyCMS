<?php
/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license      MIT, See LICENSE file
 * @since      Mar 25, 2013
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
     * @param array $get $_GET data
     * @param array $post $_POST data
     * @param array $request $_REQUEST data
     */
    public function __construct($get= false, $post = false, $request = false)
    {
        $this->get = $get;
        $this->post = $post;
        $this->request = $request;
    }

    /**
     * Renders a TWIG template in a module folder, setting default tr and uid variables
     * @param string $module module name
     * @param string $template template (no extension) file name
     * @param array $data array of data to inject in template (uid and tr object are available by default)
     * @param boolean $return if true data will be returned, else will be echoed
     */
    public function render($module, $template, $data = false, $return = false)
    {
        // add support for user plugin template folders
        if (file_exists(MOD_DIR . $module . '/tmpl')) {
            $dir = MOD_DIR . $module . '/tmpl';
        } elseif (file_exists(SITE_DIR . 'modules/' . $module . '/tmpl')) {
            $dir = SITE_DIR . 'modules/' . $module . '/tmpl';
        } else {
            throw new Exception("Template folder for module {$modeule} not found");
        }

        $twig = new \Twig\Environment(new \Twig\Loader\FilesystemLoader($dir), unserialize(CACHE));
        if ($_SESSION['debug']) {
            $twig->addExtension(new \Twig\Extension\DebugExtension());
        }

        $data['tr'] = new tr();
        $data['uid'] = uniqid();
        $data['lang'] = $_SESSION['adm_lang'];

        $html = $twig->render($template . '.twig', $data);

        if ($return) {
            return $html;
        } else {
            echo $html;
        }
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
      'OAI_ctrl' => '*',
      'protectedtags_ctrl' => ['login', 'logout'],
      'download_ctrl' =>['go']
    ];

        return (
      $permitted[$object] === '*' ||
      (is_array($permitted[$object]) && in_array($method, $permitted[$object]))
      );
    }

    /**
     * Main routing system. Uses the GET/POST/REQUEST parameters to run modules.
     * If no valid session data are present the safe whitelist will be checked
     * Also, foreach object, the admin blacklist will be checked
     * @throws Exception
     */
    public function route($obj = false, $method = false, $params = [])
    {
        if ($obj || $this->request['obj']) {
            $obj = $obj ? $obj : $this->request['obj'];
            $method = $method ? $method : $this->request['method'];

            unset($this->request['obj']);
            unset($this->request['method']);
            unset($this->get['obj']);
            unset($this->get['method']);
            unset($this->post['obj']);
            unset($this->post['method']);

            $param = !empty($params) ? $params : array_merge((array)$this->get['param'], ['post' => $this->post, 'get' => $this->get]);

            $trace_params = $obj . '::' . $method . '(' . var_export($param, true) . ')';


            if (!$_SESSION['user_confirmed'] && !$this->nonNeed2Auth($obj, $method)) {
                throw new Exception('Permission denied. Line: ' . __LINE__ . '; details: ' . $trace_params);
            }

            if (get_parent_class($obj) === 'Controller') {
                $_aa = new $obj($this->get, $this->post, $this->request);

                if ($_aa->adminRequired
          && is_array($_aa->adminRequired)
          && in_array($method, $_aa->adminRequired)
          && !$_SESSION['user_admin']) {
                    throw new Exception('Permission denied. Line: ' . __LINE__ . '; details: ' . $trace_params);
                }

                // Extensions of class Controller do not need GET & POST parameters as
                // function arguments. These variables are available via $this->post, $this->get, $this->request
                call_user_func_array([$_aa, $method], $params);
            } else {
                if (property_exists($obj, 'adminRequired')) {
                    $ref = new ReflectionClass($obj);
                    $def_prop = $ref->getDefaultProperties();
                    $admin_methods = $def_prop['adminRequired'];

                    if (is_array($admin_methods) && in_array($method, $admin_methods) && !$_SESSION['user_admin']) {
                        throw new Exception('Permission denied. Line: ' . __LINE__ . '; details: ' . $trace_params);
                    }
                }
                call_user_func_array(array($obj, $method), $param);
            }
        } else {
            throw new Exception('No data to load');
        }
    }

    /**
     * Utlity method that get array with status and text
     * and returns json encoded response
     * @param  string $status response status
     * @param  string $text   response text
     * @return string         json encoded response
     */
    protected function responseJson($status, $text)
    {
        return json_encode(array(
      'status' => $status,
      'text' => $text
    ));
    }
}
