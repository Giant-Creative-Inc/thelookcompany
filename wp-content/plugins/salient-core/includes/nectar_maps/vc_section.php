<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return [
	'name' => esc_html__( 'Section', 'js_composer' ),
	'is_container' => true,
	'icon' => 'vc_icon-vc-section',
	'show_settings_on_create' => false,
	'category' => esc_html__( 'Content', 'js_composer' ),
	'as_parent' => [
		'only' => 'vc_row',
	],
	'as_child' => [
		'only' => '', // Only root.
	],
	'class' => 'vc_main-sortable-element',
	'description' => esc_html__( 'Group multiple rows in section', 'js_composer' ),
	'params' => [
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Type", "salient-core" ),
			"param_name" => "type",
			'save_always' => true,
			"value" => array(
				esc_html__("In Container", "salient-core" ) => "in_container",
				esc_html__("Full Width Background", "salient-core" ) => "full_width_background",
				esc_html__("Full Width Content", "salient-core" ) => "full_width_content",
			)
		),

		...SalientWPbakeryParamGroups::height_group(),
		array(
			"type" => "nectar_group_header",
			"class" => "",
			"heading" => esc_html__("Content", "salient-core" ),
			"param_name" => "group_header_2",
			"edit_field_class" => "",
			"value" => ''
		),

		...SalientWPbakeryParamGroups::layout_group(),


		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Text Color", "salient-core"),
			"param_name" => "text_color",
			"value" => array(
				esc_html__("Dark", "salient-core") => "dark",
				esc_html__("Light", "salient-core") => "light",
				esc_html__("Custom", "salient-core") => "custom"
			),
			'save_always' => true
		),

		...SalientWPbakeryParamGroups::spacing_group('', []),

		...SalientWPbakeryParamGroups::background_group(esc_html__("Background", "salient-core"), []),
		array(
			"type" => "checkbox",
			"class" => "",
			"group" => esc_html__("Background", "salient-core"),
			'edit_field_class' => 'vc_col-xs-12 salient-fancy-checkbox',
			"heading" => esc_html__("Color Change Section", "salient-core"),
			"value" => array("Enable" => "true" ),
			"param_name" => "color_change_section",
			"description" => __("Allow this section to animate the color of the page background when it scrolls into view.", "salient-core")
		),
		...SalientWpbakeryParamGroups::color_layer_group(esc_html__("Color Overlay", "salient-core"), [], ''),

		array(
			"type" => "nectar_group_header",
			"class" => "",
			"heading" => esc_html__("Rounded Edges", "salient-core" ),
			"param_name" => "group_header_4",
			"edit_field_class" => "",
			"value" => ''
		),
		array(
			"type" => "dropdown",
			"heading" => esc_html__("Border Radius", "salient-core"),
			'save_always' => true,
			"edit_field_class" => "col-md-6",
			"param_name" => "row_border_radius",
			"value" => array(
				esc_html__("0px", "salient-core") => "none",
				esc_html__("5px", "salient-core") => "5px",
				esc_html__("10px", "salient-core") => "10px",
				esc_html__("15px", "salient-core") => "15px",
				esc_html__("20px", "salient-core") => "20px",
				esc_html__("Custom", "salient-core") => "custom",
			),
			"description" => ''
		),
		array(
			"type" => "dropdown",
			"heading" => esc_html__("Applies To", "salient-core"),
			'save_always' => true,
			"edit_field_class" => "col-md-6 col-md-6-last",
			"param_name" => "row_border_radius_applies",
			"value" => array(
				esc_html__("Row Background", "salient-core") => "bg",
				esc_html__("Inner Content", "salient-core") => "inner",
				esc_html__("Both", "salient-core") => "both"),
			"description" => ''
		),

		array(
			"type" => "nectar_group_header",
			"class" => "",
			"heading" => esc_html__("Advanced", "salient-core" ),
			"param_name" => "group_header_5",
			"edit_field_class" => "",
			"value" => ''
		),
		[
			'type' => 'el_id',
			'heading' => esc_html__( 'Section ID', 'js_composer' ),
			'param_name' => 'el_id',
			'description' => sprintf( esc_html__( 'Enter element ID (Note: make sure it is unique and valid according to %1$sw3c specification%2$s).', 'js_composer' ), '<a href="https://www.w3schools.com/tags/att_global_id.asp" target="_blank">', '</a>' ),
		],
		[
			'type' => 'checkbox',
			'heading' => esc_html__( 'Disable section', 'js_composer' ),
			'param_name' => 'disable_element',
			// Inner param name.
			'description' => esc_html__( 'If checked the section won\'t be visible on the public side of your website. You can switch it back any time.', 'js_composer' ),
			'value' => [ esc_html__( 'Yes', 'js_composer' ) => 'yes' ],
		],
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
		],
		[
			"type" => "checkbox",
			"class" => "",
			"heading" => esc_html__("Parallax Background Media On Scroll", "salient-core"),
			"value" => array("Enable Parallax Background?" => "true" ),
			"param_name" => "parallax_bg",
			'edit_field_class' => 'vc_col-xs-12 salient-fancy-checkbox',
			"description" => esc_html__("This will cause the background media on your row to scroll at a different speed than the content", "salient-core"),
			"group" => "Animation"
		],
		[
			"type" => "dropdown",
			"class" => "",
			"description" => '',
			"heading" => esc_html__("Scroll Effect", "salient-core"),
			"param_name" => "parallax_bg_scroll_effect",
			'save_always' => true,
			"value" => array(
				 esc_html__("Parallax", "salient-core") => "parallax",
				 esc_html__("Parallax Fade", "salient-core") => "parallax_fade",
			),
			"group" => "Animation",
			"dependency" => Array('element' => "parallax_bg", 'not_empty' => true)
		],
		[
			"type" => "dropdown",
			"class" => "",
			"description" => esc_html__("The more dramatic the parallax speed, the larger the BG layer will have to be resized to accommodate. Note: the \"Fixed in place\" option will have no effect on the video layer.", "salient-core"),
			"heading" => esc_html__("Parallax Background Layer Speed", "salient-core"),
			"param_name" => "parallax_bg_speed",
			'save_always' => true,
			"value" => array(
				 esc_html__("Subtle", "salient-core") => "fast",
				 esc_html__("Regular", "salient-core") => "medium_fast",
				 esc_html__("Medium", "salient-core") => "medium",
				 esc_html__("High", "salient-core") => "slow"
			),
			"group" => "Animation",
			"dependency" => Array('element' => "parallax_bg", 'not_empty' => true)
		],

	],
	'js_view' => 'VcSectionView',
];
