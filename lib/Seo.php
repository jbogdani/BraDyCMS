<?php
/**
 * Performs CRUD operatins on Seo table. The field url will be considered main key field
 * Structure of seo table:
 * 	- id
 * 	- url (unique)
 * 	- title
 * 	- description
 * 	- keywords
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      April 10, 2016
 * @uses      Redbean
 */



class Seo
{
    /**
     * Backward compatibility function: creates table seo if not present in the database
     */
    public static function createIfNotExists()
    {
        R::exec("CREATE TABLE IF NOT EXISTS seo ("
      . "id INTEGER PRIMARY KEY AUTOINCREMENT, "
      . "url VARCHAR( 255 ) UNIQUE, "
      . "title VARCHAR( 255 ), "
      . "description VARCHAR( 255 ), "
      . "keywords VARCHAR( 255 ))");

        R::exec("CREATE TABLE IF NOT EXISTS seotrans ("
      . "id INTEGER PRIMARY KEY AUTOINCREMENT, "
      . "lang VARCHAR (10), "
      . "url VARCHAR (255), "
      . "title VARCHAR (255), "
      . "description VARCHAR (255), "
      . "keywords VARCHAR (255));");
    }

    /**
     * Edits existing record (url) or adds new one
     * @param  int $id  id to edit
     * @param  array $data array of metadata
     * @return boolean       true on success, false on failure. Syntax errors will be logged
     */
    public static function edit($id = false, $data)
    {
        try {
            self::createIfNotExists();

            if ($data['lang']) {
                $seo = R::findOne('seotrans', 'url = ? AND lang = ?', [$data['url'], $data['lang']]);

                if (!$seo) {
                    $seo = R::dispense('seotrans');
                }
                $seo->lang = $data['lang'];
            } else {
                $seo = $id ? $seo = R::findOne('seo', 'id = ?', [$id]) : $seo = R::dispense('seo');
            }

            $seo->url = $data['url'];
            $seo->title = $data['title'];
            $seo->description = $data['description'];
            $seo->keywords = $data['keywords'];

            R::store($seo);
            return true;
        } catch (RedException\SQL $e) {
            error_log($e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Deletes metadata for existing record (url)
     * @param  int $id  Id to edit
     * @return boolean       true on success, false on failure. Syntax errors will be logged
     */
    public static function delete($id)
    {
        try {
            self::createIfNotExists();

            $seo = R::findOne('seo', 'id = ?', [$id]);
            if (!$seo) {
                return true;
            }
            // Delete translations if any
            $trans = R::find('seotrans', 'url = ?', [$seo->url]);
            if ($trans && is_array($trans)) {
                R::trashAll($trans);
            }
            R::trash($seo);
            return true;
        } catch (RedException\SQL $e) {
            error_log($e->getMessage);
            return false;
        }
    }

    /**
     * Returns array of data for existing record (url)
     * @param  string $url  url to edit
     * @param string $lang  translated language
     * @return array|false       array of data, false on failure. Syntax errors will be logged
     */
    public static function get($url, $lang = false)
    {
        try {
            self::createIfNotExists();

            $seo = $lang ? R::findOne('seotrans', 'url = ? AND lang = ?', [$url, $lang]) : R::findOne('seo', 'url = ?', [$url]);
            if (!$seo) {
                return false;
            }
            return $seo->export();
        } catch (RedException\SQL $e) {
            error_log($e->getMessage);
            return false;
        }
    }

    /**
     * Returns array of data for existing record (url)
     * @param  int $id  id to edit
     * @param string $lang  translated language
     * @return array|false       array of data, false on failure. Syntax errors will be logged
     */
    public static function getById($id, $lang = false)
    {
        try {
            self::createIfNotExists();

            $seo = R::findOne('seo', 'id = ?', [$id]);

            if (!$seo) {
                return false;
            }

            if ($lang) {
                $tr_seo = R::findOne('seotrans', 'url = ? AND lang = ?', [$seo->url, $lang]);
                if (!$tr_seo) {
                    return ['url' => $seo->url, 'lang' => $lang];
                } else {
                    return $tr_seo;
                }
            } else {
                return $seo->export();
            }
        } catch (RedException\SQL $e) {
            error_log($e->getMessage);
            return false;
        }
    }
}
