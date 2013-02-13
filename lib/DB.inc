<?php
/**
 * Main database connection class
 *
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 */

class DB {

	/**
	 *
	 * initialized database object
	 * @var object
	 */
	private $db;

	/**
	 *
	 * Parses connect.ini
	 * Opens connectio to database
	 */
	public function __construct()
	{
		// try to get mysql config (mysql is the default connection)
		if (file_exists(MAIN_DIR . 'sites/default/cfg/connect.ini'))
		{
			$conn_arr = parse_ini_file( MAIN_DIR . 'sites/default/cfg/connect.ini', true);

			if (in_array($_SERVER['HTTP_HOST'], array_keys($conn_arr)))
			{
				$conn_def_arr = $conn_arr[$_SERVER['HTTP_HOST']];
			}
			else if (is_array($conn_arr['%']))
			{
				$conn_def_arr = $conn_arr['%'];
			}
			else
			{
				throw Exception('Mancano i dati di connessione per l\'host: ' . $_SERVER['HTTP_HOST']);
			}
			
			$conn_string = 'mysql:host=' . $conn_def_arr['host'] . ';dbname=' . $conn_def_arr['db'];
		}
		
		// try to get sqlite config
		else if (file_exists(MAIN_DIR . 'sites/default/cfg/database.sqlite'))
		{
			$conn_string = 'sqlite:' . MAIN_DIR . 'sites/default/cfg/database.sqlite';
		}
		
		// no database found
		else
		{
			throw new Exception('Manca il file di configurazione del database per questo sito');
		}
		
		try
		{
			$this->db = new PDO($conn_string , $conn_def_arr['user'], $conn_def_arr['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
			return $this->db;
		}
			
		catch (PDOException $e)
		{
			throw new Exception($e);
		}
	}

	/**
	 *
	 * Prepares and executes $query in database using (if availabe) $values
	 * @param string $query		query string to prepare & execute
	 * @param array $values		array of values to use in execute statement
	 * @param string $type		(id | read | false) if read query result will be returned (read query); if id last inserted id will be returned; if false boolean will be returned
	 */
	public function executeQuery( $query, $values = false, $type = false )
	{
		if ( !$values ) $values = array();

		try
		{
			$sql = $this->db->prepare($query);
				
			$flag_ret = $sql->execute ( $values );
				
			$err = $sql->errorInfo();
				
			switch ($type)
			{
				case 'read':
					return $sql->fetchAll(PDO::FETCH_ASSOC);
					break;
						
				case 'id':
					return $this->db->lastInsertId();
					break;
				
				case false:
				default:
					return $flag_ret;
					break;
			}
				
		}
		catch (PDOException $e)
		{
			error_log($e->getMessage() . ' :: ' . $query . ' :: ' . var_export($values, 1) . ' :: ' . $type);
			throw new Exception($e);
		}
	}

	/**
	 *
	 * Returns html encoded values
	 * @param mixed $text string or array
	 */
	public function format_value( $text )
	{
		if ( is_array ( $text ) )
		{
			foreach ( $text as $k => $v )
			{
				$n_text[$k] = $this->format_value( $v );
			}
			return $n_text;
		}
		else
		{
			return htmlentities($text, ENT_QUOTES, 'UTF-8');
		}
	}
	
	public function commit()
	{
		$this->db->commit();
	}
	
	public function rollBack()
	{
		$this->db->rollBack();
	}
	
	public function beginTransaction()
	{
		$this->db->beginTransaction();
	}
	


}



?>