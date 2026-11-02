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

/**
 * Implements hook_preprocess_menu_tree().
 *
 * Adds Bootstrap 5 / Tabler nav classes. Reference: Barrio menu markup.
 * @see https://github.com/landNull/d-LitFunc/blob/main/standards.md
 */
function tabler_preprocess_menu_tree(&$variables) {
  if (empty($variables['attributes']['class'])) {
    $variables['attributes']['class'] = array();
  }
  if ($variables['depth'] == 0) {
    $variables['attributes']['class'][] = 'nav';
    $variables['attributes']['class'][] = 'flex-column';
  }
  else {
    $variables['attributes']['class'][] = 'nav';
    $variables['attributes']['class'][] = 'flex-column';
    $variables['attributes']['class'][] = 'ms-3';
  }
}

/**
 * Implements hook_preprocess_menu_link().
 *
 * Adds nav-item and nav-link. Reference: Barrio menu markup.
 */
function tabler_preprocess_menu_link(&$variables) {
  $element = &$variables['element'];
  if (empty($element['#attributes']['class'])) {
    $element['#attributes']['class'] = array();
  }
  $element['#attributes']['class'][] = 'nav-item';

  if (empty($element['#localized_options']['attributes']['class'])) {
    $element['#localized_options']['attributes']['class'] = array();
  }
  $element['#localized_options']['attributes']['class'][] = 'nav-link';

  // Active state for Tabler / Bootstrap 5.
  $classes = $element['#attributes']['class'];
  if (in_array('active', $classes) || in_array('active-trail', $classes)) {
    $element['#localized_options']['attributes']['class'][] = 'active';
  }
}

/**
 * Implements theme_breadcrumb().
 *
 * Bootstrap 5 breadcrumb markup. Reference: Barrio breadcrumb component.
 */
function tabler_breadcrumb($variables) {
  $breadcrumb = $variables['breadcrumb'];
  if (empty($breadcrumb)) {
    return '';
  }
  $output = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
  $last = count($breadcrumb) - 1;
  foreach ($breadcrumb as $key => $item) {
    if ($key == $last) {
      $output .= '<li class="breadcrumb-item active" aria-current="page">' . $item . '</li>';
    }
    else {
      $output .= '<li class="breadcrumb-item">' . $item . '</li>';
    }
  }
  $output .= '</ol></nav>';
  return $output;
}
