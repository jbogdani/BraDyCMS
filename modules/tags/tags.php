<?php

/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since        Jan 2, 2015
 */

class tags_ctrl extends Controller
{

  public function manage()
  {
    $this->render('tags', 'list', array(
      'tags' => Tag::getAll()
    ));
  }

  /**
   * Adds a new tag in the database and returns formatted JSON
   * @param string $title Tag title
   */
  public function addTag($title = false)
  {
    if(!$title) $title = $this->get['param'][0];

    if (R::count( 'tag', ' title = ? ', [ $title ] ) > 0)
    {
      echo $this->responseJson('error', tr::sget('tag_already_used', $title));
    }
    else
    {
      echo Tag::add($title) ?
        $this->responseJson('success', tr::get('tag_added')) :
        $this->responseJson('error', tr::get('tag_not_added'));
    }
  }

  /**
   * Removes tag and returns formatted JSON response
   * @param int $tagId ID of tag to be removed
   */
  public function removeTag($tagId = false)
  {
    if(!$tagId) $tagId = $this->get['param'][0];


    echo Tag::delete($tagId) ?
        $this->responseJson('success', tr::get('tag_deleted')) :
        $this->responseJson('error', $e->getMessage());
  }

  /**
   * Renames tag and returns formatted JSON response
   * @param int $tagId ID of tag to be renamed
   * @param string $title New tag title
   */
  public function renameTag($tagId = false, $title = false)
  {
    if(!$tagId) $tagId = $this->get['param'][0];
    if(!$title) $title = $this->get['param'][1];

    if (R::count( 'tag', ' title = ? ', [ $title ] ) > 0)
    {
      echo $this->responseJson('error', tr::sget('tag_already_used', $title));
    }
    else
    {
      echo Tag::rename($title, $tagId) ?
        $this->responseJson('success', tr::get('tag_renamed')):
        $this->responseJson('error', tr::get('tag_not_renamed'));
    }
  }
}
?>
