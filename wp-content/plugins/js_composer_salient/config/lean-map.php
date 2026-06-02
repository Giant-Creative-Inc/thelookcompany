<?php
/**
 * WPBakery Page Builder Shortcodes settings Lazy mapping.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$vc_config_path = vc_path_dir( 'CONFIG_DIR' );
vc_lean_map( 'vc_row_inner', null, $vc_config_path . '/containers/shortcode-vc-row-inner.php' );
 vc_lean_map( 'vc_column_text', null, $vc_config_path . '/content/shortcode-vc-column-text.php' );

 vc_lean_map( 'vc_text_separator', null, $vc_config_path . '/content/shortcode-vc-text-separator.php' );

 vc_lean_map( 'vc_single_image', null, $vc_config_path . '/content/shortcode-vc-single-image.php' );
 vc_lean_map( 'vc_gallery', null, $vc_config_path . '/content/shortcode-vc-gallery.php' );

 vc_lean_map( 'vc_custom_heading', null, $vc_config_path . '/content/shortcode-vc-custom-heading.php' );

 vc_lean_map( 'vc_raw_html', null, $vc_config_path . '/structure/shortcode-vc-raw-html.php' );
 vc_lean_map( 'vc_raw_js', null, $vc_config_path . '/structure/shortcode-vc-raw-js.php' );
 vc_lean_map( 'vc_pie', null, $vc_config_path . '/content/shortcode-vc-pie.php' );
 vc_lean_map( 'vc_video', null, $vc_config_path . '/content/shortcode-vc-video.php' );

 vc_lean_map( 'vc_icon', null, $vc_config_path . '/content/shortcode-vc-icon.php' );
 vc_lean_map( 'vc_zigzag', null, $vc_config_path . '/content/shortcode-vc-zigzag.php' );


 /* nectar addition */
 if( ! defined( 'NECTAR_THEME_NAME' ) ) {
 	vc_lean_map( 'vc_row', null, $vc_config_path . '/containers/shortcode-vc-row.php' );
 	vc_lean_map( 'vc_column', null, $vc_config_path . '/containers/shortcode-vc-column.php' );
 	vc_lean_map( 'vc_column_inner', null, $vc_config_path . '/containers/shortcode-vc-column-inner.php' );
 }

 if( has_filter('salient_register_core_wpbakery_els') ) {

 	// Els that are acceptable to add in via filter.
 	$el_list = array(
 		'vc_hoverbox'    => '/content/shortcode-vc-hoverbox.php',
 		'vc_message'     => '/content/shortcode-vc-message.php',
 		'vc_btn'         => '/buttons/shortcode-vc-btn.php',
 		'vc_cta'         => '/buttons/shortcode-vc-cta.php',
 		'vc_empty_space' => '/content/shortcode-vc-empty-space.php',
 		'vc_button'      => '/deprecated/shortcode-vc-button.php',
 		'vc_button2'     => '/deprecated/shortcode-vc-button2.php',
 		'vc_cta_button'  => '/deprecated/shortcode-vc-cta-button.php',
 		'vc_cta_button2' => '/deprecated/shortcode-vc-cta-button2.php',
		// 'vc_section'     => '/containers/shortcode-vc-section.php',
		'vc_round_chart' => '/content/shortcode-vc-round-chart.php',
		'vc_line_chart'  => '/content/shortcode-vc-line-chart.php',
 	);

 	$el_list_keys = array_keys($el_list);

 	$core_els_list = array();
 	$core_els_list = apply_filters('salient_register_core_wpbakery_els', $core_els_list);

 	foreach ($core_els_list as $k => $el) {

 		if( in_array($el, $el_list_keys) ) {
 			vc_lean_map( $el, null, $vc_config_path . $el_list[$el] );
 		}

 	}

 }

 /* nectar addition - vc grid */
 $enable_raw_wpbakery_post_grid = false;
 if( has_filter('salient_enable_core_wpbakery_post_grid') ) {
 	$enable_raw_wpbakery_post_grid = apply_filters('salient_enable_core_wpbakery_post_grid', $enable_raw_wpbakery_post_grid);
 }

 if( true === $enable_raw_wpbakery_post_grid ) {
 	vc_lean_map( 'vc_basic_grid', null, $vc_config_path . '/grids/shortcode-vc-basic-grid.php' );
 	vc_lean_map( 'vc_media_grid', null, $vc_config_path . '/grids/shortcode-vc-media-grid.php' );
 	vc_lean_map( 'vc_masonry_grid', null, $vc_config_path . '/grids/shortcode-vc-masonry-grid.php' );
 	vc_lean_map( 'vc_masonry_media_grid', null, $vc_config_path . '/grids/shortcode-vc-masonry-media-grid.php' );
 	vc_lean_map( 'vc_btn', null, $vc_config_path . '/buttons/shortcode-vc-btn.php' );
 }

 /*
 vc_lean_map( 'vc_row', null, $vc_config_path . '/containers/shortcode-vc-row.php' );
 vc_lean_map( 'vc_row_inner', null, $vc_config_path . '/containers/shortcode-vc-row-inner.php' );
 vc_lean_map( 'vc_column', null, $vc_config_path . '/containers/shortcode-vc-column.php' );
 vc_lean_map( 'vc_column_inner', null, $vc_config_path . '/containers/shortcode-vc-column-inner.php' );
 vc_lean_map( 'vc_column_text', null, $vc_config_path . '/content/shortcode-vc-column-text.php' );
 vc_lean_map( 'vc_section', null, $vc_config_path . '/containers/shortcode-vc-section.php' );
 vc_lean_map( 'vc_icon', null, $vc_config_path . '/content/shortcode-vc-icon.php' );
 vc_lean_map( 'vc_separator', null, $vc_config_path . '/content/shortcode-vc-separator.php' );
 vc_lean_map( 'vc_zigzag', null, $vc_config_path . '/content/shortcode-vc-zigzag.php' );
 vc_lean_map( 'vc_text_separator', null, $vc_config_path . '/content/shortcode-vc-text-separator.php' );
 vc_lean_map( 'vc_message', null, $vc_config_path . '/content/shortcode-vc-message.php' );
 vc_lean_map( 'vc_hoverbox', null, $vc_config_path . '/content/shortcode-vc-hoverbox.php' );

 vc_lean_map( 'vc_facebook', null, $vc_config_path . '/social/shortcode-vc-facebook.php' );
 vc_lean_map( 'vc_tweetmeme', null, $vc_config_path . '/social/shortcode-vc-tweetmeme.php' );
 vc_lean_map( 'vc_googleplus', null, $vc_config_path . '/social/shortcode-vc-googleplus.php' );
 vc_lean_map( 'vc_pinterest', null, $vc_config_path . '/social/shortcode-vc-pinterest.php' );

 vc_lean_map( 'vc_toggle', null, $vc_config_path . '/content/shortcode-vc-toggle.php' );
 vc_lean_map( 'vc_single_image', null, $vc_config_path . '/content/shortcode-vc-single-image.php' );
 vc_lean_map( 'vc_gallery', null, $vc_config_path . '/content/shortcode-vc-gallery.php' );
 vc_lean_map( 'vc_images_carousel', null, $vc_config_path . '/content/shortcode-vc-images-carousel.php' );

 vc_lean_map( 'vc_tta_tabs', null, $vc_config_path . '/tta/shortcode-vc-tta-tabs.php' );
 vc_lean_map( 'vc_tta_tour', null, $vc_config_path . '/tta/shortcode-vc-tta-tour.php' );
 vc_lean_map( 'vc_tta_accordion', null, $vc_config_path . '/tta/shortcode-vc-tta-accordion.php' );
 vc_lean_map( 'vc_tta_pageable', null, $vc_config_path . '/tta/shortcode-vc-tta-pageable.php' );
 vc_lean_map( 'vc_tta_section', null, $vc_config_path . '/tta/shortcode-vc-tta-section.php' );

 vc_lean_map( 'vc_custom_heading', null, $vc_config_path . '/content/shortcode-vc-custom-heading.php' );

 vc_lean_map( 'vc_btn', null, $vc_config_path . '/buttons/shortcode-vc-btn.php' );
 vc_lean_map( 'vc_cta', null, $vc_config_path . '/buttons/shortcode-vc-cta.php' );

 vc_lean_map( 'vc_widget_sidebar', null, $vc_config_path . '/structure/shortcode-vc-widget-sidebar.php' );
 vc_lean_map( 'vc_posts_slider', null, $vc_config_path . '/content/shortcode-vc-posts-slider.php' );
 vc_lean_map( 'vc_video', null, $vc_config_path . '/content/shortcode-vc-video.php' );
 vc_lean_map( 'vc_gmaps', null, $vc_config_path . '/content/shortcode-vc-gmaps.php' );
 vc_lean_map( 'vc_raw_html', null, $vc_config_path . '/structure/shortcode-vc-raw-html.php' );
 vc_lean_map( 'vc_raw_js', null, $vc_config_path . '/structure/shortcode-vc-raw-js.php' );
 vc_lean_map( 'vc_flickr', null, $vc_config_path . '/content/shortcode-vc-flickr.php' );
 vc_lean_map( 'vc_progress_bar', null, $vc_config_path . '/content/shortcode-vc-progress-bar.php' );
 vc_lean_map( 'vc_pie', null, $vc_config_path . '/content/shortcode-vc-pie.php' );
 vc_lean_map( 'vc_round_chart', null, $vc_config_path . '/content/shortcode-vc-round-chart.php' );
 vc_lean_map( 'vc_line_chart', null, $vc_config_path . '/content/shortcode-vc-line-chart.php' );

 vc_lean_map( 'vc_wp_search', null, $vc_config_path . '/wp/shortcode-vc-wp-search.php' );
 vc_lean_map( 'vc_wp_meta', null, $vc_config_path . '/wp/shortcode-vc-wp-meta.php' );
 vc_lean_map( 'vc_wp_recentcomments', null, $vc_config_path . '/wp/shortcode-vc-wp-recentcomments.php' );
 vc_lean_map( 'vc_wp_calendar', null, $vc_config_path . '/wp/shortcode-vc-wp-calendar.php' );
 vc_lean_map( 'vc_wp_pages', null, $vc_config_path . '/wp/shortcode-vc-wp-pages.php' );
 vc_lean_map( 'vc_wp_tagcloud', null, $vc_config_path . '/wp/shortcode-vc-wp-tagcloud.php' );
 vc_lean_map( 'vc_wp_custommenu', null, $vc_config_path . '/wp/shortcode-vc-wp-custommenu.php' );
 vc_lean_map( 'vc_wp_text', null, $vc_config_path . '/wp/shortcode-vc-wp-text.php' );
 vc_lean_map( 'vc_wp_posts', null, $vc_config_path . '/wp/shortcode-vc-wp-posts.php' );
 vc_lean_map( 'vc_wp_links', null, $vc_config_path . '/wp/shortcode-vc-wp-links.php' );
 vc_lean_map( 'vc_wp_categories', null, $vc_config_path . '/wp/shortcode-vc-wp-categories.php' );
 vc_lean_map( 'vc_wp_archives', null, $vc_config_path . '/wp/shortcode-vc-wp-archives.php' );
 vc_lean_map( 'vc_wp_rss', null, $vc_config_path . '/wp/shortcode-vc-wp-rss.php' );

 vc_lean_map( 'vc_empty_space', null, $vc_config_path . '/content/shortcode-vc-empty-space.php' );

 vc_lean_map( 'vc_tabs', null, $vc_config_path . '/deprecated/shortcode-vc-tabs.php' );
 vc_lean_map( 'vc_tour', null, $vc_config_path . '/deprecated/shortcode-vc-tour.php' );
 vc_lean_map( 'vc_tab', null, $vc_config_path . '/deprecated/shortcode-vc-tab.php' );
 vc_lean_map( 'vc_accordion', null, $vc_config_path . '/deprecated/shortcode-vc-accordion.php' );
 vc_lean_map( 'vc_accordion_tab', null, $vc_config_path . '/deprecated/shortcode-vc-accordion-tab.php' );
 vc_lean_map( 'vc_posts_grid', null, $vc_config_path . '/deprecated/shortcode-vc-posts-grid.php' );
 vc_lean_map( 'vc_carousel', null, $vc_config_path . '/deprecated/shortcode-vc-carousel.php' );
 vc_lean_map( 'vc_button', null, $vc_config_path . '/deprecated/shortcode-vc-button.php' );
 vc_lean_map( 'vc_button2', null, $vc_config_path . '/deprecated/shortcode-vc-button2.php' );
 vc_lean_map( 'vc_cta_button', null, $vc_config_path . '/deprecated/shortcode-vc-cta-button.php' );
 vc_lean_map( 'vc_cta_button2', null, $vc_config_path . '/deprecated/shortcode-vc-cta-button2.php' );

 */
if ( is_admin() ) {
	add_action( 'admin_print_scripts-post.php', [
		Vc_Shortcodes_Manager::getInstance(),
		'buildShortcodesAssets',
	], 1 );
	add_action( 'admin_print_scripts-post-new.php', [
		Vc_Shortcodes_Manager::getInstance(),
		'buildShortcodesAssets',
	], 1 );
	add_action( 'vc-render-templates-preview-template', [
		Vc_Shortcodes_Manager::getInstance(),
		'buildShortcodesAssets',
	], 1 );
} elseif ( vc_is_page_editable() ) {
	add_action( 'wp_head', [
		Vc_Shortcodes_Manager::getInstance(),
		'buildShortcodesAssetsForEditable',
	] ); // @todo where these icons are used in iframe?
}

require_once vc_path_dir( 'CONFIG_DIR', 'grids/vc-grids-functions.php' );
if ( 'vc_get_autocomplete_suggestion' === vc_request_param( 'action' ) || 'vc_edit_form' === vc_post_param( 'action' ) ) {
	add_filter( 'vc_autocomplete_vc_basic_grid_include_callback', 'vc_include_field_search' ); // Get suggestion(find). Must return an array.
	add_filter( 'vc_autocomplete_vc_basic_grid_include_render', 'vc_include_field_render' ); // Render exact product. Must return an array (label,value).
	add_filter( 'vc_autocomplete_vc_masonry_grid_include_callback', 'vc_include_field_search' ); // Get suggestion(find). Must return an array.
	add_filter( 'vc_autocomplete_vc_masonry_grid_include_render', 'vc_include_field_render' ); // Render exact product. Must return an array (label,value).

	// Narrow data taxonomies.
	add_filter( 'vc_autocomplete_vc_basic_grid_taxonomies_callback', 'vc_autocomplete_taxonomies_field_search' );
	add_filter( 'vc_autocomplete_vc_basic_grid_taxonomies_render', 'vc_autocomplete_taxonomies_field_render' );

	add_filter( 'vc_autocomplete_vc_masonry_grid_taxonomies_callback', 'vc_autocomplete_taxonomies_field_search' );
	add_filter( 'vc_autocomplete_vc_masonry_grid_taxonomies_render', 'vc_autocomplete_taxonomies_field_render' );

	// Narrow data taxonomies for exclude_filter.
	add_filter( 'vc_autocomplete_vc_basic_grid_exclude_filter_callback', 'vc_autocomplete_taxonomies_field_search' );
	add_filter( 'vc_autocomplete_vc_basic_grid_exclude_filter_render', 'vc_autocomplete_taxonomies_field_render' );

	add_filter( 'vc_autocomplete_vc_masonry_grid_exclude_filter_callback', 'vc_autocomplete_taxonomies_field_search' );
	add_filter( 'vc_autocomplete_vc_masonry_grid_exclude_filter_render', 'vc_autocomplete_taxonomies_field_render' );

	add_filter( 'vc_autocomplete_vc_basic_grid_exclude_callback', 'vc_exclude_field_search' ); // Get suggestion(find). Must return an array.
	add_filter( 'vc_autocomplete_vc_basic_grid_exclude_render', 'vc_exclude_field_render' ); // Render exact product. Must return an array (label,value).
	add_filter( 'vc_autocomplete_vc_masonry_grid_exclude_callback', 'vc_exclude_field_search' ); // Get suggestion(find). Must return an array.
	add_filter( 'vc_autocomplete_vc_masonry_grid_exclude_render', 'vc_exclude_field_render' ); // Render exact product. Must return an array (label,value).
}

