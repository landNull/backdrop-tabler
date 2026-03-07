<?php
/**
 * @file
 * Block. Uses a Tabler card when it has a title.
 */
?>
<div class="<?php print implode(' ', $classes); ?>"<?php print backdrop_attributes($attributes); ?>>
  <?php print render($title_prefix); ?>
  <?php if ($title): ?>
    <h2 class="block-title h3"><?php print $title; ?></h2>
  <?php endif; ?>
  <?php print render($title_suffix); ?>
  <div class="block-content">
    <?php print render($content); ?>
  </div>
</div>
