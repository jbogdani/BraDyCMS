<?php
/**
 *
 * Manages all actions to article tables (main and translations)
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			23/mag/2011
 */

class Article extends DB{

	/**
	 *
	 * Returns array with article fields names
	 */
	protected function getArticleFields(){

		return array(
			'title',
			'text_id',
			'sort',
			'summary',
			'text',
			'keywords',
			'author',
			'status',
			'section',
			'created',
			'publish_on',
			'expires',
			'updated'
			);
	}

}