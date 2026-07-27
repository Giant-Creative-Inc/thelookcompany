<?php
/**
 * Plugin Name: Salient Portfolio Slider
 * Plugin URI: https://giantcreative.com
 * Description: Fullscreen zoom portfolio slider for WPBakery with manual repeater slides instead of portfolio CPT posts.
 * Author: GIANT Creative
 * Author URI: https://giantcreative.com
 * Version: 1.0.3
 * Text Domain: salient-portfolio-slider
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SALIENT_PORTFOLIO_SLIDER_ROOT_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'SALIENT_PORTFOLIO_SLIDER_PLUGIN_PATH', plugins_url( 'salient-portfolio-slider' ) );

if ( ! defined( 'SALIENT_PORTFOLIO_SLIDER_VERSION' ) ) {
	define( 'SALIENT_PORTFOLIO_SLIDER_VERSION', '1.0.3' );
}

require_once SALIENT_PORTFOLIO_SLIDER_ROOT_DIR_PATH . 'includes/class-plugin.php';

Salient_Portfolio_Slider::get_instance();
