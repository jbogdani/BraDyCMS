<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Nov 1, 2011
 */

?>
<h1>Gestione traduzioni</h1>
<p style="text-align: right;">
	<a href="javascript:void(0);"
		onclick="menu.translate.list('<?php echo $_GET['context']; ?>')"><img
		src="../css/view-refresh-3.png" alt="Ricarica" /> </a>
</p>

<?php

$translate = new translate();

foreach ($cfg['languages'] as $lang=>$language)
{
	$list = $translate->get_list($_GET['context'], $lang);

	if ($list)
	{
		echo '<table class="tablesorter" colspan="5">'
		. '<thead>'
		// header's first row
		. '<tr>'
		. '<th colspan="6">REPORT PER LA LINGUA: ' . strtoupper($language) . '</th>'
		. '</tr>'
		. '<tr>'
		. '<th colspan="2">Originale</th>'
		. '<th colspan="2">Traduzione</th>'
		. '<th rowspan="2">Stato</th>'
		. '<th rowspan="2">&nbsp;</th>'
		. '</tr>'
		// heder's second row
		. '<tr>'
		. '<th>ID</th>'
		. '<th>Titolo</th>'
		. '<th>ID</th>'
		. '<th>Titolo</th>'
		. '</tr>'
		. '</thead>'
		. '<tbody>';
		
		foreach ($list as $row)
		{
			echo '<tr>';
				
			foreach ($row as $id=>$val)
			{
				if ($id != 'tr_translated')
					echo '<td>' . $val . '</td>';
			}
			echo '<td class="' . ( ($row['tr_translated'] == 0) ? 'ui-state-error"><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-alert"></span> Non aggiornato' : 'ui-state-highlight"><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-check"></span> Aggiornato') . '</td>'
			. '<td><button onclick="menu.translate.form(\'' . $_GET['context'] . '\', ' . $row['o_id'] . ', \'' . $lang . '\')">apri</button></td>';
			echo '</tr>';
		}
		echo '</tbody>'
		. '</table>';
	}
}
?>
<script>
	$('button').button();
</script>
