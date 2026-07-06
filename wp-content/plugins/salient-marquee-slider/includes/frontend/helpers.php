<?php
/**
 * Frontend helpers.
 *
 * @package Salient_Marquee_Slider
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'salient_marquee_slider_resolve_image_url' ) ) {
	/**
	 * Resolve an attachment ID or URL to an image URL.
	 *
	 * @param string $src  Attachment ID or URL.
	 * @param string $size Image size.
	 * @return string
	 */
	function salient_marquee_slider_resolve_image_url( $src, $size = 'full' ) {
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

if ( ! function_exists( 'salient_marquee_slider_parse_logos' ) ) {
	/**
	 * Parse and normalize logo repeater rows.
	 *
	 * @param string $logos_raw Serialized param_group value.
	 * @return array<int, array{image:string,title:string,link:string}>
	 */
	function salient_marquee_slider_parse_logos( $logos_raw ) {
		if ( empty( $logos_raw ) || ! function_exists( 'vc_param_group_parse_atts' ) ) {
			return array();
		}

		$logos = vc_param_group_parse_atts( $logos_raw );

		if ( empty( $logos ) || ! is_array( $logos ) ) {
			return array();
		}

		$parsed = array();

		foreach ( $logos as $logo ) {
			if ( ! is_array( $logo ) ) {
				continue;
			}

			$image = isset( $logo['image'] ) ? trim( (string) $logo['image'] ) : '';

			if ( '' === $image ) {
				continue;
			}

			$parsed[] = array(
				'image' => $image,
				'title' => isset( $logo['title'] ) ? trim( (string) $logo['title'] ) : '',
				'link'  => isset( $logo['link'] ) ? trim( (string) $logo['link'] ) : '',
			);
		}

		return $parsed;
	}
}

if ( ! function_exists( 'salient_marquee_slider_render_logo_item' ) ) {
	/**
	 * Render a single logo item for the infinite scroll strip.
	 *
	 * @param array $logo Logo row with image, title, and link keys.
	 * @return string
	 */
	function salient_marquee_slider_render_logo_item( $logo ) {
		$image_url = salient_marquee_slider_resolve_image_url( $logo['image'], 'medium' );

		if ( empty( $image_url ) ) {
			return '';
		}

		if ( function_exists( 'nectar_ssl_check' ) ) {
			$image_url = nectar_ssl_check( $image_url );
		}

		$title = ! empty( $logo['title'] ) ? $logo['title'] : '';
		$img   = '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $title ) . '" />';

		if ( ! empty( $logo['link'] ) ) {
			$link_label = '' !== $title
				? $title
				: esc_html__( 'Logo link', 'salient-marquee-slider' );

			return '<a href="' . esc_url( $logo['link'] ) . '" aria-label="' . esc_attr( $link_label ) . '">' . $img . '</a>';
		}

		return $img;
	}
}

if ( ! function_exists( 'salient_marquee_slider_sanitize_max_width' ) ) {
	/**
	 * Sanitize a CSS max-width value.
	 *
	 * @param string $max_width Raw max-width input.
	 * @return string Sanitized value or empty string for full width.
	 */
	function salient_marquee_slider_sanitize_max_width( $max_width ) {
		$max_width = trim( (string) $max_width );

		if ( '' === $max_width ) {
			return '';
		}

		if ( preg_match( '/^\d+(\.\d+)?$/', $max_width ) ) {
			return $max_width . 'px';
		}

		if ( preg_match( '/^\d+(\.\d+)?(px|%|rem|em|vw|vh)$/i', $max_width ) ) {
			return $max_width;
		}

		return '';
	}
}

if ( ! function_exists( 'salient_marquee_slider_sanitize_align' ) ) {
	/**
	 * Sanitize alignment value.
	 *
	 * @param string $align Raw alignment input.
	 * @return string left, center, or right.
	 */
	function salient_marquee_slider_sanitize_align( $align ) {
		$align = strtolower( trim( (string) $align ) );

		if ( in_array( $align, array( 'left', 'center', 'right' ), true ) ) {
			return $align;
		}

		return 'left';
	}
}

if ( ! function_exists( 'salient_marquee_slider_render_marquee' ) ) {
	/**
	 * Render the infinite CSS scroll logo strip.
	 *
	 * @param array $logos Parsed logo rows.
	 * @param array $args  Optional aria_label, max_width, and align keys.
	 * @return string
	 */
	function salient_marquee_slider_render_marquee( $logos, $args = array() ) {
		if ( empty( $logos ) ) {
			return '';
		}

		$args = wp_parse_args(
			is_array( $args ) ? $args : array( 'aria_label' => (string) $args ),
			array(
				'aria_label' => '',
				'max_width'  => '',
				'align'      => 'left',
			)
		);

		$items = '';

		foreach ( $logos as $logo ) {
			$item = salient_marquee_slider_render_logo_item( $logo );

			if ( '' !== $item ) {
				$items .= $item;
			}
		}

		if ( '' === $items ) {
			return '';
		}

		$aria_label = trim( (string) $args['aria_label'] );
		if ( '' === $aria_label ) {
			$aria_label = esc_html__( 'Partner logos', 'salient-marquee-slider' );
		}

		$align      = salient_marquee_slider_sanitize_align( $args['align'] );
		$max_width  = salient_marquee_slider_sanitize_max_width( $args['max_width'] );
		$style_attr = '';

		if ( '' !== $max_width ) {
			$style_attr = ' style="max-width:' . esc_attr( $max_width ) . ';"';
		}

		$wrapper_class = 'sms-marquee-slider sms-marquee-slider--align-' . esc_attr( $align );

		$markup  = '<div class="' . $wrapper_class . '">';
		$markup .= '<div class="sms-logo-scroll scroll-container"' . $style_attr . ' aria-label="' . esc_attr( $aria_label ) . '">';
		$markup .= '<div class="scroll-track">';
		$markup .= '<div class="scroll-group">' . $items . '</div>';
		$markup .= '<div class="scroll-group" aria-hidden="true">' . $items . '</div>';
		$markup .= '</div>';
		$markup .= '</div>';
		$markup .= '</div>';

		return $markup;
	}
}
