<?php

/**
 * Force Salient transparent header on blog index, single posts, and video taxonomy pages.
 */
add_filter( 'nectar_activate_transparent_header', function ( $active ) {
  if ( is_home() || is_single() || is_tax( 'video-category' ) ) {
    return true;
  }
  return $active;
} );

/**
 * Add resource hints early in wp_head (priority 1):
 * - Preconnect/dns-prefetch for Adobe Fonts (Typekit)
 * - Preload for the two most-used Gotham weights so the browser fetches
 *   them immediately rather than waiting to discover them via CSS.
 */
add_action( 'wp_head', function () {
  $fonts_uri = get_stylesheet_directory_uri() . '/fonts';

  echo '<link rel="preconnect" href="https://use.typekit.net" crossorigin>' . "\n";
  echo '<link rel="dns-prefetch" href="https://use.typekit.net">' . "\n";
  echo '<link rel="preload" href="' . $fonts_uri . '/Gotham-Book.woff2" as="font" type="font/woff2" crossorigin>' . "\n";
  echo '<link rel="preload" href="' . $fonts_uri . '/Gotham-Bold.woff2" as="font" type="font/woff2" crossorigin>' . "\n";
}, 1 );

/**
 * Inject global section banner after outer wrap open on the blog index.
 *
 * NOTE: The ID 484 is a hardcoded Nectar Global Section post ID.
 * If the post is deleted or the DB is migrated, update this ID.
 */
add_action( 'nectar_hook_after_outer_wrap_open', function () {
  if ( is_home() ) {
    echo '<div class="container-wrap" style="padding-top: 0px; padding-bottom: 0px;"><div class="container main-content" role="banner"><div class="row">' . do_shortcode( '[nectar_global_section id="484"]' ) . '</div></div></div>';
  }
}, 20 );
