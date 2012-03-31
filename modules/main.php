<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Oct 31, 2011
 */

?>

<article>
	<h3>Articoli</h3>
	<ul class="home_menu">
		<li><a href="javascript:void(0);" onclick="menu.article.form();"><img
				src="./css/list-add-font.png" alt="add new" /><br />Nuovo articolo</a>
		</li>
		<li><a href="javascript:void(0)" onclick="menu.article.showall()"><img
				src="./css/view-list-icons.png" alt="show all" /><br />Mostra tutti
				gli articoli</a></li>
		<li><a href="javascript:void(0)"
			onclick="menu.translate.list('article')"><img
				src="./css/applications-education-language.png" alt="Translation" /><br />Traduzioni</a>
		</li>
	</ul>
</article>

<article>
	<h3>Media</h3>
	<ul class="home_menu">
		<li><a href="javascript:void(0)" onclick="menu.file.showall()"><img
				src="./css/folder-image.png" alt="show all" /><br />Gestione immagini</a>
		</li>
	</ul>
</article>

<article>
	<h3>Menu</h3>
	<ul class="home_menu">
		<li><a href="javascript:void(0)" onclick="menu.menu.add_new();"><img
				src="./css/list-add-3.png" alt="show all" />Aggiungi menu</a></li>
				<?php
				$menu = new Menu();
	
				$list  = $menu->getList();
	
				if (is_array($list))
				{
					foreach ($list as $l)
					{
						echo '<li><a href="javascript:void(0);" onclick="menu.menu.list(\'' . $l . '\')"><img src="./css/view-list-tree-4.png" alt="show all"/>Menu<br /><strong>' . $l . '</strong></a></li>';
					}
				}
	
				?>
		<li><a href="javascript:void(0)" onclick="menu.translate.list('menu')"><img
				src="./css/applications-education-language.png" alt="Translation" /><br />Traduzioni</a>
		</li>
	</ul>
</article>

<?php if ($cfg['modules']['tables']) :?>
<article>
	<h3>Tabelle dati</h3>
	<ul class="home_menu">
		<?php foreach ($cfg['modules']['tables'] as $tb) :?>
		<li>
			<a href="javascript:void(0)" onclick="gui.openInTab('tables/show', 'tb=<?php echo $tb; ?>', 'Gestione dati')">
			<img alt="changelog" src="./css/insert-table.png" /><br />Gestisci <?php echo strtoupper($tb); ?></a>
		</li>
		<?php endforeach;?>
	</ul>
</article>
<?php endif;?>
<article>
<h3>Altro</h3>
	<ul class="home_menu">
	
		<li>
			<a href="./" target="_blank"><img src="./css/checkbox.png" alt="view site" /><br />Vedi sito</a>
		</li>
		
		<li>
			<a href="javascript:void(0)" onclick="gui.openInTab('changelog', false, 'Changelog')">
			<img alt="changelog" src="./css/text-x-changelog.png" /><br />Changelog</a>
		</li>
		
		<li>
			<a href="./admin"><img src="./css/view-refresh-3.png" alt="Reload" /><br />Ricarica</a>
		</li>
		
		<li>
			<a href="javascript:void(0)" onclick="gui.openInTab('view_log', false, 'Log degli errori')">
			<img src="./css/utilities-log-viewer.png" alt="View log" /><br />Log degli errori</a>
		</li>
		
		<li>
			<a href="./admin:logout"><img src="./css/application-exit-3.png" alt="Log out" /><br />Log out</a>
		</li>
	</ul>
</article>

<div style="clear: both; padding-top: 150px; text-align: center;">
	<hr />
	<p>BraDyCMS is a BraDypUS creation</p>
	<p>
		versione
		<?php echo version::current(); ?>
	</p>
</div>
