<?php
/**
 * @file
 * Tabler layout. Top bar with site name and menu, content, footer.
 * Regions come from the Layout UI.
 */
$navbar_theme = theme_get_setting('tabler_navbar_theme', 'tabler');
$navbar_theme = $navbar_theme ? $navbar_theme : 'dark';
?>
<div class="page">
  <header class="navbar navbar-expand-md navbar-<?php print $navbar_theme; ?> d-print-none" data-bs-theme="<?php print $navbar_theme; ?>">
    <div class="container-xl">
      <h1 class="navbar-brand navbar-brand-autodark pe-0 pe-md-3">
        <?php if (!empty($content['header'])): ?>
          <?php print $content['header']; ?>
        <?php endif; ?>
      </h1>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#tabler-nav" aria-controls="tabler-nav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="tabler-nav">
        <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
          <ul class="navbar-nav">
            <?php if (!empty($content['navigation'])): ?>
              <?php print $content['navigation']; ?>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <div class="page-wrapper">
    <?php if (!empty($content['top'])): ?>
      <div class="page-top">
        <div class="container-xl">
          <?php print $content['top']; ?>
        </div>
      </div>
    <?php endif; ?>
    <div class="page-body">
      <div class="container-xl">
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
        <div class="container-xl">
          <?php print $content['footer']; ?>
        </div>
      </footer>
    <?php endif; ?>
  </div>
</div>
