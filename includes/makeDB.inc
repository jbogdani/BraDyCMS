<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			15/feb/2012
 */

class makeDB
{
	/**
	 * 
	 * Returns SQL test for mysql tables
	 * @param string $prefix	tables prefix
	 * @param array $lang		language array
	 */
	public function make_mysql($prefix, $lang = false)
	{
		$sql = <<<EOD
			CREATE TABLE IF NOT EXISTS `"$prefix"__articles` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) NOT NULL,
			  `text_id` varchar(100) NOT NULL,
			  `sort` int(11) NOT NULL,
			  `summary` varchar(255) NOT NULL,
			  `text` longtext NOT NULL,
			  `keywords` varchar(255) NOT NULL,
			  `author` varchar(200) NOT NULL,
			  `status` tinyint(1) NOT NULL,
			  `section` varchar(100) NOT NULL,
			  `created` date NOT NULL,
			  `publish_on` date NOT NULL,
			  `expires` date NOT NULL,
			  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`),
			UNIQUE KEY `text_id_no_dupl` (`text_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;


			CREATE TABLE IF NOT EXISTS `"$prefix"__menu` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `item` varchar(100) NOT NULL,
			  `href` varchar(255) NOT NULL,
			  `target` varchar(20) NOT NULL,
			  `title` varchar(200) NOT NULL,
			  `menu` varchar(100) NOT NULL,
			  `sort` int(11) NOT NULL,
			  `subof` varchar(100) NOT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
EOD;

		if ($lang AND is_array($lang))
		{
			foreach($lang as $l)
			{
				$sql .= <<<EOD
					
					CREATE TABLE IF NOT EXISTS `"$prefix"__articles_"$l"` (
					  `id` int(10) NOT NULL AUTO_INCREMENT,
					  `id_art` int(11) NOT NULL,
					  `translated` tinyint(1) NOT NULL DEFAULT '1',
					  `title` varchar(255) NOT NULL,
					  `summary` varchar(255) NOT NULL,
					  `text` longtext NOT NULL,
					  `keywords` varchar(255) NOT NULL,
					PRIMARY KEY (`id`),
					UNIQUE KEY `id_art` (`id_art`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
		
					
					CREATE TABLE IF NOT EXISTS `"$prefix"__menu_"$l"` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `id_menu` int(11) NOT NULL,
					  `translated` tinyint(1) NOT NULL DEFAULT '1',
					  `item` varchar(100) NOT NULL,
					  `title` varchar(2200) NOT NULL,
					PRIMARY KEY (`id`),
					UNIQUE KEY `id_menu` (`id_menu`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
EOD;
			}
		}
	}
	
	/**
	 * 
	 * Returns SQL test for sqlite tables
	 * @param string $prefix	tables prefix
	 * @param array $lang		language array
	 */
	public function make_sqlite($prefix, $lang)
	{
		$sql = <<<EOD
			CREATE TABLE "$prefix"__articles	(
				id INTEGER PRIMARY KEY AUTOINCREMENT, 
				title varchar(255), 
				text_id varchar(100) UNIQUE, 
				sort int(11), 
				summary varchar(255), 
				text longtext, 
				keywords varchar(255), 
				author varchar(200), 
				status tinyint(1), 
				section varchar(100), 
				created date, 
				publish_on date, 
				expires date, 
				updated timestamp DEFAULT CURRENT_TIMESTAMP
			);

		CREATE TABLE "$prefix"__menu (
				id INTEGER PRIMARY KEY AUTOINCREMENT, 
				item varchar(100), 
				href varchar(255), 
				target varchar(20), 
				title varchar(200), 
				menu varchar(100), 
				sort int(11), 
				subof varchar(100)
			);
EOD;
		if ($lang AND is_array($lang))
		{
			foreach($lang as $l)
			{
				$sql .= <<<EOD
				CREATE TABLE "$prefix"__articles_"$l" (
					id INTEGER PRIMARY KEY, 
					id_art int(11) UNIQUE, 
					translated tinyint(1), 
					title varchar(255), 
					summary varchar(255), 
					text longtext, 
					keywords varchar(255)
				);
				
				CREATE TABLE "$prefix"__menu_"$l" (
					id INTEGER PRIMARY KEY, 
					id_menu int(11), 
					translated tinyint(1), 
					item varchar(100), 
					title varchar(200)
				);
EOD;
			}
		}
		return $sql;
	}
}



?>