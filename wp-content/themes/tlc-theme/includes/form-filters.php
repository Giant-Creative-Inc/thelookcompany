<?php
// Filter for the front-end, admin, validation, and submission
add_filter( 'gform_pre_render_2', 'populate_cpt_posts' );
add_filter( 'gform_pre_validation_2', 'populate_cpt_posts' );
add_filter( 'gform_pre_submission_filter_2', 'populate_cpt_posts' );
add_filter( 'gform_admin_pre_render_2', 'populate_cpt_posts' );

function populate_cpt_posts( $form ) {
    foreach ( $form['fields'] as &$field ) {
        if ( $field->id != 15 ) continue;

        // Try to get the choices from the cache first
        $cache_key = 'gf_dynamic_choices_' . $field->id;
        $choices = get_transient( $cache_key );

        if ( false === $choices ) {
            // Cache expired or doesn't exist, do the heavy lifting
            $posts = get_posts( array(
                'post_type'   => 'open_positions',
                'numberposts' => -1,
                'post_status' => 'publish',
                'fields'      => 'ids', // Performance boost: only get IDs first
            ) );

            $choices = array();
            // $choices[] = array( 'text' => '', 'value' => '' );

            foreach ( $posts as $post_id ) {
                $choices[] = array( 'text' => get_the_title( $post_id ), 'value' => $post_id );
            }

            // Store the result for 12 hours (43200 seconds)
            set_transient( $cache_key, $choices, 12 * HOUR_IN_SECONDS );
        }

        $field->choices = $choices;
    }
    return $form;
}