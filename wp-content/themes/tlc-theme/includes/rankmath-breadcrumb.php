<?php

add_filter( 'rank_math/frontend/breadcrumb/items', function( $crumbs, $class ) {

    if ( is_singular( 'case-study' ) ) {

        $parent_page_id = 1556;

        return [
            $crumbs[0], // Home
            [
                get_the_title( $parent_page_id ),
                get_permalink( $parent_page_id ),
            ],
            end( $crumbs ), // Current post
        ];
    }

    return $crumbs;

}, 10, 2 );