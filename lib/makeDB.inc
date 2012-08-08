<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			15/feb/2012
 */

class makeDB
{
	private $sql, $driver;
	
	private function getArticlesFields()
	{
		$fields = array(
			//field_name => array(0-type, 1-length, 2-is_null? 3-default 4-is_autoincrement? 5-is_primary)
			'id'		=> array( 'INTEGER', false, false, false, true, true),
			'title'		=> array('varchar', 255),
			'text_id'	=> array('varchar', 100),
			'sort'		=> array('int', 11),
			'summary'	=> array('varchar', 255),
			'text'		=> array('longtext'),
			'keywords'	=> array('varchar', 255),
			'author'	=> array('varchar', 200),
			'status'	=> array('tinyint', 1),
			'section'	=> array('varchar', 100),
			'created'	=> array('date'),
			'publish_on'=> array('date'),
			'expires'	=> array('date'),
			'expires'	=> array('date'),
			'updated'	=> array('timestamp', false, false, 'CURRENT_TIMESTAMP')
		);
		return $fields;
	}
	
	private function getTraslationArticleFields()
	{
		$fields = array(
			'id'		=> array( 'INTEGER', false, false, false, true, true),
			'id_art'	=> array('int', 11),
			'translated'=> array('int', 1),
			'title'		=> array('varchar', 255),
			'summary'	=> array('varchar', 255),
			'text'		=> array('longtext'),
			'keywords'	=> array('varchar', 255)
		);
		return $fields;
	}
	
	private function getMenuFields()
	{
		$fields = array(
			//field_name => array(type, length, is_null? default is_autoincrement?)
			'id'		=> array( 'INTEGER', false, false, false, true, true),
			'item'		=> array('varchar', 100),
			'href'		=> array('varchar', 255),
			'target'	=> array('varchar', 20),
			'title'		=> array('varchar', 200),
			'menu'		=> array('varchar', 100),
			'sort'		=> array('int', 11),
			'subof'		=> array('int', 11, true)
		);
		return $fields;
	}
	
	private function getTranslationMenuFields()
	{
		$fields = array(
			'id'		=> array( 'INTEGER', false, false, false, true, true),
			'id_menu'	=> array('int', 11),
			'translated'=> array('int', 1),
			'item'		=> array('varchar', 100),
			'title'		=> array('varchar', 200)
		);
		return $fields;
	}
	
	/**
	 * 
	 * Builds array of sql statement
	 * Returns main object (chainable)
	 * @param string $prefix		tables' prefix
	 * @param string $driver		one of: mysql || sqlite
	 * @param string $table			one of: all || article || translation_article || menu || translation_menu
	 * @param string $lang			translation language
	 */
	public function buildSQL($prefix, $driver, $table, $lang = false)
	{
		$this->driver = $driver;
		
		switch($table)
		{
			case 'all':
				$tb['articles'] = $this->getArticlesFields();
				$tb['menu'] = $this->getMenuFields();
				
				if ($lang)
				{
					$tb['articles_' . $lang] = $this->getTraslationArticleFields();
					$tb['menu_' . $lang] = $this->getTranslationMenuFields();
				}
				break;
			
			case 'article':
				$tb['articles'] = $this->getArticlesFields();
				break;
				
			case 'menu':
				$tb['menu'] = $this->getMenuFields();
				break;
				
			case 'translation_article':
				if ($lang)
				{
					$tb['articles_' . $lang] = $this->getTraslationArticleFields();
				}
				break;
				
			case 'translation_menu':
				if ($lang)
				{
					$tb['menu_' . $lang] = $this->getTranslationMenuFields();	
				}
				break;
		}
		
		if (is_array($tb))
		{
			foreach ($tb as $tb_name => $tb_fields)
			{
				switch($driver)
				{
					case 'mysql':
						$sql_fields = array();
						foreach ($tb_fields as $name => $details)
						{
							//field_name => array(0-type, 1-length, 2-is_null? 3-default 4-is_autoincrement? 5-is_primary)
							$sql_fields[] = '`' . $name . '` ' 
								. $details[0] 
								. ($details[1] ? '(' . $details[1] . ') ' : ' ')
								. ($details[2] ? ' NULL ' : ' NOT NULL ')
								. ($details[4] ? ' AUTO_INCREMENT ' : '')
								. ($details[5] ? ' PRIMARY KEY ' : '')
								. ($details[3] ? ' DEFAULT ' . $details[3] . ' ' : '');
						}
						$sql[] = 'CREATE TABLE `' . $prefix . '__' . $tb_name. "` (\n" . implode(", \n", $sql_fields) . "\n) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;";
						break;
						
					case 'sqlite':
						$sql_fields = array();
						foreach ($tb_fields as $name => $details)
						{
							$sql_fields[] = $name . ' ' 
								. $details[0] 
								. ($details[1] ? '(' . $details[1] . ')' : '')
								. ($details[5] ? ' PRIMARY KEY' : '')
								. ($details[3] ? ' DEFAULT ' . $details[3] : '');
						}
						$sql[]= 'CREATE TABLE ' . $prefix . '__' . $tb_name. " (\n" . implode(", \n", $sql_fields) . "\n);";
						break;
				}
			}
			$this->sql = $sql;
			
			return $this;
		}
	}
	
	/**
	 * 
	 * Returns sql statement as array or as string
	 * @param boolean $as_string	if true an imploded string will be returned
	 */
	public function getSQL($as_string = false)
	{
		if (!$this->sql OR !is_array($this->sql))
		{
			return false;
		}
		
		return $as_string ? implode("\n\n", $this->sql) : $this->sql;
	}
	
	public function executeAll()
	{
		if (!$this->sql OR !is_array($this->sql))
		{
			return false;
		}
		
		switch($this->driver)
		{
			case 'sqlite':
				return $this->createSqlite();
				break;
				
			case 'mysql':
				return $this->createMysql();
				break;
				
			default:
				return false;
				break;
				
		}
	}
	
	private function createMysql()
	{
		// TODO: finire!
		return $this->getSQL(true);
	}
	/**
	 * 
	 * Creates a new sql database, named tmp.sql with table structure
	 */
	private function createSqlite()
	{
		$pdo = new PDO('sqlite:' . ROOT . 'sites/default/cfg/tmp.sqlite' , false, false, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		
		$pdo->beginTransaction();
		
		foreach($this->sql as $query)
		{
			$stmt = $pdo->prepare($query);
			$ret[] = $stmt->execute();	
		}
		
		$pdo->commit();
		
		return $ret;
			
	}

}



?>