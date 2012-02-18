<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Nov 27, 2011
 */

require_once 'lib/class.tableAction.inc';

$tbA = new tableAction();

$count = $tbA->getTotal($_GET['tb']);


if ( $count == 0 )
{
	echo '<h3>Nessun record trovato nella tabella ' . strtoupper($_GET['tb']) . '</h3>';
}
else
{
	echo '<h3>Tabella ' . strtoupper($_GET['tb']) . '</h3>';
}

$col_names = $tbA->getTableMetadata($_GET['tb'], 1);

?>
<table style="width:100%">
	<tr>
		<td style="vertical-align: top">
			<table id="list"> </table>
			<div id="pager"> </div>
		</td>
		<td style="vertical-align: top">
			<button id="exportXlS">Esporta dati in formato XLS</button>
			<div style="padding-top: 10px;">
				<div id="XLSuploader"></div>
			</div>
		</td>
	</tr>
</table>
<script>
$('#list').jqGrid({
	url:'loader.php?nw=1&mod=tables/sql2json&tot=<?php echo $count; ?>&tb=<?php echo $_GET['tb']; ?>',
	datatype: 'json',
	colNames:['<?php echo implode("','", str_replace("'", "\'", $col_names ) ); ?>'],
	colModel:[
	      	<?php
	      	foreach ( $col_names as $id) {
	      		$tmp_arr[] = "{name:'{$id}',index:'{$id}', " . ($id == 'id' ? '' : 'editable:true,' ) . " width:" . ($id == 'id' ? '25' : '250') . "}";
	      	}
	      	echo implode ( ', ', $tmp_arr );
	      	?>
	          ],
	  rowNum:20,
	  pager: '#pager',
	  sortname: 'id',
	  viewrecords: true,
	  sortorder: "asc",
	  jsonReader: { repeatitems : false, id: "0" },
	  editurl:"loader.php?nw=1&mod=tables/edit&tb=<?php echo $_GET['tb']; ?>"
	});
$("#list").jqGrid(
		'navGrid',
		'#pager',
		{
			edit:true,
			add:true,
			del:true
		},
		{// edit options
			afterComplete:function(response){
				$().toastmessage('showToast', {
					text     : (response.responseText == 'success' ? 'Aggiornamento effettuato con successo' : 'Aggiornamento non effettuato. Consulta il log degli errori per maggiori info'),
				 	type     : response.responseText,
				 });
								
				},
				mtype:'POST',
					
				closeAfterEdit: true
		},
		{// add options
			afterComplete:function(response){
				$().toastmessage('showToast', {
					text     : (response.responseText == 'success' ? 'Aggiornamento effettuato con successo' : 'Aggiornamento non effettuato. Consulta il log degli errori per maggiori info'),
				 	type     : response.responseText,
				 });
								
				},
				mtype:'POST',
				closeAfterAdd: true
		},
		{// delete options
			afterComplete:function(response){
				$().toastmessage('showToast', {
					text     : (response.responseText == 'success' ? 'Aggiornamento effettuato con successo' : 'Aggiornamento non effettuato. Consulta il log degli errori per maggiori info'),
				 	type     : response.responseText,
				 });
								
				},
				mtype:'POST',
				closeAfterAdd: true
		}
		); 

$('#exportXlS').click(function(){
	$.get(
			'loader.php?nw=1&mod=tables/export&tb=<?php echo $_GET['tb']?>',
			function(data)
				{
				if (data == 'error')
					{
					$().toastmessage('showToast', {
						text     : 'Non è stato possibile esportare la tabella. Potrebbe essere vuota? Per maggiori informazioni si prega di consultare il log degli errori.',
					 	type     : 'error',
					 });
					 
					}
				else
					{
					window.open(data);
					}
			});
});
new qq.FileUploader({
	element: $('#XLSuploader')[0],
	params: {
		upload_dir: '<?php echo TMP_DIR; ?>'
	},
	allowedExtensions: ['xls', 'XLS'],
	action: 'modules/upload.php',
	sizeLimit: 0,
	minSizeLimit: 0,
	onComplete: function(id, fileName){
		$.get(
				'loader.php?nw=1&mod=tables/import&tb=<?php echo $_GET['tb'];?>&file=<?php echo TMP_DIR; ?>' + fileName,
				function(data){
					$("#list").trigger("reloadGrid");
					$().toastmessage('showToast', {
						text     : data.verbose,
					 	type     : data.status,
					 });
					 
				},
				'json'
				);
	}
});


$('button').button();
</script>
