(function ($, Drupal, drupalSettings) {
  // Not using behaviors for most of this.
  Drupal.behaviors.AsuDegreeRfiBehavior = {
    attach: function (context, settings) {

      // Behavior code here.
      console.log('asu_degree_rfi behavior');

    }
  };


  // Launcher code here.
  console.log('asu_degree_rfi launcher');


// TODO Without jQuery, we get Uncaught ReferenceError: jQuery is not defined.
// Is it required by Drupal or drupalSettings? Would like it working w/o jQuery.
})(jQuery, Drupal, drupalSettings);
