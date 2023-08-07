/**
 * @file
 * Provide js functionality for bundle type contact us.
 *
 * This message will show in the console.
 */
(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.siteWide = {
    attach: function (context, settings) {
      console.log('it is a contact us page');
    }
  }
})(jQuery, Drupal, drupalSettings);
