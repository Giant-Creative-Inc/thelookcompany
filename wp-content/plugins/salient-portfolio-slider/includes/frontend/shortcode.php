<?php
/**
 * Portfolio slider shortcode.
 *
 * @package Salient_Portfolio_Slider
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'salient_portfolio_slider_render' ) ) {
	/**
	 * Render the fullscreen zoom portfolio slider.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return string
	 */
	function salient_portfolio_slider_render( $atts, $content = null ) {
		$atts = shortcode_atts(
			array(
				'slider_controls'          => 'arrows',
				'slider_text_color'        => 'light',
				'slider_heading_structure' => 'default',
				'overlay_strength'         => '0',
				'autorotate'               => '',
				'custom_link_text'         => '',
				'slider_label'             => '',
				'slides'                   => '',
			),
			$atts,
			'salient_portfolio_slider'
		);

		$slides = array();

		if ( function_exists( 'vc_param_group_parse_atts' ) && ! empty( $atts['slides'] ) ) {
			$slides = vc_param_group_parse_atts( $atts['slides'] );
		}

		if ( ! empty( $slides ) ) {
			$slides = array_values(
				array_filter(
					$slides,
					function ( $slide ) {
						if ( ! is_array( $slide ) ) {
							return false;
						}

						$image      = salient_portfolio_slider_slide_image_src( $slide );
						$title      = isset( $slide['title'] ) ? trim( (string) $slide['title'] ) : '';
						$text       = isset( $slide['text'] ) ? trim( wp_strip_all_tags( (string) $slide['text'] ) ) : '';
						$button_url = isset( $slide['button_url'] ) ? trim( (string) $slide['button_url'] ) : '';

						return ! ( '' === $image && '' === $title && '' === $text && '' === $button_url );
					}
				)
			);
		}

		if ( empty( $slides ) ) {
			return '';
		}

		Salient_Portfolio_Slider::get_instance()->mark_slider_on_page();

		$slide_count          = count( $slides );
		$slider_controls_attr = $atts['slider_controls'];
		$autorotate_attr      = $atts['autorotate'];

		if ( $slide_count <= 1 ) {
			$slider_controls_attr = 'arrows';
			$autorotate_attr      = '';
		}

		$slider_label = ! empty( $atts['slider_label'] )
			? $atts['slider_label']
			: esc_html__( 'Portfolio slider', 'salient-portfolio-slider' );

		$default_button_text = ! empty( $atts['custom_link_text'] )
			? $atts['custom_link_text']
			: esc_html__( 'View Project', 'salient-portfolio-slider' );

		$markup      = '';
		$slide_index = 0;

		$markup .= '<div class="nectar_fullscreen_zoom_recent_projects" role="region" aria-roledescription="carousel" aria-label="' . esc_attr( $slider_label ) . '" data-autorotate="' . esc_attr( $autorotate_attr ) . '" data-slider-text-color="' . esc_attr( $atts['slider_text_color'] ) . '" data-slider-controls="' . esc_attr( $slider_controls_attr ) . '" data-overlay-opacity="' . esc_attr( $atts['overlay_strength'] ) . '">';
		$markup .= '<div class="sps-carousel-live screen-reader-text" aria-live="polite" aria-atomic="true"></div>';
		$markup .= '<div class="project-slides">';

		foreach ( $slides as $slide ) {
			$project_img = salient_portfolio_slider_slide_image_url( $slide );

			if ( empty( $project_img ) ) {
				$project_img = salient_portfolio_slider_default_image_url();
			}

			if ( function_exists( 'nectar_ssl_check' ) && ! empty( $project_img ) ) {
				$project_img = nectar_ssl_check( $project_img );
			}

			$active_class   = ( 0 === $slide_index ) ? 'current' : 'next';
			$aria_hidden    = ( 0 === $slide_index ) ? 'false' : 'true';
			$title          = isset( $slide['title'] ) ? $slide['title'] : '';
			$slide_label    = salient_portfolio_slider_slide_a11y_label( $slide_index, $slide_count, $title );
			$bg_a11y_attrs  = salient_portfolio_slider_slide_bg_a11y_attrs( $slide );
			$text_markup    = '';

			if ( ! empty( $slide['text'] ) ) {
				$text_markup = salient_portfolio_slider_sanitize_slide_text( $slide['text'] );
			}

			$button_text = ! empty( $slide['button_text'] ) ? $slide['button_text'] : $default_button_text;
			$button_url  = ! empty( $slide['button_url'] ) ? $slide['button_url'] : '';
			$link_markup = '';

			if ( ! empty( $button_url ) ) {
				$link_markup = ' <a href="' . esc_url( $button_url ) . '">' . esc_html( $button_text ) . '</a>';
			}

			$markup .= '<section class="project-slide ' . esc_attr( $active_class ) . '" role="group" aria-roledescription="slide" aria-label="' . esc_attr( $slide_label ) . '" aria-hidden="' . esc_attr( $aria_hidden ) . '" data-sps-slide-index="' . esc_attr( (string) $slide_index ) . '">';
			$markup .= '<div class="bg-outer-wrap"><div class="bg-outer"><div class="bg-inner-wrap" style="background-color: #000000;"><div class="slide-bg" style="background-image:url(' . esc_attr( $project_img ) . ')"' . $bg_a11y_attrs . '></div></div></div></div>';
			$markup .= '<div class="project-info"><div class="container normal-container">';

			if ( empty( $atts['slider_heading_structure'] ) || 'default' === $atts['slider_heading_structure'] ) {
				$markup .= '<h1 class="project-slide__title">' . esc_html( $title ) . '</h1> ';
			} elseif ( 'first_h1' === $atts['slider_heading_structure'] ) {
				if ( 0 === $slide_index ) {
					$markup .= '<h1 class="project-slide__title">' . esc_html( $title ) . '</h1> ';
				} else {
					$markup .= '<h2 class="project-slide__title" data-inherit-heading-family="h1">' . esc_html( $title ) . '</h2> ';
				}
			} else {
				$markup .= '<h2 class="project-slide__title">' . esc_html( $title ) . '</h2> ';
			}

			$logo_markup = salient_portfolio_slider_render_logo_scroll(
				salient_portfolio_slider_parse_slide_logos( $slide )
			);

			$markup .= $text_markup;

			if ( $link_markup || $logo_markup ) {
				$markup .= '<div class="sps-slide-actions">';
				$markup .= $link_markup;
				$markup .= $logo_markup;
				$markup .= '</div>';
			}

			$markup .= '</div></div>';
			$markup .= '</section>';

			++$slide_index;
		}

		$controls_markup = '';

		if ( $slide_count > 1 ) {
			$show_arrows = ( 'both' === $atts['slider_controls'] || 'arrows' === $atts['slider_controls'] );

			if ( $show_arrows ) {
				$controls_markup .= '<button type="button" class="prev" aria-label="' . esc_attr__( 'Previous slide', 'salient-portfolio-slider' ) . '"><i class="fa fa-angle-left" aria-hidden="true"></i></button>';
				$controls_markup .= '<button type="button" class="next" aria-label="' . esc_attr__( 'Next slide', 'salient-portfolio-slider' ) . '"><i class="fa fa-angle-right" aria-hidden="true"></i></button>';
			}

			if ( ! empty( $autorotate_attr ) ) {
				$controls_markup .= '<button type="button" class="sps-carousel-pause" aria-pressed="false" aria-label="' . esc_attr__( 'Pause slideshow', 'salient-portfolio-slider' ) . '">';
				$controls_markup .= '<i class="fa fa-pause sps-pause-icon" aria-hidden="true"></i>';
				$controls_markup .= '<i class="fa fa-play sps-play-icon" aria-hidden="true"></i>';
				$controls_markup .= '<span class="screen-reader-text">' . esc_html__( 'Pause slideshow', 'salient-portfolio-slider' ) . '</span>';
				$controls_markup .= '</button>';
			}

			if ( '' !== $controls_markup ) {
				$controls_markup = '<div class="zoom-slider-controls">' . $controls_markup . '</div>';
			}
		}

		$markup .= '</div><div class="container normal-container">' . $controls_markup . '</div></div><!--nectar_fullscreen_zoom_recent_projects-->';

		return $markup;
	}
}

add_shortcode( 'salient_portfolio_slider', 'salient_portfolio_slider_render' );
