<?php
/**
 * WPBakery map for Salient Globe Locations.
 *
 * @package Salient_Globe_Locations
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$category = defined( 'NECTAR_THEME_NAME' )
	? esc_html__( 'Query', 'salient-globe-locations' )
	: esc_html__( 'Salient', 'salient-globe-locations' );

return array(
	'name'        => esc_html__( 'Globe Locations', 'salient-globe-locations' ),
	'base'        => 'salient_globe_locations',
	'weight'      => 8,
	'icon'        => 'icon-wpb-map',
	'category'    => $category,
	'description' => esc_html__( 'Interactive map with location pins and card slider', 'salient-globe-locations' ),
	'params'      => array(
		array(
			'type'        => 'textfield',
			'heading'     => esc_html__( 'Section Label', 'salient-globe-locations' ),
			'param_name'  => 'section_label',
			'value'       => esc_html__( 'Global locations', 'salient-globe-locations' ),
			'description' => esc_html__( 'Accessible name for this section, used by screen readers.', 'salient-globe-locations' ),
		),
		array(
			'type'        => 'attach_image',
			'heading'     => esc_html__( 'Map Background Image', 'salient-globe-locations' ),
			'param_name'  => 'map_image',
			'description' => esc_html__( 'Upload the world map background image.', 'salient-globe-locations' ),
		),
		array(
			'type'        => 'textfield',
			'heading'     => esc_html__( 'Map Alt Text', 'salient-globe-locations' ),
			'param_name'  => 'map_alt',
			'value'       => '',
			'description' => esc_html__( 'Optional. Leave empty to treat the map as decorative.', 'salient-globe-locations' ),
		),
		array(
			'type'        => 'checkbox',
			'heading'     => esc_html__( 'Full Width Cards', 'salient-globe-locations' ),
			'param_name'  => 'full_width_cards',
			'value'       => array( esc_html__( 'Yes', 'salient-globe-locations' ) => 'yes' ),
			'description' => esc_html__( 'Bleed the location card slider to the viewport edge on desktop.', 'salient-globe-locations' ),
		),
		array(
			'type'       => 'param_group',
			'heading'    => esc_html__( 'Locations', 'salient-globe-locations' ),
			'param_name' => 'locations',
			'value'      => urlencode(
				wp_json_encode(
					array(
						array(
							'name' => esc_html__( 'Location 1', 'salient-globe-locations' ),
						),
					)
				)
			),
			'params'     => array(
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Name', 'salient-globe-locations' ),
					'param_name'  => 'name',
					'admin_label' => true,
				),
				array(
					'type'       => 'textarea',
					'heading'    => esc_html__( 'Address', 'salient-globe-locations' ),
					'param_name' => 'address',
					'rows'       => 3,
				),
				array(
					'type'       => 'textfield',
					'heading'    => esc_html__( 'Phone Number', 'salient-globe-locations' ),
					'param_name' => 'phone',
				),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'X Position (%)', 'salient-globe-locations' ),
					'param_name'  => 'x_pos',
					'description' => esc_html__( 'Horizontal position from the left edge (0–100).', 'salient-globe-locations' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Y Position (%)', 'salient-globe-locations' ),
					'param_name'  => 'y_pos',
					'description' => esc_html__( 'Vertical position from the top edge (0–100).', 'salient-globe-locations' ),
				),
			),
		),
	),
);
