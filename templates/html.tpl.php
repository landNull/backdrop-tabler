<?php
/**
 * @file
 * HTML shell. Writes the Tabler data-bs attributes on the html tag.
 */
$attr = '';
if (!empty($tabler_attrs) && is_array($tabler_attrs)) {
  foreach ($tabler_attrs as $k => $v) {
    $attr .= ' ' . check_plain($k) . '="' . check_plain($v) . '"';
  }
}
?><!DOCTYPE html>
<html<?php print $attr; ?> lang="<?php print $language->lang; ?>">
<head>
  <?php print $head; ?>
  <title><?php print $head_title; ?></title>
  <?php print $styles; ?>
  <?php print $scripts; ?>
</head>
<body class="<?php print $classes; ?>" <?php print $attributes; ?>>
  <?php print $page; ?>
  <?php print $page_bottom; ?>
</body>
</html>
