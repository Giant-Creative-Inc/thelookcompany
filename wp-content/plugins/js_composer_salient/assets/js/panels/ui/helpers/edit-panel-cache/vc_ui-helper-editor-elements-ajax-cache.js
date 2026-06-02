/**
 * We use this cache in editor to keep edit element panel ajax request data.
 */

( function () {
	'use strict';

	window.vc.HelperEditorElementsAjaxCache = {
		setEditPanelEditorElementAjaxCache: function ( modelId, data ) {
			/* nectar addition - remove caching */
			// Disabled: prevent storing ajax edit-panel data cache (can cause stale/incorrect settings).
			return;
			/* nectar addition end - remove caching */
			if ( !window.vc.EditElementEditorAjaxCache ) {
				window.vc.EditElementEditorAjaxCache = {};
			}

			window.vc.EditElementEditorAjaxCache[modelId] = data;
		},
		removeEditPanelEditorElementAjaxCache: function ( modelId ) {
			/* nectar addition - remove caching */
			return;
			/* nectar addition end - remove caching */
			if ( window.vc.EditElementEditorAjaxCache && window.vc.EditElementEditorAjaxCache[modelId]) {
				delete window.vc.EditElementEditorAjaxCache[modelId];
			}
		},
		isEditPanelEditorElementAjaxCached: function ( modelId ) {
			/* nectar addition - remove caching */
			return false;
			/* nectar addition end - remove caching */
			return window.vc.EditElementEditorAjaxCache && window.vc.EditElementEditorAjaxCache[modelId];
		},
		// check it isEditPanelEditorElementAjaxCached before using
		getEditPanelEditorElementAjaxCache: function ( modelId ) {
			/* nectar addition - remove caching */
			return null;
			/* nectar addition end - remove caching */
			return window.vc.EditElementEditorAjaxCache[modelId];
		}
	};
})();
