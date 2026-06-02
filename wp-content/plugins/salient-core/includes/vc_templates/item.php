<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

extract(shortcode_atts(array(
	'simple_slider_bg_image_url' => '',
	'simple_slider_bg_image_position' => 'default',
	'simple_slider_font_color' => '',
	'simple_slider_enable_gradient' => '',
	'simple_slider_color_overlay' => '',
	'simple_slider_color_overlay_2' => '',
	'simple_slider_overlay_strength' => '',
	'simple_slider_bg_color' => '',
  'simple_slider_bg_image_loading' => 'normal',
  'flickity_bg_image_url' => '',
  'flickity_bg_image_loading' => 'normal',
  'flickity_item_url' => '',
  'flickity_item_aria_label_text' => '',
  'flickity_item_link_type' => '',
  'advanced_gradient' => '',
  'advanced_gradient_opacity' => '1',
  'advanced_gradient_opacity_hover' => '1',
  'video_bg'=> '',
  'video_webm'=> '',
  'video_mp4'=> '',
  'background_video_loading' => 'default',
), $atts));

if( isset($_GET['vc_editable']) ) {
	$nectar_using_VC_front_end_editor = sanitize_text_field($_GET['vc_editable']);
	$nectar_using_VC_front_end_editor = ($nectar_using_VC_front_end_editor == 'true') ? true : false;
} else {
	$nectar_using_VC_front_end_editor = false;
}



// Limit script choices on front end editor
if( $nectar_using_VC_front_end_editor ) {

	$nectar_carousel_script_store = 'flickity';

	if( isset($GLOBALS['nectar-carousel-script']) && 'simple_slider' === $GLOBALS['nectar-carousel-script'] ) {
		$nectar_carousel_script_store = 'simple_slider';
	}
} else {
	$nectar_carousel_script_store = isset($GLOBALS['nectar-carousel-script']) ? $GLOBALS['nectar-carousel-script'] : 'flickity';
}


if( $nectar_carousel_script_store === 'carouFredSel' ) {
	echo '<li class="col span_4">' . do_shortcode(wp_kses_post($content)) . '</li>';
}
else if( $nectar_carousel_script_store === 'owl_carousel' ) {
	echo '<div class="carousel-item">' . do_shortcode(wp_kses_post($content)) . '</div>';
}
else if( $nectar_carousel_script_store === 'flickity') {
	$class_names = array('cell');
	if( function_exists('nectar_el_dynamic_classnames') ) {
		$class_names[] = nectar_el_dynamic_classnames('flickity_carousel_slide', $atts);
	}

	// Flickity Image and background markup.
	$flickity_bg_markup = '';
	$flickity_style = '';
	$flickity_lazy_attrs = '';

	// Background color.
	if (!empty($GLOBALS['nectar_carousel_column_color'])) {
		$flickity_style .= 'background-color: ' . sanitize_text_field($GLOBALS['nectar_carousel_column_color']) . ';';
	}

	// Flickity Image.
	if( !empty($flickity_bg_image_url) ) {
		$bg_image_src = '';
		if(!preg_match('/^\d+$/',$flickity_bg_image_url)) {
			$bg_image_src = $flickity_bg_image_url;
		}
		else {
			$bg_image_src = wp_get_attachment_image_src($flickity_bg_image_url, 'full');
			if( isset($bg_image_src[0]) ) {
				$bg_image_src = $bg_image_src[0];
			}
		}

		// Lazy loading.
		if( (property_exists('NectarLazyImages', 'global_option_active') &&
			true === NectarLazyImages::$global_option_active &&
			true !== $nectar_using_VC_front_end_editor &&
			'skip-lazy-load' !== $flickity_bg_image_loading) ||
			'lazy-load' ===  $flickity_bg_image_loading) {
			$flickity_lazy_attrs = 'data-nectar-lazy-bg data-nectar-img-src="'.esc_url($bg_image_src).'"';
		}
		else {
			$flickity_style .= 'background-image: url(\''.esc_url($bg_image_src).'\');';
		}
	}

	// Build markup
	if ($flickity_style !== '') {
		$flickity_bg_markup .= 'style="' . $flickity_style . '"';
	}
	if ($flickity_lazy_attrs !== '') {
		// Add a space if style was present
		if ($flickity_bg_markup !== '') {
			$flickity_bg_markup .= ' ';
		}
		$flickity_bg_markup .= $flickity_lazy_attrs;
	}

	$bg_overlay = '';
	if( !empty($advanced_gradient) ) {
		$bg_overlay = '<div class="nectar_color_layer" style="background:'.esc_attr($advanced_gradient).'; opacity:'.esc_attr($advanced_gradient_opacity).';" data-h-opacity="'.esc_attr($advanced_gradient_opacity_hover).'"></div>';
	}

	$video_bg_markup = nectar_get_video_background_markup($video_bg, $video_webm, $video_mp4, $background_video_loading);
	$video_layer_output = '';
	if( !empty($video_bg_markup) ) {
		$video_layer_output = $video_bg_markup;
	}

	// Overlay link for Flickity items.
	$flickity_overlay_link = '';
	if ( ! empty( $flickity_item_url ) ) {
		$link_classes = array( 'flickity-slider__link' );
		$target_attr = '';
		$rel_attr = '';
		$aria_attr = '';

		if ( ! empty( $flickity_item_aria_label_text ) ) {
			$aria_attr = ' aria-label="' . esc_attr( wp_strip_all_tags( $flickity_item_aria_label_text ) ) . '"';
		}

		if ( 'video_lightbox' === $flickity_item_link_type ) {
			$link_classes[] = 'pp';
			$link_classes[] = 'nectar_video_lightbox';
		}
		else if ( 'image_lightbox' === $flickity_item_link_type ) {
			$link_classes[] = 'pp';
		}
		else if ( 'new_tab' === $flickity_item_link_type ) {
			$target_attr = ' target="_blank"';
			$rel_attr = ' rel="noopener noreferrer"';
		}

		$flickity_overlay_link = '<a class="' . esc_attr( implode( ' ', $link_classes ) ) . '"' . $target_attr . $rel_attr . $aria_attr . ' href="' . esc_url( $flickity_item_url ) . '"></a>';
	}

	echo '<div class="'.esc_attr(implode(' ', $class_names)).'"><div class="inner-wrap-outer"><div class="inner-wrap" '.$flickity_bg_markup.'>' . $video_layer_output . $bg_overlay . $flickity_overlay_link . do_shortcode(wp_kses_post($content)) . '</div></div></div>';
}
else if( $nectar_carousel_script_store === 'simple_slider' || $nectar_using_VC_front_end_editor ) {

	$style             = '';
	$inner_attrs       = '';
	$class_names       = array('cell');
	$inner_class_names = array('inner');

	// Image.
	if( !empty($simple_slider_bg_image_url) ) {

		$bg_image_src = '';

		if(!preg_match('/^\d+$/',$simple_slider_bg_image_url)) {
			$bg_image_src = $simple_slider_bg_image_url;
		}
		else {
			$bg_image_src = wp_get_attachment_image_src($simple_slider_bg_image_url, 'full');
			if( isset($bg_image_src[0]) ) {
				$bg_image_src = $bg_image_src[0];
			}
		}

    // Lazy loading.
		if( (property_exists('NectarLazyImages', 'global_option_active') &&
       true === NectarLazyImages::$global_option_active &&
       true !== $nectar_using_VC_front_end_editor &&
       'skip-lazy-load' !== $simple_slider_bg_image_loading) ||
       'lazy-load' ===  $simple_slider_bg_image_loading) {
			  $style .= ' data-nectar-lazy-bg data-nectar-img-src="'.esc_url($bg_image_src).'"';
		}
    else {
			  $style .= ' style="background-image: url(\''.esc_url($bg_image_src).'\'); "';
		}

	}

	$parallax_layer_class = ' parallax-layer';

	// FE Editor Specific.
	if( true === $nectar_using_VC_front_end_editor ) {
		$inner_class_names[] = 'inner-wrap';
		$parallax_layer_class = '';
		$inner_attrs = (isset($GLOBALS['nectar_carousel_column_color']) && !empty($GLOBALS['nectar_carousel_column_color'])) ? 'style="background-color: ' . sanitize_text_field($GLOBALS['nectar_carousel_column_color']) . ';"': '';
	}

	$simple_slider_bg_color_style = '';

	if( !empty($simple_slider_bg_color) ) {
		$simple_slider_bg_color_style = 'style="background-color: '.esc_attr($simple_slider_bg_color).';"';
		$class_names[] = 'has-bg-color';
	}

	// Dynamic style classes.
	if( function_exists('nectar_el_dynamic_classnames') ) {
		$class_names[] = nectar_el_dynamic_classnames('simple_slider_slide', $atts);
	}

	echo '<div class="'.esc_attr(implode(" ", $class_names)).'">
		<div class="bg-layer-wrap'.esc_attr($parallax_layer_class).'" '.$simple_slider_bg_color_style.'><div class="bg-layer"'.$style.'></div>';
		if( !empty($simple_slider_color_overlay) || !empty($simple_slider_color_overlay_2) ) {
			echo '<div class="color-overlay" data-strength="'.esc_attr($simple_slider_overlay_strength).'"></div>';
		}
		echo '</div>
		<div class="'.esc_attr(implode(" ", $inner_class_names)).'" '.$inner_attrs.'>'.do_shortcode(wp_kses_post($content)).'</div>
	</div>';
}

?>
