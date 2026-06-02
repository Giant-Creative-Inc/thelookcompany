/**
 * In fact, it's not a cache,
 * We just render edit panel for editor elements with hidden visibility
 * And show it when user clicks on edit element.
 */

( function () {
	'use strict';

	window.vc.HelperEditPanelCache = {
		addEditPanelToCache: function ( modelId, el ) {
			/* nectar addition - remove caching */
			// Disabled: prevent edit panel reuse cache (can cause stale/incorrect settings).
			return;
			/* nectar addition end - remove caching */
			window.vc.editPanelCache = {};
			window.vc.editPanelCache.modelId = modelId;
			window.vc.editPanelCache.element = el;
		},
		isEditPanelCached: function ( modelId ) {
			/* nectar addition - remove caching */
			return false;
			/* nectar addition end - remove caching */
			if ( ! window.vc.editPanelCache ) {
				return false;
			}

			return window.vc.editPanelCache.modelId === modelId;
		},
		removeEditPanelCache: function () {
			/* nectar addition - remove caching */
			return;
			/* nectar addition end - remove caching */
			window.vc.editPanelCache = {};
		},
		isPanelActiveOrHidden: function () {
			if ( ! vc.active_panel ) {
				return true;
			}

			return getComputedStyle( vc.active_panel.el ).visibility === 'hidden';
		},
		cacheEditPanel: function () {
			/* nectar addition - remove caching */
			return;
			/* nectar addition end - remove caching */
			var modelId = this.model.get( 'id' );

			if ( this.isEditPanelCached( modelId ) ) {
				return;
			}

			if ( ! this.isPanelActiveOrHidden() ) {
				return;
			}

			vc.closeActivePanel();

			vc.edit_element_block_view.render( this.model, false, true );
		},
		cacheEditPanelParent: function () {
			/* nectar addition - remove caching */
			return;
			/* nectar addition end - remove caching */
			var modelId = this.parent_view.model.get( 'id' );

			if ( this.isEditPanelCached( modelId ) ) {
				return;
			}

			if ( ! this.isPanelActiveOrHidden() ) {
				return;
			}

			vc.edit_element_block_view.render( this.parent_view.model, false, true );
		},
		isCurrentPanelActiveAndNotCached: function () {
			if ( ! vc.active_panel.model || ! this.model ) {
				return false;
			}

			if ( this.isEditPanelCached( this.model.get( 'id' ) ) ) {
				return false;
			}

			return vc.active_panel.model.get( 'id' ) === this.model.get( 'id' );
		}
	};
})();
