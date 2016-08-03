/**
 * @file
 * A JavaScript file for the theme.
 *
 * In order for this JavaScript to be loaded on pages, see the instructions in
 * the README.txt next to this file.
 */

// JavaScript should be made compatible with libraries other than jQuery by
// wrapping it with an "anonymous closure". See:
// - https://drupal.org/node/1446420
// - http://www.adequatelygood.com/2010/3/JavaScript-Module-Pattern-In-Depth
(function (Drupal, $) {

  'use strict';

  // To understand behaviors, see https://drupal.org/node/756722#behaviors
  Drupal.behaviors.my_custom_behavior = {
    attach: function (context, settings) {

      // Place your code here.
      $(window).scroll(function () {
        var offset = 60;
        var scrollTop = $(window).scrollTop();
        var check = $('#block-nvli-branding').hasClass('sticky-branding');

        if ((scrollTop > offset) && check === false) {
          $('.header').addClass('sticky-header');
          $('#block-nvli-branding').addClass('sticky-branding');
          $('.block-nvli-custom-search').addClass('sticky-search-header');
          return false;
        }
        if ((scrollTop < offset) && check === true) {
          $('.header').removeClass('sticky-header');
          $('#block-nvli-branding').removeClass('sticky-branding');
          $('.block-nvli-custom-search').removeClass('sticky-search-header');
          return false;
        }
      });
    }
  };

  // We pass the parameters of this anonymous function are the global variables
  // that this script depend on. For example, if the above script requires
  // jQuery, you should change (Drupal) to (Drupal, jQuery) in the line below
  // and, in this file's first line of JS, change function (Drupal) to
  // (Drupal, $)
})(Drupal, jQuery);
