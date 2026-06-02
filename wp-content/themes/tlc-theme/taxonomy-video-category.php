<?php
/**
 * Taxonomy archive template: video-category
 * Renders WPBakery shortcodes as the page content for the archive.
 *
 * File:
 * /wp-content/themes/tlc-theme/taxonomy-video-category.php
 */
defined('ABSPATH') || exit;

get_header(); ?>
<div class="container-wrap video-category-archive" style="padding-top: 0 !important; padding-bottom: 0;">
<div class="container main-content" role="main" style="--nectar-sticky-top-distance: 162px;">
<div class="row">

<?php // Your WPBakery/Salient content as a string.
// You can edit this right in this file.
$bakery_content = <<<'SVLVC'
[vc_row type="full_width_background" full_screen_row_position="middle" column_margin="default" equal_height="yes" content_placement="middle" column_direction="default" column_direction_tablet="default" column_direction_phone="default" bg_color="#0C0D0E" bg_image="184" background_image_preload="true" bg_image_type="img" bg_position="center center" background_image_loading="default" bg_repeat="no-repeat" scene_position="center" text_color="light" text_align="left" row_border_radius="none" row_border_radius_applies="bg" row_position_desktop="default" row_position_tablet="inherit" row_position_phone="inherit" overflow="visible" class="video-gallery__hero transparent-hero header-transparent" overlay_strength="0.3" gradient_direction="left_to_right" shape_divider_position="bottom" bg_image_animation="none" gradient_type="default" shape_type=""][vc_column column_padding="no-extra-padding" column_padding_tablet="inherit" column_padding_phone="inherit" column_padding_position="all" flex_gap_desktop="10px" column_element_direction_desktop="default" column_element_spacing="default" desktop_text_alignment="default" tablet_text_alignment="default" phone_text_alignment="default" background_color_opacity="1" background_hover_color_opacity="1" column_backdrop_filter="none" column_shadow="none" column_border_radius="none" column_link_target="_self" column_position="default" gradient_direction="left_to_right" overlay_strength="0.3" width="1/1" tablet_width_inherit="default" animation_type="default" bg_image_animation="none" border_type="simple" column_border_width="none" column_border_style="solid"][vc_row_inner column_margin="default" column_direction="default" column_direction_tablet="default" column_direction_phone="default" text_align="left" row_position="default" row_position_tablet="inherit" row_position_phone="inherit" overflow="visible" pointer_events="all"][vc_column_inner left_padding_desktop="27px" left_padding_tablet="0" column_element_direction_desktop="default" column_element_spacing="default" desktop_text_alignment="default" tablet_text_alignment="default" phone_text_alignment="default" background_color_opacity="1" background_hover_color_opacity="1" column_backdrop_filter="none" column_shadow="none" column_border_radius="none" column_link_target="_self" overflow="visible" gradient_direction="left_to_right" overlay_strength="0.3" width="1/1" tablet_width_inherit="default" animation_type="default" bg_image_animation="none" border_type="advanced" border_left_desktop="2.5" border_left_tablet="0" border_left_phone="0" column_border_color="#E31937" column_border_style="solid" column_padding_type="advanced" content_layout="default" gradient_type="default"][vc_column_text css="" text_direction="default"]
<h1 class="no-margin">{{VIDEO_CATEGORY_NAME}}</h1>
[/vc_column_text][/vc_column_inner][/vc_row_inner][/vc_column][/vc_row][vc_row type="full_width_background" full_screen_row_position="middle" column_margin="default" column_direction="default" column_direction_tablet="default" column_direction_phone="default" scene_position="center" text_color="dark" text_align="left" row_border_radius="none" row_border_radius_applies="bg" row_position_desktop="default" row_position_tablet="inherit" row_position_phone="inherit" overflow="visible" class="video-gallery__gallery no-padding-top" overlay_strength="0.3" gradient_direction="left_to_right" shape_divider_position="bottom" bg_image_animation="none" gradient_type="default" shape_type=""][vc_column column_padding="no-extra-padding" column_padding_tablet="inherit" column_padding_phone="inherit" column_padding_position="all" flex_gap_desktop="10px" column_element_direction_desktop="default" column_element_spacing="default" desktop_text_alignment="default" tablet_text_alignment="default" phone_text_alignment="default" background_color_opacity="1" background_hover_color_opacity="1" column_backdrop_filter="none" column_shadow="none" column_border_radius="none" column_link_target="_self" column_position="default" gradient_direction="left_to_right" overlay_strength="0.3" width="1/1" tablet_width_inherit="default" animation_type="default" bg_image_animation="none" border_type="simple" column_border_width="none" column_border_style="solid"][svl_video_library eager_first="3" preload_first="3"][/vc_column][/vc_row]
SVLVC;
$term = get_queried_object();
if ($term && !is_wp_error($term) && !empty($term->name)) {
	$bakery_content = str_replace('{{VIDEO_CATEGORY_NAME}}', esc_html($term->name), $bakery_content);
}
// Output the VC content.
// do_shortcode is required, and we run wpautop off to avoid stray <p> wrappers.
echo do_shortcode(shortcode_unautop($bakery_content)); ?>
</div>
</div>
</div>
<?php get_footer();