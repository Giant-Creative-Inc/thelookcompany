/**
 * Home Rebrand - Sticky row market button background toggle.
 *
 * The markets section has a Salient sticky row
 * (.nectar-sticky-row-wrap--top_after_nav) containing 3 see-through
 * buttons (.home-subpage-btn). Each button has a border_color_XXXXXX
 * class. When the row becomes sticky, each button's background
 * transitions from transparent to its border color.
 */
(function ($) {
  'use strict';

  var STICKY_ROW_SELECTOR = '.nectar-sticky-row-wrap';
  var BUTTON_SELECTOR = '.home-subpage-btn';

  /**
   * Extract hex color from a class name like "border_color_E51937".
   * Returns "#E51937" or null.
   */
  function getBorderColor(el) {
    var classes = el.classList;
    for (var i = 0; i < classes.length; i++) {
      var match = classes[i].match(/^border_color_([A-Fa-f0-9]{6})$/);
      if (match) {
        return '#' + match[1];
      }
    }
    return null;
  }

  /**
   * Apply or clear background color on all buttons inside the sticky row.
   */
  function updateButtons(stickyRow, isStuck) {
    var buttons = stickyRow.querySelectorAll(BUTTON_SELECTOR);
    for (var i = 0; i < buttons.length; i++) {
      var btn = buttons[i];
      var link = btn.querySelector('.link_text');
      if (!link) continue;
      if (isStuck) {
        var color = getBorderColor(btn);
        if (color) {
          link.style.setProperty('background-color', color, 'important');
        }
      } else {
        link.style.removeProperty('background-color');
      }
    }
  }

  function init() {
    var stickyRow = document.querySelector(STICKY_ROW_SELECTOR);
    if (!stickyRow) return;

    // Insert a sentinel element right before the sticky row.
    // When the sentinel scrolls out of the viewport, the row is stuck.
    var sentinel = document.createElement('div');
    sentinel.className = 'tlc-sticky-sentinel';
    sentinel.setAttribute('aria-hidden', 'true');

    stickyRow.parentNode.insertBefore(sentinel, stickyRow);

    var observer = new IntersectionObserver(
      function (entries) {
        // sentinel not intersecting → row is stuck
        var isStuck = !entries[0].isIntersecting;
        updateButtons(stickyRow, isStuck);
      },
      { threshold: 0 }
    );

    observer.observe(sentinel);
  }

  $(init);
})(jQuery);
