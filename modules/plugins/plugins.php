<?php

/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Jun 12, 2013
 */

class plugins_ctrl extends Controller
{
    private $remote_repo = 'https://github.com/bdus-dev/bradycms-plugins/';
    private $plugin_list = 'https://raw.githubusercontent.com/bdus-dev/bradycms-plugins/master/plugin-list.json';

    public function manage()
    {
        $installed = (array) utils::dirContent('./sites/default/modules');
        $available = (array) json_decode(file_get_contents($this->plugin_list), true);

        $plugins = [];

        foreach ($available as $a) {
            $plugins[$a['id']] = $a;
            $plugins[$a['id']]['url'] = "{$this->remote_repo}tree/master/{$a['id']}";
        }

        foreach ($installed as $i) {
            $plugins[$i]['installed'] = true;
            if (!$plugins[$i]['id']) {
                $plugins[$i]['id'] = $i;
            }
        }

        $this->render('plugins', 'list', [
            'plugins' => $plugins
        ]);
    }

    public function uninstall(): bool
    {
        $resp = [
            "status" => "error"
        ];
        $plugin = $this->get['plugin'];
        if (!$plugin) {
            $resp['text'] = tr::get("missing_plugin_name");
            echo json_encode($resp);
            return false;
        }
        if (!is_dir("sites/default/modules/{$plugin}")) {
            $resp['text'] = tr::sget("plugin_not_installed", [$plugin]);
            echo json_encode($resp);
            return false;
        }

        $err_del = utils::recursive_delete("sites/default/modules/{$plugin}");

        if ($err_del && is_array($err_del) && !empty($err_del)) {
            $resp['text'] = tr::sget("error_uninstall_plugin", [$plugin]);
        } else {
            $resp['text'] = tr::sget("success_uninstall_plugin", [$plugin]);
            $resp['status'] = "success";
        }
        echo json_encode($resp);
        return true;
    }

    public function install(): bool
    {
        $resp = [
            "status" => "error"
        ];
        $plugin = $this->get['plugin'];
        if (!$plugin) {
            $resp['text'] = tr::get("missing_plugin_name");
            echo json_encode($resp);
            return false;
        }
        $available = (array) json_decode(file_get_contents($this->plugin_list), true);
        $found = false;
        foreach ($available as $p) {
            if ($p['id'] === $plugin) {
                $found = true;
            }
        }
        if (!$found) {
            $resp['text'] = tr::sget("plugin_not_available", [$plugin]);
            echo json_encode($resp);
            return false;
        }
        if (!$this->install_plugin($plugin)) {
            $resp['text'] = tr::sget('error_install_plugin', [$plugin]);
        } else {
            $resp['status'] = 'success';
            $resp['text'] = tr::sget('success_install_plugin', [$plugin]);
        }
        echo json_encode($resp);
        return true;
    }

    private function install_plugin(string $plugin): bool
    {
        $localPath = TMP_DIR . md5($plugin) . '/';
        $remoteZip = $this->remote_repo . 'archive/master.zip';
        $localZip = $localPath . 'master.zip';

        if (!is_dir($localPath)) {
            @mkdir($localPath, 0777, true);
        }

        $update = new Update();
        $update->downloadFile($remoteZip, $localZip);
        $update->unzip($localZip, $localPath);
        $update->install("{$localPath}bradycms-plugins-master/{$plugin}", "sites/default/modules/{$plugin}/");
        return true;
    }

    public function run()
    {
        try {
            $plugin = $this->get['param'][0];

            $param = $this->get['param'];
            array_shift($param);

            if (!$plugin) {
                throw new Exception('No plugin name defined');
            }

            if (!method_exists($plugin, 'admin')) {
                throw new Exception('Method <code>' . $plugin . '::admin</code> not found in <code>' . $plugin . '</code>');
                return false;
            }

            call_user_func_array(array(new $plugin, 'admin'), $param);
        } catch (Exception $e) {
            echo '<p class="text-danger"><strong>Error: </strong>' . $e->getMessage() . '</p>';
        }
    }
}
