<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Oct 31, 2011
 */

switch($_GET['log_message'])
{
	case 'denied':
		echo '<div class="ui-widget">'
		. '<div style="padding: 0 .7em;" class="ui-state-error ui-corner-all">'
		. '<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-alert"></span>'
		. '<strong>Attenzione:</strong> Username e/o password errati! Si prega di riprovare.</p>'
		. '</div>'
		. '</div>';
		break;

	case 'out':
		echo '<div class="ui-widget">'
		. '<div style="padding: 0 .7em;" class="ui-state-highlight ui-corner-all">'
		. '<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-info"></span>'
		. 'Il logout è stato effettuato senza errori</p>'
		. '</div>'
		. '</div>';
		break;
		break;

	case '':
	default:
		break;
}
?>
<style>
<!--
#loginform input {
	border: 1px solid #999999;
	font-size: 1.5em;
	margin: 10px;
	padding: 15px;
	border-radius: 20px;
	width: 200px;
	background: #ebebeb;
}
-->
</style>
<form action="./admin" method="post" id="loginform"
	style="margin: 20px 0;">
	<input placeholder="username..." name="username" type="text" /> <input
		placeholder="password..." name="password" type="password" />
	<button type="submit" style="display: none;"></button>
</form>

<?php
echo 'BraDyCMS, v.' . version::current();
?>
