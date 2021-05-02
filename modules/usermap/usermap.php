<?php

/**
 * 
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Aug 29, 2013
 */


class usermap_ctrl extends Controller
{
    public function createNew()
    {
        $name = $this->get['param'][0] . '.map';

        $text = array(
            'elId' => 'map',
            'scrollWheelZoom' => false,
            'attribution' => '<a href="http://bradypus.net" title="BraDypUS. Communicating Cultural Heritage">BraDypUS</a>',
            'zoom' => '7',
            'center' => array(44.51618, 11.34238),
            'zoomToBounds' => false,
            'markers' => [[
                "coord" =>  array(44.51618,  11.34238),
                "name" => "<big><strong>BraDypUS</strong></big>.<br />Via A. Fioravanti, 72.<br />40129 Bologna Italy"
            ]]

        );


        if (!is_dir('./sites/default/modules/usermaps')) {
            @mkdir('./sites/default/modules/usermaps', 0777, true);
        }

        if (utils::write_in_file('./sites/default/modules/usermaps/' . $name, $text, 'json')) {
            echo json_encode(array('status' => 'success', 'text' => tr::get('ok_map_config_saved')));
        } else {
            echo json_encode(array('status' => 'error', 'text' => tr::get('error_map_config_not_saved')));
        }
    }

    public function save()
    {
        if (utils::write_in_file('./sites/default/modules/usermaps/' . $this->get['param'][0], $this->post['data'], 'json')) {
            echo json_encode(array('status' => 'success', 'text' => tr::get('ok_map_config_saved')));
        } else {
            echo json_encode(array('status' => 'error', 'text' => tr::get('error_map_config_not_saved')));
        }
    }

    public function erase()
    {
        if (unlink('./sites/default/modules/usermaps/' . $this->get['param'][0])) {
            echo json_encode(array('status' => 'success', 'text' => tr::get('ok_map_deleted')));
        } else {
            echo json_encode(array('status' => 'error', 'text' => tr::get('error_map_not_deleted')));
        }
    }

    public function edit_map()
    {
        $map = $this->get['param'][0];

        $content = file_get_contents('./sites/default/modules/usermaps/' . $map);
        $this->render('usermap', 'edit_map', array(
            'map' => $map,
            'content' => $content
        ));
    }


    public function view()
    {
        $this->render('usermap', 'list', array(
            'maps' => utils::dirContent('./sites/default/modules/usermaps')
        ));
    }

    /**
     * Formats and return HTML with map data
     * @param array $param general parameters.
     *  Mandatory value: $param['content']: the map to show
     *  Optional value: $param['width'], default '100%'
     *  Optional value: $param['height'], default '400px'
     * @return string
     */
    public function showMap($param, Out $out)
    {
        //data-cfg="lavori" style="width: 100%; height: 400px;"
        $html = '<div'
            . ' id="' . uniqid() . '"'
            . ' class="usermap"'
            . ($param['marker'] ? ' data-marker="' . $param['marker'] . '"' : '')
            . ($param['zoom'] ? ' data-zoom="' . $param['zoom'] . '"' : '')
            . ($param['platform'] ? ' data-platform="' . $param['platform'] . '"' : '')
            . ($param['type'] ? ' data-type="' . $param['type'] . '"' : '')
            . ' data-cfg="' . $param['content'] . '"'
            . ' style="'
            . 'width: ' . ($param['width'] ? $param['width'] : '100%') . ';'
            . 'height:' . ($param['height'] ? $param['height'] : '400px') . ';"'
            . '></div>';
        $out->setQueue('modules', '<script>window.usermap || document.write(\'<script src="./modules/usermap/usermap.js"><\/script>\');</script>');
        return $html;
    }
}
