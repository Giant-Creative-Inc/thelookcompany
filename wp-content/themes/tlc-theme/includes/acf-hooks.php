<?php

/**
 * Set the featured image for gallery-items posts using the ACF "image" field.
 *
 * Runs after ACF saves field values. Only applies to the gallery-items post type
 * and will not overwrite an existing featured image.
 */
add_action( 'acf/save_post', 'set_gallery_item_featured_image_from_acf', 20 );

function set_gallery_item_featured_image_from_acf( $post_id ) {
  if ( ! is_numeric( $post_id ) ) {
    return;
  }
  if ( get_post_type( $post_id ) !== 'gallery-items' ) {
    return;
  }
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    return;
  }
  if ( wp_is_post_revision( $post_id ) ) {
    return;
  }
  if ( has_post_thumbnail( $post_id ) ) {
    return;
  }

  $image = get_field( 'image', $post_id );

  if ( ! empty( $image ) && is_array( $image ) && ! empty( $image['ID'] ) ) {
    set_post_thumbnail( $post_id, $image['ID'] );
  }
}
