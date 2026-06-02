<?php

// Child theme version — bump this when deploying CSS/JS changes to bust the cache.
// Kept separate from nectar_get_theme_version() so Salient updates don't
// invalidate our own cached assets.
define( 'TLC_VERSION', '1.1.0' );

require_once get_stylesheet_directory() . '/includes/enqueue.php';
require_once get_stylesheet_directory() . '/includes/critical-css.php';
require_once get_stylesheet_directory() . '/includes/header-filters.php';
require_once get_stylesheet_directory() . '/includes/blog-rewrites.php';
require_once get_stylesheet_directory() . '/includes/landing-page-rewrites.php';
require_once get_stylesheet_directory() . '/includes/acf-hooks.php';
require_once get_stylesheet_directory() . '/includes/form-filters.php';
require_once get_stylesheet_directory() . '/includes/open-positions-sc.php';
require_once get_stylesheet_directory() . '/includes/acf-page-schema.php';
require_once get_stylesheet_directory() . '/includes/rankmath-breadcrumb.php';