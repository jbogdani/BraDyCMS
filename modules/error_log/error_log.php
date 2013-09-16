<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 11, 2012
 */
 
class error_log_ctrl
{
	private static $file = 'logs/error.log'; 

	public static function show()
	{
		$html = '<h2>Error log</h2>';
		
		if (file_exists(self::$file) AND filesize(self::$file) > 0)
		{
			$html .= '<div class="btn-group">' .
					  '<a class="btn btn-default log-reload"><i class="glyphicon glyphicon-refresh"></i> Reload</a>' .
					  '<a class="btn btn-default log-delete"><i class="glyphicon glyphicon-trash"></i> Delete</a>' .
					  '</div>' .
					  '<hr />';
		
			$handle = fopen(self::$file, 'r');
			$contents = fread($handle, filesize(self::$file));
			fclose($handle);
		
			$html .= '<pre>' . nl2br($contents) . '</pre>';
			
		}
		else
		{
			$html .= '<div class="text-success"><i class="glyphicon glyphicon-ok"></i> Error log is empty</div>';
			
		}
		
		echo $html;
		?>
			<script>
				$('.log-reload').click(function(){
					admin.tabs.reloadActive();
				});
				$('.log-delete').click(function(){
					$.get('controller.php?obj=error_log_ctrl&method=delete', function(data){
						admin.message(data.text, data.status);
						if (data.status == 'success'){
							admin.tabs.closeActive('error_log/show');
						}
					}, 'json');
				});
			</script>
		<?php
		
	}
	
	
	public static function delete()
	{
		$handle = @fopen(self::$file, 'w+');
		if ($handle)
		{
			fclose($handle);
			$out['status'] = 'success';
			$out['text'] = 'Error-log deleted';
		}
		else
		{
			$out['status'] = 'error';
			$out['text'] = 'Error! Error-log not deleted';
		}
		
		echo json_encode($out);
	}
}