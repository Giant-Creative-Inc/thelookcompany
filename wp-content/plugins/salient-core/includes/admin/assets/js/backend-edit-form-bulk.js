jQuery(document).ready(function($){

  "use strict";

  // The chosen one
  var nectarChosenSelectors = [
    '.vc_ui-panel:not([data-vc-shortcode="vc_text_separator"]):not([data-vc-shortcode="vc_pie"]) .wpb_edit_form_elements select[name="color"]',
    '.wpb_edit_form_elements select[name="nectar_marker_color"]',
    '.wpb_edit_form_elements select[name="star_rating_color"]',
    '.wpb_edit_form_elements select[name="cta_button_style"]',
    '.wpb_edit_form_elements select[name="divider_color"]',
    '.vc_ui-panel[data-vc-shortcode="nectar_horizontal_list_item"] .wpb_edit_form_elements select[name="color"]',
    '.vc_ui-panel[data-vc-shortcode="nectar_video_lightbox"] .wpb_edit_form_elements select[name*="color"]',
    '.wpb_edit_form_elements select[name="hover_color"]',
    '.wpb_edit_form_elements select[name="icon_color"]',
    '.wpb_edit_form_elements select[name="color_1"]',
    '.wpb_edit_form_elements select[name="button_color_2"]',
    '.wpb_edit_form_elements select[name="button_color"]',
    '.wpb_edit_form_elements select.nectar-theme-color-selector'
  ].join(', ');

  // Function to update chosen container class based on selected color
  function updateChosenContainerClass(selectElement) {
    var $select = $(selectElement);
    var $container = $select.next('.chosen-container');
    var selectedValue = $select.val();

    if ($container.length && selectedValue) {
      // Remove all existing color classes
      $container.removeClass(function(index, className) {
        return (className.match(/(^|\s)color-\S+/g) || []).join(' ');
      });

      // Add new color class
      $container.addClass('color-' + selectedValue);
    }
  }

  // Initialize chosen and add color classes
  $(nectarChosenSelectors).chosen().each(function() {
    updateChosenContainerClass(this);
  });

  // Update color class when selection changes
  $(nectarChosenSelectors).on('change', function() {
    updateChosenContainerClass(this);
  });

  $(nectarChosenSelectors).on('chosen:showing_dropdown', function(evt, params) {

    if(params && params.chosen && params.chosen.container) {
      params.chosen.container.find('ul.chosen-results').first().unbind('mousewheel.chosen DOMMouseScroll.chosen');
    }

  });
});