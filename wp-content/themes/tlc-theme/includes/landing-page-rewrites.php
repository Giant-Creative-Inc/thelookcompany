<?php

/**
 * Only run Landing Page Admin Logic inside the Dashboard
 */
if ( is_admin() ) {
  /**
 * Add standard Categories to the 'page' post type
 */
  add_action( 'init', function() {
    register_taxonomy_for_object_type( 'category', 'page' );
  });

  /**
   * Add 'Category' column to the Pages admin list
   */
  add_filter( 'manage_pages_columns', function( $columns ) {
      $columns['page_category'] = 'Category';
      return $columns;
  });

  /**
   * Populate the 'Category' column with the actual category names
   */
  add_action( 'manage_pages_custom_column', function( $column, $post_id ) {
      if ( $column === 'page_category' ) {
          $categories = get_the_category( $post_id );
          if ( ! empty( $categories ) ) {
              echo esc_html( $categories[0]->name );
          } else {
              echo '<span style="color:#ccc;">—</span>';
          }
      }
  }, 10, 2 );

  /**
   * 1. Create a Sidebar Menu for "Landing Pages" (Filtered Pages)
   */
  add_action('admin_menu', function() {
    add_menu_page(
      'Landing Pages',                  // Page Title
      'Landing Pages',                  // Menu Title
      'edit_pages',                     // Capability
      'edit.php?post_type=page&landing_page=true', // URL with a custom flag
      '',                               // Function (none needed for a link)
      'dashicons-layout',               // Icon
      -20                               // Position
    );
  });

  /**
   * 2. Filter the Page List & Auto-Categorize New Pages
   */
  add_action('current_screen', function($screen) {
    // Only run if we are on the Pages list or the Add New page
    if ($screen->post_type !== 'page') return;

    $is_landing_menu = isset($_GET['landing_page']) && $_GET['landing_page'] === 'true';

    // A. FILTER THE LIST: Only show pages in the 'Landing Page' category
    if ($screen->base === 'edit' && $is_landing_menu) {
      add_filter('request', function($query_vars) {
        $query_vars['category_name'] = 'landing-page'; // Ensure this matches your category slug
        return $query_vars;
      });
    }

    // B. AUTO-SELECT CATEGORY: When clicking "Add New" from this menu
    if ($screen->base === 'post' && $is_landing_menu) {
      add_action('admin_footer', function() {
        ?>
        <script type="text/javascript">
          jQuery(document).ready(function($) {
            // This waits for the Gutenberg editor (or Classic) to load
            // and checks the 'Landing Page' category checkbox
            var checkCategory = setInterval(function() {
              var label = $("label:contains('Landing Page')");
              if (label.length) {
                label.closest('div').find('input[type="checkbox"]').prop('checked', true);
                clearInterval(checkCategory);
              }
            }, 500);
          });
        </script>
        <?php
      });
    }
  });
}