<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Feb 18, 2012
 */

interface frontend
{

	function mainSetter($get, $lang = false);
	
	function getContext();

	function getMeta($el, $html = false );
	
	function printMenu($menu_to_show, $show_sub = false );
	
	function printsubMenu($item, $show_sub = false);
	
	function printSection($section);
	
	function printArticleBody($art_id = false, $art_title = false);
	
	function printBody();
	
	function printSiblings();
	
	function printSearch();
	
	function printSearchResult();
	
	function printLangSelect($text = false);
	
}