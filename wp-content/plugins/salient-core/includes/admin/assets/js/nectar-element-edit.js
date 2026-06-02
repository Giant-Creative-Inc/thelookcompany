(function($) {

  "use strict";

  var nectarAdminStore = {
    mouseX: 0,
    mouseUp: false,
    bindEvents: function() {
      $(window).on('mousemove',function(e) {
        nectarAdminStore.mouseX = e.clientX;
      });
      $(window).on('mouseup',function() {
        nectarAdminStore.mouseUp = true;
        $('.wpb_edit_form_elements .wpb_el_type_nectar_numerical')
          .removeClass('scrubbing')
          .removeClass('no-scrubbing');
      });
    },
    init: function() {
      this.bindEvents();
    }
  };


  /* CF Repeater */
  function NectarCFRepeater(el) {

    this.$el = el;
    this.valueInput   = this.$el.find('.nectar-repeater-field__save_data input');
    this.data         = [];
    this.saveData     = this.valueInput.val();
    this.itemTemplate = this.$el.find('.nectar-repeater-field__template').html();

    // Store reference to this instance on the element for color picker callbacks
    this.$el.data('nectar-repeater', this);

    this.buildItems()
    this.events();
  }

  NectarCFRepeater.prototype.events = function() {
    var that = this;

    // Add.
    this.$el.find('.nectar-repeater-field__add').on('click', function() {
      that.add();
      return false;
    });

    // Remove - use event delegation but scope to this specific repeater
    this.$el.on('click', '.nectar-repeater-field__item__remove', function() {
      $(this).parents('.nectar-repeater-field__item').remove();
      that.update();
      return false;
    });

        // Update.
    this.$el.on('change', ':input', this.update.bind(this));

    // Handle WordPress color picker changes specifically
    this.$el.on('change', 'input[name="color_value"]', this.update.bind(this));
    this.$el.on('input', 'input[name="color_value"]', this.update.bind(this));

    // Listen for WordPress color picker custom events
    this.$el.on('wp-color-change', 'input[name="color_value"]', this.update.bind(this));
    this.$el.on('wp-color-picker-change', 'input[name="color_value"]', this.update.bind(this));
  };

    NectarCFRepeater.prototype.add = function() {
    var $item = $(this.itemTemplate);
    this.$el.find('.nectar-repeater-field__items').append($item);
    nectarFancyCheckboxes();

    // Initialize color pickers for new color repeater fields
    this.initColorPickers($item);

  };

  NectarCFRepeater.prototype.update = function() {
    var data = [];
    this.$el.find('.nectar-repeater-field__items .nectar-repeater-field__item').each(function() {
      var formObject = {};

      // Handle color picker fields specially
      if ($(this).find('input[name="color_value"]').length > 0) {
        // For color picker fields, get the value from the color picker
        var colorInput = $(this).find('input[name="color_value"]');
        var colorValue = colorInput.val();

        // If it's a WordPress color picker, get the value from the color picker instance
        if (colorInput.hasClass('wp-color-picker') && colorInput.wpColorPicker) {
          colorValue = colorInput.wpColorPicker('color');
        }

        formObject.color_value = colorValue;

      } else {
        // For regular fields, use serializeArray
        var formData = $(this).find(':input').serializeArray();
        $.each(formData, function(_, kv) {
          formObject[kv.name] = kv.value;
        });
      }

      data.push(formObject);
    });

    this.valueInput.val(encodeURIComponent(JSON.stringify(data)));
  };


  NectarCFRepeater.prototype.buildItems = function() {
    var that = this;
    if ( !this.saveData ) {
      return;
    }

    var data = JSON.parse(decodeURIComponent(this.saveData));
    if( data.length > 0 ) {
      $.each(data, function(i, item) {
        var $item = $(that.itemTemplate);
        $item.find(':input').each(function() {
          var name = $(this).attr('name');
          $(this).val(item[name]);
          // set prop for checkbox
          if( $(this).is(':checkbox') ) {
            if( item.hasOwnProperty(name) ) {
              $(this).prop('checked', true);
              $(this).parents('.salient-fancy-checkbox').addClass('fancy-checkbox-activated');
            }
          }
        });
                that.$el.find('.nectar-repeater-field__items').append($item);

        // For color picker fields, set the color value after initialization
        if (that.$el.hasClass('nectar-color-repeater') && item.color_value) {
          // Wait for color picker to be initialized, then set the value
          setTimeout(function() {
            var colorInput = $item.find('input[name="color_value"]');
            if (colorInput.hasClass('wp-color-picker') && colorInput.wpColorPicker) {
              colorInput.wpColorPicker('color', item.color_value);
            } else {
              colorInput.val(item.color_value);
            }
          }, 100);
        }

        // Initialize color pickers for existing items
        that.initColorPickers($item);

      });
    }
  };

    NectarCFRepeater.prototype.initColorPickers = function($item) {

    // Check if this is a color repeater field
    if (this.$el.hasClass('nectar-color-repeater') ||
        this.$el.find('.nectar-repeater-field__template input[name="color_value"]').length > 0) {


      // Initialize color picker for color_value fields
      $item.find('input[name="color_value"]').each(function() {
        var repeaterInstance = this; // 'this' refers to the NectarCFRepeater instance

        if (typeof $.fn.wpColorPicker !== 'undefined') {
          // Add a small delay to ensure the DOM element is fully ready
          var $input = $(this);
          setTimeout(function() {
            try {

              var colorScheme = ['#27CCC0', '#f6653c', '#2ac4ea', '#ae81f9', '#FF4629', '#78cd6e'];
              if( window.nectar_theme_colors ) {
                window.nectar_theme_colors.forEach(function(element, index){
                  if(element.value) {
                    colorScheme[index] = element.value;
                  }
                });
              }

              $input.wpColorPicker({
                palettes: colorScheme,
                change: function(event, ui) {
                  // Trigger our update method using the stored reference
                  $input.closest('.nectar-repeater-field').data('nectar-repeater').update();
                }
              });
            } catch (e) {
              console.error('Error initializing color picker:', e);
            }
          }, 50);
        }
      });
    }
  };



  /* Global Sections */
  function SalientGlobalSections(el) {

    this.$el = el;
    this.$templates = this.$el.find('.templates');
    this.$hiddenInput = this.$el.find('input[name="id"][type="hidden"]');

    this.searchField();
    this.clickEvents();
  }

  SalientGlobalSections.prototype.clickEvents = function() {

    var that = this;

    this.$templates.find('.section').on('click',function() {
      var id = $(this).attr('data-id');
      that.$hiddenInput.val(id);

      that.$templates.find('.section').removeClass('active');
      $(this).addClass('active');

    });

  };


  SalientGlobalSections.prototype.searchField = function() {

    var that = this;

    this.$el.find('input[name="section_search"]').on('keyup', function() {

      var searchValue = $(this).val().toLowerCase();

      if( searchValue.length == 0 ) {
        that.$templates.find('.section').removeClass('hidden');
        return true;
      }

      that.$templates.find('.section').each(function() {

        var templateName = $(this).find('h4').text().toLowerCase();

        if( templateName.indexOf(searchValue) != -1 ) {
          $(this).removeClass('hidden');
        } else {
          $(this).addClass('hidden');
        }

      });
    });
  };


  /* Constrained Inputs */
  function ConstrainedInput(el) {

    this.$el = el;

    this.elements = [];
    this.$elements = '';
    this.className = false;
    this.active = false;

    this.createStyle();
    this.getInitialSet();
    this.trackActive();
    this.constrainEvents();

  }

  ConstrainedInput.prototype.createStyle = function() {
    this.$el.parents('.vc_checkbox-label').wrapInner('<div class="constrained-cb"></div>');
  }

  ConstrainedInput.prototype.getInitialSet = function() {

    var that = this;
    var classes = this.$el[0].className.split(/\s+/);

    // Store classname
    $.each(classes, function(i, name) {
      if (name.indexOf('constrain_group_') === 0 ) {
        that.className = name;
      }
    });

    // Store element set.
    $('.wpb_edit_form_elements .vc_wrapper-param-type-nectar_numerical[class*="constrain_group"]').each(function() {
      if( $(this).hasClass(that.className) ) {
        that.elements.push($(this).find('input.wpb_vc_param_value'));
      }
    });

    // Cache dom elements.
    that.$elements = $('.wpb_edit_form_elements').find('.' +that.className + ' input.wpb_vc_param_value');

  }

  ConstrainedInput.prototype.trackActive = function() {

    var that = this;

    this.active = this.$el.prop('checked');

    this.$el.on('change', function() {

      // Store state.
      that.active = $(this).prop('checked');

      // Alter icon.
      if( that.active == true ) {
        $(this).parents('.vc_checkbox-label').addClass('active');
      } else {
        $(this).parents('.vc_checkbox-label').removeClass('active');
      }

      // Trigger changes.
      if( that.elements.length > 0 ) {

        $.each(that.elements,function(i, element) {
          if( that.active == true ) {
            element.addClass('constrained');
          } else {
            element.removeClass('constrained');
          }
          element.trigger('keyup');
          element.trigger('change');
        });

      }

    });

    // Trigger on load.
    this.$el.trigger('change');

  }

  ConstrainedInput.prototype.constrainEvents = function() {

    if( this.className == false ) {
      return;
    }

    var that = this;

    // Bind event.
    $.each(this.elements, function(i, element) {

      element.on('change, keyup', function() {

        // Keep values in sync when constrain in active.
        if( that.active ) {
          var val = $(this).val();
          that.$elements.val(val).trigger('change');
        }

      });

    });

  };


  /* Numerical Inputs */
  function NectarNumericalInput(el) {

    this.$el = el;
    this.$scrubber = '';
    this.$scrubberIndicator = '';
    this.scrubberIndicatorX = 0;
    this.$editFormLine = el.parents('.edit_form_line');
    this.$placeHolder = el.parents('.edit_form_line').find('.placeholder');
    this.mouseDown = false;
    this.initialX = 0;
    this.calculatedVal = 0;
    this.scrubberCurrent = 0;
    this.currentVal = 0;
    this.divider = 3;
    this.zeroFloor = false;
    this.bottomFloor = 0;
    this.topCeil = 1000000;
    this.unit = '';

    if( el.is('[class*=padding]') || el.parents('.zero-floor').length > 0 ) {
      this.zeroFloor = true;
    }

    if( el.is('[class*=_intensity]') ) {
      //this.zeroFloor = true;
      this.divider = 30;
      this.topCeil = 5;
      this.zeroFloor = true;
      this.bottomFloor = -5;
    }

    this.createMarkup();
    this.trackActive();
    this.scrubbing();

  }

  NectarNumericalInput.prototype.createMarkup = function() {

    var $parent = this.$el.parent();

    if($parent.find('.vc_description').length > 0) {
      $('<span class="scrubber relative" />').insertBefore($parent.find('.vc_description'));
    } else {
      $parent.append('<span class="scrubber" />');
    }


    this.$scrubber = this.$el.parents('.edit_form_line').find('.scrubber');
    this.$scrubber.append('<span class="inner"/>');
    this.$scrubber.find('.inner').append('<span />');
    this.$scrubber.append('<i class="dashicons dashicons-arrow-left-alt2" />');
    this.$scrubber.append('<i class="dashicons dashicons-arrow-right-alt2" />');

    this.$scrubberIndicator = this.$scrubber.find('.inner span');
  };


  NectarNumericalInput.prototype.trackActive = function() {

    var that = this;

    // focus
    this.$el.on('focus', function() {
      that.$placeHolder.addClass('focus');
    });

    // change
    this.$el.on('change',function() {
      if( that.$el.val().length > 0 ) {
        that.$placeHolder.addClass('focus');
      } else {
        that.$placeHolder.removeClass('focus');
      }
    });

    // blur
    this.$el.on('blur',function() {
      if( that.$el.val().length == 0 ) {
        that.$placeHolder.removeClass('focus');
      }

      that.$el.trigger('change');
    });


  };

  NectarNumericalInput.prototype.getUnit = function() {

    if( this.currentVal.indexOf('%') != -1 ) {
      this.unit = '%';
    } else if( this.currentVal.indexOf('px') != -1 ) {
      this.unit = 'px';
    } else if( this.currentVal.indexOf('vw') != -1 ) {
      this.unit = 'vw';
    } else if( this.currentVal.indexOf('vh') != -1 ) {
      this.unit = 'vh';
    } else {
      this.unit = '';
    }

  };

  NectarNumericalInput.prototype.scrubbing = function() {

    var that = this;

    this.$scrubber.on('mousedown',function() {

      $('.wpb_el_type_nectar_numerical').addClass('no-scrubbing');
      that.$el.parents('.wpb_el_type_nectar_numerical').removeClass('no-scrubbing').addClass('scrubbing');

      // Track that the mouse is down / store initial
      that.mouseDown = true;
      nectarAdminStore.mouseUp = false;

      // Starting pos
      that.initialX = nectarAdminStore.mouseX;

      // Empty
      if( that.$el.val().length == 0 ) {

        this.scrubberCurrent = 0;
        that.currentVal = 0;
        that.unit = '';

      } else {

        that.currentVal = that.$el.val();

        if( that.$scrubberIndicator.css('transform') != 'none' ) {
          var transformMatrix = that.$scrubberIndicator.css('transform').replace(/[^0-9\-.,]/g, '').split(',');
          that.scrubberCurrent = transformMatrix[12] || transformMatrix[4];
        }

        if( isNaN( parseInt(that.currentVal) ) ) {
          that.currentVal = '0';
        }

        // Using units?
        that.getUnit();

      }

      // Change value RAF loop
      requestAnimationFrame(that.scrubbingAlter.bind(that));

    });


  };


  NectarNumericalInput.prototype.scrubbingAlter = function(e) {

    if( nectarAdminStore.mouseUp != true ) {
      requestAnimationFrame(this.scrubbingAlter.bind(this))
    }

    // Value

    //// Every x pixels moved, ++ or --
    this.calculatedVal = parseInt(this.currentVal) + parseInt(nectarAdminStore.mouseX - this.initialX)/this.divider;

    //// Who wants decimals??
    this.calculatedVal = Math.floor(this.calculatedVal);

    //// Stop number from going below 0
    if( this.zeroFloor && this.calculatedVal < this.bottomFloor) {

      this.$el.val(this.bottomFloor);
    } else {

      //// Ceil.
      if( this.calculatedVal > this.topCeil ) {
        this.$el.val(this.topCeil);
      } else {
        this.$el.val(this.calculatedVal + this.unit);

        // Indicator
        this.scrubberIndicatorX = linearInterpolate(this.scrubberIndicatorX, parseInt(this.scrubberCurrent) + parseInt(nectarAdminStore.mouseX - this.initialX)/4, 0.14);

        this.$scrubberIndicator.css({
          'transform': 'translate3d('+ this.scrubberIndicatorX +'px, 0px, 0px)'
        });
      }
    }

    this.$el.trigger('keyup');
    this.$el.trigger('focus');


  }





  function salientElementSettingsLoading() {

    var $modalContainer = $('div[data-vc-ui-element="panel-edit-element"] .vc_ui-panel-window-inner > .vc_ui-panel-content-container .vc_edit_form_elements');
    $('<div class="salient-element-settings-loading"></di>').insertAfter($modalContainer);

    var $loadingContainer = $modalContainer.parent().find('.salient-element-settings-loading');

    $loadingContainer.append('<div class="salient-element-loading"><i class="vc-composer-icon vc-c-icon-cog"></i></div>');

  }


  function createDeviceGroup($selector) {

    // Check if device group is already initialized
    if ($('.'+$selector+'-wrap').length > 0) {
      return; // Already initialized, skip
    }

    // Hide tabbed on load.
    $('body').find('.' + $selector + ':not(.desktop)').hide();

    var $title = $('body').find('.' + $selector).find('.group-title').clone();

    // Group Markup.
    $('body').find('.' + $selector).wrapAll('<div class="'+$selector+'-wrap nectar-device-group-wrap vc_column" />');

    // Header Markup.
    $('body').find('.' + $selector).find('.group-title').hide();
    $('.'+$selector+'-wrap').before('<div class="'+$selector+'-header nectar-device-group-header" />');



    var $header = $('.'+$selector+'-header');
    $header.append($title);
    $header.append('<span class="device-selection"><i class="dashicons-before dashicons-desktop active" data-filter="desktop" title="Desktop"></i> <i class="dashicons-before dashicons-tablet" data-filter="tablet" title="Tablet"></i> <i class="dashicons-before dashicons-smartphone" data-filter="phone" title="Phone"></i></span>');

  }


  /**
   * Auto-discovers and initializes device groups created via nectar_responsive_field().
   * Targets elements with the 'nectar-resp-field' marker class to avoid conflicts.
   */
  function autoInitDeviceGroups() {
    var discoveredGroups = {};

    // Find all elements with the marker class from nectar_responsive_field()
    $('.wpb_edit_form_elements .nectar-resp-field[class*="-device-group"]').each(function() {
      var classes = $(this).attr('class').split(/\s+/);

      classes.forEach(function(className) {
        // Match classes like "effect-enabled-device-group" but not "-wrap" or "-header" variants
        var match = className.match(/^(.+-device-group)$/);
        if (match && className.indexOf('-wrap') === -1 && className.indexOf('-header') === -1) {
          discoveredGroups[match[1]] = true;
        }
      });
    });

    // Initialize each discovered group
    Object.keys(discoveredGroups).forEach(function(groupName) {
      createDeviceGroup(groupName);
    });
  }


  function deviceHighlightInUse($input) {

    var $groupHeader = $input.parents('.nectar-device-group-wrap').prev('.nectar-device-group-header');
    var inUse        = false;
    var type         = 'select';


    if( $input.is('input[type="text"]') ) {
      type = 'text';
    } else if( $input.is('input[type="hidden"]') ) {
      type = 'hidden';
    }

    // Determine which icon is related
    var iconSelector = 'desktop';
    if( $input.parents('div[class*="vc_wrapper-param-type"].tablet').length > 0 ) {
       iconSelector = 'tablet';
    } else if( $input.parents('div[class*="vc_wrapper-param-type"].phone').length > 0 ) {
      iconSelector = 'phone';
    }

    $groupHeader.find('i[data-filter="'+iconSelector+'"]').removeClass('in-use');

    // Check each input in the group for value set.

    //// Text inputs.
    if( type == 'text' ) {
      $input.parents('.nectar-device-group-wrap').find('.'+iconSelector+' input[type="text"]').each(function(){
        if( $(this).parents('.vc_wrapper-param-type-textfield').length > 0 && $(this).val().length ) {
          inUse = true;
        } else if( $(this).parents('.vc_wrapper-param-type-nectar_numerical').length > 0 && $(this).val().length ) {
          inUse = true;
        }
      });
    }
    // Hidden inputs (images)
    else if( type == 'hidden' ) {
      $input.parents('.nectar-device-group-wrap').find('.'+iconSelector+' input[type="hidden"]').each(function(){
        if( $(this).parents('.vc_wrapper-param-type-fws_image').length > 0 && $(this).val().length ) {
          inUse = true;
        } else if( $(this).parents('.vc_wrapper-param-type-nectar_checkbox_tab_selection').length > 0 && $(this).val().length ) {
          inUse = true;
        }
      });
    }
    //// Selects.
    else {
      $input.parents('.nectar-device-group-wrap').find('.'+iconSelector+' select').each(function() {

        if( iconSelector != 'desktop' && $(this).parents('.vc_wrapper-param-type-dropdown').length > 0 && $(this).val().length ) {

          if( $(this).val() != 'inherit' && $(this).val() != 'default' ) {
            inUse = true;
          }

        }
        else if( iconSelector == 'desktop' && $(this).parents('.vc_wrapper-param-type-dropdown').length > 0 && $(this).val().length ) {

          if( $(this).val() != 'no-extra-padding' && $(this).val() != 'default' ) {
            inUse = true;
          }

        }

      });
    }

    // If using value in group, highlight icon.
    if (inUse == true ) {
      $groupHeader.find('i[data-filter="'+iconSelector+'"]').addClass('in-use');
    }

  }



  function deviceGroupEvents() {
    // Unbind existing events before rebinding to prevent duplicates
    $('.nectar-device-group-header i').off('click.nectar_device_groups');
    $('.nectar-device-group-wrap input[type="text"], .nectar-device-group-wrap select, .nectar-device-group-wrap input[type="hidden"]').off('change.nectar_device_groups');

    $('.nectar-device-group-header i').on('click.nectar_device_groups', function() {

      var filter = $(this).attr('data-filter');
      var group  = $(this).parents('.nectar-device-group-header').next('.nectar-device-group-wrap');
      // Already active.
      if( $(this).hasClass('active') ) {
        return;
      }

      // Active class.
      $(this).parents('.nectar-device-group-header').find('i').removeClass('active');
      $(this).addClass('active');

      // Display Grouping.
      group.find('> div').hide();
      group.find('> div.'+filter).fadeIn();

      // Trigger resize.
      if( group.find('.nectar_range_slider').length > 0 ) {
        $(window).trigger('resize');
      }

    });

    $('.nectar-device-group-header .device-selection i').each(function(){
      var $group = $(this).parents('.nectar-device-group-header').next('.nectar-device-group-wrap');

      // On change.
      $group.find('input[type="text"], select, input[type="hidden"]').on('change.nectar_device_groups',function(){
        deviceHighlightInUse($(this));
      });

      // Initial Load.
      $group.find('input[type="text"], select, input[type="hidden"]').each(function(){
        deviceHighlightInUse($(this));
      })

    });

  }



  function colorOverlayImageUpdate() {

    var $tab      = $('div[data-vc-shortcode-param-name="color_overlay"]').parents('.vc_edit-form-tab');
    var $BGimage  = $tab.parents('.wpb_edit_form_elements').find('div[data-vc-shortcode-param-name="bg_image"].wpb_el_type_fws_image');

    if( $BGimage.length == 0 ) {
      // Look for column BG img instead
      $BGimage = $tab.parents('.wpb_edit_form_elements').find('div[data-vc-shortcode-param-name="background_image"].wpb_el_type_fws_image');
    }
    var $colorPreview = $('.nectar-color-overlay-preview');

    if( $BGimage.find('img[src]').length > 0 ) {

      var src = $BGimage.find('img[src]').attr('src');
      // full size preview.
      if( src.indexOf('-150x150') != -1 ) {
        src = src.replace('-150x150.','.');
      }
      $colorPreview.find('span.wrap').css('background-image','url('+ src +')').addClass('using-img');
    } else {
      $colorPreview.find('span.wrap').css('background-image','').removeClass('using-img');
    }

  }



  function colorOverlayPreview(el) {
    // Unbind existing events before rebinding to prevent duplicates
    $('input[name="color_overlay"], input[name="color_overlay_2"]').off('change.nectar_color_overlay');
    $('select[name="gradient_direction"], select[name="color_layer_gradient_direction"], select[name="overlay_strength"], select[name="gradient_type"], select[name="color_layer_gradient_type"]').off('change.nectar_color_overlay');
    $('input[name="enable_gradient"]').off('change.nectar_color_overlay');
    $('input[class*="fws_image"]').off('change.nectar_color_overlay');
    $('input.wp-picker-clear').off('mousedown.nectar_color_overlay');
    $('input[type="range"][name="alpha"]').off('change.nectar_color_overlay');

    // Check if color preview already exists and remove it to prevent duplicates
    $('.nectar-color-overlay-preview').remove();

    // Markup.
    var $tab = $('div[data-vc-shortcode-param-name="color_overlay"]').parents('.vc_edit-form-tab');

    var $colorPreview = $('<div class="nectar-color-overlay-preview"></div>');
    var inputName     = ('row' === el) ? 'bg_image' : 'background_image';

    $colorPreview.append('<span class="wrap" />');
    $colorPreview.find('.wrap').append('<span />');

    if( el != 'general') {
      $colorPreview.insertAfter($('.col-md-6-last[data-vc-shortcode-param-name="color_overlay_2"]'));
    } else {
      //inputName = 'none';
      $colorPreview.find('.wrap').addClass('hide-icon')
      $colorPreview.insertBefore($('.generate-color-overlay-preview'));
    }


    // Events.
    $('input[name="color_overlay"]').on('change.nectar_color_overlay', colorOverlayPreviewUpdate);
    $('input[name="color_overlay_2"]').on('change.nectar_color_overlay', colorOverlayPreviewUpdate);
    $('select[name="gradient_direction"], select[name="color_layer_gradient_direction"]').on('change.nectar_color_overlay', colorOverlayPreviewUpdate);
    $('select[name="overlay_strength"], select[name="color_layer_gradient_direction"]').on('change.nectar_color_overlay', colorOverlayPreviewUpdate);
    $('select[name="gradient_type"] select[name="color_layer_gradient_type"]').on('change.nectar_color_overlay', colorOverlayPreviewUpdate);
    $('input[name="enable_gradient"]').on('change.nectar_color_overlay', colorOverlayPreviewUpdate);

    $('input[name="'+inputName+'"].'+inputName+'.fws_image').on('change.nectar_color_overlay', colorOverlayImageUpdate);

    setTimeout(function() {
      $('div[data-vc-shortcode-param-name="color_overlay"] input.wp-picker-clear').on('mousedown.nectar_color_overlay', colorOverlayPreviewUpdate);
      $('div[data-vc-shortcode-param-name="color_overlay_2"] input.wp-picker-clear').on('mousedown.nectar_color_overlay', colorOverlayPreviewUpdate);
      $('div[data-vc-shortcode-param-name="color_overlay"] input[type="range"][name="alpha"]').on('change.nectar_color_overlay', colorOverlayPreviewUpdate);
      $('div[data-vc-shortcode-param-name="color_overlay_2"] input[type="range"][name="alpha"]').on('change.nectar_color_overlay', colorOverlayPreviewUpdate);
    },2000);

    colorOverlayPreviewUpdate();
    colorOverlayImageUpdate();

  }

  function colorOverlayPreviewUpdate() {

    setTimeout(function(){

      var $color1  = $('input[name="color_overlay"]');
      var $color2  = $('input[name="color_overlay_2"]');
      var $useGrad = $('input#enable_gradient-true');
      var $gradDir = $('select[name="gradient_direction"]');
      var $opacity = $('select[name="overlay_strength"]');
      var $gradientType = $('input[name="gradient_type"]').val();

      if( $useGrad.length > 0 && $useGrad.prop('checked') &&
      $color1.length > 0 &&
      $color2.length > 0 &&
      $gradDir.length > 0 &&
      $gradientType != 'advanced') {

        var gradientDirectionDeg = '90deg';
        var $gradDirVal = $gradDir.val();

        switch( $gradDirVal ) {
          case 'left_to_right' :
            gradientDirectionDeg = '90deg';
            break;
          case 'left_t_to_right_b' :
            gradientDirectionDeg = '135deg';
            break;
          case 'left_b_to_right_t' :
            gradientDirectionDeg = '45deg';
            break;
          case 'top_to_bottom' :
            gradientDirectionDeg = 'to bottom';
            break;
        }

        var $color1Val = ( $color1.val().length > 0 ) ? $color1.val() : 'rgba(255,255,255,0.001)';
        var $color2Val = ( $color2.val().length > 0 ) ? $color2.val() : 'rgba(255,255,255,0.001)';

        if( $gradDirVal != 'radial' ) {
          $('.nectar-color-overlay-preview .wrap span').css('background', 'linear-gradient('+gradientDirectionDeg+', '+ $color1Val +', '+ $color2Val +')');
        }
        else {
          $('.nectar-color-overlay-preview .wrap span').css('background', 'radial-gradient(50% 50% at 50% 50%, '+ $color1Val +' 0%, '+ $color2Val +' 100%)');
        }

        $('.nectar-color-overlay-preview .wrap span').css('opacity', $opacity.val());


      } else if($gradientType != 'advanced') {
        $('.nectar-color-overlay-preview .wrap span').css({
          'background': '',
          'background-color': $color1.val()
        });
        $('.nectar-color-overlay-preview .wrap span').css('opacity', $opacity.val());

      }




    }, 150); // settimeout

  }


  function columnDeviceGroupHeaderToggles() {
    $('select[name="border_type"]').on('change', function() {
      if( 'simple' === $(this).val() ) {
        $('.column-border-device-group-header').hide();
      } else {
        $('.column-border-device-group-header').show();
      }
    }).trigger('change');

    // Mask param group device group header toggle
    $('input#mask_enable-true').on('change', function() {
      if( $(this).prop('checked') != true ) {
        $('.mask-alignment-device-group-header').hide();
      } else {
        $('.mask-alignment-device-group-header').show();
      }
    }).trigger('change');

    // column padding device group header toggle
    $('input[name="column_padding_type"]').on('change', function() {
      if( $(this).val() === 'advanced' ) {
        $('.column-padding-adv-device-group-header').show();
        $('.column-padding-device-group-header').hide();
      } else {
        $('.column-padding-adv-device-group-header').hide();
        $('.column-padding-device-group-header').show();
      }
    }).trigger('change');

  }


  function nectarClipPathDependency() {
    $('select[name="bg_image_animation"]').on('change', function() {
      if( 'clip-path' !== $(this).val() ) {
        $('.clip-path-device-group-header, .clip-path-end-device-group-header').hide();
      } else {
        $('.clip-path-device-group-header, .clip-path-end-device-group-header').show();
      }
    }).trigger('change');
  }


  function nectarCheckboxTabEvents() {
    $('body').on('change', '.nectar-radio-tab .n_radio_tab_val', function(e) {
      var $group = $(this).closest('.nectar-radio-tab');
      var $checkboxes = $group.find('.n_radio_tab_val');
      var min = parseInt($group.data('min-items'), 10);
      if (Number.isNaN(min)) min = 1;
      var max = parseInt($group.data('max-items'), 10);
      if (Number.isNaN(max)) max = $checkboxes.length;
      var allowEmpty = $group.find('.nectar_radio_tab_list').data('allow-empty') === true || $group.find('.nectar_radio_tab_list').data('allow-empty') === 'true';
      var checkedCount = $checkboxes.filter(':checked').length;

      // If checking and max would be exceeded, uncheck the first checked box (not this one)
      if ($(this).is(':checked') && checkedCount > max) {
        // Find all checked except the one just checked
        var $checked = $checkboxes.filter(':checked').not(this);
        if ($checked.length) {
          $checked.first().prop('checked', false).trigger('change');
        }
      }

      // If unchecking and min would be violated, revert
      if (!allowEmpty && !$(this).is(':checked') && checkedCount < min) {
        $(this).prop('checked', true);
        return;
      }

      // when unchecking, push empty value to hidden input
      // if (!$(this).is(':checked')) {
      //   $(this).parents('.wpb_el_type_nectar_checkbox_tab_selection').find('input[type="hidden"]').val('');
      // }

    });

    // On page load, ensure at least min are checked (optional, for safety)
    $('.nectar-radio-tab').each(function() {
      var $group = $(this);
      var $checkboxes = $group.find('.n_radio_tab_val');
      var min = parseInt($group.data('min-items'), 10) || 1;
      var checkedCount = $checkboxes.filter(':checked').length;
      if (checkedCount < min) {
        $checkboxes.slice(0, min).prop('checked', true);
      }
    });
  }

  function nectarFancyRadioEvents() {

    $("body").on('change','.n_radio_html_val',function(){

      var group_id = $(this).parents('.nectar-radio-html').data("grp-id");
      $("#nectar-radio-html-"+group_id).val($(this).val()).trigger('change');
      $('.nectar-radio-html-list li').removeClass('active');
      $(this).parents('li').addClass('active');

    });

    $("body").on('click','.nectar-radio-html-list li',function(){
      $(this).removeClass('clicked');
      setTimeout(() => {
        $(this).addClass('clicked');
      }, 100);

    });

  }

  function nectarFancyCheckboxes() {

    $('.vc_edit_form_elements .vc_shortcode-param.salient-fancy-checkbox:not(.constrain-icon) input[type="checkbox"].wpb_vc_param_value.checkbox').each(function(){

      // Return if already done.
      if ( $(this).parents('.nectar-cb-enabled').length > 0 ) {
        return;
      }

      if( $(this).prop('checked') ) {
        var $checkboxMarkup = $('<label class="cb-enable selected"><span>On</span></label><label class="cb-disable"><span>Off</span></label>');
      } else {
        var $checkboxMarkup = $('<label class="cb-enable"><span>On</span></label><label class="cb-disable selected"><span>Off</span></label>');
      }

      // Remove desc.
      var $parent = $(this).parent();
      var $checkbox = $(this).detach();

      $parent.empty();
      $parent.append($checkbox);

      $checkbox = $parent.find('input[type="checkbox"].wpb_vc_param_value.checkbox');

      // Create HTML.
      $checkbox.wrap('<div class="switch-options salient" />');
      $parent.find('.switch-options').prepend($checkboxMarkup);

      var $switchOptions = $checkbox.parents('.switch-options');

      if( $switchOptions.parent().is('.vc_checkbox-label') ) {
        $switchOptions.unwrap();
      }

      $switchOptions.wrap('<div class="nectar-cb-enabled" />');


    });


    // Start activated.
    $('.vc_edit_form_elements .switch-options.salient').each(function(){
      if( $(this).find('.cb-enable.selected').length > 0 ) {
        $(this).addClass( 'activated');
      }
    });


  }

  var graPickers = [];

  function NectarGradientColorPickerAngle(el) {

    this.$el = el;
    this.$input = el.find('input[type="number"]');
    this.value = this.$input.val();
    this.centerPointX = 0;
    this.centerPointY = 0;
    this.active = false;

    this.events();
    this.update();

  }

  NectarGradientColorPickerAngle.prototype.events = function() {

    var that = this;

    this.$el.find('.nectar-angle-selection-input').on('mousedown', function() {
      that.active = true;

      var rect = that.$el.find('.nectar-angle-selection-input').offset();
      that.centerPointX = rect.left + 15;
      that.centerPointY = rect.top + 15;
    });

    this.$input.on('change keyup',function(){
      that.value = $(this).val();
      that.update();
    });

    $('body').on('mouseup', function() {
      that.active = false;

    });

    $(window).on('mousemove',function(e) {

      if(that.active) {

        var angle = Math.atan2(e.pageY - that.centerPointY, e.pageX - that.centerPointX);

        angle = angle + 1.5; // alter by 90 deg to match mouse
        if (angle < 0) {
          angle += 2 * Math.PI;
        }
        that.value = Math.floor(angle * 180 / Math.PI);

        that.update();
      }
    });

  };

  NectarGradientColorPickerAngle.prototype.update = function() {
    var $gradientType = $('input[name="gradient_type"]').val();

    if( !this.value ) {
      this.value = '0';
      this.$input.val('');
    } else {
      this.$input.val(this.value);
    }


    this.$el.find('.inner').css('transform','rotate('+this.value+'deg)');
    if( graPickers[0] && $gradientType == 'advanced' || graPickers[0] && $('.generate-color-overlay-preview').length > 0 ) {
      graPickers[0].setDirection(this.value+'deg');
    }
  };



  function nectarGradientColorpicker() {

     // Grapick.
     if( $('.nectar-grapick-wrap').length > 0 ) {

      graPickers = [];


      $('.nectar-grapick-wrap').each(function(i){

        var id = $(this).attr('id');
        var input = $('.vc_shortcode-param input[type="hidden"][id="'+id+'"]');
        var savedColors = input.val();
        var savedDisplayType = $('input[name="advanced_gradient_display_type"]').val();
        var savedDir = ( savedDisplayType === 'radial' ) ? $('select[name="advanced_gradient_radial_position"]').val() : input.closest('.nectar_angle_selection').val();

        graPickers[i] = new Grapick({
          el: '.nectar-grapick-wrap',
          colorEl: '<input id="colorpicker"/>'
        });

        graPickers[i].setColorPicker(handler => {
          const el = handler.getEl().querySelector('#colorpicker');
          const $el = $(el);

          $el.spectrum({
              color: handler.getColor(),
              showAlpha: true,
              preferredFormat: "hex",
              showInput: true,
              change(color) {
                handler.setColor(color.toRgbString());
              },
              move(color) {
                handler.setColor(color.toRgbString(), 0);
              }
          });

          // return a function in order to destroy the custom color picker
          return () => {
            $el.spectrum('destroy');
          }
        });

        graPickers[i].on('change', function(complete) {
          var value = graPickers[i].getValue();
          var colorVal = graPickers[i].getColorValue();

          var bgPreviewEl = $('.nectar-color-overlay-preview span span');

          if(bgPreviewEl.length > 0) {
            $('.nectar-color-overlay-preview span span')[0].style.backgroundColor = '';
            $('.nectar-color-overlay-preview span span')[0].style.backgroundImage = value;
            $('.nectar-color-overlay-preview span span')[0].style.opacity = '1';
          }

          /* Dont save the default */
          if( colorVal != '#f3f3f3 10%, #f3f3f3 10%' &&
              colorVal != '#f3f3f3 10%, #f3f3f3 90%' ) {
            input.val(value);
          }

        });

        if( savedColors.length > 0 ) {
          graPickers[i].setValue(savedColors);
          if(savedDir) {
            graPickers[i].setDirection(savedDir);
          }
          if(savedDisplayType) {
            graPickers[i].setType(savedDisplayType);
          }

        } else {
          graPickers[i].addHandler(10, '#f3f3f3');
          graPickers[i].addHandler(90, '#f3f3f3');
        }

        graPickers[i].emit('change');

      });



      // Gradient type.
      $('input[name="advanced_gradient_display_type"]').on('change', function(){
        var val = $(this).val();
        for( var i=0; i<graPickers.length; i++) {
          graPickers[i].setType(val);

        }
      });

      // Radial direction
      // Note: Grapick needed a modification to support top left/right + bottom left/right
      $('select[name="advanced_gradient_radial_position"]').on('change', function(){

        if( $('input[name="advanced_gradient_display_type"]').val() == 'radial') {
          var val = $(this).val();
          for( var i=0; i<graPickers.length; i++) {
            graPickers[i].setDirection(val);
          }
        }
      });

      $('input[name="gradient_type"]').on('change', function(val){
        var val = $(this).val();
        if(val == 'advanced') {
          for( var i=0; i<graPickers.length; i++) {
            graPickers[i].emit('change');
          }
        }
      });

      if( $('input[name="gradient_type"]').val() == 'advanced' || $('.generate-color-overlay-preview').length > 0 ) {
        setTimeout(function(){
          $('input[name="advanced_gradient_display_type"], select[name="advanced_gradient_radial_position"]').trigger('change');
        },300);

      }

    }

      // Angles
      $('.nectar-angle-selection-wrap').each(function(i){
        new NectarGradientColorPickerAngle($(this));
      });
  }


  function nectarBoxShadowGeneratorInit() {
    $('div.nectar-box-shadow-generator').each(function(){
      new NectarBoxShadowGenerator($(this));
    });
  }

  function NectarBoxShadowGenerator(el) {
    this.el = el;
    this.input = el.find('.wpb_vc_param_value');
    this.state = {
      'horizontal': 0,
      'vertical': 0,
      'blur': 0,
      'spread': 0,
      'opacity': 0,
    };

    this.events();
  }

  NectarBoxShadowGenerator.prototype.events = function() {
    this.el.find('input.nectar-range-slider').on('change', this.calculateValue.bind(this));
  };

  NectarBoxShadowGenerator.prototype.calculateValue = function() {

    var that = this;

    this.el.find('.nectar-range-slider').each(function(){

      var name = $(this).attr('name');
      that.state[name] = $(this).val();
    });

    this.input.val(this.parseToShortcodeAttr(this.state));
  };

  NectarBoxShadowGenerator.prototype.parseToShortcodeAttr = function() {

    var that = this;
    var string = '';

    Object.keys(this.state).forEach(function(key) {
      string += key + ':' + that.state[key] + ',';
    });

    string = string.slice(0, -1);
    return string;

  }



  function nectarRadioTabEvents() {
    // Unbind existing events before rebinding to prevent duplicates
    $("body").off('change.nectar_radio_tabs', '.vc_edit_form_elements .wpb_el_type_nectar_radio_tab_selection .n_radio_tab_val');
    $("body").off('change.nectar_radio_tabs', '.vc_edit_form_elements .wpb_el_type_nectar_checkbox_tab_selection .n_radio_tab_val');

    // Radio tab selection.
    $("body").on('change.nectar_radio_tabs','.vc_edit_form_elements .wpb_el_type_nectar_radio_tab_selection .n_radio_tab_val',function(){

      var group_id = $(this).parents('.nectar-radio-tab').data("grp-id");

      $("#nectar-radio-tab-"+group_id).val($(this).val()).trigger('change');
      // dependent fields.
      nectarRadioDependentFields();
    });

    // Checkbox tab selection.
    $("body").on('change.nectar_radio_tabs','.vc_edit_form_elements .wpb_el_type_nectar_checkbox_tab_selection .n_radio_tab_val',function(){

      var group_id = $(this).parents('.nectar-radio-tab').data("grp-id");
      // account for empty values when unchecking.
      if ($(this).prop('checked')) {
        $("#nectar-radio-tab-"+group_id).val($(this).val()).trigger('change');
      } else {
        $("#nectar-radio-tab-"+group_id).val('').trigger('change');
      }

      // dependent fields.
      nectarRadioDependentFields();
    });

    // Simulate save_always..
    $('.vc_edit_form_elements .wpb_el_type_nectar_radio_tab_selection .edit_form_line > input[type="hidden"], .wpb_el_type_nectar_checkbox_tab_selection .edit_form_line > input[type="hidden"]').each(function(){

      if( $(this).val().length == 0 && $(this).attr('data-default-val') ) {
        $(this).val($(this).attr('data-default-val')).trigger('change');
      }

    });

    // dependent fields.
    nectarRadioDependentFields();

  }

  function nectarRadioDependentFields() {

    $('.vc_edit_form_elements .radio_tab_dep').each(function(){

      var classes = Array.from($(this)[0].classList);
      var depClass = classes.find(function(item){
        if( item.indexOf('dep--') > -1 ) {
          return true;
        }
        return false;
      });

      if( depClass ) {
        var depClassVal = depClass.split("--").pop();
        var depField = depClass.split("--")[1];

        if( $('.vc_edit_form_elements').find('input[name="'+depField+'"]').length > 0 ) {
          var depValue = $('.vc_edit_form_elements').find('input[name="'+depField+'"]').val();

          if( depClassVal !== depValue ) {
            $(this).addClass('dep-hidden');
          } else {
            $(this).removeClass('dep-hidden');
          }
        }

      }

    });

    $(window).trigger('nectar_radio_tab_dependent_fields');
  }

  function nectarRangeSliders() {

    /* Single Range */
    var textContent = ('textContent' in document) ? 'textContent' : 'innerText';

    function valueOutput(element) {
      var value = element.value;
      var output = $(element).parent().siblings('.output');
      output = output.find('.number')[0];

      output[textContent] = value;
    }

    $('input[type="range"].nectar-range-slider').rangeslider({
      polyfill: false,
      onInit: function() {
        valueOutput(this.$element[0]);
      },
      onSlideEnd: function(position, value) {
        this.$element.val(value);
        var min = this.$element.attr('min');
        var max = this.$element.attr('max');

        // fix overflow on slider
        if( value > (max * 0.6)) {
          $(window).trigger('resize');
        }

      }
    });

    $('body').on('input', '.nectar-range-slider', function(e) {
      valueOutput(e.target);
    });



    /* Multi Range */
    $('.nectar-multi-range-slider').each(function(){

      var slider = $(this)[0];
      var sliderInput = $(this).find('.wpb_vc_param_value');

      // Check if slider is already initialized and destroy it
      if (slider.noUiSlider) {
        slider.noUiSlider.destroy();
      }

      var startingValue = sliderInput.val().indexOf(',') > -1 ? sliderInput.val().split(',') : [0,100];
      var min = parseInt(sliderInput.attr('data-min'));
      var max = parseInt(sliderInput.attr('data-max'));

      noUiSlider.create(slider, {
        start: startingValue,
        connect: true,
        tooltips: [wNumb({decimals: 0}), wNumb({decimals: 0})],
        step: 1,
        range: {
            'min': min,
            'max': max
        }
      });

      // Set value to input for saving.
      slider.noUiSlider.on('update', function (values, handle) {
          sliderInput.val(slider.noUiSlider.get().join(','));
      });

    });


  }

  function nectarFancyCheckboxEvents() {
    // Unbind existing events before rebinding to prevent duplicates
    $('body').off('click.nectar_fancy_checkboxes', '.vc_edit_form_elements .switch-options.salient .cb-enable');
    $('body').off('click.nectar_fancy_checkboxes', '.vc_edit_form_elements .switch-options.salient .cb-disable');

    // Click events.
    $('body').on('click.nectar_fancy_checkboxes', '.vc_edit_form_elements .switch-options.salient .cb-enable' ,function() {

      var parent = $( this ).parents( '.switch-options' );

      $( '.cb-disable', parent ).removeClass( 'selected' );
      $( this ).addClass( 'selected' );

      $(this).parent().addClass( 'activated');
      $(this).parents('.salient-fancy-checkbox').addClass( 'fancy-checkbox-activated' );

      $( 'input[type="checkbox"]', parent ).prop("checked", true).trigger('change');

    });

    $('body').on('click.nectar_fancy_checkboxes', '.vc_edit_form_elements .switch-options.salient .cb-disable' ,function() {

      var parent = $( this ).parents( '.switch-options' );

      $( '.cb-enable', parent ).removeClass( 'selected' );
      $( this ).addClass( 'selected' );
      $(this).parent().removeClass( 'activated');
      $(this).parents('.salient-fancy-checkbox').removeClass( 'fancy-checkbox-activated' );

      $( 'input[type="checkbox"]', parent ).prop("checked", false).trigger('change');

    });

  }


  function simpleSliderFields() {

    if( vc && vc.shortcodes && vc.shortcodes.models ) {

      $.each(vc.shortcodes.models, function(i, el) {

        if( el.attributes && el.attributes.shortcode && el.attributes.shortcode === 'carousel' ) {

          if( el.attributes.params && el.attributes.params.script && el.attributes.params.script === 'simple_slider') {
            $('.vc_edit_form_elements .simple_slider_specific_field').show().addClass('nectar-show-element');
          }
          else if( el.attributes.params && el.attributes.params.script && el.attributes.params.script === 'flickity') {
            $('.vc_edit_form_elements .flickity_specific_field').show().addClass('nectar-show-element');
          }

        }
      });
    }

  }

  function NectarLottiePreview(el) {
    this.$el = el;
    this.$input = this.$el.find('input');
    this.rendered = false;
    this.events();
  }

  NectarLottiePreview.prototype.events = function() {
    var that = this;
    this.$input.on('change', function(){
      that.source = $(this).val();

      if( that.source.length === 0 ) {
        that.$el.find('.nectar-lottie-preview-render').hide();
      } else {

        if( !that.source.endsWith('.json') ) {
          that.source = '';
        } else {
          that.$el.find('.nectar-lottie-preview-render').show();
        }

      }

      that.init();

    }).trigger('change');

     // error.
     this.player.addEventListener("error", (e) => {
      that.$el.find('.error').show();
    });

    this.player.addEventListener("play", (e) => {
      that.$el.find('.error').hide();
    });

  };

  NectarLottiePreview.prototype.init = function() {

    var that = this;

    this.player = this.$el.find("lottie-player")[0];

    // Changing sources
    if( this.rendered && this.source.length > 0) {
      this.player.load(this.source);
    }

    // Initial load.
    this.player.addEventListener("rendered", (e) => {
      if( this.source.length > 0 ) {
        that.player.load(this.source);
      }

      that.rendered = true;
    });

    setTimeout(function(){
      that.player.dispatchEvent(new Event('rendered', { bubbles: true }));
    }, 500);


  };

    function nectarFlexboxLayout() {
    // Unbind existing events before rebinding to prevent duplicates
    $('body').off('change.nectar_flexbox', '.wpb_vc_param_value[name*="flex_layout_"]');
    $('body').off('click.nectar_flexbox', '.flexbox-layout-device-group-header i');
    $(window).off('nectar_radio_tab_dependent_fields.nectar_flexbox');

    $('body').on('change.nectar_flexbox', '.wpb_vc_param_value[name*="flex_layout_"]', function() {
      var selectedViewport = $('.flexbox-layout-device-group-header').find('i.active');
      var activeDevice = selectedViewport.length > 0 ? selectedViewport.attr('data-filter') : 'desktop';
      var layoutType = $(this).val(); // 'row' or 'column'
      swapFlexIcons(layoutType, activeDevice);
    });
    $('body').on('click.nectar_flexbox', '.flexbox-layout-device-group-header i', function() {
      // When switching devices, use the fallback logic to determine layout direction
      setTimeout(function() {
        setupFlexboxIconDirection();
      }, 50);
    });
    setTimeout(function() {
      setupFlexboxIconDirection();
    }, 200);

    $(window).on('nectar_radio_tab_dependent_fields.nectar_flexbox', function() {
      setupFlexboxIconDirection();
    });
  }

  function setupFlexboxIconDirection() {
    var selectedViewport = $('.flexbox-layout-device-group-header').find('i.active');
    var filter = selectedViewport.length > 0 ? selectedViewport.attr('data-filter') : 'desktop';

    // Get the layout direction, with fallback logic
    var layoutDirection = getFlexboxLayoutDirection(filter);
    swapFlexIcons(layoutDirection, filter);
  }

  function getFlexboxLayoutDirection(activeDevice) {
    // Hierarchy: phone -> tablet -> desktop
    var $layoutHeader = $('.flexbox-layout-device-group-header');
    var deviceHierarchy = ['desktop', 'tablet', 'phone'];
    var startIndex = deviceHierarchy.indexOf(activeDevice);

    // Start from the active device and work backwards to desktop
    for (var i = startIndex; i >= 0; i--) {
      var device = deviceHierarchy[i];
      var $icon = $layoutHeader.find('i[data-filter="'+device+'"]');

      // Check if this device has a value set (in-use class)
      if ($icon.hasClass('in-use') || device === 'desktop') {
        // Get the layout value for this device
        var $layoutInput = $('.wpb_vc_param_value[name*="flex_layout_'+device+'"]');
        if ($layoutInput.length > 0 && $layoutInput.val()) {
          return $layoutInput.val(); // 'row' or 'column'
        }
      }
    }

    // Default fallback to 'row' if no value found
    return 'row';
  }

  function swapFlexIcons(layoutType, activeDevice) {
    // Only swap icons for the currently active viewport's controls
    $('.vc_edit-form-tab').find('div[class*="flexbox"][class*="device-group-wrap"]').each(function() {
      var $deviceGroup = $(this);

      // Only process controls for the active viewport
      $deviceGroup.find('> div.'+activeDevice+' img').each(function() {
        var $img = $(this);
        if(layoutType === 'row') {
          // Set to horizontal icon
          $img.attr('src', $img.data('horizontal-src'));
        } else {
          // Set to vertical icon
          $img.attr('src', $img.data('vertical-src'));
        }
      });
    });
  }


  function videoAttachFields() {

    $(".wpb_edit_form_elements .nectar-add-media-btn").on('click', function(e) {

      e.preventDefault();

      var $that = $(this);
      var custom_file_frame = null;

      custom_file_frame = wp.media.frames.customHeader = wp.media({
        title: $(this).data("choose"),
        library: {
          type: 'video'
        },
        button: {
          text: $(this).data("update")
        }
      });

      custom_file_frame.on( "select", function() {

        var file_attachment = custom_file_frame.state().get("selection").first();

        $('.wpb_edit_form_elements #' + $that.attr('rel-id') ).val(file_attachment.attributes.url).trigger('change');

        $that.parent().find('.nectar-add-media-btn').css('display','none');
        $that.parent().find('.nectar-remove-media-btn').css('display','inline-block');

      });

      custom_file_frame.open();

    });


    $(".wpb_edit_form_elements .nectar-remove-media-btn").on('click', function(e) {

      e.preventDefault();

      $('.wpb_edit_form_elements #' + $(this).attr('rel-id')).val('');
      $(this).prev().css('display','inline-block');
      $(this).css('display','none');

    });

  }



  function studioSortByDate(a, b) {

    a = parseFloat($(a).attr("data-date"));
    b = parseFloat($(b).attr("data-date"));

    return a > b ? 1 : -1;

  };

  function studioSortByAlphabetical(a, b) {

    a = $(a).find('.vc_ui-list-bar-item-trigger').text();
    b = $(b).find('.vc_ui-list-bar-item-trigger').text();

    return a < b ? 1 : -1;

  };

  function salientStudioSorting() {

    var $container = $(".vc_templates-list-default_templates");

    // Create Markup.
    var $selectEl = $('<select id="salient-studio-sorting"></select>');
    $selectEl.append('<option value="alphabetical">'+nectar_translations.alphabetical+'</option>');
    $selectEl.append('<option value="date">'+nectar_translations.date+'</option>');

    $('div[data-vc-ui-element="panel-templates"] .library_categories').prepend('<div class="library-sorting" />');
    $('div[data-vc-ui-element="panel-templates"] .library-sorting').prepend($selectEl);
    $('div[data-vc-ui-element="panel-templates"] .library-sorting').prepend('<label for="salient-studio-sorting">'+nectar_translations.sortby+'</label>');


    // Events.
    $('body').on('change','select#salient-studio-sorting', function() {

      var $items = $(".vc_templates-list-default_templates > div");

      // Convert Date to Standard JS Format.
      if( !$(".vc_templates-list-default_templates > div:first-child").is('data-date')) {
        $items.each(function() {

          var dateClass = this.className.match(/(date\-[^\s]*)/);
          if(dateClass && typeof dateClass[0] != 'undefined' ){
              var date = dateClass[0].replace('date-','');

              var formattedDate = date.split("-");
              var standardDate = formattedDate[1]+" "+formattedDate[0]+" "+formattedDate[2];
              standardDate = new Date(standardDate).getTime();
              $(this).attr("data-date", standardDate);
          } else {
            $(this).attr("data-date", '1000');
          }

        });
      }

      // Sort
      var val = $(this).val();

      if(val === 'date') {

        $items.sort(studioSortByDate).each(function(){
            $container.prepend(this);
        });

      } else if( val === 'alphabetical' ) {

        $items.sort(studioSortByAlphabetical).each(function(){
            $container.prepend(this);
        });

      }

    });


  }


  function linearInterpolate(a, b, n) {
    return (1 - n) * a + n * b;
  }

  /* Updates custom post type in post grid for tax search */
  window.nectarPostGridCustomQueryTaxCallBack = function() {
      var $filterByPostType, $taxonomies, autocomplete, defaultValue;
      if ($filterByPostType = $(".wpb_vc_param_value[name=cpt_name]", this.$content), defaultValue = $filterByPostType.val(), $taxonomies = $(".wpb_vc_param_value[name=custom_query_tax]", this.$content), void 0 === (autocomplete = $taxonomies.data("vc-param-object"))) return !1;

      $filterByPostType.on("change", function() {

          var $this = $(this);
          defaultValue !== $this.val() && autocomplete.clearValue(), autocomplete.source_data = function() {
              return {
                  vc_filter_post_type: $filterByPostType.val()
              }
          }
      }).trigger("change");
  }

  window.nectarPostGridFeaturedFirstItemCallback = function() {

    var $featuredTopItem = $(".wpb_vc_param_value[name=featured_top_item]", this.$content);

    // Hides masory options if featured top item is checked.
    $featuredTopItem.on("change", function() {

      if( $featuredTopItem.prop('checked') == true ) {
        $(".vc_shortcode-param[data-vc-shortcode-param-name=enable_masonry], .vc_shortcode-param[data-vc-shortcode-param-name*=col_masonry_layout]", this.$content).hide();
      } else {
        $(".vc_shortcode-param[data-vc-shortcode-param-name=enable_masonry], .vc_shortcode-param[data-vc-shortcode-param-name*=col_masonry_layout]", this.$content).show();
      }

    }).trigger('change');

    // Should not be available when using 1 col layout.


  };

  window.nectarOutlineAppliesToCallback = function() {
    var elStyle = $(".wpb_vc_param_value[name=style]", this.$content);
    var elRepeatDivider = $(".wpb_vc_param_value[name=text_repeat_divider]", this.$content);
    var elOutlineAppliesTo = $('[data-vc-shortcode-param-name="outline_applies_to"]', this.$content);

    elStyle.on("change", function() {
      if ( elStyle.val() === 'text_outline' && elRepeatDivider.val() === 'custom') {
        elOutlineAppliesTo.removeClass('hidden-element');
      } else {
        elOutlineAppliesTo.addClass('hidden-element');
      }
    }).trigger('change');

    elRepeatDivider.on("change", function() {
      if ( elStyle.val() === 'text_outline' && elRepeatDivider.val() === 'custom') {
        elOutlineAppliesTo.removeClass('hidden-element');
      } else {
        elOutlineAppliesTo.addClass('hidden-element');
      }
    }).trigger('change');

  };


  window.nectarPostGridDisplayTypeCallback = function() {

    var $displayType = $(".wpb_vc_param_value[name=display_type]", this.$content);
    var $gridStyle = $(".wpb_vc_param_value[name=grid_style]", this.$content);

    var dependentSelectors = [
      'enable_masonry',
      'featured_top_item',
      'enable_indicator',
      'mouse_indicator_style',
      'mouse_indicator_color',
      'mouse_indicator_text',
      'mouse_indicator_text_color',
      'mouse_indicator_blurred_bg',
      '2_col_masonry_layout',
      '3_col_masonry_layout',
      '4_col_masonry_layout',
    ];

    var stackHiddenSelectors = [
      'enable_masonry',
      'featured_top_item',
      '2_col_masonry_layout',
      '3_col_masonry_layout',
      '4_col_masonry_layout',
      'parallax_scrolling'
    ];

    $displayType.on("change", function() {

      var that = this;
      var gridStyleField = $(".vc_shortcode-param[data-vc-shortcode-param-name=grid_style] select", this.$content);

      // grid
      if( $displayType.val() === 'grid' ) {

        dependentSelectors.forEach(function(el){
          $(".vc_shortcode-param[data-vc-shortcode-param-name="+el+"]", that.$content).removeClass('hidden-element');
        });
        $('select[name=grid_style] option.mouse_follow_image', that.$content).prop('disabled', false);
        $('select[name=grid_style] option.vertical_list', that.$content).prop('disabled', false);
        $('select[name=grid_style] option.content_next_to_image', that.$content).prop('disabled', false);
        $('select[name=grid_style] option.content_under_image', that.$content).prop('disabled', false);


        $(".column-device-group-header", that.$content).removeClass('hidden-device-group-element');
        $(".column-device-group-header", that.$content).removeClass('hidden-device-group-element-full');

      }
      else if( $displayType.val() === 'carousel' ) {

        stackHiddenSelectors.forEach(function(el){
          $(".vc_shortcode-param[data-vc-shortcode-param-name="+el+"]", that.$content).removeClass('hidden-element');
        });

        // carousel
        dependentSelectors.forEach(function(el){
          $(".vc_shortcode-param[data-vc-shortcode-param-name="+el+"]", that.$content).addClass('hidden-element');
        });

        // Hide device options.
        $(".column-device-group-header", that.$content).removeClass('hidden-device-group-element-full');
        $(".column-device-group-header", that.$content).addClass('hidden-device-group-element');
        $('.column-device-group-header .device-selection [data-filter="desktop"]', that.$content).trigger('click');

        // Limit styles
        if( gridStyleField.val() !== 'content_under_image' &&
            gridStyleField.val() !== 'content_overlaid' ) {
          $('select[name=grid_style]').val('content_overlaid');
          $('select[name=grid_style]').trigger('change');
        }
        $('select[name=grid_style] option.mouse_follow_image', that.$content).prop('disabled', true);
        $('select[name=grid_style] option.vertical_list', that.$content).prop('disabled', true);
        $('select[name=grid_style] option.content_next_to_image', that.$content).prop('disabled', true);
        $('select[name=grid_style] option.content_under_image', that.$content).prop('disabled', false);


    } else if ( $displayType.val() === 'stack' ) {

      setTimeout(() => {
        stackHiddenSelectors.forEach(function(el){
          $(".vc_shortcode-param[data-vc-shortcode-param-name="+el+"]", that.$content).addClass('hidden-element');
        });
        // Hide device options.
        $(".column-device-group-header", that.$content).addClass('hidden-device-group-element-full');
        $('.column-device-group-header .device-selection [data-filter="desktop"]', that.$content).trigger('click');

         // Limit styles
         if( gridStyleField.val() !== 'content_overlaid' ) {
          $('select[name=grid_style]').val('content_overlaid');
          $('select[name=grid_style]').trigger('change');
        }
        $('select[name=grid_style] option.mouse_follow_image', that.$content).prop('disabled', true);
        $('select[name=grid_style] option.vertical_list', that.$content).prop('disabled', true);
        $('select[name=grid_style] option.content_next_to_image', that.$content).prop('disabled', true);
        $('select[name=grid_style] option.content_under_image', that.$content).prop('disabled', true);

      }, 10);

    }

  }).trigger('change');

  // Hide carousel option for certain styles
  $gridStyle.on("change", function() {

      var $displayType = $(".wpb_vc_param_value[name=display_type]", this.$content);

      if ($(this).val() !== 'content_under_image' && $(this).val() !== 'content_overlaid') {
        $displayType.val('grid').parents('.vc_shortcode-param').hide();

      } else {
        $displayType.parents('.vc_shortcode-param').show();
      }

       // Limit masonry variatns for certain styles
      if ($(this).val() !== 'content_under_image' && $(this).val() !== 'content_overlaid') {
        $('.vc_shortcode-param[data-vc-shortcode-param-name=4_col_masonry_layout]', this.$content).addClass('hidden-element');
        $('.vc_shortcode-param[data-vc-shortcode-param-name=3_col_masonry_layout]', this.$content).addClass('hidden-element');
        $(".wpb_vc_param_value[name=featured_top_item]", this.$content).prop('checked', false);
        $('.vc_shortcode-param[data-vc-shortcode-param-name=featured_top_item]', this.$content).addClass('hidden-element');
      } else {
        if ( $displayType.val() !== 'carousel' ) {
          $('.vc_shortcode-param[data-vc-shortcode-param-name=4_col_masonry_layout]', this.$content).removeClass('hidden-element');
          $('.vc_shortcode-param[data-vc-shortcode-param-name=3_col_masonry_layout]', this.$content).removeClass('hidden-element');
          $('.vc_shortcode-param[data-vc-shortcode-param-name=featured_top_item]', this.$content).removeClass('hidden-element');
        }
      }


  }).trigger('change');



}

  window.nectarPostGridTextColorDependency = function() {

    var $container = this.$content || $('#vc_ui-panel-edit-element');

    if (!$container || !$container.length) {
      return;
    }

    var $gridStyle = $(".wpb_vc_param_value[name=grid_style]", $container);

    if (!$gridStyle.length) {
      return;
    }

    var $textColor = $(".wpb_vc_param_value[name=text_color]", $container);
    var $textColorHover = $(".wpb_vc_param_value[name=text_color_hover]", $container);
    var hiddenClass = 'nectar-text-color-grid-hidden';

    var fieldConfig = {
      'light_text_color': {
        styles: ['content_overlaid','content_under_image','content_next_to_image','mouse_follow_image'],
        valueField: $textColor,
        value: 'light'
      },
      'dark_text_color': {
        styles: ['content_overlaid','content_under_image','content_next_to_image','mouse_follow_image'],
        valueField: $textColor,
        value: 'dark'
      },
      'light_text_color_hover': {
        styles: ['content_overlaid'],
        valueField: $textColorHover,
        value: 'light'
      },
      'dark_text_color_hover': {
        styles: ['content_overlaid'],
        valueField: $textColorHover,
        value: 'dark'
      }
    };

    var toggleField = function(paramName, shouldShow) {
      var $param = $('.vc_shortcode-param[data-vc-shortcode-param-name="'+paramName+'"]', $container);

      if (!$param.length) {
        return;
      }

      if (shouldShow) {
        if ($param.hasClass(hiddenClass)) {
          $param.removeClass(hiddenClass + ' hidden-element');
        }
      } else {
        if (!$param.hasClass(hiddenClass)) {
          $param.addClass(hiddenClass);
        }
        $param.addClass('hidden-element');
      }
    };

    var runUpdate = function() {
      $.each(fieldConfig, function(paramName, config) {
        var gridMatches = config.styles.indexOf($gridStyle.val()) > -1;
        var valueMatches = true;

        if (config.valueField && config.valueField.length && typeof config.value !== 'undefined') {
          valueMatches = config.valueField.val() === config.value;
        }

        toggleField(paramName, gridMatches && valueMatches);
      });
    };

    $gridStyle.off('change.nectarPostGridTextColor').on('change.nectarPostGridTextColor', runUpdate);

    if ($textColor.length) {
      $textColor.off('change.nectarPostGridTextColor').on('change.nectarPostGridTextColor', runUpdate);
    }

    if ($textColorHover.length) {
      $textColorHover.off('change.nectarPostGridTextColor').on('change.nectarPostGridTextColor', runUpdate);
    }

    runUpdate();

  };

  window.nectarRecentPostsFontSizeCallback = function() {

    var $style = $(".wpb_vc_param_value[name=style]", this.$content);

    $style.on("change", function() {

      if( $style.val() === 'multiple_large_featured' ||
          $style.val() === 'single_large_featured' ) {
        $('.font-size-device-group-header, .font-size-device-group-wrap, [data-vc-shortcode-param-name="font_size_min"], [data-vc-shortcode-param-name="font_line_height"], [data-vc-shortcode-param-name="font_size_max"], [data-vc-shortcode-param-name="font_text_indent"]').removeClass('hidden-element');
      } else {
        $('.font-size-device-group-header, .font-size-device-group-wrap, [data-vc-shortcode-param-name="font_size_min"], [data-vc-shortcode-param-name="font_line_height"], [data-vc-shortcode-param-name="font_size_max"], [data-vc-shortcode-param-name="font_text_indent"]').addClass('hidden-element');
      }
    }).trigger('change');
    setTimeout(function(){
      $style.trigger('change');
    }, 60);
  };

  window.nectarFitTextCallback = function() {
    var $fitText = $(".wpb_vc_param_value[name=fit_text_to_container]", this.$content);

    $fitText.on("change", function() {


      if( $fitText.prop('checked') == true ) {
        $('.font-size-device-group-header, .font-size-device-group-wrap, [data-vc-shortcode-param-name="font_size_min"], [data-vc-shortcode-param-name="font_size_max"]').addClass('hidden-element');
      } else {
        $('.font-size-device-group-header, .font-size-device-group-wrap, [data-vc-shortcode-param-name="font_size_min"], [data-vc-shortcode-param-name="font_size_max"]').removeClass('hidden-element');
      }
    }).trigger('change');
    setTimeout(function(){
      $fitText.trigger('change');
    }, 60);
  };


  window.nectarSecondaryProjectImgCallback = function() {

    var $post_type = $(".wpb_vc_param_value[name=post_type]", this.$content);
    var $grid_style = $(".wpb_vc_param_value[name=grid_style]", this.$content);
    var $overlay_secondary_image = $(".wpb_vc_param_value[name=overlay_secondary_project_image]", this.$content);

    $post_type.on("change", function() {

      if( $post_type.val() === 'portfolio' && $grid_style.val() === 'content_under_image') {
        $('.vc_edit_form_elements .custom-portfolio-dep').show();
      }
      else {
        $('.vc_edit_form_elements .custom-portfolio-dep').hide();
        $overlay_secondary_image.prop('checked', false);
        var $switchOptions = $overlay_secondary_image.parents('.switch-options.salient');
        $switchOptions.removeClass('activated');
        $switchOptions.find('.cb-enable').removeClass('selected');
      }

    }).trigger('change');

    $grid_style.on("change", function() {
      if( $post_type.val() === 'portfolio' && $grid_style.val() === 'content_under_image') {
        $('.vc_edit_form_elements .custom-portfolio-dep').show();
      }
      else {
        $('.vc_edit_form_elements .custom-portfolio-dep').hide();
        $overlay_secondary_image.prop('checked', false);
        var $switchOptions = $overlay_secondary_image.parents('.switch-options.salient');
        $switchOptions.removeClass('activated');
        $switchOptions.find('.cb-enable').removeClass('selected');
      }
    }).trigger('change');


  };


  function elSettingsPostitionRefresh() {

    /* Entering the backend editor when using the sidebar el setting position on front-end */
    if( 'admin_frontend_editor' !== window.vc_mode &&
         typeof(Storage) !== "undefined" &&
         typeof(window.setUserSetting) !== "undefined" ) {

          var frontEndSettingsLayout = (localStorage.getItem("nectar_wpbakery_settings_pos")) ? localStorage.getItem("nectar_wpbakery_settings_pos") : 'modal';

          if( frontEndSettingsLayout == 'sidebar' ) {
            window.setUserSetting('edit_element_vcUIPanelWidth','565');
            window.setUserSetting('edit_element_vcUIPanelLeft',Math.floor(($(window).width() - 565)/2) + 'px');
            window.setUserSetting('edit_element_vcUIPanelTop','150px');
          }

    }

  }

  function createFlexboxDeviceGroups() {
    createDeviceGroup('flexbox-layout-device-group');
    createDeviceGroup('flexbox-justify-content-device-group');
    createDeviceGroup('flexbox-align-items-device-group');
    createDeviceGroup('flexbox-direction-device-group');
    createDeviceGroup('flexbox-gap-device-group');
    createDeviceGroup('flexbox-wrap-device-group');
    createDeviceGroup('flexbox-reverse-device-group');
  }

  jQuery(document).ready(function($) {

    nectarAdminStore.init();

    var constrainedInputs     = [],
        nectarNumericalInputs = [];

    const isFullScreenRowsComposeModeActive = function() {
      const backendToggleActive = $('._nectar_full_screen_rows label[for="nectar_meta_on"].ui-state-active').length > 0;
      const frontendEditorActive =
        typeof window.vc_mode !== 'undefined' &&
        window.vc_mode === 'admin_frontend_editor' &&
        typeof window.vc !== 'undefined' &&
        window.vc &&
        window.vc.frame_window &&
        window.vc.frame_window.jQuery &&
        window.vc.frame_window.jQuery('body').hasClass('nectar_pfsr_compose_mode');

      return backendToggleActive || frontendEditorActive;
    };

    const maybeHideRowSpacingControls = function(shouldDisable) {
      const $rowSpacingGroups = $('.row-padding-device-group.col-md-6, .row-margin-device-group.col-md-6');
      const $rowSpacingForms = $('.wpb_edit_form_elements .row-padding-device-group, .wpb_edit_form_elements .row-margin-device-group');

      if (shouldDisable) {
        $rowSpacingGroups.hide();
        $rowSpacingForms.addClass('fullscreen-rows-disabled');
      }
    };

    elSettingsPostitionRefresh();

    // On modal open.
    $("#vc_ui-panel-edit-element").on('vcPanel.shown',function() {

      var $shortcode = ( $('#vc_ui-panel-edit-element[data-vc-shortcode]').length > 0 ) ? $('#vc_ui-panel-edit-element').attr('data-vc-shortcode') : '';

      const fullScreenRowsActive = isFullScreenRowsComposeModeActive();


      // Section.
      if( 'vc_section' === $shortcode ) {
        createDeviceGroup('height-device-group');
        createDeviceGroup('color-overlay-device-group');
        createDeviceGroup('column-bg-img-device-group');
        createDeviceGroup('row-padding-device-group');
        createDeviceGroup('row-margin-device-group');
        createFlexboxDeviceGroups();
      }

      // Row.
      if( 'vc_row' === $shortcode ) {

        // Device Groups
        if ( ! fullScreenRowsActive ) {
          createDeviceGroup('row-padding-device-group');
          createDeviceGroup('row-margin-device-group');
        }

        createDeviceGroup('row-transform-device-group');
        createDeviceGroup('column-direction-device-group');
        createDeviceGroup('shape-divider-device-group');
        createDeviceGroup('row-bg-img-device-group');
        createDeviceGroup('clip-path-device-group');
        createDeviceGroup('clip-path-end-device-group');
        createDeviceGroup('device-visibility-device-group');
        nectarClipPathDependency();

        colorOverlayPreview('row');

        createDeviceGroup('height-device-group');

        createDeviceGroup('row-position-display-device-group');

        // Must be after all other device groups are created.
        maybeHideRowSpacingControls(fullScreenRowsActive);
      } // endif row el.



      // Inner Row.
      if( 'vc_row_inner' === $shortcode ) {

          createDeviceGroup('row-position-display-device-group');
          createDeviceGroup('row-position-device-group');
          createDeviceGroup('row-padding-device-group');
          createDeviceGroup('row-transform-device-group');
          createDeviceGroup('row-min-width-device-group');
          createDeviceGroup('row-max-width-device-group');
          createDeviceGroup('column-direction-device-group');

          createDeviceGroup('height-device-group');
          createDeviceGroup('device-visibility-device-group');

      }


      // Column.
      if( 'vc_column' === $shortcode || 'vc_column_inner' === $shortcode ) {

        createDeviceGroup('column-transform-device-group');
        createDeviceGroup('column-padding-device-group');
        createDeviceGroup('column-margin-device-group');
        createDeviceGroup('column-border-device-group');
        createDeviceGroup('column-bg-img-device-group');
        createDeviceGroup('mask-alignment-device-group');
        createDeviceGroup('column-padding-adv-device-group');
        createDeviceGroup('column-el-direction-device-group');
        createDeviceGroup('column-text-align-device-group');
        createDeviceGroup('flexbox-layout-device-group');
        createDeviceGroup('flexbox-justify-content-device-group');
        createDeviceGroup('flexbox-align-items-device-group');
        createDeviceGroup('flexbox-direction-device-group');
        createDeviceGroup('flexbox-gap-device-group');
        createDeviceGroup('flexbox-wrap-device-group');
        createDeviceGroup('flexbox-reverse-device-group');
        createDeviceGroup('height-device-group');
        columnDeviceGroupHeaderToggles();

        if( 'vc_column' === $shortcode ) {
          createDeviceGroup('column-max-width-device-group');
        }

        colorOverlayPreview('column');
      }



      if( 'vc_column' !== $shortcode &&
          'vc_column_inner' !== $shortcode &&
          'vc_row' !== $shortcode ) {
          colorOverlayPreview('general');
      }

      if( 'nectar_global_section' === $shortcode ) {
        new SalientGlobalSections($('#vc_ui-panel-edit-element .wpb_el_type_nectar_global_section_select .edit_form_line'));
      }

      if( 'image_with_animation' === $shortcode ) {
        createDeviceGroup('image-margin-device-group');
        createDeviceGroup('image-custom-width-device-group');
        createDeviceGroup('position-display-device-group');
        createDeviceGroup('position-device-group');
        createDeviceGroup('transform-device-group');
        createDeviceGroup('mask-alignment-device-group');

        $('[data-vc-shortcode="image_with_animation"] [data-vc-shortcode-param-name="max_width"] select[name="max_width"]').on('change', function(){
          if($(this).val() != 'custom') {
            $('.image-custom-width-device-group-header').hide();
          } else {
            $('.image-custom-width-device-group-header').show();
          }
        }).trigger('change');

      }

            if( 'nectar_content_trail' === $shortcode ) {
        createDeviceGroup('position-device-group');
        createDeviceGroup('position-display-device-group');
        createDeviceGroup('transform-device-group');
        createDeviceGroup('size-device-group');
        createDeviceGroup('image-width-device-group');
      }

      if( 'nectar_icon' === $shortcode ) {
        createDeviceGroup('position-device-group');
        createDeviceGroup('position-display-device-group');
        createDeviceGroup('transform-device-group');
      }

      if( 'nectar_project_categories' === $shortcode ) {
        createDeviceGroup('alignment-device-group');
      }

      if( 'nectar_cta' === $shortcode ) {
        createDeviceGroup('alignment-device-group');
        createDeviceGroup('display-device-group');
        createDeviceGroup('font-size-device-group');
        createDeviceGroup('font-text-indent-device-group');
        createDeviceGroup('position-display-device-group');
        createDeviceGroup('position-device-group');
        createDeviceGroup('transform-device-group');
      }

      if( 'divider' === $shortcode ) {
        createDeviceGroup('divider-height-device-group');
      }

      if( 'split_line_heading' === $shortcode ||
          'testimonial_slider' === $shortcode ) {
        createDeviceGroup('font-size-device-group');
        createDeviceGroup('font-text-indent-device-group');
      }

      if( 'nectar_text_inline_images' === $shortcode ) {
        createDeviceGroup('margin-device-group');
        createDeviceGroup('font-size-device-group');
        createDeviceGroup('font-text-indent-device-group');
      }

      if( 'fancy_box' === $shortcode ) {
        createDeviceGroup('fancybox-min-height-device-group');
      }

      if('fancy-ul' === $shortcode) {
        createDeviceGroup('font-size-device-group');
        createDeviceGroup('font-text-indent-device-group');
      }

      if( 'item' === $shortcode ) {
        simpleSliderFields();
        createFlexboxDeviceGroups();
      }

      if( 'nectar_sticky_media_sections' === $shortcode ) {
        createDeviceGroup('max-height-device-group');
        createDeviceGroup('layered-card-reveal-width-device-group');
        createDeviceGroup('overlap-amount-device-group');
      }

      if( 'nectar_video_player_self_hosted' === $shortcode ) {
        createDeviceGroup('video-aspect-ratio-device-group');
      }

      if( 'nectar_lottie' === $shortcode ) {
        createDeviceGroup('lottie-dimensions-device-group');
        createDeviceGroup('position-device-group');
        createDeviceGroup('position-display-device-group');
        createDeviceGroup('transform-device-group');
      }

      if( 'nectar_circle_images' === $shortcode ) {
        createDeviceGroup('circle-images-alignment-device-group');
      }

      if( 'nectar_badge' === $shortcode ) {
        createDeviceGroup('position-device-group');
        createDeviceGroup('position-display-device-group');
        createDeviceGroup('transform-device-group');
        createDeviceGroup('font-size-device-group');
        createDeviceGroup('font-text-indent-device-group');
      }

      if( 'nectar_highlighted_text' === $shortcode ) {
        createDeviceGroup('font-size-device-group');
        createDeviceGroup('font-text-indent-device-group');
      }

      if( 'nectar_price_typography' === $shortcode ) {
        createDeviceGroup('font-size-device-group');
        createDeviceGroup('font-text-indent-device-group');
      }

      if ( 'nectar_post_grid' === $shortcode ) {
        createDeviceGroup('font-size-device-group');
        createDeviceGroup('font-text-indent-device-group');
        createDeviceGroup('padding-device-group');
        createDeviceGroup('column-device-group');
      }



      if( 'recent_posts' === $shortcode ) {
        createDeviceGroup('font-size-device-group');
        createDeviceGroup('font-text-indent-device-group');
      }

      if( 'nectar_responsive_text' === $shortcode ) {
        createDeviceGroup('font-size-device-group');
        createDeviceGroup('font-text-indent-device-group');
      }

      if( 'nectar_icon_list_item' === $shortcode ) {
        createDeviceGroup('font-size-device-group');
        createDeviceGroup('font-text-indent-device-group');
      }


      if ('nectar_animated_shape' === $shortcode) {
        createDeviceGroup('dimensions-device-group');
        createDeviceGroup('position-device-group');
        createDeviceGroup('transform-device-group');
        createDeviceGroup('position-display-device-group');
      }

      if ('nectar_lottie' === $shortcode) {

        $('.vc_edit_form_elements .nectar-lottie-preview').each(function(){
          new NectarLottiePreview($(this));
        });

      }

      // Initialize all repeater fields for any shortcode (must be after all shortcode-specific code)
      var repeaterFields = $('.vc_edit_form_elements .nectar-repeater-field');

      repeaterFields.each(function(index){

        new NectarCFRepeater($(this));
      });




      // Device Group Events.
      if( 'vc_column' === $shortcode ||
      'vc_column_inner' === $shortcode ||
      'vc_row_inner' === $shortcode ||
      'vc_row' === $shortcode ||
      'vc_section' === $shortcode ||
      'image_with_animation' === $shortcode ||
      'divider' === $shortcode ||
      'fancy_box' === $shortcode ||
      'nectar_cta' === $shortcode ||
      'nectar_content_trail' === $shortcode ||
      'split_line_heading' === $shortcode ||
      'nectar_text_inline_images' === $shortcode ||
      'testimonial_slider' === $shortcode ||
      'nectar_video_player_self_hosted' === $shortcode ||
      'nectar_icon' === $shortcode ||
      'nectar_project_categories' === $shortcode ||
      'nectar_lottie' === $shortcode ||
      'nectar_animated_shape' === $shortcode ||
      'nectar_price_typography' === $shortcode ||
      'nectar_responsive_text' === $shortcode ||
      'nectar_icon_list_item' === $shortcode ||
      'nectar_badge' === $shortcode ||
      'nectar_highlighted_text' === $shortcode ||
      'fancy-ul' === $shortcode ||
      'nectar_circle_images' === $shortcode ||
      'nectar_post_grid' === $shortcode ||
      'recent_posts' === $shortcode ||
      'nectar_flexbox' === $shortcode ||
      'nectar_sticky_media_sections' === $shortcode ||
      'item' === $shortcode ) {
        deviceGroupEvents();
      }

      // Radio tabs
      nectarRadioTabEvents();

      // Fancy checkboxes.
      nectarFancyCheckboxes();

      // Gradient Colorpickers.
      nectarGradientColorpicker();

      // Range sliders.
      nectarRangeSliders();

      // Box Shadow Generators.
      nectarBoxShadowGeneratorInit();

      // Video field.
      videoAttachFields();

      // Flexbox layout.
      nectarFlexboxLayout();

      // Constrained values.
      $('.wpb_edit_form_elements input[type="checkbox"][class*="constrain_group_"]').each(function(i) {
        constrainedInputs[i] = new ConstrainedInput($(this));
      });

      // Number Scrubber.
      $('input[type="text"].nectar-numerical').each(function(){
        nectarNumericalInputs = new NectarNumericalInput($(this));
      });

      // Auto-initialize any new device groups and bind events.
      autoInitDeviceGroups();
      deviceGroupEvents();


    }); // Modal open end.

    // Modal loading markup.
    salientElementSettingsLoading();

    // Salient Studio Template Sorting
    salientStudioSorting();

    // Fancy checkbox events.
    nectarFancyCheckboxEvents();

    // Custom radio parms
    nectarFancyRadioEvents();

    // Checkbox tab events.
    nectarCheckboxTabEvents();

    // Dynamic el styling - front end page builder
    $(window).on('load', function() {

      if( typeof window.vc_mode !== 'undefined' && 'admin_frontend_editor' === window.vc_mode ) {

        $(window).on('nectar_wpbakery_el_save nectar_wpbakery_template_add', function() {

          var page_content = window.vc.builder.getContent();

          if( page_content.length > 0 ) {

            $.ajax({
              type: 'POST',
              url: window.ajaxurl,
              data: {
                'action': 'nectar_frontend_builder_generate_styles',
                '_vcnonce': window.vcAdminNonce,
                'nectar_page_content': page_content
              },
              success: function(response) {

                var style = document.createElement('style');

        				style.type = 'text/css';
                style.setAttribute('id','salient-el-dynamic-ajax');
        				if (style.styleSheet) {
        					style.styleSheet.cssText = response;
        				} else {
        					style.appendChild(document.createTextNode(response));
        				}

                // Clean up previous styles.
                var dynamicCSSLength = window.vc.frame_window.jQuery("body").find('style[id="salient-el-dynamic-ajax"]').length;

                if( dynamicCSSLength > 2 ) {
                  window.vc.frame_window.jQuery("body").find('style[id="salient-el-dynamic-ajax"]:first').remove();
                }

                window.vc.frame_window.jQuery("body").append(style);

                setTimeout(function() {
                  window.vc.frame_window.jQuery("body").trigger("vc_reload");
                  window.vc.frame_window.jQuery("body").trigger("resize");
                }, 10);

              } // success

            }); //ajax

          }

        });

        $('body').on('mouseup','.vc_templates-template-type-default_templates button.vc_ui-list-bar-item-trigger',function(){

          // When adding studio template, also regenerate the dynamic css
          setTimeout(function() {
            $(window).trigger('nectar_wpbakery_el_save');
          },1600);

        });


      } // on front end editor

    }); // end dynamic el styling

  });

})(jQuery);
