/**
 * @file
 * Provide phone number formatting.
 *
 * On key up change the phone number value.
 */
(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.ds = {
    attach: function (context, settings) {
      $('#edit-message').keyup(function () {
        var input = $(this).val().replace(/\D/g, '');
        var formatted = "";
        if (input.length >= 10) {
          formatted = '(' + input.substr(0, 3) + ') ' + input.substr(3, 3) + '-' + input.substr(6, 4);
        } else {
          formatted = input;
        }
        $(this).val(formatted);
      });
    }
  }
})(jQuery, Drupal, drupalSettings);
