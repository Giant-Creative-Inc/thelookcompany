<?php
/**
 * WPBakery element registration.
 *
 * @package Salient_Marquee_Slider
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'vc_before_init', 'salient_marquee_slider_maps' );

if ( ! function_exists( 'salient_marquee_slider_maps' ) ) {
	/**
	 * Register WPBakery lean maps.
	 */
	function salient_marquee_slider_maps() {
		vc_lean_map(
			'salient_marquee_slider',
			null,
			SALIENT_MARQUEE_SLIDER_ROOT_DIR_PATH . 'includes/wpbakery/maps/marquee_slider.php'
		);
	}
}
