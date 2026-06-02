<?php
/**
 * Plugin Name: Salient - Case Study Library (WPBakery Element)
 * Description: Filterable case study library for the "case-study" CPT with market taxonomy button filters.
 * Version: 1.0.0
 * Author: Giant Creative Inc
 *
 * CPT:
 * - case-study
 *
 * Taxonomies:
 * - market
 *
 * Fields:
 * - Post title:      the_title()
 * - Featured image   -> get_post_thumbnail_id()
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Salient_Case_Study_Library {

	const SHORTCODE       = 'scsl_case_study_library';
	const CACHE_PREFIX    = 'scsl_';
	const CACHE_TTL_QUERY = 10 * MINUTE_IN_SECONDS;
	const CACHE_TTL_TERMS = 60 * MINUTE_IN_SECONDS;

	const STYLE_HANDLE  = 'scsl-case-study-library';
	const SCRIPT_HANDLE = 'scsl-case-study-library';

	/**
	 * Bootstrap plugin hooks.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_assets' ) );

		add_action( 'wp_ajax_scsl_filter', array( __CLASS__, 'ajax_filter' ) );
		add_action( 'wp_ajax_nopriv_scsl_filter', array( __CLASS__, 'ajax_filter' ) );

		add_action( 'vc_before_init', array( __CLASS__, 'register_vc_element' ) );

		// Cache invalidation when case studies change.
		add_action( 'save_post_case-study', array( __CLASS__, 'clear_caches' ) );
		add_action( 'trashed_post', array( __CLASS__, 'clear_caches' ) );
		add_action( 'deleted_post', array( __CLASS__, 'clear_caches' ) );
	}

	public static function register_shortcode() {
		add_shortcode( self::SHORTCODE, array( __CLASS__, 'render_shortcode' ) );
	}

	public static function register_assets() {
		$url = plugin_dir_url( __FILE__ );

		wp_register_style(
			self::STYLE_HANDLE,
			$url . 'assets/case-study-library.css',
			array(),
			'1.0.0'
		);

		wp_register_script(
			self::SCRIPT_HANDLE,
			$url . 'assets/case-study-library.js',
			array( 'jquery' ),
			'1.0.0',
			true
		);
	}

	public static function register_vc_element() {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		vc_map( array(
			'name'        => 'Case Study Library',
			'base'        => self::SHORTCODE,
			'category'    => 'Content',
			'description' => 'Filterable case study library with market button filters.',
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => 'Posts per page',
					'param_name'  => 'per_page',
					'description' => 'Default -1 (all). Enter a number to limit.',
				),
				array(
					'type'        => 'textfield',
					'heading'     => 'Eager-load first N thumbnails',
					'param_name'  => 'eager_first',
					'description' => 'Default 3. Helps LCP.',
				),
			),
		) );
	}

	/* =========================================================
	 * Cache invalidation
	 * ========================================================= */

	public static function clear_caches() {
		$ver = (int) get_option( 'scsl_cache_ver', 1 );
		update_option( 'scsl_cache_ver', $ver + 1, false );
	}

	private static function get_cache_ver() {
		return (int) get_option( 'scsl_cache_ver', 1 );
	}

	/* =========================================================
	 * Shortcode render
	 * ========================================================= */

	public static function render_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'per_page'    => '-1',
				'eager_first' => '3',
			),
			$atts,
			self::SHORTCODE
		);

		$per_page    = (int) $atts['per_page'];
		$eager_first = max( 0, absint( $atts['eager_first'] ) );

		wp_enqueue_style( self::STYLE_HANDLE );
		wp_enqueue_script( self::SCRIPT_HANDLE );

		wp_localize_script(
			self::SCRIPT_HANDLE,
			'SCSL',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'scsl_nonce' ),
				'strings' => array(
					'loading'   => 'Loading&hellip;',
					'noResults' => 'No case studies found for that filter.',
				),
				'config'  => array(
					'perPage'    => $per_page,
					'eagerFirst' => $eager_first,
				),
			)
		);

		$market_terms = self::get_market_terms_cached();
		$items        = self::get_case_studies_cached( 0, $per_page );

		ob_start();
		?>
		<div class="scsl" data-scsl>

			<div class="scsl__filters" role="group" aria-label="Filter by market">
				<button
					type="button"
					class="scsl__filter-btn scsl__filter-btn--active"
					data-scsl-market="0"
					aria-pressed="true"
				>All</button>
				<?php foreach ( $market_terms as $t ) : ?>
					<button
						type="button"
						class="scsl__filter-btn"
						data-scsl-market="<?php echo esc_attr( $t->term_id ); ?>"
						aria-pressed="false"
					><?php echo esc_html( $t->name ); ?></button>
				<?php endforeach; ?>
			</div>

			<div class="scsl__status" role="status" aria-live="polite" aria-busy="false" data-scsl-status>
				<span class="scsl__loader" data-scsl-loader hidden>
					<span class="scsl__spinner" aria-hidden="true"></span>
					<span class="scsl__loader-text">Loading&hellip;</span>
				</span>
			</div>

			<div class="scsl__results" data-scsl-results>
				<?php echo self::render_case_studies_html( $items, $eager_first ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- pre-escaped HTML. ?>
			</div>

		</div>
		<?php
		return ob_get_clean();
	}

	/* =========================================================
	 * AJAX
	 * ========================================================= */

	public static function ajax_filter() {
		check_ajax_referer( 'scsl_nonce', 'nonce' );

		$market      = isset( $_POST['market'] ) ? absint( $_POST['market'] ) : 0;
		$per_page    = isset( $_POST['perPage'] ) ? (int) $_POST['perPage'] : -1;
		$eager_first = isset( $_POST['eagerFirst'] ) ? max( 0, absint( $_POST['eagerFirst'] ) ) : 3;

		$items = self::get_case_studies_cached( $market, $per_page );

		wp_send_json_success( array(
			'html' => self::render_case_studies_html( $items, $eager_first ),
		) );
	}

	/* =========================================================
	 * Caching
	 * ========================================================= */

	private static function get_market_terms_cached() {
		$key = self::CACHE_PREFIX . 'market_terms_' . self::get_cache_ver();

		$cached = get_transient( $key );
		if ( false !== $cached ) {
			return $cached;
		}

		$terms = self::query_market_terms();
		set_transient( $key, $terms, self::CACHE_TTL_TERMS );

		return $terms;
	}

	private static function get_case_studies_cached( $market_id, $per_page ) {
		$key = self::CACHE_PREFIX . 'items_' . md5( wp_json_encode( array(
			'ver'    => self::get_cache_ver(),
			'market' => $market_id,
			'per'    => $per_page,
		) ) );

		$cached = get_transient( $key );
		if ( false !== $cached ) {
			return $cached;
		}

		$items = self::query_case_studies( (int) $market_id, (int) $per_page );
		set_transient( $key, $items, self::CACHE_TTL_QUERY );

		return $items;
	}

	/* =========================================================
	 * Queries
	 * ========================================================= */

	/**
	 * Get market terms that have at least one published case-study.
	 */
	private static function query_market_terms() {
		global $wpdb;

		$term_ids = $wpdb->get_col( $wpdb->prepare(
			"SELECT DISTINCT tt.term_id
			FROM {$wpdb->term_taxonomy} tt
			INNER JOIN {$wpdb->term_relationships} tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
			INNER JOIN {$wpdb->posts} p ON p.ID = tr.object_id
			WHERE tt.taxonomy = %s
			  AND p.post_type = %s
			  AND p.post_status = 'publish'",
			'market',
			'case-study'
		) );

		if ( empty( $term_ids ) ) {
			return array();
		}

		$terms = get_terms( array(
			'taxonomy'   => 'market',
			'hide_empty' => false,
			'include'    => $term_ids,
			'orderby'    => 'name',
			'order'      => 'ASC',
		) );

		return is_wp_error( $terms ) ? array() : $terms;
	}

	/**
	 * Query published case studies, optionally filtered by market term ID.
	 */
	private static function query_case_studies( $market_id, $per_page ) {
		$args = array(
			'post_type'      => 'case-study',
			'post_status'    => 'publish',
			'posts_per_page' => ( 0 === $per_page || -1 === $per_page ) ? -1 : $per_page,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'no_found_rows'  => true,
			'fields'         => 'ids',
		);

		if ( $market_id > 0 ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'market',
					'field'    => 'term_id',
					'terms'    => $market_id,
				),
			);
		}

		$q = new WP_Query( $args );

		if ( empty( $q->posts ) ) {
			return array();
		}

		$out = array();

		foreach ( $q->posts as $post_id ) {
			$title = get_the_title( $post_id );

			$thumb_id     = (int) get_post_thumbnail_id( $post_id );
			$thumb_src    = $thumb_id ? (string) wp_get_attachment_image_url( $thumb_id, 'large' ) : '';
			$thumb_srcset = $thumb_id ? (string) wp_get_attachment_image_srcset( $thumb_id, 'large' ) : '';
			$thumb_sizes  = $thumb_id ? (string) wp_get_attachment_image_sizes( $thumb_id, 'large' ) : '';

			$alt = '';
			if ( $thumb_id ) {
				$alt = trim( (string) get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) );
			}
			if ( '' === $alt ) {
				$alt = $title;
			}

			$out[] = array(
				'id'           => (int) $post_id,
				'permalink'    => (string) get_permalink( $post_id ),
				'title'        => (string) $title,
				'thumb_src'    => $thumb_src,
				'thumb_srcset' => $thumb_srcset,
				'thumb_sizes'  => $thumb_sizes,
				'alt'          => (string) $alt,
			);
		}

		return $out;
	}

	/* =========================================================
	 * Rendering
	 * ========================================================= */

	private static function render_case_studies_html( $items, $eager_first ) {
		if ( empty( $items ) ) {
			return '<div class="scsl__empty" role="status">No case studies found for that filter.</div>';
		}

		$html = '<div class="scsl__grid" role="list">';

		foreach ( $items as $i => $item ) {
			$html .= self::render_case_study_card( $item, $i < $eager_first );
		}

		$html .= '</div>';

		return $html;
	}

	private static function render_case_study_card( $it, $is_eager ) {
		$title     = esc_html( $it['title'] );
		$permalink = esc_url( $it['permalink'] );
		$thumb_src = (string) $it['thumb_src'];

		$loading       = $is_eager ? 'eager' : 'lazy';
		$fetchpriority = $is_eager ? 'high' : 'auto';

		if ( $thumb_src ) {
			$img = sprintf(
				'<img class="scsl__img" src="%1$s" %2$s %3$s alt="%4$s" loading="%5$s" fetchpriority="%6$s" decoding="async" />',
				esc_url( $thumb_src ),
				( ! empty( $it['thumb_srcset'] ) ? 'srcset="' . esc_attr( $it['thumb_srcset'] ) . '"' : '' ),
				( ! empty( $it['thumb_sizes'] ) ? 'sizes="' . esc_attr( $it['thumb_sizes'] ) . '"' : '' ),
				esc_attr( $it['alt'] ),
				esc_attr( $loading ),
				esc_attr( $fetchpriority )
			);
		} else {
			$img = '<div class="scsl__img-fallback" aria-hidden="true"></div>';
		}

		$html  = '<article class="scsl__card" role="listitem">';
		$html .= '<a class="scsl__card-img-link" href="' . $permalink . '" tabindex="-1" aria-hidden="true">';
    $html .= '<span class="scsl__tile-caption scsl__sr-only">' . $title . '</span>';
		$html .= '</a>';
		$html .= '<div class="scsl__thumb">' . $img . '</div>';
		$html .= '<div class="scsl__card-body">';
		$html .= '<h3 class="scsl__name">' . $title . '</h3>';
    $html .= '<span class="scsl__read-more">Read More <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="w-6 h-6 ml-1 hover:translate-x-4"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></span>';
		$html .= '</div>';
		$html .= '</article>';

		return $html;
	}
}

Salient_Case_Study_Library::init();
