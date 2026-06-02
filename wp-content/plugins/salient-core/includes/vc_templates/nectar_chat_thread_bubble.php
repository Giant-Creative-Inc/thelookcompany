<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

extract(shortcode_atts(array(
	'direction' => "incoming",
), $atts));

$outgoing_bubble_image = $GLOBALS['nectar-chat-thread-outgoing-bubble-image'];
$incoming_bubble_image = $GLOBALS['nectar-chat-thread-incoming-bubble-image'];
$outgoing_bubble_name = $GLOBALS['nectar-chat-thread-outgoing-bubble-name'];
$incoming_bubble_name = $GLOBALS['nectar-chat-thread-incoming-bubble-name'];
if($direction === "outgoing") {
    $bubble_image = $outgoing_bubble_image;
    $bubble_name = $outgoing_bubble_name;
} else {
    $bubble_image = $incoming_bubble_image;
    $bubble_name = $incoming_bubble_name;
}

// Prepare image data and standard lazy-loading attrs.
$bubble_img_url = $bubble_image;
$bubble_img_alt = '';
$img_has_dimensions = false;
$img_width = null;
$img_height = null;
$img_extra_attrs = '';
$img_srcset_attr = '';
$image_sizes_attr = '';
$wp_image_size = 'full';
$use_lazy = ( property_exists('NectarLazyImages', 'global_option_active') && true === NectarLazyImages::$global_option_active );
$img_classes = array('nectar-chat-thread__bubble-image');

if( $use_lazy ) {
    $img_classes[] = 'nectar-lazy';
}

if( preg_match('/^\d+$/', $bubble_image) ){
    // Localize attachment ID for WPML if present.
    if( function_exists('apply_filters') ) {
        $bubble_image = apply_filters('wpml_object_id', $bubble_image, 'attachment', true);
    }
    $wp_img_alt_tag = get_post_meta( $bubble_image, '_wp_attachment_image_alt', true );
    if (!empty($wp_img_alt_tag)) {
        $bubble_img_alt = $wp_img_alt_tag;
    }

    $bubble_image_src = wp_get_attachment_image_src($bubble_image, $wp_image_size);
    if( isset($bubble_image_src[0]) ) {
        $bubble_img_url = $bubble_image_src[0];
        $img_has_dimensions = true;
        $img_width = $bubble_image_src[1];
        $img_height = $bubble_image_src[2];
    }

    // Build srcset/sizes following image_with_animation pattern.
    if( function_exists('wp_get_attachment_image_srcset') ) {
        $image_srcset_values = wp_get_attachment_image_srcset($bubble_image, $wp_image_size);
        if( $image_srcset_values ) {
            if( $use_lazy ) {
                $img_srcset_attr = ' data-nectar-img-srcset="'.esc_attr($image_srcset_values).'"';
            } else {
                $img_srcset_attr = ' srcset="'.esc_attr($image_srcset_values).'"';
            }

            $image_sizes = wp_get_attachment_image_sizes($bubble_image, $wp_image_size);
            if( !empty($image_sizes) ) {
                $image_sizes_attr = ' sizes="'.esc_attr($image_sizes).'"';
            }
        }
    }

    // Lazy loading when global option is active.
    if( $img_has_dimensions && $use_lazy ) {
        $placeholder_img_src = "data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg'%20viewBox%3D'0%200%20".esc_attr($img_width).'%20'.esc_attr($img_height)."'%2F%3E";
        $img_extra_attrs .= ' data-nectar-img-src="'.esc_url($bubble_img_url).'"';
        $bubble_img_url = $placeholder_img_src;
    }
}
$img_markup = '';
if( !empty($bubble_img_url) ) {
    $img_markup = '<img class="'.esc_attr( implode(' ', $img_classes) ).'" src="'.esc_attr( $bubble_img_url ).'" alt="'.esc_attr( $bubble_img_alt ).'"'.( $img_has_dimensions ? ' width="'.esc_attr($img_width).'" height="'.esc_attr($img_height).'"' : '' ).$img_extra_attrs.$img_srcset_attr.$image_sizes_attr.' />';
}
$bubble_name_markup = '';
if( !empty($bubble_name) ) {
    $bubble_name_markup = '<div class="nectar-chat-thread__bubble-name"><strong>'.esc_html($bubble_name).'</strong></div>';
}
echo '<div class="nectar-chat-thread__bubble" data-direction="'.esc_attr($direction).'">';
echo $img_markup;
echo '<div class="nectar-chat-thread__bubble-content">';
echo $bubble_name_markup;
echo '<div class="nectar-chat-thread__bubble-content__inner">'.wp_kses_post($content).'</div>';
echo '</div>';
echo '</div>';
