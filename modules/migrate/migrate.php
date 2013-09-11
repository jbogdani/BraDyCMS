<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Aug 28, 2013
 */

class migrate_ctrl extends Controller
{
	public function tags()
	{
		echo 'Security issue! Uncomment the return statement in /modules/migrate/migrate.php to continue'; return;
		$art_list = R::findAll('articles');
		
		foreach($art_list as $art)
		{
			$tags = utils::csv_explode($art->tags);
			
			if ($art->section && !in_array($art->section, $tags))
			{
				$tags[] = $art->section;
			}
			
			if (is_array($tags) && !empty($tags))
			{
				R::tag($art, $tags);
			}
		}
	}
}
?>

