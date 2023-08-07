(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.siteWide = {
    attach: function (context, settings) {
      console.log('it is a contact us page');
    }
  }
})(jQuery, Drupal, drupalSettings);
