<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$image_trail_params = array(
  array(
    "type" => "nectar_radio_tab_selection",
    "class" => "",
    'save_always' => true,
    "heading" => esc_html__(" Type", "salient-core"),
    "param_name" => "type",
      "options" => array(
        esc_html__("Images", "salient-core") => "images",
        esc_html__("Text", "salient-core") => "text",
      ),
    ),
		array(
      'type' => 'attach_images',
      'heading' => esc_html__( 'Images', 'js_composer' ),
      'param_name' => 'images',
      'value' => '',
      'dependency' => array(
        'element' => 'type',
        'value' => array('images'),
      ),
      'description' => esc_html__( 'Select images from media library.', 'js_composer' )
    ),
    array(
      "type" => "nectar_text_repeater",
      "heading" => '',
      "param_name" => "text_array",
      "description" => '',
      "edit_field_class" => "",
      "dependency" => array(
        'element' => 'type',
        'value' => array('text'),
      ),
    ),
    array(
      "type" => "nectar_color_repeater",
      "heading" => '',
      "param_name" => "text_color_array",
      "description" => '',
      "edit_field_class" => "",
      "dependency" => array(
        'element' => 'type',
        'value' => array('text'),
      ),
    ),
    array(
      'type' => 'dropdown',
      'class' => '',
      'save_always' => true,
      'admin_label' => true,
      'heading' => esc_html__( 'Image Size', 'salient-core' ),
      'param_name' => 'image_size',
      'value' => array(
          esc_html__('Large', 'salient-core') => 'large',
          esc_html__('Medium', 'salient-core') => 'medium',
          esc_html__('Medium Large', 'salient-core') => 'medium_large',
          esc_html__('Small', 'salient-core') => 'thumbnail',
          esc_html__('Small Landscape', 'salient-core') => 'portfolio-thumb',
          esc_html__('Large Square', 'salient-core') => 'medium_featured',
          esc_html__('Square', 'salient-core') => 'regular',
          esc_html__('Full', 'salient-core') => 'full',
      ),
      "dependency" => array(
        'element' => 'type',
        'value' => array('images'),
      ),
      'description' => esc_html__( 'Select the image size (resolution) to load. Options are based on the image sizes defined in WordPress.', 'salient-core' ),
      'std' => 'large',
  ),
    array(
      "type"              => "nectar_numerical",
      "class"             => "",
      "edit_field_class"  => "desktop image-width-device-group vc_col-xs-12",
      "heading"           => '<span class="group-title">' . esc_html__("Image Width", "salient-core") . "</span>",
      "value"             => '200px',
      "placeholder"       => esc_html__("Width", "salient-core"),
      "dependency" => array(
        'element' => 'type',
        'value' => array('images'),
      ),
      "param_name"        => "image_width_desktop",
      "description"       => ""
  ),
  array(
      "type"              => "nectar_numerical",
      "class"             => "",
      "edit_field_class"  => "tablet image-width-device-group",
      "heading"           => "<span class='attr-title'>" . esc_html__("Image Width", "salient-core") . "</span>",
      "value"             => "",
      "placeholder"       => esc_html__("Width", "salient-core"),
      "param_name"        => "image_width_tablet",
      "dependency" => array(
        'element' => 'type',
        'value' => array('images'),
      ),
      "description"       => ""
  ),
  array(
    "type"              => "nectar_numerical",
    "class"             => "",
    "edit_field_class"  => "phone image-width-device-group",
    "heading"           => "<span class='attr-title'>" . esc_html__("Image Width", "salient-core") . "</span>",
    "value"             => "",
    "placeholder"       => esc_html__("Width", "salient-core"),
    "param_name"        => "image_width_phone",
    "dependency" => array(
      'element' => 'type',
      'value' => array('images'),
    ),
    "description"       => ""
  ),
    array(
      "type" => "dropdown",
      "class" => "",
      'save_always' => true,
      "heading" => "Animation In",
      "param_name" => "animation_in",
      "admin_label" => true,
      "value" => array(
        esc_html__("Scale", "salient-core") => "scale",
        esc_html__("Circle Mask", "salient-core") => "circle_mask",
        esc_html__("None", "salient-core") => "none",
      )
    ),
    array(
      "type" => "dropdown",
      "class" => "",
      'save_always' => true,
      "heading" => "Animation Out",
      "param_name" => "animation_out",
      "admin_label" => true,
      "value" => array(
        esc_html__("Scale", "salient-core") => "scale",
        esc_html__("Fade", "salient-core") => "fade",
        esc_html__("None", "salient-core") => "none",
      )
    ),
    array(
      "type" => "dropdown",
      "class" => "",
      "heading" => esc_html__("Aspect Ratio", 'salient-core'),
      'save_always' => true,
      "param_name" => "aspect_ratio",
      "dependency" => array(
        'element' => 'type',
        'value' => array('images'),
      ),
      "value" => array(
        esc_html__("Auto", "salient-core") => "auto",
        "1:1" => "1-1",
        "4:3" => "4-3",
        "3:2" => "3-2",
        "16:9" => "16-9",
        "2:1" => "2-1",
        "4:5" => "4-5",
      ),
    ),
    array(
      'type' => 'nectar_range_slider',
      'heading' => esc_html__( 'Border Radius', 'salient-core' ),
      'description' => '',
      'param_name' => 'border_radius',
      'value' => '0',
      'save_always' => true,
      'options' => array(
        'min' => '0',
        'max' => '50',
        'step' => '1',
        'suffix' => 'px'
      )
    ),
    array(
      'type' => 'nectar_range_slider',
      'heading' => esc_html__( 'Frequency', 'salient-core' ),
      'description' => esc_html__( 'How often does the content change based on the number of pixels the mouse must move before swapping content.', 'salient-core' ),
      'param_name' => 'frequency',
      'value' => '100',
      'save_always' => true,
      'options' => array(
        'min' => '20',
        'max' => '200',
        'step' => '1',
        'suffix' => 'px'
      )
    ),
    array(
      'type' => 'nectar_range_slider',
      'heading' => esc_html__( 'Content Display Duration', 'salient-core' ),
      'description' => esc_html__( 'How long your content will remain visible for before disappearing.', 'salient-core' ),
      'param_name' => 'duration',
      'value' => '1',
      'options' => array(
        'min' => '0.25',
        'max' => '2',
        'step' => '0.05',
        'suffix' => 's'
      )
    ),
    array(
      'type' => 'nectar_range_slider',
      'heading' => esc_html__( 'Smoothing', 'salient-core' ),
      'param_name' => 'lerp',
      'value' => '30',
      'options' => array(
        'min' => '0',
        'max' => '100',
        'step' => '1',
      )
    ),
    array(
			"type" => "checkbox",
			"class" => "",
			"heading" => esc_html__("Random Order", 'salient-core'),
			"param_name" => "randomize",
			"value" => Array(esc_html__("Yes", 'js_composer') => 'true'),
			"description" => "",
			'edit_field_class' => 'vc_col-xs-12 salient-fancy-checkbox',
		),

    array(
			"type" => "checkbox",
			"class" => "",
			"heading" => esc_html__("Random Rotation", 'salient-core'),
			"param_name" => "randomize_rotation",
			"value" => Array(esc_html__("Yes", 'js_composer') => 'true'),
			"description" => "",
			'edit_field_class' => 'vc_col-xs-12 salient-fancy-checkbox',
		),
    array(
			"type" => "colorpicker",
			"class" => "",
			"heading" => "Font Color",
			"param_name" => "font_color",
			"value" => "#000",
      "dependency" => array(
        'element' => 'type',
        'value' => array('text'),
      ),
			"description" => "",
		),

);

$size_group = SalientWPbakeryParamGroups::size_group('', '100%', '400px');
$position_group = SalientWPbakeryParamGroups::position_group( esc_html__( 'Positioning', 'salient-core' ), true );

$imported_groups = array( $position_group, $size_group );

foreach ( $imported_groups as $group ) {

    foreach ( $group as $option ) {
      $image_trail_params[] = $option;
    }

}

$image_trail_params[] = array(
  "type" => "dropdown",
  "heading" => esc_html__("Overflow Visibility", "salient-core"),
  "param_name" => "overflow",
  "value" => array(
    "Hidden" => "hidden",
    "Visible" => "visible",
  ),
  'save_always' => true,
);


return array(
	"name" => esc_html__("Content Trail", "salient-core"),
	"base" => "nectar_content_trail",
	"icon" => "icon-wpb-single-image",
	"category" => esc_html__('Media', 'salient-core'),
	"description" => esc_html__('Mouse trail of content', 'salient-core'),
	"params" => $image_trail_params
);

?>