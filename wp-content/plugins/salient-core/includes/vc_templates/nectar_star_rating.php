<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

extract(shortcode_atts(array(
	"star_rating" => '5',
	"sizing" => '20px',
	'icon_color' => 'accent-color'
), $atts));

$styles = ' style="font-size: '.nectar_css_sizing_units($sizing).';"';

echo '<div class="nectar-star-rating">
	<div class="nectar-star-rating__icon size-'.esc_attr($star_rating).' nectar-color-'.esc_attr($icon_color).'" '.$styles.'></div>';
	if( isset($content) && !empty($content) ) {
    	echo '<div class="nectar-star-rating__content">'.wp_kses_post($content).'</div>';
	}
echo '</div>';

