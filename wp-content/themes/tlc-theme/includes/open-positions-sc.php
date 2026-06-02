<?php

add_shortcode( 'open_positions', 'display_open_positions_cached' );

function display_open_positions_cached() {
    // 1. Check if the HTML is already in the cache (transient)
    $cache_key = 'open_positions_html_cache';
    $cached_output = get_transient( $cache_key );

    if ( false !== $cached_output ) {
        return $cached_output; // Return the saved HTML immediately
    }

    // 2. If not cached, run the query
    $args = array(
        'post_type'      => 'open_positions',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    );

    $query = new WP_Query( $args );
    $output = '<div class="job-listings-container">';

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id  = get_the_ID();
            $location = get_field( 'location', $post_id );
            $file     = get_field( 'position_information', $post_id );

            $output .= '<div class="job-item">';
            $output .= '<h3>' . get_the_title() . '</h3>';

            if ( $location ) {
                $output .= '<p><strong>Location:</strong> ' . esc_html( $location ) . '</p>';
            }

            if ( $file ) {
                $file_url = is_array( $file ) ? $file['url'] : $file;
                $output .= '<a href="' . esc_url( $file_url ) . '" class="button" target="_blank"><span>Download Position Info (PDF)</span></a>';
            }

            $output .= '</div>';
        }
        wp_reset_postdata();
    } else {
        $output .= '<p>No open positions found at this time.</p>';
    }

    $output .= '</div>';

    // 3. Save the result to the cache for 12 hours
    set_transient( $cache_key, $output, 12 * HOUR_IN_SECONDS );

    return $output;
}

// Clear the cache whenever an "open_position" is saved or updated
add_action( 'save_post_open_positions', 'clear_open_positions_cache' );

function clear_open_positions_cache() {
    delete_transient( 'open_positions_html_cache' );
}