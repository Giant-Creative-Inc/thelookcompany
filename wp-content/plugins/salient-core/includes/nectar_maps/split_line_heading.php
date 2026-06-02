<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}
$split_line_heading_params = array(
	array(
		"type" => "textarea_html",
		"heading" => esc_html__("Text Content", "salient-core"),
		"param_name" => "content",
		"value" => '',
		"description" => '',
		"admin_label" => true,
		'dependency' => array(
			'element' => 'animation_type',
			'value' => array('default'),
		),
	),
	array(
		"type" => "textarea",
		"heading" => esc_html__("Text", "salient-core"),
		"param_name" => "text_content",
		"admin_label" => true,
		'dependency' => array(
			'element' => 'animation_type',
			'value' => array('line-reveal-by-space', 'letter-fade-reveal', 'twist-in'),
		),
		"description" => ''
	),
	// array(
	// 	"type" => "dropdown",
	// 	"class" => "",
	// 	'save_always' => true,
	// 	"heading" => "Text Effect",
	// 	'dependency' => array(
	// 		'element' => 'animation_type',
	// 		'value' => array('line-reveal-by-space'),
	// 	),
	// 	"description" => esc_html__("Animation Type", "salient-core"),
	// 	"param_name" => "line_reveal_by_space_text_effect",
	// 	"value" => array(
	// 		esc_html__("Reveal from Bottom", 'salient-core') => "default",
	// 		esc_html__("Blur from Bottom", 'salient-core') => "blur-bottom",
	// 		esc_html__("Fade from Bottom", 'salient-core') => "fade-bottom",
	// 		esc_html__("Rotate from Bottom", 'salient-core') => "twist-bottom",
	// 		esc_html__("Twist from Bottom", 'salient-core') => "twist-bottom-2",

	// 		esc_html__("Reveal Single Letter from Bottom", 'salient-core') => "letter-reveal-bottom",
	// 		esc_html__('Blur Single Letter from Bottom', 'salient-core') => "letter-reveal-blur-bottom",
	// 		esc_html__("Fade Single Letter from Bottom", 'salient-core') => "letter-reveal-fade-bottom",
	// 		esc_html__("Reveal Single Letter from Top", 'salient-core') => "letter-reveal-top",
	// 		esc_html__('Blur Single Letter from Top', 'salient-core') => "letter-reveal-blur-top",

	// 		esc_html__("Scroll Opacity Reveal", 'salient-core') => "scroll-opacity-reveal",
	// 		esc_html__("None", 'salient-core') => "none",
	// 	)
	// ),

	array(
        'type' => 'nectar_radio_html',
        'class' => '',
        'save_always' => true,
		"heading" => "Text Effect",
		'dependency' => array(
			'element' => 'animation_type',
			'value' => array('line-reveal-by-space'),
		),
		"description" => esc_html__("Animation Type", "salient-core"),
		"param_name" => "line_reveal_by_space_text_effect",
        'options' => array(
			'<span>' .esc_html__('Word Animations', 'salient-core') . '</span>' => 'SKIP_OPTION',
            '<div data-anim="reveal" class="nectar-text"><span>Reveal</span></div>' => 'default',
            '<div data-anim="blur-bottom" class="nectar-text"><span>Blur</span></div>' => 'blur-bottom',
            '<div data-anim="fade-bottom" class="nectar-text"><span>Fade</span></div>' => 'fade-bottom',
            '<div data-anim="twist-bottom" class="nectar-text"><span>Rotate</span></div>' => 'twist-bottom',
            '<div data-anim="twist-bottom-2" class="nectar-text"><span>Twist</span></div>' => 'twist-bottom-2',
			'<span>' .esc_html__('Single Letter From Bottom', 'salient-core') . '</span>' => 'SKIP_OPTION',
            '<div data-anim="letter-reveal-bottom" class="nectar-text"><span>R</span><span>e</span><span>v</span><span>e</span><span>a</span><span>l</span></div>' => 'letter-reveal-bottom',
            '<div data-anim="letter-reveal-blur-bottom" class="nectar-text"><span>B</span><span>l</span><span>u</span><span>r</span></div>' => 'letter-reveal-blur-bottom',
            '<div data-anim="letter-reveal-fade-bottom" class="nectar-text"><span>F</span><span>a</span><span>d</span><span>e</span></div>' => 'letter-reveal-fade-bottom',
			'<span>' .esc_html__('Single Letter from Top', 'salient-core') . '</span>' => 'SKIP_OPTION',
            '<div data-anim="letter-reveal-top" class="nectar-text"><span>R</span><span>e</span><span>v</span><span>e</span><span>a</span><span>l</span></div>' => 'letter-reveal-top',
            '<div data-anim="letter-reveal-blur-top" class="nectar-text"><span>B</span><span>l</span><span>u</span><span>r</span></div>' => 'letter-reveal-blur-top',
			'<div data-anim="letter-reveal-fade-top" class="nectar-text"><span>F</span><span>a</span><span>d</span><span>e</span></div>' => 'letter-reveal-fade-top',
			'<span>' .esc_html__('Scroll Position', 'salient-core') . '</span>' => 'SKIP_OPTION',
            '<div data-anim="scroll-opacity-reveal" class="nectar-text"><span>O</span><span>p</span><span>a</span><span>c</span><span>i</span><span>t</span><span>y</span></div>' => 'scroll-opacity-reveal',
			'<span>' .esc_html__('No Animation', 'salient-core') . '</span>' => 'SKIP_OPTION',
            '<div data-anim="none" class="nectar-text"><span>None</span></div>' => 'none',
        ),
        'description' => '',
        'std' => 'default',
    ),


	array(
		"type" => "dropdown",
		"class" => "",
		'save_always' => true,
		"heading" => "Text Element",
		'dependency' => array(
			'element' => 'animation_type',
			'value' => array('line-reveal-by-space', 'letter-fade-reveal', 'twist-in'),
		),
		"description" => esc_html__("Choose what element your text will inherit styling from", "salient-core"),
		"param_name" => "font_style",
		"value" => array(
			"H1" => "h1",
			"H2" => "h2",
			"H3" => "h3",
			"H4" => "h4",
			"H5" => "h5",
			"H6" => "h6",
			"Paragraph" => "p",
			"Italic" => "i",
		)
	),
	array(
		"type" => "colorpicker",
		"class" => "",
		"heading" => "Text Color",
		"param_name" => "text_color",
		"value" => "",
		'dependency' => array(
			'element' => 'animation_type',
			'value' => array('line-reveal-by-space', 'letter-fade-reveal', 'twist-in'),
		),
		"description" => esc_html__("Defaults to light or dark based on the current row text color.", "salient-core")
	),
	array(
		"type" => "checkbox",
		"class" => "",
		'edit_field_class' => 'vc_col-xs-12 salient-fancy-checkbox',
		"heading" => esc_html__("Stagger Animation", "salient-core"),
		"param_name" => "stagger_animation",
		"value" => array(esc_html__("Yes", "salient-core") => 'true'),
		"description" => esc_html__("Causes each word to animate in at a slightly different rate for a more dramatic effect.", "salient-core"),
		'dependency' => array(
			'element' => 'animation_type',
			'value' => array('line-reveal-by-space'),
		),
	),
	array(
		"type" => "checkbox",
		"class" => "",
		'edit_field_class' => 'vc_col-xs-12 salient-fancy-checkbox',
		"heading" => esc_html__("Fit Text to Container", "salient-core"),
		"param_name" => "fit_text_to_container",
		"value" => array(esc_html__("Yes", "salient-core") => 'true'),
		"description" => esc_html__("This will scale your text so that it fits perfectly to the parent column.", "salient-core"),
		"dependency" => array('callback' => 'nectarFitTextCallback'),
	),

	array(
		"type" => "checkbox",
		"class" => "",
		'edit_field_class' => 'vc_col-xs-12 salient-fancy-checkbox',
		"heading" => esc_html__("Disable Animation on Mobile", "salient-core"),
		"param_name" => "mobile_disable_animation",
		"value" => array(esc_html__("Yes", "salient-core") => 'true'),
		'dependency' => array(
			'element' => 'animation_type',
			'value' => array('line-reveal-by-space'),
		),
	),
	array(
		"type" => "textfield",
		"heading" => esc_html__("Animation Delay", "salient-core"),
		"param_name" => "animation_delay",
		"edit_field_class" => "nectar-one-half",
		"description" => esc_html__("Enter delay (in milliseconds) if needed e.g. 150.", "salient-core")
	),
	array(
		"type" => "textfield",
		"class" => "",
		"heading" => esc_html__("Animation Offset", "salient-core"),
		"param_name" => "animation_offset",
		"edit_field_class" => "nectar-one-half nectar-one-half-last",
		"admin_label" => false,
		"description" => esc_html__("Optionally specify the offset from the top of the screen for when the animation will trigger. Defaults to 80%.", "salient-core"),
	),
);

$font_size_group = SalientWPbakeryParamGroups::font_sizing_group('font_size', 'Custom Font Size');

$imported_groups = array($font_size_group);

foreach ($imported_groups as $group) {

	foreach ($group as $option) {
		$split_line_heading_params[] = $option;
	}
}

$split_line_heading_params = array_merge(
	$split_line_heading_params,
	array(

		array(
			"type" => "textfield",
			"heading" => esc_html__("Max Width", "salient-core"),
			"param_name" => "max_width",
			"admin_label" => false,
			"description" => esc_html__("Optionally enter your desired max width in pixels without the \"px\", e.g. 200", "salient-core")
		),
		array(
			"type" => "dropdown",
			"class" => "",
			'save_always' => true,
			"heading" => "Content Align",
			'dependency' => array(
				'element' => 'animation_type',
				'value' => array('line-reveal-by-space', 'letter-fade-reveal', 'twist-in'),
			),
			"edit_field_class" => "nectar-one-half",
			"description" => esc_html__("When using a max width smaller than the container, you can use this to optionally define how to align the text content.", "salient-core"),
			"param_name" => "content_alignment",
			"value" => array(
				esc_html__("Default", 'salient-core') => "default",
				esc_html__("Left", 'salient-core') => "left",
				esc_html__("Center", 'salient-core') => "center",
				esc_html__("Right", 'salient-core') => 'right'
			)
		),
		array(
			"type" => "dropdown",
			"class" => "",
			'save_always' => true,
			"heading" => "Mobile Content Align",
			'dependency' => array(
				'element' => 'animation_type',
				'value' => array('line-reveal-by-space', 'letter-fade-reveal', 'twist-in'),
			),
			"edit_field_class" => "nectar-one-half nectar-one-half-last",
			"description" => '',
			"param_name" => "mobile_content_alignment",
			"value" => array(
				esc_html__("Inherit", 'salient-core') => "inherit",
				esc_html__("Left", 'salient-core') => "left",
				esc_html__("Center", 'salient-core') => "center",
				esc_html__("Right", 'salient-core') => 'right'
			)
		),

		array(
			"type" => "nectar_radio_tab_selection",
			"class" => "",
			'save_always' => true,
			"heading" => esc_html__("Text Direction", "salient-core"),
			"param_name" => "text_direction",
			"options" => array(
				esc_html__("Auto", "salient-core") => "default",
				esc_html__("Left", "salient-core") => "ltr",
				esc_html__("Right", "salient-core") => "rtl",
			),
		),

		array(
			"type" => "textfield",
			"heading" => esc_html__("Extra Class Name", "salient-core"),
			"param_name" => "el_class",
			"description" => ''
		),

		array(
			"type" => "dropdown",
			"heading" => esc_html__("Functionality (Deprecated)", "salient-core"),
			"param_name" => "animation_type",
			"value" => array(
				esc_html__("Line reveal by available space", "salient-core") => "line-reveal-by-space",
				esc_html__("Line reveal by each new line", "salient-core") => "default",
				esc_html__("Twist in entire element", "salient-core") => "twist-in"
			),
			'save_always' => true,
			"description" => ''
		),

		array(
			"type" => "textfield",
			'group' => esc_html__('Link', 'salient-core'),
			"heading" => esc_html__("Link URL", "salient-core"),
			"param_name" => "link_href",
			'dependency' => array(
				'element' => 'animation_type',
				'value' => array('line-reveal-by-space', 'letter-fade-reveal', 'twist-in'),
			),
			"description" => esc_html__("The URL that will be used for the link", "salient-core")
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => esc_html__("Link Functionality", "salient-core"),
			"param_name" => "link_target",
			'save_always' => true,
			'group' => esc_html__('Link', 'salient-core'),
			'dependency' => array(
				'element' => 'animation_type',
				'value' => array('line-reveal-by-space', 'letter-fade-reveal', 'twist-in'),
			),
			'value' => array(
				esc_html__("Open in same window", "salient-core") => "_self",
				esc_html__("Open in new window", "salient-core") => "_blank"
			)
		),
		array(
			"type" => "checkbox",
			"class" => "",
			'edit_field_class' => 'vc_col-xs-12 salient-fancy-checkbox',
			"heading" => esc_html__("Link Mouse Indicator", "salient-core"),
			"param_name" => "link_indicator",
			'group' => esc_html__('Link', 'salient-core'),
			'dependency' => array(
				'element' => 'animation_type',
				'value' => array('line-reveal-by-space', 'letter-fade-reveal', 'twist-in'),
			),
			"value" => array(esc_html__("Yes", "salient-core") => 'true'),
		),
		array(
			"type" => "colorpicker",
			"class" => "",
			"heading" => "Touch Indicator BG Color",
			"param_name" => "link_indicator_bg_color",
			"value" => "",
			"dependency" => array('element' => "link_indicator", 'not_empty' => true),
			'group' => esc_html__('Link', 'salient-core'),
			"description" => esc_html__("The color of the background of your touch indicator button.", "salient-core")
		),
		array(
			"type" => "colorpicker",
			"class" => "",
			"heading" => "Touch Indicator Icon Color",
			"param_name" => "link_indicator_icon_color",
			"value" => "",
			'group' => esc_html__('Link', 'salient-core'),
			"dependency" => array('element' => "link_indicator", 'not_empty' => true),
			"description" => esc_html__("The color of your touch indicator button icon.", "salient-core")
		)

	)
);




return array(
	"name" => esc_html__("Animated Text", "salient-core"),
	"base" => "split_line_heading",
	"icon" => "icon-wpb-split-line-heading",
	"allowed_container_element" => 'vc_row',
	"weight" => '2',
	"category" => esc_html__('Typography', 'salient-core'),
	"description" => esc_html__('Formerly "Split Line Heading"', 'salient-core'),
	"params" => $split_line_heading_params
);