<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.ALLOANDGO
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       3.0
 */
defined ( '_JEXEC' ) or die ();

$app = JFactory::getApplication ();
$doc = JFactory::getDocument ();
$lang = JFactory::getLanguage ();
$this->language = $doc->language;
$this->direction = $doc->direction;
$input = $app->input;
$user = JFactory::getUser ();

// get $usergroup to define user login is suprer admin, isn't user.
$usergroup = $user->getAuthorisedGroups ();
jimport ( 'joomla.user.helper' );
$groups = JUserHelper::getUserGroups ( $user->id );
$usergroup = implode ( " ", $groups );
 //echo $usergroup;
// die;

// Add JavaScript Frameworks
JHtml::_ ( 'bootstrap.framework' );
$doc->addScriptVersion ( $this->baseurl . '/templates/' . $this->template . '/js/template.js' );

// Add Stylesheets
$doc->addStyleSheetVersion ( $this->baseurl . '/templates/' . $this->template . '/css/template' . ($this->direction == 'rtl' ? '-rtl' : '') . '.css' );


// Load specific language related CSS
$file = 'language/' . $lang->getTag () . '/' . $lang->getTag () . '.css';

if (is_file ( $file )) {
	$doc->addStyleSheetVersion ( $file );
}

// Detecting Active Variables
$option = $input->get ( 'option', '' );
$view = $input->get ( 'view', '' );
$layout = $input->get ( 'layout', '' );
$task = $input->get ( 'task', '' );
$itemid = $input->get ( 'Itemid', '' );
$sitename = htmlspecialchars ( $app->get ( 'sitename', '' ), ENT_QUOTES, 'UTF-8' );
$cpanel = ($option === 'com_cpanel');

$hidden = JFactory::getApplication ()->input->get ( 'hidemainmenu' );

$showSubmenu = false;
$this->submenumodules = JModuleHelper::getModules ( 'submenu' );

foreach ( $this->submenumodules as $submenumodule ) {
	$output = JModuleHelper::renderModule ( $submenumodule );
	
	if (strlen ( $output )) {
		$showSubmenu = true;
		break;
	}
}

// Template Parameters
$displayHeader = $this->params->get ( 'displayHeader', '1' );
$statusFixed = $this->params->get ( 'statusFixed', '1' );
$stickyToolbar = $this->params->get ( 'stickyToolbar', '1' );

// Header classes
$template_is_light = ($this->params->get ( 'templateColor' ) && colorIsLight ( $this->params->get ( 'templateColor' ) ));
$header_is_light = ($displayHeader && $this->params->get ( 'headerColor' ) && colorIsLight ( $this->params->get ( 'headerColor' ) ));

if ($displayHeader) {
	// Logo file
	if ($this->params->get ( 'logoFile' )) {
		// $logo = JUri::root() . $this->params->get('logoFile');
	} else {
		// $logo = $this->baseurl . '/templates/' . $this->template . '/images/logo' . ($header_is_light ? '-inverse' : '') . '.png';
	}
}
function colorIsLight($color) {
	$r = hexdec ( substr ( $color, 1, 2 ) );
	$g = hexdec ( substr ( $color, 3, 2 ) );
	$b = hexdec ( substr ( $color, 5, 2 ) );
	$yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
	
	return $yiq >= 200;
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"
	xml:lang="<?php echo $this->language; ?>"
	lang="<?php echo $this->language; ?>"
	dir="<?php echo $this->direction; ?>">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<jdoc:include type="head" />

<!-- Template color -->
	<?php if ($this->params->get('templateColor')) : ?>
		<style type="text/css">
.navbar-inner,.navbar-inverse .navbar-inner,.dropdown-menu li>a:hover,.dropdown-menu .active>a,.dropdown-menu .active>a:hover,.navbar-inverse .nav li.dropdown.open>.dropdown-toggle,.navbar-inverse .nav li.dropdown.active>.dropdown-toggle,.navbar-inverse .nav li.dropdown.open.active>.dropdown-toggle,#status.status-top
	{
	background: <?php
		
echo $this->params->get ( 'templateColor' );
		?>;
}
</style>
	<?php endif; ?>
	<!-- Template header color -->
	<?php if ($displayHeader && $this->params->get('headerColor')) : ?>
		<style type="text/css">
.header {
	background: <?php
		
echo $this->params->get ( 'headerColor' );
		?>;
}
</style>
	<?php endif; ?>

	<!-- Sidebar background color -->
	<?php if ($this->params->get('sidebarColor')) : ?>
		<style type="text/css">
.nav-list>.active>a,.nav-list>.active>a:hover {
	background: <?php
		
echo $this->params->get ( 'sidebarColor' );
		?>;
}
</style>
	<?php endif; ?>

	<!-- Link color -->
	<?php if ($this->params->get('linkColor')) : ?>
		<style type="text/css">
a,.j-toggle-sidebar-button {
	color: <?php
		
echo $this->params->get ( 'linkColor' );
		?>;
}
</style>
	<?php endif; ?>

	<!--[if lt IE 9]>
	<script src="<?php echo JUri::root(true); ?>/media/jui/js/html5.js"></script>
	<![endif]-->
</head>

<body
	class="admin <?php echo $option . ' view-' . $view . ' layout-' . $layout . ' task-' . $task . ' itemid-' . $itemid; ?>">
	<!-- Top Navigation -->
	<nav
		class="navbar<?php echo $template_is_light ? '' : ' navbar-inverse'; ?> navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container-fluid">
			<?php if ($this->params->get('admin_menus') != '0') : ?>
				<a href="#" class="btn btn-navbar collapsed" data-toggle="collapse"
					data-target=".nav-collapse"> <span class="icon-bar"></span> <span
					class="icon-bar"></span> <span class="icon-bar"></span>
				</a>
			<?php endif; ?>

			 <a class="brand hidden-desktop hidden-tablet"
					href="<?php echo JUri::root(); ?>"
					title="<?php echo JText::sprintf('TPL_ALLOANDGO_PREVIEW', $sitename); ?>"
					target="_blank"><?php echo JHtml::_('string.truncate', $sitename, 14, false, false); ?>
				<span class="icon-out-2 small"></span></a>
			<?php if($usergroup == 8){?>
			<div
					<?php echo ($this->params->get('admin_menus') != '0') ? ' class="nav-collapse collapse"' : ''; ?>>
					<jdoc:include type="modules" name="menu" style="none" />
			<?php }else{?>
				<ul id="menu" class="nav">

						<li class="dropdown"><a class="dropdown-toggle"
							data-toggle="dropdown" href="#"><?php echo JText::_('Users')?><span class="caret"></span></a>
						<ul class="dropdown-menu">
								<li class="dropdown-submenu"><a
									class="dropdown-toggle menu-user" data-toggle="dropdown"
									href="index.php?option=com_users&amp;view=users">Manage</a>
								<ul id="menu-com-users-users"
										class="dropdown-menu menu-component">
										<li><a class="menu-newarticle"
											href="index.php?option=com_users&amp;task=user.add">Add New
												User</a></li>
									</ul></li>
								<li class="dropdown-submenu"><a
									class="dropdown-toggle menu-groups" data-toggle="dropdown"
									href="index.php?option=com_users&amp;view=groups">Groups</a>
								<ul id="menu-com-users-groups"
										class="dropdown-menu menu-component">
										<li><a class="menu-newarticle"
											href="index.php?option=com_users&amp;task=group.add">Add New
												Group</a></li>
									</ul></li>
								<li class="dropdown-submenu"><a
									class="dropdown-toggle menu-levels" data-toggle="dropdown"
									href="index.php?option=com_users&amp;view=levels">Access Levels</a>
								<ul id="menu-com-users-levels"
										class="dropdown-menu menu-component">
										<li><a class="menu-newarticle"
											href="index.php?option=com_users&amp;task=level.add">Add New
												Access Level</a></li>
									</ul></li>
								<li class="divider"><span></span></li>
								<li class="dropdown-submenu"><a
									class="dropdown-toggle menu-user-note" data-toggle="dropdown"
									href="index.php?option=com_users&amp;view=notes">User Notes</a>
								<ul id="menu-com-users-notes"
										class="dropdown-menu menu-component">
										<li><a class="menu-newarticle"
											href="index.php?option=com_users&amp;task=note.add">Add User
												Note</a></li>
									</ul></li>
								<li class="dropdown-submenu"><a
									class="dropdown-toggle menu-category" data-toggle="dropdown"
									href="index.php?option=com_categories&amp;view=categories&amp;extension=com_users">User
										Note Categories</a>
								<ul id="menu-com-categories-categories-com-users"
										class="dropdown-menu menu-component">
										<li><a class="menu-newarticle"
											href="index.php?option=com_categories&amp;task=category.add&amp;extension=com_users">Add
												New Category</a></li>
									</ul></li>
								<li class="divider"><span></span></li>
								<li><a class="menu-massmail"
									href="index.php?option=com_users&amp;view=mail">Mass Mail Users</a></li>
							</ul></li>
						<li>
							<a class="dropdown-toggle"
							data-toggle="dropdown" href="index.php?option=com_content&view=articles"><?php echo JText::_('Content')?><span
								class="caret"></span></a>
						</li>

						<li class="dropdown"><a class="dropdown-toggle"
							data-toggle="dropdown" href="#">Manager<span
								class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a class="menu-dashboard"
									href="index.php?option=com_bookpro">Dashboard</a></li>
							<li><a class="menu-order"
									href="index.php?option=com_bookpro&view=orders">Orders</a></li>
							<li><a class="menu-registration"
									href="index.php?option=com_bookpro&view=customers&state=0&layout=registration">Registrations</a></li>
							<li><a class="menu-customer"
									href="index.php?option=com_bookpro&view=customers">Customers</a></li>
							<li><a class="menu-tranporttype"
									href="index.php?option=com_bookpro&view=transport_types">Transport Type</a></li>
							<li><a class="menu-package"
									href="index.php?option=com_bookpro&view=packages">Packages</a></li>
							<li class="dropdown-submenu"><a class="dropdown-toggle menu-category" data-toggle="dropdown"
									href="index.php?option=com_bookpro&view=prices">Price</a>
								<ul id="menu-com-categories-categories-com-bookpro"
										class="dropdown-menu menu-component">
										<li><a class="menu-distance"
											href="index.php?option=com_bookpro&view=prices">Distance</a></li>
										<li><a class="menu-date"
											href="index.php?option=com_bookpro&view=prices&layout=date">
												Date</a></li>
										<li><a class="menu-dayofweek"
											href="index.php?option=com_bookpro&view=prices&layout=week">
												Day of week</a></li>
										<li><a class="menu-vehicletype"
											href="index.php?option=com_bookpro&view=vehicle_types">
												Vehicle Type</a></li>
										<li><a class="menu-vehicle"
											href="index.php?option=com_bookpro&view=vehicles">
												Vehicle</a></li>
								</ul>									
							</li>
							<li class="dropdown-submenu"><a class="dropdown-toggle menu-category" data-toggle="dropdown"
									href="index.php?option=com_bookpro&view=vehicle_types">Vehicle Type</a>
								<ul id="menu-com-categories-categories-com-bookpro"
										class="dropdown-menu menu-component">
										<li><a class="menu-vehicle"
											href="index.php?option=com_bookpro&view=vehicles">
												Vehicles</a></li>
										<li><a class="menu-vehicletype"
											href="index.php?option=com_bookpro&view=vehicle_types">
												Vehicle Types</a></li>
								</ul>									
							</li>
							<li class="dropdown-submenu"><a class="dropdown-toggle menu-category" data-toggle="dropdown"
									href="#">More</a>
								<ul id="menu-com-categories-categories-com-bookpro"
										class="dropdown-menu menu-component">
										<li><a class="menu-email"
											href="index.php?option=com_bookpro&view=applications">Email Setting</a></li>
										<li><a class="menu-coupon"
											href="index.php?option=com_bookpro&view=coupons">Coupons</a></li>
								</ul>									
							</li>
						</ul></li>
					</ul>
<?php }?>
				<ul
						class="nav nav-user<?php echo ($this->direction == 'rtl') ? ' pull-left' : ' pull-right'; ?>">
						<li class="dropdown"><a
							class="<?php echo ($hidden ? ' disabled' : 'dropdown-toggle'); ?>"
							data-toggle="<?php echo ($hidden ? '' : 'dropdown'); ?>"
							<?php echo ($hidden ? '' : 'href="#"'); ?>><span class="icon-cog"></span>
								<span class="caret"></span></a>
							<ul class="dropdown-menu">
							<?php if (!$hidden) : ?>
								<li><span> <span class="icon-user"></span> <strong><?php echo $user->name; ?></strong>
								</span></li>
								<li class="divider"></li>
								<li><a
									href="index.php?option=com_admin&amp;task=profile.edit&amp;id=<?php echo $user->id; ?>"><?php echo JText::_('TPL_ALLOANDGO_EDIT_ACCOUNT'); ?></a>
								</li>
								<li class="divider"></li>
								<li class=""><a
									href="<?php echo JRoute::_('index.php?option=com_login&task=logout&' . JSession::getFormToken() . '=1'); ?>"><?php echo JText::_('TPL_ALLOANDGO_LOGOUT'); ?></a>
								</li>
							<?php endif; ?>
						</ul></li>
					</ul>
					<a class="brand visible-desktop visible-tablet"
						href="<?php echo JUri::root(); ?>"
						title="<?php echo JText::sprintf('TPL_ALLOANDGO_PREVIEW', $sitename); ?>"
						target="_blank"><?php echo JHtml::_('string.truncate', $sitename, 14, false, false); ?>
					<span class="icon-out-2 small"></span></a>
				</div>
				<!--/.nav-collapse -->

			</div>
		</div>
	</nav>

	<!-- Header -->
<?php if ($displayHeader) : ?>
	<header
		class="header<?php echo $header_is_light ? ' header-inverse' : ''; ?>">
		<div class="container-logo"></div>
		<div class="container-title">
			<jdoc:include type="modules" name="title" />
		</div>
	</header>
<?php endif; ?>
<?php if ((!$statusFixed) && ($this->countModules('status'))) : ?>
	<!-- Begin Status Module -->
	<div id="status" class="navbar status-top hidden-phone">
		<div class="btn-toolbar">
			<jdoc:include type="modules" name="status" style="no" />
		</div>
		<div class="clearfix"></div>
	</div>
	<!-- End Status Module -->
<?php endif; ?>
<?php if (!$cpanel) : ?>
	<!-- Subheader -->
	<a class="btn btn-subhead" data-toggle="collapse"
		data-target=".subhead-collapse"><?php echo JText::_('TPL_ALLOANDGO_TOOLBAR'); ?>
		<span class="icon-wrench"></span></a>
	<div class="subhead-collapse collapse">
		<div class="subhead">
			<div class="container-fluid">
				<div id="container-collapse" class="container-collapse"></div>
				<div class="row-fluid">
					<div class="span12">
						<jdoc:include type="modules" name="toolbar" style="no" />
					</div>
				</div>
			</div>
		</div>
	</div>
<?php else : ?>
	<div style="margin-bottom: 20px"></div>
<?php endif; ?>
<!-- container-fluid -->
	<div class="container-fluid container-main">
		<section id="content">
			<!-- Begin Content -->
			<jdoc:include type="modules" name="top" style="xhtml" />
			<div class="row-fluid">
			<?php if ($showSubmenu) : ?>
			<div class="span2">
					<jdoc:include type="modules" name="submenu" style="none" />
				</div>
				<div class="span10">
				<?php else : ?>
				<div class="span12">
					<?php endif; ?>
					<jdoc:include type="message" />
					<?php
					// Show the page title here if the header is hidden
					if (! $displayHeader) :
						?>
						<h1 class="content-title"><?php echo JHtml::_('string.truncate', $app->JComponentTitle, 0, false, false); ?></h1>
					<?php endif; ?>
					<jdoc:include type="component" />
					</div>
				</div>
			<?php if ($this->countModules('bottom')) : ?>
				<jdoc:include type="modules" name="bottom" style="xhtml" />
			<?php endif; ?>
			<!-- End Content -->
		
		</section>

	<?php if (!$this->countModules('status') || (!$statusFixed && $this->countModules('status'))) : ?>
		<footer class="footer">
			<p align="center">
				<jdoc:include type="modules" name="footer" style="no" />
				&copy; <?php echo $sitename; ?> <?php echo date('Y'); ?></p>
		</footer>
	<?php endif; ?>
</div>
<?php if (($statusFixed) && ($this->countModules('status'))) : ?>
	<!-- Begin Status Module -->
	<div id="status" class="navbar navbar-fixed-bottom hidden-phone">
		<div class="btn-toolbar">
			<div class="btn-group pull-right">
				<p>
					 <?php echo $sitename; ?>
				</p>

			</div>
			<jdoc:include type="modules" name="status" style="no" />
		</div>
	</div>
	<!-- End Status Module -->
<?php endif; ?>
<jdoc:include type="modules" name="debug" style="none" />
<?php if ($stickyToolbar) : ?>
	<script>
		jQuery(function($)
		{

			var navTop;
			var isFixed = false;

			processScrollInit();
			processScroll();

			$(window).on('resize', processScrollInit);
			$(window).on('scroll', processScroll);

			function processScrollInit()
			{
				if ($('.subhead').length) {
					navTop = $('.subhead').length && $('.subhead').offset().top - <?php echo ($displayHeader || !$statusFixed) ? 30 : 20;?>;

					// Fix the container top
					$(".container-main").css("top", $('.subhead').height() + $('nav.navbar').height());

					// Only apply the scrollspy when the toolbar is not collapsed
					if (document.body.clientWidth > 480)
					{
						$('.subhead-collapse').height($('.subhead').height());
						$('.subhead').scrollspy({offset: {top: $('.subhead').offset().top - $('nav.navbar').height()}});
					}
				}
			}

			function processScroll()
			{
				if ($('.subhead').length) {
					var scrollTop = $(window).scrollTop();
					if (scrollTop >= navTop && !isFixed) {
						isFixed = true;
						$('.subhead').addClass('subhead-fixed');

						// Fix the container top
						$(".container-main").css("top", $('.subhead').height() + $('nav.navbar').height());
					} else if (scrollTop <= navTop && isFixed) {
						isFixed = false;
						$('.subhead').removeClass('subhead-fixed');
					}
				}
			}
		});
	</script>
<?php endif; ?>
</body>
</html>
