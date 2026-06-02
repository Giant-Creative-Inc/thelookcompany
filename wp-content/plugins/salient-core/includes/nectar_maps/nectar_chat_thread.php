<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	"name" => esc_html__("Chat Thread", "salient-core"),
	"base" => "nectar_chat_thread",
    "icon" => "icon-wpb-clients",
    "show_settings_on_create" => false,
    "is_container" => false,
    "as_parent" => array(
        'only' => 'nectar_chat_thread_bubble'
    ),
    "class" => "nectar-general-container",
	"category" => esc_html__('Content', 'salient-core'),
    "description" => esc_html__('Chat Thread', 'salient-core'),
    "js_view" => 'VcColumnView',
	"params" => array(

       // Outgoing
        array(
			"type" => "attach_image",
			"class" => "",
			"heading" => esc_html__("Outgoing Chat Thread Bubble Image", "salient-core"),
			"value" => "",
			"param_name" => "outgoing_bubble_image",
			"group" => esc_html__("Outgoing", "salient-core"),
			"description" => esc_html__("Add an optional image for the outgoing chat thread", "salient-core")
		),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Outgoing Bubble Name", "salient-core"),
            "param_name" => "outgoing_bubble_name",
            "value" => "",
            "group" => esc_html__("Outgoing", "salient-core"),
        ),
        array(
            "type" => "colorpicker",
            "heading" => esc_html__("Outgoing Bubble Color", "salient-core"),
            "param_name" => "outgoing_bubble_color",
            "value" => "",
            "group" => esc_html__("Outgoing", "salient-core"),
        ),
         array(
            "type" => "colorpicker",
            "heading" => esc_html__("Outgoing Text Color", "salient-core"),
            "param_name" => "outgoing_text_color",
            "value" => "",
            "group" => esc_html__("Outgoing", "salient-core"),
        ),

        // Incoming
        array(
			"type" => "attach_image",
			"class" => "",
			"heading" => esc_html__("Incoming Chat Thread Bubble Image", "salient-core"),
			"value" => "",
			"param_name" => "incoming_bubble_image",
			"group" => esc_html__("Incoming", "salient-core"),
			"description" => esc_html__("Add an optional image for the incoming chat thread", "salient-core")
		),
        // incoming bubble name
        array(
            "type" => "textfield",
            "heading" => esc_html__("Incoming Bubble Name", "salient-core"),
            "param_name" => "incoming_bubble_name",
            "value" => "",
            "group" => esc_html__("Incoming", "salient-core"),
        ),
        // color incoming bubble
        array(
            "type" => "colorpicker",
            "heading" => esc_html__("Incoming Bubble Color", "salient-core"),
            "param_name" => "incoming_bubble_color",
            "value" => "",
            "group" => esc_html__("Incoming", "salient-core"),
        ),
        // text color
        array(
            "type" => "colorpicker",
            "heading" => esc_html__("Incoming Text Color", "salient-core"),
            "param_name" => "incoming_text_color",
            "value" => "",
            "group" => esc_html__("Incoming", "salient-core"),
        ),

        // Style
        array(
        'type' => 'nectar_range_slider',
        'heading' => esc_html__( 'Image Size', 'salient-core' ),
        'description' => esc_html__( 'How large the image will be.', 'salient-core' ),
        'param_name' => 'image_size',
        'value' => '30',
        'save_always' => true,
        'options' => array(
            'min' => '20',
            'max' => '50',
            'step' => '1',
            'suffix' => 'px'
            )
        ),
        array(
        'type' => 'nectar_range_slider',
        'heading' => esc_html__( 'Chat Bubble Border Radius', 'salient-core' ),
        'description' => '',
        'param_name' => 'border_radius',
        'value' => '10',
        'save_always' => true,
        'options' => array(
            'min' => '0',
            'max' => '30',
            'step' => '1',
            'suffix' => 'px'
            )
        ),


        // animation
        array(
            "type" => "dropdown",
            "heading" => esc_html__("Animation", "salient-core"),
            "param_name" => "animation",
            "value" => array(
                esc_html__("None", "salient-core") => "none",
                esc_html__("Fade In", "salient-core") => "fade-in",
                esc_html__("Typing", "salient-core") => "typing",
            ),
            "group" => esc_html__("Animation", "salient-core"),
        ),

        // Auto height
        array(
			"type" => "checkbox",
			"class" => "",
			'edit_field_class' => 'vc_col-xs-12 salient-fancy-checkbox',
			"heading" => esc_html__("Auto Height", "salient-core"),
			"value" => array("Enable Auto Height?" => "true" ),
			"param_name" => "auto_height_animation",
			"description" => "",
            "group" => esc_html__("Animation", "salient-core"),
			"dependency" => Array('element' => "animation", 'value' => array('fade-in', 'typing'))
		),


        // loop animation
        array(
			"type" => "checkbox",
			"class" => "",
			'edit_field_class' => 'vc_col-xs-12 salient-fancy-checkbox',
			"heading" => esc_html__("Loop Animation", "salient-core"),
			"value" => array("Enable Loop Animation?" => "true" ),
			"param_name" => "loop_animation",
			"description" => "",
            "group" => esc_html__("Animation", "salient-core"),
			"dependency" => Array('element' => "animation", 'value' => array('typing'))
		),



	)
);

?>