<?php
/**
 * WPBakery element registration.
 *
 * @package Salient_Globe_Locations
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'vc_before_init', 'salient_globe_locations_maps' );

if ( ! function_exists( 'salient_globe_locations_maps' ) ) {
	/**
	 * Register WPBakery lean maps.
	 */
	function salient_globe_locations_maps() {
		vc_lean_map(
			'salient_globe_locations',
			null,
			SALIENT_GLOBE_LOCATIONS_ROOT_DIR_PATH . 'includes/wpbakery/maps/globe_locations.php'
		);
	}
}
