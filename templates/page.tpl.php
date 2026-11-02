<?php
/**
 * @file
 * HTML shell. Content comes from Layouts. Tabler attributes go on the html tag.
 */
?><!DOCTYPE html>
<html<?php print backdrop_attributes($html_attributes); ?>>
  <head>
    <?php print backdrop_get_html_head(); ?>
    <title><?php print $head_title; ?></title>
    <?php print backdrop_get_css(); ?>
    <?php print backdrop_get_js(); ?>
  </head>
  <body class="<?php print implode(' ', $classes); ?>"<?php print backdrop_attributes($body_attributes); ?>>
    <?php print $page; ?>
    <?php print $page_bottom; ?>
    <?php print backdrop_get_js('footer'); ?>
  </body>
</html>
