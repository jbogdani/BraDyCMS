<?php
/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Mar 25, 2013
 */

class downloads_ctrl extends Controller
{
    private $path = DOWNLOADS_DIR;

    public function all()
    {
        if (!is_dir($this->path)) {
            mkdir($this->path, 0777, true);
        }

        $all_downloads = utils::dirContent($this->path);

        asort($all_downloads);

        $this->render('downloads', 'list', [
            'nodes' => $all_downloads
        ]);
    }

    public function edit()
    {
        // Create covers directory, if does not exist
        if (!is_dir($this->path . $this->get['param'][0] . '/covers/')) {
            mkdir($this->path . $this->get['param'][0] . '/covers', 0777, true);
        }

        $node_content = utils::dirContent($this->path . $this->get['param'][0]);

        $lang = $this->get['param'][1];

        arsort($node_content);

        if (file_exists($this->path . $this->get['param'][0] . '/data.json')) {
            $data = json_decode(file_get_contents($this->path . $this->get['param'][0] . '/data.json'), true);
        }

        if ($lang) {
            $orig = $data;

            unset($data);

            if (file_exists($this->path . $this->get['param'][0] . '/data_' . $lang . '.json')) {
                $data = json_decode(file_get_contents($this->path . $this->get['param'][0] . '/data_' . $lang . '.json'), true);
            }
        }

        if (is_array($data)) {
            $biggerSort = max(array_column($data, 'sort')) + 1;
        } else {
            $biggerSort = 1;
        }

        $files = [];
        foreach ($node_content as $file) {
            if ($file !== 'data.json' && !preg_match('/\.json/', $file) && $file !== 'covers') {
                $formattedName = str_replace('.', '__x__', $file);

                // file is not - yet - present in index file. Add it
                if (!$data[$formattedName]) {
                    $data[$formattedName] = [
                      'title' => $file,
                      'sort' => $biggerSort
                    ];
                    $changed = true;
                }

                $fullpath = $this->path . $this->get['param'][0] . '/' . $file;

                $files[] = [
                    'name' => $file,
                    'formattedName' => $formattedName,
                    'fullpath' => $fullpath,
                    'title' => $data[$formattedName]['title'],
                    'description' => $data[$formattedName]['description'],
                    'sort' => $data[$formattedName]['sort'],
                    'cover' => file_exists($this->getCoverName($fullpath)) ? $this->getCoverName($fullpath) : false
                ];
            }
        }

        if ($changed) {
            utils::write_in_file($this->path . $this->get['param'][0] . '/data' . ($lang ? '_' . $lang : '') . '.json', $data, 'json');
        }

        usort($files, function ($a, $b) {
            if ($a['sort'] === $b['sort']) {
                return 0;
            }
            return ($a['sort'] > $b['sort']) ? -1 : 1;
        });

        $this->render('downloads', 'editNode', [
            'node'=> $this->get['param'][0],
            'files'  => $files,
            'thumbs'=> $thumbs,
            'upload_dir'=> $this->path . $this->get['param'][0],
            'langs' => cfg::get('languages'),
            'translation' => $lang,
            'tmp_path' => TMP_DIR,
        ]);
    }

    public function saveData()
    {
        $json_file = $this->get['param'][0] . '/data' . ($this->get['param'][1] ? '_' . $this->get['param'][1] : '') . '.json';

        if (utils::write_in_file($json_file, $this->post, 'json')) {
            $ret = array('status' => 'success', 'text' => tr::get('download_node_updated'));
        } else {
            $ret = array('status' => 'error', 'text' => tr::get('download_node_not_updated'));
        }

        echo json_encode($ret);
    }

    /**
     * Adds a new file to the download node
     */
    public function add()
    {
        try {
            $node_name = $this->path . strtolower(str_replace(array(' ', "'", '"'), '_', $this->get['param'][0]));


            if (is_dir($node_name)) {
                throw new Exception(tr::get('download_node_exists'));
            }
            @mkdir($node_name, 0777, true);

            if (!is_dir($node_name)) {
                throw new Exception(tr::get('download_node_not_created'));
            }

            $msg['text'] = tr::get('download_node_created');
            $msg['status'] = 'success';
        } catch (Exception $e) {
            $msg['text'] = $e->getMessage();
            $msg['status'] = 'error';
        }
        echo json_encode($msg);
    }

    /**
     * Completeley removes a single file from the download node
     * @return string json response
     */
    public function deleteFile()
    {
        $warning = array();

        try {
            $file = $this->get['param'][0] . '/' . $this->get['param'][1];

            // Deletes file if exist
            if (file_exists($file)) {
                @unlink($file);

                if (file_exists($file)) {
                    throw new Exception(tr::get('img_not_deleted'));
                }
            }

            // Deletes cover, if exists
            if (file_exists($this->getCoverName($file))) {
                @unlink($this->getCoverName($file));
                if (file_exists($this->getCoverName($file))) {
                    array_push($warning, tr::get('file_deleted_cover_not_deleted'));
                }
            }

            // Load file information from data file, for each language
            $data_file[] = $this->get['param'][0] . '/data.json';


            if (is_array(cfg::get('languages'))) {
                foreach (cfg::get('languages') as $lng) {
                    $data_file[] = $this->get['param'][0] . '/data_' . $lng['id']. '.json';
                }
            }

            // Removes from all data files (different languages) the entry
            foreach ($data_file as $d_file) {
                if (file_exists($d_file)) {
                    $json = json_decode(file_get_contents($d_file), true);

                    $entry_id = str_replace('.', '__x__', $this->get['param'][1]);

                    unset($json[$entry_id]);

                    if (!utils::write_in_file($d_file, $json, 'json')) {
                        array_push($warning, tr::get('file_deleted_json_not_deleted'));
                    }
                }
            }

            if (!empty($warning)) {
                $ret['status'] = 'warning';
                $ret['text'] = implode('<br>', $warning);
            } else {
                $ret['status'] = 'success';
                $ret['text'] = tr::get('file_data_deleted');
            }
        } catch (Exception $e) {
            $ret['status'] = 'error';
            $ret['text'] = $e->getMessage();
        }

        echo json_encode($ret);
    }

    /**
     * Deletes all files in a node folder
     * @return string json response
     */
    public function deleteNode()
    {
        $error = utils::recursive_delete($this->get['param'][0]);

        if ($error) {
            $msg['status'] = 'error';
            $msg['text'] = tr::get('download_node_not_deleted');
            error_log(implode("\n", $error));
        } else {
            $msg['status'] = 'success';
            $msg['text'] = tr::get('download_node_deleted');
        }

        echo json_encode($msg);
    }


    public function deleteCover()
    {
        @unlink($this->get['param']['file']);

        if (file_exists($this->get['param']['file'])) {
            $resp = array('status' => 'error');
        } else {
            $resp = array('status' => 'success');
        }

        echo json_encode($resp);
    }


    /**
     * Moves uploaded cover from temporary directory to cover directory
     */
    public function addCover()
    {
        $destFile = $this->getCoverName($this->get['param']['path'] . DIRECTORY_SEPARATOR . $this->get['param']['refFile']);

        if (file_exists($destFile)) {
            @unlink($destFile);
        }

        @rename(TMP_DIR . '/' . $this->get['param']['tmpFile'], $destFile);

        if (file_exists($destFile)) {
            $resp = array('status' => 'success');
        } else {
            $resp = array('status' => 'error');
        }

        echo json_encode($resp);
    }

    /**
     * Calculates and returns cover name from file name
     * @param  string $file file (path and) name
     * @return string       cover (path and) name
     */
    public function getCoverName($file)
    {
        return pathinfo($file, PATHINFO_DIRNAME) .
            DIRECTORY_SEPARATOR .
            'covers' .
            DIRECTORY_SEPARATOR .
            pathinfo($file, PATHINFO_FILENAME) .
            '.jpg';
    }
}
