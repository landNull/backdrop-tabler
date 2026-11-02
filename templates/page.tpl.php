<?php
/**
 * @file
 * Tabler page shell. Regions map to Backdrop. Markup is Tabler.
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
      <h1 class="navbar-brand">
        <a href="<?php print $front_page; ?>"><?php print $site_name; ?></a>
      </h1>
      <div class="collapse navbar-collapse" id="tabler-nav">
        <?php if ($navigation): ?>
          <?php print render($navigation); ?>
        <?php endif; ?>
      </div>
    </div>
  </aside>

  <div class="page-wrapper">
    <?php if ($header): ?>
      <header class="navbar navbar-expand-md d-print-none">
        <div class="container-xl">
          <?php print render($header); ?>
        </div>
      </header>
    <?php endif; ?>

    <div class="page-body">
      <div class="container-xl">
        <?php if ($highlighted): ?>
          <?php print render($highlighted); ?>
        <?php endif; ?>
        <?php if ($help): ?>
          <?php print render($help); ?>
        <?php endif; ?>
        <?php if ($messages): ?>
          <div class="mb-3"><?php print $messages; ?></div>
        <?php endif; ?>
        <?php if ($tabs): ?>
          <div class="mb-3"><?php print render($tabs); ?></div>
        <?php endif; ?>
        <?php print render($page['content']); ?>
        <?php if ($sidebar): ?>
          <aside class="mt-4"><?php print render($sidebar); ?></aside>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($footer): ?>
      <footer class="footer footer-transparent d-print-none">
        <div class="container-xl">
          <?php print render($footer); ?>
        </div>
      </footer>
    <?php endif; ?>
  </div>
</div>
