<?php
/**
 * @file
 * Standalone test harness for the Tabler theme. No Backdrop install needed:
 * stubs the Backdrop API, loads template.php and theme-settings.php, and
 * asserts the behavior of every theme hook.
 *
 * Usage: php tests/run_tests.php
 * Exit code 0 = all green, 1 = failures.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

define('CSS_THEME', 100);
define('JS_DEFAULT', 0);

$THEME_ROOT = dirname(__DIR__);

/* ---------------------------------------------------------------------------
 * Backdrop API stubs
 * ------------------------------------------------------------------------- */

$GLOBALS['SETTINGS'] = array();
$GLOBALS['HTTP_RESPONSE'] = NULL;
$GLOBALS['REQUEST_PATH'] = '';
$GLOBALS['PAGE_TITLE'] = '';
$GLOBALS['MENU_ITEM'] = array();
$GLOBALS['CAPTURE'] = NULL;
$GLOBALS['user'] = (object) array('roles' => array(2 => 'authenticated user'));

function theme_get_setting($key, $theme = NULL) {
  return isset($GLOBALS['SETTINGS'][$key]) ? $GLOBALS['SETTINGS'][$key] : NULL;
}
function set_settings(array $s) { $GLOBALS['SETTINGS'] = $s; }
function backdrop_get_path($type, $name) {
  global $THEME_ROOT;
  return $type === 'theme' && $name === 'tabler' ? $THEME_ROOT : 'themes/' . $name;
}
function backdrop_attributes($attrs) {
  $out = '';
  foreach ((array) $attrs as $k => $v) {
    if (is_array($v)) { $v = implode(' ', $v); }
    $out .= ' ' . $k . '="' . check_plain($v) . '"';
  }
  return $out;
}
function check_plain($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }
function t($s, $args = array()) {
  foreach ($args as $k => $v) {
    $v = (string) $v;
    if (isset($k[0]) && $k[0] === '@') { $v = check_plain($v); }
    $s = str_replace($k, $v, $s);
  }
  return $s;
}
function backdrop_clean_css_identifier($s) {
  return trim(preg_replace('/[^a-z0-9-]+/', '-', strtolower($s)), '-');
}
function request_path() { return $GLOBALS['REQUEST_PATH']; }
function backdrop_get_normal_path($p) { return $p; }
function backdrop_get_title() { return $GLOBALS['PAGE_TITLE']; }
function menu_get_item() { return $GLOBALS['MENU_ITEM']; }
function menu_tree($menu_name) { $GLOBALS['CAPTURE'] = 'menu_tree:' . $menu_name; return 'TREE(' . $menu_name . ')'; }
function menu_navigation_links($menu_name) {
  return array(
    'user/' => array('title' => 'My account', 'href' => 'user', 'localized_options' => array('attributes' => array())),
    'user/logout' => array('title' => 'Log out', 'href' => 'user/logout', 'localized_options' => array('attributes' => array())),
  );
}
function theme_links($menu) {
  $link_classes = array();
  foreach ($menu['links'] as $l) { $link_classes[] = implode(' ', isset($l['attributes']['class']) ? $l['attributes']['class'] : array()); }
  return 'LINKS[' . implode(' ', isset($menu['attributes']['class']) ? $menu['attributes']['class'] : array()) . '|' . implode(',', $link_classes) . ']';
}
function theme_menu_tree($variables) { return 'DEFAULT-TREE'; }
// Minimal theme() dispatcher: theme overrides (tabler_*) win, then fallbacks.
function theme($hook, $vars = array()) {
  if (function_exists('tabler_' . $hook)) { return call_user_func('tabler_' . $hook, $vars); }
  if ($hook === 'links__user_menu') { return theme_links($vars); }
  $fn = 'theme_' . $hook;
  if (function_exists($fn)) { return $fn($vars); }
  return '';
}
function backdrop_render($element) {
  $GLOBALS['CAPTURE'] = $element;
  return (isset($element['#prefix']) ? $element['#prefix'] : '') . 'RENDER' . (isset($element['#suffix']) ? $element['#suffix'] : '');
}
function render($el) { return ''; }
function hide(&$el) { $el = array(); }
function l($title, $path, $options = array()) { return '<a href="/' . $path . '">' . $title . '</a>'; }
function url($path) { return '/' . $path; }
function filter_xss_admin($s) { return strip_tags($s); }
function format_interval($s) { return $s . ' units'; }
function element_set_attributes(&$element, $attrs) { foreach ($attrs as $a) { if (isset($element['#' . $a])) { $element['#attributes'][$a] = $element['#' . $a]; } } }
function _form_set_class(&$element, $class) { foreach ((array) $class as $c) { $element['#attributes']['class'][] = $c; } }
function backdrop_js_defaults($data = NULL) {
  return array('type' => 'file', 'group' => JS_DEFAULT, 'every_page' => FALSE, 'weight' => 0, 'scope' => 'header', 'cache' => TRUE, 'defer' => FALSE, 'preprocess' => TRUE, 'version' => NULL, 'data' => $data, 'attributes' => array());
}
function backdrop_http_request($url, $options = array()) {
  return $GLOBALS['HTTP_RESPONSE'] ?: (object) array('code' => 0, 'error' => 'offline', 'data' => NULL);
}
function backdrop_json_decode($s) { return json_decode($s, TRUE); }
function backdrop_add_css($p, $o = array()) {}
function backdrop_add_js($p, $o = array()) {}

// Theme-function fallbacks the overrides delegate to.
function _el_classes($variables) { return 'EL{' . implode('|', isset($variables['element']['#attributes']['class']) ? $variables['element']['#attributes']['class'] : array()) . '}'; }
function theme_button($v) { return _el_classes($v); }
function theme_textfield($v) { return _el_classes($v); }
function theme_email($v) { return _el_classes($v); }
function theme_webform_email($v) { return _el_classes($v); }
function theme_password($v) { return _el_classes($v); }
function theme_search($v) { return _el_classes($v); }
function theme_textarea($v) { return _el_classes($v); }
function theme_checkbox($v) { return _el_classes($v); }
function theme_radio($v) { return _el_classes($v); }
function theme_select($v) { return _el_classes($v); }
function theme_fieldset($v) { return 'DEFAULT-FIELDSET'; }
function theme_form_element($v) {
  return 'FORM_ELEMENT[' . implode('|', isset($v['element']['#wrapper_attributes']['class']) ? $v['element']['#wrapper_attributes']['class'] : array()) . ']';
}

/* ---------------------------------------------------------------------------
 * Tiny assertion framework
 * ------------------------------------------------------------------------- */

$TESTS = 0; $FAILS = 0;
function ok($cond, $msg) {
  global $TESTS, $FAILS;
  $TESTS++;
  if ($cond) { echo "ok   - $msg\n"; }
  else { $FAILS++; echo "FAIL - $msg\n"; }
}
function is($got, $want, $msg) {
  ok($got === $want, $msg . ' (want ' . var_export($want, TRUE) . ', got ' . var_export($got, TRUE) . ')');
}
function contains($hay, $needle, $msg) {
  $found = is_string($hay) && strpos($hay, $needle) !== FALSE;
  ok($found, $msg . ($found ? '' : ' (missing ' . var_export($needle, TRUE) . ' in ' . var_export(substr((string) $hay, 0, 200), TRUE) . ')'));
}

/* ---------------------------------------------------------------------------
 * Load the theme code under test
 * ------------------------------------------------------------------------- */

require $THEME_ROOT . '/template.php';
require $THEME_ROOT . '/theme-settings.php';

$DEFAULTS = array(
  'tabler_cdn' => '1',
  'tabler_version' => '1.4.0',
  'tabler_mode' => 'auto',
  'tabler_primary' => 'blue',
  'tabler_base' => 'gray',
  'tabler_radius' => '1',
  'tabler_layout' => 'fluid',
  'tabler_navbar' => 'vertical',
  'tabler_navbar_theme' => 'dark',
  'tabler_navbar_user_menu' => 0,
  'tabler_breadcrumb_home' => 1,
  'tabler_breadcrumb_title' => 0,
  'tabler_datetime' => 0,
);

/* ---------------------------------------------------------------------------
 * 1. Framework loading: css_alter / js_alter
 * ------------------------------------------------------------------------- */

echo "\n== framework loading ==\n";

set_settings($DEFAULTS);
$css = array('theme-local' => array('data' => 'css/tabler.css', 'weight' => 0, 'group' => CSS_THEME));
tabler_css_alter($css);
$cdn_key = 'https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.min.css';
ok(isset($css[$cdn_key]), 'css_alter adds the jsDelivr CDN css');
is($css[$cdn_key]['type'], 'external', 'CDN css is external');
is($css[$cdn_key]['weight'], -2, 'framework css loads before theme css (weight -2 < 0)');
is($css[$cdn_key]['group'], CSS_THEME, 'framework css stays in CSS_THEME');
is($css[$cdn_key]['preprocess'], FALSE, 'minified CDN css is not preprocessed');

$js = array();
tabler_js_alter($js);
$cdn_js = 'https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js';
ok(isset($js[$cdn_js]), 'js_alter adds the jsDelivr CDN js');
is($js[$cdn_js]['type'], 'external', 'CDN js is external');
is($js[$cdn_js]['weight'], -1, 'framework js loads before theme js (weight -1)');
is($js[$cdn_js]['scope'], 'header', 'framework js keeps header scope');

set_settings(array_merge($DEFAULTS, array('tabler_cdn' => 'module')));
$css = array(); tabler_css_alter($css);
$bundle_css = $THEME_ROOT . '/dist/css/tabler.min.css';
ok(isset($css[$bundle_css]), 'bundled mode uses dist/css/tabler.min.css');
is($css[$bundle_css]['type'], 'file', 'bundled css is a local file');
ok(file_exists($bundle_css), 'bundled css exists on disk');
$js = array(); tabler_js_alter($js);
$bundle_js = $THEME_ROOT . '/dist/js/tabler.min.js';
ok(isset($js[$bundle_js]) && file_exists($bundle_js), 'bundled js exists on disk and is used');

set_settings(array_merge($DEFAULTS, array('tabler_cdn' => 0)));
$css = array('x' => 1); tabler_css_alter($css);
is(count($css), 1, 'cdn disabled: css untouched');
$js = array('x' => 1); tabler_js_alter($js);
is(count($js), 1, 'cdn disabled: js untouched');

set_settings(array_merge($DEFAULTS, array('tabler_version' => '9.9.9')));
$css = array(); tabler_css_alter($css);
ok(isset($css['https://cdn.jsdelivr.net/npm/@tabler/core@9.9.9/dist/css/tabler.min.css']), 'version setting drives the CDN url');

/* ---------------------------------------------------------------------------
 * 2. preprocess_page: html attributes + body classes
 * ------------------------------------------------------------------------- */

echo "\n== preprocess_page ==\n";

set_settings($DEFAULTS);
$GLOBALS['REQUEST_PATH'] = 'admin/content';
$variables = array('html_attributes' => array(), 'classes' => array(), 'is_front' => FALSE);
tabler_preprocess_page($variables);
is($variables['html_attributes']['data-bs-theme'], 'auto', 'data-bs-theme=auto');
is($variables['html_attributes']['data-bs-theme-primary'], 'blue', 'data-bs-theme-primary=blue');
is($variables['html_attributes']['data-bs-theme-base'], 'gray', 'data-bs-theme-base=gray');
is($variables['html_attributes']['data-bs-theme-radius'], '1', 'data-bs-theme-radius=1');
is($variables['html_attributes']['data-bs-layout'], 'fluid', 'data-bs-layout=fluid');
is($variables['html_attributes']['data-bs-navbar-position'], 'vertical', 'navbar position attribute');
ok(in_array('not-front', $variables['classes']), 'not-front body class');
ok(in_array('page-admin', $variables['classes']), 'page-admin class');
ok(in_array('page-admin-content', $variables['classes']), 'nested page-admin-content class');
ok(in_array('role-authenticated-user', $variables['classes']), 'role class');

$variables = array('html_attributes' => array(), 'classes' => array(), 'is_front' => TRUE);
tabler_preprocess_page($variables);
ok(!in_array('not-front', $variables['classes']), 'front page has no not-front class');

set_settings(array_merge($DEFAULTS, array('tabler_mode' => 'dark', 'tabler_radius' => '0.5')));
$variables = array('html_attributes' => array(), 'classes' => array(), 'is_front' => TRUE);
tabler_preprocess_page($variables);
is($variables['html_attributes']['data-bs-theme'], 'dark', 'mode setting overrides auto');
is($variables['html_attributes']['data-bs-theme-radius'], '0.5', 'radius setting passes through');

/* ---------------------------------------------------------------------------
 * 3. preprocess_layout: container class + user menu
 * ------------------------------------------------------------------------- */

echo "\n== preprocess_layout ==\n";

foreach (array('fluid' => 'container-fluid', 'boxed' => 'container', 'default' => 'container-xl') as $layout => $class) {
  set_settings(array_merge($DEFAULTS, array('tabler_layout' => $layout)));
  $variables = array();
  tabler_preprocess_layout($variables);
  is($variables['container_class'], $class, "layout=$layout -> $class");
}

set_settings(array_merge($DEFAULTS, array('tabler_navbar_user_menu' => 0)));
$variables = array(); tabler_preprocess_layout($variables);
is($variables['user_menu'], '', 'user menu off -> empty');

set_settings(array_merge($DEFAULTS, array('tabler_navbar_user_menu' => 1)));
$variables = array(); tabler_preprocess_layout($variables);
is($variables['user_menu'], 'TREE(user-menu)', 'user menu on -> user-menu tree rendered');

/* ---------------------------------------------------------------------------
 * 4. Menus
 * ------------------------------------------------------------------------- */

echo "\n== menus ==\n";

$variables = array('attributes' => array(), 'depth' => 0);
tabler_preprocess_menu_tree($variables);
ok(in_array('nav', $variables['attributes']['class']) && in_array('flex-column', $variables['attributes']['class']), 'depth 0 tree gets nav flex-column');
$variables = array('attributes' => array(), 'depth' => 2);
tabler_preprocess_menu_tree($variables);
ok(in_array('ms-3', $variables['attributes']['class']), 'submenu gets ms-3 indent');

$variables = array('element' => array('#attributes' => array('class' => array('active-trail')), '#localized_options' => array('attributes' => array())));
tabler_preprocess_menu_link($variables);
ok(in_array('nav-item', $variables['element']['#attributes']['class']), 'menu link li gets nav-item');
ok(in_array('nav-link', $variables['element']['#localized_options']['attributes']['class']), 'menu link a gets nav-link');
ok(in_array('active', $variables['element']['#localized_options']['attributes']['class']), 'active-trail adds active');

set_settings(array_merge($DEFAULTS, array('tabler_navbar_user_menu' => 1)));
$out = tabler_menu_tree__user_menu(array());
contains($out, 'dropstart', 'user menu wrapper uses dropstart');
contains($out, 'data-bs-toggle="dropdown"', 'user menu toggle uses data-bs-toggle');
contains($out, 'Account', 'user menu has an accessible label');
contains($out, 'dropdown-menu', 'user menu links render as dropdown-menu');
contains($out, 'dropdown-item', 'user menu links get dropdown-item');

set_settings(array_merge($DEFAULTS, array('tabler_navbar_user_menu' => 0)));
is(tabler_menu_tree__user_menu(array()), 'DEFAULT-TREE', 'user menu off -> default tree');

/* ---------------------------------------------------------------------------
 * 5. Breadcrumb
 * ------------------------------------------------------------------------- */

echo "\n== breadcrumb ==\n";

set_settings($DEFAULTS);
$out = tabler_breadcrumb(array('breadcrumb' => array('<a href="/">Home</a>', '<a href="/x">X</a>', 'Current')));
contains($out, '<nav aria-label="breadcrumb">', 'breadcrumb nav wrapper');
contains($out, '<ol class="breadcrumb">', 'breadcrumb ol');
contains($out, '<li class="breadcrumb-item active" aria-current="page">Current</li>', 'last item active with aria-current');
contains($out, 'You are here', 'screen-reader heading');
is(tabler_breadcrumb(array('breadcrumb' => array())), '', 'empty breadcrumb renders nothing');

$GLOBALS['PAGE_TITLE'] = 'Current Page';
$GLOBALS['MENU_ITEM'] = array();
$variables = array('breadcrumb' => array('<a href="/">Home</a>', 'X'));
tabler_preprocess_breadcrumb($variables);
is(count($variables['breadcrumb']), 2, 'breadcrumb_home on keeps home link');

set_settings(array_merge($DEFAULTS, array('tabler_breadcrumb_home' => 0, 'tabler_breadcrumb_title' => 1)));
$variables = array('breadcrumb' => array('<a href="/">Home</a>', 'X'));
tabler_preprocess_breadcrumb($variables);
is($variables['breadcrumb'][0], 'X', 'breadcrumb_home off drops home link');
is(end($variables['breadcrumb']), 'Current Page', 'breadcrumb_title on appends current title');

/* ---------------------------------------------------------------------------
 * 6. Form element mapping
 * ------------------------------------------------------------------------- */

echo "\n== forms ==\n";

contains(tabler_textfield(array('element' => array('#attributes' => array()))), 'form-control', 'textfield gets form-control');
contains(tabler_email(array('element' => array('#attributes' => array()))), 'form-control', 'email gets form-control');
contains(tabler_password(array('element' => array('#attributes' => array()))), 'form-control', 'password gets form-control');
contains(tabler_textarea(array('element' => array('#attributes' => array()))), 'form-control', 'textarea gets form-control');
contains(tabler_search(array('element' => array('#attributes' => array()))), 'form-control', 'search gets form-control');
$out = tabler_select(array('element' => array('#attributes' => array(), '#size' => 5)));
contains($out, 'form-select', 'select gets form-select');
contains(tabler_checkbox(array('element' => array('#attributes' => array()))), 'form-check-input', 'checkbox gets form-check-input');
contains(tabler_radio(array('element' => array('#attributes' => array()))), 'form-check-input', 'radio gets form-check-input');

$out = tabler_form_element(array('element' => array('#type' => 'checkbox', '#attributes' => array(), '#wrapper_attributes' => array(), '#description' => 'Help text')));
contains($out, 'FORM_ELEMENT[form-check]', 'checkbox wrapper gets form-check');
contains($out, '<div class="form-hint">Help text</div>', 'description becomes form-hint');

$out = tabler_button(array('element' => array('#id' => 'edit-submit', '#attributes' => array('class' => array('button', 'button-secondary')))));
contains($out, 'btn', 'button gets btn');
contains($out, 'btn-secondary', 'secondary maps to btn-secondary');
ok(strpos($out, 'button') === FALSE, 'old button class replaced');
$out = tabler_button(array('element' => array('#id' => 'edit-delete', '#attributes' => array('class' => array('button-secondary')))));
contains($out, 'btn-danger', 'delete button becomes btn-danger');
$out = tabler_button(array('element' => array('#id' => 'edit-save', '#attributes' => array())));
contains($out, 'btn-primary', 'default button is btn-primary');

$out = tabler_fieldset(array('element' => array('#title' => 'Group', '#children' => 'FIELDS', '#attributes' => array(), '#id' => 'fs1')));
contains($out, 'class="form-wrapper card mb-3"', 'fieldset renders as card');
contains($out, '<legend class="card-header">', 'legend is card-header');
contains($out, 'fieldset-wrapper card-body', 'wrapper is card-body');
$out = tabler_fieldset(array('element' => array('#group_fieldset' => TRUE, '#attributes' => array())));
is($out, 'DEFAULT-FIELDSET', 'fieldset inside vertical tabs keeps default rendering');

$form = array('actions' => array('cancel' => array('#type' => 'link', '#options' => array('attributes' => array()))));
$form_state = array();
tabler_form_alter($form, $form_state, 'some_form');
ok(in_array('btn', $form['actions']['cancel']['#options']['attributes']['class']) && in_array('btn-secondary', $form['actions']['cancel']['#options']['attributes']['class']), 'cancel link becomes a secondary button');

/* ---------------------------------------------------------------------------
 * 7. Tasks, actions, dropbutton, tables
 * ------------------------------------------------------------------------- */

echo "\n== tasks/tables ==\n";

$link = array('#link' => array('localized_options' => array('attributes' => array('class' => array()))));
$variables = array('primary' => array('a' => $link), 'secondary' => array('b' => $link));
$out = tabler_menu_local_tasks($variables);
contains($out, 'nav nav-tabs tabs-primary', 'primary tasks as nav-tabs');
contains($out, 'nav nav-pills secondary', 'secondary tasks as nav-pills');
ok(in_array('nav-link', $variables['primary']['a']['#link']['localized_options']['attributes']['class']), 'task links get nav-link');

$menu = array('links' => array('x' => array('attributes' => array('class' => array()))));
$out = tabler_links__dropbutton($menu);
contains($out, 'btn', 'dropbutton links get btn');

$variables = array('attributes' => array('class' => array()));
tabler_preprocess_table($variables);
ok(in_array('table', $variables['attributes']['class']) && in_array('table-hover', $variables['attributes']['class']) && in_array('table-striped', $variables['attributes']['class']), 'tables get table classes');
$variables = array('attributes' => array('class' => array('table-no-striping')));
tabler_preprocess_table($variables);
ok(!in_array('table-striped', $variables['attributes']['class']), 'table-no-striping respected');

/* ---------------------------------------------------------------------------
 * 8. Admin pages
 * ------------------------------------------------------------------------- */

echo "\n== admin ==\n";

$out = tabler_admin_block(array('block' => array('show' => TRUE, 'title' => 'Content', 'content' => 'LIST', 'description' => '')));
contains($out, 'card mb-3', 'admin block is a card');
contains($out, '<h3 class="card-title">Content</h3>', 'admin block title in card-header');
contains($out, 'card-body', 'admin block content in card-body');
is(tabler_admin_block(array('block' => array('show' => FALSE))), '', 'hidden admin block renders nothing');

$out = tabler_node_add_list(array('content' => array(array('title' => 'Article', 'href' => 'node/add/article', 'description' => 'desc', 'localized_options' => array('attributes' => array())))));
contains($out, 'list-group', 'node add list uses list-group');
contains($out, 'list-group-item', 'items get list-group-item');

$out = tabler_system_admin_index(array('menu_items' => array('System' => array('desc', array('a' => array('title' => 'A link', 'href' => 'admin/a', 'localized_options' => array('attributes' => array())))))));
contains($out, 'admin row', 'admin index is a row');
contains($out, 'col-md-6', 'admin index two columns');

$out = tabler_progress_bar(array('percent' => 42, 'message' => 'Working'));
contains($out, 'class="progress"', 'progress bar markup');
contains($out, 'width: 42%', 'progress width');

/* ---------------------------------------------------------------------------
 * 9. Time ago
 * ------------------------------------------------------------------------- */

echo "\n== timeago ==\n";

set_settings(array_merge($DEFAULTS, array('tabler_datetime' => 1)));
$variables = array('elements' => array('#node' => (object) array('created' => time() - 3600)));
tabler_preprocess_node($variables);
contains(isset($variables['timeago']) ? $variables['timeago'] : '', 'ago', 'node timeago set when enabled');
$variables = array('elements' => array('#comment' => (object) array('changed' => time() - 60)));
tabler_preprocess_comment($variables);
contains(isset($variables['timeago']) ? $variables['timeago'] : '', 'ago', 'comment timeago set when enabled');

set_settings($DEFAULTS);
$variables = array('elements' => array('#node' => (object) array('created' => time())));
tabler_preprocess_node($variables);
ok(!isset($variables['timeago']), 'timeago off by default');

/* ---------------------------------------------------------------------------
 * 10. Theme settings form
 * ------------------------------------------------------------------------- */

echo "\n== theme settings form ==\n";

set_settings($DEFAULTS);
$GLOBALS['HTTP_RESPONSE'] = NULL; // offline
$form = array('theme' => array('#value' => 'tabler'));
$form_state = array();
tabler_form_system_theme_settings_alter($form, $form_state);
is($form['tabler_tabs']['#type'], 'vertical_tabs', 'settings page uses vertical tabs');
is($form['tabler_framework']['#group'], 'tabler_tabs', 'framework fieldset in the tab group');
is($form['tabler_appearance']['#group'], 'tabler_tabs', 'appearance fieldset in the tab group');
is($form['tabler_layout_group']['#group'], 'tabler_tabs', 'layout fieldset in the tab group');
is($form['tabler_chrome']['#group'], 'tabler_tabs', 'chrome fieldset in the tab group');
foreach (array('tabler_cdn', 'tabler_version', 'tabler_mode', 'tabler_primary', 'tabler_base', 'tabler_radius', 'tabler_layout', 'tabler_navbar', 'tabler_navbar_theme', 'tabler_navbar_user_menu', 'tabler_breadcrumb_home', 'tabler_breadcrumb_title', 'tabler_datetime') as $key) {
  $found = FALSE;
  foreach (array('tabler_framework', 'tabler_appearance', 'tabler_layout_group', 'tabler_chrome') as $group) {
    if (isset($form[$group][$key])) { $found = TRUE; }
  }
  ok($found, "setting element present: $key");
}
is($form['tabler_framework']['tabler_version']['#type'], 'textfield', 'offline: version falls back to textfield');
contains($form['tabler_framework']['tabler_cdn']['#options']['1'], 'CDN', 'cdn select has CDN option');
is($form['tabler_framework']['tabler_cdn']['#options']['module'], 'Bundled (dist/ in this theme)', 'cdn select has bundled option');
contains($form['tabler_tabs']['#attached']['js'][0], 'js/tabler-settings.js', 'settings js attached');
ok(file_exists($THEME_ROOT . '/js/tabler-settings.js'), 'settings js exists on disk');

$GLOBALS['HTTP_RESPONSE'] = (object) array('code' => 200, 'data' => json_encode(array('versions' => array(
  array('version' => '1.3.0'),
  array('version' => '1.5.0-beta'),
  array('version' => '1.4.0'),
))));
$form = array('theme' => array('#value' => 'tabler'));
tabler_form_system_theme_settings_alter($form, $form_state);
is($form['tabler_framework']['tabler_version']['#type'], 'select', 'online: version becomes a select');
$opts = $form['tabler_framework']['tabler_version']['#options'];
is(array_keys($opts), array('1.4.0', '1.3.0'), 'version select lists stable versions, current first');
ok(!isset($opts['1.5.0-beta']), 'prerelease versions excluded');

/* ---------------------------------------------------------------------------
 * 11. tabler.info defaults
 * ------------------------------------------------------------------------- */

echo "\n== tabler.info ==\n";

// Backdrop parses .info with its own backdrop_parse_info_format(); here we
// only need the settings[...] defaults, so extract them with a regex.
$info_text = file_get_contents($THEME_ROOT . '/tabler.info');
preg_match_all('/^settings\[([^\]]+)\]\s*=\s*(.+)$/m', $info_text, $m, PREG_SET_ORDER);
$info_settings = array();
foreach ($m as $row) { $info_settings[$row[1]] = trim($row[2], " \t\"'"); }
foreach (array('tabler_cdn', 'tabler_version', 'tabler_mode', 'tabler_navbar_user_menu', 'tabler_breadcrumb_home', 'tabler_breadcrumb_title', 'tabler_datetime') as $key) {
  ok(isset($info_settings[$key]), "info default: $key");
}
ok(file_exists($THEME_ROOT . '/css/tabler.css') && file_exists($THEME_ROOT . '/js/tabler.js'), 'info-referenced assets exist');

/* ---------------------------------------------------------------------------
 * 12. Template rendering smoke tests
 * ------------------------------------------------------------------------- */

echo "\n== template rendering ==\n";

set_settings($DEFAULTS);

// Layout.
$content = array('header' => 'HDR', 'navigation' => 'NAV', 'top' => 'TOP', 'content' => 'BODY', 'sidebar' => 'SIDE', 'footer' => 'FTR');
$container_class = 'container-xl';
$user_menu = '<div class="cog">COG</div>';
ob_start();
include $THEME_ROOT . '/layouts/tabler/layout--tabler.tpl.php';
$out = ob_get_clean();
foreach (array('tabler-sidebar', 'container-xl', 'COG', 'NAV', 'BODY', 'SIDE', 'FTR', 'page-wrapper') as $needle) {
  contains($out, $needle, "layout renders $needle");
}

// Node with timeago.
set_settings(array_merge($DEFAULTS, array('tabler_datetime' => 1)));
$variables = array('elements' => array('#node' => (object) array('created' => time() - 3600)));
tabler_preprocess_node($variables);
$node = (object) array('nid' => 7);
$classes = array('node'); $attributes = array(); $title_prefix = array(); $page = FALSE;
$title = 'Hello'; $node_url = '/node/7'; $title_suffix = array();
$display_submitted = TRUE; $user_picture = ''; $submitted = 'Submitted';
$timeago = $variables['timeago'];
$content_attributes = array(); $content = array('links' => array()); $comments = array();
ob_start();
include $THEME_ROOT . '/templates/node.tpl.php';
$out = ob_get_clean();
contains($out, 'card', 'node renders as card');
contains($out, 'card-title', 'node title in card-header');
contains($out, 'ago', 'node shows timeago');

// Comment without timeago.
set_settings($DEFAULTS);
$classes = array('comment'); $attributes = array(); $new = ''; $title_display = FALSE;
$title_prefix = array(); $title = ''; $title_classes = array(); $title_suffix = array();
$user_picture = ''; $submitted = 'Submitted'; $timeago = ''; $permalink = 'P';
$content_attributes = array(); $content = array('links' => array()); $signature = '';
ob_start();
include $THEME_ROOT . '/templates/comment.tpl.php';
$out = ob_get_clean();
contains($out, 'card mb-3', 'comment renders as card');
ok(strpos($out, 'ago') === FALSE, 'comment without timeago shows no timeago');

/* ---------------------------------------------------------------------------
 * 13. Syntax lint of every shipped php/js file
 * ------------------------------------------------------------------------- */

echo "\n== lint ==\n";

$php_files = array_merge(
  array($THEME_ROOT . '/template.php', $THEME_ROOT . '/theme-settings.php'),
  glob($THEME_ROOT . '/templates/*.tpl.php'),
  glob($THEME_ROOT . '/layouts/*/*.tpl.php')
);
foreach ($php_files as $f) {
  $out = shell_exec('php -l ' . escapeshellarg($f) . ' 2>&1');
  ok(strpos($out, 'No syntax errors') !== FALSE, 'php -l ' . str_replace($THEME_ROOT . '/', '', $f));
}
foreach (glob($THEME_ROOT . '/js/*.js') as $f) {
  $out = shell_exec('node --check ' . escapeshellarg($f) . ' 2>&1');
  ok($out === '' || $out === NULL, 'node --check ' . basename($f) . ($out ? " ($out)" : ''));
}

/* ------------------------------------------------------------------------- */

echo "\n----------------------------------------\n";
echo "$TESTS tests, $FAILS failures\n";
exit($FAILS ? 1 : 0);
