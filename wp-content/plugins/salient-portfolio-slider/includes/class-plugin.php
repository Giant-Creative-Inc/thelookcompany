<?php
/**
 * Main plugin class.
 *
 * @package Salient_Portfolio_Slider
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Salient Portfolio Slider bootstrap.
 */
class Salient_Portfolio_Slider {

	/**
	 * Singleton instance.
	 *
	 * @var Salient_Portfolio_Slider|false
	 */
	private static $instance = false;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $plugin_version = SALIENT_PORTFOLIO_SLIDER_VERSION;

	/**
	 * Whether the slider shortcode rendered on this request.
	 *
	 * @var bool
	 */
	private $slider_on_page = false;

	/**
	 * Whether the fallback init inline script was attached.
	 *
	 * @var bool
	 */
	private $fallback_init_added = false;

	/**
	 * Get singleton instance.
	 *
	 * @return Salient_Portfolio_Slider
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'after_setup_theme', array( $this, 'register_wpbakery' ), 0 );
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'admin_notices', array( $this, 'dependency_notices' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 99 );
		add_action( 'wp_enqueue_scripts', array( $this, 'finalize_slider_scripts' ), 999 );
		add_action( 'wp_print_scripts', array( $this, 'finalize_slider_scripts' ), 0 );
		add_action( 'wp_footer', array( $this, 'late_enqueue_assets' ), 1 );
		add_action( 'init', array( $this, 'init' ), 20 );
	}

	/**
	 * Register WPBakery lean map before vc_before_init fires.
	 */
	public function register_wpbakery() {
		if ( class_exists( 'WPBakeryVisualComposerAbstract' ) ) {
			require_once SALIENT_PORTFOLIO_SLIDER_ROOT_DIR_PATH . 'includes/wpbakery/wpbakery-elements.php';
		}
	}

	/**
	 * Load plugin text domain.
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'salient-portfolio-slider',
			false,
			dirname( plugin_basename( SALIENT_PORTFOLIO_SLIDER_ROOT_DIR_PATH . 'salient-portfolio-slider.php' ) ) . '/languages'
		);
	}

	/**
	 * Load frontend and WPBakery integrations.
	 */
	public function init() {
		require_once SALIENT_PORTFOLIO_SLIDER_ROOT_DIR_PATH . 'includes/frontend/helpers.php';
		require_once SALIENT_PORTFOLIO_SLIDER_ROOT_DIR_PATH . 'includes/frontend/shortcode.php';
	}

	/**
	 * Whether WPBakery is available.
	 *
	 * @return bool
	 */
	public function has_wpbakery() {
		return defined( 'WPB_VC_VERSION' ) || function_exists( 'vc_lean_map' );
	}

	/**
	 * Whether salient-portfolio plugin is active.
	 *
	 * @return bool
	 */
	public function has_salient_portfolio() {
		return defined( 'SALIENT_PORTFOLIO_ROOT_DIR_PATH' );
	}

	/**
	 * Whether Salient theme is active.
	 *
	 * @return bool
	 */
	public function has_salient_theme() {
		return defined( 'NECTAR_THEME_NAME' );
	}

	/**
	 * Show admin notices for missing dependencies.
	 */
	public function dependency_notices() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		if ( ! $this->has_wpbakery() ) {
			echo '<div class="notice notice-warning"><p>';
			echo esc_html__(
				'Salient Portfolio Slider requires WPBakery Page Builder to register its element.',
				'salient-portfolio-slider'
			);
			echo '</p></div>';
		}
	}

	/**
	 * Whether a content string contains the portfolio slider shortcode.
	 *
	 * @param string $content Content to search.
	 * @return bool
	 */
	public function content_contains_slider( $content ) {
		return is_string( $content ) && stripos( $content, 'salient_portfolio_slider' ) !== false;
	}

	/**
	 * Whether WPBakery front-end or backend editor is active.
	 *
	 * @return bool
	 */
	public function is_wpbakery_editor() {
		$vc_editable = isset( $_GET['vc_editable'] ) ? sanitize_text_field( wp_unslash( $_GET['vc_editable'] ) ) : '';

		if ( 'true' === $vc_editable ) {
			return true;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['wpb-backend-editor'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Whether portfolio slider assets should load on the current request.
	 *
	 * @return bool
	 */
	public function should_load_assets() {
		if ( $this->is_wpbakery_editor() ) {
			return true;
		}

		global $post;

		if ( $post ) {
			$portfolio_extra_content = get_post_meta( $post->ID, '_nectar_portfolio_extra_content', true );

			if (
				$this->content_contains_slider( $post->post_content ) ||
				$this->content_contains_slider( $portfolio_extra_content )
			) {
				return true;
			}
		}

		if ( class_exists( 'NectarElAssets' ) ) {
			if ( $this->content_contains_slider( NectarElAssets::$post_content ) ) {
				return true;
			}

			if ( $this->content_contains_slider( NectarElAssets::$portfolio_content ) ) {
				return true;
			}

			if ( ! empty( NectarElAssets::$templatera_content ) ) {
				foreach ( NectarElAssets::$templatera_content as $template_content ) {
					if ( $this->content_contains_slider( $template_content ) ) {
						return true;
					}
				}
			}

			if ( ! empty( NectarElAssets::$global_section_locations_content ) ) {
				foreach ( NectarElAssets::$global_section_locations_content as $section_content ) {
					if ( $this->content_contains_slider( $section_content ) ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Whether the current page uses the portfolio slider shortcode.
	 *
	 * @return bool
	 */
	public function using_portfolio_slider_el() {
		return $this->needs_slider_assets() && ! $this->is_wpbakery_editor();
	}

	/**
	 * Whether slider assets are needed on this request.
	 *
	 * @return bool
	 */
	public function needs_slider_assets() {
		return $this->slider_on_page || $this->should_load_assets();
	}

	/**
	 * Called when the shortcode renders so assets still load on late-discovered pages.
	 */
	public function mark_slider_on_page() {
		$this->slider_on_page = true;
		$this->enqueue_portfolio_assets();
	}

	/**
	 * Register fullscreen zoom slider styles and portfolio scripts.
	 */
	private function ensure_portfolio_assets_registered() {
		if ( ! wp_style_is( 'salient-portfolio-slider-zoom', 'registered' ) ) {
			wp_register_style(
				'salient-portfolio-slider-zoom',
				SALIENT_PORTFOLIO_SLIDER_PLUGIN_PATH . '/includes/frontend/fullscreen-zoom-slider.css',
				array(),
				$this->plugin_version
			);
		}

		if ( wp_script_is( 'salient-portfolio-js', 'registered' ) ) {
			return;
		}

		$portfolio_js = WP_PLUGIN_DIR . '/salient-portfolio/js/salient-portfolio.js';

		if ( file_exists( $portfolio_js ) ) {
			wp_register_script(
				'salient-portfolio-js',
				plugins_url( 'salient-portfolio/js/salient-portfolio.js' ),
				array( 'jquery' ),
				defined( 'SALIENT_PORTFOLIO_PLUGIN_VERSION' ) ? SALIENT_PORTFOLIO_PLUGIN_VERSION : $this->plugin_version,
				true
			);

			wp_localize_script(
				'salient-portfolio-js',
				'nectar_theme_info',
				array(
					'using_salient' => defined( 'NECTAR_THEME_NAME' ) ? 'true' : 'false',
				)
			);

			return;
		}

		if ( function_exists( 'salient_portfolio_fallback_assets' ) ) {
			salient_portfolio_fallback_assets();
		}
	}

	/**
	 * Ensure nectar-frontend loads after salient-portfolio-js.
	 */
	public function fix_script_load_order() {
		global $wp_scripts;

		if ( ! isset( $wp_scripts->registered['nectar-frontend'] ) ) {
			return;
		}

		$deps = $wp_scripts->registered['nectar-frontend']->deps;

		if ( ! in_array( 'salient-portfolio-js', $deps, true ) ) {
			$wp_scripts->registered['nectar-frontend']->deps[] = 'salient-portfolio-js';
		}
	}

	/**
	 * Fallback slider init when theme init runs before portfolio JS.
	 */
	public function add_slider_fallback_init() {
		if ( $this->fallback_init_added || ! wp_script_is( 'salient-portfolio-js', 'enqueued' ) ) {
			return;
		}

		$inline_script = "jQuery(function($){if(typeof SalientRecentProjectsFullScreen==='undefined'){return;}\$('.nectar_fullscreen_zoom_recent_projects').each(function(){var \$el=\$(this);if(\$el.data('sps-initialized')){return;}\$el.data('sps-initialized',true);var instance=new SalientRecentProjectsFullScreen(\$el);\$el.data('spsFsInstance',instance);if(window.SalientPortfolioSliderA11y){window.SalientPortfolioSliderA11y.enhance(\$el,instance);}});setTimeout(function(){\$('.nectar_fullscreen_zoom_recent_projects').each(function(){var \$el=\$(this);var instance=\$el.data('spsFsInstance');if(instance&&typeof instance.sliderCalcs==='function'){instance.sliderCalcs();}if(window.SalientPortfolioSliderA11y){window.SalientPortfolioSliderA11y.enhance(\$el,instance);}});},300);});";

		wp_add_inline_script( 'salient-portfolio-js', $inline_script );
		$this->fallback_init_added = true;
	}

	/**
	 * Enqueue Salient portfolio assets required by the fullscreen zoom slider.
	 */
	private function enqueue_portfolio_assets() {
		$this->ensure_portfolio_assets_registered();

		if ( wp_style_is( 'salient-portfolio-slider-zoom', 'registered' ) ) {
			wp_enqueue_style( 'salient-portfolio-slider-zoom' );
		}

		wp_register_style(
			'salient-portfolio-slider-logo-scroll',
			SALIENT_PORTFOLIO_SLIDER_PLUGIN_PATH . '/includes/frontend/logo-scroll.css',
			array(),
			$this->plugin_version
		);
		wp_enqueue_style( 'salient-portfolio-slider-logo-scroll' );

		wp_register_style(
			'salient-portfolio-slider-a11y',
			SALIENT_PORTFOLIO_SLIDER_PLUGIN_PATH . '/includes/frontend/accessibility.css',
			array(),
			$this->plugin_version
		);
		wp_enqueue_style( 'salient-portfolio-slider-a11y' );

		if ( wp_script_is( 'salient-portfolio-js', 'registered' ) ) {
			wp_enqueue_script( 'salient-portfolio-js' );
		}

		wp_register_script(
			'salient-portfolio-slider-a11y',
			SALIENT_PORTFOLIO_SLIDER_PLUGIN_PATH . '/includes/frontend/accessibility.js',
			array( 'jquery', 'salient-portfolio-js' ),
			$this->plugin_version,
			true
		);
		wp_localize_script(
			'salient-portfolio-slider-a11y',
			'spsA11yStrings',
			array(
				'previous'  => esc_html__( 'Previous slide', 'salient-portfolio-slider' ),
				'next'      => esc_html__( 'Next slide', 'salient-portfolio-slider' ),
				'slideOf'   => esc_html__( 'Slide %1$d of %2$d', 'salient-portfolio-slider' ),
				'pause'     => esc_html__( 'Pause slideshow', 'salient-portfolio-slider' ),
				'play'      => esc_html__( 'Play slideshow', 'salient-portfolio-slider' ),
				'goToSlide' => esc_html__( 'Go to slide %1$d of %2$d', 'salient-portfolio-slider' ),
			)
		);
		wp_enqueue_script( 'salient-portfolio-slider-a11y' );
	}

	/**
	 * Fix script order and attach fallback init once portfolio JS is queued.
	 */
	public function finalize_slider_scripts() {
		if ( ! $this->needs_slider_assets() || ! wp_script_is( 'salient-portfolio-js', 'enqueued' ) ) {
			return;
		}

		$this->fix_script_load_order();
		$this->add_slider_fallback_init();
	}

	/**
	 * Enqueue assets when the shortcode is discovered after wp_enqueue_scripts.
	 */
	public function late_enqueue_assets() {
		if ( ! $this->slider_on_page ) {
			return;
		}

		$this->finalize_slider_scripts();
	}

	/**
	 * Enqueue existing Salient portfolio assets when our element is present.
	 */
	public function enqueue_assets() {
		if ( ! $this->should_load_assets() ) {
			return;
		}

		$this->enqueue_portfolio_assets();
	}
}
