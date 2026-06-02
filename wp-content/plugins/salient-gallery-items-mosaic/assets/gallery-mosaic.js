/**
 * Gallery Mosaic JS
 * - AJAX filtering (no reload)
 * - Infinite scroll pagination (append pages)
 * - Accessible lightbox: focus trap, Esc close, Prev/Next, keyboard support
 * - Tiles are <a> links:
 *   - plain click opens lightbox
 *   - modified click (cmd/ctrl/shift/middle) keeps normal link behavior
 */
(function ($) {
  "use strict";

  function getItemsFromGrid($grid) {
    return $grid.find("[data-sgim-item]").toArray();
  }

  function trapFocus($dialog, e) {
    const focusable = $dialog
      .find(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
      )
      .filter(":visible")
      .toArray();

    if (!focusable.length) return;

    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    if (e.key === "Tab") {
      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault();
        last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    }
  }

  function isModifiedClick(e) {
    return e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button === 1;
  }

  /**
   * Replaces native <select data-sgim-filter> elements with an accessible
   * custom listbox dropdown (black + gold theme).
   *
   * The native <select> stays hidden in the DOM so that the gallery JS can
   * still read/set its value via jQuery's .val() — the custom UI syncs to it.
   *
   * ARIA pattern: button (aria-haspopup="listbox") + ul (role="listbox")
   * Keyboard: Enter/Space open; arrows navigate; Enter/Space select;
   *           Escape closes; Home/End jump; Tab closes.
   */
  function initCustomSelects($root) {
    $root.find("[data-sgim-filter]").each(function () {
      const $native   = $(this);
      const origId    = $native.attr("id") || "";
      const filterKey = $native.data("sgim-filter");
      const listboxId = "sgim-listbox-" + filterKey;

      // Move the id to the trigger so <label for="..."> still works.
      $native.removeAttr("id");

      // Wrap and hide native select (kept for .val() reads by gallery JS).
      $native.wrap('<div class="sgim__select-wrapper"></div>');
      const $wrapper = $native.parent();
      $native.addClass("sgim__select--hidden").attr({
        "aria-hidden": "true",
        "tabindex":    "-1",
      });

      // --- Build trigger button ---
      const initialText = $native.find("option:selected").text() ||
                          $native.find("option:first").text();

      const $trigger = $('<button type="button" class="sgim__select-trigger"></button>').attr({
        id:               origId,
        "aria-haspopup":  "listbox",
        "aria-expanded":  "false",
        "aria-controls":  listboxId,
      });

      $('<span class="sgim__select-value"></span>').text(initialText).appendTo($trigger);
      // Chevron arrow (SVG)
      $('<span class="sgim__select-arrow" aria-hidden="true">' +
          '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none">' +
            '<path d="M1 1L6 7L11 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>' +
          '</svg>' +
        '</span>').appendTo($trigger);

      // --- Build listbox ---
      const $listbox = $('<ul role="listbox" class="sgim__select-listbox" hidden></ul>').attr("id", listboxId);

      $native.find("option").each(function (i) {
        const val        = $(this).val();
        const text       = $(this).text();
        const isSelected = $(this).is(":selected");

        $('<li role="option" class="sgim__select-option"></li>').attr({
          "data-value":   val,
          "aria-selected": isSelected ? "true" : "false",
          id:              listboxId + "-" + i,
        }).text(text).appendTo($listbox);
      });

      $wrapper.append($trigger).append($listbox);

      // --- State ---
      let focusedIdx = -1;
      const isOpen   = () => $trigger.attr("aria-expanded") === "true";
      const $opts    = () => $listbox.find("[role='option']");

      function openList() {
        $trigger.attr("aria-expanded", "true");
        $listbox.prop("hidden", false);
        // Focus the currently selected item.
        const selIdx = $opts().index($opts().filter("[aria-selected='true']"));
        setFocus(selIdx >= 0 ? selIdx : 0);
      }

      function closeList() {
        $trigger.attr({ "aria-expanded": "false", "aria-activedescendant": "" });
        $listbox.prop("hidden", true);
        $opts().removeClass("sgim__select-option--focused");
        focusedIdx = -1;
      }

      function setFocus(idx) {
        const opts = $opts();
        opts.removeClass("sgim__select-option--focused");
        if (idx < 0 || idx >= opts.length) return;
        const $opt = opts.eq(idx);
        $opt.addClass("sgim__select-option--focused");
        $trigger.attr("aria-activedescendant", $opt.attr("id") || "");
        // Scroll option into view within the listbox.
        const listEl = $listbox[0];
        const optEl  = $opt[0];
        if (optEl.offsetTop < listEl.scrollTop) {
          listEl.scrollTop = optEl.offsetTop;
        } else if (optEl.offsetTop + optEl.offsetHeight > listEl.scrollTop + listEl.clientHeight) {
          listEl.scrollTop = optEl.offsetTop + optEl.offsetHeight - listEl.clientHeight;
        }
        focusedIdx = idx;
      }

      function selectByIndex(idx) {
        const opts = $opts();
        if (idx < 0 || idx >= opts.length) return;
        const $opt = opts.eq(idx);
        opts.attr("aria-selected", "false");
        $opt.attr("aria-selected", "true");
        $trigger.find(".sgim__select-value").text($opt.text());
        $trigger.toggleClass("sgim__select-trigger--active", String($opt.data("value")) !== "0");
        // Sync native select and notify gallery JS.
        $native.val($opt.data("value")).trigger("change");
        closeList();
        $trigger.focus();
      }

      // --- Interactions ---
      $trigger.on("click", function () {
        isOpen() ? closeList() : openList();
      });

      $trigger.on("keydown", function (e) {
        const opts    = $opts();
        const open    = isOpen();

        switch (e.key) {
          case "Enter":
          case " ":
            e.preventDefault();
            if (open) {
              if (focusedIdx >= 0) selectByIndex(focusedIdx);
            } else {
              openList();
            }
            break;
          case "ArrowDown":
            e.preventDefault();
            if (!open) { openList(); break; }
            setFocus(Math.min(focusedIdx + 1, opts.length - 1));
            break;
          case "ArrowUp":
            e.preventDefault();
            if (!open) { openList(); break; }
            setFocus(Math.max(focusedIdx - 1, 0));
            break;
          case "Home":
            e.preventDefault();
            if (open) setFocus(0);
            break;
          case "End":
            e.preventDefault();
            if (open) setFocus(opts.length - 1);
            break;
          case "Escape":
            if (open) { e.preventDefault(); closeList(); }
            break;
          case "Tab":
            if (open) closeList();
            break;
        }
      });

      // Use mousedown (not click) so that preventDefault() keeps focus on the
      // trigger button. Without this, focus leaves the trigger on mousedown,
      // the focusout handler closes the list, and the click never lands on an option.
      $listbox.on("mousedown", function (e) {
        e.preventDefault(); // Keep focus on trigger; suppress focusout-driven close.
      });

      $listbox.on("mousedown", "[role='option']", function (e) {
        e.preventDefault();
        selectByIndex($(this).index());
      });

      // Highlight on hover (mouse users expect this).
      $listbox.on("mousemove", "[role='option']", function () {
        const idx = $(this).index();
        if (idx !== focusedIdx) setFocus(idx);
      });

      // Close when focus leaves the wrapper entirely (keyboard Tab away etc.).
      $wrapper.on("focusout", function () {
        setTimeout(function () {
          if (!$wrapper[0].contains(document.activeElement)) closeList();
        }, 0);
      });

      // Close on outside click.
      $(document).on("click.sgim-select-" + filterKey, function (e) {
        if (!$wrapper[0].contains(e.target)) closeList();
      });
    });

    // When the gallery's clear button fires, reset custom dropdown labels too.
    $root.on("click.sgim-select-clear", "[data-sgim-clear]", function () {
      $root.find(".sgim__select-wrapper").each(function () {
        const $wrapper = $(this);
        const $native  = $wrapper.find("[data-sgim-filter]");
        const $trigger = $wrapper.find(".sgim__select-trigger");
        const $listbox = $wrapper.find(".sgim__select-listbox");

        const firstText = $native.find("option:first").text();
        $trigger.find(".sgim__select-value").text(firstText);
        $trigger.removeClass("sgim__select-trigger--active");
        $listbox.find("[role='option']").attr("aria-selected", "false");
        $listbox.find("[role='option']:first").attr("aria-selected", "true");
      });
    });
  }

  function initGallery($root) {
    initCustomSelects($root);
    const $grid = $root.find("[data-sgim-grid]");
    const $status = $root.find("[data-sgim-status]");
    const $sentinel = $root.find("[data-sgim-sentinel]");
    const $loader = $root.find("[data-sgim-loader]");
    const $filterLoader = $root.find("[data-sgim-filter-loader]");
    const $clearBtn = $root.find("[data-sgim-clear]");

    const $lightbox = $root.find("[data-sgim-lightbox]");
    const $dialog = $lightbox.find(".sgim__lightbox-dialog");
    const $img = $lightbox.find("[data-sgim-lightbox-img]");
    const $cap = $lightbox.find("[data-sgim-lightbox-caption]");

    // Move lightbox to <body> to escape any parent stacking context
    $("body").append($lightbox);

    let activeIndex = 0;
    let lastFocusedEl = null;

    let currentPage = 1;
    let hasMore = true;
    let isLoading = false;
    let pending = null;

    function setStatus(msg, loading) {
      // msg can be empty when done
      $status.attr("aria-busy", loading ? "true" : "false");

      if (loading) {
        $loader.prop("hidden", false);
        $loader.find(".sgim__loader-text").text(msg || "Loading images…");
        $filterLoader.prop("hidden", false);
      } else {
        $loader.prop("hidden", true);
        $filterLoader.prop("hidden", true);
        // keep text minimal so screen readers don't repeat
        // $status.text("");
        // re-insert loader node because we cleared status text above
        // $status.append($loader);
      }
    }

    function updateClearButtonVisibility() {
      const hasActiveFilter = $root
        .find("[data-sgim-filter]")
        .toArray()
        .some((el) => el.value && el.value !== "0");

      $clearBtn.prop("hidden", !hasActiveFilter);
    }

    function reindexTiles() {
      $grid.find("[data-sgim-item]").each(function (i) {
        $(this).attr("data-index", i);
      });
    }

    function openLightbox(index) {
      const items = getItemsFromGrid($grid);
      if (!items.length) return;

      activeIndex = Math.max(0, Math.min(index, items.length - 1));
      const el = items[activeIndex];
      const $el = $(el);

      const full = $el.data("full");
      const title = $el.data("title") || "";
      const caption = $el.data("caption") || "";

      lastFocusedEl = document.activeElement;

      $img.attr("src", full);
      $img.attr("alt", title);
      $cap.text(caption);

      $lightbox.prop("hidden", false);
      $("body").addClass("sgim--modal-open");

      setTimeout(function () {
        $dialog.attr("tabindex", "-1").focus();
      }, 0);
    }

    function closeLightbox() {
      $lightbox.prop("hidden", true);
      $("body").removeClass("sgim--modal-open");

      if (lastFocusedEl && typeof lastFocusedEl.focus === "function") {
        lastFocusedEl.focus();
      }
    }

    function goPrev() {
      const items = getItemsFromGrid($grid);
      if (!items.length) return;
      activeIndex = (activeIndex - 1 + items.length) % items.length;
      openLightbox(activeIndex);
    }

    function goNext() {
      const items = getItemsFromGrid($grid);
      if (!items.length) return;
      activeIndex = (activeIndex + 1) % items.length;
      openLightbox(activeIndex);
    }

    function getFilterState() {
      return {
        market: $root.find('[data-sgim-filter="market"]').val() || "0",
        product: $root.find('[data-sgim-filter="product"]').val() || "0",
        project: $root.find('[data-sgim-filter="project"]').val() || "0",
      };
    }

    function requestPage(page, append) {
      if (isLoading) return;
      isLoading = true;

      // Abort prior request if user changes filters fast
      if (pending && typeof pending.abort === "function") {
        pending.abort();
      }

      setStatus(SGIM?.strings?.loading || "Loading images…", true);

      const f = getFilterState();

      pending = $.ajax({
        url: SGIM.ajaxUrl,
        method: "POST",
        dataType: "json",
        data: {
          action: "sgim_filter",
          nonce: SGIM.nonce,
          market: f.market,
          product: f.product,
          project: f.project,
          orderBy: SGIM.orderBy,
          eagerFirst: SGIM.eagerFirst,
          page: page,
          perPage: SGIM.perPage || 24,
        },
      })
        .done(function (res) {
          if (!res || !res.success) return;

          const html = res.data.html || "";
          hasMore = !!res.data.hasMore;
          currentPage = res.data.page || page;

          if (append) {
            $grid.append(html);
          } else {
            $grid.html(html);
          }

          reindexTiles();
        })
        .always(function () {
          isLoading = false;
          setStatus("", false);
        });
    }

    function resetAndLoad() {
      currentPage = 1;
      hasMore = true;
      requestPage(1, false);
    }

    function loadNextPage() {
      if (!hasMore || isLoading) return;
      requestPage(currentPage + 1, true);
    }

    // Tile click -> lightbox (delegated)
    $root.on("click", "[data-sgim-item]", function (e) {
      if (isModifiedClick(e)) return;
      e.preventDefault();

      const idx = parseInt($(this).data("index"), 10) || 0;
      openLightbox(idx);
    });

    $lightbox.on("click", "[data-sgim-close]", function () {
      closeLightbox();
    });

    $lightbox.on("click", "[data-sgim-prev]", function () {
      goPrev();
    });

    $lightbox.on("click", "[data-sgim-next]", function () {
      goNext();
    });

    $(document).on("keydown.sgim", function (e) {
      if ($lightbox.prop("hidden")) return;

      if (e.key === "Escape") {
        e.preventDefault();
        closeLightbox();
        return;
      }
      if (e.key === "ArrowLeft") {
        e.preventDefault();
        goPrev();
        return;
      }
      if (e.key === "ArrowRight") {
        e.preventDefault();
        goNext();
        return;
      }

      trapFocus($dialog, e);
    });

    // Filters
    $root.on("change", "[data-sgim-filter]", function () {
      updateClearButtonVisibility();
      resetAndLoad();
    });

    $root.on("click", "[data-sgim-clear]", function () {
      $root.find("[data-sgim-filter]").val("0");
      updateClearButtonVisibility();
      resetAndLoad();
    });

    // Infinite scroll (only if enabled)
    const infiniteEnabled = parseInt(SGIM.infinite, 10) === 1;

    if (infiniteEnabled && $sentinel.length) {
      if ("IntersectionObserver" in window) {
        const io = new IntersectionObserver(
          function (entries) {
            entries.forEach((entry) => {
              if (entry.isIntersecting) loadNextPage();
            });
          },
          { root: null, rootMargin: "600px 0px", threshold: 0 }
        );

        io.observe($sentinel.get(0));
      } else {
        // Fallback: basic scroll listener for older browsers
        $(window).on("scroll.sgim", function () {
          const nearBottom =
            window.innerHeight + window.scrollY >= document.body.offsetHeight - 800;
          if (nearBottom) loadNextPage();
        });
      }
    }

    // Initial reindex for first page
    reindexTiles();
    updateClearButtonVisibility();
  }

  $(function () {
    $("[data-sgim]").each(function () {
      initGallery($(this));
    });
  });
})(jQuery);