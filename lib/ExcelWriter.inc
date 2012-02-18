<?php
/**
 * Class is used for save the data into microsoft excel format.
 * It takes data into array or you can write data column vise.
 * 
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @author			http://www.sean-barton.co.uk/tag/php-xls-class/
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			02/mar/2010
 */

Class ExcelWriter{
	
	private $xls_string = '';
	private $row = 0;
	private $col = 0;
				

	/**
	 * 
	 * Increases the row number, and sets column number to 0
	 */
	function end_row()
	{
		$this->row++;
		$this->col = 0;
	}
	

	/**
	 * 
	 * Write $value in a cell and increases the col number. The value can be a number or a string 
	 * @param mixed $value	int or string. Value to write in cell
	 */
	function cell($value)
	{
		$value = utf8_decode($value);
		
		if (is_numeric($value))
		{
			$this->xls_string .= pack("sssss", 0x203, 14, $this->row, $this->col, 0x0);
			
			$value = pack("d", $value);
		}
		else
		{
			$l = strlen($value);
			
			$this->xls_string .= pack("ssssss", 0x204, 8 + $l, $this->row, $this->col, 0x0, $l);
		}

		$this->xls_string .= $value;

		$this->col++;
	}
	
	
	/**
	 * 
	 * Packs binary file and writes content to filename.
	 * @throws MyExc
	 */
	function close($filename)
	{
		$content = pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
		
		$content .= $this->xls_string;
		
		$content .= pack("ss", 0x0A, 0x00);
		
		if ($filename)
		{
			if(!$hndl = fopen($filename, "w+"))
			{
				throw new MyExc("Cannot open " . $filename);
			}
			
			if(!fwrite($hndl,$content))
			{
				throw new MyExc("Cannot write in ".$filename);
			}
			
			if(!fclose($hndl))
			{
				throw new MyExc("Cannot close ".$filename);
			}
		}
		else
		{
			header('Content-Description: File Transfer');
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename=export.xls');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			ob_clean();
			flush();
			
			echo $content;
			
		}
		
	}
	
	
	
	/**
	 * 
	 * Writes an array to a line
	 * @param array $arr	array with data to write in line
	 */
	function writeLine($arr)
	{
		if(!is_array($arr))
		{
			throw new MyExc("Argument is not valid. Supply an valid Array");
		}
		foreach($arr as $data)
		{
			$this->cell($data);
		}
		$this->end_row();
	}
}
?>