<?php
/**
 * Frontend helpers.
 *
 * @package Salient_Globe_Locations
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'sgl_resolve_image_url' ) ) {
	/**
	 * Resolve an attachment ID or URL to an image URL.
	 *
	 * @param string $src  Attachment ID or URL.
	 * @param string $size Image size.
	 * @return string
	 */
	function sgl_resolve_image_url( $src, $size = 'full' ) {
		if ( empty( $src ) ) {
			return '';
		}

		if ( function_exists( 'nectar_resolve_img_url' ) ) {
			return nectar_resolve_img_url( $src, $size );
		}

		if ( preg_match( '/^\d+$/', $src ) ) {
			$url = wp_get_attachment_image_url( (int) $src, $size );
			return $url ? esc_url( $url ) : '';
		}

		return esc_url( $src );
	}
}

if ( ! function_exists( 'sgl_sanitize_coord' ) ) {
	/**
	 * Sanitize and clamp a map coordinate to 0–100.
	 *
	 * @param mixed $value Raw coordinate value.
	 * @return float
	 */
	function sgl_sanitize_coord( $value ) {
		if ( '' === $value || null === $value ) {
			return 50.0;
		}

		$coord = floatval( $value );

		return max( 0.0, min( 100.0, $coord ) );
	}
}

if ( ! function_exists( 'sgl_format_phone_link' ) ) {
	/**
	 * Build a tel: href from a phone number string.
	 *
	 * @param string $phone Raw phone number.
	 * @return string
	 */
	function sgl_format_phone_link( $phone ) {
		if ( empty( $phone ) ) {
			return '';
		}

		$digits = preg_replace( '/[^\d+]/', '', $phone );

		return $digits ? 'tel:' . $digits : '';
	}
}

if ( ! function_exists( 'sgl_parse_locations' ) ) {
	/**
	 * Parse and normalize location repeater rows.
	 *
	 * @param string $locations_raw Encoded param_group value.
	 * @return array<int, array{name:string,address:string,phone:string,x_pos:float,y_pos:float}>
	 */
	function sgl_parse_locations( $locations_raw ) {
		if ( empty( $locations_raw ) || ! function_exists( 'vc_param_group_parse_atts' ) ) {
			return array();
		}

		$rows = vc_param_group_parse_atts( $locations_raw );

		if ( empty( $rows ) || ! is_array( $rows ) ) {
			return array();
		}

		$parsed = array();

		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$name = isset( $row['name'] ) ? trim( (string) $row['name'] ) : '';

			if ( '' === $name ) {
				continue;
			}

			$parsed[] = array(
				'name'    => sanitize_text_field( $name ),
				'address' => isset( $row['address'] ) ? sanitize_textarea_field( (string) $row['address'] ) : '',
				'phone'   => isset( $row['phone'] ) ? sanitize_text_field( (string) $row['phone'] ) : '',
				'x_pos'   => sgl_sanitize_coord( isset( $row['x_pos'] ) ? $row['x_pos'] : 50 ),
				'y_pos'   => sgl_sanitize_coord( isset( $row['y_pos'] ) ? $row['y_pos'] : 50 ),
			);
		}

		return $parsed;
	}
}

if ( ! function_exists( 'sgl_get_pin_label' ) ) {
	/**
	 * Build an accessible label for a map pin button.
	 *
	 * @param string $name Location name.
	 * @return string
	 */
	function sgl_get_pin_label( $name ) {
		return sprintf(
			/* translators: %s: location name */
			__( '%s. Select location.', 'salient-globe-locations' ),
			$name
		);
	}
}

if ( ! function_exists( 'sgl_render_pin' ) ) {
	/**
	 * Render a single map pin button.
	 *
	 * @param array $location Parsed location row.
	 * @param int   $index    Location index.
	 * @return string
	 */
	function sgl_render_pin( $location, $index ) {
		$style = sprintf(
			'left:%s%%;top:%s%%;',
			esc_attr( $location['x_pos'] ),
			esc_attr( $location['y_pos'] )
		);

		return sprintf(
			'<button type="button" class="sgl-pin" data-index="%1$d" data-name="%4$s" data-address="%5$s" data-phone="%6$s" style="%2$s" aria-pressed="false" aria-label="%3$s"><span class="sgl-pin__dot" aria-hidden="true"></span></button>',
			(int) $index,
			$style,
			esc_attr( sgl_get_pin_label( $location['name'] ) ),
			esc_attr( $location['name'] ),
			esc_attr( $location['address'] ),
			esc_attr( $location['phone'] )
		);
	}
}

if ( ! function_exists( 'sgl_render_card' ) ) {
	/**
	 * Render a single location card.
	 *
	 * @param array $location     Parsed location row.
	 * @param int   $index        Location index.
	 * @param bool  $is_duplicate Whether this is a marquee duplicate (non-focusable).
	 * @return string
	 */
	function sgl_render_card( $location, $index, $is_duplicate = false ) {
		$phone_markup = '';
		$tabindex     = $is_duplicate ? ' tabindex="-1"' : '';

		if ( ! empty( $location['phone'] ) ) {
			$tel_href = sgl_format_phone_link( $location['phone'] );

			if ( $tel_href ) {
				$phone_markup = sprintf(
					'<p class="sgl-card__phone"><a href="%1$s"%4$s aria-label="%3$s">p: %2$s</a></p>',
					esc_url( $tel_href ),
					esc_html( $location['phone'] ),
					esc_attr(
						sprintf(
							/* translators: %s: phone number */
							__( 'Phone: %s', 'salient-globe-locations' ),
							$location['phone']
						)
					),
					$tabindex
				);
			} else {
				$phone_markup = sprintf(
					'<p class="sgl-card__phone">p: %s</p>',
					esc_html( $location['phone'] )
				);
			}
		}

		$address_markup = '';

		if ( ! empty( $location['address'] ) ) {
			$address_markup = sprintf(
				'<span class="sgl-card__address">%s</span>',
				esc_html( $location['address'] )
			);
		}

		$button_tabindex = $is_duplicate ? ' tabindex="-1"' : '';

		return sprintf(
			'<article class="sgl-card" role="listitem" data-index="%1$d"><button type="button" class="sgl-card__select" data-index="%1$d" data-name="%4$s" data-address="%5$s" data-phone="%6$s" aria-pressed="false"%8$s><span class="sgl-card__name">%2$s</span>%3$s%7$s</button></article>',
			(int) $index,
			esc_html( $location['name'] ),
			$address_markup,
			esc_attr( $location['name'] ),
			esc_attr( $location['address'] ),
			esc_attr( $location['phone'] ),
			$phone_markup,
			$button_tabindex
		);
	}
}
