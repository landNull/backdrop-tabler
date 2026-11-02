<?php
/**
 * @file
 * Reads the theme settings and writes the Tabler data-bs attributes.
 */

/**
 * Implements hook_preprocess_html().
 */
function tabler_preprocess_html(&$variables) {
  $mode = theme_get_setting('tabler_mode', 'tabler');
  $primary = theme_get_setting('tabler_primary', 'tabler');
  $base = theme_get_setting('tabler_base', 'tabler');
  $radius = theme_get_setting('tabler_radius', 'tabler');
  $layout = theme_get_setting('tabler_layout', 'tabler');
  $navbar = theme_get_setting('tabler_navbar', 'tabler');

  $variables['attributes_array']['data-bs-theme'] = $mode ? $mode : 'auto';
  $variables['attributes_array']['data-bs-theme-primary'] = $primary ? $primary : 'blue';
  $variables['attributes_array']['data-bs-theme-base'] = $base ? $base : 'gray';
  $variables['attributes_array']['data-bs-theme-radius'] = $radius !== NULL ? $radius : '1';
  $variables['attributes_array']['data-bs-layout'] = $layout ? $layout : 'fluid';
  $variables['attributes_array']['data-bs-navbar-position'] = $navbar ? $navbar : 'vertical';

  if (theme_get_setting('tabler_cdn', 'tabler')) {
    $ver = theme_get_setting('tabler_version', 'tabler');
    $ver = $ver ? $ver : '1.4.0';
    backdrop_add_css('https://cdn.jsdelivr.net/npm/@tabler/core@' . $ver . '/dist/css/tabler.min.css', array('type' => 'external'));
    backdrop_add_js('https://cdn.jsdelivr.net/npm/@tabler/core@' . $ver . '/dist/js/tabler.min.js', array('type' => 'external'));
  }
}
