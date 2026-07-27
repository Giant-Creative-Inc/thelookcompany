<?php
/**
 * Plugin Name: Salient Globe Locations
 * Plugin URI: https://giantcreative.com
 * Description: Interactive globe map with location pins and a card slider for WPBakery.
 * Author: GIANT Creative
 * Author URI: https://giantcreative.com
 * Version: 1.0.0
 * Text Domain: salient-globe-locations
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SALIENT_GLOBE_LOCATIONS_ROOT_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'SALIENT_GLOBE_LOCATIONS_PLUGIN_PATH', plugins_url( 'salient-globe-locations' ) );

if ( ! defined( 'SALIENT_GLOBE_LOCATIONS_VERSION' ) ) {
	define( 'SALIENT_GLOBE_LOCATIONS_VERSION', '1.0.0' );
}

require_once SALIENT_GLOBE_LOCATIONS_ROOT_DIR_PATH . 'includes/class-plugin.php';

Salient_Globe_Locations::get_instance();
