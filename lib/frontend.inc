<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Feb 18, 2012
 */

interface frontend
{

	/**
	 *
	 * Gets GET data and populates $out with all context information
	 * @param array $get 	art | art_title | section
	 * @param string $lang	user language
	 */
	function mainSetter($get, $lang = false);
	
	/**
	 *
	 * Returns context (home | search | article | section)
	 */
	function getContext();
	
	/**
	 * 
	 * Returns section name, if available
	 */
	function getSection();

	/**
	 *
	 * Returns meta information about site, article or section blog
	 * @param string $el meta element (title | description (or summary) | keywords)
	 * @param boolean $html		if true html element will be returned
	 */
	function getMeta($el, $html = false );
	
	/**
	 *
	 * Echoes HTML menu forr $menu
	 * for html structure see documentation of recursive_print_menu_items
	 * @param string $menu	menu name to print
	 * @param boolean $show_sub	boolean check to controle printing of submenus
	 * @param array array containing tags to add to ul element
	 */
	function printMenu($menu_to_show, $show_sub = false, $tags = false );
	
	/**
	 *
	 * Echoes HTML submenu for $item
	 * for html structure see documentation of recursive_print_menu_items
	 * @param string $item	item menu to print submenu for
	 * @param boolean $show_sub	boolean check to controle printing of submenus
	 */
	function printsubMenu($item, $show_sub = false);
	
	/**
	 *
	 * Echoes list of articles for given section
	 * HTML structure:
	 * 		article > h1 (title)
	 * 				> div [.content] > text (summary)
	 * 				> div[.read_more] > a > text (read more)
	 * @param string $section section name to retrieve articles for
	 */
	function printSection($section);
	
	
	/**
	 *
	 * Echoes a single article specified as uid or title
	 * @param int $art_id			article's uid
	 * @param string $art_title		article's uniqui text title
	 */
	function printArticleBody($art_id = false, $art_title = false);
	
	/**
	 *
	 * Echoes page main content, depending on context (section or article)
	 */
	function printBody();
	
	/**
	 *
	 * Echoes section of article preview list with current article siblings
	 */
	function printSiblings();
	
	/**
	 *
	 * Echoes HTML for search form
	 * HTML structure
	 * 		form > input[.search, name=search, type=text]
	 */
	function printSearch();
	
	/**
	 *
	 * Prints out a list of items matching the searched term
	 */
	function printSearchResult();
	
	/**
	 * 
	 * Prints a html div > a  element for language selection 
	 * @param boolean $text flag to check if text will show up or only flags
	 * @param boolean $button	flag to check if lenu should be prented ad JQM button
	 */
	function printLangSelect($text = false, $button = false);
	
	/**
	 * prints a un.menu lang with language selection
	 * @param mixed $flag can be true | false | right | left
	 * @param boolean $no_text if true no text (only icon) will be shown
	 */
	function printLangMenu($flag = false, $no_text = false);
	
}