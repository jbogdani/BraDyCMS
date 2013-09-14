<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 1, 2012
 */
try
{
	$root = './';
	require_once $root . 'lib/globals.inc';
}
catch (Exception $e)
{
	error_log($e->getMessage());
	echo 'Something went wrong: ' . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
 	<head>
 	
	 	<meta charset="utf-8" />
	 	<title>bdus.CMS</title>
	 	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link href="./css/admin.css" rel="stylesheet" />
	    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	    <!--[if lt IE 9]>
	      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	    <![endif]-->
 	</head>

	<body<?php if (!$_SESSION['user_confirmed']) { echo ' class="login" '; }?>>
		<?php
		
		if (!$_SESSION['user_confirmed'])
		{
		?>
		
		<div class="container">
			
			<div style="text-align:center; display:none;" class="text-danger" id="logerror"></div>
			
			<form class="form-signin" id="signin" action="javascript:void(0);">
				<h2 class="text-muted">BDUS.CMS <small>[v.<?php echo version::current() ?>]</small></h2>
				<h2 class="form-signin-heading"><?php echo tr::get('please_sign_in'); ?></h2>
				
				<div class="form-group">
					<input type="text" class="form-control" placeholder="<?php echo tr::get('email_address'); ?>" name="username">
				</div>
				
				<div class="form-group">
					<input type="password" class="form-control" placeholder="<?php echo tr::get('password'); ?>" name="password">
				</div>
				<button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo tr::get('sign_in'); ?></button>
			</form>
			
		</div>
		<?php
		}
		// USER NOT CONFIRMED
		else
		{
		?>
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="container">
				 <div class="navbar-header">
				 	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				 		<span class="icon-bar"></span>
				 		<span class="icon-bar"></span>
				 		<span class="icon-bar"></span>
				 	</button>
				 	
				 	<a class="navbar-brand" href="#">bdus.CMS</a>
				 </div>
				 
				 <div class="collapse navbar-collapse">
					<ul class="nav navbar-nav">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-edit"></i> <?php echo tr::get('articles'); ?> <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="#article/addNew"><i class="glyphicon glyphicon-plus-sign"></i> <?php echo tr::get('add_new_article'); ?></a></li>
								<li><a href="#article/all"><i class="glyphicon glyphicon-list-alt"></i> <?php echo tr::get('show_all_articles'); ?></a></li>
							</ul>
						</li>
						
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-list"></i> <?php echo tr::get('menu'); ?> <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="#menu/addNew"><i class="glyphicon glyphicon-plus-sign"></i> <?php echo tr::get('add_new_menu_item'); ?></a></li>
								<li><a href="#menu/all"><i class="glyphicon glyphicon-list"></i> <?php echo tr::get('show_all_menus'); ?></a></li>
							</ul>
						</li>
						
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-camera"></i> <?php echo tr::get('media'); ?> <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="#media/all"><i class="glyphicon glyphicon-picture"></i> <?php echo tr::get('media'); ?></a></li>
								<li><a href="#galleries/all"><i class="glyphicon glyphicon-expand"></i> <?php echo tr::get('galleries'); ?></a></li>
							</ul>
						</li>
						
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-leaf"></i> <?php echo tr::get('plugins'); ?> <b class="caret"></b></a>
							<ul class="dropdown-menu">
									<li><a href="#userform/view"><i class="glyphicon glyphicon-list-alt"></i> <?php echo tr::get('user_forms'); ?></a></li>
									
						<?php
						$usr_mods = utils::dirContent('./sites/default/modules');
						if (is_array($usr_mods) && !empty($usr_mods))
						{
							foreach ($usr_mods as $mod)
							{
								if (file_exists('./sites/default/modules/' . $mod . '/' . $mod . '.inc'))
								{
									require_once './sites/default/modules/' . $mod . '/' . $mod . '.inc';

									if (method_exists($mod, 'admin'))
									{
										$custom_mods[] = $mod;
									}
								}
							}
						}
						
						if ($custom_mods): ?>
						
								<?php foreach($custom_mods as $mod): ?>
								<li><a href="./admin#plugins/run/<?php echo $mod; ?>"><i class="glyphicon glyphicon-asterisk"></i> <?php echo $mod; ?></a></li>
								<?php endforeach;?>
							
						<?php endif ?>
						</ul>
						</li>
						
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-globe"></i> <?php echo tr::get('language'); ?> <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="./admin:lng-it"><i class="glyphicon glyphicon-save"></i> Italiano</a></li>
								<li><a href="./admin:lng-en"><i class="glyphicon glyphicon-save"></i> English</a></li>
							</ul>
						</li>
						
						
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-cog"></i> <?php echo tr::get('other'); ?> <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="#changelog/show"><i class="glyphicon glyphicon-asterisk"></i> <?php echo tr::get('changelog'); ?></a></li>
								<li><a href="#error_log/show"><i class="glyphicon glyphicon-exclamation-sign"></i> <?php echo tr::get('error_log'); ?></a></li>
								<li><a href="./" target="_blank"><i class="glyphicon glyphicon-share-alt"></i> <?php echo tr::get('view_site'); ?></a></li>
								
								<li><a href="./admin"><i class="glyphicon glyphicon-repeat"></i> <?php echo tr::get('reload'); ?></a></li>
								
								<li class="divider"></li>
								
								<li><a href="#cfg/edit"><i class="glyphicon glyphicon-wrench"></i> <?php echo tr::get('cfg_editor'); ?></a></li>
								
								<li><a href="#template/dashboard"><i class="glyphicon glyphicon-file"></i> <?php echo tr::get('template_mng'); ?></a></li>

								<li><a href="#sys_translate/showList"><i class="glyphicon glyphicon-random"></i> <?php echo tr::get('sys_translate'); ?></a></li>
								
								
								
								<li class="divider"></li>
								<li class="text-center"><strong><?php echo tr::get('docs'); ?></strong></li>
								
								<li><a href="#docs/tmpl/faq"><i class="glyphicon glyphicon-question-sign"></i> FAQ</a></li>
								
								<li><a href="#docs/tmpl/main"><i class="glyphicon glyphicon-book"></i> Template Docs</a></li>
								<li><a href="#docs/tmpl/example"><i class="glyphicon glyphicon-file"></i> Template Example</a></li>
								
								<li><a href="#docs/tmpl/userform"><i class="glyphicon glyphicon-info-sign"></i> How to build and embed a form</a></li>
								<li><a href="#docs/tmpl/howto-oai"><i class="glyphicon glyphicon-info-sign"></i> How to setup a OAI-PMH interface</a></li>
								<li><a href="#docs/tmpl/howto-module"><i class="glyphicon glyphicon-info-sign"></i> How to create create custom modules</a></li>
								
							</ul>
							
						</li>
						
						<li><a href="#log/out"><i class="glyphicon glyphicon-off"></i> <?php echo tr::get('logout'); ?></a></li>
					</ul>
				</div><!--/.nav-collapse -->
			</div>
		</div>
		
		<div class="container">
		
			<div class="tabbable">
			
				<ul class="nav nav-tabs" id="tabs" data-tabs="tabs">
					<li class="active"><a href="#home">Welcome</a></li>
				</ul>
				
				<div class="tab-content">
					<div class="tab-pane active" id="home">
						<div class="row">
							<div class="col-md-8">
								<?php
								if (file_exists('./sites/default/welcome.html')){
									require_once './sites/default/welcome.html';
								}
								else
								{
								?>
								<div class="jumbotron">
									<p>Welcome to</p>
									<h1>BraDy.CMS</h1>
									<p>An opensource, highly customizable, easy to setup & use php5 mysql/sqlite CMS</p>
								</div>
								<?php
								}
								?>
							</div>
							
							<div class="col-md-4">
								<img src="./img/octocat.png" alt="octocats GITHUB" class="pull-left" style="width:150px" />
								
								<div class="media-body">
									<h3 class="media-heading">BraDyCMS</h3>
									<p class="lead">is an open source project available for fork and/or download on <a href="https://github.com/jbogdani/BraDyCMS" target="_blank">GitHub</a></p>
									<p><a href="https://github.com/jbogdani/BraDyCMS/archive/master.zip" target="_blank">Download latest build</a></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<footer class="footer">
			<h4>by BraDypUS <small>COMMUNICATING CULTURAL HERITAGE</small></h4>
			<p>BraDyCMS [v. <?php echo version::current() ?>] is an open source project available for download <a href="https://github.com/jbogdani/BraDyCMS" target="_blank">on Github</a></p>
		</footer>
	<?php } ?>
	
	<script src="controller.php?obj=tr&method=lang2json&param[]=true"></script>
	<script src="./js/jquery-2.0.3.min.js"></script>
	<script src="./js/jquery.nestable.js"></script>
	<script src="./js/bootstrap.min.js"></script>
	<script src="./js/jquery.dataTables.js"></script>
	<script src="./js/dataTable-bootstrap.js"></script>
	
	<script src="./js/admin.js"></script>
	<script src="./js/jquery.pnotify.js"></script>
	<script src="./js/bootstrap-datepicker.js"></script>
	<script src="./js/select2.js"></script>
	<script src="./tiny_mce/tiny_mce.js"></script>
	<script src="./js/fileuploader.js"></script>
	<script src="./js/prettify.js"></script>
	
	<?php
	if (!$_SESSION['user_confirmed'])
	{
	?>
	<script>
	$('#signin').on('submit', function(){
		$('#logerror').hide();
		$.post('controller.php?obj=log_ctrl&method=in', $(this).serialize(), function(data){

			if (data.status == 'success'){
				window.location.href = './admin';
				return false;
			} else {
				$('#logerror').html(data.text).show();
			}
		}, 'json');
	});
	</script>
	<?php
	}
	?>
  </body>
</html>