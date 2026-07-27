<?php
/**
 * Marquee slider shortcode.
 *
 * @package Salient_Marquee_Slider
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'salient_marquee_slider_render' ) ) {
	/**
	 * Render the partner logo marquee slider.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return string
	 */
	function salient_marquee_slider_render( $atts, $content = null ) {
		$atts = shortcode_atts(
			array(
				'logos'           => '',
				'scroll_duration' => '20',
				'max_width'       => '',
				'align'           => 'left',
				'aria_label'      => '',
			),
			$atts,
			'salient_marquee_slider'
		);

		Salient_Marquee_Slider::get_instance()->mark_on_page();

		$logos = salient_marquee_slider_parse_logos( $atts['logos'] );

		return salient_marquee_slider_render_marquee(
			$logos,
			array(
				'aria_label'      => $atts['aria_label'],
				'scroll_duration' => $atts['scroll_duration'],
				'max_width'       => $atts['max_width'],
				'align'           => $atts['align'],
			)
		);
	}
}

add_shortcode( 'salient_marquee_slider', 'salient_marquee_slider_render' );
