(function($) {

  "use strict";

  /* List View */
  function NectarWPBakeryListView() {

    // Bail early if WPBakery frontend editor is not available.
    if (typeof window.vc === 'undefined' || typeof window.vc.events === 'undefined') {
      console.error('NectarWPBakeryListView: WPBakery frontend editor (window.vc) is not available.');
      return;
    }

    this.state = {
      open: false,
      throttle: '',
      // Drag and drop state
      dragging: false,
      draggedEl: null,
      draggedModelId: null,
      draggedShortcode: null,
      dropTarget: null,
      dropPosition: null // 'before', 'after', 'inside'
    }

    // Persistent Set to track expanded elements - survives updateListView calls
    this.expandedSet = new Set();

    // Preload empty drag image to prevent browser default on first drag
    // Using Canvas to create transparent pixel (avoids base64 strings that some servers flag)
    var canvas = document.createElement('canvas');
    canvas.width = 1;
    canvas.height = 1;
    this.emptyDragImg = new Image();
    this.emptyDragImg.src = canvas.toDataURL();

    // =========================================================================
    // ELEMENT CLASSIFICATION LISTS
    // Centralized definitions for drag & drop validation
    // =========================================================================

    // Structural elements can only be reordered within their parent container
    // They cannot be dropped "inside" other elements
    this.structuralElements = [
      'vc_row',
      'vc_row_inner',
      'vc_column',
      'vc_column_inner',
      'vc_section',
      'vc_grid_container',
      'vc_grid_container_item',
      'vc_flexbox_container',
      'vc_flexbox_container_item',
      // Child elements of special containers
      'nectar_sticky_media_section',
      'tab',
      'toggle',
      'item',
      'testimonial',
      'client',
      'pricing_column',
      'nectar_icon_list_item',
      'nectar_chat_thread_bubble',
      'page_link',
      'nectar_hotspot'
    ];

    // Root-only elements must stay at the top level (no parent)
    this.rootOnlyElements = [
      'vc_section',
      'vc_grid_container',
      'vc_flexbox_container'
    ];

    // Valid parent rules - element => array of allowed parent shortcodes (null = root level)
    // These elements can only exist at root OR within specific parent containers
    this.validParentRules = {
      'vc_row': [null, 'vc_section'],           // root level OR inside vc_section
      'vc_column': ['vc_row'],                   // only inside vc_row
      'vc_row_inner': ['vc_column', 'nectar_sticky_media_sections'],  // only inside vc_column or special containers
      'vc_column_inner': ['vc_row_inner']        // only inside vc_row_inner
    };

    // Strict parent rules - child element => required parent shortcode
    // These elements can ONLY exist within their specified parent container
    this.strictParentRules = {
      'nectar_sticky_media_section': 'nectar_sticky_media_sections',
      'tab': 'tabbed_section',
      'toggle': 'toggles',
      'testimonial': 'testimonial_slider',
      'client': 'clients',
      'pricing_column': 'pricing_table',
      'item': 'carousel',
      'nectar_icon_list_item': 'nectar_icon_list',
      'nectar_chat_thread_bubble': 'nectar_chat_thread',
      'page_link': 'page_submenu',
      'nectar_hotspot': 'nectar_image_with_hotspots'
    };

    // =========================================================================

    this.map = [];
    this.data = [];
    this.winH = window.innerHeight;
    this.winW = window.innerWidth - 320;

    this.createDOM();
    this.events();
    this.initDragAndDrop();

  }

  NectarWPBakeryListView.prototype.createDOM = function() {
    var i18n = window.nectar_wpbakery_i18n || {};
    var navLabel = i18n.element_navigator || 'Element Navigator';

    $('.vc_navbar-frontend .vc_navbar-nav').append($('<li class="pull-right"><a id="nectar-list-view-trigger" class="vc_icon-btn vc_element-button" href="#"><i class="nectar_el_icon element-navigator"></i><span>'+navLabel+'</span></a></li>'));
    $('body').append($('<div id="nectar-list-view-content"/>'));

    this.$el = $('#nectar-list-view-content');
  }

  NectarWPBakeryListView.prototype.events = function() {

    var that = this;

    // Shortcodes have changed.
    window.vc.events.on("app.render", this.updateListView.bind(this));

    // Add post-init classes to iframe body after shortcodes have rendered for loading performance.
    window.vc.events.on("app.render", function() {
      if (window.vc.frame_window && window.vc.frame_window.jQuery) {
        var $iframeBody = window.vc.frame_window.jQuery('body');
        $iframeBody.addClass('nectar-fe-ready');
        if (window.vc.frame_window.jQuery('.vc-main-sortable-container > .vc_vc_section').length > 0) {
          $iframeBody.addClass('nectar-fe-has-sections');
        }
      }
    });
    window.vc.events.on("undoredo:add undoredo:undo undoredo:redo shortcodeView:ready", function() {

      clearTimeout(that.state.throttle);

      // throttle.
      that.state.throttle = setTimeout(function(){
        that.updateListView();
      },50);
    });

    // Clear expanded state on undo/redo.
    window.vc.events.on("undoredo:undo undoredo:redo", function() {
      that.expandedSet.clear();
    });

    // Toggle list view.
    var listViewTrigger = document.getElementById("nectar-list-view-trigger");
    if (listViewTrigger) {
      listViewTrigger.addEventListener('click', this.toggleListView.bind(this));
    }

    // Edit list view items.
    $('body').on('click', '#nectar-list-view-content button', {instance: this}, this.elEdit);

    // Hover list view items.
    $('body').on('mouseenter', '#nectar-list-view-content .flex', this.elHighlight);
    $('body').on('mouseleave', '#nectar-list-view-content .flex', this.elHighlightRemove);

    // List view toggle.
    $('body').on('click', '#nectar-list-view-content .toggle-group', {instance: this}, this.toggleListViewItem);

    // Track window height.
    window.addEventListener('resize', function() {
      that.winH = window.innerHeight;
      that.winW = window.innerWidth - 330;
    });

  };

  NectarWPBakeryListView.prototype.convertToTree = function(list) {

    var node, roots = [], i;

    for (i = 0; i < list.length; i += 1) {
      this.map[list[i].attributes.id] = i; // initialize the map
      list[i].children = []; // initialize the children
    }

    for (i = 0; i < list.length; i += 1) {

      node = list[i];

      var disabled = (node.attributes.params && typeof node.attributes.params.disable_element !== 'undefined' && node.attributes.params.disable_element === 'yes') ? true : false;

      // Children.
      if (node.attributes.parent_id != 0 ) {

        if( !disabled ) {
          var parentIndex = this.map[node.attributes.parent_id];
          // Safety check: ensure parent exists in map before pushing
          if (typeof parentIndex !== 'undefined' && list[parentIndex]) {
            list[parentIndex].children.push(node);
            // Keep order.
            list[parentIndex].children.sort(this.treeOrder);
          }
        }

      }

      // Root rows.
      else {

        if( node.settings && typeof node.settings.name !== 'undefined' && !disabled ) {
          roots.push(node);

          // Keep order.
          roots.sort(this.treeOrder);
        }
      }
    }

    return roots;
  }


  NectarWPBakeryListView.prototype.treeOrder = function(a,b) {
    var orderA = (a && a.attributes) ? (a.attributes.order || 0) : 0;
    var orderB = (b && b.attributes) ? (b.attributes.order || 0) : 0;
    if( orderA > orderB ) {
      return 1;
    }
    else if( orderA < orderB ) {
      return -1;
    }
    return 0;
  }

  NectarWPBakeryListView.prototype.outputTree = function(box,data) {

    var arrow = '', icon = '';

    for (var i = 0; i < data.length; i++) {

      // Safety check - skip items without required properties
      if (!data[i] || !data[i].settings || !data[i].attributes) {
        continue;
      }

      var _oUl = document.createElement("ul");
      var _oLi = document.createElement("li");
      var settings = data[i].settings;
      var attributes = data[i].attributes;
      var shortcode = attributes.shortcode || '';

      // toggle arrow.
      if( settings.is_container === true && data[i].children && data[i].children.length > 0 ) {
        _oLi.classList.add('container-toggle');
        arrow = '<div class="dashicons dashicons-arrow-down toggle-group"> </div>';
      } else if( settings.is_container === true ) {
        // Still mark as container even if empty (for drag and drop purposes)
        _oLi.classList.add('container-toggle');
        _oLi.classList.add('empty-container');
        arrow = '';
      } else {
        arrow = '';
      }

      // el icon.
      if( settings.icon ) {
        icon = '<i class="nectar_el_icon ' + settings.icon + '"></i>';
      } else if( shortcode.indexOf('vc_column') !== -1 ) {
        icon = '<i class="nectar_el_icon icon-vc_column"></i>';
      } else {
        icon = '<i class="nectar_el_icon vc_general"></i>';
      }

      if( shortcode.indexOf('column') !== -1 || shortcode.indexOf('row') !== -1  ) {
        _oLi.classList.add('core-container');
      }
      _oLi.setAttribute('data-id', attributes.id || '');
      _oLi.setAttribute('data-shortcode', shortcode);

      _oLi.innerHTML = '<span class="flex" draggable="true">'+ arrow +'<button type="button" data-shortcode="'+ shortcode +'" data-id="'+ (attributes.id || '') +'">' + icon + (settings.name || shortcode) + '</button></span>';

      if(data[i].children){
        _oLi.appendChild(_oUl);
        this.outputTree(_oUl,data[i].children);
      }
      box.appendChild(_oLi);
    }

  }


  NectarWPBakeryListView.prototype.updateListView = function(e) {

    if(!this.state.open) {
      return;
    }

    var that = this;

    this.data = window.vc.shortcodes;


    // Walk and render.
    this.data = this.convertToTree(this.data.models);

    // Show empty state if no elements
    if (!this.data || this.data.length === 0) {
      var i18n = window.nectar_wpbakery_i18n || {};
      var emptyTitle = i18n.empty_page_title || 'No elements yet';
      var emptyDesc = i18n.empty_page_desc || 'Elements added to the page will appear here in a tree view for easy navigation and reordering.';

      this.$el.html(
        '<div class="nectar-list-empty">' +
          '<div class="nectar-list-empty-notice">' +
            '<i class="nectar_el_icon element-navigator"></i>' +
            '<strong>' + emptyTitle + '</strong>' +
            '<span>' + emptyDesc + '</span>' +
          '</div>' +
        '</div>'
      );
      return;
    }

    this.$el.html('<ul />');

    this.outputTree(this.$el.find('ul')[0], this.data);

    // Restore expanded state from persistent Set
    this.expandedSet.forEach(function(id) {
      var $li = that.$el.find('li[data-id="'+id+'"]');
      if ($li.length && $li.hasClass('container-toggle')) {
        $li.addClass('open');
        $li.find('> ul').show();
      } else {
        // Element no longer exists, remove from set
        that.expandedSet.delete(id);
      }
    });

  }



  NectarWPBakeryListView.prototype.elEdit = function(e) {

    var that = e.data.instance;
    var id = $(this).attr('data-id');
    var shortcode = $(this).attr('data-shortcode');
    var controls, editBtn;

    // Safety check - ensure frame_window is available
    if (!vc.frame_window || !vc.frame_window.jQuery) {
      console.warn('NectarWPBakeryListView: frame_window not available');
      return;
    }

    // Scroll to element.
    var el = vc.frame_window.jQuery('[data-model-id="'+id+'"]')[0];

    // Safety check - element might not exist
    if (!el) {
      console.warn('NectarWPBakeryListView: Element not found in iframe', id);
      return;
    }

    var elRect = el.getBoundingClientRect();

    if( elRect.left < that.winW && vc.frame_window.jQuery('#nectar_fullscreen_rows').length == 0 ) {
      el.scrollIntoView({
        behavior: "smooth",
        block: (el.clientHeight < that.winH) ? "center" : "start"
      });
    }

    if (e.shiftKey) {
      return true;
    }

    // Open edit settings.
    if( shortcode === 'vc_row' || shortcode === 'vc_row_inner' ) {
      controls = vc.frame_window.jQuery('[data-model-id="'+id+'"] > .wpb_row > .span_12 > .vc_container-block > .vc_controls');

      if( vc.frame_window.jQuery('#nectar_fullscreen_rows').length > 0 && shortcode === 'vc_row' ) {
        controls = vc.frame_window.jQuery('[data-model-id="'+id+'"] .full-page-inner .span_12').first();
        if( controls.length ) {
          controls = controls.find('> .vc_container-block > .vc_controls');
        }
      }

      editBtn = controls.find('.vc_controls-out-tl .vc_parent .vc_advanced .vc_control-btn-edit')[0];
      if (editBtn) editBtn.click();
    }

    else if( shortcode === 'vc_column' || shortcode === 'vc_column_inner' ) {
      controls = vc.frame_window.jQuery('[data-model-id="'+id+'"] > .vc_controls');
      editBtn = controls.find('.vc_controls-out-tl .vc_element .vc_advanced .vc_control-btn-edit')[0];
      if (editBtn) editBtn.click();
    }

    else if( shortcode === 'carousel' ||
             shortcode === 'toggles' ||
             shortcode === 'testimonial_slider' ||
             shortcode === 'clients' ||
             shortcode === 'page_submenu' ||
             shortcode === 'tabbed_section' ||
             shortcode === 'pricing_table' ||
             shortcode === 'nectar_icon_list' ) {

              controls = vc.frame_window.jQuery('[data-model-id="'+id+'"] .vc_controls-out-tr .parent-'+shortcode+' .vc_advanced').first();
              editBtn = controls.find('.vc_control-btn-edit')[0];
              if (editBtn) editBtn.click();
    }
    else if( shortcode === 'item' ||
             shortcode === 'toggle' ||
             shortcode === 'testimonial' ||
             shortcode === 'client' ||
             shortcode === 'page_link' ||
             shortcode === 'tab' ||
             shortcode === 'pricing_column' ||
             shortcode === 'nectar_icon_list_item' ) {

              controls = vc.frame_window.jQuery('[data-model-id="'+id+'"] > .vc_controls .element-'+shortcode);
              editBtn = controls.find('.vc_control-btn-edit')[0];
              if (editBtn) editBtn.click();
    }

    else {
      controls = vc.frame_window.jQuery('[data-model-id="'+id+'"] > .vc_controls');
      editBtn = controls.find('.vc_control-btn-edit')[0];
      if (editBtn) editBtn.click();
    }

  }

  NectarWPBakeryListView.prototype.elHighlight = function(e) {
    if (!vc.frame_window || !vc.frame_window.jQuery) return;

    var id = $(this).closest('li').attr('data-id');
    vc.frame_window.jQuery('[data-model-id="'+id+'"]').addClass('nectar-vc-el-outline-active');
  }

  NectarWPBakeryListView.prototype.elHighlightRemove = function(e) {
    if (!vc.frame_window || !vc.frame_window.jQuery) return;

    vc.frame_window.jQuery('[data-model-id]').removeClass('nectar-vc-el-outline-active');
  }

  NectarWPBakeryListView.prototype.toggleListViewItem = function(e) {

    var that = e.data.instance;
    var $parent = $(this).closest('li');
    var modelID = $parent.find('button').attr('data-id');

    if (!$parent.hasClass('open')) {
      // Opening - add to expanded set
      that.expandedSet.add(modelID);

      $parent.addClass('open');
      $parent.find('> ul').show();

      // Auto-open children and add them to expanded set
      $parent.find('li.container-toggle.core-container').each(function(){
        var childId = $(this).find('> .flex > button').attr('data-id');
        if (childId) {
          that.expandedSet.add(childId);
        }
        $(this).addClass('open');
        $(this).find('> ul').show();
      });

    } else {
      // Closing - remove from expanded set
      that.expandedSet.delete(modelID);

      $parent.find('> ul').hide();
      $parent.removeClass('open');

      // Close children and remove from expanded set
      $parent.find('li.container-toggle.core-container').each(function(){
        var childId = $(this).find('> .flex > button').attr('data-id');
        if (childId) {
          that.expandedSet.delete(childId);
        }
        $(this).removeClass('open');
        $(this).find('> ul').hide();
      });
    }
  }

  NectarWPBakeryListView.prototype.toggleListView = function(e) {

    e.preventDefault();

    var body = document.querySelector("body");
    var that = this;

    // Open.
    if( this.state.open == false ) {
      body.style.paddingLeft = '320px';
      document.querySelector("body").classList.add('el-navigator-open');
      this.$el[0].classList.add('open');
      setTimeout(function() { that.updateListView(); },100);
    }
    // Close.
    else {
      body.style.paddingLeft = '0px';
      document.querySelector("body").classList.remove('el-navigator-open');
      this.$el[0].classList.remove('open');
    }

    this.state.open = !this.state.open;

  }


  /**
   * Initialize drag and drop functionality for the tree list.
   */
  NectarWPBakeryListView.prototype.initDragAndDrop = function() {
    var that = this;

    // Delegate drag events to list items
    this.$el.on('dragstart', 'li > .flex', function(e) {
      that.handleDragStart(e, this);
    });

    this.$el.on('dragend', 'li > .flex', function(e) {
      that.handleDragEnd(e);
    });

    this.$el.on('dragover', 'li', function(e) {
      that.handleDragOver(e, this);
    });

    this.$el.on('dragleave', 'li', function(e) {
      that.handleDragLeave(e, this);
    });

    this.$el.on('drop', 'li', function(e) {
      that.handleDrop(e, this);
    });

    // Safety: Escape key cancels drag
    $(document).on('keydown.nectarDrag', function(e) {
      if (e.key === 'Escape' && that.state.dragging) {
        that.handleDragEnd(e);
      }
    });

    // Safety: Window blur/focus loss cancels drag
    $(window).on('blur.nectarDrag', function() {
      if (that.state.dragging) {
        that.handleDragEnd({});
      }
    });

    // Safety: Catch drops outside the list view (but not on list items themselves)
    $(document).on('drop.nectarDrag', function(e) {
      if (that.state.dragging && !$(e.target).closest('#nectar-list-view-content li').length) {
        e.preventDefault();
        that.handleDragEnd(e);
      }
    });

  };


  /**
   * Handle drag start event.
   */
  NectarWPBakeryListView.prototype.handleDragStart = function(e, el) {
    var $li = $(el).closest('li');
    var $btn = $li.find('> .flex > button');
    var that = this;

    this.state.dragging = true;
    this.state.draggedEl = $li[0];

    // Try button first, fall back to li attributes
    this.state.draggedModelId = $btn.attr('data-id') || $li.attr('data-id');
    this.state.draggedShortcode = $btn.attr('data-shortcode') || $li.attr('data-shortcode');

    // Set up drag data
    e.originalEvent.dataTransfer.effectAllowed = 'move';
    e.originalEvent.dataTransfer.setData('text/plain', this.state.draggedModelId);

    // Use preloaded transparent image to hide browser's default drag image
    e.originalEvent.dataTransfer.setDragImage(this.emptyDragImg, 0, 0);

    // Create custom floating drag ghost that follows mouse
    var elementName = $btn.text().trim();
    var $dragGhost = $('<div id="nectar-drag-ghost">' + elementName + '</div>');
    $dragGhost.css({
      position: 'fixed',
      top: e.originalEvent.clientY + 'px',
      left: e.originalEvent.clientX + 'px',
      padding: '8px 16px',
      backgroundColor: '#3a67ff',
      color: '#fff',
      fontSize: '13px',
      fontWeight: '600',
      borderRadius: '6px',
      whiteSpace: 'nowrap',
      boxShadow: '0 4px 6px -1px rgba(0,0,0,0.1), 0 10px 15px -3px rgba(0,0,0,0.2), 0 20px 25px -5px rgba(0,0,0,0.15)',
      zIndex: '999999',
      pointerEvents: 'none',
      transform: 'translate(-50%, -50%) scale(0.85)',
      opacity: '0',
      transition: 'transform 0.15s ease-out, opacity 0.15s ease-out'
    });
    $('body').append($dragGhost);

    // Trigger entrance animation
    setTimeout(function() {
      $dragGhost.css({
        transform: 'translate(-50%, -50%) scale(1)',
        opacity: '1'
      });
    }, 10);

    // Store reference and set up mouse tracking via drag event on the element
    this._dragGhost = $dragGhost;
    var ghost = $dragGhost[0];
    var lastX = e.originalEvent.clientX;
    var lastY = e.originalEvent.clientY;

    this._dragHandler = function(ev) {
      // drag event may have 0,0 coordinates at end, so filter those
      if (ev.clientX !== 0 || ev.clientY !== 0) {
        lastX = ev.clientX;
        lastY = ev.clientY;
      }
      ghost.style.top = lastY + 'px';
      ghost.style.left = lastX + 'px';
    };

    this._dragOverHandler = function(ev) {
      ev.preventDefault();
      if (ev.clientX !== 0 || ev.clientY !== 0) {
        lastX = ev.clientX;
        lastY = ev.clientY;
      }
      ghost.style.top = lastY + 'px';
      ghost.style.left = lastX + 'px';
    };

    $li[0].addEventListener('drag', this._dragHandler);
    document.addEventListener('dragover', this._dragOverHandler);
    this._draggedLi = $li[0];

    // Add is-dragging class immediately to disable hover states during drag
    this.$el.addClass('is-dragging');

    // Add dragging class to the item after a small delay to prevent it from being part of the drag image
    setTimeout(function() {
      $li.addClass('nectar-dragging');
    }, 0);

    // Trigger visual indicator in iframe
    if (vc.frame_window && vc.frame_window.jQuery) {
      vc.frame_window.jQuery(window).trigger('nectar-frontend-sorting-start');
    }
  };


  /**
   * Handle drag end event.
   */
  NectarWPBakeryListView.prototype.handleDragEnd = function(e) {
    // Remove custom drag ghost and event listeners
    if (this._dragHandler && this._draggedLi) {
      this._draggedLi.removeEventListener('drag', this._dragHandler);
      this._dragHandler = null;
      this._draggedLi = null;
    }
    if (this._dragOverHandler) {
      document.removeEventListener('dragover', this._dragOverHandler);
      this._dragOverHandler = null;
    }
    if (this._dragGhost) {
      this._dragGhost.remove();
      this._dragGhost = null;
    }

    this.state.dragging = false;
    this.state.draggedEl = null;
    this.state.draggedModelId = null;
    this.state.draggedShortcode = null;
    this.state.dropTarget = null;
    this.state.dropPosition = null;

    // Remove all drag-related classes
    this.$el.removeClass('is-dragging');
    this.$el.find('.nectar-dragging').removeClass('nectar-dragging');
    this.$el.find('.nectar-drop-before, .nectar-drop-after, .nectar-drop-inside').removeClass('nectar-drop-before nectar-drop-after nectar-drop-inside');

    // Trigger visual indicator stop in iframe
    if (vc.frame_window && vc.frame_window.jQuery) {
      vc.frame_window.jQuery(window).trigger('nectar-frontend-sorting-stop');
    }
  };


  /**
   * Handle drag over event - determines drop position and validates the move.
   */
  NectarWPBakeryListView.prototype.handleDragOver = function(e, el) {
    if (!this.state.dragging) return;

    e.preventDefault();
    e.stopPropagation(); // Prevent bubbling to parent li elements
    e.originalEvent.dataTransfer.dropEffect = 'move';

    var $li = $(el);

    // Don't allow dropping on self or children of dragged element
    if (this.state.draggedEl === el || $.contains(this.state.draggedEl, el)) {
      // Clear indicators when over self/children
      this.$el.find('.nectar-drop-before, .nectar-drop-after, .nectar-drop-inside').removeClass('nectar-drop-before nectar-drop-after nectar-drop-inside');
      this.state.dropTarget = null;
      this.state.dropPosition = null;
      return;
    }

    var $btn = $li.find('> .flex > button');
    // Try button first, fall back to li attributes
    var targetModelId = $btn.attr('data-id') || $li.attr('data-id');
    var targetShortcode = $btn.attr('data-shortcode') || $li.attr('data-shortcode');

    // Safety check - make sure we have required data
    if (!this.state.draggedShortcode || !targetModelId || !targetShortcode) {
      return;
    }

    var draggedIsStructural = this.structuralElements.indexOf(this.state.draggedShortcode) !== -1;


    // Calculate drop position based on mouse position within the element
    var rect = el.getBoundingClientRect();
    var mouseY = e.originalEvent.clientY;
    var relativeY = mouseY - rect.top;
    var height = rect.height;

    var position;
    var isContainer = $li.hasClass('container-toggle');
    var isEmptyContainer = $li.hasClass('empty-container');

    // Elements that should never show "inside" indicator (even when empty)
    var neverInsideTargets = ['vc_row', 'vc_row_inner', 'vc_section'];
    var targetNeverAllowsInside = neverInsideTargets.indexOf(targetShortcode) !== -1;

    // Columns can accept content dropped "inside" when empty
    var columnTargets = ['vc_column', 'vc_column_inner'];
    var targetIsColumn = columnTargets.indexOf(targetShortcode) !== -1;

    // Special case: vc_row can be dropped INSIDE vc_section
    var draggedRowCanGoInSection = (this.state.draggedShortcode === 'vc_row' && targetShortcode === 'vc_section');
    // Special case: vc_row_inner can be dropped INSIDE valid parent containers (vc_column or special containers)
    var innerRowValidParents = this.validParentRules['vc_row_inner'] || ['vc_column'];
    var draggedInnerRowCanGoInValidParent = (this.state.draggedShortcode === 'vc_row_inner' && innerRowValidParents.indexOf(targetShortcode) !== -1);

    // Structural elements can ONLY be placed before/after, never inside (except special cases)
    if (draggedIsStructural && !draggedRowCanGoInSection && !draggedInnerRowCanGoInValidParent) {
      position = relativeY < height * 0.5 ? 'before' : 'after';
    } else if (draggedRowCanGoInSection || draggedInnerRowCanGoInValidParent) {
      // For vc_row over vc_section or vc_row_inner over vc_column, use larger "inside" zone
      // Only 15% at top/bottom for before/after, 70% in middle for inside
      if (relativeY < height * 0.15) {
        position = 'before';
      } else if (relativeY > height * 0.85) {
        position = 'after';
      } else {
        position = 'inside';
      }
    } else if (targetNeverAllowsInside) {
      // Rows/sections never show "inside" - only lines between
      position = relativeY < height * 0.5 ? 'before' : 'after';
    } else if (targetIsColumn && isEmptyContainer) {
      // Empty columns - allow dropping inside (show larger drop zone)
      position = 'inside';
    } else if (targetIsColumn && !isEmptyContainer) {
      // Non-empty columns - only show before/after (content goes between children)
      position = relativeY < height * 0.5 ? 'before' : 'after';
    } else if (isContainer) {
      // For content elements dragged onto content containers (tabs, accordions), allow inside
      if (relativeY < height * 0.25) {
        position = 'before';
      } else if (relativeY > height * 0.75) {
        position = 'after';
      } else {
        position = 'inside';
      }
    } else {
      // For non-containers, only allow before/after
      position = relativeY < height * 0.5 ? 'before' : 'after';
    }

    // Clear previous drop indicators
    this.$el.find('.nectar-drop-before, .nectar-drop-after, .nectar-drop-inside')
      .removeClass('nectar-drop-before nectar-drop-after nectar-drop-inside');

    // For structural elements, find the correct target immediately
    if (draggedIsStructural) {
      // Special case: vc_row dropping inside vc_section - validate and show indicator
      if (draggedRowCanGoInSection && position === 'inside') {
        // Validate: Can't drop row into section that contains the row (or is a descendant of it)
        if (!this.isDescendantOf(targetModelId, this.state.draggedModelId)) {
          $li.addClass('nectar-drop-inside');
          this.state.dropTarget = el;
          this.state.dropPosition = 'inside';
          return;
        }
      }

      // Special case: vc_row_inner dropping inside valid parent container - validate and show indicator
      if (draggedInnerRowCanGoInValidParent && position === 'inside') {
        // Validate: Can't drop inner row into container that contains the inner row (or is a descendant of it)
        if (!this.isDescendantOf(targetModelId, this.state.draggedModelId)) {
          $li.addClass('nectar-drop-inside');
          this.state.dropTarget = el;
          this.state.dropPosition = 'inside';
          return;
        }
      }

      var validTarget = this.findValidStructuralTarget(targetModelId, position, targetShortcode);
      if (validTarget) {
        var $validLi = this.$el.find('li[data-id="' + validTarget.id + '"]');
        if ($validLi.length) {
          $validLi.addClass('nectar-drop-' + validTarget.position);
          this.state.dropTarget = $validLi[0];
          this.state.dropPosition = validTarget.position;
          return;
        }
      }
      this.state.dropTarget = null;
      this.state.dropPosition = null;
      return;
    }

    // For content elements, validate normally
    var isValid = this.validateMove(targetModelId, targetShortcode, position);

    if (isValid) {
      $li.addClass('nectar-drop-' + position);
      this.state.dropTarget = el;
      this.state.dropPosition = position;
    } else {
      this.state.dropTarget = null;
      this.state.dropPosition = null;
    }
  };

  /**
   * Helper to find a model by ID - handles WPBakery's ID format
   */
  NectarWPBakeryListView.prototype.getModelById = function(modelId) {
    // Try direct get first
    var model = vc.shortcodes.get(modelId);
    if (model) {
      return model;
    }

    // Fallback: find by attributes.id
    model = vc.shortcodes.find(function(m) {
      return m.get('id') === modelId || m.id === modelId;
    });

    return model || null;
  };

  /**
   * Find a valid target for structural element drops.
   * Returns { id, position } or null if no valid target.
   */
  NectarWPBakeryListView.prototype.findValidStructuralTarget = function(targetModelId, position, targetShortcode) {
    var draggedModel = this.getModelById(this.state.draggedModelId);
    var targetModel = this.getModelById(targetModelId);

    if (!draggedModel || !targetModel) {
      return null;
    }

    var draggedShortcode = this.state.draggedShortcode;
    var currentParentId = draggedModel.get('parent_id');
    var rootOnlyElements = ['vc_section', 'vc_grid_container', 'vc_flexbox_container'];
    var isRootOnly = rootOnlyElements.indexOf(draggedShortcode) !== -1;

    if (isRootOnly) {
      // For root-only elements, find the root ancestor of the target
      var rootTarget = this.findRootAncestor(targetModelId);
      if (rootTarget && rootTarget !== this.state.draggedModelId) {
        return { id: rootTarget, position: position };
      }
      return null;
    }

    // Special handling for vc_row - can move between root and vc_section
    if (draggedShortcode === 'vc_row') {
      var targetParentId = targetModel.get('parent_id');
      var targetShortcodeType = targetShortcode || targetModel.get('shortcode');

      // Case 1: Target is a vc_section - allow dropping before/after (snaps to section level)
      if (targetShortcodeType === 'vc_section') {
        return { id: targetModelId, position: position };
      }

      // Case 2: Target is a vc_row - check if it's a valid sibling
      if (targetShortcodeType === 'vc_row') {
        // If both have same parent (both at root, or both in same section), valid sibling
        if (targetParentId === currentParentId && targetModelId !== this.state.draggedModelId) {
          return { id: targetModelId, position: position };
        }
        // If target is in a different valid parent (e.g., dragging root row, target is in section)
        // Still allow - will snap to that row's position
        return { id: targetModelId, position: position };
      }

      // Case 3: Target is something else (column, content) - find nearest valid ancestor
      // Look for a vc_row sibling in target's ancestry
      var ancestorRow = this.findAncestorByType(targetModelId, 'vc_row');
      if (ancestorRow && ancestorRow !== this.state.draggedModelId) {
        // When snapping to parent row, recalculate position based on DOM position
        var snapPosition = this.getSmartPosition(ancestorRow, targetModelId, position);
        return { id: ancestorRow, position: snapPosition };
      }

      // Look for section to snap to - row can go inside section
      var ancestorSection = this.findAncestorByType(targetModelId, 'vc_section');
      if (ancestorSection) {
        // When over content in a section, snap to the containing row or section edge
        var sectionSnapPosition = this.getSmartPosition(ancestorSection, targetModelId, position);
        return { id: ancestorSection, position: sectionSnapPosition };
      }

      // Fall back to root ancestor
      var rootTarget = this.findRootAncestor(targetModelId);
      if (rootTarget && rootTarget !== this.state.draggedModelId) {
        return { id: rootTarget, position: position };
      }

      return null;
    }

    // Special handling for vc_row_inner - can move between valid parent containers
    if (draggedShortcode === 'vc_row_inner') {
      var targetParentId = targetModel.get('parent_id');
      var targetShortcodeType = targetShortcode || targetModel.get('shortcode');
      var innerRowValidParents = this.validParentRules['vc_row_inner'] || ['vc_column'];

      // Case 1: Target is a valid parent container (vc_column, nectar_sticky_media_section, etc.)
      // IMPORTANT: Only allow 'inside' position - before/after would make parent be the container's parent (e.g., vc_row)
      if (innerRowValidParents.indexOf(targetShortcodeType) !== -1) {
        // Force 'inside' position to ensure inner row goes INTO the column, not beside it
        return { id: targetModelId, position: 'inside' };
      }

      // Case 2: Target is a vc_row_inner - check if parent is valid, then allow before/after
      if (targetShortcodeType === 'vc_row_inner') {
        if (targetModelId !== this.state.draggedModelId) {
          // Verify target's parent is a valid container
          var targetParent = this.getModelById(targetParentId);
          if (targetParent && innerRowValidParents.indexOf(targetParent.get('shortcode')) !== -1) {
            return { id: targetModelId, position: position };
          }
        }
        return null;
      }

      // Case 3: Target is something else - find nearest valid ancestor
      // First look for a vc_row_inner sibling in target's ancestry (within a valid parent)
      var ancestorInnerRow = this.findAncestorByType(targetModelId, 'vc_row_inner');
      if (ancestorInnerRow && ancestorInnerRow !== this.state.draggedModelId) {
        // Verify the inner row's parent is valid
        var innerRowModel = this.getModelById(ancestorInnerRow);
        if (innerRowModel) {
          var innerRowParent = this.getModelById(innerRowModel.get('parent_id'));
          if (innerRowParent && innerRowValidParents.indexOf(innerRowParent.get('shortcode')) !== -1) {
            var snapPosition = this.getSmartPosition(ancestorInnerRow, targetModelId, position);
            return { id: ancestorInnerRow, position: snapPosition };
          }
        }
      }

      // Look for a valid parent container to snap to - use 'inside' to ensure correct parent
      for (var i = 0; i < innerRowValidParents.length; i++) {
        var validParentType = innerRowValidParents[i];
        if (validParentType) { // Skip null
          var ancestorContainer = this.findAncestorByType(targetModelId, validParentType);
          if (ancestorContainer) {
            // Always use 'inside' for containers to ensure inner row goes INTO the container
            return { id: ancestorContainer, position: 'inside' };
          }
        }
      }

      return null;
    }

    // For other elements with valid parent rules
    // They can only be reordered within their current parent container
    var targetParentId = targetModel.get('parent_id');

    // If target has the same parent as dragged, it's a valid sibling
    if (targetParentId === currentParentId && targetModelId !== this.state.draggedModelId) {
      return { id: targetModelId, position: position };
    }

    // Try to find an ancestor of the target that shares the same parent
    var ancestorInSameParent = this.findAncestorInParent(targetModelId, currentParentId);
    if (ancestorInSameParent && ancestorInSameParent !== this.state.draggedModelId) {
      return { id: ancestorInSameParent, position: position };
    }

    return null;
  };

  /**
   * Find an ancestor of the given model that has the specified shortcode type.
   */
  NectarWPBakeryListView.prototype.findAncestorByType = function(modelId, shortcodeType) {
    var model = this.getModelById(modelId);
    if (!model) return null;

    if (model.get('shortcode') === shortcodeType) {
      return modelId;
    }

    var parentId = model.get('parent_id');
    if (!parentId) return null;

    return this.findAncestorByType(parentId, shortcodeType);
  };

  /**
   * Determine smart drop position when snapping to a container.
   * Looks at where the actual target is within the container's children.
   */
  NectarWPBakeryListView.prototype.getSmartPosition = function(containerId, targetId, defaultPosition) {
    var containerModel = this.getModelById(containerId);
    if (!containerModel) return defaultPosition;

    // Get all children of the container
    var children = vc.shortcodes.filter(function(s) {
      return s.get('parent_id') === containerId;
    });

    if (children.length === 0) {
      // Empty container - dropping inside (at the end)
      return 'after';
    }

    // Sort by order
    children.sort(function(a, b) {
      return (a.get('order') || 0) - (b.get('order') || 0);
    });

    // Find the target or its ancestor among children
    var targetIndex = -1;
    for (var i = 0; i < children.length; i++) {
      var childId = children[i].get('id') || children[i].id;
      if (childId === targetId || this.isDescendantOf(targetId, childId)) {
        targetIndex = i;
        break;
      }
    }

    // If target is in first half of children, use 'before', else 'after'
    if (targetIndex >= 0 && targetIndex < children.length / 2) {
      return 'before';
    }
    return 'after';
  };

  /**
   * Check if potentialDescendantId is a descendant of ancestorId.
   */
  NectarWPBakeryListView.prototype.isDescendantOf = function(potentialDescendantId, ancestorId) {
    if (potentialDescendantId === ancestorId) {
      return true;
    }

    var model = this.getModelById(potentialDescendantId);
    if (!model) return false;

    var parentId = model.get('parent_id');
    if (!parentId) return false;

    return this.isDescendantOf(parentId, ancestorId);
  };

  /**
   * Find the root-level ancestor of an element.
   */
  NectarWPBakeryListView.prototype.findRootAncestor = function(modelId) {
    var model = this.getModelById(modelId);
    if (!model) return null;

    var parentId = model.get('parent_id');
    if (!parentId) {
      // This element is at root level
      return modelId;
    }

    // Recursively find root ancestor
    return this.findRootAncestor(parentId);
  };

  /**
   * Find an ancestor of targetId that has the specified parentId.
   */
  NectarWPBakeryListView.prototype.findAncestorInParent = function(targetId, desiredParentId) {
    var model = this.getModelById(targetId);
    if (!model) return null;

    var parentId = model.get('parent_id');

    // If this element's parent is the desired parent, return this element
    if (parentId === desiredParentId) {
      return targetId;
    }

    // If we hit root without finding, return null
    if (!parentId) {
      return null;
    }

    // Recursively check parent
    return this.findAncestorInParent(parentId, desiredParentId);
  };


  /**
   * Handle drag leave event.
   */
  NectarWPBakeryListView.prototype.handleDragLeave = function(e, el) {
    var $li = $(el);
    $li.removeClass('nectar-drop-before nectar-drop-after nectar-drop-inside');
  };


  /**
   * Handle drop event - execute the move.
   */
  NectarWPBakeryListView.prototype.handleDrop = function(e, el) {
    e.preventDefault();
    e.stopPropagation();

    if (!this.state.dragging || !this.state.dropPosition || !this.state.dropTarget) {
      this.handleDragEnd(e);
      return;
    }

    // IMPORTANT: Use the validated dropTarget from handleDragOver, NOT the direct drop element.
    // For structural elements like vc_row, findValidStructuralTarget snaps to valid siblings.
    var $targetLi = $(this.state.dropTarget);
    var $targetBtn = $targetLi.find('> .flex > button');
    var targetModelId = $targetBtn.attr('data-id') || $targetLi.attr('data-id');

    // Execute the move
    this.executeMove(this.state.draggedModelId, targetModelId, this.state.dropPosition);

    // Clean up
    this.handleDragEnd(e);
  };


  /**
   * Validate if a move is allowed based on WPBakery's element rules.
   * @param {string} targetModelId - The model ID of the drop target
   * @param {string} targetShortcode - The shortcode type of the drop target
   * @param {string} position - 'before', 'after', or 'inside'
   * @returns {boolean}
   */
  NectarWPBakeryListView.prototype.validateMove = function(targetModelId, targetShortcode, position) {
    var draggedShortcode = this.state.draggedShortcode;
    var draggedModel = this.getModelById(this.state.draggedModelId);
    var targetModel = this.getModelById(targetModelId);

    if (!targetModel || !draggedModel) {
      return false;
    }

    // RULE 0: Cannot move element into itself or its descendants
    if (this.isDescendantOf(targetModelId, this.state.draggedModelId)) {
      return false;
    }

    var isStructuralElement = this.structuralElements.indexOf(draggedShortcode) !== -1;
    var isRootOnlyElement = this.rootOnlyElements.indexOf(draggedShortcode) !== -1;

    var currentParentId = draggedModel.get('parent_id');
    var newParentId, newParentShortcode;

    if (position === 'inside') {
      // Dropping inside the target - target becomes the parent
      newParentId = targetModelId;
      newParentShortcode = targetShortcode;
    } else {
      // Dropping before/after - target's parent becomes the parent
      newParentId = targetModel.get('parent_id');
      if (newParentId) {
        var parentModel = this.getModelById(newParentId);
        newParentShortcode = parentModel ? parentModel.get('shortcode') : null;
      } else {
        // Root level (no parent)
        newParentShortcode = null;
      }
    }

    // RULE 1: Root-only elements (sections, etc.) must stay at root level
    if (isRootOnlyElement) {
      // Cannot drop inside anything - must stay at root
      if (position === 'inside') {
        return false;
      }
      // New parent must be null (root level)
      if (newParentId) {
        return false;
      }
      // Current parent must also be null (already at root)
      if (currentParentId) {
        return false;
      }
      return true;
    }

    // RULE 1.25: Elements with valid parent rules (e.g., vc_row can be in root OR vc_section)
    if (this.validParentRules[draggedShortcode]) {
      var allowedParents = this.validParentRules[draggedShortcode];
      var isValidParentType = allowedParents.indexOf(newParentShortcode) !== -1;

      if (!isValidParentType) {
        return false;
      }

      // vc_row can change parents (move between root and vc_section)
      // vc_row_inner can change parents (move between different vc_columns)
      if (draggedShortcode === 'vc_row' || draggedShortcode === 'vc_row_inner') {
        // Allow parent change since new parent is valid
        return true;
      }

      // For other structural elements, they can only reorder within same parent
      if (currentParentId !== newParentId) {
        return false;
      }

      return true;
    }

    // RULE 1.5: Elements with strict parent requirements
    if (this.strictParentRules[draggedShortcode]) {
      var requiredParent = this.strictParentRules[draggedShortcode];
      // Must stay in same parent AND parent must be the correct type
      if (newParentShortcode !== requiredParent) {
        return false;
      }
      // Can only reorder within parent, not drop inside other elements
      if (position === 'inside' && targetShortcode !== requiredParent) {
        return false;
      }
      // If before/after, the new parent must be correct
      if (position !== 'inside' && newParentShortcode === requiredParent) {
        return true;
      }
      return false;
    }

    // RULE 2: Structural elements can only be reordered within their current parent
    // They cannot be moved to a different parent
    if (isStructuralElement) {
      // Cannot drop "inside" another element
      if (position === 'inside') {
        return false;
      }
      // Must stay in same parent
      if (currentParentId !== newParentId) {
        return false;
      }
      return true;
    }

    // RULE 3: Content elements can change parents, but follow WPBakery rules

    // Cannot drop at root level (content elements need a column parent)
    if (!newParentShortcode) {
      return false;
    }

    // Content elements can only go inside columns OR other content elements (tabs, accordions, etc.)
    var validContentParents = ['vc_column', 'vc_column_inner'];
    var isValidParent = validContentParents.indexOf(newParentShortcode) !== -1;

    // For dropping inside non-column containers, check if it's a valid content container
    if (!isValidParent && position === 'inside') {
      var parentMap = vc.map[newParentShortcode];
      // Allow if it's a content element that can contain children
      isValidParent = parentMap && this.structuralElements.indexOf(newParentShortcode) === -1;
    }

    if (!isValidParent) {
      return false;
    }

    // Use WPBakery's checkRelevance function for content elements
    if (typeof vc.checkRelevance === 'function') {
      if (!vc.checkRelevance(newParentShortcode, draggedShortcode)) {
        return false;
      }
    }

    // Check allowed_container_element restriction
    var parentMapCheck = vc.map[newParentShortcode];
    var childMap = vc.map[draggedShortcode];

    if (parentMapCheck && childMap) {
      if (typeof parentMapCheck.allowed_container_element !== 'undefined') {
        if (parentMapCheck.allowed_container_element === false && childMap.is_container) {
          return false;
        }
        if (typeof parentMapCheck.allowed_container_element === 'string' && childMap.is_container) {
          if (draggedShortcode !== parentMapCheck.allowed_container_element &&
              draggedShortcode.replace(/_inner$/, '') !== parentMapCheck.allowed_container_element) {
            return false;
          }
        }
      }
    }

    return true;
  };


  /**
   * Execute the move operation using WPBakery's API.
   *
   * Strategy: Update models AND manipulate DOM. The model is the source of truth
   * for saving, and DOM manipulation provides immediate visual feedback.
   *
   * @param {string} draggedModelId - The model ID being moved
   * @param {string} targetModelId - The model ID of the drop target
   * @param {string} position - 'before', 'after', or 'inside'
   */
  NectarWPBakeryListView.prototype.executeMove = function(draggedModelId, targetModelId, position) {
    var that = this;
    var draggedModel = this.getModelById(draggedModelId);
    var targetModel = this.getModelById(targetModelId);

    if (!draggedModel || !targetModel) {
      console.warn('Nectar DnD: Invalid models - draggedModelId:', draggedModelId, 'targetModelId:', targetModelId);
      return;
    }

    var oldParentId = draggedModel.get('parent_id');
    var newParentId;

    if (position === 'inside') {
      newParentId = targetModelId;
    } else {
      newParentId = targetModel.get('parent_id');
    }

    var parentChanging = oldParentId !== newParentId;

    // ========== STEP 1: Calculate new order ==========

    // Get siblings - filter by parent_id attribute
    var siblings = vc.shortcodes.filter(function(s) {
      return s.get('parent_id') === newParentId;
    });
    // Exclude the dragged element
    siblings = siblings.filter(function(s) {
      return s.get('id') !== draggedModelId && s.id !== draggedModelId;
    });
    siblings.sort(function(a, b) {
      return (a.get('order') || 0) - (b.get('order') || 0);
    });

    var insertIndex;
    var targetId = targetModel.get('id') || targetModel.id;
    if (position === 'inside') {
      insertIndex = siblings.length;
    } else {
      insertIndex = 0;
      for (var i = 0; i < siblings.length; i++) {
        var sibId = siblings[i].get('id') || siblings[i].id;
        if (sibId === targetId) {
          insertIndex = (position === 'before') ? i : i + 1;
          break;
        }
        insertIndex = i + 1;
      }
    }

    siblings.splice(insertIndex, 0, draggedModel);

    // ========== STEP 2: DETACH DOM ELEMENT FIRST ==========
    //
    // CRITICAL: Must detach the DOM element BEFORE updating the model.
    // WPBakery's model.save() triggers internal handlers that try to move the DOM,
    // which causes circular reference errors if element is still in old parent.
    //
    var $iframe = (vc.frame_window && vc.frame_window.jQuery) ? vc.frame_window.jQuery : null;
    var $draggedEl = $iframe ? $iframe('[data-model-id="' + draggedModelId + '"]') : null;
    var detachedEl = null;

    if ($draggedEl && $draggedEl.length && parentChanging) {
      // Detach element from DOM before any model changes
      detachedEl = $draggedEl.detach();
    }

    // ========== STEP 3: Update model data ==========

    if (parentChanging) {
      // Element is already detached, so WPBakery's internal DOM manipulation won't cause errors
      draggedModel.save({ parent_id: newParentId }, { silent: true }); // Silent to prevent WPBakery DOM manipulation
    }

    siblings.forEach(function(model, index) {
      if (model.get('order') !== index) {
        model.save({ order: index }, { silent: true });
      }
    });

    if (parentChanging && oldParentId) {
      var oldSiblings = vc.shortcodes.filter(function(s) {
        return s.get('parent_id') === oldParentId;
      });
      oldSiblings.sort(function(a, b) {
        return (a.get('order') || 0) - (b.get('order') || 0);
      });
      oldSiblings.forEach(function(model, index) {
        if (model.get('order') !== index) {
          model.save({ order: index }, { silent: true });
        }
      });
    }

    // ========== STEP 4: Place DOM element in new position ==========
    //
    if ($iframe && (detachedEl || ($draggedEl && $draggedEl.length))) {
      var $elToMove = detachedEl || $draggedEl;
      var domMoved = false;

      // Strategy: Always insert relative to siblings
      if (insertIndex > 0 && siblings[insertIndex - 1]) {
        var prevSiblingId = siblings[insertIndex - 1].id;
        var $prevSibling = $iframe('[data-model-id="' + prevSiblingId + '"]');
        if ($prevSibling.length) {
          $elToMove.insertAfter($prevSibling);
          domMoved = true;
        }
      }

      if (!domMoved && siblings.length > 1 && siblings[1]) {
        var nextSiblingId = siblings[1].id;
        var $nextSibling = $iframe('[data-model-id="' + nextSiblingId + '"]');
        if ($nextSibling.length) {
          $elToMove.insertBefore($nextSibling);
          domMoved = true;
        }
      }

      // If we haven't moved yet and there's a parent, try container approach
      if (!domMoved && newParentId) {
        var $parentEl = $iframe('[data-model-id="' + newParentId + '"]');
        if ($parentEl.length) {
          var $container = $parentEl.find('.vc_element-container, .wpb_column_container, .vc_column-inner > .wpb_wrapper').first();
          if ($container.length) {
            $elToMove.appendTo($container);
            domMoved = true;
          }
        }
      }

      // For root-level moves, find the main sortable container
      if (!domMoved && !newParentId) {
        var $rootContainer = $iframe('.vc_main-sortable-container, #vc_content_holder, .wpb-content-layouts').first();
        if ($rootContainer.length) {
          if (insertIndex === 0 || siblings.length <= 1) {
            $elToMove.prependTo($rootContainer);
            domMoved = true;
          } else {
            // Find a sibling to insert after
            for (var si = insertIndex - 1; si >= 0; si--) {
              if (siblings[si]) {
                var $safeSibling = $iframe('[data-model-id="' + siblings[si].id + '"]');
                if ($safeSibling.length) {
                  $elToMove.insertAfter($safeSibling);
                  domMoved = true;
                  break;
                }
              }
            }
            if (!domMoved) {
              $elToMove.prependTo($rootContainer);
              domMoved = true;
            }
          }
        }
      }
    }

    // ========== STEP 5: Update parent views ==========

    if (parentChanging) {
      if (oldParentId) {
        var oldParentModel = that.getModelById(oldParentId);
        if (oldParentModel && oldParentModel.view && typeof oldParentModel.view.checkIsEmpty === 'function') {
          oldParentModel.view.checkIsEmpty();
        }
      }
      if (newParentId) {
        var newParentModel = that.getModelById(newParentId);
        if (newParentModel && newParentModel.view && typeof newParentModel.view.checkIsEmpty === 'function') {
          newParentModel.view.checkIsEmpty();
        }
      }
    }

    // ========== STEP 6: Trigger events ==========

    vc.events.trigger('shortcodes:update', draggedModel);
    vc.events.trigger('shortcodeView:updated', draggedModel);

    // ========== STEP 7: Reinitialize JavaScript in iframe ==========

    if (vc.frame_window && vc.frame_window.jQuery) {
      vc.frame_window.jQuery(vc.frame_window).trigger('vc_reload');
    }

    // ========== STEP 8: Update list view ==========

    setTimeout(function() {
      that.updateListView();
    }, 150);
  };


  function NectarWPBakerySettingsPosition() {

    this.state = {
      position: (localStorage.getItem("nectar_wpbakery_settings_pos")) ? localStorage.getItem("nectar_wpbakery_settings_pos") : 'modal'
    }

    this.$modal = $('.vc_ui-panel-window[data-vc-ui-element="panel-edit-element"]');
    this.setup();
    this.events();
  }

  NectarWPBakerySettingsPosition.prototype.setup = function() {
      var i18n = window.nectar_wpbakery_i18n || {};
      var sidebarTitle = i18n.sidebar_switch || 'Switch to Sidebar View';
      var modalTitle = i18n.modal_switch || 'Switch to Modal View';

      $('<span class="nectar-sidebar-switch" title="'+sidebarTitle+'"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="none" d="M0 0h24v24H0z"/><path d="M21 3a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h18zm-1 2H4v14h16V5zm-2 2v10h-2V7h2z"/></svg></span>')
      .insertBefore(this.$modal.find('.vc_ui-panel-header-controls [data-vc-ui-element="button-close"]'));

      $('<span class="nectar-modal-switch" title="'+modalTitle+'"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="none" d="M0 0h24v24H0z"/><path d="M21 3a1 1 0 0 1 1 1v7h-2V5H4v14h6v2H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h18zm0 10a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1h-8a1 1 0 0 1-1-1v-6a1 1 0 0 1 1-1h8zm-1 2h-6v4h6v-4zm-8.5-8L9.457 9.043l2.25 2.25-1.414 1.414-2.25-2.25L6 12.5V7h5.5z"/></svg>')
      .insertBefore(this.$modal.find('.vc_ui-panel-header-controls [data-vc-ui-element="button-close"]'));

      this.$sidebarSwitch = $('.vc_ui-panel-window[data-vc-ui-element="panel-edit-element"] .nectar-sidebar-switch');
      this.$modalSwitch = $('.vc_ui-panel-window[data-vc-ui-element="panel-edit-element"] .nectar-modal-switch');

    if( this.state.position == 'modal' ) {
      this.$sidebarSwitch.addClass('visible');
    } else {
      this.$modalSwitch.addClass('visible');
    }

  }

  NectarWPBakerySettingsPosition.prototype.events = function() {

    var that = this;

    this.$sidebarSwitch.on('click',this.togglePos.bind(this));
    this.$modalSwitch.on('click',this.togglePos.bind(this));

    // WPBakery modal.
    function callback(mutationsList, observer) {

      mutationsList.forEach(mutation => {
          if ( mutation.attributeName === 'class' && that.state.position == 'sidebar' ) {

            if( that.$modal.hasClass('vc_active') ) {
              that.updateLayout('sidebar');
            }
            else {
              that.updateLayout('modal');
            }
          }
      });
    }

    const mutationObserver = new MutationObserver(callback)

    mutationObserver.observe(this.$modal[0], { attributes: true });

  }

  NectarWPBakerySettingsPosition.prototype.togglePos = function(e) {

    this.$sidebarSwitch.toggleClass('visible');
    this.$modalSwitch.toggleClass('visible');

    var pos = ( this.state.position === 'sidebar' ) ? 'modal' : 'sidebar';

    localStorage.setItem("nectar_wpbakery_settings_pos", pos);
    this.state.position = pos;

    this.updateLayout(pos);
  };

  NectarWPBakerySettingsPosition.prototype.updateLayout = function(toggle) {
    if( toggle == 'sidebar' ) {
      document.querySelector("body").style.paddingRight = '350px';
      document.querySelector("body").classList.add('sidebar-settings-open');
      this.$modal[0].setAttribute('data-sidebar-view','true');
    }
    // Close.
    else {
      document.querySelector("body").style.paddingRight = '0px';
      document.querySelector("body").classList.remove('sidebar-settings-open');
      this.$modal[0].setAttribute('data-sidebar-view','false');
      this.$modal[0].style.left = '400px';
      this.$modal[0].style.width = '450px';
    }
  };


  $(document).ready(function(){
    new NectarWPBakeryListView();

    if( typeof(Storage) !== "undefined" ) {
      new NectarWPBakerySettingsPosition();
    }
  });


})(jQuery);