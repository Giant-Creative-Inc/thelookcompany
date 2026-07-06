/**
 * Home Rebrand - Market section scroll-to-section handler.
 *
 * The markets section uses Salient's "Sticky Scroll Pinned Sections" widget
 * (type--scroll-pinned-sections) with effect-overlapping + stacking-effect.
 * Three external anchor buttons (#retail-solutions, #sports-and-events,
 * #interior-branding) should scroll to and reveal the matching sticky section.
 *
 * Problem: All sections use position:sticky at the same top offset. Later DOM
 * sections paint on top of earlier ones. The overlapping effect only pushes
 * sections up by the overlap amount (20px), so section 2 (last in DOM) always
 * covers sections 0 and 1 visually. Native anchor scrolling lands at a position
 * where the wrong section is visible.
 *
 * Solution: Intercept clicks, force z-index stacking so the target section sits
 * on top (persists — Salient's RAF only modifies transforms, not z-index),
 * scroll to the calculated offset matching Salient's internal formula.
 */
(function ($) {
  'use strict';

  // Section IDs in DOM order (first section = index 0, etc.)
  var SECTION_IDS = [
    'retail-solutions',
    'sports-and-events',
    'interior-branding',
  ];

  /**
   * Parse a CSS length value (px, vh, rem, etc.) to a pixel number.
   */
  function parseCSSValue(value, contextEl) {
    if (!value) return 0;
    value = String(value).trim();
    if (!value) return 0;

    if (value.indexOf('px') > -1)
      return parseFloat(value.replace('px', '')) || 0;
    if (value.indexOf('vh') > -1)
      return ((parseFloat(value.replace('vh', '')) || 0) / 100) * window.innerHeight;
    if (value.indexOf('rem') > -1)
      return (parseFloat(value.replace('rem', '')) || 0) *
        (parseFloat(getComputedStyle(document.documentElement).fontSize) || 16);
    if (value.indexOf('em') > -1) {
      var fs = contextEl
        ? parseFloat(getComputedStyle(contextEl).fontSize) || 16
        : 16;
      return (parseFloat(value.replace('em', '')) || 0) * fs;
    }
    if (value.indexOf('%') > -1) {
      var parentW = contextEl && contextEl.parentElement
        ? contextEl.parentElement.offsetWidth
        : window.innerWidth;
      return ((parseFloat(value.replace('%', '')) || 0) / 100) * parentW;
    }
    return parseFloat(value) || 0;
  }

  /**
   * Calculate the gap (top offset) for sticky sections.
   * Replicates Salient's calculateGap() for section-height-90vh subtract-nav-height.
   */
  function calculateGap(stickyEl) {
    var classList = stickyEl.classList;
    var gapPx = 0;

    for (var i = 0; i < classList.length; i++) {
      var match = classList[i].match(/^section-height-(\d+)vh$/);
      if (match) {
        var pct = (100 - parseInt(match[1], 10)) / 2;
        gapPx = window.innerHeight * (pct / 100);
        break;
      }
    }

    if (classList.contains('subtract-nav-height')) {
      var navH = getComputedStyle(document.documentElement).getPropertyValue(
        '--header-nav-height'
      );
      if (navH) {
        gapPx += parseCSSValue(navH, stickyEl);
      }
    }

    return gapPx;
  }

  /**
   * Get the section elements and map IDs to indices.
   * Returns { els: NodeList, indexMap: { id: index } }
   */
  function getSectionData(stickyEl) {
    var sectionEls = stickyEl.querySelectorAll(
      '.nectar-sticky-media-section__content-section'
    );
    var indexMap = {};

    for (var i = 0; i < SECTION_IDS.length; i++) {
      for (var j = 0; j < sectionEls.length; j++) {
        if (sectionEls[j].querySelector('#' + SECTION_IDS[i])) {
          indexMap[SECTION_IDS[i]] = j;
          break;
        }
      }
    }

    return { els: sectionEls, indexMap: indexMap };
  }

  /**
   * Force z-index stacking so targetIndex is on top.
   * Lower indices get progressively higher z-index so the first section
   * (top of scroll range) naturally sits on top. The target section gets
   * the absolute highest z-index regardless of its index.
   *
   * This persists — Salient's RAF loop only modifies transforms, never z-index.
   * The next button click will re-assign z-indices for the new target.
   */
  function forceZIndex(sectionEls, targetIndex) {
    var count = sectionEls.length;
    var base = count;

    for (var i = 0; i < count; i++) {
      if (i === targetIndex) {
        // Target section gets highest z-index.
        sectionEls[i].style.zIndex = base + count;
      } else {
        // Non-target sections: earlier sections get higher z-index so they
        // sit above later ones. This way the natural stacking has section 0
        // on top of section 1 on top of section 2.
        sectionEls[i].style.zIndex = base - i;
      }
    }
  }

  /**
   * Get the absolute document offset of an element by walking offsetParent.
   * Unlike getBoundingClientRect() + scrollY, offsetTop is unaffected by
   * transforms, sticky positioning, or scroll state — it's pure layout.
   */
  function getAbsoluteOffsetTop(el) {
    var top = 0;
    do {
      top += el.offsetTop;
      el = el.offsetParent;
    } while (el);
    return top;
  }

  /**
   * Calculate the scroll position that activates section `index`.
   * Uses offsetTop (layout position, stable) instead of getBoundingClientRect
   * (visual position, changes when sticky sections are active).
   *
   * Matches Salient's calculateOffsets() for type--scroll-pinned-sections:
   *   offset = elementTop - gap + sectionHeight * index
   */
  function getSectionScrollOffset(stickyEl, sectionEls, index) {
    var gap = calculateGap(stickyEl);
    var elementTop = getAbsoluteOffsetTop(stickyEl);

    // Salient formula: offset = elementTop - gap + sectionEl.offsetHeight * i
    // sectionEl.offsetHeight includes border-bottom (box-sizing: content-box).
    // For equal-height sections, cumulative sum equals height * index.
    var cumulativeHeight = 0;
    for (var i = 0; i < index; i++) {
      cumulativeHeight += sectionEls[i].offsetHeight;
    }

    return elementTop - gap + cumulativeHeight;
  }

  /**
   * Click handler for market anchor buttons.
   */
  function onMarketButtonClick(e) {
    var href = this.getAttribute('href');
    if (!href || href === '#') return;

    var targetId = href.replace('#', '');
    if (SECTION_IDS.indexOf(targetId) === -1) return;

    var stickyEl = document.querySelector(
      '.nectar-sticky-media-sections.type--scroll-pinned-sections'
    );
    if (!stickyEl) return;

    var data = getSectionData(stickyEl);
    var sectionIndex = data.indexMap[targetId];
    if (typeof sectionIndex === 'undefined') return;

    e.preventDefault();

    // 1. Force z-index so target section is visually on top.
    //    Persists until next button click — Salient's RAF only modifies
    //    transforms, never z-index, so this won't be overridden.
    forceZIndex(data.els, sectionIndex);

    // 2. Calculate and scroll to the activation offset.
    //    Add half section height to land in the middle of the active range,
    //    ensuring the Salient RAF loop firmly detects this section.
    var baseOffset = getSectionScrollOffset(stickyEl, data.els, sectionIndex);
    var sectionHeight = data.els[sectionIndex].offsetHeight;
    var targetScroll = Math.max(0, baseOffset + sectionHeight / 2);

    window.scrollTo({
      top: targetScroll,
      behavior: 'smooth',
    });
  }

  /**
   * Check if an element matches one of our market button selectors.
   */
  function isMarketButton(el) {
    var href = el.getAttribute('href');
    if (!href) return false;
    var id = href.replace('#', '');
    return SECTION_IDS.indexOf(id) !== -1;
  }

  /**
   * Initialize: bind click handler with {passive: false} using native listener
   * with manual delegation.
   */
  function init() {
    document.addEventListener('click', function (e) {
      var el = e.target;
      while (el && el !== document) {
        if (el.tagName === 'A' && isMarketButton(el)) {
          onMarketButtonClick.call(el, e);
          return;
        }
        el = el.parentElement;
      }
    }, {passive: false});
  }

  $(init);
})(jQuery);
