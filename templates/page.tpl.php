<?php

/**
 * @file
 * Default theme implementation to display a single Drupal page.
 *
 * The doctype, html, head and body tags are not in this template. Instead they
 * can be found in the html.tpl.php template in this directory.
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
 * @see html.tpl.php
 */
?>
<?php if ($page['page_first']): ?>
  <?php //echo var_dump($page); ?>
  <div id="page_top" class="clearfix region">
    <?php print render($page['page_first']); ?>
  </div>
<?php endif; ?>

<div id="page" class="<?php print $classes; ?>"<?php print $attributes; ?>>

  <!-- ______________________ HEADER _______________________ -->

  <header id="page-header"class="clearfix">
  
    <?php 
    /*
    * @region Header right
    */
    if ($page['header_right']): ?>
      <div id="header_right" class="region">
        <?php print render($page['header_right']); ?>
      </div>
    <?php endif; ?>
    
    <?php 
    /*
    * @logo
    */
    if ($logo): ?>
      <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" id="logo">
        <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>"/>
      </a>
    <?php endif; ?>

    <?php 
    /*
    * @structure only show the following HTML if at least one of them is shown 
    */
    if ($site_name || $site_slogan): ?>
      <div id="name-and-slogan">

        <?php 
          /*
          * @Site Name
          */
          if ($site_name): ?>
            <?php 
            /*
            * @Site Title If there is a Site title, this is no heading.
            *             Then the title will be the h1.
            */
            if ($title): ?>
            <div id="site-name">
              <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home"><?php print $site_name; ?></a>
            </div>
          <?php else: /* Use h1 when the content title is empty */ ?>
            <h1 id="site-name">
              <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home"><?php print $site_name; ?></a>
            </h1>
          <?php endif; ?>
        <?php endif; ?>

        <?php 
          /*
          * @Site Slogan
          */
          if ($site_slogan): ?>
          <div id="site-slogan"><?php print $site_slogan; ?></div>
        <?php endif; ?>

      </div>
    <?php endif; ?>

    <?php  
      /*
      * @Region Header
      */
      if ($page['header']): ?>
      <div id="header_overlay" class="region">
        <?php print render($page['header']); ?>
      </div>
    <?php endif; ?>

  </header> <!-- /header -->

  <!-- ______________________ MAIN NAVIGATION _______________________ -->

  <?php 
    /*
    * @Region Menu top
    */
    if ($page['menu_top']) { ?>

    <nav id="menu_top" class="clearfix region">
      <?php print render($page['menu_top']); ?>
    </nav>
  <?php } else { ?>

    <?php if ($main_menu): ?>
      <nav id="navigation" class="menu clearfix<?php if (!empty($main_menu)) {print " with-primary";} if (!empty($secondary_menu)) {print " with-secondary";} ?>">
        <?php print theme('links', array('links' => $main_menu, 'attributes' => array('id' => 'primary', 'class' => array('links', 'clearfix', 'main-menu')))); ?>
      </nav>
    <?php endif; ?>

  <?php } ?>

  <!-- ______________________ HIGHLIGHT _______________________ -->

  <?php if ( $page['highlight_first'] || $page['highlight_second'] || $page['highlight_third'] ) { ?>
  <div id="highlight" class="clearfix highlights regions">

    <?php if ($page['highlight_first']): ?>
      <div id="highlight-first" class="column highlight first <?php if ( $page['highlight_second'] && $page['highlight_third'] ) { ?>tripple<?php } else if ( $page['highlight_second'] || $page['highlight_third'] ) { ?>double<?php } else { ?>single<?php } ?>">
        <div id="highlight-first-inner" class="inner">
          <?php print render($page['highlight_first']); ?>
        </div>
      </div>
    <?php endif; ?> <!-- /highlight-first -->

    <?php if ($page['highlight_second']): ?>
      <div id="highlight-second" class="column highlight second <?php if ( $page['highlight_first'] && $page['highlight_third'] ) { ?>tripple<?php } else if ( $page['highlight_first'] || $page['highlight_third'] ) { ?>double<?php } else { ?>single<?php } ?>">
        <div id="highlight-second-inner" class="inner">
          <?php print render($page['highlight_second']); ?>
        </div>
      </div>
    <?php endif; ?> <!-- /highlight-second -->

    <?php if ($page['highlight_third']): ?>
      <div id="highlight-third" class="column highlight third <?php if ( $page['highlight_first'] && $page['highlight_second'] ) { ?>tripple<?php } else if ( $page['highlight_first'] || $page['highlight_second'] ) { ?>double<?php } else { ?>single<?php } ?>">
        <div id="highlight-third-inner" class="inner">
          <?php print render($page['highlight_third']); ?>
        </div>
      </div>
    <?php endif; ?> <!-- /highlight-third -->

  </div>
  <?php } ?> <!-- /highlights -->

  <!-- ______________________ MAIN _______________________ -->

  <section id="main" class="clearfix">

    <?php if ($page['sidebar_first']) { ?>
      <div id="sidebar-first" class="column sidebar first">
        <div id="sidebar-first-inner" class="inner">
          <?php print render($page['sidebar_first']); ?>
        </div>
      </div>
    <?php } else { ?> <!-- /sidebar-first -->

      <?php if ($secondary_menu): ?>
        <?php print theme('links', array('links' => $secondary_menu, 'attributes' => array('id' => 'secondary', 'class' => array('links', 'clearfix', 'sub-menu')))); ?>
      <?php endif; ?>

    <?php } ?>

      <article id="content">
        <div id="content-inner" class="inner column center">


          <!-- ______________________ CONTENT HIGHLIGHT _______________________ -->

          <?php if ( $page['content_highlight_top'] || $page['content_highlight_first'] || $page['content_highlight_second'] ): ?>
          <div id="content_highlights" class="clearfix highlights regions">

            <?php if ($page['content_highlight_top']): ?>
              <div id="content_highlight-top" class="column content_highlight top single region">
                <div id="content_highlight-top-inner" class="inner">
                  <?php print render($page['content_highlight_top']); ?>
                </div>
              </div>
            <?php endif; ?> <!-- /content_highlight-top -->

            <?php if ( $page['content_highlight_first'] || $page['content_highlight_second'] ): ?>
              <div class="clearfix wrapper">

              <?php if ($page['content_highlight_first']): ?>
                <div id="content_highlight-first" class="column content_highlight region first <?php if ( $page['content_highlight_second'] ) { ?>double<?php } else { ?>single<?php } ?>">
                  <div id="content_highlight-first-inner" class="inner">
                    <?php print render($page['content_highlight_first']); ?>
                  </div>
                </div>
              <?php endif; ?> <!-- /content_highlight-first -->

              <?php if ($page['content_highlight_second']): ?>
                <div id="content_highlight-second" class="column content_highlight region second <?php if ( $page['content_highlight_first'] ) { ?>double<?php } else { ?>single<?php } ?>">
                  <div id="content_highlight-second-inner" class="inner">
                    <?php print render($page['content_highlight_second']); ?>
                  </div>
                </div>
              <?php endif; ?> <!-- /highlight-second -->
            </div>
          <?php endif; ?> <!-- /highlight-wrapper -->
          </div>
        <?php endif; ?> 

        <?php if ($breadcrumb || $title|| $messages || $tabs || $action_links): ?>
          <header id="content-header">

            <?php print $breadcrumb; ?>

            <?php if ($title): ?>
              <h1 class="title"><?php print $title; ?><?php print render($title_suffix); ?></h1>
            <?php endif; ?>

            <?php print $messages; ?>
            <?php print render($page['help']); ?>

            <?php if ($tabs): ?>
              <div class="tabs"><?php print render($tabs); ?></div>
            <?php endif; ?>

            <?php if ($action_links): ?>
              <ul class="action-links"><?php print render($action_links); ?></ul>
            <?php endif; ?>
            
          </header> <!-- /#content-header -->
        <?php endif; ?>

        <!-- ______________________ CONTENT Top _______________________ -->

        <?php if ( $page['content_top_first'] || $page['content_top_second'] ): ?>
          <div id="content_top" class="clearfix wrapper regions">

            <?php if ($page['content_top_first']): ?>
              <div id="content_top-first" class="column content_top region first <?php if ( $page['content_top_second'] ) { ?>double<?php } else { ?>single<?php } ?>">
                <div id="content_top-first-inner" class="inner">
                  <?php print render($page['content_top_first']); ?>
                </div>
              </div>
            <?php endif; ?> <!-- /content_top-first -->

            <?php if ($page['content_top_second']): ?>
              <div id="content_top-second" class="column content_top region second <?php if ( $page['content_top_first'] ) { ?>double<?php } else { ?>single<?php } ?>">
                <div id="content_top-second-inner" class="inner">
                  <?php print render($page['content_top_second']); ?>
                </div>
              </div>
            <?php endif; ?> <!-- /content_top-second -->

          </div>
        <?php endif; ?> 

        <section id="content-area">
          <?php print render($page['content']) ?>
        </section>


        <!-- ______________________ CONTENT bottom _______________________ -->

        <?php if ( $page['content_bottom_first'] || $page['content_bottom_second'] ): ?>
          <div id="content_bottom" class="clearfix wrapper regions">

            <?php if ($page['content_bottom_first']): ?>
              <div id="content_bottom-first" class="column content_bottom region first <?php if ( $page['content_bottom_second'] ) { ?>double<?php } else { ?>single<?php } ?>">
                <div id="content_bottom-first-inner" class="inner">
                  <?php print render($page['content_bottom_first']); ?>
                </div>
              </div>
            <?php endif; ?> <!-- /content_bottom-first -->

            <?php if ($page['content_bottom_second']): ?>
              <div id="content_bottom-second" class="column content_bottom region second <?php if ( $page['content_bottom_first'] ) { ?>double<?php } else { ?>single<?php } ?>">
                <div id="content_bottom-second-inner" class="inner">
                  <?php print render($page['content_bottom_second']); ?>
                </div>
              </div>
            <?php endif; ?> <!-- /content_bottom-second -->
            
          </div>
        <?php endif; ?> 

        <?php print $feed_icons; ?>

      </div>
    </article> <!-- /content-inner /content -->

    <?php if ($page['sidebar_second']): ?>
      <div id="sidebar-second" class="column sidebar second">
        <div id="sidebar-second-inner" class="inner">
          <?php print render($page['sidebar_second']); ?>
        </div>
      </div>
    <?php endif; ?> <!-- /sidebar-second -->

  </section> <!-- /main -->

  <!-- ______________________ FOOTER _______________________ -->

  <?php if ( $page['footer'] ) : ?>
    <footer class="region">
      <?php print render($page['footer']); ?>
    </footer> <!-- /footer -->
  <?php endif; ?>

</div> <!-- /page -->