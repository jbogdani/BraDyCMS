<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Oct 30, 2011
 */

class ArticleEdit extends ArticleRead{


	/**
	 *
	 * Updates article with given ID with array od $data
	 * @param int $id		id of article to update
	 * @param array $data	array of data to write
	 */
	public function update($id, $data){

		$flds = $this->getArticleFields();

		foreach ( $flds as $fld )
		{
			$stat[] = "`{$fld}` = :{$fld}";
				
			if ($fld != 'updated')
			{
				$values[':' . $fld] = $data[$fld];
			}
		}
		$statement = "UPDATE `" . PREFIX . "__articles` SET "
		. implode(', ', $stat)
		. ", `updated` = date('now')" 
		. " WHERE `id`	= :id";
		

		$values[':id'] = $id;
		return $this->executeQuery( $statement, $values );
	}

	/**
	 *
	 * Add new article using array of $data
	 * @param array $data array of data to write in DB
	 */
	public function add ( $data ) {

		$flds = $this->getArticleFields();

		$apices[] = '`id`';
		$values[':id'] = '';

		foreach ( $flds as $fld ){
			$apices[] = '`' . $fld . '`';
			$values[':' . $fld] = $data[$fld];
		}


		$statement = "INSERT INTO `" . PREFIX . "__articles` "
		. "(" . implode(',', $apices). ")"
		. " VALUES "
		. "(" . implode ( ',', array_keys ( $values ) ) . ")";

		return $this->executeQuery($statement, $values, 'id' );

	}

	/**
	 *
	 * Deletes article from DB
	 * @param int $id	article id
	 */
	public function delete( $id ){

		global $cfg;

		$statement = "DELETE FROM `" . PREFIX . "__articles` WHERE `id` = " . $id;
		$ret = $this->executeQuery($statement);

		foreach ($cfg['languages'] as $l=>$lang)
		{
			$this->executeQuery("DELETE FROM `" . PREFIX . "__articles_" . $l . "` WHERE `id_art` = " . $id);
		}
		return $ret;
	}
}