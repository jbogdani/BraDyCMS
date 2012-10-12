<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Oct 12, 2012
 */
 
class file_ctrl
{
	public static function move_file($orig, $dest)
	{
		try
		{
			if (!copy($orig, $dest)){
				throw new MyExc('Can not copy ' . $orig . ' to ' . $dest);
			}
			if (!unlink($orig))
			{
				throw new MyExc('Can not delete ' . $orig);
			}
			
			$resp['status'] = 'success';
			$resp['text'] = 'Il file è stato spostato';
		}
		catch (MyExc $e)
		{
			$resp['status'] = 'error';
			$resp['text'] = 'Il file non è stato spostato (' . $e->getMessage() . ')';
		}
		
		echo json_encode($resp);
	}
	
	
	public static function erase($file)
	{
		$file = base64_decode($file);
		
		try
		{
			if ( is_file ( $file ) )
			{
				if ( !@unlink($file) )
				{
					throw new Exception("Nessuna è stato possibile cancellare il file: " . $file . "!");
				}
				else
				{
					$resp['status'] = 'success';
					$resp['text'] = "Il file è stato cancellato!";
				}
		
			}
			else if ( is_dir ( $file ) )
			{
				if ( !@rmdir($file) )
				{
					throw new Exception("Nessuna è stato possibile cancellare la cartella: " . $file . "!<br /> Controllare che la cartella sia vuota!");
				}
				else
				{
					$resp['status'] = 'success';
					$resp['text'] = "La cartalla è stata cancellata!";
				}
			}
			
		}
		catch(Exception $e)
		{
			$resp['status'] = 'error';
			$resp['text'] = $e->getMessage();
		}
		
		echo json_encode($resp);
	}
	
	
	public static function buildThumbs($dir)
	{
		try
		{
			if (!is_dir($dir . '/thumbs'))
			{
				if (!mkdir($dir . '/thumbs', 0777, true))
				{
					throw new MyExc('Can not create directory: ' . $dir . '/thumbs');
				}
				
				$files = utils::dirContent($dir);
				
				foreach ($files as $file)
				{
					$ftype = utils::checkMimeExt($dir . '/' . $file);
					
					if ($ftype[0] == 'image')
					{
						$cmd = '/usr/bin/convert -thumbnail 300x300 "' . $dir . '/' . $file . '" "' . $dir . '/thumbs/' . $file . '"';
						exec($cmd);
					}
				}
			}
			
			$resp['status'] = 'success';
			$resp['text'] = 'Le miniature sono state create';
			
		}
		catch(myExc $e)
		{
			$resp['status'] = 'error';
			$resp['text'] = $e->getMessage();
			$e->log();
		}
		echo json_encode($resp);
	}
}