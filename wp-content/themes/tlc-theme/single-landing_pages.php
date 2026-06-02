<?php
/**
 * Template for single Landing Page posts.
 * Mirrors page.php so WPBakery builder content renders correctly,
 * without Salient's blog-specific chrome (sidebar, meta, related posts, etc).
 *
 * @package TLC Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
nectar_page_header( $post->ID );
$nectar_fp_options = nectar_get_full_page_options();

?>
<div class="container-wrap">
	<div class="<?php echo ( $nectar_fp_options['page_full_screen_rows'] !== 'on' ) ? 'container' : ''; ?> main-content" role="main">
		<div class="<?php echo apply_filters( 'nectar_main_container_row_class_name', 'row' ); ?>">
			<?php
			nectar_hook_before_content();

			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
			endif;

			nectar_hook_after_content();
			?>
		</div>
	</div>
	<?php nectar_hook_before_container_wrap_close(); ?>
</div>
<?php get_footer(); ?>
