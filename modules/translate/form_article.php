<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Nov 1, 2011
 */


$trans = new translate();

$res = $trans->get_translation('article', $_GET['id'], $_GET['lang']);

$res = $res[0];

if ($res):
$uid = str_replace('.', '', microtime(1));
?>
<form action="javascript:void(0)" id="translate_art">
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
				<td colspan="3" class="tb"><?php echo $res['o_id'];?></td>
			</tr>
			<tr>
				<th class="tb">Titolo</th>
				<td class="tb"><?php echo $res['o_title'];?></td>
				<td class="tb"><button class="copy" type="button">&gt;&gt;</button>
				</td>
				<td class="tb"><input type="text" name="title"
					value="<?php echo $res['t_title'];?>" /></td>
			</tr>
			<tr>
				<th class="tb">Parole chiave</th>
				<td class="tb"><?php echo $res['o_keywords'];?></td>
				<td class="tb"><button class="copy" type="button">&gt;&gt;</button>
				</td>
				<td class="tb"><input type="text" name="keywords"
					value="<?php echo $res['t_keywords'];?>" /></td>
			</tr>
			<tr>
				<th class="tb">Sommario</th>
				<td class="tb"><?php echo $res['o_summary'];?></td>
				<td class="tb"><button class="copy" type="button">&gt;&gt;</button>
				</td>
				<td class="tb"><textarea style="width: 100%; height: 200px;"
						name="summary" id="summary<?php echo $uid; ?>">
						<?php echo $res['t_summary'];?>
					</textarea></td>
			</tr>
			<tr>
				<th class="tb">Testo</th>
				<td class="tb"><?php echo $res['o_text'];?></td>
				<td class="tb"><button class="copy" type="button">&gt;&gt;</button>
				</td>
				<td class="tb"><textarea style="width: 100%; height: 500px;"
						name="text" id="text<?php echo $uid; ?>">
						<?php echo $res['t_text'];?>
					</textarea></td>
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
$('#translate_art').submit(function(){
	$.post(
			'loader.php?nw=1&mod=translate/action2json&a=save&context=article&lang=<?php echo $_GET['lang']; ?>&id=<?php echo $res['o_id'] . ($res['t_id'] ? '&t_id=' . $res['t_id'] : ''); ?>',
			$('#translate_art').serialize(),
			function(data){
				$().toastmessage('showToast', {text: data.text,type: data.type});

				if (data.type == 'success'){
					menu.translate.form('article', '<?php echo $res['o_id']; ?>', '<?php echo $_GET['lang']; ?>');
				}
			},
			'json'
		);
});

tinyMCE.execCommand("mceAddControl", true, "summary<?php echo $uid; ?>");
tinyMCE.execCommand("mceAddControl", true, "text<?php echo $uid; ?>");



$('td.tb, th.tb')
	.attr('valign', 'top')
	.css('border-bottom', '1px solid #ccc')
	.css('padding', '10px');
	
$('button').button();

<?php //TODO: i bottoni non funczionano più con tinymce!!! ?>
$('button.copy').click(function(){

	console.log($(this).parent().next().find(':input'));
	$(this).parent().next().find(':input').val($(this).parent().prev().html());
});
//-->
</script>
<?php
endif;