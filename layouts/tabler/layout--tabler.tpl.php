<?php
/**
 * @file
 * Tabler layout. Vertical navbar, page wrapper, content, footer.
 * Regions come from the Layout UI. Place the main menu in Navigation.
 */
$navbar_theme = theme_get_setting('tabler_navbar_theme', 'tabler');
$navbar_theme = $navbar_theme ? $navbar_theme : 'dark';
?>
<div class="page">
  <aside class="navbar navbar-vertical navbar-expand-lg navbar-<?php print $navbar_theme; ?>" data-bs-theme="<?php print $navbar_theme; ?>">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#tabler-nav" aria-controls="tabler-nav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <h1 class="navbar-brand navbar-brand-autodark">
        <?php if (!empty($content['header'])): ?>
          <?php print $content['header']; ?>
        <?php endif; ?>
      </h1>
      <div class="collapse navbar-collapse" id="tabler-nav">
        <ul class="navbar-nav pt-lg-3">
          <?php if (!empty($content['navigation'])): ?>
            <?php print $content['navigation']; ?>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </aside>

  <div class="page-wrapper">
    <div class="page-header d-print-none">
      <div class="container-xl">
        <?php if (!empty($content['sidebar'])): ?>
          <div class="row align-items-center">
            <div class="col">
              <?php print $content['sidebar']; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="page-body">
      <div class="container-xl">
        <?php if (!empty($content['content'])): ?>
          <?php print $content['content']; ?>
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
