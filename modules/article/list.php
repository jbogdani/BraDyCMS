<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Oct 31, 2011
 */

$article_edit = new ArticleEdit();

$res_arr = $article_edit->get_all_articles();

?>
<h1>Gestione Articoli</h1>

<p style="text-align: right;">
	<a onclick="menu.article.form();" href="javascript:void(0);" title="Aggiungi nuovo"><img alt="add new" src="./css/list-add-font.png" alt="Aggiungi nuovo"></a>
	
	<a onclick="menu.translate.list('article')" href="javascript:void(0)" title="Traduci"><img alt="Translation" src="./css/applications-education-language.png" alt="Traduci"></a>

	<a href="javascript:void(0);" onclick="menu.article.showall()" title="Ricarica"><img src="./css/view-refresh-3.png" alt="Ricarica" /></a>
	
</p>

<input name="filter" id="filter-box" value="" maxlength="30" style="width:200px;" type="text" placeholder="<?php echo utils::tr('search'); ?>..." >
<input id="filter-clear-button" type="button" value="<?php echo utils::tr('clear');?>" style="width: 70px;"/>
<table cellspacing="2" cellpadding="2" class="tablesorter"
	style="width: 100%" id="art_list">

	<thead>
		<tr>
			<th class="header">ID</th>
			<th class="header">title</th>
			<th class="header">ID testuale</th>
			<th class="header">Stato</th>
			<th class="header">Sezione</th>
			<th class="header">Ordinamento</th>
			<th class="header">Data creazione</th>
			<th class="header">Data pubblicazione</th>
			<th class="header">Data scadenza</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tr>
	</thead>

	<tbody>
	<?php foreach ( $res_arr as $res ): ?>

		<tr ondblclick="menu.article.form(<?php echo $res['id']; ?>);">
			<td><?php echo $res['id']; ?></td>
			<td><?php echo $res['title']; ?></td>
			<td><?php echo $res['text_id']; ?></td>
			<td><?php echo $res['status']; ?></td>
			<td><?php echo $res['section']; ?></td>
			<td><?php echo $res['sort']; ?></td>
			<td><?php echo $res['created']; ?></td>
			<td><?php echo $res['publish_on']; ?></td>
			<td><?php echo $res['expires']; ?></td>
			<td><button onclick="menu.article.form(<?php echo $res['id']; ?>);">modifica</button>
			</td>
			<td><button onclick="menu.article.erase(<?php echo $res['id']; ?>);">cancella</button>
			</td>
		</tr>
		<?php endforeach;?>
	</tbody>

</table>

<script>
	$("#art_list")
		.tablesorter({
			headers: {
				9 : {sorter: false},
				10: {sorter: false}
		}})
		.tablesorterFilter({
			filterContainer: $("#filter-box"),
			filterClearContainer: $("#filter-clear-button"),
		});
	$('button').button();
</script>
