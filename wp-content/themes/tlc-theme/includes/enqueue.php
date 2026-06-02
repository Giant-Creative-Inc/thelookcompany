<?php

add_action( 'wp_enqueue_scripts', 'salient_child_enqueue_styles', 100 );

function salient_child_enqueue_styles() {
  $v = TLC_VERSION;

  wp_enqueue_style( 'theme-custom-style', get_stylesheet_directory_uri() . '/assets/css/style.min.css', array(), filemtime( get_stylesheet_directory() . '/assets/css/style.min.css' ) );
  wp_register_script( 'tlc-form-js', get_stylesheet_directory_uri() . '/assets/js/forms.js', array( 'jquery' ), '', true );

  // Pages that need the forms JS enqueued.
  $form_pages = array( 'contact', 'careers' );

  // Front page gets its own stylesheet (home.min.css).
  if ( is_front_page() ) {
    wp_enqueue_style( 'tlc-homepage-style', get_stylesheet_directory_uri() . '/assets/css/home.min.css', array(), filemtime( get_stylesheet_directory() . '/assets/css/home.min.css' ) );
  }

  // Auto-load page-specific CSS based on the page slug.
  // Convention: create assets/scss/pages/{slug}.scss and it loads automatically.
  if ( is_singular( 'page' ) ) {
    $slug = get_post_field( 'post_name', get_the_ID() );
    $file = get_stylesheet_directory() . "/assets/css/{$slug}.min.css";

    if ( file_exists( $file ) ) {
      wp_enqueue_style( "tlc-{$slug}-style", get_stylesheet_directory_uri() . "/assets/css/{$slug}.min.css", array(), filemtime( get_stylesheet_directory() . "/assets/css/{$slug}.min.css" ) );
    }

    if ( in_array( $slug, $form_pages, true ) ) {
      wp_enqueue_script( 'tlc-form-js' );
    }
  }

  if ( is_singular( 'case-study' ) ) {
    wp_enqueue_style( 'tlc-case-study-style', get_stylesheet_directory_uri() . '/assets/css/case-studies.min.css', array(), filemtime( get_stylesheet_directory() . '/assets/css/case-studies.min.css' ) );
  }

  if ( is_page() && has_category( 'landing-page' ) ) {
    wp_enqueue_style( 'tlc-landing-page-style', get_stylesheet_directory_uri() . '/assets/css/landing-pages.min.css', array(), filemtime( get_stylesheet_directory() . '/assets/css/landing-pages.min.css' ) );
    wp_enqueue_script( 'tlc-form-js' );
  }

  // Video taxonomy archive.
  if ( is_tax( 'video-category' ) ) {
    wp_enqueue_style( 'tlc-videos-style', get_stylesheet_directory_uri() . '/assets/css/videos.min.css', array(), filemtime( get_stylesheet_directory() . '/assets/css/videos.min.css' ) );
  }

  // Blog index and single posts.
  if ( is_home() || is_singular( 'post' ) ) {
    wp_enqueue_style( 'tlc-blog-style', get_stylesheet_directory_uri() . '/assets/css/blog.min.css', array(), filemtime( get_stylesheet_directory() . '/assets/css/blog.min.css' ) );
  }

  // Category and tag archives.
  if ( is_category() || is_tag() ) {
    wp_enqueue_style( 'tlc-blog-category-style', get_stylesheet_directory_uri() . '/assets/css/blog-category.min.css', array(), filemtime( get_stylesheet_directory() . '/assets/css/blog-category.min.css' ) );
  }

  if ( is_rtl() ) {
    wp_enqueue_style( 'salient-rtl', get_template_directory_uri() . '/rtl.css', array(), '1', 'screen' );
  }
}
