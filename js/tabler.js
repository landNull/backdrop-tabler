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
