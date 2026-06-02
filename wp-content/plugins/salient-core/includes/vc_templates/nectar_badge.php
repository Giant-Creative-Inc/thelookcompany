<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

extract(shortcode_atts(array(
  'text' => '',
  'color' => 'accent-color',
  'display_tag' => 'body',
  'display' => 'block',
  'bg_color_custom' => '',
  'bg_color_type' => '',
  'border_color' => '',
  'text_color' => '',
  'padding' => '',
  'border' => '',
  'border_width' => '1',
  'border_radius' => '',
  'margin_top' => '',
  'margin_left' => '',
  'margin_right' => '',
  'margin_bottom' => ''
), $atts));

$classes = array('nectar-badge');
$classes[] = 'nectar-inherit-'.esc_attr($display_tag);
$classes[] = 'nectar-display-'.esc_attr($display);

// Dynamic style classes.
if( function_exists('nectar_el_dynamic_classnames') ) {
    $classes[] = nectar_el_dynamic_classnames('nectar_badge', $atts);
}

$inner_classes = array('nectar-badge__inner');

if( 'global' === $bg_color_type ) {
    $inner_classes[] = 'nectar-bg-'.esc_attr($color);
}

// Margins.
$styles = '';

if( !empty($margin_top) || '0' === $margin_top ) {
  $styles .= 'margin-top: ' . nectar_css_sizing_units($margin_top) . '; ';
}
if( !empty($margin_right) || '0' === $margin_right ) {
  $styles .= 'margin-right: ' . nectar_css_sizing_units($margin_right) . '; ';
}
if( !empty($margin_bottom) || '0' === $margin_bottom ) {
  $styles .= 'margin-bottom: ' . nectar_css_sizing_units($margin_bottom) . '; ';
}
if( !empty($margin_left) || '0' === $margin_left ) {
  $styles .= 'margin-left: ' . nectar_css_sizing_units($margin_left) . ';';
}

// Border.
if ( $border === 'true' ) {
  $styles .= '--border-color: '.esc_attr($border_color).';';
  $styles .= '--border-width: '.esc_attr($border_width).'px;';

}

$el_atts = '';

if( 'custom' === $bg_color_type ) {
  $el_atts .= ' data-bg-color-custom="'.esc_attr($bg_color_custom).'"';
}

$el_style = '';
if(!empty($styles)) {
    $el_style = ' style="'.$styles.'"';
}

echo '<div class="'.nectar_clean_classnames(implode(' ',$classes)).'"'.$el_style . $el_atts.'><div class="'.nectar_clean_classnames(implode(' ',$inner_classes)).'">' . do_shortcode(wp_kses_post($text)) . '</div></div>';
