<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

extract(shortcode_atts(array(
    "outgoing_bubble_image" => "",
    "outgoing_bubble_name" => "",
    "outgoing_bubble_color" => "",
    "outgoing_text_color" => "",
    "incoming_bubble_image" => "",
    "incoming_bubble_name" => "",
    "incoming_bubble_color" => "",
    "incoming_text_color" => "",
    "image_size" => "30",
    "border_radius" => "10",
    "loop_animation" => "",
    "auto_height_animation" => "",
    "animation" => ""
), $atts));

$GLOBALS['nectar-chat-thread-outgoing-bubble-image'] = $outgoing_bubble_image;
$GLOBALS['nectar-chat-thread-incoming-bubble-image'] = $incoming_bubble_image;
$GLOBALS['nectar-chat-thread-outgoing-bubble-name'] = $outgoing_bubble_name;
$GLOBALS['nectar-chat-thread-incoming-bubble-name'] = $incoming_bubble_name;

// set style for coloring in css variables
$style = 'style="--outgoing-bubble-color: '.esc_attr($outgoing_bubble_color).';
        --outgoing-text-color: '.esc_attr($outgoing_text_color).';
        --incoming-bubble-color: '.esc_attr($incoming_bubble_color).';
        --incoming-text-color: '.esc_attr($incoming_text_color).';
        --image-size: '.esc_attr($image_size).'px;
        --border-radius: '.esc_attr($border_radius).'px;"';

    //remove all line breaks and white space from style
    $style = preg_replace('/\s+/', ' ', $style);
echo '<div class="nectar-chat-thread" data-animation="'.esc_attr($animation).'" data-auto-height-animation="'.esc_attr($auto_height_animation).'" data-loop-animation="'.esc_attr($loop_animation).'"'.$style.'>'.do_shortcode(wp_kses_post($content)).'</div>';