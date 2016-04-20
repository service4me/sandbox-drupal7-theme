<?php
/**
 * @file
 * image_style_thumbnail_wide.features.inc
 */

/**
 * Implements hook_image_default_styles().
 */
function image_style_thumbnail_wide_image_default_styles() {
  $styles = array();

  // Exported image style: thumbnail-wide.
  $styles['thumbnail-wide'] = array(
    'label' => 'Vorschautbild Weit',
    'effects' => array(
      5 => array(
        'name' => 'image_scale_and_crop',
        'data' => array(
          'width' => 400,
          'height' => 125,
        ),
        'weight' => 1,
      ),
    ),
  );

  return $styles;
}
