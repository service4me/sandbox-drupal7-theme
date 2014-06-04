<?php

/**
 * Here we override the default HTML output of drupal.
 * refer to http://drupal.org/node/550722
 */
 
// Auto-rebuild the theme registry during theme development.
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

function sandbox_library(){

  $theme_path = drupal_get_path('theme', 'sandbox');

  $items = array();

  $items['modernizr'] = array(
    'title' => 'Modernizr',
    'website' => 'http://modernizr.com',
    'version' => '2.6.2',
    'js' => array(
      $theme_path . '/includes/initializr/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js' => array(
        'group' => JS_LIBRARY,
        'weight' => -21,
      )
    )
  );

  $items['jquery.migrate'] = array(
    'title' => 'jQuery migrate',
    'website' => 'https://github.com/jquery/jquery-migrate/#readme',
    'version' => '1.2.1',
    'js' => array(
      $theme_path . '/includes/jquery-migrate/jquery-migrate-1.2.1.min.js' => array(
        'group' => JS_LIBRARY,
        'weight' => -19.5,
      )
    )
  );

  return $items;
}

if (!module_exists('modernizr')) {
  drupal_add_library('sandbox', 'modernizr');
}
drupal_add_library('sandbox', 'jquery.migrate');

function sandbox_js_alter(&$javascript){

  $theme_path = drupal_get_path('theme', 'sandbox');

  $jQuery_version = '1.11.0';
  $jQuery_migrate_version = '1.2.1';
  $modernizr_version = '2.6.2';

  $jQuery_path = $theme_path . '/includes/initializr/js/vendor/jquery-1.11.0.min.js';
  $jQuery_migrate_path = $theme_path . '/includes/jquery-migrate/jquery-migrate-1.2.1.min.js';
  $modernizr_path = $theme_path . '/includes/initializr/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js';
 
  if (!module_exists('modernizr')) {
    drupal_add_js($modernizr_path, array(
      'group' => JS_LIBRARY,
      'every_page' => true,
      'version' => $modernizr_version,
      'weight' => -21
    ));
  }

  $javascript['misc/jquery.js']['data'] = $jQuery_path;
  $javascript['misc/jquery.js']['version'] = $jQuery_version;

  drupal_add_js($jQuery_migrate_path, array(
    'group' => JS_LIBRARY,
      'every_page' => true,
      'version' => $jQuery_migrate_version,
    'weight' => -19.5
  ));
  
}

function sandbox_preprocess(&$vars) {
}

function sandbox_preprocess_html(&$vars) {

}

function sandbox_preprocess_page(&$vars, $hook) {
  if (isset($vars['node_title'])) {
    $vars['title'] = $vars['node_title'];
  }
  
  $vars['classes_array'][] = 'wrapper';
  $vars['classes_array'][] = 'wrapper';
  // Adding a class to #main in wireframe mode
  if (theme_get_setting('wireframe_mode')) {
    $vars['classes_array'][] = 'wireframe-mode';
  }
  // Adding classes wether #navigation is here or not
  if (!empty($vars['main_menu']) or !empty($vars['sub_menu'])) {
    $vars['classes_array'][] = 'with-navigation';
  }
  if (!empty($vars['secondary_menu'])) {
    $vars['classes_array'][] = 'with-subnav';
  }

  // Add first/last classes to node listings about to be rendered.
  if (isset($vars['page']['content']['system_main']['nodes'])) {
    // All nids about to be loaded (without the #sorted attribute).
    $nids = element_children($vars['page']['content']['system_main']['nodes']);
    // Only add first/last classes if there is more than 1 node being rendered.
    if (count($nids) > 1) {
      $first_nid = reset($nids);
      $last_nid = end($nids);
      $first_node = $vars['page']['content']['system_main']['nodes'][$first_nid]['#node'];
      $first_node->classes_array = array('first');
      $last_node = $vars['page']['content']['system_main']['nodes'][$last_nid]['#node'];
      $last_node->classes_array = array('last');
    }
  }
}

function sandbox_preprocess_node(&$vars) {
  // Add a striping class.
  $vars['classes_array'][] = 'node-' . $vars['zebra'];

  // Merge first/last class (from sandbox_preprocess_page) into classes array of current node object.
  $node = $vars['node'];
  if (!empty($node->classes_array)) {
    $vars['classes_array'] = array_merge($vars['classes_array'], $node->classes_array);
  }
}

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
}

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
      $breadcrumb_separator = theme_get_setting('sandbox_breadcrumb_separator');
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
 * 	The string
 * @return
 * 	The converted string
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
  $sub_menu = '';

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

function sandbox_detect_orientation($height, $width){

  $orientation = array(

    'height' => $height, 
    'width' => $width, 
    'portrait' => false, 
    'landscape' => false, 
    'quadratic' => false, 

  );

  if ( $height > $width ){

    $orientation["portrait"] = true;
    $orientation["get_orientation"] = 'portrait';

  } else if ( $width > $height ){

    $orientation["landscape"] = true;
    $orientation["get_orientation"] = 'landscape';

  } else {

    $orientation["quadratic"] = true;
    $orientation["get_orientation"] = 'quadratic';

  }

  return $orientation;

}
