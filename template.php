<?php
/**
 * @file
 * Sets the Tabler data-bs attributes on the html tag.
 */

/**
 * Implements hook_preprocess_page().
 */
function tabler_preprocess_page(&$variables) {
  $mode = theme_get_setting('tabler_mode', 'tabler');
  $primary = theme_get_setting('tabler_primary', 'tabler');
  $base = theme_get_setting('tabler_base', 'tabler');
  $radius = theme_get_setting('tabler_radius', 'tabler');
  $layout = theme_get_setting('tabler_layout', 'tabler');
  $navbar = theme_get_setting('tabler_navbar', 'tabler');

  $variables['html_attributes']['data-bs-theme'] = $mode ? $mode : 'auto';
  $variables['html_attributes']['data-bs-theme-primary'] = $primary ? $primary : 'blue';
  $variables['html_attributes']['data-bs-theme-base'] = $base ? $base : 'gray';
  $variables['html_attributes']['data-bs-theme-radius'] = ($radius !== NULL && $radius !== '') ? $radius : '1';
  $variables['html_attributes']['data-bs-layout'] = $layout ? $layout : 'fluid';
  $variables['html_attributes']['data-bs-navbar-position'] = $navbar ? $navbar : 'vertical';

  if (theme_get_setting('tabler_cdn', 'tabler')) {
    $ver = theme_get_setting('tabler_version', 'tabler');
    $ver = $ver ? $ver : '1.4.0';
    backdrop_add_css('https://cdn.jsdelivr.net/npm/@tabler/core@' . $ver . '/dist/css/tabler.min.css', array('type' => 'external'));
    backdrop_add_js('https://cdn.jsdelivr.net/npm/@tabler/core@' . $ver . '/dist/js/tabler.min.js', array('type' => 'external'));
  }
}
