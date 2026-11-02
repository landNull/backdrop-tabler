(function ($, Backdrop) {
  /*global jQuery:false */
  /*global Backdrop:false */
  "use strict";

  /**
   * Provide vertical tab summaries for the Tabler settings.
   */
  Backdrop.behaviors.tablerSettingSummaries = {
    attach: function (context) {
      var $context = $(context);

      // Framework

      $context.find('#edit-tabler-framework').backdropSetSummary(function () {
        var summary = [];
        var source = $context.find('select[name="tabler_cdn"] :selected').text();
        summary.push('Source: ' + source);
        if (source !== 'Disabled') {
          var version = $context.find('select[name="tabler_version"] :selected').text() || $context.find('input[name="tabler_version"]').val();
          summary.push('Version: ' + version);
        }
        return summary.join('<br>');
      });

      // Appearance

      $context.find('#edit-tabler-appearance').backdropSetSummary(function () {
        var summary = [];
        summary.push('Mode: ' + $context.find('select[name="tabler_mode"] :selected').text());
        summary.push('Primary: ' + $context.find('select[name="tabler_primary"] :selected').text());
        summary.push('Base: ' + $context.find('select[name="tabler_base"] :selected').text());
        summary.push('Radius: ' + $context.find('select[name="tabler_radius"] :selected').text());
        return summary.join('<br>');
      });

      // Layout and navigation

      $context.find('#edit-tabler-layout-group').backdropSetSummary(function () {
        var summary = [];
        summary.push('Width: ' + $context.find('select[name="tabler_layout"] :selected').text());
        summary.push('Nav: ' + $context.find('select[name="tabler_navbar"] :selected').text());
        summary.push('Nav color: ' + $context.find('select[name="tabler_navbar_theme"] :selected').text());
        if ($context.find(':input[name="tabler_navbar_user_menu"]').is(':checked')) {
          summary.push(Backdrop.t('Account menu'));
        }
        return summary.join('<br>');
      });

      // Breadcrumbs and dates

      $context.find('#edit-tabler-chrome').backdropSetSummary(function () {
        var summary = [];
        if ($context.find(':input[name="tabler_breadcrumb_home"]').is(':checked')) {
          summary.push(Backdrop.t('Home link'));
        }
        if ($context.find(':input[name="tabler_breadcrumb_title"]').is(':checked')) {
          summary.push(Backdrop.t('Current title'));
        }
        if ($context.find(':input[name="tabler_datetime"]').is(':checked')) {
          summary.push(Backdrop.t('"XX time ago"'));
        }
        return summary.join('<br>');
      });
    }
  };

})(jQuery, Backdrop);
