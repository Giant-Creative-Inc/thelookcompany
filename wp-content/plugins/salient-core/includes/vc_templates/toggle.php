<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

extract(shortcode_atts(array(
	"title" => 'Title',
	'heading_tag' => 'default',
	'heading_tag_functionality' => 'default',
	'color' => 'Accent-Color'),
	$atts));

$typography_class = ( in_array($heading_tag, array('h2','h3','h4','h5','h6')) ) ? 'nectar-inherit-'.$heading_tag.' toggle-heading' : 'toggle-heading';

$heading_tag_html = 'h3';
if( 'change_html_tag' === $heading_tag_functionality &&
	in_array($heading_tag, array('h2','h3','h4','h5','h6','span')) ) {
		$heading_tag_html = $heading_tag;
}

echo '<div class="toggle '.esc_attr(strtolower($color)).'" data-inner-wrap="true">';
echo '<'.$heading_tag_html.' class="toggle-title">';
echo '<a href="#" role="button" class="'.$typography_class.'">';
if (isset($GLOBALS['nectar-toggle-style']) && $GLOBALS['nectar-toggle-style'] === 'animated_circle') {
	echo '<svg class="nectar-toggle-icon" width="100%" height="100%" viewBox="0 0 40 40" preserveAspectRatio="none">
		<circle class="nectar-toggle-icon-circle" cx="20" cy="20" r="18" stroke="currentColor" stroke-width="1" fill="none" />
		<circle class="nectar-toggle-icon-circle-hover" cx="20" cy="20" r="18" stroke="currentColor" stroke-width="1" fill="none" />
		<path
			class="plus-line plus-line-vertical"
			d="M20,15 L20,25"
		/>
		<path
			class="plus-line plus-line-horizontal"
			d="M15,20 L25,20"
		/>
		</svg>';
} else {
	echo '<i role="presentation" class="fa fa-plus"></i>';
}
echo wp_kses_post($title);
echo '</a>';
echo '</'.$heading_tag_html.'>';
echo '<div><div class="inner-toggle-wrap">' . do_shortcode(wp_kses_post($content)) . '</div></div>';
echo '</div>';

?>
