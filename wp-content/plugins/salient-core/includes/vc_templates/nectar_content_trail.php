<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

extract(shortcode_atts(array(
    'images' => '-1',
    'type' => 'images',
    'image_size' => 'large',
    'text_array' => '',
    'text_color_array' => '',
    'font_color' => '#000',
    'animation_in' => 'none',
    'animation_out' => 'none',
    'frequency' => '130',
    'duration' => '1',
    'aspect_ratio' => 'auto',
    'lerp' => '70',
    'randomize' => 'false',
    'randomize_rotation' => 'false',
    'border_radius' => '0',
    'overflow' => 'hidden',
  ), $atts));


/*************** IMAGES ***************/
$image_size_for_media_lib = $image_size;


/* Gather images into an arr */
$images = explode( ',', $images );
$images_markup_arr = array();

$image_classes = ['nectar-content-trail__image'];

if( $aspect_ratio !== 'auto' ) {
  $image_classes[] = 'nectar-aspect-ratio--'.esc_attr($aspect_ratio);
}

// Handle text type
if( $type === 'text' ) {

    $text_items = array();
    $text_colors = array();

    if( !empty($text_array) ) {
        // Decode URL-encoded JSON and extract text values
        $decoded_text = urldecode($text_array);
        $text_data = json_decode($decoded_text, true);

        if( is_array($text_data) ) {
            $text_items = array();
            foreach( $text_data as $text_obj ) {
                if( isset($text_obj['text_value']) ) {
                    $text_items[] = $text_obj['text_value'];
                }
            }
        } else {
            $text_items = array();
        }
    } else {
        // Add sample words if text array is empty
        $text_items = array('salient', 'wordpress', 'theme');
    }

    if( !empty($text_color_array) ) {
        // Decode URL-encoded JSON and extract color values
        $decoded_colors = urldecode($text_color_array);
        $color_data = json_decode($decoded_colors, true);

        if( is_array($color_data) ) {
            $text_colors = array();
            foreach( $color_data as $color_obj ) {
                if( isset($color_obj['color_value']) ) {
                    $text_colors[] = $color_obj['color_value'];
                }
            }
        } else {
            $text_colors = array();
        }
    }

    foreach ($text_items as $index => $text_item) {
        $text_classes = ['nectar-content-trail__text'];
        $images_markup_arr[] = '<div class="'.esc_attr(implode(' ', $text_classes)).'">'.esc_html(trim($text_item)).'</div>';
    }


    // Create JSON encoded color array for data attribute
    $colors_json = !empty($text_colors) ? json_encode($text_colors) : '[]';

} else {
    // Handle images type (existing logic)
    foreach ($images as $attach_id) {

        if ($attach_id > 0) {

            // if( 'lazy-load' === $image_loading && NectarLazyImages::activate_lazy() ||
            //     ( property_exists('NectarLazyImages', 'global_option_active') && true === NectarLazyImages::$global_option_active && 'skip-lazy-load' !== $image_loading ) ) {

                $image_arr = wp_get_attachment_image_src($attach_id, $image_size_for_media_lib);

                if( isset($image_arr[0]) ) {

                    $image_src    = $image_arr[0];
                    $img_dimens_w = $image_arr[1];
                    $img_dimens_h = $image_arr[2];
                    $placeholder_img_src = "data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg'%20viewBox%3D'0%200%20".esc_attr($img_dimens_w).'%20'.esc_attr($img_dimens_h)."'%2F%3E";

                    $alt_tag = '';
                    $wp_img_alt_tag = get_post_meta( $attach_id, '_wp_attachment_image_alt', true );
                    if (!empty($wp_img_alt_tag)) {
                        $alt_tag = $wp_img_alt_tag;
                    }

                    $image_classes[] = 'nectar-lazy';
                    $images_markup_arr[] = '<img class="'.esc_attr(implode(' ', $image_classes)).'" src="'.esc_attr($placeholder_img_src).'" data-nectar-img-src="'.esc_attr($image_src).'" alt="'.esc_attr($alt_tag).'" width="'.esc_attr($img_dimens_w).'" height="'.esc_attr($img_dimens_h).'" />';

                }

            // }

            // else {

            //     $image_src = wp_get_attachment_image_src($attach_id, $image_size_for_media_lib);
            //     $image_alt = get_post_meta($attach_id, '_wp_attachment_image_alt', TRUE);

            //     if( $image_src ) {
            //       $images_markup_arr[] = '<img class="'.esc_attr(implode(' ', $image_classes)).'" src="'.esc_attr($image_src[0]).'" width="'.esc_attr($image_src[1]).'" height="'.esc_attr($image_src[2]).'" alt="'.esc_html($image_alt).'" />';
            //     }

            // }

        }


    }

    if( count($images) == 1 && $images[0] == '-1' || count($images) == 1 && $images[0] == '') {
      for( $i=0; $i<7; $i++) {
        $placeholder_image_classes = ['nectar-content-trail__image', 'nectar-content-trail__image--placeholder'];

        if( $aspect_ratio !== 'auto' ) {
          $placeholder_image_classes[] = 'nectar-aspect-ratio--'.esc_attr($aspect_ratio);
        }

        $placeholder_image_classes[] = 'nectar-content-trail--gradient-'.($i+1);
        $images_markup_arr[$i] = '<div class="'.esc_attr(implode(' ', $placeholder_image_classes)).'"></div>';
      }
    }

    // Set empty colors array for images
    $colors_json = '[]';
}

// Build CSS variables from shortcode attributes
$css_vars = array();

$css_attributes = array(
    'border_radius' => '--border-radius',
    'font_color' => '--font-color',
);

foreach ($css_attributes as $attr => $css_var) {
    if (isset($atts[$attr]) && !empty($atts[$attr])) {
        $value = $atts[$attr];

        // Add px to border_radius if it's just a number
        if ($attr === 'border_radius' && is_numeric($value)) {
            $value = $value . 'px';
        }

        // Only include font_color when in text mode
        if ($attr === 'font_color' && $type !== 'text') {
            continue;
        }


        $css_vars[] = $css_var . ': ' . esc_attr($value);
    }
}

$css_variables = !empty($css_vars) ? ' style="' . implode('; ', $css_vars) . ';"' : '';

// style classes.
$el_classes = array('nectar-content-trail');

if( function_exists('nectar_el_dynamic_classnames') ) {
	$el_classes[] = nectar_el_dynamic_classnames('nectar_content_trail', $atts);
} else {
	$el_classes[] = '';
}



echo '<div data-type="'.esc_attr($type).'" data-colors="'.esc_attr($colors_json).'" data-duration="'.esc_attr($duration).'" data-frequency="'.esc_attr($frequency).'" data-lerp="'.esc_attr($lerp).'" data-animation-in="'.esc_attr($animation_in).'" data-animation-out="'.esc_attr($animation_out).'" data-randomize="'.esc_attr($randomize).'" data-randomize-rotation="'.esc_attr($randomize_rotation).'"'.$css_variables.' class="'.nectar_clean_classnames(implode(' ',$el_classes)).'"><div class="nectar-content-trail__list">' .  implode('', $images_markup_arr) . '</div></div>';