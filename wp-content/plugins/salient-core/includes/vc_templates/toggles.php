<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_enqueue_style( 'nectar-element-toggle-panels' );

extract(shortcode_atts(array(
	"accordion" => 'false',
	'accordion_starting_functionality' => 'default',
	'style' => 'default',
	'border_radius' => '',
	'animated_circle_divider' => 'false',
	'animated_circle_bg_color' => '',
	'animated_circle_position' => 'left',
	'animated_circle_size' => '40',
	'animated_circle_gap' => '15'
), $atts));

($accordion === 'true') ? $accordion_class = 'accordion': $accordion_class = '';

if( 'minimal_shadow' === $style ) {
	$style = 'minimal';
	$accordion_class .= ' toggles--minimal-shadow';
}

$GLOBALS['nectar-toggle-style'] = $style;

// Inline CSS variables for animated circle style.
$inline_style_attr = '';
$data_attrs = '';
if ( 'animated_circle' === $style ) {
	$ac_position = ( in_array( $animated_circle_position, array( 'left', 'right' ), true ) ) ? $animated_circle_position : 'left';
	$ac_size     = $animated_circle_size;
	if ( '' !== $ac_size && false === strpos( $ac_size, 'px' ) ) {
		$ac_size .= 'px';
	}
	$ac_bg_color = $animated_circle_bg_color;
	$ac_divider  = ( 'true' === $animated_circle_divider ) ? '1' : '0';
	$br_value    = ( 'none' === $border_radius || '' === $border_radius ) ? '0px' : $border_radius;

	$css_parts = array();
	$css_parts[] = '--ac-size:' . $ac_size;
	$css_parts[] = '--ac-bg-color:' . $ac_bg_color;
	$css_parts[] = '--toggles-border-radius:' . $br_value;
	$css_parts[] = '--ac-gap:' . $animated_circle_gap . 'px';
	$css_vars   = implode( '; ', $css_parts ) . ';';

	$inline_style_attr = ' style="' . esc_attr( $css_vars ) . '"';

	// has bg color
	$has_bg_color = ( '#ffffff' !== $ac_bg_color ) ? '1' : '0';
	$data_attrs = ' data-ac-position="' . esc_attr( $ac_position ) . '" data-ac-divider="' . esc_attr( $ac_divider ) . '" data-ac-bg-color="' . esc_attr( $has_bg_color ) . '"';
}

echo '<div class="toggles '.$accordion_class.'"'.$inline_style_attr.$data_attrs.' data-br="'.esc_attr($border_radius).'" data-starting="'.esc_attr($accordion_starting_functionality).'" data-style="'.esc_attr($style).'">' . do_shortcode(wp_kses_post($content)) . '</div>';



















