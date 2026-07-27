<?php
/**
 * Plugin Name: Salient Marquee Slider
 * Plugin URI: https://giantcreative.com
 * Description: Infinite-scrolling partner logo marquee for WPBakery with image, title, and link fields.
 * Author: GIANT Creative
 * Author URI: https://giantcreative.com
 * Version: 1.0.1
 * Text Domain: salient-marquee-slider
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SALIENT_MARQUEE_SLIDER_ROOT_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'SALIENT_MARQUEE_SLIDER_PLUGIN_PATH', plugins_url( 'salient-marquee-slider' ) );

if ( ! defined( 'SALIENT_MARQUEE_SLIDER_VERSION' ) ) {
	define( 'SALIENT_MARQUEE_SLIDER_VERSION', '1.0.1' );
}

require_once SALIENT_MARQUEE_SLIDER_ROOT_DIR_PATH . 'includes/class-plugin.php';

Salient_Marquee_Slider::get_instance();
