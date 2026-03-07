<?php
/**
 * @file
 * Tabler theme settings. Every control on this page writes a theme setting.
 * template.php reads the same settings and sets the data-bs attributes.
 */

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function tabler_form_system_theme_settings_alter(&$form, &$form_state) {
  $form['tabler'] = array(
    '#type' => 'fieldset',
    '#title' => t('Tabler'),
    '#weight' => -10,
  );

  $form['tabler']['tabler_cdn'] = array(
    '#type' => 'checkbox',
    '#title' => t('Load Tabler from the CDN'),
    '#default_value' => theme_get_setting('tabler_cdn', 'tabler'),
    '#description' => t('Uncheck to use a copy you place in this theme.'),
  );

  $form['tabler']['tabler_version'] = array(
    '#type' => 'textfield',
    '#title' => t('Tabler version'),
    '#default_value' => theme_get_setting('tabler_version', 'tabler'),
    '#description' => t('CDN version. Example: 1.4.0'),
    '#size' => 12,
  );

  $form['tabler']['tabler_mode'] = array(
    '#type' => 'select',
    '#title' => t('Color mode'),
    '#options' => array(
      'auto' => t('Auto (follow the system)'),
      'light' => t('Light'),
      'dark' => t('Dark'),
    ),
    '#default_value' => theme_get_setting('tabler_mode', 'tabler'),
  );

  $form['tabler']['tabler_primary'] = array(
    '#type' => 'select',
    '#title' => t('Primary color'),
    '#options' => array(
      'blue' => t('Blue'),
      'azure' => t('Azure'),
      'indigo' => t('Indigo'),
      'purple' => t('Purple'),
      'pink' => t('Pink'),
      'red' => t('Red'),
      'orange' => t('Orange'),
      'yellow' => t('Yellow'),
      'lime' => t('Lime'),
      'green' => t('Green'),
      'teal' => t('Teal'),
      'cyan' => t('Cyan'),
    ),
    '#default_value' => theme_get_setting('tabler_primary', 'tabler'),
  );

  $form['tabler']['tabler_base'] = array(
    '#type' => 'select',
    '#title' => t('Neutral palette'),
    '#options' => array(
      'gray' => t('Gray'),
      'slate' => t('Slate'),
      'zinc' => t('Zinc'),
      'neutral' => t('Neutral'),
      'stone' => t('Stone'),
    ),
    '#default_value' => theme_get_setting('tabler_base', 'tabler'),
  );

  $form['tabler']['tabler_radius'] = array(
    '#type' => 'select',
    '#title' => t('Corner radius'),
    '#options' => array(
      '0' => t('Square'),
      '0.5' => t('Small'),
      '1' => t('Default'),
      '1.5' => t('Round'),
    ),
    '#default_value' => theme_get_setting('tabler_radius', 'tabler'),
  );

  $form['tabler']['tabler_layout'] = array(
    '#type' => 'select',
    '#title' => t('Page width'),
    '#options' => array(
      'default' => t('Default'),
      'fluid' => t('Fluid'),
      'boxed' => t('Boxed'),
    ),
    '#default_value' => theme_get_setting('tabler_layout', 'tabler'),
  );

  $form['tabler']['tabler_navbar'] = array(
    '#type' => 'select',
    '#title' => t('Navigation'),
    '#options' => array(
      'vertical' => t('Sidebar'),
      'horizontal' => t('Top bar'),
    ),
    '#default_value' => theme_get_setting('tabler_navbar', 'tabler'),
  );

  $form['tabler']['tabler_navbar_theme'] = array(
    '#type' => 'select',
    '#title' => t('Navigation color'),
    '#options' => array(
      'dark' => t('Dark'),
      'primary' => t('Primary'),
    ),
    '#default_value' => theme_get_setting('tabler_navbar_theme', 'tabler'),
  );
}
