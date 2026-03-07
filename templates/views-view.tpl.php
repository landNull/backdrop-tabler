<?php
/**
 * @file
 * Views wrapper. Adds Tabler card and spacing.
 */
?>
<div class="<?php print implode(' ', $classes); ?> card">
  <?php print render($title_prefix); ?>
  <?php if ($title): ?>
    <div class="card-header">
      <h3 class="card-title"><?php print $title; ?></h3>
    </div>
  <?php endif; ?>
  <?php print render($title_suffix); ?>

  <div class="card-body">
    <?php if ($header): ?>
      <div class="view-header mb-3"><?php print $header; ?></div>
    <?php endif; ?>

    <?php if ($exposed): ?>
      <div class="view-filters mb-3"><?php print $exposed; ?></div>
    <?php endif; ?>

    <?php if ($attachment_before): ?>
      <div class="attachment attachment-before"><?php print $attachment_before; ?></div>
    <?php endif; ?>

    <?php if ($rows): ?>
      <div class="view-content">
        <?php print (is_array($rows)) ? backdrop_render($rows) : $rows; ?>
      </div>
    <?php elseif ($empty): ?>
      <div class="view-empty empty">
        <p class="empty-title"><?php print $empty; ?></p>
      </div>
    <?php endif; ?>

    <?php if ($pager): ?>
      <div class="mt-3"><?php print $pager; ?></div>
    <?php endif; ?>

    <?php if ($attachment_after): ?>
      <div class="attachment attachment-after"><?php print $attachment_after; ?></div>
    <?php endif; ?>

    <?php if ($more): ?>
      <div class="mt-3"><?php print $more; ?></div>
    <?php endif; ?>

    <?php if ($footer): ?>
      <div class="view-footer mt-3"><?php print $footer; ?></div>
    <?php endif; ?>

    <?php if ($feed_icon): ?>
      <div class="feed-icon"><?php print $feed_icon; ?></div>
    <?php endif; ?>
  </div>
</div>
