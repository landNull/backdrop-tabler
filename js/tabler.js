/**
 * @file
 * Small helpers. Tabler JS loads from the CDN.
 */
(function () {
  'use strict';
  // Collapse the sidebar on small screens after a nav click.
  document.addEventListener('click', function (e) {
    var link = e.target.closest('#tabler-nav a');
    if (!link) return;
    var nav = document.getElementById('tabler-nav');
    if (nav && nav.classList.contains('show')) {
      nav.classList.remove('show');
    }
  });
})();

/**
 * Resolve data-bs-theme="auto" to light/dark from the OS preference.
 * Runs early; Backdrop loads theme scripts in the header, so the first
 * paint uses the resolved mode.
 */
(function () {
  'use strict';
  var html = document.documentElement;
  var mql = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;
  // Follow OS changes only while the theme setting is auto.
  var auto = html.getAttribute('data-bs-theme') === 'auto';
  function resolve() {
    if (html.getAttribute('data-bs-theme') !== 'auto') return;
    var dark = mql ? mql.matches : false;
    html.setAttribute('data-bs-theme', dark ? 'dark' : 'light');
  }
  resolve();
  if (auto && mql && mql.addEventListener) {
    mql.addEventListener('change', function (e) {
      html.setAttribute('data-bs-theme', e.matches ? 'dark' : 'light');
    });
  }
})();
