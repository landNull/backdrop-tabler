<?php

/**
 * @file
 * Tabler theme hooks. Sets the Tabler data-bs attributes on the html tag,
 * loads the Tabler framework (CDN or bundled), and maps Backdrop markup to
 * Bootstrap 5 / Tabler classes. Markup reference: Barrio.
 * Patterns ported from the Backdrop "Bootstrap 5 Lite" theme.
 * @see https://github.com/backdrop-contrib/bootstrap5_lite
 */

/**
 * Implements hook_css_alter().
 *
 * Loads the Tabler framework CSS before the theme's own css/tabler.css
 * (which ships at weight 0 in the CSS_THEME group via tabler.info), so the
 * local overrides always win.
 */
function tabler_css_alter(&$css)
{
    $theme_path = backdrop_get_path('theme', 'tabler');
    $cdn = theme_get_setting('tabler_cdn', 'tabler');
    if (!$cdn) {
        return;
    }
    if ($cdn === 'module' || $cdn === 'bundled') {
      // Bundled copy shipped in this theme.
        $src = $theme_path . '/dist/css/tabler.min.css';
        $type = 'file';
    } else {
      // CDN. Legacy truthy values (e.g. 1) mean CDN.
        $ver = theme_get_setting('tabler_version', 'tabler');
        $ver = $ver ? $ver : '1.4.0';
        $src = 'https://cdn.jsdelivr.net/npm/@tabler/core@' . $ver . '/dist/css/tabler.min.css';
        $type = 'external';
    }
    $css[$src] = array(
    'data' => $src,
    'type' => $type,
    'every_page' => true,
    'every_page_weight' => -1,
    'media' => 'all',
    'preprocess' => false,
    'group' => CSS_THEME,
    'browsers' => array('IE' => true, '!IE' => true),
    'weight' => -2,
    );
}

/**
 * Implements hook_js_alter().
 *
 * Loads the Tabler framework JS ahead of the theme's js/tabler.js.
 */
function tabler_js_alter(&$js)
{
    $theme_path = backdrop_get_path('theme', 'tabler');
    $cdn = theme_get_setting('tabler_cdn', 'tabler');
    if (!$cdn) {
        return;
    }
    if ($cdn === 'module' || $cdn === 'bundled') {
        $src = $theme_path . '/dist/js/tabler.min.js';
        $type = 'file';
    } else {
        $ver = theme_get_setting('tabler_version', 'tabler');
        $ver = $ver ? $ver : '1.4.0';
        $src = 'https://cdn.jsdelivr.net/npm/@tabler/core@' . $ver . '/dist/js/tabler.min.js';
        $type = 'external';
    }
    $js[$src] = array(
    'data' => $src,
    'type' => $type,
    'every_page' => true,
    'weight' => -1,
    ) + backdrop_js_defaults();
}

/**
 * Implements hook_preprocess_page().
 *
 * Sets the Tabler data-bs attributes on the html tag and adds body classes.
 */
function tabler_preprocess_page(&$variables)
{
    $mode = theme_get_setting('tabler_mode', 'tabler');
    $primary = theme_get_setting('tabler_primary', 'tabler');
    $base = theme_get_setting('tabler_base', 'tabler');
    $radius = theme_get_setting('tabler_radius', 'tabler');
    $layout = theme_get_setting('tabler_layout', 'tabler');
    $navbar = theme_get_setting('tabler_navbar', 'tabler');

    $variables['html_attributes']['data-bs-theme'] = $mode ? $mode : 'auto';
    $variables['html_attributes']['data-bs-theme-primary'] = $primary ? $primary : 'blue';
    $variables['html_attributes']['data-bs-theme-base'] = $base ? $base : 'gray';
    $variables['html_attributes']['data-bs-theme-radius'] = ($radius !== null && $radius !== '') ? $radius : '1';
    $variables['html_attributes']['data-bs-layout'] = $layout ? $layout : 'fluid';
    $variables['html_attributes']['data-bs-navbar-position'] = $navbar ? $navbar : 'vertical';

  // Add 'not-front' if we're not on the front page.
    if (!$variables['is_front']) {
        $variables['classes'][] = 'not-front';
    }

  // Add classes based on normal path parts.
    $normal_path = strtolower(backdrop_get_normal_path(request_path()));
    $normal_path = str_replace('_', '-', $normal_path);
    $path_parts = $normal_path ? explode('/', $normal_path) : array();
    foreach ($path_parts as $i => $part) {
        $variables['classes'][] = 'page-' . backdrop_clean_css_identifier(implode('-', array_slice($path_parts, 0, $i + 1)));
    }

  // Add classes based on user roles.
    global $user;
    foreach ($user->roles as $role) {
        $variables['classes'][] = 'role-' . backdrop_clean_css_identifier($role);
    }

  // Debug mode: body class plus an on-page panel with the resolved settings.
    if (theme_get_setting('tabler_debug', 'tabler')) {
        $variables['classes'][] = 'tabler-debug';
        $cdn = theme_get_setting('tabler_cdn', 'tabler');
        if (!$cdn) {
            $source = 'disabled (no framework loaded by the theme)';
        } elseif ($cdn === 'module' || $cdn === 'bundled') {
            $source = 'bundled dist/ in the theme';
        } else {
            $ver = theme_get_setting('tabler_version', 'tabler');
            $source = 'CDN @tabler/core@' . ($ver ? $ver : '1.4.0');
        }
        try {
            $core_theme_debug = config_get('system.core', 'theme_debug') ? 'on' : 'off';
        } catch (Exception $e) {
            $core_theme_debug = 'unknown';
        }
        $panel = '<pre class="tabler-debug-panel">' . check_plain(
            "Tabler debug\n" .
            'framework: ' . $source . "\n" .
            'mode: ' . $mode . '  primary: ' . $primary . '  base: ' . $base . '  radius: ' . $radius . "\n" .
            'layout: ' . $layout . '  navbar: ' . $navbar . '  navbar theme: ' . theme_get_setting('tabler_navbar_theme', 'tabler') . "\n" .
            'core theme_debug: ' . $core_theme_debug . ' (admin/config/development/performance)'
        ) . '</pre>';
        $variables['page_bottom'] = (isset($variables['page_bottom']) ? $variables['page_bottom'] : '') . $panel;
    }
}

/**
 * Implements hook_preprocess_layout().
 *
 * Computes the container class from the tabler_layout setting and renders
 * the user menu (cog dropdown) when enabled.
 */
function tabler_preprocess_layout(&$variables)
{
    $layout = theme_get_setting('tabler_layout', 'tabler');
    switch ($layout) {
        case 'fluid':
            $variables['container_class'] = 'container-fluid';
            break;

        case 'boxed':
            $variables['container_class'] = 'container';
            break;

        default:
            $variables['container_class'] = 'container-xl';
    }

    if (theme_get_setting('tabler_navbar_user_menu', 'tabler')) {
        $variables['user_menu'] = menu_tree('user-menu');
    } else {
        $variables['user_menu'] = '';
    }
}

/**
 * Implements hook_preprocess_menu_tree().
 *
 * Adds Bootstrap 5 / Tabler nav classes. Reference: Barrio menu markup.
 * @see https://github.com/landNull/d-LitFunc/blob/main/standards.md
 */
function tabler_preprocess_menu_tree(&$variables)
{
    if (empty($variables['attributes']['class'])) {
        $variables['attributes']['class'] = array();
    }
    if ($variables['depth'] == 0) {
        $variables['attributes']['class'][] = 'nav';
        $variables['attributes']['class'][] = 'flex-column';
    } else {
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
function tabler_preprocess_menu_link(&$variables)
{
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
 * Implements hook_links() for the user menu.
 *
 * Dropdown markup for the navbar cog menu.
 */
function tabler_links__user_menu($menu)
{
    foreach ($menu['links'] as $item => $link) {
        $menu['links'][$item]['attributes']['class'][] = 'dropdown-item';
    }
    $menu['attributes']['class'][] = 'dropdown-menu';
    return theme_links($menu);
}

/**
 * Implements theme_menu_tree__user_menu() wrapper.
 *
 * Renders the user menu as a cog-style dropdown in the navbar.
 */
function tabler_menu_tree__user_menu($variables)
{
    if (!theme_get_setting('tabler_navbar_user_menu', 'tabler')) {
        return theme_menu_tree($variables);
    }
    $menu = menu_navigation_links('user-menu');
    $links = $menu ? theme('links__user_menu', array('links' => $menu)) : null;
    return '
<div class="menu nav dropstart p-2 ms-auto">
  <div class="dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">' . t('Account') . '</a>
    ' . $links . '
  </div>
</div>';
}

/**
 * Implements hook_preprocess_breadcrumb().
 *
 * Optionally removes the home link and appends the current page title.
 */
function tabler_preprocess_breadcrumb(&$variables)
{
    $breadcrumb = &$variables['breadcrumb'];

  // Optionally get rid of the homepage link.
    if (!theme_get_setting('tabler_breadcrumb_home', 'tabler')) {
        array_shift($breadcrumb);
    }
    if (theme_get_setting('tabler_breadcrumb_title', 'tabler') && !empty($breadcrumb)) {
        $item = menu_get_item();
        $breadcrumb[] = !empty($item['tab_parent']) ? check_plain($item['title']) : backdrop_get_title();
    }
}

/**
 * Implements theme_breadcrumb().
 *
 * Bootstrap 5 breadcrumb markup. Reference: Barrio breadcrumb component.
 */
function tabler_breadcrumb($variables)
{
    $breadcrumb = $variables['breadcrumb'];
    if (empty($breadcrumb)) {
        return '';
    }
    $output = '<nav aria-label="breadcrumb">';
  // Navigational heading for screen-reader users.
    $output .= '<h2 class="element-invisible">' . t('You are here') . '</h2>';
    $output .= '<ol class="breadcrumb">';
    $last = count($breadcrumb) - 1;
    foreach ($breadcrumb as $key => $item) {
        if ($key == $last) {
            $output .= '<li class="breadcrumb-item active" aria-current="page">' . $item . '</li>';
        } else {
            $output .= '<li class="breadcrumb-item">' . $item . '</li>';
        }
    }
    $output .= '</ol></nav>';
    return $output;
}

/**
 * Implements hook_field_group_pre_render_alter().
 *
 * Keep fieldsets inside vertical tabs as plain fieldsets.
 */
function tabler_field_group_pre_render_alter(&$element, $group, &$form)
{
    if ($group->format_type == 'tab') {
        $element['#group_fieldset'] = true;
    }
}

/**
 * Returns HTML for a fieldset form element.
 *
 * Tabler card markup: card with a card-header legend and card-body wrapper.
 */
function tabler_fieldset($variables)
{
    $element = $variables['element'];
  // Fieldsets inside vertical tabs keep the default rendering.
    if ((isset($element['#group_fieldset']) && !empty($element['#group_fieldset'])) || !empty($element['#groups']['vertical_tabs'])) {
        return theme_fieldset($variables);
    }
    element_set_attributes($element, array('id'));
    _form_set_class($element, array('form-wrapper'));
    $element['#attributes']['class'][] = 'card';
    $element['#attributes']['class'][] = 'mb-3';
    $output = '<fieldset' . backdrop_attributes($element['#attributes']) . '>';
    if (!empty($element['#title'])) {
      // Always wrap fieldset legends in a SPAN for CSS positioning.
        $output .= '<legend class="card-header"><span class="fieldset-legend">' . $element['#title'] . '</span></legend>';
    }
    $output .= '<div class="fieldset-wrapper card-body">';
    if (!empty($element['#description'])) {
        $output .= '<div class="fieldset-description form-hint">' . $element['#description'] . '</div>';
    }
    $output .= $element['#children'];
    if (isset($element['#value'])) {
        $output .= $element['#value'];
    }
    $output .= '</div>';
    $output .= "</fieldset>\n";
    return $output;
}

/**
 * Returns HTML for a button form element.
 *
 * Maps Backdrop's button classes to Tabler / Bootstrap 5 btn classes.
 */
function tabler_button($variables)
{
    $element = &$variables['element'];
    if (isset($element['#attributes']['class'])) {
        $default = true;
        foreach ($element['#attributes']['class'] as $key => $class) {
            if (false !== strpos($class, 'secondary')) {
                $element['#attributes']['class'][$key] = (isset($element['#id']) && $element['#id'] == 'edit-delete') ? 'btn-danger' : 'btn-secondary';
                $default = false;
            } elseif (false !== strpos($class, 'button')) {
                $element['#attributes']['class'][$key] = str_replace('button', 'btn', $class);
                $default = false;
            }
        }
        if ($default) {
            $element['#attributes']['class'][] = 'btn-primary';
        }
    } else {
        $element['#attributes']['class'][] = 'btn-primary';
    }
    $element['#attributes']['class'][] = 'btn';
    return theme_button($variables);
}

/**
 * Returns HTML for an email form element.
 */
function tabler_email($variables)
{
    $variables['element']['#attributes']['class'][] = 'form-control';
    return theme_email($variables);
}

/**
 * Returns HTML for a webform email form element.
 */
function tabler_webform_email($variables)
{
    $variables['element']['#attributes']['class'][] = 'form-control';
    return theme_webform_email($variables);
}

/**
 * Returns HTML for a textfield form element.
 */
function tabler_textfield($variables)
{
    $variables['element']['#attributes']['class'][] = 'form-control';
    return theme_textfield($variables);
}

/**
 * Returns HTML for a textarea form element.
 */
function tabler_textarea($variables)
{
    $variables['element']['#attributes']['class'][] = 'form-control';
    return theme_textarea($variables);
}

/**
 * Returns HTML for a checkbox form element.
 */
function tabler_checkbox($variables)
{
    $variables['element']['#attributes']['class'][] = 'form-check-input';
    return theme_checkbox($variables);
}

/**
 * Returns HTML for a radio form element.
 */
function tabler_radio($variables)
{
    $variables['element']['#attributes']['class'][] = 'form-check-input';
    return theme_radio($variables);
}

/**
 * Returns HTML for a form element.
 *
 * Wraps checkboxes and radios in .form-check and moves the description into
 * a .form-hint line.
 */
function tabler_form_element($variables)
{
    if (isset($variables['element']['#type'])) {
        if ($variables['element']['#type'] == 'checkbox' || $variables['element']['#type'] == 'radio') {
            $variables['element']['#wrapper_attributes']['class'][] = 'form-check';
        }
    }
    $description = false;
    if (isset($variables['element']['#description'])) {
        $description = $variables['element']['#description'];
        unset($variables['element']['#description']);
    }
    $output = theme_form_element($variables);
    if ($description) {
        $output .= '<div class="form-hint">' . $description . "</div>\n";
    }
    return $output;
}

/**
 * Returns HTML for a password form element.
 */
function tabler_password($variables)
{
    $variables['element']['#attributes']['class'][] = 'form-control';
    return theme_password($variables);
}

/**
 * Returns HTML for a search form element.
 */
function tabler_search($variables)
{
    $variables['element']['#attributes']['class'][] = 'form-control';
    return theme_search($variables);
}

/**
 * Returns HTML for a select form element.
 */
function tabler_select($variables)
{
    if (isset($variables['element']['#size'])) {
        unset($variables['element']['#size']);
    }
    $variables['element']['#attributes']['class'][] = 'form-select';
    return theme_select($variables);
}

/**
 * Implements hook_preprocess_table().
 *
 * Adds Tabler table classes to plain tables.
 */
function tabler_preprocess_table(&$variables)
{
    $variables['attributes']['class'][] = 'table';
    $variables['attributes']['class'][] = 'table-hover';
    if (!in_array('table-no-striping', $variables['attributes']['class'])) {
        $variables['attributes']['class'][] = 'table-striped';
    }
}

/**
 * Implements hook_form_alter().
 *
 * Styles cancel links as secondary buttons.
 */
function tabler_form_alter(&$form, &$form_state = array(), $form_id = null)
{
    if ($form_id) {
        if (isset($form['actions']['cancel']) && isset($form['actions']['cancel']['#type']) && $form['actions']['cancel']['#type'] == 'link') {
            $form['actions']['cancel']['#options']['attributes']['class'][] = 'btn';
            $form['actions']['cancel']['#options']['attributes']['class'][] = 'btn-secondary';
        }
        if (isset($form['actions']['cancel_form']) && $form['actions']['cancel_form']['#type'] == 'link') {
            $form['actions']['cancel_form']['#options']['attributes']['class'][] = 'btn';
            $form['actions']['cancel_form']['#options']['attributes']['class'][] = 'btn-secondary';
        }
    }
}

/**
 * Returns HTML for primary and secondary local tasks.
 *
 * Primary tabs as nav-tabs, secondary as nav-pills.
 */
function tabler_menu_local_tasks(&$variables)
{
    $output = '';

    if (!empty($variables['primary'])) {
        foreach ($variables['primary'] as $key => $item) {
            $variables['primary'][$key]['#link']['localized_options']['attributes']['class'][] = 'nav-link';
        }
        $variables['primary']['#prefix'] = '<h2 class="element-invisible">' . t('Primary tabs') . '</h2>';
        $variables['primary']['#prefix'] .= '<ul class="nav nav-tabs tabs-primary">';
        $variables['primary']['#suffix'] = '</ul>';
        $output .= backdrop_render($variables['primary']);
    }
    if (!empty($variables['secondary'])) {
        foreach ($variables['secondary'] as $key => $item) {
            $variables['secondary'][$key]['#link']['localized_options']['attributes']['class'][] = 'nav-link';
        }
        $variables['secondary']['#prefix'] = '<h2 class="element-invisible">' . t('Secondary tabs') . '</h2>';
        $variables['secondary']['#prefix'] .= '<ul class="nav nav-pills secondary">';
        $variables['secondary']['#suffix'] = '</ul>';
        $output .= backdrop_render($variables['secondary']);
    }

    return $output;
}

/**
 * Implements hook_links() for dropbuttons.
 */
function tabler_links__dropbutton($menu)
{
    foreach ($menu['links'] as $name => $settings) {
        $menu['links'][$name]['attributes']['class'][] = 'btn';
        $menu['links'][$name]['attributes']['class'][] = 'btn-secondary';
    }
    return theme_links($menu);
}

/**
 * Returns HTML for the local actions.
 */
function tabler_menu_local_actions(&$variables)
{
    $output = backdrop_render($variables['actions']);
    if ($output) {
        $output = '<ul class="nav nav-pills action-links">' . $output . '</ul>';
    }
    return $output;
}

/**
 * Overrides theme_node_add_list().
 *
 * List of content types as a Tabler list group.
 */
function tabler_node_add_list($variables)
{
    $content = $variables['content'];
    $output = '';
    if ($content) {
        $output = '<ul class="list-group">';
        foreach ($content as $item) {
            $title = '<h4 class="list-group-item-heading">' . $item['title'] . '</h4>';
            if (isset($item['description'])) {
                $title .= '<p class="list-group-item-text text-secondary">' . filter_xss_admin($item['description']) . '</p>';
            }
            $item['localized_options']['attributes']['class'][] = 'list-group-item';
            $item['localized_options']['html'] = true;
            $output .= l($title, $item['href'], $item['localized_options']);
        }
        $output .= '</ul>';
    } else {
        $output = '<p>' . t('You have not created any content types yet. Go to the <a href="@create-content">content type creation page</a> to add a new content type.', array('@create-content' => url('admin/structure/types/add'))) . '</p>';
    }
    return $output;
}

/**
 * Overrides theme_admin_block_content().
 *
 * Unordered list markup in both compact and extended mode.
 */
function tabler_admin_block_content($variables)
{
    return tabler_node_add_list($variables);
}

/**
 * Returns HTML for an administrative block.
 *
 * Tabler card markup.
 */
function tabler_admin_block($variables)
{
    $block = $variables['block'];
    $output = '';

  // Don't display the block if it has no content to display.
    if (empty($block['show'])) {
        return $output;
    }

    $output .= '<div class="card mb-3">';
    if (!empty($block['title'])) {
        $output .= '<div class="card-header"><h3 class="card-title">' . $block['title'] . '</h3></div>';
    }
    if (!empty($block['content'])) {
        $output .= '<div class="card-body">' . $block['content'] . '</div>';
    } else {
        $output .= '<div class="card-body text-secondary">' . $block['description'] . '</div>';
    }
    $output .= '</div>';

    return $output;
}

/**
 * Returns HTML for the output of the dashboard page.
 */
function tabler_system_admin_index($variables)
{
    $menu_items = $variables['menu_items'];

    $container = array('left' => '', 'right' => '');
    $flip = array('left' => 'right', 'right' => 'left');
    $position = 'left';

  // Iterate over all modules.
    foreach ($menu_items as $module => $block) {
        list($description, $items) = $block;

      // Output links.
        if (count($items)) {
            $block = array();
            $block['title'] = $module;
            $block['content'] = theme('admin_block_content', array('content' => $items));
            $block['description'] = t($description);
            $block['show'] = true;

            if ($block_output = theme('admin_block', array('block' => $block))) {
                if (!isset($block['position'])) {
                  // Perform automatic striping.
                    $block['position'] = $position;
                    $position = $flip[$position];
                }
                $container[$block['position']] .= $block_output;
            }
        }
    }

    $output = '<div class="admin row">';
    foreach ($container as $id => $data) {
        $output .= '<div class="col-md-6 col-sm-12">';
        $output .= $data;
        $output .= '</div>';
    }
    $output .= '</div>';

    return $output;
}

/**
 * Returns HTML for an administrative page.
 */
function tabler_admin_page($variables)
{
    $blocks = $variables['blocks'];

    $stripe = 0;
    $container = array();

    foreach ($blocks as $block) {
        if ($block_output = theme('admin_block', array('block' => $block))) {
            if (empty($block['position'])) {
                // Perform automatic striping.
                $block['position'] = ++$stripe % 2 ? 'left' : 'right';
            }
            if (!isset($container[$block['position']])) {
                $container[$block['position']] = '';
            }
            $container[$block['position']] .= $block_output;
        }
    }

    $output = '<div class="admin row">';
    foreach ($container as $id => $data) {
        $output .= '<div class="col-md-6 col-sm-12">';
        $output .= $data;
        $output .= '</div>';
    }
    $output .= '</div>';
    return $output;
}

/**
 * Returns HTML for an individual permission description.
 */
function tabler_user_permission_description($variables)
{
    $description = array();
    $permission_item = $variables['permission_item'];
    if (!empty($permission_item['description'])) {
        $description[] = $permission_item['description'];
    }
    if (!empty($permission_item['warning'])) {
        $description[] = '<em class="text-danger">' . $permission_item['warning'] . '</em>';
    }
    if (!empty($description)) {
        return implode(' ', $description);
    }
}

/**
 * Implements hook_preprocess_node().
 *
 * Optional "XX time ago" instead of the raw date.
 */
function tabler_preprocess_node(&$variables)
{
    if (theme_get_setting('tabler_datetime', 'tabler')) {
        $node = $variables['elements']['#node'];
        $variables['timeago'] = t('@time ago', array('@time' => format_interval(time() - $node->created)));
    }
}

/**
 * Implements hook_preprocess_comment().
 *
 * Optional "XX time ago" instead of the raw date.
 */
function tabler_preprocess_comment(&$variables)
{
    if (theme_get_setting('tabler_datetime', 'tabler')) {
        $comment = $variables['elements']['#comment'];
        $variables['timeago'] = t('@time ago', array('@time' => format_interval(time() - $comment->changed)));
    }
}

/**
 * Overrides theme_progress_bar().
 *
 * Bootstrap 5 progress markup.
 */
function tabler_progress_bar($variables)
{
    $output = '<div id="progress">';
    $output .= '<div class="progress">';
    $output .= '  <div class="progress-bar" role="progressbar" style="width: ' . $variables['percent'] . '%" aria-valuenow="' . $variables['percent'] . '" aria-valuemin="0" aria-valuemax="100"></div>';
    $output .= '</div>';
    $output .= '<div class="d-flex justify-content-between">';
    $output .= '<div class="message">' . $variables['message'] . '</div>';
    $output .= '<div class="percentage">' . $variables['percent'] . '%</div>';
    $output .= '</div>';
    $output .= '</div>';

    return $output;
}
