<?php
/**
 * Output for custom modifications.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Vc_Modifications
 */
class Vc_Modifications {
	/**
	 * Modified flag.
	 *
	 * @var bool
	 */
	public static $modified = false;

	/**
	 * Vc_Modifications constructor.
	 */
	public function __construct() {
		add_action( 'wp_footer', [
			$this,
			'renderScript',
		] );
	}

	/**
	 * Render script.
	 */
	public function renderScript() {
		if ( self::$modified ) {
			// output script.
			$tag = 'script';
			// nectar addition - remove type="text/html" from tag
			echo '<' . esc_attr( $tag ) . ' id="wpb-modifications"> window.wpbCustomElement = 1; </' . esc_attr( $tag ) . '>';
			// nectar addition end
		}
	}
}
