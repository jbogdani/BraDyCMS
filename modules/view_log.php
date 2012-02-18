<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Nov 22, 2011
 */

$filename = 'error.log';

if ($_GET['erase'] == true)
{
	$handle = fopen($filename, 'w+');
	fclose($handle);
}
?>
<button id="reload_log">Ricarica</button>
<button id="erase_log">Cancella</button>
<hr />
<?php
if (file_exists($filename) AND filesize($filename) > 0)
{
	$handle = fopen($filename, 'r');
	$contents = fread($handle, filesize($filename));
	fclose($handle);

	echo nl2br($contents);
}
else
{
	echo 'Il log degli errori è vuoto!';
}
?>
<script>
	$('button').button();
	$('#erase_log').click(function(){
		gui.openInTab('view_log', 'erase=true');
	});
	$('#reload_log').click(function(){
		gui.openInTab('view_log');
	});
</script>
