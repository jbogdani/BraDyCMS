<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Nov 26, 2011
 */


?>
<h1>BraDy CMS</h1>
<h3>Current version: <?php echo version::current(); ?></h3>
<h3>Changelog</h3>
<ul>
<?php
$chl = version::changelog();

if (is_array($chl))
{
	foreach ($chl as $v=>$arr)
	{
		echo '<li>' . $v
		. '<ul>'
			. '<li>' . implode('</li><li>', $arr) . '</li>'
		. '</ul>' 
		. '</li>';
	}
}
?>
</ul>