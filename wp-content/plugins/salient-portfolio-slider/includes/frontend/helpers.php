<?php
/**
 * Frontend helpers.
 *
 * @package Salient_Portfolio_Slider
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'salient_portfolio_slider_resolve_image_url' ) ) {
	/**
	 * Resolve an attachment ID or URL to an image URL.
	 *
	 * @param string $src  Attachment ID or URL.
	 * @param string $size Image size.
	 * @return string
	 */
	function salient_portfolio_slider_resolve_image_url( $src, $size = 'full' ) {
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

if ( ! function_exists( 'salient_portfolio_slider_slide_image_src' ) ) {
	/**
	 * Get the raw slide background image source with legacy field fallbacks.
	 *
	 * @param array $slide Slide repeater item.
	 * @return string
	 */
	function salient_portfolio_slider_slide_image_src( $slide ) {
		if ( ! empty( $slide['image'] ) ) {
			return trim( (string) $slide['image'] );
		}

		if ( ! empty( $slide['image_desktop'] ) ) {
			return trim( (string) $slide['image_desktop'] );
		}

		if ( ! empty( $slide['image_tablet'] ) ) {
			return trim( (string) $slide['image_tablet'] );
		}

		if ( ! empty( $slide['image_mobile'] ) ) {
			return trim( (string) $slide['image_mobile'] );
		}

		return '';
	}
}

if ( ! function_exists( 'salient_portfolio_slider_slide_image_url' ) ) {
	/**
	 * Resolve a slide background image URL.
	 *
	 * @param array $slide Slide repeater item.
	 * @return string
	 */
	function salient_portfolio_slider_slide_image_url( $slide ) {
		return salient_portfolio_slider_resolve_image_url(
			salient_portfolio_slider_slide_image_src( $slide ),
			'full'
		);
	}
}

if ( ! function_exists( 'salient_portfolio_slider_slide_image_alt' ) ) {
	/**
	 * Get accessible text for a slide background image.
	 *
	 * @param array $slide Slide repeater item.
	 * @return string
	 */
	function salient_portfolio_slider_slide_image_alt( $slide ) {
		if ( ! empty( $slide['image_alt'] ) ) {
			return trim( (string) $slide['image_alt'] );
		}

		$src = salient_portfolio_slider_slide_image_src( $slide );

		if ( preg_match( '/^\d+$/', $src ) ) {
			$attachment_alt = get_post_meta( (int) $src, '_wp_attachment_image_alt', true );
			if ( is_string( $attachment_alt ) && '' !== trim( $attachment_alt ) ) {
				return trim( $attachment_alt );
			}
		}

		return '';
	}
}

if ( ! function_exists( 'salient_portfolio_slider_slide_bg_a11y_attrs' ) ) {
	/**
	 * Build accessibility attributes for the slide background element.
	 *
	 * @param array $slide Slide repeater item.
	 * @return string
	 */
	function salient_portfolio_slider_slide_bg_a11y_attrs( $slide ) {
		$alt = salient_portfolio_slider_slide_image_alt( $slide );

		if ( '' !== $alt ) {
			return ' role="img" aria-label="' . esc_attr( $alt ) . '"';
		}

		return ' role="presentation" aria-hidden="true"';
	}
}

if ( ! function_exists( 'salient_portfolio_slider_slide_a11y_label' ) ) {
	/**
	 * Build an accessible label for a slide group.
	 *
	 * @param int    $index       Zero-based slide index.
	 * @param int    $slide_count Total slides.
	 * @param string $title       Slide title.
	 * @return string
	 */
	function salient_portfolio_slider_slide_a11y_label( $index, $slide_count, $title ) {
		$position = $index + 1;

		if ( '' !== trim( $title ) ) {
			/* translators: 1: slide number, 2: total slides, 3: slide title */
			return sprintf(
				__( 'Slide %1$d of %2$d: %3$s', 'salient-portfolio-slider' ),
				$position,
				$slide_count,
				$title
			);
		}

		/* translators: 1: slide number, 2: total slides */
		return sprintf(
			__( 'Slide %1$d of %2$d', 'salient-portfolio-slider' ),
			$position,
			$slide_count
		);
	}
}

if ( ! function_exists( 'salient_portfolio_slider_default_image_url' ) ) {
	/**
	 * Fallback image when no desktop image is set.
	 *
	 * @return string
	 */
	function salient_portfolio_slider_default_image_url() {
		if ( defined( 'SALIENT_PORTFOLIO_PLUGIN_PATH' ) ) {
			return SALIENT_PORTFOLIO_PLUGIN_PATH . '/img/no-portfolio-item-small.jpg';
		}

		return '';
	}
}

if ( ! function_exists( 'salient_portfolio_slider_parse_slide_logos' ) ) {
	/**
	 * Parse and normalize logo repeater rows for a slide.
	 *
	 * @param array $slide Slide repeater item.
	 * @return array<int, array{image:string,title:string,link:string}>
	 */
	function salient_portfolio_slider_parse_slide_logos( $slide ) {
		if ( ! is_array( $slide ) || empty( $slide['logos'] ) ) {
			return array();
		}

		if ( ! function_exists( 'vc_param_group_parse_atts' ) ) {
			return array();
		}

		$logos = vc_param_group_parse_atts( $slide['logos'] );

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

if ( ! function_exists( 'salient_portfolio_slider_render_logo_item' ) ) {
	/**
	 * Render a single logo item for the infinite scroll strip.
	 *
	 * @param array $logo Logo row with image, title, and link keys.
	 * @return string
	 */
	function salient_portfolio_slider_render_logo_item( $logo ) {
		$image_url = salient_portfolio_slider_resolve_image_url( $logo['image'], 'medium' );

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
				: esc_html__( 'Logo link', 'salient-portfolio-slider' );

			return '<a href="' . esc_url( $logo['link'] ) . '" aria-label="' . esc_attr( $link_label ) . '">' . $img . '</a>';
		}

		return $img;
	}
}

if ( ! function_exists( 'salient_portfolio_slider_render_logo_scroll' ) ) {
	/**
	 * Render the infinite CSS scroll logo strip.
	 *
	 * @param array $logos Parsed logo rows.
	 * @return string
	 */
	function salient_portfolio_slider_render_logo_scroll( $logos ) {
		if ( empty( $logos ) ) {
			return '';
		}

		$items = '';

		foreach ( $logos as $logo ) {
			$item = salient_portfolio_slider_render_logo_item( $logo );

			if ( '' !== $item ) {
				$items .= $item;
			}
		}

		if ( '' === $items ) {
			return '';
		}

		$markup  = '<div class="sps-logo-scroll scroll-container" aria-label="' . esc_attr__( 'Partner logos', 'salient-portfolio-slider' ) . '">';
		$markup .= '<div class="scroll-track">';
		$markup .= '<div class="scroll-group">' . $items . '</div>';
		$markup .= '<div class="scroll-group" aria-hidden="true">' . $items . '</div>';
		$markup .= '</div>';
		$markup .= '</div>';

		return $markup;
	}
}

if ( ! function_exists( 'salient_portfolio_slider_sanitize_slide_text' ) ) {
	/**
	 * Sanitize slide text block output.
	 *
	 * @param string $text Raw slide text.
	 * @return string
	 */
	function salient_portfolio_slider_sanitize_slide_text( $text ) {
		if ( empty( $text ) ) {
			return '';
		}

		$text = wp_kses_post( $text );

		if ( false === stripos( $text, '<p' ) ) {
			$text = wpautop( $text );
		}

		return $text;
	}
}
