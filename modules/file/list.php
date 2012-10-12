<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Oct 31, 2011
 */

if ( !$_GET['upload_dir'] )
{
	$upload_dir = './sites/default/images';
}
else
{
	$upload_dir = preg_replace('/\/$/', null, $_GET['upload_dir']);

	if ( !is_dir($upload_dir) ) {

		$upload_dir = str_replace(array('-', "'", '"', ' ', ',', ';'), '_', $upload_dir);

		if ( !mkdir($upload_dir, 01777, 1) ){
			echo 'Errore! Non è stato possibile creare la cartella '. $upload_dir;
				
			$upload_dir = './sites/default/images';
		}
	}
}


if (preg_match('/sites\/default\/images\/\.\./', $upload_dir) ) {
	$upload_dir = './sites/default/images/';
}

$path_arr = explode('/', preg_replace("/^\.\/sites\/default\/images\/?/", null, $upload_dir));

$path = '';
$cons = '';

echo '<button type="button" onclick="menu.file.showall(\'./sites/default/images/' . $cons . '\')">.</button> /';
if ($path_arr[0])
{
	foreach ($path_arr as $sh)
	{
		$cons .= $sh . '/';
		echo '<button type="button" onclick="menu.file.showall(\'./sites/default/images/' . $cons . '\')">' . $sh . '</button> / ';
		$path[$sh] = $cons;
	}
}
?>

<div
	style="margin: 20px 0; border-bottom: 1px dotted #999; padding: 0 0 10px 0;">
	<input type="text" placeholder="Nome nuova cartella"
		style="width: 200px">
	<button style="margin: 0 10px;"
		onclick="menu.file.showall('<?php echo $upload_dir; ?>/' + escape($(this).prev().val()))">crea</button>
</div>

<div id="file-uploader"></div>
<script>
	new qq.FileUploader({
		element: $('#file-uploader')[0],
		params: {
			upload_dir: '<?php echo $upload_dir; ?>'
		},
		action: 'modules/upload.php',
		sizeLimit: 0,
		minSizeLimit: 0,
		onComplete: function(){
			menu.file.showall('<?php echo $upload_dir; ?>');
		}
	});
</script>

<?php
$files = utils::dirContent($upload_dir);

sort($files);

foreach ($files as $file)
{
	$html .= '<div class="img' . (is_file($upload_dir . '/'. $file ) ? ' draggable' : ' droppable') . '" data-path="' . $file . '">';
	
	if ( is_file($upload_dir . '/'. $file ) )
	{
		$ftype = utils::checkMimeExt($upload_dir . '/' . $file);
	
		if ($ftype[0] == 'image')
		{
			$img_src = $upload_dir . '/' . $file;
			$html .= '<div class="ui-widget-header">'  . $file . '</div>';
		}
		else
		{
			$img_src = './css/ftype_icons/' . $ftype[1];
		}
		$html .= '<img src="' . $img_src .'" width="80px;" /><br />'
		. 'url:<br />'
		. '<input type="text" value="' . $upload_dir . '/'. $file .'" style="font-size:0.6em;" onfocus="this.select();" /><br />';
	}
	else
	{
		$html .= '<div onclick="menu.file.showall(\''. $upload_dir . '/' . $file .'\')">'
		. '<img src="css/folder.png" />'
		. '<p class="path"><strong>' . basename($file) . '</strong></p>'
		. '</div>';
	}
	
	$html .=''
		. '<button onclick="menu.file.erase(\''. base64_encode($upload_dir . '/'. $file) .'\', \'' . $upload_dir . '\')"> cancella </button>'
		. '</div>';
	
}

$html .= '<div style="clear:both;"></div>';

echo $html;


?>
<script>
$('button').button();
$('div.draggable').draggable({revert:true});
$('div.droppable').droppable({
	accept: "div.draggable",
    activeClass: "ui-state-highlight",
    drop: function(event, ui){
		$(this).addClass( "ui-state-highlight")
		
		var file = ui.draggable.data('path');
		var newPath = $(this).data('path');

		$.get('loader.php?obj=file_ctrl&method=move_file&param[]=<?php echo $upload_dir; ?>/' + file + '&param[]=<?php echo $upload_dir; ?>/' + newPath + '/' + file, function(data){
			gui.message(data.text, data.status);
			if (data.status == 'success'){
				ui.draggable.remove();
			}
		},'json');
		
	}
});
</script>
