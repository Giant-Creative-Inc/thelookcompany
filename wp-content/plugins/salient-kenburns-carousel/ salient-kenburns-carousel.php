<?php
/**
 * Plugin Name: Salient - WPBakery Ken Burns Carousel
 * Description: WPBakery element for Salient: full-width background carousel with Ken Burns zoom + fade, accessible and AODA-friendly.
 * Version: 1.0.1
 * Author: Giant Creative Inc
 *
 * @package Salient_KenBurns_Carousel
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Salient_KenBurns_Carousel' ) ) {

	/**
	 * Main plugin class.
	 *
	 * @since 1.0.0
	 */
	final class Salient_KenBurns_Carousel {

		/**
		 * Plugin version.
		 *
		 * @var string
		 */
		const VERSION = '1.0.0';

		/**
		 * Shortcode base.
		 *
		 * @var string
		 */
		const SHORTCODE = 'skbc_kenburns_carousel';

		/**
		 * Asset handles.
		 *
		 * @var string
		 */
		const STYLE_HANDLE  = 'skbc-kenburns-carousel';
		const SCRIPT_HANDLE = 'skbc-kenburns-carousel';

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'register_vc_element' ) );
			add_shortcode( self::SHORTCODE, array( $this, 'render_shortcode' ) );

			// Register assets early; enqueue only when shortcode renders.
			add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
		}

		/**
		 * Register CSS/JS with WordPress (but do not enqueue yet).
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function register_assets() {
			$base_url = plugin_dir_url( __FILE__ );

			wp_register_style(
				self::STYLE_HANDLE,
				$base_url . 'assets/css/kenburns-carousel.css',
				array(),
				self::VERSION
			);

			wp_register_script(
				self::SCRIPT_HANDLE,
				$base_url . 'assets/js/kenburns-carousel.js',
				array(),
				self::VERSION,
				true
			);
		}

		/**
		 * Register WPBakery element via vc_map (only if WPBakery is active).
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function register_vc_element() {
			if ( ! function_exists( 'vc_map' ) ) {
				return;
			}

			vc_map(
				array(
					'name'        => __( 'Ken Burns Background Carousel (AODA)', 'skbc' ),
					'base'        => self::SHORTCODE,
					'category'    => __( 'Content', 'skbc' ),
					'description' => __( 'Full-width background carousel with Ken Burns zoom + fade.', 'skbc' ),
					'icon'        => 'icon-wpb-images-stack',
					'params'      => array(
						array(
							'type'        => 'textfield',
							'heading'     => __( 'Accessible Label', 'skbc' ),
							'param_name'  => 'aria_label',
							'value'       => __( 'Featured content carousel', 'skbc' ),
							'description' => __( 'Used by screen readers to describe this carousel region.', 'skbc' ),
						),

						array(
							'type'        => 'textfield',
							'heading'     => __( 'Slide Display Duration (ms)', 'skbc' ),
							'param_name'  => 'slide_duration',
							'value'       => '6000',
							'description' => __( 'How long each slide stays visible before moving to the next (in milliseconds). Example: 6000 = 6 seconds.', 'skbc' ),
						),

						array(
							'type'        => 'textfield',
							'heading'     => __( 'Fade Transition Duration (ms)', 'skbc' ),
							'param_name'  => 'fade_duration',
							'value'       => '800',
							'description' => __( 'Fade time between slides (in milliseconds).', 'skbc' ),
						),

						array(
							'type'        => 'textfield',
							'heading'     => __( 'Ken Burns Zoom Duration (ms)', 'skbc' ),
							'param_name'  => 'kenburns_duration',
							'value'       => '8000',
							'description' => __( 'Duration of the background zoom animation per slide (in milliseconds).', 'skbc' ),
						),

						array(
							'type'        => 'dropdown',
							'heading'     => __( 'Autoplay', 'skbc' ),
							'param_name'  => 'autoplay',
							'value'       => array(
								__( 'On', 'skbc' )  => '1',
								__( 'Off', 'skbc' ) => '0',
							),
							'std'         => '1',
							'description' => __( 'Autoplay will start automatically, but users can pause.', 'skbc' ),
						),

						array(
							'type'        => 'param_group',
							'heading'     => __( 'Slides', 'skbc' ),
							'param_name'  => 'slides',
							'description' => __( 'Add one or more slides.', 'skbc' ),
							'params'      => array(
								array(
									'type'        => 'attach_image',
									'heading'     => __( 'Background Image', 'skbc' ),
									'param_name'  => 'bg_image_id',
									'description' => __( 'Used as the full-width background for the slide.', 'skbc' ),
								),
								array(
									'type'       => 'textfield',
									'heading'    => __( 'Title', 'skbc' ),
									'param_name' => 'title',
								),
								array(
									'type'       => 'textarea',
									'heading'    => __( 'Content', 'skbc' ),
									'param_name' => 'content',
								),
								array(
									'type'        => 'textfield',
									'heading'     => __( 'Button Text', 'skbc' ),
									'param_name'  => 'button_text',
									'description' => __( 'Leave blank to hide the button.', 'skbc' ),
								),
								array(
									'type'        => 'vc_link',
									'heading'     => __( 'Button Link', 'skbc' ),
									'param_name'  => 'button_link',
									'description' => __( 'Set a URL and (optional) target.', 'skbc' ),
								),
							),
						),
					),
				)
			);
		}

		/**
		 * Shortcode renderer.
		 *
		 * Outputs:
		 * - Full-width region with slides
		 * - Titles: first slide is H1, the rest are H2
		 * - AODA/WCAG-friendly controls (prev/next/pause), keyboard nav, ARIA
		 * - Loads CSS/JS only when shortcode is used on the page
		 *
		 * @since 1.0.0
		 *
		 * @param array  $atts    Shortcode attributes.
		 * @param string $content Shortcode content (unused).
		 * @return string
		 */
		public function render_shortcode( $atts, $content = '' ) {
			$atts = shortcode_atts(
				array(
					'aria_label'        => __( 'Featured content carousel', 'skbc' ),
					'slide_duration'    => '6000',
					'fade_duration'     => '800',
					'kenburns_duration' => '8000',
					'autoplay'          => '1',
					'slides'            => '',
				),
				$atts,
				self::SHORTCODE
			);

			// Enqueue only when this shortcode is actually rendered.
			wp_enqueue_style( self::STYLE_HANDLE );
			wp_enqueue_script( self::SCRIPT_HANDLE );

			// Sanitize timing values.
			$slide_duration    = max( 1000, absint( $atts['slide_duration'] ) );
			$fade_duration     = max( 200, absint( $atts['fade_duration'] ) );
			$kenburns_duration = max( 500, absint( $atts['kenburns_duration'] ) );
			$autoplay          = ( '1' === (string) $atts['autoplay'] ) ? 'true' : 'false';

			// Parse slides from param_group.
			$slides = array();
			if ( function_exists( 'vc_param_group_parse_atts' ) ) {
				$slides = vc_param_group_parse_atts( $atts['slides'] );
			}

			if ( empty( $slides ) || ! is_array( $slides ) ) {
				return '';
			}

			// Unique ID for ARIA relationships.
			$uid = 'skbc-' . wp_generate_uuid4();

			// Provide data to JS via dataset on the root element (simple + no inline scripts required).
			$root_attrs = array(
				'id'                     => $uid,
				'class'                  => 'skbc-carousel',
				'data-slide-duration'    => (string) $slide_duration,
				'data-fade-duration'     => (string) $fade_duration,
				'data-kenburns-duration' => (string) $kenburns_duration,
				'data-autoplay'          => $autoplay,
			);

			// Build root attribute string safely.
			$root_attr_str = '';
			foreach ( $root_attrs as $key => $value ) {
				$root_attr_str .= sprintf( ' %s="%s"', esc_attr( $key ), esc_attr( $value ) );
			}

			ob_start();
			?>
			<section
				<?php echo $root_attr_str; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				role="region"
				aria-roledescription="carousel"
				aria-label="<?php echo esc_attr( $atts['aria_label'] ); ?>"
			>
				<div class="skbc-carousel__inner">
					<!--
						Live region:
						- Announces slide changes to screen reader users without being too noisy.
						- "polite" means it waits until the user is idle.
					-->
					<p class="skbc-sr-only" aria-live="polite" aria-atomic="true" data-skbc-live></p>

					<!-- Slides -->
					<ul class="skbc-carousel__slides" data-skbc-slides>
						<?php
						$index = 0;

						foreach ( $slides as $slide ) {
							$bg_image_id = isset( $slide['bg_image_id'] ) ? absint( $slide['bg_image_id'] ) : 0;
							$title       = isset( $slide['title'] ) ? (string) $slide['title'] : '';
							$body_html   = isset( $slide['content'] ) ? (string) $slide['content'] : '';

							$button_text = isset( $slide['button_text'] ) ? (string) $slide['button_text'] : '';
							$button_link = isset( $slide['button_link'] ) ? (string) $slide['button_link'] : '';

							// Build responsive image data.
              $bg_src    = '';
              $bg_srcset = '';
              $bg_sizes  = '100vw'; // full-width carousel

              if ( $bg_image_id ) {
                $image_src = wp_get_attachment_image_src( $bg_image_id, 'full' );
                if ( is_array( $image_src ) && ! empty( $image_src[0] ) ) {
                  $bg_src = $image_src[0];
                }

                $bg_srcset = wp_get_attachment_image_srcset( $bg_image_id, 'full' );
              }


							// Parse vc_link into a normalized link array.
							$link = array(
								'url'    => '',
								'target' => '',
								'title'  => '',
							);

							if ( function_exists( 'vc_build_link' ) && ! empty( $button_link ) ) {
								$built = vc_build_link( $button_link );
								if ( is_array( $built ) ) {
									$link['url']    = isset( $built['url'] ) ? $built['url'] : '';
									$link['target'] = isset( $built['target'] ) ? $built['target'] : '';
									$link['title']  = isset( $built['title'] ) ? $built['title'] : '';
								}
							}

							// Title tags: first slide uses H1, the rest use H2.
							$title_tag = ( 0 === $index ) ? 'h1' : 'h2';

							// Slide IDs for aria relationships.
							$slide_id = $uid . '-slide-' . $index;

							// Determine active state for first slide (others start hidden).
							$is_active  = ( 0 === $index );
							$aria_hidden = $is_active ? 'false' : 'true';
							$tabindex    = $is_active ? '0' : '-1';
							?>
							<li
								id="<?php echo esc_attr( $slide_id ); ?>"
								class="skbc-slide<?php echo $is_active ? ' is-active' : ''; ?>"
								role="group"
								aria-roledescription="slide"
								aria-label="<?php echo esc_attr( ( $index + 1 ) . ' / ' . count( $slides ) ); ?>"
								aria-hidden="<?php echo esc_attr( $aria_hidden ); ?>"
								tabindex="<?php echo esc_attr( $tabindex ); ?>"
								data-skbc-slide
								data-skbc-title="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>"
							>
								<!-- Background layer (Ken Burns animates here). -->
								<div class="skbc-slide__bg" aria-hidden="true">
                  <?php
                  echo wp_get_attachment_image(
                    $bg_image_id,
                    'full',
                    false,
                    array(
                      'class'         => 'skbc-slide__img',
                      'alt'           => '',
                      'sizes'         => '100vw',
                      'loading'       => ( 0 === $index ) ? 'eager' : 'lazy',
                      'decoding'      => 'async',
                      'fetchpriority' => ( 0 === $index ) ? 'high' : 'auto',
                    )
                  );
                  ?>
                </div>
								<!-- Background layer Colour Overlay -->
								<div class="skbc-slide__color-bg" aria-hidden="true"></div>


								<!-- Content wrapper: full width background, but content respects theme container width. -->
								<div class="skbc-slide__content">
									<div class="skbc-slide__content-inner container normal-container">
										<?php if ( '' !== trim( $title ) ) : ?>
											<<?php echo esc_html( $title_tag ); ?> class="skbc-slide__title">
												<?php echo esc_html( $title ); ?>
											</<?php echo esc_html( $title_tag ); ?>>
										<?php endif; ?>

										<?php if ( '' !== trim( wp_strip_all_tags( $body_html ) ) ) : ?>
											<div class="skbc-slide__text">
												<?php
												// WPBakery content may include markup; keep it safe.
												echo wp_kses_post( $body_html );
												?>
											</div>
										<?php endif; ?>

										<?php if ( '' !== trim( $button_text ) && ! empty( $link['url'] ) ) : ?>
											<a
												class="skbc-slide__btn"
												href="<?php echo esc_url( $link['url'] ); ?>"
												<?php echo ( ! empty( $link['target'] ) ) ? ' target="' . esc_attr( $link['target'] ) . '"' : ''; ?>
												<?php echo ( '_blank' === $link['target'] ) ? ' rel="noopener noreferrer"' : ''; ?>
											>
												<?php echo esc_html( $button_text ); ?>
											</a>
										<?php endif; ?>
									</div>
								</div>
							</li>
							<?php
							$index++;
						}
						?>
					</ul>

          <!-- Dot navigation -->
          <div class="skbc-carousel__dots" aria-label="<?php echo esc_attr__( 'Slide navigation', 'skbc' ); ?>">
            <?php for ( $i = 0; $i < count( $slides ); $i++ ) : ?>
              <button
                type="button"
                class="skbc-carousel__dot<?php echo ( 0 === $i ) ? ' is-active' : ''; ?>"
                data-skbc-dot
                data-skbc-index="<?php echo esc_attr( (string) $i ); ?>"
                aria-label="<?php echo esc_attr( sprintf( __( 'Go to slide %d', 'skbc' ), $i + 1 ) ); ?>"
                aria-current="<?php echo ( 0 === $i ) ? 'true' : 'false'; ?>"
              ></button>
            <?php endfor; ?>
          </div>

				</div>
			</section>
			<?php

			return (string) ob_get_clean();
		}
	}

	new Salient_KenBurns_Carousel();
}
