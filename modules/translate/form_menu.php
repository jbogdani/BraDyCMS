<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Nov 1, 2011
 */


$trans = new translate();

$res = $trans->get_translation('menu', $_GET['id'], $_GET['lang']);

$res = $res[0];

if ($res):
?>
<form action="javascript:void(0)" id="translate_menu">
	<table style="width: 100%;">
		<thead>
			<tr>
				<th style="width: 100px;"></th>
				<th style="width: 300px;">Originale</th>
				<th style="width: 50px;">&nbsp;</th>
				<th>Traduzione</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="tb">Stato</th>
				<td class="tb">&nbsp;</td>
				<td class="tb">&nbsp;</td>
				<td class="tb"><select name="translated">
					<option value="0" <?php if ($res['t_translated'] == 0) echo ' selected="selected" '; ?>>non finito</option>
					<option value="1" <?php if ($res['t_translated'] == 1) echo ' selected="selected" '; ?>>tradotto</option>
				</select></td>
			</tr>
			<tr>
				<th class="tb">ID</th>
				<td colspan="3" class="tb"><?php echo $res['o_id'];?>
				</td>
			</tr>
			<tr>
				<th class="tb">Testo</th>
				<td class="tb"><?php echo $res['o_item'];?></td>
				<td class="tb"><button class="copy" type="button">&gt;&gt;</button>
				</td>
				<td class="tb"><input type="text" name="item"
					value="<?php echo $res['t_item'];?>" /></td>
			</tr>
			<tr>
				<th class="tb">Title</th>
				<td class="tb"><?php echo $res['o_title'];?></td>
				<td class="tb"><button class="copy" type="button">&gt;&gt;</button>
				</td>
				<td class="tb"><input type="text" name="title"
					value="<?php echo $res['t_title'];?>" /></td>
			</tr>
		</tbody>
	</table>

	<p>
		<button type="submit">Salva</button>
		<button type="reset">Annulla</button>
	</p>
</form>
<script type="text/javascript">
<!--
$('#translate_menu').submit(function(){
	$.post(
			'loader.php?nw=1&mod=translate/action2json&a=save&context=menu&lang=<?php echo $_GET['lang']; ?>&id=<?php echo $res['o_id'] . ($res['t_id'] ? '&t_id=' . $res['t_id'] : ''); ?>',
			$('#translate_menu').serialize(),
			function(data){
				$().toastmessage('showToast', {text: data.text,type: data.type});

				if (data.type == 'success'){
					menu.translate.form('menu', '<?php echo $res['o_id']; ?>', '<?php echo $_GET['lang']; ?>');
				}
			},
			'json'
		);
});

$('td.tb, th.tb')
	.attr('valign', 'top')
	.css('border-bottom', '1px solid #ccc')
	.css('padding', '10px');
	
$('button').button();

$('button.copy').click(function(){
	$(this).parent().next().find(':input').val($(this).parent().prev().html());
});
//-->
</script>
<?php
endif;