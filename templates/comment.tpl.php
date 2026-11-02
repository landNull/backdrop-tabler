<?php
/**
 * @file
 * Comment. Uses a Tabler card.
 */
?>
<article class="<?php print implode(' ', $classes); ?> card mb-3"<?php print backdrop_attributes($attributes); ?>>
  <div class="card-body">
    <?php if ($new): ?>
      <span class="badge bg-primary"><?php print $new; ?></span>
    <?php endif; ?>
    <?php if ($title_display): ?>
      <?php print render($title_prefix); ?>
      <h3 class="<?php print implode(' ', $title_classes); ?>"><?php print $title; ?></h3>
      <?php print render($title_suffix); ?>
    <?php endif; ?>

    <div class="text-secondary mb-2">
      <?php print $user_picture; ?>
      <?php print $submitted; ?>
      <?php if (!empty($timeago)): ?>
        <small class="text-muted">&middot; <?php print $timeago; ?></small>
      <?php endif; ?>
      <?php print $permalink; ?>
    </div>

    <div class="content"<?php print backdrop_attributes($content_attributes); ?>>
      <?php hide($content['links']); print render($content); ?>
      <?php if ($signature): ?>
        <div class="user-signature text-secondary mt-2"><?php print $signature; ?></div>
      <?php endif; ?>
    </div>

    <?php print render($content['links']); ?>
  </div>
</article>
