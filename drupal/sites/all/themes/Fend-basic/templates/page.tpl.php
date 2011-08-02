<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language->language ?>" lang="<?php print $language->language ?>" dir="<?php print $language->dir ?>">
  <head>
   <title><?php print $head_title; ?></title>
    <?php print $head; ?>
    <?php print $styles; ?>
    <!--[if lte IE 6]><style type="text/css" media="all">@import "<?php print $base_path . path_to_theme() ?>/css/ie6.css"</style><![endif]-->
    <!--[if IE]><link rel="stylesheet" type="text/css" href="<?php print $base_path . path_to_theme() ?>/css/ie7.css"</style><![endif]-->


    <?php print $scripts; ?>
  </head>
  <body class="<?php print $body_classes; ?>">
    <div id="skip"><a href="#content"><?php print t('Skip to Content'); ?></a> <a href="#navigation"><?php print t('Skip to Navigation'); ?></a></div>  
    <div id="page">
    <!-- ______________________ HEADER _______________________ -->

    <div id="header">
      <div id="Leftheader">
	  <div id="logo-title">
	    
	    <?php if (!empty($logo)): ?>
	      <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" id="logo">
		<img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>"/>
	      </a>
	    <?php endif; ?>
	  </div> <!-- /logo-title -->  
	</div><!-- /Leftheader --> 
	  
	<div id="Rightheader">
	  	     <?php global $user; ?>
	      <?php if ($user->uid) : ?>
	      &nbsp; &nbsp;
	      <?php print l('My Account','user/'.$user->uid); ?>
	      <br/>
	      <?php print l("logout","logout"); ?>
	      <?php else : ?>
	      <?php print l("Login or Register","user/login") ; ?>
	      <?php endif; ?>
	  <?php if ($Rightheader): ?>
	    <div id="header-region">
	      <?php print $Rightheader; ?>
	    </div>
	  <?php endif; ?>
	    <div id="name-and-slogan">
	      <?php //if (!empty($site_slogan)): ?>
	      
		<div id="site-slogan"><?php print 'Unite for Diabetes' ?></div>
	      <?php //endif; ?>
	    </div> <!-- /name-and-slogan -->

	</div><!-- /RightHeader --> 
	  
	  <?php if ($header): ?>
	    <div id="header-region">
	      <?php print $header; ?>
	    </div>
	  <?php endif; ?>
	  <?php // Uncomment to add the search box.// print $search_box; ?>
    </div> <!-- /header -->
    <!-- ______________________ MAIN _______________________ -->

    <div id="main" class="clearfix">
      <?php
      if ($banner_front){
	$contentid = 'content-banner';
	$sidebarClass = 'sidebar-banner';
      }else{
	  $contentid = 'content';
	  $sidebarClass = "sidebar";
      }
      ?>
      
        <?php
      if ($left){
	$contentinner= 'content-inner';
	
      }else{
	if ($right){
	  $contentinner = 'contentinner-no-left';
	}else{
	  $contentinner = 'contentinner-no-left-no-right';
	}
      }
      
       
      ?>
      
      <div id="bread"><?php  breadcpath($title,$site_name) ?></div>
              <?php if (!empty($primary_links) || !empty($secondary_links)): ?>
          <div id="navigation" class="menu <?php if (!empty($primary_links)) { print "with-main-menu"; } if (!empty($secondary_links)) { print " with-sub-menu"; } ?>">
            <?php if (!empty($primary_links)){
	      $menu_name = variable_get('menu_primary_links_source', 'primary-links');
	      print menu_tree($menu_name);
	      }
	      ?>
            <?php if (!empty($secondary_links)){ print theme('links', $secondary_links, array('id' => 'secondary', 'class' => 'links sub-menu')); } ?>
          
   
    </div> <!-- /navigation -->
        <?php endif; ?>
	
	
	
      
      	<?php if ($banner_front): ?>
            <div id="banner_front">
              <?php print $banner_front; ?>
            </div> <!-- /#banner_front-->
        <?php endif; ?>
	
	  <div id="sidebar-first" class="column <?php echo $sidebarClass; ?> first">
            <div id="sidebar-first-inner" class="inner">
              <?php print $left; ?>
            </div>
          </div><!-- /sidebar-left -->
	
      
      <div id="<?php echo $contentid; ?>">
        <div id="<?php echo $contentinner?>" class="inner column center">
          <?php if ($content_top): ?>
            <div id="content-top">
              <?php print $content_top; ?>
            </div> <!-- /#content-top -->
          <?php endif; ?>

          <?php if ($breadcrumb || $title || $mission || $messages || $help || $tabs): ?>
            <div id="content-header">
              <?php if ($title): ?>
                <h1 class="title"><?php print $title; ?></h1>
              <?php endif; ?>

              <?php if ($mission): ?>
                <div id="mission"><?php print $mission; ?></div>
              <?php endif; ?>

              <?php print $messages; ?>

              <?php print $help; ?> 

              <?php if ($tabs): ?>
                <div class="tabs"><?php print $tabs; ?></div>
              <?php endif; ?>

            </div> <!-- /#content-header -->
          <?php endif; ?>

          <div id="content-area">
            <?php print $content; ?>
          </div> <!-- /#content-area -->

          <?php print $feed_icons; ?>

          <?php if ($content_bottom): ?>
            <div id="content-bottom">
              <?php print $content_bottom; ?>
            </div><!-- /#content-bottom -->
          <?php endif; ?>

          </div>
        </div> <!-- /content-inner /content -->

        <?php if ($right): ?>
          <div id="sidebar-second" class="column <?php echo $sidebarClass; ?> second">
            <div id="sidebar-second-inner" class="inner">
              <?php print $right; ?>
            </div>
          </div>
        <?php endif; ?> <!-- /sidebar-second -->
      </div> <!-- /main -->


    <div class="clearfix">  </div>
    </div> <!-- /page -->
    
          <!-- ______________________ FOOTER _______________________ -->

      <?php if(!empty($footer_message) || !empty($footer_block) || !empty($left_footer) || !empty($right_footer)): ?>
        <div id="footer" class="clearfix">
          <?php print $footer_message; ?>
          <?php print $footer_block; ?>
            <div id="left_col_footer">
              <?php print $left_footer; ?>
            </div> <!-- /#left_footer-->
	    <div id="right_col_footer">
              <?php print $right_footer; ?>
            </div> <!-- /#right_footer-->
        </div> <!-- /footer -->
      <?php endif; ?>
    
    <?php print $closure; ?>
  </body>
</html>