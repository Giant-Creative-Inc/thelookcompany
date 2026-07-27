<?php
/**
 * Main plugin class.
 *
 * @package Salient_Marquee_Slider
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Salient Marquee Slider bootstrap.
 */
class Salient_Marquee_Slider {

	/**
	 * Singleton instance.
	 *
	 * @var Salient_Marquee_Slider|false
	 */
	private static $instance = false;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $plugin_version = SALIENT_MARQUEE_SLIDER_VERSION;

	/**
	 * Whether the marquee shortcode rendered on this request.
	 *
	 * @var bool
	 */
	private $marquee_on_page = false;

	/**
	 * Get singleton instance.
	 *
	 * @return Salient_Marquee_Slider
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
		add_action( 'wp_footer', array( $this, 'late_enqueue_assets' ), 1 );
		add_action( 'init', array( $this, 'init' ), 20 );
	}

	/**
	 * Register WPBakery lean map before vc_before_init fires.
	 */
	public function register_wpbakery() {
		if ( class_exists( 'WPBakeryVisualComposerAbstract' ) ) {
			require_once SALIENT_MARQUEE_SLIDER_ROOT_DIR_PATH . 'includes/wpbakery/wpbakery-elements.php';
		}
	}

	/**
	 * Load plugin text domain.
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'salient-marquee-slider',
			false,
			dirname( plugin_basename( SALIENT_MARQUEE_SLIDER_ROOT_DIR_PATH . 'salient-marquee-slider.php' ) ) . '/languages'
		);
	}

	/**
	 * Load frontend and WPBakery integrations.
	 */
	public function init() {
		require_once SALIENT_MARQUEE_SLIDER_ROOT_DIR_PATH . 'includes/frontend/helpers.php';
		require_once SALIENT_MARQUEE_SLIDER_ROOT_DIR_PATH . 'includes/frontend/shortcode.php';
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
	 * Show admin notices for missing dependencies.
	 */
	public function dependency_notices() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		if ( ! $this->has_wpbakery() ) {
			echo '<div class="notice notice-warning"><p>';
			echo esc_html__(
				'Salient Marquee Slider requires WPBakery Page Builder to register its element.',
				'salient-marquee-slider'
			);
			echo '</p></div>';
		}
	}

	/**
	 * Whether a content string contains the marquee slider shortcode.
	 *
	 * @param string $content Content to search.
	 * @return bool
	 */
	public function content_contains_marquee( $content ) {
		return is_string( $content ) && stripos( $content, 'salient_marquee_slider' ) !== false;
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
	 * Whether marquee assets should load on the current request.
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
				$this->content_contains_marquee( $post->post_content ) ||
				$this->content_contains_marquee( $portfolio_extra_content )
			) {
				return true;
			}
		}

		if ( class_exists( 'NectarElAssets' ) ) {
			if ( $this->content_contains_marquee( NectarElAssets::$post_content ) ) {
				return true;
			}

			if ( $this->content_contains_marquee( NectarElAssets::$portfolio_content ) ) {
				return true;
			}

			if ( ! empty( NectarElAssets::$templatera_content ) ) {
				foreach ( NectarElAssets::$templatera_content as $template_content ) {
					if ( $this->content_contains_marquee( $template_content ) ) {
						return true;
					}
				}
			}

			if ( ! empty( NectarElAssets::$global_section_locations_content ) ) {
				foreach ( NectarElAssets::$global_section_locations_content as $section_content ) {
					if ( $this->content_contains_marquee( $section_content ) ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Whether marquee assets are needed on this request.
	 *
	 * @return bool
	 */
	public function needs_marquee_assets() {
		return $this->marquee_on_page || $this->should_load_assets();
	}

	/**
	 * Called when the shortcode renders so assets still load on late-discovered pages.
	 */
	public function mark_on_page() {
		$this->marquee_on_page = true;
		$this->enqueue_marquee_assets();
	}

	/**
	 * Register and enqueue marquee CSS.
	 */
	private function enqueue_marquee_assets() {
		if ( ! wp_style_is( 'salient-marquee-slider', 'registered' ) ) {
			wp_register_style(
				'salient-marquee-slider',
				SALIENT_MARQUEE_SLIDER_PLUGIN_PATH . '/includes/frontend/marquee.css',
				array(),
				$this->plugin_version
			);
		}

		wp_enqueue_style( 'salient-marquee-slider' );
	}

	/**
	 * Enqueue assets when the shortcode is discovered after wp_enqueue_scripts.
	 */
	public function late_enqueue_assets() {
		if ( ! $this->marquee_on_page ) {
			return;
		}

		$this->enqueue_marquee_assets();
	}

	/**
	 * Enqueue marquee assets when the element is present.
	 */
	public function enqueue_assets() {
		if ( ! $this->should_load_assets() ) {
			return;
		}

		$this->enqueue_marquee_assets();
	}
}
