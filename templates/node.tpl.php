<?php
/**
 * @file
 * Node. Uses a Tabler card.
 */
?>
<article id="node-<?php print $node->nid; ?>" class="<?php print implode(' ', $classes); ?> card"<?php print backdrop_attributes($attributes); ?>>
  <?php print render($title_prefix); ?>
  <?php if (!$page && !empty($title)): ?>
    <div class="card-header">
      <?php if (!empty($node_url)): ?>
        <h2 class="card-title"><a href="<?php print $node_url; ?>" rel="bookmark"><?php print $title; ?></a></h2>
      <?php else: ?>
        <h2 class="card-title"><?php print $title; ?></h2>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <?php print render($title_suffix); ?>

  <div class="card-body">
    <?php if ($display_submitted): ?>
      <div class="text-secondary mb-2">
        <?php print $user_picture; ?>
        <?php print $submitted; ?>
        <?php if (!empty($timeago)): ?>
          <small class="text-muted">&middot; <?php print $timeago; ?></small>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <div class="content"<?php print backdrop_attributes($content_attributes); ?>>
      <?php hide($content['links']); print render($content); ?>
    </div>

    <?php print render($content['links']); ?>
  </div>

  <?php if ($comments): ?>
    <div class="card-footer">
      <?php if ($comments['comments']): ?>
        <h3><?php print t('Comments'); ?></h3>
        <?php print render($comments['comments']); ?>
      <?php endif; ?>
      <?php if ($comments['comment_form']): ?>
        <h3><?php print t('Add comment'); ?></h3>
        <?php print render($comments['comment_form']); ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</article>
