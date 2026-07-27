<?php
/**
 * Main plugin class.
 *
 * @package Salient_Globe_Locations
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Salient Globe Locations bootstrap.
 */
class Salient_Globe_Locations {

	/**
	 * Singleton instance.
	 *
	 * @var Salient_Globe_Locations|false
	 */
	private static $instance = false;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $plugin_version = SALIENT_GLOBE_LOCATIONS_VERSION;

	/**
	 * Whether the globe shortcode rendered on this request.
	 *
	 * @var bool
	 */
	private $globe_on_page = false;

	/**
	 * Get singleton instance.
	 *
	 * @return Salient_Globe_Locations
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
			require_once SALIENT_GLOBE_LOCATIONS_ROOT_DIR_PATH . 'includes/wpbakery/wpbakery-elements.php';
		}
	}

	/**
	 * Load plugin text domain.
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'salient-globe-locations',
			false,
			dirname( plugin_basename( SALIENT_GLOBE_LOCATIONS_ROOT_DIR_PATH . 'salient-globe-locations.php' ) ) . '/languages'
		);
	}

	/**
	 * Load frontend and WPBakery integrations.
	 */
	public function init() {
		require_once SALIENT_GLOBE_LOCATIONS_ROOT_DIR_PATH . 'includes/frontend/helpers.php';
		require_once SALIENT_GLOBE_LOCATIONS_ROOT_DIR_PATH . 'includes/frontend/shortcode.php';
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
				'Salient Globe Locations requires WPBakery Page Builder to register its element.',
				'salient-globe-locations'
			);
			echo '</p></div>';
		}
	}

	/**
	 * Whether a content string contains the globe locations shortcode.
	 *
	 * @param string $content Content to search.
	 * @return bool
	 */
	public function content_contains_globe( $content ) {
		return is_string( $content ) && stripos( $content, 'salient_globe_locations' ) !== false;
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
	 * Whether globe locations assets should load on the current request.
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
				$this->content_contains_globe( $post->post_content ) ||
				$this->content_contains_globe( $portfolio_extra_content )
			) {
				return true;
			}
		}

		if ( class_exists( 'NectarElAssets' ) ) {
			if ( $this->content_contains_globe( NectarElAssets::$post_content ) ) {
				return true;
			}

			if ( $this->content_contains_globe( NectarElAssets::$portfolio_content ) ) {
				return true;
			}

			if ( ! empty( NectarElAssets::$templatera_content ) ) {
				foreach ( NectarElAssets::$templatera_content as $template_content ) {
					if ( $this->content_contains_globe( $template_content ) ) {
						return true;
					}
				}
			}

			if ( ! empty( NectarElAssets::$global_section_locations_content ) ) {
				foreach ( NectarElAssets::$global_section_locations_content as $section_content ) {
					if ( $this->content_contains_globe( $section_content ) ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Whether globe assets are needed on this request.
	 *
	 * @return bool
	 */
	public function needs_globe_assets() {
		return $this->globe_on_page || $this->should_load_assets();
	}

	/**
	 * Called when the shortcode renders so assets still load on late-discovered pages.
	 */
	public function mark_globe_on_page() {
		$this->globe_on_page = true;
		$this->enqueue_globe_assets();
	}

	/**
	 * Register globe locations styles and scripts.
	 */
	private function ensure_globe_assets_registered() {
		if ( ! wp_style_is( 'salient-globe-locations', 'registered' ) ) {
			wp_register_style(
				'salient-globe-locations',
				SALIENT_GLOBE_LOCATIONS_PLUGIN_PATH . '/includes/frontend/globe-locations.css',
				array(),
				$this->plugin_version
			);
		}

		if ( ! wp_script_is( 'salient-globe-locations', 'registered' ) ) {
			wp_register_script(
				'salient-globe-locations',
				SALIENT_GLOBE_LOCATIONS_PLUGIN_PATH . '/includes/frontend/globe-locations.js',
				array( 'jquery' ),
				$this->plugin_version,
				true
			);

			wp_localize_script(
				'salient-globe-locations',
				'sglGlobeLocations',
				array(
					'mobileBreakpoint'  => 690,
					'selectedLabel'     => __( 'selected', 'salient-globe-locations' ),
					'clearedLabel'      => __( 'Selection cleared', 'salient-globe-locations' ),
					'phoneLabel'        => __( 'Phone:', 'salient-globe-locations' ),
				)
			);
		}
	}

	/**
	 * Enqueue globe locations assets.
	 */
	private function enqueue_globe_assets() {
		$this->ensure_globe_assets_registered();

		wp_enqueue_style( 'salient-globe-locations' );
		wp_enqueue_script( 'salient-globe-locations' );
	}

	/**
	 * Enqueue assets when the shortcode is discovered after wp_enqueue_scripts.
	 */
	public function late_enqueue_assets() {
		if ( ! $this->globe_on_page ) {
			return;
		}

		$this->enqueue_globe_assets();
	}

	/**
	 * Enqueue assets when our element is present.
	 */
	public function enqueue_assets() {
		if ( ! $this->should_load_assets() ) {
			return;
		}

		$this->enqueue_globe_assets();
	}
}
