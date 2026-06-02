<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	"name" => esc_html__("Toggle Panels", "salient-core"),
	"base" => "toggles",
	"show_settings_on_create" => false,
	"is_container" => true,
	"icon" => "icon-wpb-ui-accordion",
	"category" => esc_html__('Interactive', 'salient-core'),
	"description" => esc_html__('jQuery toggles/accordion', 'salient-core'),
	"params" => array(
		array(
			"type" => "dropdown",
			"heading" => esc_html__("Style", "salient-core"),
			"param_name" => "style",
			"admin_label" => true,
			"value" => array(
				esc_html__("Default", "salient-core") => "default",
				esc_html__("Animated Circle", "salient-core") => "animated_circle",
				esc_html__("Minimal", "salient-core") => "minimal",
				esc_html__("Minimal Shadow", "salient-core") => "minimal_shadow",
				esc_html__("Inline Small", "salient-core") => "minimal_small",
			),
			'save_always' => true,
			"description" => esc_html__("Please select the style you desire for your toggle element.", "salient-core")
		),

		array(
			"type" => "dropdown",
			"heading" => esc_html__("Animated Circle Position", "salient-core"),
			"param_name" => "animated_circle_position",
			"dependency" => Array('element' => "style", 'value' => array('animated_circle')),
			'save_always' => true,
			"value" => array(
				esc_html__("Left", "salient-core") => "left",
				esc_html__("Right", "salient-core") => "right",
			),
			"description" => ""
		),
		array(
			"type" => "nectar_range_slider",
			"heading" => esc_html__("Animated Circle Size", "salient-core"),
			"param_name" => "animated_circle_size",
			"dependency" => Array('element' => "style", 'value' => array('animated_circle')),
			"value" => "40",
			"save_always" => true,
			"options" => array(
				"min" => "20",
				"max" => "60",
				"step" => "1",
				"suffix" => "px"
			),
			"description" => ""
		),
		array(
			"type" => "colorpicker",
			"heading" => esc_html__("Toggle Background Color", "salient-core"),
			"param_name" => "animated_circle_bg_color",
			"dependency" => Array('element' => "style", 'value' => array('animated_circle')),
			"value" => "",
			"std" => "",
			"description" => ""
		),
		array(
			"type" => "checkbox",
			"heading" => esc_html__("Enable Divider", "salient-core"),
			"param_name" => "animated_circle_divider",
			"dependency" => Array('element' => "style", 'value' => array('animated_circle')),
			'edit_field_class' => 'vc_col-xs-12 salient-fancy-checkbox',
			"value" => Array(esc_html__("Yes, please", "salient-core") => 'true'),
			"description" => ""
		),
		array(
			"type" => "nectar_range_slider",
			"heading" => esc_html__("Toggle Gap", "salient-core"),
			"param_name" => "animated_circle_gap",
			"dependency" => Array('element' => "style", 'value' => array('animated_circle')),
			"value" => "15",
			"save_always" => true,
			"options" => array(
				"min" => "1",
				"max" => "30",
				"step" => "1",
				"suffix" => "px"
			),
			"description" => ""
		),

		array(
			"type" => 'checkbox',
			"heading" => esc_html__("Accordion Toggles", "salient-core"),
			"param_name" => "accordion",
			'edit_field_class' => 'vc_col-xs-12 salient-fancy-checkbox',
			"description" => esc_html__("Selecting this will make it so that only one toggle can be opened at a time.", "salient-core"),
			"value" => Array(esc_html__("Allow", "salient-core") => 'true')
		),
		array(
			"type" => "dropdown",
			"heading" => esc_html__("Starting Functionality", "salient-core"),
			"param_name" => "accordion_starting_functionality",
			"admin_label" => true,
			"value" => array(
				esc_html__("First Toggle Open", "salient-core") => "default",
				esc_html__("First Toggle Closed", "salient-core") => "closed",
			),
			"dependency" => Array('element' => "accordion", 'not_empty' => true),
			'save_always' => true,
		),
		array(
            "type" => "dropdown",
            "heading" => esc_html__("Border Radius", "salient-core"),
            'save_always' => true,
			"dependency" => Array('element' => "style", 'value' => array('minimal_shadow', 'default', 'animated_circle')),
            "param_name" => "border_radius",
            "value" => array(
              esc_html__("0px", "salient-core") => "none",
              esc_html__("3px", "salient-core") => "3px",
              esc_html__("5px", "salient-core") => "5px",
              esc_html__("10px", "salient-core") => "10px",
              esc_html__("15px", "salient-core") => "15px",
              esc_html__("20px", "salient-core") => "20px"),
            "description" => ''
          ),
	),
	"custom_markup" => '
	<div class="wpb_accordion_holder wpb_holder clearfix vc_container_for_children">
	%content%
	</div>
	<div class="tab_controls">
	<a class="add_tab" title="' . esc_html__( 'Add section', 'salient-core' ) . '"><span class="vc_icon"></span> <span class="tab-label">' . esc_html__( 'Add section', 'salient-core' ) . '</span></a>
	</div>
	',
	'default_content' => '
	[toggle title="'.esc_html__('Section', "salient-core").'"][/toggle]
	[toggle title="'.esc_html__('Section', "salient-core").'"][/toggle]
	',
	'js_view' => 'VcAccordionView'
);
?>