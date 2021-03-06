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
 $is_single = $sandbox['page']['type'] != 'single';
?>

  <!-- ______________________ HEADER _______________________ -->
  <header id="siteHeader" class="site-header container">
    <div class="inner wrapper clearfix">

    <?php
    /*
    * @logo
    */
    if ( $logo ) : ?>
      <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" id="logo">
        <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>"/>
      </a>
    <?php endif; ?>

    <?php
    /*
    * @structure only show the following HTML if at least one of them is shown
    */
    if ($site_name || $site_slogan): ?>
      <div class="name-slogan">

        <?php
          /*
          * @Site Name
          */
        ?>
        <<?php if ( $is_front ) { echo 'h1'; } else { echo 'h2'; } ?> id="site-name" class="title">
          <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home"><?php print $site_name; ?></a>
        </<?php if ( $is_front ) { echo 'h1'; } else { echo 'h2'; } ?>>

        <?php
          /*
          * @Site Slogan
          */
          if ($site_slogan) : ?>
          <div id="site-slogan"><?php print $site_slogan; ?></div>
        <?php endif; ?>

      </div>
    <?php endif; ?>

    <?php
      /*
      * @Region Header
      */
      if ($page['header']) { ?>

      <aside id="headerBar" class="sidebar region header-bar region-header-bar">
        <div class="inner clearfix">
          <?php print render($page['header']); ?>
        </div>
      </aside>

    <?php } elseif ($secondary_menu) { ?>

      <aside id="headerBar" class="menu sidebar header-bar secondary region-header-bar">
        <div class="inner clearfix">
          <?php print theme('links', array('links' => $secondary_menu, 'attributes' => array('id' => 'secondary', 'class' => array('links', 'clearfix', 'sub-menu')))); ?>
      </aside>

    <?php } ?>

    </div>
  </header> <!-- /header -->

    <!-- ______________________ MAIN NAVIGATION _______________________ -->

    <?php
      /*
      * @Region Menu Bar
      */
      if ( $page['menu_bar'] ) { ?>

      <section id="menuBar" class="menu-bar container sidebar region region-menu-bar">
        <div class="inner wrapper clearfix">
          <?php print render($page['menu_bar']); ?>
        </div>
      </section>

    <?php
      /*
      * @Theme Menu Bar
      */
      } elseif ( $main_menu ) { ?>

      <nav id="menuBar" class="menu-bar container sidebar region-menu-bar">
        <div class="inner wrapper clearfix">
        <?php print $main_menu; ?>
        </div>
      </nav>

    <?php } ?>

  <?php if ( $page['highlight'] ) { ?>
  <!-- ______________________ HIGHLIGHT _______________________ -->
  <aside id="highlightBar" class="container sidebar clearfix highlights region region-highlights">
    <div class="inner wrapper">
      <?php print render($page['highlight']); ?>
    </div>
  </aside>
  <?php } ?> <!-- /highlights -->

  <!-- ______________________ MAIN _______________________ -->

  <main class="main container"<?php print $attributes; ?>>
    <div class="inner wrapper clearfix">

  <?php // if ( !$is_single ) { ?>
    <?php if ($page['content_before']) { ?>
      <aside id="contentBefore" class="wrapper sidebar content-before region region-content-before container">
        <div class="inner">
          <?php print render($page['content_before']); ?>
        </div>
      </aside>
    <?php } ?>
  <?php // } ?> <!-- /content-before -->

    <?php
      /*
      * @Regions Content
      * wrap different container based on page type
      */
      if ( $is_single ) { ?>

      <section id="content" class="articles wrapper container">

    <?php } else { ?>

      <article id="content" class="wrapper container">

    <?php } ?>
        <div class="inner clearfix">

      <?php if ( $breadcrumb ) { ?>
        <?php print $breadcrumb; ?>
      <?php } ?>

      <?php if ( $is_single ) { ?>
        <?php if ($title): ?>
          <h1 class="title"><?php print $title; ?><?php print render($title_suffix); ?></h1>
        <?php endif; ?>
      <?php } else { ?>
        <?php if ( $title|| $messages || $tabs || $action_links): ?>
          <header id="content-header">

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

    <?php } ?>
        <?php print render($page['content']) ?>

        <?php print $feed_icons; ?>

      </div>
    <?php if ( $is_single ) { ?>
      </section>
    <?php } else { ?>
      </article>
    <?php } ?><!-- /content -->

  <?php // if ( !$is_single ) { ?>
    <?php if ($page['content_after']): ?>
      <aside id="contenAfter" class="sidebar region content-after region-content-before wrapper container">
        <div class="inner">
          <?php print render($page['content_after']); ?>
        </div>
      </aside>
    <?php endif; ?>
  <?php // } ?> <!-- /content-after -->
    </div>
  </main> <!-- /main -->

  <!-- ______________________ FOOTER _______________________ -->

  <footer id="siteFooter" class="region container clearfix site-footer region-site-footer">
    <div class="inner wrapper">
      <?php if ( $page['footer'] ) { ?>
        <?php print render($page['footer']); ?>
      <?php } else { ?>
        <p><?php echo date('Y'); ?> by <a href="http://www.service4me.at">service4me</a> and <a href="http://www.netzgestaltung.at" target="_blank">Netzgestaltung</a> under <a href="https://github.com/service4me/sandbox-drupal7-theme/blob/master/LICENSE" target="_blank">GPLv3</a></p>
      <?php } ?>
    </div>
  </footer> <!-- /footer -->

</div> <!-- /page -->
