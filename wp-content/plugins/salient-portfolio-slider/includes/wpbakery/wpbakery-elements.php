<?php
/**
 * WPBakery element registration.
 *
 * @package Salient_Portfolio_Slider
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'vc_before_init', 'salient_portfolio_slider_maps' );

if ( ! function_exists( 'salient_portfolio_slider_maps' ) ) {
	/**
	 * Register WPBakery lean maps.
	 */
	function salient_portfolio_slider_maps() {
		vc_lean_map(
			'salient_portfolio_slider',
			null,
			SALIENT_PORTFOLIO_SLIDER_ROOT_DIR_PATH . 'includes/wpbakery/maps/portfolio_slider.php'
		);
	}
}
