<?php
/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Jun 12, 2013
 */

class plugins_ctrl extends Controller
{
  public function run()
  {
    try{

      $plugin = $this->get['param'][0];

      $param = $this->get['param'];
      array_shift($param);

      if (!$plugin)
      {
        throw new Exception('No plugin name defined');
      }

      if (!class_exists($plugin))
      {
        $plg_file = './sites/default/modules/' . $plugin . '/' . $plugin . '.inc';

        if (!file_exists($plg_file))
        {
          throw new Exception('Plugin file <code>' . $plg_file . '</code> not found!');
        }

        require_once $plg_file;

      }

      if(!method_exists($plugin, 'admin'))
      {
        throw new Exception('Method <code>' . $plugin . '::admin</code> not found in <code>' . $plg_file . '</code>');
        return false;
      }

      call_user_func_array(array(new $plugin, 'admin'), $param);
    }
    catch (Exception $e)
    {
      echo '<p class="text-danger"><strong>Error: </strong>' . $e->getMessage() . '</p>';
    }
  }
}
