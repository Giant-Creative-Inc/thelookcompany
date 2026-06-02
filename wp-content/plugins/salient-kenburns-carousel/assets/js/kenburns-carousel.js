/**
 * Ken Burns Carousel (AODA/WCAG-friendly)
 *
 * Goals:
 * - Fade between slides
 * - Ken Burns zoom on background image per slide
 * - Keyboard accessible controls + arrow navigation
 * - Pause/Play for motion control
 * - Live region announcements for screen reader users
 * - Load only when shortcode is rendered (handled in PHP enqueue)
 *
 * Notes:
 * - No dependencies (vanilla JS)
 * - Uses data- attributes supplied by PHP
 */
(function () {
  'use strict';

  /**
   * Convert string to integer with fallback.
   *
   * @param {string} value
   * @param {number} fallback
   * @returns {number}
   */
  function toInt(value, fallback) {
    var n = parseInt(value, 10);
    return Number.isFinite(n) ? n : fallback;
  }

  /**
   * Check prefers-reduced-motion.
   *
   * @returns {boolean}
   */
  function prefersReducedMotion() {
    return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }

  /**
   * Carousel constructor.
   *
   * @param {HTMLElement} root
   */
  function SKBCCarousel(root) {
    this.root = root;

    this.slidesWrap = root.querySelector('[data-skbc-slides]');
    this.slides = Array.prototype.slice.call(root.querySelectorAll('[data-skbc-slide]'));

    this.dots = Array.prototype.slice.call(root.querySelectorAll('[data-skbc-dot]'));
    this.liveRegion = root.querySelector('[data-skbc-live]');

    this.slideDuration = toInt(root.getAttribute('data-slide-duration'), 6000);
    this.fadeDuration = toInt(root.getAttribute('data-fade-duration'), 800);
    this.kenBurnsDuration = toInt(root.getAttribute('data-kenburns-duration'), 8000);
    this.autoplay = root.getAttribute('data-autoplay') === 'true';

    this.activeIndex = 0;
    this.timer = null;

    // Apply CSS variables for timing (keeps CSS simple and user-configurable).
    root.style.setProperty('--skbc-fade-duration', this.fadeDuration + 'ms');
    root.style.setProperty('--skbc-kenburns-duration', this.kenBurnsDuration + 'ms');

    // If reduced motion is requested, disable autoplay and animation.
    this.reducedMotion = prefersReducedMotion();
    if (this.reducedMotion) {
      this.autoplay = false;
    }

    this.bindEvents();
    this.syncUI();

    // Start animation on first slide (unless reduced motion).
    this.startKenBurns(this.activeIndex);

    if (this.autoplay) {
      this.startAutoplay();
    } else {
      this.setPausedState(true);
    }
  }

  /**
   * Bind DOM events.
   */
  SKBCCarousel.prototype.bindEvents = function () {
    var self = this;

    if (this.dots && this.dots.length) {
      this.dots.forEach(function (dot) {
        dot.addEventListener('click', function () {
          var index = parseInt(dot.getAttribute('data-skbc-index'), 10);
          if (Number.isFinite(index)) {
            self.goTo(index);
          }
        });
      });
    }

    // Keyboard support:
    // - Left/Right to change slides when focus is within the carousel.
    // - Space toggles pause when focused on toggle button or inside region.
    this.root.addEventListener('keydown', function (e) {
      var key = e.key;

      if (key === 'ArrowLeft') {
        e.preventDefault();
        self.userInteracted();
        self.prev();
      }

      if (key === 'ArrowRight') {
        e.preventDefault();
        self.userInteracted();
        self.next();
      }

    });

    // Pause autoplay on hover/focus to reduce unexpected motion for mouse/keyboard users.
    this.root.addEventListener('mouseenter', function () {
      self.pauseAutoplay();
    });

    this.root.addEventListener('mouseleave', function () {
      if (self.autoplay && !self.reducedMotion) {
        self.startAutoplay();
      }
    });

    this.root.addEventListener('focusin', function () {
      self.pauseAutoplay();
    });

    this.root.addEventListener('focusout', function () {
      if (self.autoplay && !self.reducedMotion) {
        self.startAutoplay();
      }
    });
  };

  /**
   * If user clicks prev/next, we should pause autoplay by default
   * to avoid fighting the user.
   */
  SKBCCarousel.prototype.userInteracted = function () {
    // If autoplay is on, pause it after interaction.
    if (this.autoplay) {
      this.setPausedState(true);
      this.pauseAutoplay();
    }
  };

  /**
   * Start autoplay loop.
   */
    SKBCCarousel.prototype.startAutoplay = function () {
      var self = this;

      if (!this.autoplay || this.reducedMotion) return;

      this.pauseAutoplay(); // clear any existing timer

      this.timer = setTimeout(function () {
        self.next();
      }, this.slideDuration);
    };


  /**
   * Pause autoplay loop.
   */
  SKBCCarousel.prototype.pauseAutoplay = function () {
    this.clearTimer();
  };

  /**
   * Clear timer safely.
   */
  SKBCCarousel.prototype.clearTimer = function () {
    if (this.timer) {
      window.clearTimeout(this.timer);
      this.timer = null;
    }
  };

  /**
   * Toggle pause state.
   */
  SKBCCarousel.prototype.togglePause = function () {
    // Reduced motion: treat as permanently paused (no autoplay).
    if (this.reducedMotion) {
      this.setPausedState(true);
      return;
    }

    this.setPausedState(!this.isPaused);

    if (!this.isPaused && this.autoplay) {
      this.startAutoplay();
      this.startKenBurns(this.activeIndex);
    } else {
      this.pauseAutoplay();
      this.stopKenBurns(this.activeIndex);
    }
  };

  /**
   * Set paused state and update button label/icon.
   *
   * @param {boolean} paused
   */
  SKBCCarousel.prototype.setPausedState = function (paused) {
    this.isPaused = paused;

    if (!this.btnToggle || !this.toggleLabel || !this.toggleIcon) {
      return;
    }

    if (this.isPaused) {
      this.toggleLabel.textContent = 'Play autoplay';
      this.toggleIcon.textContent = '▶';
      this.btnToggle.setAttribute('aria-pressed', 'true');
    } else {
      this.toggleLabel.textContent = 'Pause autoplay';
      this.toggleIcon.textContent = '❚❚';
      this.btnToggle.setAttribute('aria-pressed', 'false');
    }
  };

  /**
   * Go to previous slide.
   */
  SKBCCarousel.prototype.prev = function () {
    var nextIndex = this.activeIndex - 1;
    if (nextIndex < 0) {
      nextIndex = this.slides.length - 1;
    }
    this.goTo(nextIndex);
  };

  /**
   * Go to next slide.
   */
  SKBCCarousel.prototype.next = function () {
    var nextIndex = this.activeIndex + 1;
    if (nextIndex >= this.slides.length) {
      nextIndex = 0;
    }
    this.goTo(nextIndex);
  };

  /**
   * Go to an index.
   *
   * @param {number} index
   */
  SKBCCarousel.prototype.goTo = function (index) {
    if (index === this.activeIndex) {
      return;
    }

    var prevIndex = this.activeIndex;
    this.activeIndex = index;

    // Stop animation on previous slide; start on new slide.
    this.stopKenBurns(prevIndex);
    this.syncUI();
    this.startKenBurns(index);

    // Restart autoplay timer if needed.
    if (this.autoplay && !this.reducedMotion) {
      this.startAutoplay();
    }

  };

  /**
   * Sync ARIA + classes for slides.
   */
  SKBCCarousel.prototype.syncUI = function () {
    var self = this;

    this.slides.forEach(function (slide, i) {
      var isActive = i === self.activeIndex;

      slide.classList.toggle('is-active', isActive);
      slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
      slide.setAttribute('tabindex', isActive ? '0' : '-1');

      // Make sure inactive slides are not accidentally focused.
      if (!isActive) {
        slide.blur && slide.blur();
      }
    });

    // Announce the active slide title if available.
    if (this.liveRegion) {
      var activeSlide = this.slides[this.activeIndex];
      var title = activeSlide ? activeSlide.getAttribute('data-skbc-title') : '';
      var announcement = title ? ('Slide ' + (this.activeIndex + 1) + ': ' + title) : ('Slide ' + (this.activeIndex + 1));
      this.liveRegion.textContent = announcement;
    }

    if (this.dots && this.dots.length) {
      this.dots.forEach(function (dot, i) {
        var isActive = i === self.activeIndex;
        dot.classList.toggle('is-active', isActive);
        dot.setAttribute('aria-current', isActive ? 'true' : 'false');
      });
    }

  };

  /**
   * Start Ken Burns animation on slide background.
   * This is handled by toggling a class, so CSS can do the animation.
   *
   * @param {number} index
   */
  SKBCCarousel.prototype.startKenBurns = function (index) {
    if (this.reducedMotion) {
      return;
    }

    var slide = this.slides[index];
    if (!slide) {
      return;
    }

    // Force reflow to restart CSS animation reliably.
    slide.classList.remove('is-animating');
    // eslint-disable-next-line no-unused-expressions
    slide.offsetHeight;
    slide.classList.add('is-animating');
  };

  /**
   * Stop Ken Burns animation on slide background.
   *
   * @param {number} index
   */
  SKBCCarousel.prototype.stopKenBurns = function (index) {
    var slide = this.slides[index];
    if (!slide) {
      return;
    }
    slide.classList.remove('is-animating');
  };

  /**
   * Initialize all carousels on DOM ready.
   */
  function init() {
    var roots = document.querySelectorAll('.skbc-carousel');
    if (!roots || !roots.length) {
      return;
    }

    Array.prototype.forEach.call(roots, function (root) {
      // Prevent double init if a builder preview triggers multiple loads.
      if (root.getAttribute('data-skbc-initialized') === 'true') {
        return;
      }
      root.setAttribute('data-skbc-initialized', 'true');
      // eslint-disable-next-line no-new
      new SKBCCarousel(root);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
