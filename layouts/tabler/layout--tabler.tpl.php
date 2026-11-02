<?php
/**
 * @file
 * Tabler layout. Left sidebar navigation, content, footer.
 * Regions come from the Layout UI.
 */
$navbar_theme = theme_get_setting('tabler_navbar_theme', 'tabler');
$navbar_theme = $navbar_theme ? $navbar_theme : 'dark';
?>
<div class="page tabler-page">
  <?php if (!empty($content['navigation']) || !empty($content['header'])): ?>
    <aside class="navbar navbar-vertical navbar-expand-lg navbar-<?php print $navbar_theme; ?> tabler-sidebar" data-bs-theme="<?php print $navbar_theme; ?>" role="navigation">
      <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#tabler-nav" aria-controls="tabler-nav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <?php if (!empty($content['header'])): ?>
          <h1 class="navbar-brand navbar-brand-autodark">
            <?php print $content['header']; ?>
          </h1>
        <?php endif; ?>
        <div class="collapse navbar-collapse" id="tabler-nav">
          <?php if (!empty($content['navigation'])): ?>
            <?php print $content['navigation']; ?>
          <?php endif; ?>
        </div>
      </div>
    </aside>
  <?php endif; ?>

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
        <div class="row">
          <div class="col">
            <?php if (!empty($content['content'])): ?>
              <?php print $content['content']; ?>
            <?php endif; ?>
          </div>
          <?php if (!empty($content['sidebar'])): ?>
            <aside class="col-auto tabler-content-sidebar">
              <?php print $content['sidebar']; ?>
            </aside>
          <?php endif; ?>
        </div>
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