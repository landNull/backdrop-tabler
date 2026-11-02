<?php

/**
 * @file
 * Tabler theme settings. Every control on this page writes a theme setting
 * (the leaf element key is the setting name). template.php reads the same
 * settings and sets the data-bs attributes.
 */

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function tabler_form_system_theme_settings_alter(&$form, &$form_state)
{
    $theme_name = 'tabler';

  // Vertical tabs group. Fieldsets below use #group => 'tabler_tabs'.
    $form['tabler_tabs'] = array(
    '#type' => 'vertical_tabs',
    '#attached' => array(
      'js' => array(backdrop_get_path('theme', 'tabler') . '/js/tabler-settings.js'),
    ),
    '#prefix' => '<h2><small>' . t('Tabler Settings') . '</small></h2>',
    '#weight' => -10,
    );

  // Framework: CDN or bundled, and version.

    $form['tabler_framework'] = array(
    '#type' => 'fieldset',
    '#title' => t('Framework'),
    '#description' => t('Load the Tabler framework from the jsDelivr CDN or from the bundled copy in <code>dist/</code>. Disable only if you provide your own Tabler source.'),
    '#collapsible' => true,
    '#collapsed' => false,
    '#group' => 'tabler_tabs',
    );

  // Backwards compatibility: legacy boolean 1 means CDN.
    $cdn_setting = theme_get_setting('tabler_cdn', $theme_name);
    if ($cdn_setting && $cdn_setting !== 'module') {
        $cdn_default = '1';
    } else {
        $cdn_default = $cdn_setting;
    }
    $form['tabler_framework']['tabler_cdn'] = array(
    '#type' => 'select',
    '#title' => t('Tabler source'),
    '#options' => array(
      '1' => t('CDN (jsDelivr)'),
      'module' => t('Bundled (dist/ in this theme)'),
    ),
    '#default_value' => $cdn_default,
    '#empty_option' => t('Disabled'),
    '#empty_value' => null,
    );

  // Version: prefer the live list from the jsDelivr API; fall back to a
  // textfield when the server cannot reach it.
    $versions = array();
    $current_version = theme_get_setting('tabler_version', $theme_name);
    $request = backdrop_http_request('https://data.jsdelivr.com/v1/packages/npm/@tabler/core', array('timeout' => 3));
    if ($request && $request->code == 200 && !empty($request->data)) {
        $api = backdrop_json_decode($request->data);
        if (!empty($api['versions']) && is_array($api['versions'])) {
            if ($current_version) {
                $versions[$current_version] = $current_version;
            }
            foreach (array_reverse($api['versions']) as $v) {
                if (isset($v['version']) && strpos($v['version'], '-') === false) {
                    $versions[$v['version']] = $v['version'];
                    if (count($versions) >= 15) {
                        break;
                    }
                }
            }
        }
    }
    if ($versions) {
        $form['tabler_framework']['tabler_version'] = array(
        '#type' => 'select',
        '#title' => t('Tabler version'),
        '#options' => $versions,
        '#default_value' => $current_version ? $current_version : '1.4.0',
        '#description' => t('CDN version. Listed from the jsDelivr API.'),
        );
    } else {
        $form['tabler_framework']['tabler_version'] = array(
        '#type' => 'textfield',
        '#title' => t('Tabler version'),
        '#default_value' => $current_version ? $current_version : '1.4.0',
        '#description' => t('CDN version. Example: 1.4.0. (The jsDelivr API was unreachable, so no version list is available.)'),
        '#size' => 12,
        );
    }

  // Appearance: color mode, palettes, radius.

    $form['tabler_appearance'] = array(
    '#type' => 'fieldset',
    '#title' => t('Appearance'),
    '#collapsible' => true,
    '#collapsed' => true,
    '#group' => 'tabler_tabs',
    );

    $form['tabler_appearance']['tabler_mode'] = array(
    '#type' => 'select',
    '#title' => t('Color mode'),
    '#options' => array(
      'auto' => t('Auto (follow the system)'),
      'light' => t('Light'),
      'dark' => t('Dark'),
    ),
    '#default_value' => theme_get_setting('tabler_mode', $theme_name),
    );

    $form['tabler_appearance']['tabler_primary'] = array(
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
    '#default_value' => theme_get_setting('tabler_primary', $theme_name),
    );

    $form['tabler_appearance']['tabler_base'] = array(
    '#type' => 'select',
    '#title' => t('Neutral palette'),
    '#options' => array(
      'gray' => t('Gray'),
      'slate' => t('Slate'),
      'zinc' => t('Zinc'),
      'neutral' => t('Neutral'),
      'stone' => t('Stone'),
    ),
    '#default_value' => theme_get_setting('tabler_base', $theme_name),
    );

    $form['tabler_appearance']['tabler_radius'] = array(
    '#type' => 'select',
    '#title' => t('Corner radius'),
    '#options' => array(
      '0' => t('Square'),
      '0.5' => t('Small'),
      '1' => t('Default'),
      '1.5' => t('Round'),
    ),
    '#default_value' => theme_get_setting('tabler_radius', $theme_name),
    );

  // Layout and navigation.

    $form['tabler_layout_group'] = array(
    '#type' => 'fieldset',
    '#title' => t('Layout and navigation'),
    '#collapsible' => true,
    '#collapsed' => true,
    '#group' => 'tabler_tabs',
    );

    $form['tabler_layout_group']['tabler_layout'] = array(
    '#type' => 'select',
    '#title' => t('Page width'),
    '#options' => array(
      'default' => t('Default'),
      'fluid' => t('Fluid'),
      'boxed' => t('Boxed'),
    ),
    '#default_value' => theme_get_setting('tabler_layout', $theme_name),
    );

    $form['tabler_layout_group']['tabler_navbar'] = array(
    '#type' => 'select',
    '#title' => t('Navigation'),
    '#options' => array(
      'vertical' => t('Sidebar'),
      'horizontal' => t('Top bar'),
    ),
    '#default_value' => theme_get_setting('tabler_navbar', $theme_name),
    );

    $form['tabler_layout_group']['tabler_navbar_theme'] = array(
    '#type' => 'select',
    '#title' => t('Navigation color'),
    '#options' => array(
      'dark' => t('Dark'),
      'primary' => t('Primary'),
    ),
    '#default_value' => theme_get_setting('tabler_navbar_theme', $theme_name),
    );

    $form['tabler_layout_group']['tabler_navbar_user_menu'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show account menu in the navbar'),
    '#description' => t('Renders the user menu as a dropdown in the navigation header.'),
    '#default_value' => theme_get_setting('tabler_navbar_user_menu', $theme_name),
    );

  // Chrome: breadcrumbs and dates.

    $form['tabler_chrome'] = array(
    '#type' => 'fieldset',
    '#title' => t('Breadcrumbs and dates'),
    '#collapsible' => true,
    '#collapsed' => true,
    '#group' => 'tabler_tabs',
    );

    $form['tabler_chrome']['tabler_breadcrumb_home'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show "Home" breadcrumb link'),
    '#default_value' => theme_get_setting('tabler_breadcrumb_home', $theme_name),
    '#description' => t('If your site has a module dedicated to handling breadcrumbs already, ensure this setting is enabled.'),
    );

    $form['tabler_chrome']['tabler_breadcrumb_title'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show current page title at end'),
    '#default_value' => theme_get_setting('tabler_breadcrumb_title', $theme_name),
    '#description' => t('If your site has a module dedicated to handling breadcrumbs already, ensure this setting is disabled.'),
    );

    $form['tabler_chrome']['tabler_datetime'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show "XX time ago"'),
    '#default_value' => theme_get_setting('tabler_datetime', $theme_name),
    '#description' => t('If enabled, shows "XX time ago" instead of the raw date for nodes and comments.'),
    );

  // Debug.

    $form['tabler_debug_group'] = array(
    '#type' => 'fieldset',
    '#title' => t('Debug'),
    '#collapsible' => true,
    '#collapsed' => true,
    '#group' => 'tabler_tabs',
    );

    $form['tabler_debug_group']['tabler_debug'] = array(
    '#type' => 'checkbox',
    '#title' => t('Debug mode'),
    '#description' => t('Adds a "tabler-debug" body class and an on-page panel with the resolved theme settings and framework source. For template file-name comments, also enable the core theme debug on the performance page (admin/config/development/performance).'),
    '#default_value' => theme_get_setting('tabler_debug', $theme_name),
    );
}
