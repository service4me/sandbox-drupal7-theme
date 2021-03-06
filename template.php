<?php

/**
 * @file
 * template.php Sandbox Template-Functions file
 * @todo
 * !!! NEEDS to ReWrite !!!
 *
 *
 */


/**
 * Here we override the default HTML output of drupal.
 * refer to http://drupal.org/node/550722
 */

/**
 * Theme Settings
 * Auto-rebuild the theme registry during theme development.
 */
if (theme_get_setting('clear_registry')) {
  // Rebuild .info data.
  system_rebuild_theme_data();
  // Rebuild theme registry.
  drupal_theme_rebuild();
}
// Add Zen Tabs styles
if (theme_get_setting('sandbox_tabs')) {
  drupal_add_css( drupal_get_path('theme', 'sandbox') .'/css/tabs.css');
}

/**
 * Header elements
 * ===============
 */


/**
 * Remove unnecessary CSS files
 */
function sandbox_css_alter(&$css) {
  $exclude = array(
    'modules/aggregator/aggregator.css' => FALSE,
    'modules/block/block.css' => FALSE,
    'modules/book/book.css' => FALSE,
    'modules/file/file.css' => FALSE,
    'modules/filter/filter.css' => FALSE,
    'modules/forum/forum.css' => FALSE,
    'modules/help/help.css' => FALSE,
    'modules/menu/menu.css' => FALSE,
    'modules/node/node.css' => FALSE,
    'modules/poll/poll.css' => FALSE,
    'modules/profile/profile.css' => FALSE,
    'modules/search/search.css' => FALSE,
    'modules/system/system.css' => FALSE,
    'modules/system/system.menus.css' => FALSE,
    'modules/system/system.messages.css' => FALSE,
    'modules/system/system.theme.css' => FALSE,
    'modules/taxonomy/taxonomy.css' => FALSE,
    'modules/tracker/tracker.css' => FALSE,
    'modules/user/user.css' => FALSE,
  );
  $css = array_diff_key($css, $exclude);
}

/**
 * build Javascript stack
 * ======================
 * includes jQuery, jQuery migrate plugin, modernizr
 * includes themes "actions.js"
 */
function sandbox_js_alter(&$javascript){

  $base_path = base_path();
  $theme_path = drupal_get_path('theme', 'sandbox');

  // versions
  $jQuery_version = '1.11.3';
  $jQuery_migrate_version = '1.2.1';
  $modernizr_version = '2.6.2';

  // paths
  $jQuery_path = '/js/jquery-' . $jQuery_version . '.min.js';
  $jQuery_migrate_path = '/js/jquery-migrate-' . $jQuery_migrate_version . '.min.js';
  $modernizr_path = '/js/modernizr-' . $modernizr_version . '-respond-1.1.0.min.js';
  
  // plugin paths
  $modernizr_addons_path = '/js/modernizr-addons.js';
  $jQuery_plugins_path = '/js/jquery.plugins.js';
  
  // theme actions
  $theme_js_path = '/js/actions.js';

  // support for drupal modernizr module
  if ( module_exists('modernizr')) {
    $javascript['sites/all/libraries/modernizr/modernizr.custom.87422.js']['data'] = $base_path . $theme_path . $modernizr_path;
  } else {
    drupal_add_js($base_path . $theme_path . $modernizr_path, array(
      'group' => JS_LIBRARY,
      'every_page' => true,
      'version' => $modernizr_version,
      'weight' => -21
    ));  
  }
  
  // modernizr addons
  drupal_add_js($base_path . $theme_path . $modernizr_addons_path, array(
    'group' => JS_LIBRARY,
    'every_page' => true,
    'version' => $modernizr_version,
    'weight' => -20.5
  ));
  
  
  // support for jQuery update module
  if (!module_exists('jquery_update')) {
    $javascript['misc/jquery.js']['data'] = $base_path . $theme_path . $jQuery_path;
    $javascript['misc/jquery.js']['version'] = $jQuery_version;

    drupal_add_js($base_path . $theme_path . $jQuery_migrate_path, array(
      'group' => JS_LIBRARY,
      'every_page' => true,
      'version' => $jQuery_migrate_version,
      'weight' => -19.5
    ));
  }
  
  // add jquery-plugins
  drupal_add_js($base_path . $theme_path . $jQuery_plugins_path, array(
    'group' => JS_LIBRARY,
    'every_page' => true,
    'weight' => -19
  ));
  
  // add theme actions
  drupal_add_js($base_path . $theme_path . $theme_js_path, array(
    'group' => JS_LIBRARY,
    'every_page' => true,
    'weight' => -17.5
  ));
}

/**
 * Internal Functions
 * ==================
 */

/**
 * gets all node ids
 * =================
 *
 */
function sandbox_get_node_ids($page){
  $node_ids = false;
  
  if ( isset($page['content']['system_main']['nodes']) ) {
    $node_ids = element_children($page['content']['system_main']['nodes']);
  }
  return $node_ids;
}


/**
 * detects page type for various preprocess functions
 * ==================================================
 *
 */
function sandbox_detect_page_type($theme, $page){
  $pageType = 'collection';
  // echo '<pre>', var_dump($theme['page']), '</pre>';
  // echo '<pre>', var_dump($page), '</pre>';

  // Do we have nodes?
  if ( $theme['page']['node_ids'] ) {
  
    // More then one?
    if ( count($theme['page']['node_ids']) > 1 ) {
      $pageType = 'archive';
    } else {
      $pageType = 'single';
    }
  }
  return $pageType;
}

/**
 * Converts a string to a suitable html ID attribute.
 *
 * http://www.w3.org/TR/html4/struct/global.html#h-7.5.2 specifies what makes a
 * valid ID attribute in HTML. This function:
 *
 * - Ensure an ID starts with an alpha character by optionally adding an 'n'.
 * - Replaces any character except A-Z, numbers, and underscores with dashes.
 * - Converts entire string to lowercase.
 *
 * @param $string
 *  The string
 * @return
 *  The converted string
 */
function sandbox_id_safe($string) {
  // Replace with dashes anything that isn't A-Z, numbers, dashes, or underscores.
  $string = strtolower(preg_replace('/[^a-zA-Z0-9_-]+/', '-', $string));
  // If the first character is not a-z, add 'n' in front.
  if (!ctype_lower($string{0})) { // Don't use ctype_alpha since its locale aware.
    $string = 'id'. $string;
  }
  return $string;
}

/**
 * Template Preprocesses
 * =====================
 */

/**
 * Implements template_preprocess()
 * ================================
 * build template variable 'sandbox'
 */
function sandbox_preprocess(&$vars, $hook) {
  $current_theme = variable_get('theme_default','none');
  
  if ( !isset($vars['sandbox']) ) {
    $vars['sandbox'] = array(
      'name' => $current_theme,
      'settings' => array(
        'tabs' => filter_var(theme_get_setting('sandbox_tabs'), FILTER_VALIDATE_BOOLEAN),
        'classes' => filter_var(theme_get_setting('sandbox_classes'), FILTER_VALIDATE_BOOLEAN),
        'breadcrumb_separator' => theme_get_setting('sandbox_breadcrumb_separator'),
        'wireframe_mode' => filter_var(theme_get_setting('wireframe_mode'), FILTER_VALIDATE_BOOLEAN),
        'clear_registry' => filter_var(theme_get_setting('clear_registry'), FILTER_VALIDATE_BOOLEAN),
        'debug' => false,
      ),
      'debug' => '',
      'path' => drupal_get_path('theme', 'sandbox'),
      'page' => array(
        'elements' => array(),
      )
    );
  }
}

/**
 * Preproces for html.tpl.php
 */
function sandbox_preprocess_html(&$vars) {
  
  // get theme vars
  $theme = $vars['sandbox'];
  $page = $vars['page'];  
  // echo '<pre>', var_dump($theme), '</pre>';
  
  // Detect front page
  $theme['is_front'] = $vars['is_front'];
  
  // check theme settings for use of css classNames
  if ( $theme['settings']['classes'] ) {

    // Adding a class to #main in wireframe mode
    if ($theme['settings']['wireframe_mode']) {
      $vars['classes_array'][] = 'wireframe-mode';
    }

    // Adding classes wether #navigation is here or not
    if (!empty($vars['main_menu']) or !empty($vars['sub_menu'])) {
      $vars['classes_array'][] = 'with-navigation';
    }
    if (!empty($vars['secondary_menu'])) {
      $vars['classes_array'][] = 'with-subnav';
    }

    if ( !empty($vars['page']['content_before']) ) {
      $vars['classes_array'][] = 'with-content-before';
    }
    if ( !empty($vars['page']['content_after']) ) {
      $vars['classes_array'][] = 'with-content-after';
    }
    if ( !empty($vars['page']['content_before']) && !empty($vars['page']['content_after']) ) {
      $vars['classes_array'][] = 'with-content-regions';
    } elseif ( !empty($vars['page']['content_before']) || !empty($vars['page']['content_after']) ) {
      $vars['classes_array'][] = 'with-content-region';
    } else {
      $vars['classes_array'][] = 'no-content-regions';
    }

    // Classes for body element. Allows advanced theming based on context
    // (home page, node of certain type, etc.)
    if (!$vars['is_front']) {
      // Add unique class for each page.
      $path = drupal_get_path_alias($_GET['q']);
      // Add unique class for each website section.
      $pathArray = explode('/', $path);
      $section = $pathArray[0];

      if ( $pathArray[0] == 'node' ) {
        if ( $pathArray[1] == 'add' ) {
          $section = 'node-add';
        } elseif ( isset($pathArray[2]) && is_numeric($pathArray[1]) && ( $pathArray[2] == 'edit' || $pathArray[2] == 'delete' ) ) {
          $section = 'node-' . $pathArray[2];
        } else {
          // MAGIC BEGINS HERE
          $node = node_load($pathArray[1]);
          $results = field_view_field('node', $node, 'field_tags', array('default'));
          foreach ($results as $key => $result) {
            if (is_numeric($key)) {
              $theme['classes_array'][] = drupal_html_class($result['#title']);
            }
          }
          // MAGIC ENDS HERE
        }
      }

      //drupal_set_message($section, 'status', false);
      $vars['classes_array'][] = drupal_html_class('section-' . $section);
    }
    
    // get node ids
    $theme['page']['node_ids'] = sandbox_get_node_ids($page);
    
    // Set page type
    $theme['page']['type'] = sandbox_detect_page_type($theme, $page);

    // shorthand variables
    $vars['classes_array'][] = $theme['page']['type'];
  }  
  
  if ( $theme['page']['type'] === 'archive' ) {
    $theme['id'] = -1;
  } else if ( $theme['page']['type'] === 'single' ) {
    $theme['id'] = $theme['page']['node_ids'][0];
  }
  if ( $theme['is_front'] ) {
    $theme['id'] = 0;
  }
  if ( !isset($theme['id']) ) {
    $theme['id'] = -2;
  }
  
  if ( $theme['name'] == 'sandbox' ) {  
  // adds js variable "sandboxTheme_data"
  $jsTheme = array(
    'id' => intval($theme['id']),
    'info' => array(
      'is_drupal' => true,
      'is_front_page' => filter_var($theme['is_front'], FILTER_VALIDATE_BOOLEAN),
      'is_home' => filter_var($theme['is_front'], FILTER_VALIDATE_BOOLEAN),
    ),
  );
  
    drupal_add_js('
      var sandboxTheme_data = {
        theme:' . json_encode($jsTheme) . ',
        page: ' . json_encode($page) . ',
      }
    ', array(
      'group' => JS_LIBRARY,
      'type' => 'inline',
      'every_page' => true,
      'weight' => -18.5
    ));
  }
  
  
  $vars['sandbox'] = $theme;
}

/**
 * Override or insert variables into the page template for HTML output.
 */
function sandbox_process_html(&$variables) {
  // Hook into color.module.
  if (module_exists('color')) {
    _color_html_alter($variables);
  }
}

/**
 * Override or insert variables into the page template.
 */
function sandbox_process_page(&$variables) {
  // Hook into color.module.
  if (module_exists('color')) {
    _color_page_alter($variables);
  }
}

/**
 * Preproces for page.tpl.php
 */
function sandbox_preprocess_page(&$vars, $hook) {

  // get the theme variable
  $theme = $vars['sandbox'];
  $page = $vars['page'];

  // introducing page variables to the sandbox
  $theme['page'] = array(
    'elements' => array(),
    // 'source' => $page,
  );
 
  // get node ids
  $theme['page']['node_ids'] = sandbox_get_node_ids($page);
  
  // Set page type
  $theme['page']['type'] = sandbox_detect_page_type($theme, $page);

  if (isset($vars['node_title'])) {
    $vars['title'] = $vars['node_title'];
  }

  // Add first/last classes to node listings about to be rendered.
  if ( $theme['page']['type'] == 'archive' ) {

    // adding a new element to the page
    $vars['sandbox']['page']['elements']['root'] = array(
      'type' => 'section',
      'attributes' => array(
        'class' => 'class="articles wrapper container"',
      ),
    );

    // Only add first/last classes if there is more than 1 node being rendered.
    $first_nId = reset($theme['page']['node_ids']);
    $last_nId = end($theme['page']['node_ids']);
    $first_node = $page['content']['system_main']['nodes'][$first_nId]['#node'];
    $first_node->classes_array = array('first');
    $last_node = $page['content']['system_main']['nodes'][$last_nId]['#node'];
    $last_node->classes_array = array('last');
  }
  // shorthand variables
  $vars['sandbox_page'] = $theme['page'];
  $vars['sandbox_page_elements'] = $theme['page']['elements'];

  if ( $vars['main_menu'] ) {
    $pid = variable_get('menu_main_links_source', 'main-menu');
    $tree = menu_tree($pid);
    $vars['main_menu'] = drupal_render($tree);
  } else {
    $vars['main_menu'] = FALSE;
  }
  
  $jsTheme = array(
    'name' => '',
    'info' => array(
      'is_admin' => filter_var(path_is_admin(current_path()), FILTER_VALIDATE_BOOLEAN),
      'is_archive' => filter_var($theme['page']['type'] === 'archive', FILTER_VALIDATE_BOOLEAN),
      'is_author' => false,
      'is_category' => false,
      'is_page' => filter_var($theme['page']['type'] === 'single', FILTER_VALIDATE_BOOLEAN),
      'is_search' => false,
      'is_single' =>  filter_var($theme['page']['type'] === 'single', FILTER_VALIDATE_BOOLEAN),
      'is_singular' => filter_var($theme['page']['type'] === 'single', FILTER_VALIDATE_BOOLEAN),
      'is_tag' => false,
      'is_tax' => false,
    ),
  );
  
  if ( $theme['name'] == 'sandbox' ) { 
    drupal_add_js('
      (function($){
        $.extend(true, sandboxTheme_data.theme, ' . json_encode($jsTheme) . ');
        $.extend(true, sandboxTheme_data.page, ' . json_encode($page) . ');
      })(jQuery);
    ', array(
      'group' => JS_LIBRARY,
      'type' => 'inline',
      'every_page' => true,
      'weight' => -18
    ));
  }  
  
  $vars['sandbox'] = $theme;  
}

/**
 * Preproces for node.tpl.php
 */
function sandbox_preprocess_node(&$vars) {
  // $theme = $vars['sandbox'];
  // $theme['debug'] = $theme['classes_array'];
  // $theme['classes'] = implode(' ', $theme['classes_array']);

  // Add a striping class.
  $vars['classes_array'][] = 'node-' . $vars['zebra'];

  // Merge first/last class (from sandbox_preprocess_page) into classes array of current node object.
  $node = $vars['node'];
  if (!empty($node->classes_array)) {
    $vars['classes_array'] = array_merge($vars['classes_array'], $node->classes_array);
  }
}

/**
 * Preproces for block.tpl.php
 */
function sandbox_preprocess_block(&$vars, $hook) {
  // Add a striping class.
  $vars['classes_array'][] = 'block-' . $vars['block_zebra'];

  // Add first/last block classes
  $first_last = "";
  // If block id (count) is 1, it's first in region.
  if ($vars['block_id'] == '1') {
    $first_last = "first";
    $vars['classes_array'][] = $first_last;
  }
  // Count amount of blocks about to be rendered in that region.
  $block_count = count(block_list($vars['elements']['#block']->region));
  if ($vars['block_id'] == $block_count) {
    $first_last = "last";
    $vars['classes_array'][] = $first_last;
  }


  $is_block_main_menu = ( $vars['elements']['#block']->delta == 'main-menu' );

  if ( $is_block_main_menu ) {

    // $vars['classes_array'][] = 'clearfix';

  }
}

/**
 * Preproces for item lists
 * ========================
 * adds clearfix to pager lists
 */
function sandbox_preprocess_item_list(&$vars, $hook) {
  if ( is_array($vars['attributes']) && isset($vars['attributes']['class']) && is_array($vars['attributes']['class']) && count($vars['attributes']['class']) > 0 && $vars['attributes']['class'][0] === 'pager' ) {
    $vars['attributes']['class'][] = 'clearfix';
  }
}

/**
 * Special elements markup
 * =======================
 */

/**
 * Return a themed breadcrumb trail.
 *
 * @param $breadcrumb
 *   An array containing the breadcrumb links.
 * @return
 *   A string containing the breadcrumb output.
 */
function sandbox_breadcrumb($variables) {
  $breadcrumb = $variables['breadcrumb'];
  // Determine if we are to display the breadcrumb.
  $show_breadcrumb = theme_get_setting('sandbox_breadcrumb');
  if ($show_breadcrumb == 'yes' || $show_breadcrumb == 'admin' && arg(0) == 'admin') {

    // Optionally get rid of the homepage link.
    $show_breadcrumb_home = theme_get_setting('sandbox_breadcrumb_home');
    if (!$show_breadcrumb_home) {
      array_shift($breadcrumb);
    }

    // Return the breadcrumb with separators.
    if (!empty($breadcrumb)) {
      $breadcrumb_separator = isset($variables['sandbox']['settings']['breadcrumb_separator']) ? $variables['sandbox']['settings']['breadcrumb_separator'] : ' > ';
      $trailing_separator = $title = '';
      if (theme_get_setting('sandbox_breadcrumb_title')) {
        $item = menu_get_item();
        if (!empty($item['tab_parent'])) {
          // If we are on a non-default tab, use the tab's title.
          $title = check_plain($item['title']);
        }
        else {
          $title = drupal_get_title();
        }
        if ($title) {
          $trailing_separator = $breadcrumb_separator;
        }
      }
      elseif (theme_get_setting('sandbox_breadcrumb_trailing')) {
        $trailing_separator = $breadcrumb_separator;
      }

      // Provide a navigational heading to give context for breadcrumb links to
      // screen-reader users. Make the heading invisible with .element-invisible.
      $heading = '<h2 class="element-invisible">' . t('You are here') . '</h2>';

      return $heading . '<div class="breadcrumb">' . implode($breadcrumb_separator, $breadcrumb) . $trailing_separator . $title . '</div>';
    }
  }
  // Otherwise, return an empty string.
  return '';
}

/**
 * Alter the theme registry information returned from hook_theme().
 *
 * @param $theme_registry
 *   The entire cache of theme registry information, post-processing.
 *
 * @see
 *   Added this due to a fault with the current Drupal 7 implementation of template_preprocess_menu_tree()
 *   http://drupal.org/node/767404
 *
 */
function sandbox_theme_registry_alter(&$theme_registry) {
  foreach ( $theme_registry['menu_tree']['preprocess functions'] as $key => $value ) {
    if ( $value == 'template_preprocess_menu_tree' ) {
      unset($theme_registry['menu_tree']['preprocess functions'][$key]);
    }
  }
}

/**
 * Preprocesses the rendered tree for theme_menu_tree().
 * http://drupal.org/node/767404
 * PULL THE FIRST MENU ITEM AND GET THE MENU NAME FROM IT
 */
function sandbox_preprocess_menu_tree(&$variables) {
  $pop = array_slice($variables['tree'], 0, 1);
  $menu_item = array_pop($pop);
  $variables['menu'] = $menu_item['#original_link'];
  $variables['tree'] = $variables['tree']['#children'];
}

/**
 * IMPLEMENTATION OF: theme_menu_tree()
 *
 * Returns HTML for a wrapper for a menu sub-tree.
 * PRINT THE MENU NAME WE PASSED ON
 *
 * @param $variables
 *   An associative array containing:
 *   - tree: An HTML string containing the tree's items.
 *
 * @see     template_preprocess_menu_tree()
 * @ingroup themeable
 */
function sandbox_menu_tree($variables) {
  return '<ul class="menu' . (($variables['menu']['menu_name']) ? ' ' . $variables['menu']['menu_name'] : '') . '">' . $variables['tree'] . '</ul>';
}


/**
 * Generate the HTML output for a menu link and submenu.
 *
 * @param $variables
 *  An associative array containing:
 *   - element: Structured array data for a menu link.
 *
 * @return
 *  A themed HTML string.
 *
 * @ingroup themeable
 *
 */
function sandbox_menu_link(array $variables){

  global $user;
  $element = $variables['element'];
  $sub_menu = false;

  if ( $element['#below'] ) {
    $sub_menu = drupal_render($element['#below']);
  }

  // Check if the user is logged in, that you are in the correct menu,
  // and that you have the right menu item
  if ( $user->uid != 0 && $element['#theme'] == 'menu_link__user_menu' && $element['#title'] == t('My account') ) {

    $element['#title'] = t('Hallo') . ' <strong>' . $user->name . '</strong>';

    // Add 'html' = TRUE to the link options
    $element['#localized_options']['html'] = TRUE;

    // Adding a class to determine the user link
    $element['#attributes']['class'][] = 'user_link';

  }

  $output = l($element['#title'], $element['#href'], $element['#localized_options']);

  // Adding a class depending on the TITLE of the link (not constant)
  $element['#attributes']['class'][] = sandbox_id_safe($element['#title']);

  // Adding a class depending on the ID of the link (constant)
  $element['#attributes']['class'][] = 'mid-' . $element['#original_link']['mlid'];

  return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";

}

/**
 * Override or insert variables into theme_menu_local_task().
 */
function sandbox_preprocess_menu_local_task(&$variables) {
  $link =& $variables['element']['#link'];

  // If the link does not contain HTML already, check_plain() it now.
  // After we set 'html'=TRUE the link will not be sanitized by l().
  if (empty($link['localized_options']['html'])) {
    $link['title'] = check_plain($link['title']);
  }
  $link['localized_options']['html'] = TRUE;
  $link['title'] = '<span class="tab">' . $link['title'] . '</span>';
}

/**
 * Duplicate of theme_menu_local_tasks() but adds clearfix to tabs.
 */
function sandbox_menu_local_tasks(&$variables) {
  $output = '';

  if (!empty($variables['primary'])) {
    $variables['primary']['#prefix'] = '<h2 class="element-invisible">' . t('Primary tabs') . '</h2>';
    $variables['primary']['#prefix'] .= '<ul class="tabs primary clearfix">';
    $variables['primary']['#suffix'] = '</ul>';
    $output .= drupal_render($variables['primary']);
  }
  if (!empty($variables['secondary'])) {
    $variables['secondary']['#prefix'] = '<h2 class="element-invisible">' . t('Secondary tabs') . '</h2>';
    $variables['secondary']['#prefix'] .= '<ul class="tabs secondary clearfix">';
    $variables['secondary']['#suffix'] = '</ul>';
    $output .= drupal_render($variables['secondary']);
  }

  return $output;
}
