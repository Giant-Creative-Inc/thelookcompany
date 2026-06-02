<?php
/**
 * Template for single Case Study posts.
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
  <style>
    .case-study-nav {
    display: flex;
    flex-direction: row;
    align-content: stretch;
    justify-content: center;
    align-items: stretch;
}

a.nav-prev ,
a.nav-next {
    flex: 1 1 50%;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    background: #53A8A8;
    align-items: center;
    padding: 16px 24px !important;
}
a.nav-prev {
    padding: 16px 24px 16px 10px !important;
}
a.nav-next {
    padding: 16px 10px 16px 24px !important;
}
a.nav-next {
    background: #E31937;
}

a.nav-prev .nav-text h3,
a.nav-next .nav-text h3 {
    color: #FFF;
    font-family: "clarendon-text-pro";
    font-size: 20px;
    font-weight: 700;
    line-height: 1.2 !important;
    display: block;
    transition: transform 0.3s ease-in-out;
}
a.nav-prev .nav-text span,
a.nav-next .nav-text span {
    color: #FFF;
    font-size: 12px;
    line-height: 1.2 !important;
    font-weight: 400;
    letter-spacing: 0.952px;
    text-transform: uppercase;
    display: block;
}
a.nav-prev .arrow,
a.nav-next .arrow,
a.nav-prev .arrow svg,
a.nav-next .arrow svg{
    display: block;
    width: 20px;
    height: 20px;
    fill: #FFF;
    transition: transform 0.3s ease-in-out;
}
a:hover .arrow {
    transform: scale(1.8);
}
a:hover h3 {
    transform: scale(1.1);
}

@media screen and (max-width: 449px) {
    .case-study-nav {
        flex-direction: column;
    }
    a.nav-prev {
        text-align: left;
        flex-direction: row-reverse;
    }
    a.nav-prev,
    a.nav-next {
        padding: 16px 24px !important;
    }
    a.nav-prev svg {
        transform: rotate(180deg);
    }
    
}
@media screen and (min-width: 450px) {
    a.nav-prev {
        text-align: right;
    }
}
@media screen and (min-width: 786px) {
    a.nav-prev .nav-text h3,
    a.nav-next .nav-text h3 {
        font-size: 26px;
    }
    a.nav-prev .nav-text span,
    a.nav-next .nav-text span {
        font-size: 14px;
    }
    .case-study-nav {
        display: flex;
        flex-direction: row;
        align-content: stretch;
        justify-content: center;
        align-items: stretch;
    }
    a.nav-prev ,
    a.nav-next {
        padding: 32px 38px !important;
    }
    a.nav-prev .arrow,
    a.nav-next .arrow,
    a.nav-prev .arrow svg,
    a.nav-next .arrow svg{
        display: block;
        width: 60px;
        height: 60px;
        fill: #FFF;
    }
}
@media screen and (min-width: 1024px) {
    a.nav-prev ,
    a.nav-next {
        padding: 40px 48px !important;
    }
    a.nav-prev .arrow,
    a.nav-next .arrow,
    a.nav-prev .arrow svg,
    a.nav-next .arrow svg{
        display: block;
        width: 80px;
        height: 80px;
        fill: #FFF;
    }
    a.nav-prev .nav-text h3,
    a.nav-next .nav-text h3 {
        font-size: 36px;
    }
}
  </style>
	<?php nectar_hook_before_container_wrap_close();
  			// Get next & previous posts (same post type)
      $prev_post = get_next_post();
      $next_post = get_previous_post();
      ?>

      <div class="case-study-nav">
          <?php if ( $prev_post ) : ?>
              <a class="nav-prev" href="<?php echo get_permalink( $prev_post->ID ); ?>">
                  <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="hidden w-10 h-10 lg:w-20 lg:h-20 lg:block"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg></span>
                  <div class="nav-text">
                      <h3>Prev Project</h3>
                      <span>Continue Reading</span>
                  </div>
              </a>
          <?php endif; ?>

          <?php if ( $next_post ) : ?>
              <a class="nav-next" href="<?php echo get_permalink( $next_post->ID ); ?>">
                  <div class="nav-text">
                      <h3>Next Project</h3>
                      <span>Continue Reading</span>
                  </div>
                  <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="hidden w-10 h-10 lg:w-20 lg:h-20 lg:block"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></span>
              </a>
          <?php endif; ?>

      </div>
</div>
<?php get_footer(); ?>
