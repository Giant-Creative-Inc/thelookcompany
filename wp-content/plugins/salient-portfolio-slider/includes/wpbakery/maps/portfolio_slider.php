<?php
/**
 * WPBakery map for Salient Portfolio Slider.
 *
 * @package Salient_Portfolio_Slider
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$category = defined( 'NECTAR_THEME_NAME' )
	? esc_html__( 'Query', 'salient-portfolio-slider' )
	: esc_html__( 'Salient', 'salient-portfolio-slider' );

$slide_section_options = array(
	esc_html__( 'Content', 'salient-portfolio-slider' )     => 'content',
	esc_html__( 'Background', 'salient-portfolio-slider' ) => 'background',
	esc_html__( 'Button', 'salient-portfolio-slider' )      => 'button',
	esc_html__( 'Logo Slider', 'salient-portfolio-slider' ) => 'logo_slider',
);

if ( class_exists( 'Salient_Core' ) ) {
	$slide_section_param = array(
		'type'        => 'nectar_radio_tab_selection',
		'heading'     => esc_html__( 'Section', 'salient-portfolio-slider' ),
		'param_name'  => 'slide_section',
		'save_always' => true,
		'options'     => $slide_section_options,
	);
} else {
	$slide_section_param = array(
		'type'        => 'dropdown',
		'heading'     => esc_html__( 'Section', 'salient-portfolio-slider' ),
		'param_name'  => 'slide_section',
		'save_always' => true,
		'value'       => $slide_section_options,
	);
}

$slide_params = array_merge(
	array( $slide_section_param ),
	array(
		array(
			'type'        => 'textfield',
			'heading'     => esc_html__( 'Title', 'salient-portfolio-slider' ),
			'param_name'  => 'title',
			'admin_label' => true,
			'dependency'  => array(
				'element' => 'slide_section',
				'value'   => array( 'content' ),
			),
		),
		array(
			'type'        => 'textarea',
			'heading'     => esc_html__( 'Text Block', 'salient-portfolio-slider' ),
			'param_name'  => 'text',
			'rows'        => 4,
			'description' => esc_html__( 'Optional text displayed below the title.', 'salient-portfolio-slider' ),
			'dependency'  => array(
				'element' => 'slide_section',
				'value'   => array( 'content' ),
			),
		),
		array(
			'type'        => 'attach_image',
			'heading'     => esc_html__( 'Background Image', 'salient-portfolio-slider' ),
			'param_name'  => 'image',
			'description' => esc_html__( 'Background image for this slide.', 'salient-portfolio-slider' ),
			'dependency'  => array(
				'element' => 'slide_section',
				'value'   => array( 'background' ),
			),
		),
		array(
			'type'        => 'textfield',
			'heading'     => esc_html__( 'Image Alt Text', 'salient-portfolio-slider' ),
			'param_name'  => 'image_alt',
			'description' => esc_html__( 'Describe the background image for accessibility. Leave empty if purely decorative.', 'salient-portfolio-slider' ),
			'dependency'  => array(
				'element' => 'slide_section',
				'value'   => array( 'background' ),
			),
		),
		array(
			'type'       => 'textfield',
			'heading'    => esc_html__( 'Button Text', 'salient-portfolio-slider' ),
			'param_name' => 'button_text',
			'dependency' => array(
				'element' => 'slide_section',
				'value'   => array( 'button' ),
			),
		),
		array(
			'type'        => 'textfield',
			'heading'     => esc_html__( 'Button URL', 'salient-portfolio-slider' ),
			'param_name'  => 'button_url',
			'description' => esc_html__( 'Include http:// or https://', 'salient-portfolio-slider' ),
			'dependency'  => array(
				'element' => 'slide_section',
				'value'   => array( 'button' ),
			),
		),
		array(
			'type'        => 'param_group',
			'heading'     => esc_html__( 'Logo Slider', 'salient-portfolio-slider' ),
			'param_name'  => 'logos',
			'description' => esc_html__( 'Optional infinite-scrolling logo strip beside the slide button.', 'salient-portfolio-slider' ),
			'dependency'  => array(
				'element' => 'slide_section',
				'value'   => array( 'logo_slider' ),
			),
			'params'      => array(
				array(
					'type'        => 'attach_image',
					'heading'     => esc_html__( 'Logo Image', 'salient-portfolio-slider' ),
					'param_name'  => 'image',
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Title', 'salient-portfolio-slider' ),
					'param_name'  => 'title',
					'description' => esc_html__( 'Used for image alt text and hover title.', 'salient-portfolio-slider' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Link URL', 'salient-portfolio-slider' ),
					'param_name'  => 'link',
					'description' => esc_html__( 'Optional. Include http:// or https://', 'salient-portfolio-slider' ),
				),
			),
		),
	)
);

return array(
	'name'        => esc_html__( 'Portfolio Slider', 'salient-portfolio-slider' ),
	'base'        => 'salient_portfolio_slider',
	'weight'      => 8,
	'icon'        => 'icon-wpb-recent-projects',
	'category'    => $category,
	'description' => esc_html__( 'Fullscreen zoom slider with manual slide items', 'salient-portfolio-slider' ),
	'params'      => array(
		array(
			'type'        => 'dropdown',
			'heading'     => esc_html__( 'Slider Controls', 'salient-portfolio-slider' ),
			'param_name'  => 'slider_controls',
			'admin_label' => true,
			'value'       => array(
				esc_html__( 'Prev/Next Arrows', 'salient-portfolio-slider' ) => 'arrows',
				esc_html__( 'Pagination Lines', 'salient-portfolio-slider' ) => 'pagination',
				esc_html__( 'Both', 'salient-portfolio-slider' )                 => 'both',
			),
			'save_always' => true,
			'description' => esc_html__( 'Please select the controls you would like your slider to use.', 'salient-portfolio-slider' ),
		),
		array(
			'type'        => 'dropdown',
			'heading'     => esc_html__( 'Slider Text Color', 'salient-portfolio-slider' ),
			'param_name'  => 'slider_text_color',
			'admin_label' => true,
			'value'       => array(
				esc_html__( 'Light', 'salient-portfolio-slider' ) => 'light',
				esc_html__( 'Dark', 'salient-portfolio-slider' )  => 'dark',
			),
			'save_always' => true,
			'description' => esc_html__( 'Please select the color scheme that will be used for your slider text and controls.', 'salient-portfolio-slider' ),
		),
		array(
			'type'        => 'dropdown',
			'heading'     => esc_html__( 'Slider Heading Structure', 'salient-portfolio-slider' ),
			'param_name'  => 'slider_heading_structure',
			'admin_label' => true,
			'value'       => array(
				esc_html__( 'Default (H1)', 'salient-portfolio-slider' )                              => 'default',
				esc_html__( 'First Slide (H1), Subsequent Slides (H2)', 'salient-portfolio-slider' ) => 'first_h1',
				esc_html__( 'All Slides (H2)', 'salient-portfolio-slider' )                          => 'h2',
			),
			'save_always' => true,
		),
		array(
			'type'        => 'dropdown',
			'heading'     => esc_html__( 'Overlay Strength', 'salient-portfolio-slider' ),
			'param_name'  => 'overlay_strength',
			'admin_label' => true,
			'value'       => array(
				'0'   => '0',
				'0.1' => '0.1',
				'0.2' => '0.2',
				'0.3' => '0.3',
				'0.4' => '0.4',
				'0.5' => '0.5',
				'0.6' => '0.6',
				'0.7' => '0.7',
				'0.8' => '0.8',
				'0.9' => '0.9',
				'1'   => '1',
			),
			'save_always' => true,
			'description' => esc_html__( 'Please select the strength for the image color overlay on your slides.', 'salient-portfolio-slider' ),
		),
		array(
			'type'        => 'textfield',
			'heading'     => esc_html__( 'Default Button Text', 'salient-portfolio-slider' ),
			'param_name'  => 'custom_link_text',
			'value'       => '',
			'description' => esc_html__( 'Default button label when a slide does not specify its own. Leave blank to use "View Project".', 'salient-portfolio-slider' ),
		),
		array(
			'type'        => 'textfield',
			'heading'     => esc_html__( 'Auto Rotate', 'salient-portfolio-slider' ),
			'param_name'  => 'autorotate',
			'value'       => '',
			'description' => esc_html__( 'If you would like this to auto rotate, enter the rotation speed in milliseconds here. e.g. 5000', 'salient-portfolio-slider' ),
		),
		array(
			'type'        => 'textfield',
			'heading'     => esc_html__( 'Accessible Slider Label', 'salient-portfolio-slider' ),
			'param_name'  => 'slider_label',
			'value'       => '',
			'description' => esc_html__( 'Describes this slider for screen readers. Defaults to "Portfolio slider" when empty.', 'salient-portfolio-slider' ),
		),
		array(
			'type'       => 'param_group',
			'heading'    => esc_html__( 'Slides', 'salient-portfolio-slider' ),
			'param_name' => 'slides',
			'value'      => urlencode(
				wp_json_encode(
					array(
						array(
							'title' => esc_html__( 'Slide 1', 'salient-portfolio-slider' ),
						),
					)
				)
			),
			'params'     => $slide_params,
		),
	),
);
