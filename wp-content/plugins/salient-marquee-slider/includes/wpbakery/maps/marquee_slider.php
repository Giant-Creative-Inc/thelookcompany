<?php
/**
 * WPBakery map for Salient Marquee Slider.
 *
 * @package Salient_Marquee_Slider
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$category = defined( 'NECTAR_THEME_NAME' )
	? esc_html__( 'Salient Elements', 'salient-marquee-slider' )
	: esc_html__( 'Salient', 'salient-marquee-slider' );

return array(
	'name'        => esc_html__( 'Marquee Slider', 'salient-marquee-slider' ),
	'base'        => 'salient_marquee_slider',
	'weight'      => 8,
	'icon'        => 'icon-wpb-recent-projects',
	'category'    => $category,
	'description' => esc_html__( 'Infinite-scrolling partner logo marquee', 'salient-marquee-slider' ),
	'params'      => array(
		array(
			'type'        => 'param_group',
			'heading'     => esc_html__( 'Logos', 'salient-marquee-slider' ),
			'param_name'  => 'logos',
			'description' => esc_html__( 'Add partner logos for the infinite-scrolling marquee.', 'salient-marquee-slider' ),
			'params'      => array(
				array(
					'type'        => 'attach_image',
					'heading'     => esc_html__( 'Logo Image', 'salient-marquee-slider' ),
					'param_name'  => 'image',
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Title', 'salient-marquee-slider' ),
					'param_name'  => 'title',
					'description' => esc_html__( 'Used for image alt text and hover title.', 'salient-marquee-slider' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Link URL', 'salient-marquee-slider' ),
					'param_name'  => 'link',
					'description' => esc_html__( 'Optional. Include http:// or https://', 'salient-marquee-slider' ),
				),
			),
		),
		array(
			'type'        => 'textfield',
			'heading'     => esc_html__( 'Scroll Duration (seconds)', 'salient-marquee-slider' ),
			'param_name'  => 'scroll_duration',
			'value'       => '20',
			'description' => esc_html__( 'Time for one full loop. Higher = slower. Longer logo lists usually need a higher value (e.g. 40–60) to match shorter ones.', 'salient-marquee-slider' ),
		),
		array(
			'type'        => 'textfield',
			'heading'     => esc_html__( 'Max Width', 'salient-marquee-slider' ),
			'param_name'  => 'max_width',
			'value'       => '',
			'description' => esc_html__( 'Maximum width of the marquee strip. e.g. 450px, 80%. Leave empty for full width.', 'salient-marquee-slider' ),
		),
		array(
			'type'        => 'dropdown',
			'heading'     => esc_html__( 'Alignment', 'salient-marquee-slider' ),
			'param_name'  => 'align',
			'value'       => array(
				esc_html__( 'Left', 'salient-marquee-slider' )   => 'left',
				esc_html__( 'Center', 'salient-marquee-slider' ) => 'center',
				esc_html__( 'Right', 'salient-marquee-slider' )  => 'right',
			),
			'std'         => 'left',
			'save_always' => true,
		),
		array(
			'type'        => 'textfield',
			'heading'     => esc_html__( 'Accessible Label', 'salient-marquee-slider' ),
			'param_name'  => 'aria_label',
			'value'       => '',
			'description' => esc_html__( 'Describes this marquee for screen readers. Defaults to "Partner logos" when empty.', 'salient-marquee-slider' ),
		),
	),
);
