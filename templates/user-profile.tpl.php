<?php
/**
 * @file
 * User profile. Uses a Tabler card.
 */
?>
<article class="profile card"<?php print backdrop_attributes($attributes); ?>>
  <div class="card-body">
    <?php print render($user_profile); ?>
  </div>
</article>
