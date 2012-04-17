<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Nov 1, 2011
 */

$menu = new Menu();

if ($_GET['id'])
{

	$data = $menu->get_menu_by_id($_GET['id']);

	$data = $menu->format_value($data[0]);
}
else
{
	if (!$_GET['menu'])
	{
		$flag_new = true;
	}
	else
	{
		$data['menu'] = $_GET['menu'];
	}
}

?>

<form action="javascript: void(0)" id="menu_edit">
	<table cellpadding="5">
		<tr>
			<th>Id</th>
			<td><input type="text" name="id" readonly="readonly"
				value="<?php echo $data['id']; ?>" /></td>
		</tr>
		<tr>
			<th>Testo</th>
			<td><input type="text" name="item"
				value="<?php echo $data['item']; ?>" /></td>
		</tr>
		<tr>
			<th>Link</th>
			<td><input type="text" name="href"
				value="<?php echo $data['href'] ? $data['href'] : './?art_title='; ?>" /></td>
		</tr>
		<tr>
			<th>Target</th>
			<td><select name="target">
			<?php
			$opts = array('', '_blank', '_parent', '_top');

			foreach ($opts as $opt)
			{
				echo '<option value="' . $opt . '" ' . ( ($data['target']==$opt) ? ' selected="selected" ' : '') . '>' . $opt . '</option>';
			}
			?>
			</select>
		
		</tr>
		<tr>
			<th>Title</th>
			<td><input type="text" name="title"
				value="<?php echo $data['title']; ?>" /></td>
		</tr>
		<tr>
			<th>Menu</th>
			<td><?php if ($flag_new) :?> <input type="text" name="menu" /> <?php else :?>
				<select name="menu">
				<?php
				$menus = $menu->getList();
				foreach ($menus as $menu_it)
				{
					echo '<option value="' . $menu_it . '" ' . (($menu_it == $data['menu']) ? ' selected="selected" ' : ''). '> ' . $menu_it . ' </option>';
				}
				?>
			</select> <?php endif; ?>
			</td>
		</tr>
		<tr>
			<th>Sottomenu di:</th>
			<td>
				<select name="subof">
					<option></option>
					<?php
					$av_items = $menu->get_all_menu_items($_GET['menu']);
					if ($av_items AND is_array($av_items))
					{
						foreach ($av_items as $arr)
						{
							echo '<option '
								. 'value="' . $arr['id'] . '"' 
								. ( ($data['subof'] == $arr['id']) ? 'selected="selected"' : '') 
								. '>' . $arr['menu'] . ' > ' 
								. $arr['item'] 
								. '</option>';
						}
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Ordinamento</th>
			<td><input type="text" name="sort"
				value="<?php echo $data['sort']; ?>" /></td>
		</tr>
	</table>
</form>
