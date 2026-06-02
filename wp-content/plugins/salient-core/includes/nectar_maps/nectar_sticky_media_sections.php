<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Build params array with responsive fields merged in (PHP 7.4 compatible).
$nectar_sticky_media_params = array(

    array(
        "type" => "dropdown",
        "heading" => esc_html__("Type", "salient-core"),
        "param_name" => "type",
        "admin_label" => false,
        "value" => array(
            "Sticky Media, Scrolling Content" => "default",
            "Sticky Scroll Pinned Sections" => "scroll-pinned-sections",
            "Horizontal Scrolling" => "horizontal-scrolling",
            "Layered Card Reveal" => "layered-card-reveal",
        ),
        'save_always' => true,
    ),

    // array(
    //     "type" => "dropdown",
    //     "heading" => esc_html__("Effect", "salient-core"),
    //     "param_name" => "layered_card_reveal_effect",
    //     "admin_label" => false,
    //     "value" => array(
    //         "Stack" => "stack",
    //         "Staggered Wave" => "staggered-wave",
    //     ),
    //     'save_always' => true,
    //     "dependency" => Array('element' => "type", 'value' => 'layered-card-reveal'),
    // ),

    array(
        "type"              => "nectar_numerical",
        "class"             => "",
        "heading"           => '<span class="group-title">' . esc_html__("Card Width", "salient-core") . "</span>",
        "placeholder"       => '',
        "edit_field_class" => "desktop layered-card-reveal-width-device-group zero-floor",
        "param_name"        => "layered_card_reveal_width_desktop",
        "dependency"        => Array('element' => "type", 'value' => 'layered-card-reveal'),
        "description"       => ""
    ),
    array(
        "type"              => "nectar_numerical",
        "class"             => "",
        "heading"           => '',
        "placeholder"       => '',
        "edit_field_class" => "tablet layered-card-reveal-width-device-group zero-floor",
        "param_name"        => "layered_card_reveal_width_tablet",
        "dependency"        => Array('element' => "type", 'value' => 'layered-card-reveal'),
        "description"       => ""
    ),
    array(
        "type"              => "nectar_numerical",
        "class"             => "",
        "heading"           => '',
        "placeholder"       => '',
        "edit_field_class" => "phone layered-card-reveal-width-device-group zero-floor",
        "param_name"        => "layered_card_reveal_width_phone",
        "dependency"        => Array('element' => "type", 'value' => 'layered-card-reveal'),
        "description"       => ""
    ),

    array(
        "type" => "dropdown",
        "class" => "",
        "heading" => esc_html__("Card Aspect Ratio", 'salient-core'),
        'save_always' => true,
        "param_name" => "layered_card_reveal_aspect_ratio",
        "value" => array(
            "1:1" => "1-1",
            "4:3" => "4-3",
            "3:2" => "3-2",
            "16:9" => "16-9",
            "2:1" => "2-1",
            "4:5" => "4-5",
        ),
        "dependency" => array('element' => "type", 'value' => 'layered-card-reveal'),
        "description" => '',
    ),



    array(
        "type" => "dropdown",
        "heading" => esc_html__("Effect", "salient-core"),
        "param_name" => "effect",
        "admin_label" => false,
        "value" => array(
            "None" => "default",
            "Overlapping" => "overlapping",
            "Scale" => "scale",
            "Blurred Scale" => "scale_blur",
            "Fade Scale" => "scale_fade",
        ),
        'save_always' => true,
        "dependency" => Array('element' => "type", 'value' => 'scroll-pinned-sections'),
    ),

    array(
        "type" => 'checkbox',
        "heading" => esc_html__("Stacked Appearance", "salient-core"),
        "param_name" => "stacking_effect",
        'edit_field_class' => 'vc_col-xs-12 salient-fancy-checkbox',
        "description" => esc_html__("Keeps the top edges of all sections slightly visible to create a visual stack.", "salient-core"),
        "value" => Array(esc_html__("Yes, please", "salient-core") => 'yes'),
        "dependency" => Array('element' => "type", 'value' => 'scroll-pinned-sections'),
    ),

);


// Effect Enabled
$nectar_sticky_media_params = array_merge(
    $nectar_sticky_media_params,
    SalientWPbakeryParamGroups::responsive_field(array(
        'type'          => 'dropdown',
        'heading'       => esc_html__('Effect Enabled', 'salient-core'),
        'param_name'    => 'effect_enabled',
        'group_name'    => 'effect-enabled',
        'desktop_value' => array(
            esc_html__('Enabled', 'salient-core')  => 'enabled',
            esc_html__('Disabled', 'salient-core') => 'disabled',
        ),
        'tablet_value'  => array(
            esc_html__('Enabled', 'salient-core')  => 'inherit',
            esc_html__('Disabled', 'salient-core') => 'disabled',
        ),
        'phone_value'   => array(
            esc_html__('Enabled', 'salient-core')  => 'inherit',
            esc_html__('Disabled', 'salient-core') => 'disabled',
        ),
        'dependency'    => array('element' => 'type', 'value' => ['scroll-pinned-sections', 'layered-card-reveal']),
        'save_always'   => true,
    ))
);

// Continue with remaining params.
$nectar_sticky_media_params = array_merge($nectar_sticky_media_params, array(

    array(
        'type' => 'nectar_numerical',
        'heading' => '<span class="group-title">' . esc_html__('Overlap Amount', 'salient-core') . '</span>',
        'param_name' => 'overlapping_overlap_amount_desktop',
        'value' => '50',
        "class"             => "",
        "placeholder"       => '',
        'dependency' => Array('element' => "effect", 'value' => 'overlapping'),
        'edit_field_class' => 'desktop overlap-amount-device-group zero-floor',
        'description' => ''
    ),
        array(
            'type' => 'nectar_numerical',
            'heading' => '',
            "class"             => "",
            "placeholder"       => '',
            'param_name' => 'overlapping_overlap_amount_tablet',
            'value' => '',
            'dependency' => Array('element' => "effect", 'value' => 'overlapping'),
            'edit_field_class' => 'tablet overlap-amount-device-group zero-floor',
            'description' => ''
        ),
        array(
            'type' => 'nectar_numerical',
            'heading' => '',
            "class"             => "",
            "placeholder"       => '',
            'param_name' => 'overlapping_overlap_amount_phone',
            'value' => '',
            'dependency' => Array('element' => "effect", 'value' => 'overlapping'),
            'edit_field_class' => 'phone overlap-amount-device-group zero-floor',
            'description' => ''
        ),

        array(
            "type" => "colorpicker",
            "heading" => esc_html__("Fade Color", "salient-core"),
            "param_name" => "scale_fade_color",
            "value" => "",
            "dependency" => Array('element' => "effect", 'value' => 'scale_fade'),
            "description" => esc_html__("Select the color that your fade out will display in.", "salient-core"),
        ),

        array(
            "type" => "dropdown",
            "heading" => esc_html__("Effect", "salient-core"),
            "param_name" => "horizontal_effect",
            "admin_label" => false,
            "value" => array(
                "None" => "default",
                "Stacking" => "stacking",
            ),
            'save_always' => true,
            "dependency" => Array('element' => "type", 'value' => 'horizontal-scrolling'),
        ),






        array(
            'type' => 'nectar_range_slider',
            'heading' => esc_html__('Section Width', 'salient-core'),
            'param_name' => 'horizontal_section_width',
            'value' => '75',
            'dependency' => Array('element' => "type", 'value' => 'horizontal-scrolling'),
            'options' => array(
                'min' => '25',
                'max' => '100',
                'step' => '1',
                'suffix' => '%'
            ),
            'description' => esc_html__('Set the width of the section. This is only applicable when horizontal scrolling is active on desktop views.', 'salient-core')
        ),


        array(
			"type" => "dropdown",
			"heading" => esc_html__("Section Height", "salient-core"),
            "dependency" => Array('element' => "type", 'value' => ['scroll-pinned-sections', 'horizontal-scrolling']),
            "param_name" => "section_height",
			"admin_label" => false,
			"value" => array(
                "50%" => "50vh",
                "55%" => "55vh",
                "60%" => "60vh",
                "65%" => "65vh",
                "70%" => "70vh",
                "75%" => "75vh",
                "80%" => "80vh",
                "85%" => "85vh",
                "90%" => "90vh",
                "95%" => "95vh",
                "100%" => "100vh",
			),
			'save_always' => true,
        ),

        array(
            "type" => "nectar_numerical",
            "heading" => esc_html__("Section Gap", "salient-core"),
            'param_name' => 'horizontal_section_gap_desktop',
            'edit_field_class' => 'zero-floor vc_col-xs-12',
            'value' => '10px',
            'save_always' => true,
            'dependency' => ['element' => "type", 'value' => 'horizontal-scrolling'],
        ),

        array(
            "type"              => "nectar_numerical",
            "class"             => "",
            "heading"           => '<span class="group-title">' . esc_html__("Max Height", "salient-core") . "</span>",
            "placeholder"       => '',
            "edit_field_class" => "desktop max-height-device-group zero-floor",
            "param_name"        => "section_max_height_desktop",
            "dependency"        => Array('element' => "type", 'value' => 'scroll-pinned-sections'),
            "description"       => ""
        ),
        array(
            "type"              => "nectar_numerical",
            "class"             => "",
            "heading"           => '',
            "placeholder"       => '',
            "edit_field_class" => "tablet max-height-device-group zero-floor",
            "param_name"        => "section_max_height_tablet",
            "dependency"        => Array('element' => "type", 'value' => 'scroll-pinned-sections'),
            "description"       => ""
        ),
        array(
            "type"              => "nectar_numerical",
            "class"             => "",
            "heading"           => '',
            "placeholder"       => '',
            "edit_field_class" => "phone max-height-device-group zero-floor",
            "param_name"        => "section_max_height_phone",
            "dependency"        => Array('element' => "type", 'value' => 'scroll-pinned-sections'),
            "description"       => ""
        ),

        array(
			"type" => 'checkbox',
			"heading" => esc_html__("Subtract Navigation Height from Section Height", "salient-core"),
			"param_name" => "subtract_nav_height",
			'edit_field_class' => 'vc_col-xs-12 salient-fancy-checkbox',
			"description" => '',
			"value" => Array(esc_html__("Yes, please", "salient-core") => 'yes'),
			"dependency" => Array('element' => "type", 'value' => ['scroll-pinned-sections', 'horizontal-scrolling', 'layered-card-reveal']),
		),
        array(
			"type" => "dropdown",
			"heading" => esc_html__("Content Alignment", "salient-core"),
            "dependency" => Array('element' => "type", 'value' => ['scroll-pinned-sections', 'horizontal-scrolling', 'layered-card-reveal']),
            "param_name" => "content_alignment",
			"admin_label" => false,
			"value" => array(
                esc_html__("Middle", "salient-core") => "middle",
                esc_html__("Stretch", "salient-core") => "stretch",
                esc_html__("Top", "salient-core") => "top",
                esc_html__("Bottom", "salient-core") => "bottom",
			),
			'save_always' => true,
        ),
        array(
			"type" => 'checkbox',
			"heading" => esc_html__("Section Navigation", "salient-core"),
			"param_name" => "navigation",
			'edit_field_class' => 'vc_col-xs-12 salient-fancy-checkbox',
			"value" => Array(esc_html__("Yes, please", "salient-core") => 'yes'),
			"dependency" => Array('element' => "type", 'value' => 'scroll-pinned-sections'),
		),

        array(
            "type" => "colorpicker",
            "class" => "",
            "heading" => esc_html__("Navigation Color", "salient-core"),
            "param_name" => "navigation_color",
            "value" => "",
            "dependency" => Array('element' => "navigation", 'not_empty' => true),
            "description" => esc_html__("Select the color that your navigation will display in.", "salient-core"),
          ),


        array(
			"type" => "dropdown",
			"heading" => esc_html__("Content Position", "salient-core"),
			"param_name" => "content_position",
			"admin_label" => false,
            "dependency" => Array('element' => "type", 'value' => 'default'),
			"value" => array(
                "Right" => "right",
                "Left" => "left",
			),
			'save_always' => true,
        ),

        array(
			"type" => "dropdown",
			"heading" => esc_html__("Content Spacing", "salient-core"),
            "dependency" => Array('element' => "type", 'value' => 'default'),
			"param_name" => "content_spacing",
			"admin_label" => false,
			"value" => array(
                "20%" => "20vh",
                "25%" => "25vh",
                "30%" => "30vh",
                "35%" => "35vh",
                "40%" => "40vh",
                "45%" => "45vh",
                "50%" => "50vh",
                "55%" => "55vh"
			),
			'save_always' => true,
		),


		array(
			"type" => "dropdown",
			"heading" => esc_html__("Media Width", "salient-core"),
            "dependency" => Array('element' => "type", 'value' => 'default'),
			"param_name" => "media_width",
            "admin_label" => false,
            "edit_field_class" => "nectar-one-half",
			"value" => array(
                "75%" => "75%",
                "70%" => "70%",
				"65%" => "65%",
                "60%" => "60%",
                "55%" => "55%",
                "50%" => "50%",
                "45%" => "45%",
                "40%" => "40%",
			),
			'save_always' => true,
        ),
        array(
			"type" => "dropdown",
			"heading" => esc_html__("Media Height", "salient-core"),
            "dependency" => Array('element' => "type", 'value' => 'default'),
            "param_name" => "media_height",
            "edit_field_class" => "nectar-one-half nectar-one-half-last",
			"admin_label" => false,
			"value" => array(
                "50%" => "50vh",
                "55%" => "55vh",
                "60%" => "60vh",
                "65%" => "65vh",
                "70%" => "70vh",
                "75%" => "75vh",
                "80%" => "80vh",
                "85%" => "85vh",
                "90%" => "90vh",
                "95%" => "95vh",
                "100%" => "100vh",
			),
			'save_always' => true,
        ),

        array(
          "type" => "dropdown",
          "heading" => esc_html__("Mobile Media Aspect Ratio", "salient-core"),
          "dependency" => Array('element' => "type", 'value' => 'default'),
              "param_name" => "mobile_aspect_ratio",
              "admin_label" => false,
              "value" => array(
                "16:9" => "16-9",
                "1:1" => "1-1",
                "3:2" =>  "3-2",
                "4:3" => "4-3",
                "4:5" => "4-5",
              ),
            'save_always' => true,
          ),

    array(
        "type" => "dropdown",
        "heading" => esc_html__("Border Radius", "salient-core"),
        'save_always' => true,
        "param_name" => "border_radius",
        "value" => array(
            esc_html__("0px", "salient-core") => "none",
            esc_html__("3px", "salient-core") => "3px",
            esc_html__("5px", "salient-core") => "5px",
            esc_html__("10px", "salient-core") => "10px",
            esc_html__("15px", "salient-core") => "15px",
            esc_html__("20px", "salient-core") => "20px",
            esc_html__("30px", "salient-core") => "30px"
        ),
    ),

)); // End array_merge for remaining params.

return array(
    "name" => esc_html__("Sticky Content Sections", "salient-core"),
    "base" => "nectar_sticky_media_sections",
    "icon" => "icon-wpb-recent-projects",
    "show_settings_on_create" => false,
    "is_container" => true,
    "as_parent" => array(
        'only' => 'nectar_sticky_media_section,vc_row'
    ),
    "category" => esc_html__('Content', 'salient-core'),
    "description" => esc_html__('Sticky Videos and Images', 'salient-core'),
    "js_view" => 'VcColumnView',
    "params" => $nectar_sticky_media_params
);

?>