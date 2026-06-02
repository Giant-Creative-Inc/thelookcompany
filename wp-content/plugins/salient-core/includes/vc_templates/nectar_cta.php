<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

extract(shortcode_atts(array(
  "heading_tag" => "h3",
  'text' => '',
  "btn_style" => "see-through",
  'margin_top' => '',
  'margin_right' => '',
  'margin_bottom' => '',
  'margin_left' => '',
  'padding_top' => '',
  'padding_right' => '',
  'padding_bottom' => '',
  'padding_left' => '',
  'button_color' => '',
	'button_color_hover' => '',
  'button_color_custom' => '',
  'btn_type' => '',
  "link_text" => "",
  'text_color' => '',
  'text_color_hover' => '',
  'next_section_color' => '',
  'next_section_shadow' => '',
  'next_section_down_arrow_animation' => '',
  'next_section_down_arrow_alt_animation' => '',
  'next_section_icon_size' => '1',
  'display' => 'block',
  'url' => '',
  'link_type' => 'regular',
  'alignment' => 'left',
	'nofollow' => '',
  'icon_gap' => '10',
  'min_icon_size' => '36',
  'icon_family' => '',
	'icon_fontawesome' => '',
	'icon_linecons' => '',
	'icon_iconsmind' => '',
	'icon_steadysets' => '',
  'icon_nectarbrands' => '',
  'bypass_link' => 'false',
  'aria_label_text' => '',
  'class' => '' ), $atts));

$target                 = ($link_type == 'new_tab') ? 'target="_blank"' : null;
$style                  = (!empty($text_color)) ? ' style="color: '.esc_attr($text_color).';"' : '';
$bg_style               = (!empty($text_color)) ? ' style="background-color: '.esc_attr($text_color).';"' : null;
$underline_border_color = esc_attr($text_color);
$text_color_stored      = $text_color;
$text_color             = (!empty($text_color)) ? 'custom' : 'std';
$nofollow_attr          = (!empty($nofollow) && 'true' === $nofollow) ? ' rel="nofollow"': '';

$aria_label_attr = (!empty($aria_label_text)) ? ' aria-label="'.esc_attr($aria_label_text).'"' : '';
$link_text_stored = $link_text;

if( 'span' === $heading_tag ) {
	$style .= ' class="nectar-button-type"';
}

// Dynamic style classes.
if( function_exists('nectar_el_dynamic_classnames') ) {
	$dynamic_el_styles = nectar_el_dynamic_classnames('nectar_cta', $atts);
} else {
	$dynamic_el_styles = '';
}

// Margins.
$margins = '';

if( !empty($margin_top) ) {
  $margins .= 'margin-top: '.nectar_css_sizing_units($margin_top).'; ';
}
if( !empty($margin_right) ) {
  $margins .= 'margin-right: '.nectar_css_sizing_units($margin_right).'; ';
}
if( !empty($margin_bottom) ) {
  $margins .= 'margin-bottom: '.nectar_css_sizing_units($margin_bottom).'; ';
}
if( !empty($margin_left) ) {
  $margins .= 'margin-left: '.nectar_css_sizing_units($margin_left).';';
}

// Padding.
$padding = '';

if( !empty($padding_top) ) {
  $padding .= 'padding-top: '.nectar_css_sizing_units($padding_top).'; ';
}
if( !empty($padding_right) ) {
  $padding .= 'padding-right: '.nectar_css_sizing_units($padding_right).'; ';
}
if( !empty($padding_bottom) ) {
  $padding .= 'padding-bottom: '.nectar_css_sizing_units($padding_bottom).'; ';
}
if( !empty($padding_left) ) {
  $padding .= 'padding-left: '.nectar_css_sizing_units($padding_left).';';
}

$using_bg_color = ( !empty($button_color) && 'default' !== $button_color ) ? 'true' : 'false';

$style_markup = null;
$style_padding_markup = null;

$custom_color = $button_color === 'custom' && isset($button_color_custom) && !empty($button_color_custom) ? $button_color_custom : '';

// Prepare background color style if custom color is set
$bg_color_style = !empty($custom_color) ? 'background-color: '.esc_attr($custom_color).';' : '';

if( !empty($margins) ) {
  $style_markup = 'style="'.$margins.'"';
}
if( !empty($padding) || !empty($bg_color_style) ) {
  $style_content = '';
  if( !empty($padding) ) {
    $style_content .= $padding;
  }
  if( !empty($bg_color_style) ) {
    $style_content .= ' '.$bg_color_style;
  }
  $style_padding_markup = 'style="'.$style_content.'"';
}

// Lightbox
$link_text_classes = '';
if( 'video_lightbox' === $link_type ) {
 $link_text_classes = ' pp nectar_video_lightbox';
} else if ( 'image_lightbox' === $link_type ) {
 $link_text_classes = ' pp';
}

$anchor_tag = 'a';
if( $bypass_link === 'true' ) {
  $anchor_tag = 'span';
}

// icon.
$icon_output = nectar_icon_el_output($atts);

// Generate CSS variables once for all styles except next-section
$css_vars = '';
if( !empty($text_color_stored) ) $css_vars .= '--nectar-text-color: '.esc_attr($text_color_stored).'; ';
if ( $button_color === 'custom' && !empty($button_color_custom) ) {
  $css_vars .= '--nectar-button-color: '.esc_attr($button_color_custom).'; ';
} else if (!empty($button_color)) {
  $css_vars .= '--nectar-button-color: var(--nectar-'.esc_attr($button_color).'); ';
}
if( !empty($button_color_hover) ) $css_vars .= '--nectar-button-color-hover: '.esc_attr($button_color_hover).'; ';
if( !empty($text_color_hover) ) $css_vars .= '--nectar-text-color-hover: '.esc_attr($text_color_hover).'; ';
if (!empty($icon_gap)) $css_vars .= '--nectar-icon-gap: '.esc_attr($icon_gap).'px; ';
if (!empty($min_icon_size) && $btn_style === 'arrow-circle-animation') $css_vars .= '--nectar-min-icon-size: '.esc_attr(intval($min_icon_size)).'px; ';

if( !empty($next_section_icon_size) && $btn_style === 'next-section' ) {
  $css_vars .= '--nectar-next-section-icon-size: '.esc_attr($next_section_icon_size).'; ';
}

// Merge CSS variables into existing style_markup
if( !empty($css_vars) ) {
  if( !empty($style_markup) && strpos($style_markup, 'style="') !== false ) {
    // Extract existing styles and combine with CSS variables
    $existing_styles = preg_replace('/^style="(.+)"$/', '$1', $style_markup);
    $style_markup = 'style="'.$existing_styles.' '.$css_vars.'"';
  } else {
    $style_markup = 'style="'.$css_vars.'"';
  }
}

// Material style.
if( $btn_style === 'material' ) {

  echo '<div class="nectar-cta '. esc_attr( $class ).esc_attr($dynamic_el_styles).'" data-style="'.esc_attr($btn_style).'" data-alignment="'.esc_attr($alignment).'" data-display="'. esc_attr($display) .'" data-text-color="'.esc_attr($text_color).'" '.$style_markup.'>';
  echo '<'.esc_html($heading_tag).'> <span class="text">'.wp_kses_post($text).' </span>';
  $combined_style = $style;
if( !empty($bg_color_style) ) {
  if( strpos($combined_style, 'style="') !== false ) {
    $combined_style = str_replace('style="', 'style="'.$bg_color_style.' ', $combined_style);
  } else {
    $combined_style .= ' style="'.$bg_color_style.'"';
  }
}
echo  '<span class="link_wrap" '.$combined_style.'><'.$anchor_tag .' '.$target . $nofollow_attr .$aria_label_attr.' class="link_text'.esc_attr($link_text_classes).'" role="button" href="'.esc_url($url).'">'.wp_kses_post($link_text).'<span class="circle" '.$bg_style.'></span><span class="arrow"></span></'.$anchor_tag .'></span>';
  echo '</'.esc_html($heading_tag).'></div>';
}
// See through.
else if( $btn_style === 'see-through' ) {

  echo '<div class="nectar-cta '.esc_attr($class) . esc_attr($dynamic_el_styles).'" data-color="'. esc_attr($button_color) .'" data-using-bg="'.esc_attr($using_bg_color).'" data-style="'.esc_attr($btn_style).'" data-display="'. esc_attr($display) .'" data-alignment="'.esc_attr($alignment).'" data-text-color="'.esc_attr($text_color).'" '.$style_markup.'>';
  echo '<'.esc_html($heading_tag). $style.'> <span class="text">'.wp_kses_post($text).' </span>';
  echo  '<span class="link_wrap" '.$style_padding_markup.'><'.$anchor_tag .' '.$target . $nofollow_attr .$aria_label_attr.' class="link_text'.esc_attr($link_text_classes).'" role="button" href="'.esc_url($url).'">'.wp_kses_post($link_text).'<span class="arrow"></span></'.$anchor_tag .'></span>';
  echo '</'.esc_html($heading_tag).'></div>';
}
// Arrow Animation.
else if( $btn_style === 'arrow-animation' ) {

  echo '<div class="nectar-cta '.esc_attr($class) .esc_attr($dynamic_el_styles) .'" data-color="'. esc_attr($button_color) .'" data-using-bg="'.esc_attr($using_bg_color).'" data-style="'.esc_attr($btn_style).'" data-display="'. esc_attr($display) .'" data-alignment="'.esc_attr($alignment).'" data-text-color="'.esc_attr($text_color).'" '.$style_markup.'>';
  echo '<'.esc_html($heading_tag). $style.'>';
  echo  '<span class="link_wrap" '.$style_padding_markup.'><'.$anchor_tag .' '.$target . $nofollow_attr . $aria_label_attr.' class="link_text'.esc_attr($link_text_classes).'" role="button" href="'.esc_url($url).'"><span class="text">'.wp_kses_post($link_text) .'</span>';
  echo '<svg class="next-arrow" aria-hidden="true" width="20px" height="25px" viewBox="0 0 50 80" xml:space="preserve">
  <polyline stroke="#ffffff" stroke-width="9" fill="none" stroke-linecap="round" stroke-linejoin="round" points="0, 0 45, 40 0, 80"/>
  </svg>  ';
  echo '<span aria-hidden="true" class="line" '.$bg_style.'></span> </'.$anchor_tag .'></span>';
  echo '</'.esc_html($heading_tag).'></div>';
}
else if( $btn_style === 'curved-arrow-animation' ) {

  echo '<div class="nectar-cta '.esc_attr($class) .esc_attr($dynamic_el_styles) .'" data-color="'. esc_attr($button_color) .'" data-using-bg="'.esc_attr($using_bg_color).'" data-style="'.esc_attr($btn_style).'" data-display="'. esc_attr($display) .'" data-alignment="'.esc_attr($alignment).'" data-text-color="'.esc_attr($text_color).'" '.$style_markup.'>';
  echo '<'.esc_html($heading_tag). $style.'>';
  echo  '<span class="link_wrap" '.$style_padding_markup.'><'.$anchor_tag .' '.$target . $nofollow_attr . $aria_label_attr.' class="link_text'.esc_attr($link_text_classes).'" role="button" href="'.esc_url($url).'"><span class="text">'.wp_kses_post($link_text) .'</span>';
  echo nectar_get_curved_arrow_markup();
  echo '</'.$anchor_tag .'></span>';
  echo '</'.esc_html($heading_tag).'></div>';
}
else if ( $btn_style === 'arrow-circle-animation' ) {

  echo '<div class="nectar-cta '.esc_attr($class) .esc_attr($dynamic_el_styles) .'" data-color="'. esc_attr($button_color) .'" data-using-bg="'.esc_attr($using_bg_color).'" data-style="'.esc_attr($btn_style).'" data-display="'. esc_attr($display) .'" data-alignment="'.esc_attr($alignment).'" data-text-color="'.esc_attr($text_color).'" '.$style_markup.'>';
  echo '<'.esc_html($heading_tag). $style.'>';
  echo  '<span class="link_wrap" '.$style_padding_markup.'><'.$anchor_tag .' '.$target . $nofollow_attr . $aria_label_attr.' class="link_text'.esc_attr($link_text_classes).'" role="button" href="'.esc_url($url).'"><span class="text">'.wp_kses_post($link_text) .'</span>';
  echo '<div class="arrow-circle-animation-arrow-wrap"><svg class="arrow-circle-animation-arrow" viewBox="0 0 24 24" fill="currentColor"><path d="M16.0037 9.41421L7.39712 18.0208L5.98291 16.6066L14.5895 8H7.00373V6H18.0037V17H16.0037V9.41421Z"></path></svg>';
  echo '<svg class="arrow-circle-animation-arrow hover" viewBox="0 0 24 24" fill="currentColor"><path d="M16.0037 9.41421L7.39712 18.0208L5.98291 16.6066L14.5895 8H7.00373V6H18.0037V17H16.0037V9.41421Z"></path></svg></div>';
  echo '</'.$anchor_tag .'></span>';
  echo '</'.esc_html($heading_tag).'></div>';
}
else if( $btn_style === 'basic' || $btn_style === 'text-reveal-wave' || $btn_style === 'text-reveal' ) {

  $btn_attrs = '';
  $btn_classes = '';

  if( $btn_style === 'text-reveal-wave' ) {
    $link_text = preg_replace("/([^\\s>])(?!(?:[^<>]*)?>)/u","<span class=\"char\">$1</span>",$link_text);
  } else if ( $btn_style === 'text-reveal' ) {
    $btn_attrs = ' data-text="'.esc_attr($link_text).'"';
    $btn_classes = ' nectar-text-reveal-button__text';
  }



	echo '<div class="nectar-cta '.esc_attr($class) .esc_attr($dynamic_el_styles) .'" data-color="'. esc_attr($button_color) .'" data-using-bg="'.esc_attr($using_bg_color).'" data-style="'.esc_attr($btn_style).'" data-display="'. esc_attr($display) .'" data-alignment="'.esc_attr($alignment).'" data-text-color="'.esc_attr($text_color).'" '.$style_markup.'>';
  echo '<'.esc_html($heading_tag). $style.'>';

  $aria_label = '';
  if( ! empty( $aria_label_attr ) ) {
    $aria_label = $aria_label_attr;
  } else if( $btn_style === 'text-reveal-wave' ) {
    $aria_label = ' aria-label="'.wp_kses_post( $link_text_stored ).'"';
  }

  echo  '<span class="link_wrap" '.$style_padding_markup.'>'.$icon_output.'<'.$anchor_tag .' '.$target . $nofollow_attr . $aria_label.' class="link_text'.esc_attr($link_text_classes).'" role="button" href="'.esc_url($url).'"><span class="text'.$btn_classes.'"'.$btn_attrs.'>'.wp_kses_post($link_text) .'</span>';
  echo '</'.$anchor_tag .'></span></'.esc_html($heading_tag).'></div>';
}
// Next section link.
else if( $btn_style === 'next-section' ) {

  $using_next_section_color = 'false';
  $next_section_color_style = null;
  $next_section_track_ball_color_style = null;

  if( !empty($next_section_color) ) {
    $using_next_section_color = 'true';
  }

  if( $btn_type === 'down-arrow-bounce' ) {

		$dark_arrow_color = '';

    if( !empty($next_section_color) ) {
			$dark_arrow_color = ( '#ffffff' === $next_section_color ) ? ' dark-arrow' : '';
      $next_section_color_style = 'style="background-color: '.esc_attr($next_section_color).';"';
    }
    echo '<div class="nectar-next-section-wrap bounce'.esc_attr($dynamic_el_styles).'" '.$style_markup.' data-animation="'.esc_attr($next_section_down_arrow_animation).'" data-shad="'.esc_attr($next_section_shadow).'" data-align="'.esc_attr($alignment).'" data-custom-color="'.esc_attr($using_next_section_color).'"><a href="#" '.$next_section_color_style.' class="nectar-next-section skip-hash"><span class="screen-reader-text">'.esc_html__('Navigate to the next section','salient-core').'</span> <i class="fa fa-angle-down'.esc_attr($dark_arrow_color).'"></i> </a></div>';
  }
  else if( $btn_type === 'down-arrow-bordered' ) {

    if( !empty($next_section_color) ) {
      $next_section_color_style = 'style="border-color: '.esc_attr($next_section_color).'; color: '.esc_attr($next_section_color).';"';
    }
    echo '<div class="nectar-next-section-wrap down-arrow-bordered'.esc_attr($dynamic_el_styles).'" '.$style_markup.' data-shad="'.esc_attr($next_section_shadow).'" data-align="'.esc_attr($alignment).'" data-custom-color="'.esc_attr($using_next_section_color).'"><div class="inner" '.$next_section_color_style.'><a href="#" class="nectar-next-section skip-hash"><span class="screen-reader-text">'.esc_html__('Navigate to the next section','salient-core').'</span><i class="fa fa-angle-down top"></i><i class="fa fa-angle-down"></i></a></div></div>';
  }
  else if( $btn_type === 'mouse-wheel' ) {
    if( !empty($next_section_color) ) {
      $stroke_color = $next_section_color;
      $next_section_color_style = 'style="border-color: '.esc_attr($next_section_color).'; color: '.esc_attr($next_section_color).';"';
      $next_section_track_ball_color_style = 'style="background-color: '.esc_attr($next_section_color).';"';
    } else {
      $stroke_color = '#ffffff';
    }
    echo '<div class="nectar-next-section-wrap mouse-wheel'.esc_attr($dynamic_el_styles).'" '.$style_markup.' data-align="'.esc_attr($alignment).'" data-custom-color="'.esc_attr($using_next_section_color).'"><a href="#" '.$next_section_color_style.' class="nectar-next-section skip-hash"><span class="screen-reader-text">'.esc_html__('Navigate to the next section','salient-core').'</span><svg class="nectar-scroll-icon" viewBox="0 0 30 45" enable-background="new 0 0 30 45">
          <path class="nectar-scroll-icon-path" fill="none" stroke="'.esc_attr($stroke_color).'" stroke-width="2" stroke-miterlimit="10" d="M15,1.118c12.352,0,13.967,12.88,13.967,12.88v18.76  c0,0-1.514,11.204-13.967,11.204S0.931,32.966,0.931,32.966V14.05C0.931,14.05,2.648,1.118,15,1.118z"></path>
        </svg><span class="track-ball" '.$next_section_track_ball_color_style.'></span></a></div>';
  }

  else if ( $btn_type === 'minimal-arrow' ) {

    if( !empty($next_section_color) ) {
      $stroke_color = $next_section_color;
    } else {
      $stroke_color = '#ffffff';
    }

    echo '<div class="nectar-next-section-wrap minimal-arrow'.esc_attr($dynamic_el_styles).'" '.$style_markup.' data-align="'.esc_attr($alignment).'" data-custom-color="'.esc_attr($using_next_section_color).'">
    <a href="#" class="nectar-next-section skip-hash">
      <span class="screen-reader-text">'.esc_html__('Navigate to the next section','salient-core').'</span>
      <svg class="next-arrow" width="40px" height="68px" viewBox="0 0 40 50" xml:space="preserve">
      <path stroke="'.esc_attr($stroke_color).'" stroke-width="2" fill="none" d="M 20 0 L 20 51"></path>
      <polyline stroke="'.esc_attr($stroke_color).'" stroke-width="2" fill="none" points="12, 44 20, 52 28, 44"></polyline>
      </svg>
    </a>
  </div>';
  } else if ( $btn_type === 'minimal-arrow-alt' ) {

    if( !empty($next_section_color) ) {
      $stroke_color = $next_section_color;
    } else {
      $stroke_color = '#ffffff';
    }

    echo '<div class="nectar-next-section-wrap next-section-down-arrow-alt-animation-'.esc_attr($next_section_down_arrow_alt_animation).' minimal-arrow-alt'.esc_attr($dynamic_el_styles).'" '.$style_markup.' data-align="'.esc_attr($alignment).'" data-custom-color="'.esc_attr($using_next_section_color).'">
    <a href="#" class="nectar-next-section skip-hash">
      <span class="screen-reader-text">'.esc_html__('Navigate to the next section','salient-core').'</span>
      <svg class="next-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="'.esc_attr($stroke_color).'">
        <path d="M13.0001 1.99974L11.0002 1.9996L11.0002 18.1715L7.05044 14.2218L5.63623 15.636L12.0002 22L18.3642 15.636L16.9499 14.2218L13.0002 18.1716L13.0001 1.99974Z"></path>
      </svg>
      <svg class="next-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="'.esc_attr($stroke_color).'">
        <path d="M13.0001 1.99974L11.0002 1.9996L11.0002 18.1715L7.05044 14.2218L5.63623 15.636L12.0002 22L18.3642 15.636L16.9499 14.2218L13.0002 18.1716L13.0001 1.99974Z"></path>
      </svg>
    </a>
  </div>';
  }


}

// All others.
else {

    $cta_text = (!empty($text)) ? '<span class="text">'.wp_kses_post($text).' </span>' : '';

  echo '<div class="nectar-cta '.esc_attr($class) .esc_attr($dynamic_el_styles) .'" data-color="'. esc_attr($button_color) .'" data-using-bg="'.esc_attr($using_bg_color).'" data-display="'. esc_attr($display) .'" data-style="'.esc_attr($btn_style).'" data-alignment="'.esc_attr($alignment).'" data-text-color="'.esc_attr($text_color).'" '.$style_markup.'>';
  echo '<'.esc_html($heading_tag). $style.'> '.$cta_text;
  $border_color_attr = (!empty($underline_border_color)) ? 'style="border-color: '.esc_attr($underline_border_color).';"' : '';
  echo  '<span class="link_wrap" '.$style_padding_markup.'>'.$icon_output.'<'.$anchor_tag .' '.$target . $nofollow_attr . $aria_label_attr.' class="link_text'.esc_attr($link_text_classes).'" '.$border_color_attr.' role="button" href="'.esc_url($url).'">'.wp_kses_post($link_text).'</'.$anchor_tag .'></span>';
  echo '</'.esc_html($heading_tag).'></div>';
}


?>