( function ( $, window ) {
	'use strict';

	var strings = window.spsA11yStrings || {};
	var prefersReducedMotion = window.matchMedia
		? window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches
		: false;

	function getString( key, fallback ) {
		return strings[ key ] || fallback;
	}

	function getSlideTitle( $slide ) {
		var $title = $slide.find( '.project-slide__title' ).first();
		return $title.length ? $.trim( $title.text() ) : '';
	}

	function getSlideCount( $slider ) {
		return $slider.find( '.project-slide' ).length;
	}

	function getCurrentSlideIndex( $slider ) {
		var $current = $slider.find( '.project-slide.current' ).first();
		return $current.length ? $current.index() : 0;
	}

	function syncAriaHidden( $slider ) {
		$slider.find( '.project-slide' ).each( function () {
			var $slide = $( this );
			var isCurrent = $slide.hasClass( 'current' );
			$slide.attr( 'aria-hidden', isCurrent ? 'false' : 'true' );
		} );
	}

	function announceSlide( $slider ) {
		var $live = $slider.find( '.sps-carousel-live' ).first();

		if ( ! $live.length ) {
			return;
		}

		var $current = $slider.find( '.project-slide.current' ).first();
		var index = getCurrentSlideIndex( $slider ) + 1;
		var total = getSlideCount( $slider );
		var title = getSlideTitle( $current );
		var message = getString( 'slideOf', 'Slide %1$d of %2$d' )
			.replace( '%1$d', index )
			.replace( '%2$d', total );

		if ( title ) {
			message += ': ' + title;
		}

		$live.text( message );
	}

	function enhanceDots( $slider ) {
		var $dotNav = $slider.find( '> .normal-container > .dot-nav' ).first();

		if ( ! $dotNav.length ) {
			return;
		}

		var total = $dotNav.find( '> span.dot' ).length;

		$dotNav.attr( 'role', 'tablist' );

		$dotNav.find( '> span.dot' ).each( function ( dotIndex ) {
			var $dot = $( this );
			var slideNumber = dotIndex + 1;
			var label = getString( 'goToSlide', 'Go to slide %1$d of %2$d' )
				.replace( '%1$d', slideNumber )
				.replace( '%2$d', total );

			$dot.attr( {
				role: 'tab',
				'aria-label': label,
				'aria-selected': $dot.hasClass( 'active' ) ? 'true' : 'false',
				tabindex: $dot.hasClass( 'active' ) ? '0' : '-1',
			} );

			$dot.find( '> span' ).attr( 'aria-hidden', 'true' );

			if ( $dot.data( 'spsDotA11yBound' ) ) {
				return;
			}

			$dot.data( 'spsDotA11yBound', true );

			$dot.on( 'keydown.spsA11y', function ( event ) {
				var $dots = $dotNav.find( '> span.dot' );
				var currentIndex = $dots.index( $dot );
				var targetIndex = currentIndex;
				var shouldTrigger = false;

				switch ( event.key ) {
					case 'ArrowRight':
					case 'ArrowDown':
						targetIndex = ( currentIndex + 1 ) % $dots.length;
						shouldTrigger = true;
						break;
					case 'ArrowLeft':
					case 'ArrowUp':
						targetIndex = ( currentIndex - 1 + $dots.length ) % $dots.length;
						shouldTrigger = true;
						break;
					case 'Home':
						targetIndex = 0;
						shouldTrigger = true;
						break;
					case 'End':
						targetIndex = $dots.length - 1;
						shouldTrigger = true;
						break;
					case 'Enter':
					case ' ':
						shouldTrigger = true;
						break;
					default:
						return;
				}

				event.preventDefault();

				if ( shouldTrigger ) {
					$dots.eq( targetIndex ).trigger( 'click' ).focus();
				}
			} );
		} );
	}

	function syncDots( $slider ) {
		var $dotNav = $slider.find( '> .normal-container > .dot-nav' ).first();

		if ( ! $dotNav.length ) {
			return;
		}

		var $dots = $dotNav.find( '> span.dot' );

		$dots.each( function () {
			var $dot = $( this );
			var isActive = $dot.hasClass( 'active' );

			$dot.attr( {
				'aria-selected': isActive ? 'true' : 'false',
				tabindex: isActive ? '0' : '-1',
			} );
		} );
	}

	function afterSlideChange( $slider ) {
		window.setTimeout( function () {
			syncAriaHidden( $slider );
			syncDots( $slider );
			announceSlide( $slider );
		}, 60 );
	}

	function bindSlideChangeHandlers( $slider ) {
		if ( $slider.data( 'spsSlideChangeBound' ) ) {
			return;
		}

		$slider.data( 'spsSlideChangeBound', true );

		$slider.on(
			'click.spsA11y',
			'.zoom-slider-controls .prev, .zoom-slider-controls .next, .dot-nav > span.dot',
			function () {
				afterSlideChange( $slider );
			}
		);
	}

	function setupRegionKeyboard( $slider ) {
		if ( $slider.data( 'spsRegionKeyBound' ) ) {
			return;
		}

		$slider.data( 'spsRegionKeyBound', true );

		$slider.on( 'keydown.spsA11y', function ( event ) {
			var tagName = event.target && event.target.tagName ? event.target.tagName.toLowerCase() : '';
			var isInteractive = tagName === 'a' || tagName === 'button' || tagName === 'input' || tagName === 'textarea';

			if ( isInteractive ) {
				return;
			}

			if ( event.key === 'ArrowLeft' ) {
				event.preventDefault();
				$slider.find( '.zoom-slider-controls .prev' ).first().trigger( 'click' );
			} else if ( event.key === 'ArrowRight' ) {
				event.preventDefault();
				$slider.find( '.zoom-slider-controls .next' ).first().trigger( 'click' );
			}
		} );
	}

	function setPauseUi( $pause, paused ) {
		var label = paused
			? getString( 'play', 'Play slideshow' )
			: getString( 'pause', 'Pause slideshow' );

		$pause.attr( {
			'aria-pressed': paused ? 'true' : 'false',
			'aria-label': label,
		} );
		$pause.find( '.screen-reader-text' ).text( label );
	}

	function getFsInstance( $slider, instance ) {
		return instance || $slider.data( 'spsFsInstance' );
	}

	function pauseInstance( instance ) {
		if ( ! instance ) {
			return;
		}

		if ( typeof instance._spsOriginalRotationSpeed === 'undefined' ) {
			instance._spsOriginalRotationSpeed = instance.rotationSpeed;
		}

		instance.rotationSpeed = 0;

		if ( instance.rotationInterval ) {
			window.clearInterval( instance.rotationInterval );
			instance.rotationInterval = null;
		}
	}

	function playInstance( instance ) {
		if ( ! instance ) {
			return;
		}

		if ( typeof instance._spsOriginalRotationSpeed !== 'undefined' ) {
			instance.rotationSpeed = instance._spsOriginalRotationSpeed;
		}

		if ( typeof instance.sliderResetRotate === 'function' ) {
			instance.sliderResetRotate();
		}
	}

	function setupPause( $slider, instance ) {
		var $pause = $slider.find( '.sps-carousel-pause' ).first();

		if ( ! $pause.length ) {
			return;
		}

		var fsInstance = getFsInstance( $slider, instance );

		if ( prefersReducedMotion && ! $pause.data( 'spsReducedMotionApplied' ) && fsInstance ) {
			pauseInstance( fsInstance );
			setPauseUi( $pause, true );
			$pause.data( 'spsReducedMotionApplied', true );
			$slider.data( 'spsFsInstance', fsInstance );
		}

		if ( $pause.data( 'spsPauseBound' ) ) {
			return;
		}

		$pause.data( 'spsPauseBound', true );

		$pause.on( 'click.spsA11y', function ( event ) {
			event.preventDefault();

			var liveInstance = $slider.data( 'spsFsInstance' );

			if ( ! liveInstance ) {
				return;
			}

			var isPaused = $pause.attr( 'aria-pressed' ) === 'true';

			if ( isPaused ) {
				playInstance( liveInstance );
				setPauseUi( $pause, false );
			} else {
				pauseInstance( liveInstance );
				setPauseUi( $pause, true );
			}
		} );
	}

	function needsDots( $slider ) {
		var controls = $slider.attr( 'data-slider-controls' ) || '';
		return controls === 'both' || controls === 'pagination';
	}

	function isReady( $slider ) {
		if ( getSlideCount( $slider ) <= 1 ) {
			return true;
		}

		if ( needsDots( $slider ) && ! $slider.find( '> .normal-container > .dot-nav' ).length ) {
			return false;
		}

		return true;
	}

	function enhance( $slider, instance ) {
		if ( ! $slider || ! $slider.length ) {
			return;
		}

		if ( instance ) {
			$slider.data( 'spsFsInstance', instance );
		}

		if ( $slider.data( 'sps-a11y-enhanced' ) ) {
			if ( prefersReducedMotion ) {
				setupPause( $slider, $slider.data( 'spsFsInstance' ) );
			}
			return;
		}

		if ( ! isReady( $slider ) ) {
			return;
		}

		$slider.data( 'sps-a11y-enhanced', true );

		syncAriaHidden( $slider );
		enhanceDots( $slider );
		bindSlideChangeHandlers( $slider );
		setupRegionKeyboard( $slider );
		setupPause( $slider, $slider.data( 'spsFsInstance' ) );
		announceSlide( $slider );
	}

	function tryEnhanceAll() {
		$( '.nectar_fullscreen_zoom_recent_projects' ).each( function () {
			var $slider = $( this );
			enhance( $slider, $slider.data( 'spsFsInstance' ) );
		} );
	}

	window.SalientPortfolioSliderA11y = {
		enhance: enhance,
		tryEnhanceAll: tryEnhanceAll,
	};

	$( function () {
		tryEnhanceAll();
		window.setTimeout( tryEnhanceAll, 350 );
		window.setTimeout( tryEnhanceAll, 800 );
	} );
}( jQuery, window ) );
