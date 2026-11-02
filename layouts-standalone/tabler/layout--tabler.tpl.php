<?php
/**
 * @file
 * Tabler layout. Sidebar, content, footer. Regions come from the Layout UI.
 */
$navbar_theme = theme_get_setting('tabler_navbar_theme', 'tabler');
$navbar_theme = $navbar_theme ? $navbar_theme : 'dark';
?>
<div class="page">
  <aside class="navbar navbar-vertical navbar-expand-lg navbar-<?php print $navbar_theme; ?>">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#tabler-nav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <h1 class="navbar-brand navbar-brand-autodark">
        <?php if (!empty($content['header'])): ?>
          <?php print $content['header']; ?>
        <?php endif; ?>
      </h1>
      <?php if (!empty($user_menu)): ?>
        <?php print $user_menu; ?>
      <?php endif; ?>
      <div class="collapse navbar-collapse" id="tabler-nav">
        <?php if (!empty($content['navigation'])): ?>
          <?php print $content['navigation']; ?>
        <?php endif; ?>
      </div>
    </div>
  </aside>

  <div class="page-wrapper">
    <div class="page-body">
      <div class="<?php print $container_class; ?>">
        <?php if (!empty($content['content'])): ?>
          <?php print $content['content']; ?>
        <?php endif; ?>
        <?php if (!empty($content['sidebar'])): ?>
          <aside class="mt-4"><?php print $content['sidebar']; ?></aside>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!empty($content['footer'])): ?>
      <footer class="footer footer-transparent d-print-none">
        <div class="<?php print $container_class; ?>">
          <?php print $content['footer']; ?>
        </div>
      </footer>
    <?php endif; ?>
  </div>
</div>
