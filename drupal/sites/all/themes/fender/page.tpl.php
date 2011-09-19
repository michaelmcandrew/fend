<?php
// $Id: page.tpl.php,v 1.47 2010/11/05 01:25:33 dries Exp $

/**
 * @file
 * Default theme implementation to display a single Drupal page.
 *
 * Available variables:
 *
 * General utility variables:
 * - $base_path: The base URL path of the Drupal installation. At the very
 *   least, this will always default to /.
 * - $directory: The directory the template is located in, e.g. modules/system
 *   or themes/bartik.
 * - $is_front: TRUE if the current page is the front page.
 * - $logged_in: TRUE if the user is registered and signed in.
 * - $is_admin: TRUE if the user has permission to access administration pages.
 *
 * Site identity:
 * - $front_page: The URL of the front page. Use this instead of $base_path,
 *   when linking to the front page. This includes the language domain or
 *   prefix.
 * - $logo: The path to the logo image, as defined in theme configuration.
 * - $site_name: The name of the site, empty when display has been disabled
 *   in theme settings.
 * - $site_slogan: The slogan of the site, empty when display has been disabled
 *   in theme settings.
 *
 * Navigation:
 * - $main_menu (array): An array containing the Main menu links for the
 *   site, if they have been configured.
 * - $secondary_menu (array): An array containing the Secondary menu links for
 *   the site, if they have been configured.
 * - $breadcrumb: The breadcrumb trail for the current page.
 *
 * Page content (in order of occurrence in the default page.tpl.php):
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title: The page title, for use in the actual HTML content.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 * - $messages: HTML for status and error messages. Should be displayed
 *   prominently.
 * - $tabs (array): Tabs linking to any sub-pages beneath the current page
 *   (e.g., the view and edit tabs when displaying a node).
 * - $action_links (array): Actions local to the page, such as 'Add menu' on the
 *   menu administration interface.
 * - $feed_icons: A string of all feed icons for the current page.
 * - $node: The node object, if there is an automatically-loaded node
 *   associated with the page, and the node ID is the second argument
 *   in the page's path (e.g. node/12345 and node/12345/revisions, but not
 *   comment/reply/12345).
 *
 * Regions:
 * - $page['help']: Dynamic help text, mostly for admin pages.
 * - $page['highlighted']: Items for the highlighted content region.
 * - $page['content']: The main content of the current page.
 * - $page['sidebar_first']: Items for the first sidebar.
 * - $page['sidebar_second']: Items for the second sidebar.
 * - $page['header']: Items for the header region.
 * - $page['footer']: Items for the footer region.
 *
 * @see template_preprocess()
 * @see template_preprocess_page()
 * @see template_process()
 */
?>
<div id="wrapper">
	<div id="header">
		<?php print render($page['header']); ?>
		<div class="floatLeft">
			<?php if ($logo): ?>
		        <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" id="logo">
		          <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
		        </a>
		      <?php endif; ?>
		
			<?php if ($site_name || $site_slogan): ?>
				<?php if ($site_name): ?>
					<h1><?php print $site_name; ?>	</h1>
				<?php endif; ?>
				<?php if ($site_slogan): ?>
					<h2 id="site-slogan"><?php print $site_slogan; ?></h2>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		
		<div id="headerText">
			<?php if ($page['header_text']): ?>    
				<?php print render($page['header_text']); ?>  		
			<?php endif; ?>
		</div>
	</div>
	
	<div id="nav">
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery("#superfish ul.menu").superfish();
			});
		</script>

		<?php if ($page['superfish_menu']): ?>
			<div id="superfish">
				<?php print render ($page['superfish_menu']); ?>
			</div>
		<?php endif; ?>
	</div>
	
	<?php print $messages; ?>

	<div id="main">	
		<?php if ($page['feature_area']): ?>    
			<div id="featureArea">
				<?php print render($page['feature_area']); ?>
			</div>  		
		<?php endif; ?>
		
			
		<div id="leftCol">
			<?php if ($page['sidebar_first']): ?>    
				<?php print render($page['sidebar_first']); ?>  		
			<?php endif; ?>
		</div>
	
		<div id="content" class="<?php if ($page['sidebar_second']) { echo 'has-right-col'; } else { echo "no-right-col"; } ?>">
			<?php if ($page['content_top']): ?>    
				<div id="contentTop">			
					<?php print render($page['content_top']); ?>  		
				</div>
			<?php endif; ?>
			
			<?php if ($title): ?>
				<h1 class="title" id="page-title">
					<?php print $title; ?>
				</h1>
			<?php endif; ?>
			<?php if ($tabs): ?><?php print render($tabs); ?><?php endif; ?>
			<?php print render($page['content']); ?>
		</div>
	
		<?php if ($page['sidebar_second']): ?>    
			<div id="rightCol">			
				<?php print render($page['sidebar_second']); ?>  		
			</div>
		<?php endif; ?>
		
		<div class="clear"></div>
	</div>

	<div class="clear"></div>
</div>

<div id="footer">
	<?php print render($page['footer']); ?>
</div>