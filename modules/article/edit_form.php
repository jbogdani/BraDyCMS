<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Oct 31, 2011
 */

//	function edit_article_form( $get = false, $post = false ) {

$id = $_REQUEST['id'];

$article_edit = new ArticleEdit();

if ($id)
{
	echo '<h2>Modifica articolo</h2>';

	$res = $article_edit->get_article_by_id($id, false, false, true);
	$res = $article_edit->format_value($res);
}
else
{
	echo '<h2>Aggiungi nuovo articolo</h2>';
}
$uid = str_replace('.', '', microtime(1));
?>

<form action="javascript:void(0)" id="edit_form">

	<table cellpadding="10" style="min-width: 1000px">
		<tr>
			<td style="vertical-align: top; width:300px;">
				<p>
				<?php echo $res['id'] ? 'ID:<strong>' . $res['id'] . '</strong>': ''; ?>
				</p>
				<p>
					Titolo<br /> <input type="text" name="title"
						value="<?php echo $res['title']; ?>">
				</p>
				<p>
					ID testuale<br /> <input type="text" name="text_id"
						value="<?php echo $res['text_id']; ?>">
				</p>
				<p>
					Ordine<br /> <input type="text" name="sort"
						value="<?php echo $res['sort']; ?>">
				</p>
				<p>
					Parole chiavi<br /> <input type="text" name="keywords"
						value="<?php echo $res['keywords']; ?>">
				</p>
				<p>
					Autore<br /> <input type="text" name="author"
						value="<?php echo $res['author']; ?>">
				</p>

				<p>
					Stato<br /> <select name="status">
					<?php
					$status_arr = array ('nascosto', 'visible');

					foreach ($status_arr as $index => $verb )
					{
						echo '<option value="' . $index . '"';
						if ( $res['status'] == $index) {
							echo ' selected="selected" ';
						}
						echo '>' . $verb . '</option>';
					}
					?>
					</select>
				</p>

				<p>
					Sezione<br /> <input type="text" name="section" id="section"
						value="<?php echo $res['section']; ?>">
						<datalist id="section-dl">
						<?php
						$sections_arr = $article_edit->get_used_sections();
						if ($sections_arr AND is_array($sections_arr))
						{
							echo '<option>' . implode('</option><option>', $sections_arr). '</option>';
						}
						
						?>
						</datalist>
				</p>
				<p>
					Data creazione<br /> <input class="date" type="text" name="created"
						value="<?php  echo $res ? $res['created'] : date('Y-m-d'); ?>">
				</p>
				<p>
					Data pubblicazione<br /> <input class="date" type="text"
						name="publish_on"
						value="<?php echo $res ? $res['publish_on'] : date('Y-m-d'); ?>">
				</p>
				<p>
					Data scadenza<br /> <input class="date" type="text" name="expires"
						value="<?php echo $res ? $res['expires'] : '0000-00-00'; ?>">
				</p>
				<p>
					Data ultimo aggiornamento<br />
					<?php echo $res['updated']; ?>
				</p>
			</td>

			<td style="vertical-align: top;">
				<p>
					Sommario<br />
					<textarea name="summary" id="summary<?php echo $uid; ?>" rows="5"
						style="width: 100%; height: 150;">
						<?php echo $res['summary']; ?>
					</textarea>
				</p>
				<p>
					Testo articolo<br />
					<textarea name="text" rows="20" style="width: 100%; height: 400;"
						id="text<?php echo $uid; ?>">
						<?php echo $res['text']; ?>
					</textarea>
				</p>
			</td>

		</tr>
	</table>



	<button type="submit">Salva</button>
	<button type="reset">Annulla</button>

</form>
<script type="text/javascript">
	$('button').button();

	$('input.date').datepicker({ dateFormat: 'yy-mm-dd' });

	$( '#section' ).combobox();

	tinyMCE.execCommand("mceAddControl", true, "summary<?php echo $uid; ?>");
	tinyMCE.execCommand("mceAddControl", true, "text<?php echo $uid; ?>");
	
	$('#edit_form').submit( function(){

		<?php if ($id): ?>
		var string = 'loader.php?nw=1&mod=article/action2json&a=edit&id=<?php echo $id;?>';
		<?php else: ?>
		var string = 'loader.php?nw=1&mod=article/action2json&a=add';		
		<?php endif;?>
		
		
		$.post(
			string,
			$('#edit_form').serialize(),
			
			function(data) {
				$().toastmessage('showToast', {text: data.text,type: data.type});
				
				<?php if (!$id) :?>
				if (data.type == 'success')
					menu.article.form(data.id);
				<?php endif; ?> 
			},
			'json'
		);			
	});
	
</script>
