<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Nov 1, 2011
 */

try
{
	if (!$_GET['menu'])
	{
		throw new MyExc('Non è specificato il menu da far vedere');
	}

	$menu_name = $_GET['menu'];

	$menu = new Menu();

	$res = $menu->get_all_menu_items($menu_name);

	$res = $menu->format_value($res);
	?>


<div style="padding: 10px;">

	<h1>
		Gestione menu:
		<?php echo strtoupper($menu_name); ?>
	</h1>

	<p>
		<button type="button"
			onclick="menu.menu.add_new('<?php echo $_GET['menu']; ?>')">
			<span class="ui-icon ui-icon-plusthick"
				style="float: left; margin: 0 4px;"></span> Aggiungi nuovo
		</button>
	</p>

	<table class="tablesorter" cellpadding="5">
		<tr>
			<th>testo</th>
			<th>link</th>
			<th>target</th>
			<th>title</th>
			<th>menu</th>
			<th>ordinamento</th>
			<th>sottomenu di</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tr>

		<?php
		if (is_array($res)):
		foreach ($res as $arr):
		?>
		<tr>
			<td><?php echo $arr['item']; ?></td>
			<td><?php echo $arr['href']; ?></td>
			<td><?php echo $arr['target']; ?></td>
			<td><?php echo $arr['title']; ?></td>
			<td><?php echo $arr['menu']; ?></td>
			<td><?php echo $arr['sort']; ?></td>
			<td><?php echo $arr['subof']; ?></td>
			<td><button type="button"
					onclick="menu.menu.edit('<?php echo $arr['id']; ?>', '<?php echo $_GET['menu'];?>')">modifica</button>
			</td>
			<td><button type="button"
					onclick="menu.menu.erase('<?php echo $arr['id']; ?>', '<?php echo $_GET['menu'];?>')">cancella</button>
			</td>
		</tr>
		<?php
		endforeach;
		endif;
		?>

	</table>
</div>
<script type="text/javascript">
	$('button').button();
	</script>
		<?php

}
catch(MyExc $e)
{
	$e->log();

	echo $e->getMessage();
}