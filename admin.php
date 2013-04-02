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
	
	$log_message = auth::check($_POST['username'], $_POST['password']);
	
	if ( $_GET['logout'] )
	{
		$log_message = auth::logout();
		
		utils::emptyTmp();
	}
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
	 	<link href="./css/bootstrap.css" rel="stylesheet" />
	 	<link href="./css/dataTable-bootstrap.css" rel="stylesheet" />
	 	<link href="./css/jquery.pnotify.default.css" rel="stylesheet" />
		<link href="./css/select2.css" rel="stylesheet" />
		<link href="./css/datepicker.css" rel="stylesheet" />
		<link href="./css/admin.css" rel="stylesheet" />
		<link href="./css/fileuploader.css" rel="stylesheet" />
		<link href="./css/prettify.css" rel="stylesheet" />
	    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	    <!--[if lt IE 9]>
	      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	    <![endif]-->
 	</head>

	<body>
		<?php
		if (!$_SESSION['user_confirmed'])
		{
		?>
		<style type="text/css">
			body {
				padding-top: 40px;
				padding-bottom: 40px;
				background-color: #f5f5f5;
			}
			
			.form-signin {
				max-width: 300px;
				padding: 19px 29px 29px;
				margin: 0 auto 20px;
				background-color: #fff;
				border: 1px solid #e5e5e5;
				-webkit-border-radius: 5px;
					-moz-border-radius: 5px;
					border-radius: 5px;
				-webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
					-moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
					box-shadow: 0 1px 2px rgba(0,0,0,.05);
			}
			
			.form-signin .form-signin-heading, .form-signin .checkbox {
	      		margin-bottom: 10px;
	      	}
	      	
	      	.form-signin input[type="text"],.form-signin input[type="password"] {
	      		font-size: 16px;
	      		height: auto;
	      		margin-bottom: 15px;
	      		padding: 7px 9px;
      		}
		</style>
		<div class="container">
			<?php if ($log_message) {?>
			<div style="text-align:center" class="text-error"><?php echo $log_message; ?></div>
			<?php }?>
			<form class="form-signin" action="./admin" method="post">
				<h2 class="muted">BDUS.CMS</h2>
				<h2 class="form-signin-heading"><?php echo tr::get('please_sign_in'); ?></h2>
				<input type="text" class="input-block-level" placeholder="<?php echo tr::get('email_address'); ?>" name="username">
				<input type="password" class="input-block-level" placeholder="<?php echo tr::get('password'); ?>" name="password">
				<button class="btn btn-large btn-primary" type="submit"><?php echo tr::get('sign_in'); ?></button>
			</form>
		</div>
		<?php
		}
		else
		{
		?>
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">    
		
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
		
					<a class="brand" href="#">bdus.CMS</a>
		
					<div class="nav-collapse collapse">
						<ul class="nav">
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon icon-edit"></i> <?php echo tr::get('articles'); ?> <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="#article/addNew"><i class="icon-plus-sign"></i> <?php echo tr::get('add_new_article'); ?></a></li>
									<li><a href="#article/all"><i class="icon-list-alt"></i> <?php echo tr::get('show_all_articles'); ?></a></li>
									<li><a href="#translate/article"><i class="icon-globe"></i> <?php echo tr::get('translations'); ?></a></li>
								</ul>
							</li>
							
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon icon-align-justify"></i> <?php echo tr::get('menu'); ?> <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="#menu/addNew"><i class="icon-plus-sign"></i> <?php echo tr::get('add_new_menu_item'); ?></a></li>
									<li><a href="#menu/all"><i class="icon-list-alt"></i> <?php echo tr::get('show_all_menus'); ?></a></li>
									<li><a href="#translate/menu"><i class="icon-globe"></i> <?php echo tr::get('translations'); ?></a></li>
								</ul>
							</li>
							
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon icon-camera"></i> <?php echo tr::get('media'); ?> <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="#media/all"><i class="icon-picture"></i> <?php echo tr::get('media'); ?></a></li>
									<li><a href="#galleries/all"><i class="icon-facetime-video"></i> <?php echo tr::get('galleries'); ?></a></li>
								</ul>
							
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon icon-cog"></i> <?php echo tr::get('other'); ?> <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="#changelog/show"><i class="icon-asterisk"></i> <?php echo tr::get('changelog'); ?></a></li>
									<li><a href="#error_log/show"><i class="icon-exclamation-sign"></i> <?php echo tr::get('error_log'); ?></a></li>
									<li><a href="./" target="_blank"><i class="icon-share-alt"></i> <?php echo tr::get('view_site'); ?></a></li>
									
									<li class="dropdown-submenu">
										<a href="#"><i class="icon-globe"></i> <?php echo tr::get('language'); ?> <b class="caret"></b></a>
										<ul class="dropdown-menu">
											<li><a href="./admin:lng-it"><i class="icon-play"></i> Italiano</a></li>
											<li><a href="./admin:lng-en"><i class="icon-play"></i> English</a></li>
										</ul>
									</li>
									
									<li><a href="./admin"><i class="icon-repeat"></i> <?php echo tr::get('reload'); ?></a></li>
									
									<li class="divider"></li>
									
									<li><a href="#cfg/edit"><i class="icon icon-wrench"></i> <?php echo tr::get('cfg_editor'); ?></a></li>
									<li><a href="#sys_translate/showList"><i class="icon icon-wrench"></i> <?php echo tr::get('sys_translate'); ?></a></li>
									
									<li class="divider"></li>
									<li class="dropdown-submenu">
										<a href="#"><i class="icon icon-eye-open"></i> <?php echo tr::get('template_mng'); ?></a>
										<ul class="dropdown-menu">
											<li><a href="#template/html"><i class="icon icon-file"></i> <?php echo tr::get('edit_html'); ?></a></li>
											<li><a href="#template/css"><i class="icon icon-adjust"></i> <?php echo tr::get('edit_css'); ?></a></li>
										</ul>
									</li>
									
									<li class="divider"></li>
									<li><a href="#docs/tmpl/faq"><i class="icon-book"></i> FAQ</a></li>
									
									<li class="divider"></li>
									<li class="dropdown-submenu">
										<a href="#">Template documentation</a>
										<ul class="dropdown-menu">
											<li><a href="#docs/tmpl/intro"><i class="icon-book"></i> Intro</a></li>
											<li><a href="#docs/tmpl/Out"><i class="icon-book"></i> Out</a></li>
											<li><a href="#docs/tmpl/htmlOut"><i class="icon-book"></i> htmlOut</a></li>
											<li><a href="#docs/tmpl/example"><i class="icon-file"></i> Template Example</a></li>
										</ul>
									</li>
								</ul>
							</li>
							
							
							<li><a href="./admin:logout"><i class="icon-off"></i> <?php echo tr::get('logout'); ?></a></li>
						</ul>
					</div><!--/.nav-collapse -->
				</div>
			</div>
		</div>
		<div class="container">
			<div class="tabbable">
			
				<ul class="nav nav-tabs" id="tabs" data-tabs="tabs">
					<li class="active"><a href="#home">Welcome</a></li>
				</ul>
				
				<div class="tab-content">
					<div class="tab-pane active" id="home">
						<div class="hero-unit">
							<p>Welcome to</p>
							<h1>BraDy.CMS</h1>
							<p>An opensource, highly customizable, easy to setup & use php5 mysql/sqlite CMS</p>
						</div>
						
						<div class="media">
							<img src="./img/octocat.png" alt="octocats GITHUB" class="pull-left" style="width:150px" />
							
							<div class="media-body">
								<h3 class="media-heading">BraDyCMS is on <a href="https://github.com/jbogdani/BraDyCMS" target="_blank">Github</a></h3>
								<p>Fork it on <a href="https://github.com/jbogdani/BraDyCMS" target="_blank">https://github.com/jbogdani/BraDyCMS</a></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<footer class="footer">
			<h4>by BraDypUS <small>COMMUNICATING CULTURAL HERITAGE</small></h4>
		</footer>
	<?php } ?>
		
	<script src="controller.php?obj=tr&method=lang2json&param[]=true"></script>
	<script src="./js/jquery-1.9.0.min.js"></script>
	<script src="./js/jquery-ui-1.10.0.custom.min.js"></script>
	<script src="./js/jquery.mjs.nestedSortable.js"></script>
	<script src="./js/bootstrap.js"></script>
	<script src="./js/jquery.dataTables.js"></script>
	<script src="./js/dataTable-bootstrap.js"></script>
	<script src="./js/admin.js"></script>
	<script src="./js/jquery.ba-bbq.js"></script>
	<script src="./js/jquery.pnotify.js"></script>
	<script src="./js/bootstrap-datepicker.js"></script>
	<script src="./js/select2.js"></script>
	<script src="./tiny_mce/tiny_mce.js"></script>
	<script src="./js/fileuploader.js"></script>
	<script src="./js/prettify.js"></script>
	
  </body>
</html>