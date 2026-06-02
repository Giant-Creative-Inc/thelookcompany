<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	"name" => esc_html__("Chat Thread Bubble", "salient-core"),
	"base" => "nectar_chat_thread_bubble",
    "icon" => "icon-wpb-clients",
    "content_element" => true,
    "as_child" => array('only' => 'nectar_chat_thread'),
	"category" => esc_html__('Content', 'salient-core'),
	"description" => esc_html__('Chat Thread Bubble', 'salient-core'),
	"params" => array(
        array(
            "type" => "dropdown",
            "heading" => esc_html__("Message Direction", "salient-core"),
            "param_name" => "direction",
            "value" => array(
                esc_html__("Incoming", "salient-core") => "incoming",
                esc_html__("Outgoing", "salient-core") => "outgoing"),
            "admin_label" => true,
            "description" => esc_html__("Select the direction of the chat thread bubble", "salient-core")
        ),
        array(
            "type" => "textarea_html",
            "heading" => esc_html__("Text", "salient-core"),
            "param_name" => "content",
            "value" => '',
            "description" => '',
            "admin_label" => true,
        ),


	)
);

?>