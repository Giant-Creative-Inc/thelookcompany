/**
 * Caches AJAX responses for the edit form
 * when adding new elements via the "Add Element" panel.
 */

( function ( $ ) {
	'use strict';

	window.vc.HelperAddElementEditPanelAjaxCache = {
		cacheTimeoutId: null,
		isAddElementEditPanelAjaxCache: function ( tag ) {
			/* nectar addition - remove caching */
			return false;
			/* nectar addition end - remove caching */
			return window.vc.addElementEditPanelAjaxCache && window.vc.addElementEditPanelAjaxCache[tag];
		},
		setAddElementEditPanelAjaxCache: function ( tag ) {
			/* nectar addition - remove caching */
			// Disabled: avoid noisy prefetch requests and stale/incorrect cached edit forms.
			return;
			if ( this.isAddElementEditPanelAjaxCache( tag ) ) {
				return;
			}

			// our grid builder elements produce fatal errors when cached.
			if ( this.isGridBuilderElement( tag ) ) {
				return;
			}

			if ( !window.vc.addElementEditPanelAjaxCache ) {
				window.vc.addElementEditPanelAjaxCache = {};
			}

			this.checkAjax();

			var _this = this;

			window.vc.addElementEditPanelAjaxCache[tag] = new Promise( function ( resolve, reject ) {
				_this.ajax = $.ajax({
					type: 'POST',
					url: window.ajaxurl,
					data: {
						action: 'wpb_add_element_edit_window_ajax_cache',
						tag: tag,
						post_id: window.vc_post_id,
						escape_usage_count: true,
						_vcnonce: window.vcAdminNonce
					}
				}).done( function ( response ) {
					if ( response.success === false ) {
						delete window.vc.addElementEditPanelAjaxCache[tag];
						reject( new Error( response.data || 'Ajax error' ) );
						return;
					}
					window.vc.addElementEditPanelAjaxCache[tag] = response;
					resolve( response );
				}).fail( function ( jqXHR, textStatus ) {
					delete window.vc.addElementEditPanelAjaxCache[tag];
					reject( new Error( textStatus || 'Ajax canceled' ) );
				});
			})
				.catch( function () {});
		},
		waitAddElementEditPanelAjaxCache: async function ( shortcodeTag ) {
			/* nectar addition - remove caching */
			return false;
			/* nectar addition end - remove caching */
			if ( !window.vc.addElementEditPanelAjaxCache ) {
				return false;
			}

			var data = window.vc.addElementEditPanelAjaxCache[shortcodeTag];

			if ( !data ) {return false;}

			if ( data instanceof Promise ) {
				try {
					await data;
					return true;
				} catch {
					console.log( 'Failed to resolve window.vc.addElementEditPanelAjaxCache[shortcodeTag]' );
					return false;
				}
			}

			return true;
		},
		getAddElementEditPanelAjaxCache: function ( shortcodeTag ) {
			/* nectar addition - remove caching */
			return null;
			/* nectar addition end - remove caching */
			return window.vc.addElementEditPanelAjaxCache[shortcodeTag];
		},
		cacheElement: function ( e ) {
			/* nectar addition - remove caching */
			return;
			/* nectar addition end - remove caching */
			var _this = this;
			var $target = $( e.currentTarget );

			if ( this.cacheTimeoutId ) {
				clearTimeout( this.cacheTimeoutId );
			}

			this.cacheTimeoutId = setTimeout( function () {
				_this.setAddElementEditPanelAjaxCache( $target.data( 'tag' ) );
			}, 300 );
		},
		isGridBuilderElement: function ( tag ) {
			return tag.startsWith( 'vc_gitem_' );
		}
	};
})( window.jQuery );
