<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Nov 27, 2011
 */

class tableAction extends DB
{
	/**
	 * 
	 * Gets array of all data from table
	 * Returns array, false or throws MyExc on error
	 * @param string $tb	table name to retrieve data from
	 * @param string $where	WHERE filter, default 1
	 * @param string $order Ordering statement, default false
	 */
	public function getAllRows($tb, $where = false, $order = false)
	{
		$query = 'SELECT * FROM `' . PREFIX . '__' .  $tb . '` WHERE ' . ($where ? $where :  '1') . ' ' . $order;
		return $this->executeQuery($query, false, 'read');
	}	
	/**
	 * 
	 * Updates a row of table with array of post data 
	 * Returns true or false or throws MyExc on error
	 * @param string $tb	table name to retrieve data from
	 * @param array $post	array with data to write in db (fieldname=>fieldvalue; id must be present in aray)
	 */
	public function updateRow($tb, $post)
	{
		$id = $post['id'];
		unset($post['id']);
		
		foreach ($post as $key=>$val)
		{
			$values[':' . $key] = $val;
			$part[] = '`' . $key . '` = :' . $key;
		}
		 
		$query = 'UPDATE `' . PREFIX . '__' . $tb . '` SET ' . implode(',', $part) . ' WHERE `id` = ' . $id;
		return $this->executeQuery($query, $values);
	}
	
	/**
	 * 
	 * Deletes a row from table using id
	 * Returns true or false or throws MyExc on error
	 * @param string $tb	table name to retrieve data from
	 * @param int $id		id of row to delete
	 */
	public function deleteRow($tb, $id)
	{
		return $this->executeQuery('DELETE FROM `' . PREFIX . '__' .  $tb  . '` WHERE `id` = ' . $id);
	}
	
	/**
	 * 
	 * Inserts a new row in table using post data
	 * Returns last inserted id or throws MyExc on error
	 * @param string $tb	table name to retrieve data from
	 * @param array $post	posta data to write in database (fieldname=>fieldvalue)
	 */
	public function insertRow($tb, $post)
	{
		foreach ($post as $key=>$val)
		{
			$values[':' . $key] = $val;
		}
		$sql = 'INSERT INTO ' . PREFIX . '__' . $tb 
			. ' (`' . implode('`, `', array_keys($post)) . '`) '
			. ' VALUES '
			. ' (' . implode(',', array_keys($values)) . ')';
			
		return $this->executeQuery($sql, $values, 'id');
	}
	
	/**
	 * 
	 * Gets total of rows present in table
	 * @param string $tb table name
	 */
	public function getTotal($tb)
	{
		$res = $this->executeQuery('SELECT count(*) as `total` FROM `' . PREFIX . '__' . $tb . '` WHERE 1', false, 'read');
		return $res[0]['total'];
	}
	
	/**
	 * 
	 * Returns array with table columns description.
	 * If fieldname is true onflu column names will be returned
	 * @param string $tb table name
	 * @param boolean $fieldname flag to control if all data or only field names will be returned
	 */
	public function getTableMetadata($tb, $fieldname = false)
	{
		$res = $this->executeQuery('DESCRIBE `' . PREFIX . '__' . $tb . '`', false, 'read');
		
		if ($fieldname)
		{
			foreach ($res as $arr)
			{
				$ret[] = $arr['Field'];
			}
			return $ret;
		}
		else
		{
			return $res;
		}
	}
	
	/**
	 * 
	 * Exports table content in XLS format
	 * @param string $tb table name
	 * @param string $filename full path to XLS file
	 * @throws MyExc
	 */
	public function export2XLS($tb, $filename = false)
	{
		$res = $this->executeQuery('SELECT * FROM `' . PREFIX . '__' . $tb .  '` WHERE 1', false, 'read');
		if (count($res)> 0)
		{
			$fields = array_keys($res[0]);
			
			$xls = new ExcelWriter();
			
			$xls->writeLine($fields);
			
			foreach ($res as $data_arr)
			{
				$xls->writeLine($data_arr);
			}
			$xls->close($filename);
		}
		else
		{
			throw new MyExc('La tabella '. $tb . ' è vuota');
		}
	}
	
	
	
	public function importXLS($tb, $filename)
	{
		if (!file_exists($filename))
		{
			throw new MyExc('Il file ' . $filename . ' non esiste!');
		}
		require_once 'includes/XLS/my_reader.inc';
		
		$data = new myReader();
		$data->setUTFEncoder('iconv');
		$data->setOutputEncoding('UTF-8');
		$data->read( $filename );
		$data->sheets[0]['cells'] = $data->recursive_iconv( $data->sheets[0]['cells'] );

		
		$xls_cols = $data->sheets[0]['cells'][1];
		
		$db_cols = $this->getTableMetadata($tb, true);
		
		if (array_values($xls_cols) != array_values($db_cols))
		{
			throw new MyExc('I campi del foglio XLS non corrisponde ai campi del database');
		}
		
		// loop throw rows
		for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++)
		{
			unset($data_arr);
			
			for ( $col = 1; $col <= $data->sheets[0]['numCols']; $col++ )
			{
				$data_arr[$data->sheets[0]['cells'][1][$col]] = $data->sheets[0]['cells'][$i][$col];
			}
			
			$exists = $this->getAllRows($tb, '`id`=' . $data_arr['id']);
			
			if (!$exists[0])
			{
				$this->insertRow($tb, $data_arr);
			}
			else 
			{
				$this->updateRow($tb, $data_arr);
			}
		}
		
	}
}