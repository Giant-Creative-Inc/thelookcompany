<?php

/**
 * Put WordPress blog posts under /blog/{post-slug}/
 *
 * After adding or modifying rewrite rules:
 * Go to Settings → Permalinks → click "Save Changes" to flush rewrite rules.
 */

/**
 * Change generated permalinks for posts to use /blog/{post-slug}/
 */
add_filter( 'post_link', function ( string $permalink, $post ): string {
  if ( ! $post instanceof WP_Post ) {
    $post_id = isset( $post->ID ) ? (int) $post->ID : 0;
    $post    = $post_id ? get_post( $post_id ) : null;
  }

  if ( ! $post instanceof WP_Post ) {
    return $permalink;
  }

  if ( $post->post_type !== 'post' ) {
    return $permalink;
  }

  return home_url( '/blog/' . $post->post_name . '/' );
}, 10, 2 );
/**
 * Add rewrite rule so /blog/{slug}/ resolves to the correct post.
 */
add_action( 'init', function (): void {
  add_rewrite_rule(
    '^blog/([^/]+)/?$',
    'index.php?name=$matches[1]',
    'top'
  );
} );

/**
 * 301 redirect old post URLs to /blog/{slug}/ to prevent duplicate content.
 */
add_action( 'template_redirect', function (): void {
  if ( ! is_singular( 'post' ) ) {
    return;
  }

  $post = get_queried_object();
  if ( ! $post instanceof WP_Post ) {
    return;
  }

  $target_url = home_url( '/blog/' . $post->post_name . '/' );

  $scheme      = is_ssl() ? 'https://' : 'http://';
  $host        = $_SERVER['HTTP_HOST'] ?? '';
  $request_uri = $_SERVER['REQUEST_URI'] ?? '';
  $current_url = strtok( $scheme . $host . $request_uri, '?' );

  $current_path = untrailingslashit( (string) parse_url( $current_url, PHP_URL_PATH ) );
  $target_path  = untrailingslashit( (string) parse_url( $target_url, PHP_URL_PATH ) );

  if ( $current_path !== $target_path ) {
    wp_redirect( $target_url, 301 );
    exit;
  }
} );
