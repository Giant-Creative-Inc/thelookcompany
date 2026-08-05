<?php
/**
 * Globe locations shortcode.
 *
 * @package Salient_Globe_Locations
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'salient_globe_locations_render' ) ) {
	/**
	 * Render the globe locations component.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return string
	 */
	function salient_globe_locations_render( $atts, $content = null ) {
		$atts = shortcode_atts(
			array(
				'section_label'    => esc_html__( 'Global locations', 'salient-globe-locations' ),
				'map_image'        => '',
				'map_alt'          => '',
				'full_width_cards' => '',
				'locations'        => '',
			),
			$atts,
			'salient_globe_locations'
		);

		$locations = sgl_parse_locations( $atts['locations'] );

		if ( empty( $locations ) ) {
			return '';
		}

		$map_url = sgl_resolve_image_url( $atts['map_image'], 'full' );

		if ( function_exists( 'nectar_ssl_check' ) && ! empty( $map_url ) ) {
			$map_url = nectar_ssl_check( $map_url );
		}

		Salient_Globe_Locations::get_instance()->mark_globe_on_page();

		static $instance_count = 0;
		++$instance_count;

		$unique_id    = 'sgl-' . $instance_count;
		$live_id      = $unique_id . '-live';
		$section_label = sanitize_text_field( $atts['section_label'] );
		$map_alt       = sanitize_text_field( $atts['map_alt'] );

		if ( '' === $section_label ) {
			$section_label = esc_html__( 'Global locations', 'salient-globe-locations' );
		}

		$section_classes = array( 'sgl-globe-locations' );

		if ( ! empty( $atts['full_width_cards'] ) && 'yes' === $atts['full_width_cards'] ) {
			$section_classes[] = 'sgl-cards-full-width';
		}

		$section_class = implode( ' ', $section_classes );

		$pins_markup  = '';
		$cards_markup = '';

		foreach ( $locations as $index => $location ) {
			$pins_markup  .= sgl_render_pin( $location, $index );
			$cards_markup .= sgl_render_card( $location, $index );
		}

		$duplicate_cards_markup = '';

		foreach ( $locations as $index => $location ) {
			$duplicate_cards_markup .= sgl_render_card( $location, $index, true );
		}

		$map_markup = '';

		if ( ! empty( $map_url ) ) {
			$is_decorative = '' === $map_alt;
			$figure_attrs  = $is_decorative ? ' aria-hidden="true"' : '';
			$img_alt       = $is_decorative ? '' : $map_alt;
			$img_role      = $is_decorative ? ' role="presentation"' : '';

			$map_markup  = '<div class="sgl-map">';
			$map_markup .= '<div class="sgl-map__stage">';
			$map_markup .= '<figure class="sgl-map__figure"' . $figure_attrs . '>';
			$map_markup .= '<img class="sgl-map__image" src="' . esc_url( $map_url ) . '" alt="' . esc_attr( $img_alt ) . '"' . $img_role . ' />';
			$map_markup .= '</figure>';
			$map_markup .= '<div class="sgl-map__pins" role="group" aria-label="' . esc_attr__( 'Location markers on map', 'salient-globe-locations' ) . '">';
			$map_markup .= $pins_markup;
			$map_markup .= '</div>';
			$map_markup .= '</div>';
			$map_markup .= '</div>';
		}

		$markup  = '<section class="' . esc_attr( $section_class ) . '" id="' . esc_attr( $unique_id ) . '" role="region" aria-label="' . esc_attr( $section_label ) . '" data-location-count="' . esc_attr( count( $locations ) ) . '">';
		$markup .= '<div class="sgl-sr-only" id="' . esc_attr( $live_id ) . '" aria-live="polite" aria-atomic="true"></div>';
		$markup .= $map_markup;
		$markup .= '<div class="sgl-cards" role="list" aria-label="' . esc_attr__( 'Location details', 'salient-globe-locations' ) . '">';
		$markup .= '<div class="sgl-cards__scroll">';
		$markup .= '<div class="sgl-cards__track">';
		$markup .= '<div class="sgl-cards__group">' . $cards_markup . '</div>';
		$markup .= '<div class="sgl-cards__group" aria-hidden="true">' . $duplicate_cards_markup . '</div>';
		$markup .= '</div>';
		$markup .= '</div>';
		$markup .= '</div>';
		$markup .= '</section><!--sgl-globe-locations-->';

		return $markup;
	}
}

add_shortcode( 'salient_globe_locations', 'salient_globe_locations_render' );
