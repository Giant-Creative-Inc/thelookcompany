/* global SCSL */
jQuery( function( $ ) {
	$( '[data-scsl]' ).each( function() {
		var $root    = $( this );
		var $loader  = $root.find( '[data-scsl-loader]' );
		var $status  = $root.find( '[data-scsl-status]' );
		var $results = $root.find( '[data-scsl-results]' );

		var activeMarket  = 0;
		var isLoading     = false;
		var responseCache = {};

		function setLoading( on ) {
			isLoading = !! on;
			$status.attr( 'aria-busy', on ? 'true' : 'false' );
			$loader.prop( 'hidden', ! on );
		}

		function setActiveMarket( termId ) {
			activeMarket = parseInt( termId, 10 ) || 0;

			$root.find( '[data-scsl-market]' ).each( function() {
				var $btn   = $( this );
				var val    = parseInt( $btn.data( 'scsl-market' ), 10 ) || 0;
				var active = ( val === activeMarket );

				$btn.toggleClass( 'scsl__filter-btn--active', active );
				$btn.attr( 'aria-pressed', active ? 'true' : 'false' );
			} );
		}

		function applyResponse( data ) {
			if ( typeof data.html === 'string' ) {
				$results.html( data.html );
			}
		}

		function requestUpdate() {
			if ( isLoading ) {
				return;
			}

			var cacheKey = String( activeMarket );

			if ( responseCache[ cacheKey ] ) {
				applyResponse( responseCache[ cacheKey ] );
				return;
			}

			setLoading( true );

			$.ajax( {
				url:      SCSL.ajaxUrl,
				method:   'POST',
				dataType: 'json',
				data: {
					action:     'scsl_filter',
					nonce:      SCSL.nonce,
					market:     activeMarket,
					perPage:    SCSL && SCSL.config && SCSL.config.perPage != null ? SCSL.config.perPage : -1,
					eagerFirst: SCSL && SCSL.config && SCSL.config.eagerFirst != null ? SCSL.config.eagerFirst : 3,
				},
			} )
				.done( function( res ) {
					if ( ! res || ! res.success ) {
						return;
					}
					responseCache[ cacheKey ] = res.data;
					applyResponse( res.data );
				} )
				.always( function() {
					setLoading( false );
				} );
		}

		$root.on( 'click', '[data-scsl-market]', function() {
			var $btn   = $( this );
			var termId = parseInt( $btn.data( 'scsl-market' ), 10 ) || 0;

			// Clicking the active non-All button deselects it (returns to All).
			if ( termId !== 0 && termId === activeMarket ) {
				termId = 0;
			}

			setActiveMarket( termId );
			requestUpdate();
		} );
	} );
} );
