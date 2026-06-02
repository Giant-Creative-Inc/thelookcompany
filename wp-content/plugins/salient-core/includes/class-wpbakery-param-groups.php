<?php

/**
 * Reusable WPBakery parameter groups
 *
 * @version 1.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('SalientWPbakeryParamGroups')) {
    class SalientWPbakeryParamGroups
    {
        // Flexbox option variables
        private static $flexbox_justify_content_options;
        private static $flexbox_align_items_options;
        private static $flexbox_wrap_options;
        private static function flex_icon_img($vertical, $horizontal, $alt = '') {
            return '<img src="' . esc_attr($horizontal) . '" ' .
                   'data-horizontal-src="' . esc_attr($horizontal) . '" ' .
                   'data-vertical-src="' . esc_attr($vertical) . '" ' .
                   'alt="' . esc_attr($alt) . '" />';
        }
        private static function init_flexbox_options() {
            if (!isset(self::$flexbox_justify_content_options)) {
                self::$flexbox_justify_content_options = array(
                    self::flex_icon_img(
                        SALIENT_CORE_PLUGIN_PATH . '/includes/icons/ui/align-start-horizontal.svg',
                        SALIENT_CORE_PLUGIN_PATH . '/includes/icons/ui/align-start-vertical.svg',
                        'Start'
                    ) . '<span class="n_radio_tab_icon_text">' . esc_html__("Start", "salient-core") . '</span>' => "flex-start",
                    self::flex_icon_img(
                        SALIENT_CORE_PLUGIN_PATH . '/includes/icons/ui/align-center-horizontal.svg',
                        SALIENT_CORE_PLUGIN_PATH . '/includes/icons/ui/align-center-vertical.svg',
                        'Center'
                    ) . '<span class="n_radio_tab_icon_text">' . esc_html__("Center", "salient-core") . '</span>' => "center",
                    self::flex_icon_img(
                        SALIENT_CORE_PLUGIN_PATH . '/includes/icons/ui/align-end-horizontal.svg',
                        SALIENT_CORE_PLUGIN_PATH . '/includes/icons/ui/align-end-vertical.svg',
                        'End'
                    ) . '<span class="n_radio_tab_icon_text">' . esc_html__("End", "salient-core") . '</span>' => "flex-end",
                    self::flex_icon_img(
                        SALIENT_CORE_PLUGIN_PATH . '/includes/icons/ui/align-vertical-space-between.svg',
                        SALIENT_CORE_PLUGIN_PATH . '/includes/icons/ui/align-horizontal-space-between.svg',
                        'Space Between'
                    ) . '<span class="n_radio_tab_icon_text">' . esc_html__("Space Between", "salient-core") . '</span>' => "space-between",
                );
            }

            if (!isset(self::$flexbox_align_items_options)) {
                self::$flexbox_align_items_options = array(
                    self::flex_icon_img(
                        SALIENT_CORE_PLUGIN_PATH . '/includes/icons/ui/align-start-vertical.svg',
                        SALIENT_CORE_PLUGIN_PATH . '/includes/icons/ui/align-start-horizontal.svg',
                        'Start'
                    ) . '<span class="n_radio_tab_icon_text">' . esc_html__("Start", "salient-core") . '</span>' => "flex-start",
                    self::flex_icon_img(
                        SALIENT_CORE_PLUGIN_PATH . '/includes/icons/ui/align-center-vertical.svg',
                        SALIENT_CORE_PLUGIN_PATH . '/includes/icons/ui/align-center-horizontal.svg',
                        'Center'
                    ) . '<span class="n_radio_tab_icon_text">' . esc_html__("Center", "salient-core") . '</span>' => "center",
                    self::flex_icon_img(
                        SALIENT_CORE_PLUGIN_PATH . '/includes/icons/ui/align-end-vertical.svg',
                        SALIENT_CORE_PLUGIN_PATH . '/includes/icons/ui/align-end-horizontal.svg',
                        'End'
                    ) . '<span class="n_radio_tab_icon_text">' . esc_html__("End", "salient-core") . '</span>' => "flex-end",
                    self::flex_icon_img(
                        SALIENT_CORE_PLUGIN_PATH . '/includes/icons/ui/stretch-vertical.svg',
                        SALIENT_CORE_PLUGIN_PATH . '/includes/icons/ui/stretch-horizontal.svg',
                        'Stretch'
                    ) . '<span class="n_radio_tab_icon_text">' . esc_html__("Stretch", "salient-core") . '</span>' => "stretch",
                );
            }

            if (!isset(self::$flexbox_wrap_options)) {
                self::$flexbox_wrap_options = array(
                    esc_html__("No Wrap", "salient-core") => "nowrap",
                    esc_html__("Wrap", "salient-core") => "wrap",
                );
            }
        }

        static $instance = false;

        public static function getInstance()
        {
            if (!self::$instance) {
                self::$instance = new self;
            }
            return self::$instance;
        }

        public static function size_group($group_name = '', $default_width = '', $default_height = '') {

            $size_arr = array(
                // Desktop Width
                array(
                    "type"              => "nectar_numerical",
                    "class"             => "",
                    "group"             => $group_name,
                    "edit_field_class"  => "col-md-6 nectar-one-half-first desktop size-device-group",
                    "heading"           => '<span class="group-title">' . esc_html__("Size", "salient-core") . "</span>",
                    "value"             => $default_width,
                    "placeholder"       => esc_html__("Width", "salient-core"),
                    "param_name"        => "width_desktop",
                    "description"       => ""
                ),
                  // Desktop Height
                  array(
                    "type"              => "nectar_numerical",
                    "class"             => "",
                    "group"             => $group_name,
                    "edit_field_class"  => "nectar-one-half nectar-one-half-last desktop size-device-group",
                    "heading"           => "<span class='attr-title'>" . esc_html__("Height", "salient-core") . "</span>",
                    "value"             => $default_height,
                    "placeholder"       => esc_html__("Height", "salient-core"),
                    "param_name"        => "height_desktop",
                    "description"       => ""
                ),

                // Tablet Width
                array(
                    "type"              => "nectar_numerical",
                    "class"             => "",
                    "group"             => $group_name,
                    "edit_field_class"  => "nectar-one-half nectar-one-half-first tablet size-device-group",
                    "heading"           => "<span class='attr-title'>" . esc_html__("Width", "salient-core") . "</span>",
                    "value"             => "",
                    "placeholder"       => esc_html__("Width", "salient-core"),
                    "param_name"        => "width_tablet",
                    "description"       => ""
                ),
                 // Tablet Height
                 array(
                    "type"              => "nectar_numerical",
                    "class"             => "",
                    "group"             => $group_name,
                    "edit_field_class"  => "nectar-one-half nectar-one-half-last tablet size-device-group",
                    "heading"           => "<span class='attr-title'>" . esc_html__("Height", "salient-core") . "</span>",
                    "value"             => "",
                    "placeholder"       => esc_html__("Height", "salient-core"),
                    "param_name"        => "height_tablet",
                    "description"       => ""
                ),

                // Mobile (Phone) Width
                array(
                    "type"              => "nectar_numerical",
                    "class"             => "",
                    "group"             => $group_name,
                    "edit_field_class"  => "nectar-one-half nectar-one-half-first phone size-device-group",
                    "heading"           => "<span class='attr-title'>" . esc_html__("Width", "salient-core") . "</span>",
                    "value"             => "",
                    "placeholder"       => esc_html__("Width", "salient-core"),
                    "param_name"        => "width_phone",
                    "description"       => ""
                ),
                 // Mobile (Phone) Height
                 array(
                    "type"              => "nectar_numerical",
                    "class"             => "",
                    "group"             => $group_name,
                    "edit_field_class"  => "nectar-one-half nectar-one-half-last phone size-device-group",
                    "heading"           => "<span class='attr-title'>" . esc_html__("Height", "salient-core") . "</span>",
                    "value"             => "",
                    "placeholder"       => esc_html__("Height", "salient-core"),
                    "param_name"        => "height_phone",
                    "description"       => ""
                )

            );

            return $size_arr;
        }


        public static function height_group($group_name = '', $dependency = []) {

            $size_arr = array(
                 // Desktop Height
                 array(
                    "type"              => "nectar_numerical",
                    "class"             => "",
                    "group"             => $group_name,
                    "edit_field_class"  => "col-md-6 nectar-one-half-first desktop height-device-group",
                    "heading"           => '<span class="group-title">' . esc_html__("Height", "salient-core") . "</span>",
                    "placeholder"       => esc_html__("Height", "salient-core"),
                    "param_name"        => "height_desktop",
                    "dependency"        => $dependency,
                    "description"       => ""
                ),
                array(
                    "type"              => "nectar_numerical",
                    "class"             => "",
                    "group"             => $group_name,
                    "edit_field_class"  => "col-md-6 nectar-one-half-last desktop height-device-group",
                    "heading"           => '',
                    "placeholder"       => esc_html__("Min Height", "salient-core"),
                    "param_name"        => "min_height_desktop",
                    "dependency"        => $dependency,
                    "description"       => ""
                ),


                 // Tablet Height
                 array(
                    "type"              => "nectar_numerical",
                    "class"             => "",
                    "group"             => $group_name,
                    "edit_field_class"  => "nectar-one-half nectar-one-half-first tablet height-device-group",
                    "heading"           => "<span class='attr-title'>" . esc_html__("Height", "salient-core") . "</span>",
                    "placeholder"       => esc_html__("Height", "salient-core"),
                    "param_name"        => "height_tablet",
                    "dependency"        => $dependency,
                    "description"       => ""
                ),
                array(
                    "type"              => "nectar_numerical",
                    "class"             => "",
                    "group"             => $group_name,
                    "edit_field_class"  => "nectar-one-half nectar-one-half-last tablet height-device-group",
                    "heading"           => "<span class='attr-title'>" . esc_html__("Min Height", "salient-core") . "</span>",
                    "placeholder"       => esc_html__("Min Height", "salient-core"),
                    "param_name"        => "min_height_tablet",
                    "dependency"        => $dependency,
                    "description"       => ""
                ),


                 // Mobile (Phone) Height
                 array(
                    "type"              => "nectar_numerical",
                    "class"             => "",
                    "group"             => $group_name,
                    "edit_field_class"  => "nectar-one-half nectar-one-half-first phone height-device-group",
                    "heading"           => "<span class='attr-title'>" . esc_html__("Height", "salient-core") . "</span>",
                    "placeholder"       => esc_html__("Height", "salient-core"),
                    "param_name"        => "height_phone",
                    "dependency"        => $dependency,
                    "description"       => ""
                 ),
                array(
                    "type"              => "nectar_numerical",
                    "class"             => "",
                    "group"             => $group_name,
                    "edit_field_class"  => "nectar-one-half nectar-one-half-last phone height-device-group",
                    "heading"           => "<span class='attr-title'>" . esc_html__("Min Height", "salient-core") . "</span>",
                    "placeholder"       => esc_html__("Min Height", "salient-core"),
                    "param_name"        => "min_height_phone",
                    "dependency"        => $dependency,
                    "description"       => ""
                ),


            );

            return $size_arr;
        }



        public static function backdrop_filter_group($group_name = '', $dependency = []) {

            $backdrop_filter_arr = array(

                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'group' => $group_name,
                    'heading' => esc_html__('Backdrop Filter', 'salient-core'),
                    'param_name' => 'backdrop_filter',
                    'description' => esc_html__('Add a backdrop filter to the element.', 'salient-core'),
                    'value' => array(
                        esc_html__('None', 'salient-core') => 'none',
                        esc_html__('Blur', 'salient-core') => 'blur',
                        esc_html__('Brightness', 'salient-core') => 'brightness',
                        esc_html__('Contrast', 'salient-core') => 'contrast',
                        esc_html__('Grayscale', 'salient-core') => 'grayscale',
                        esc_html__('Hue Rotate', 'salient-core') => 'hue-rotate',
                        esc_html__('Invert', 'salient-core') => 'invert',
                        esc_html__('Saturate', 'salient-core') => 'saturate',
                        esc_html__('Sepia', 'salient-core') => 'sepia',
                    )
                ),

                // Blur control
                array(
                    'type' => 'nectar_range_slider',
                    'heading' => esc_html__('Blur Amount', 'salient-core'),
                    'param_name' => 'backdrop_filter_blur',
                    'dependency' => array('element' => 'backdrop_filter', 'value' => array('blur')),
                    'group' => $group_name,
                    'value' => '0',
                    'options' => array(
                        'min' => '0',
                        'max' => '50',
                        'step' => '1',
                        'suffix' => 'px'
                    ),
                ),

                // Brightness control
                array(
                    'type' => 'nectar_range_slider',
                    'heading' => esc_html__('Brightness Amount', 'salient-core'),
                    'param_name' => 'backdrop_filter_brightness',
                    'dependency' => array('element' => 'backdrop_filter', 'value' => array('brightness')),
                    'group' => $group_name,
                    'value' => '1',
                    'options' => array(
                        'min' => '0',
                        'max' => '2',
                        'step' => '0.05',
                        'suffix' => ''
                    ),
                ),

                // Contrast control
                array(
                    'type' => 'nectar_range_slider',
                    'heading' => esc_html__('Contrast Amount', 'salient-core'),
                    'param_name' => 'backdrop_filter_contrast',
                    'dependency' => array('element' => 'backdrop_filter', 'value' => array('contrast')),
                    'group' => $group_name,
                    'value' => '1',
                    'options' => array(
                        'min' => '0',
                        'max' => '2',
                        'step' => '0.1',
                        'suffix' => ''
                    ),
                ),

                // Grayscale control
                array(
                    'type' => 'nectar_range_slider',
                    'heading' => esc_html__('Grayscale Amount', 'salient-core'),
                    'param_name' => 'backdrop_filter_grayscale',
                    'dependency' => array('element' => 'backdrop_filter', 'value' => array('grayscale')),
                    'group' => $group_name,
                    'value' => '0',
                    'options' => array(
                        'min' => '0',
                        'max' => '1',
                        'step' => '0.05',
                        'suffix' => ''
                    ),
                ),

                // Hue Rotate control
                array(
                    'type' => 'nectar_range_slider',
                    'heading' => esc_html__('Hue Rotate Amount', 'salient-core'),
                    'param_name' => 'backdrop_filter_hue_rotate',
                    'dependency' => array('element' => 'backdrop_filter', 'value' => array('hue-rotate')),
                    'group' => $group_name,
                    'value' => '0',
                    'options' => array(
                        'min' => '0',
                        'max' => '360',
                        'step' => '1',
                        'suffix' => 'deg'
                    ),
                ),

                // Invert control
                array(
                    'type' => 'nectar_range_slider',
                    'heading' => esc_html__('Invert Amount', 'salient-core'),
                    'param_name' => 'backdrop_filter_invert',
                    'dependency' => array('element' => 'backdrop_filter', 'value' => array('invert')),
                    'group' => $group_name,
                    'value' => '0',
                    'options' => array(
                        'min' => '0',
                        'max' => '1',
                        'step' => '0.05',
                        'suffix' => ''
                    ),
                ),

                // Saturate control
                array(
                    'type' => 'nectar_range_slider',
                    'heading' => esc_html__('Saturate Amount', 'salient-core'),
                    'param_name' => 'backdrop_filter_saturate',
                    'dependency' => array('element' => 'backdrop_filter', 'value' => array('saturate')),
                    'group' => $group_name,
                    'value' => '1',
                    'options' => array(
                        'min' => '0',
                        'max' => '2',
                        'step' => '0.1',
                        'suffix' => ''
                    ),
                ),

                // Sepia control
                array(
                    'type' => 'nectar_range_slider',
                    'heading' => esc_html__('Sepia Amount', 'salient-core'),
                    'param_name' => 'backdrop_filter_sepia',
                    'dependency' => array('element' => 'backdrop_filter', 'value' => array('sepia')),
                    'group' => $group_name,
                    'value' => '0',
                    'options' => array(
                        'min' => '0',
                        'max' => '1',
                        'step' => '1',
                        'suffix' => ''
                    ),
                ),

            );

            return $backdrop_filter_arr;
        }

        public static function background_group($group_name = '', $dependency = []) {

            $background_arr = array(
                array(
					"type" => "colorpicker",
					"class" => "",
					'group' => $group_name,
					"heading" => esc_html__("Background Color", "salient-core" ),
					"param_name" => "background_color",
					"value" => "",
					"description" => "",
				),

				array(
					"type" => "fws_image",
					'group' => $group_name,
					"class" => "",
					"edit_field_class" => "desktop column-bg-img-device-group",
					"heading" => '<span class="group-title">' . esc_html__("Background Image", "salient-core") . "</span>",
					"param_name" => "background_image",
					"value" => "",
					"description" => ""
				),

				array(
					"type" => "fws_image",
					'group' => $group_name,
					"class" => "",
					"edit_field_class" => "tablet column-bg-img-device-group",
					"heading" => '',
					"param_name" => "background_image_tablet",
					"value" => "",
					"description" => ""
				),

				array(
					"type" => "fws_image",
					'group' => $group_name,
					"class" => "",
					"edit_field_class" => "phone column-bg-img-device-group",
					"heading" => '',
					"param_name" => "background_image_phone",
					"value" => "",
					"description" => ""
				),

                array(
					"type" => "checkbox",
					"class" => "",
					'edit_field_class' => 'vc_col-xs-12 salient-fancy-checkbox',
					"group" => $group_name,
					"heading" => esc_html__("Preload Image", "salient-core" ),
					"param_name" => "background_image_preload",
					"value" => array("Preload" => "true" ),
					"description" => "Enable to preload the background image for improved performance. Recommended for prominent, above-the-fold images.",
					"dependency" => Array('element' => "background_image", 'not_empty' => true)
				),

				array(
					"type" => "dropdown",
					"class" => "",
					"group" => $group_name,
					'save_always' => true,
					"heading" => esc_html__("Background Image Type", "salient-core" ),
					"param_name" => "background_image_type",
					"description" => esc_html__("Choose how the background image is rendered. Using an image tag (`<img>`) is recommended for above-the-fold content as it improves performance and loading behavior. CSS background images are more flexible for design but load differently.", "salient-core" ),
					"value" => array(
						esc_html__("CSS Background Image", "salient-core" ) => "default",
			  		 	esc_html__("Image Tag", "salient-core" ) => "img",
					),
					"dependency" => Array('element' => "background_image", 'not_empty' => true)
				),

				array(
					"type" => "dropdown",
					"class" => "",
					"group" => $group_name,
					'save_always' => true,
					"heading" => esc_html__("Background Image Position", "salient-core" ),
					"param_name" => "background_image_position",
					"value" => array(
						esc_html__("Center Center", "salient-core" ) => "center center",
						esc_html__("Center Top", "salient-core" ) => "center top",
						esc_html__("Center Bottom", "salient-core" ) => "center bottom",
						esc_html__("Left Top", "salient-core" ) => "left top",
						esc_html__("Left Center", "salient-core" ) => "left center",
						esc_html__("Left Bottom", "salient-core" ) => "left bottom",
						esc_html__("Right Top", "salient-core" ) => "right top",
						esc_html__("Right Center", "salient-core" ) => "right center",
						esc_html__("Right Bottom", "salient-core" ) => "right bottom"
					),
					"dependency" => Array('element' => "background_image", 'not_empty' => true)
				),



				array(
                    "type" => "dropdown",
                    "class" => "",
                    'save_always' => true,
                    "heading" => esc_html__("Background Image Loading", "salient-core"),
                    "dependency" => Array('element' => "background_image", 'not_empty' => true),
                    "param_name" => "background_image_loading",
                    'group' => $group_name,
                    "value" => array(
                    "Default" => "default",
                            "Lazy Load" => "lazy-load",
                            "Skip Lazy Load" => "skip-lazy-load",
                    ),
                     "description" => esc_html__("Determine whether to load the image on page load or to use a lazy load method for higher performance.", "salient-core"),
                    'std' => 'default',
                ),

        		array(
					"type" => "checkbox",
					"class" => "",
					'group' => $group_name,
					"heading" => esc_html__("Video Background", "salient-core"),
					"value" => array("Enable Video Background?" => "use_video" ),
					"param_name" => "video_bg",
					'edit_field_class' => 'vc_col-xs-12 salient-fancy-checkbox',
					"description" => ""
				),

				array(
					"type" => "nectar_attach_video",
					"class" => "",
					'group' => $group_name,
					"heading" => esc_html__("MP4 File URL", "salient-core"),
					"value" => "",
					"param_name" => "video_mp4",
					"description" => esc_html__("You must include this format or the .webm format to render your video with cross browser compatibility. Video must be in a 16:9 aspect ratio.", "salient-core"),
					"dependency" => Array('element' => "video_bg", 'value' => array('use_video'))
				),

				array(
					"type" => "nectar_attach_video",
					"class" => "",
					'group' => $group_name,
					"heading" => esc_html__("WebM File URL", "salient-core"),
					"value" => "",
					"param_name" => "video_webm",
					"description" => esc_html__("Enter the URL for your .webm video file here.", "salient-core"),
					"dependency" => Array('element' => "video_bg", 'value' => array('use_video'))
				),

				array(
					"type" => "dropdown",
					"class" => "",
					'save_always' => true,
					"dependency" => Array('element' => "video_bg", 'value' => array('use_video')),
					"heading" => esc_html__("Self Hosted Background Video Loading", "salient-core"),
					"param_name" => "background_video_loading",
					"value" => array(
					  	"Default" => "default",
						"Lazy Load" => "lazy-load",
					),
                    'group' => $group_name,
                    "description" => esc_html__("Determine whether to load the background video on page load or to use a lazy load method for higher performance.", "salient-core"),
					'std' => 'default',
				  )
            );

            return $background_arr;
        }

        public static function background_video_group($group_name = '', $dependency = [], $edit_field_class = '') {
            $background_arr = array(
        		array(
					"type" => "checkbox",
					"class" => "",
					'group' => $group_name,
					"heading" => esc_html__("Video Background", "salient-core"),
					"value" => array("Enable Video Background?" => "use_video" ),
					"param_name" => "video_bg",
					'edit_field_class' => 'vc_col-xs-12 salient-fancy-checkbox' . $edit_field_class,
					"description" => ""
				),

				array(
					"type" => "nectar_attach_video",
					"class" => "",
					'group' => $group_name,
					"heading" => esc_html__("MP4 File URL", "salient-core"),
					"value" => "",
					"param_name" => "video_mp4",
					"description" => esc_html__("You must include this format or the .webm format to render your video with cross browser compatibility. Video must be in a 16:9 aspect ratio.", "salient-core"),
					"dependency" => Array('element' => "video_bg", 'value' => array('use_video')),
                    'edit_field_class' => 'vc_col-xs-12' . $edit_field_class,
				),

				array(
					"type" => "nectar_attach_video",
					"class" => "",
					'group' => $group_name,
					"heading" => esc_html__("WebM File URL", "salient-core"),
					"value" => "",
					"param_name" => "video_webm",
					"description" => esc_html__("Enter the URL for your .webm video file here.", "salient-core"),
					"dependency" => Array('element' => "video_bg", 'value' => array('use_video')),
                    'edit_field_class' => 'vc_col-xs-12' . $edit_field_class,
				),

				array(
					"type" => "dropdown",
					"class" => "",
					'save_always' => true,
					"dependency" => Array('element' => "video_bg", 'value' => array('use_video')),
					"heading" => esc_html__("Background Video Loading", "salient-core"),
					"param_name" => "background_video_loading",
					"value" => array(
					  	"Default" => "default",
						"Lazy Load" => "lazy-load",
					),
                    'group' => $group_name,
                    "description" => esc_html__("Determine whether to load the background video on page load or to use a lazy load method for higher performance.", "salient-core"),
					'std' => 'default',
                    'edit_field_class' => 'vc_col-xs-12' . $edit_field_class,
				  )
            );

            return $background_arr;
        }


        public static function spacing_group($group_name = '', $dependency = []) {
            $spacing_arr = array(
                array(
                    "type" => "nectar_group_header",
                    "class" => "",
                    "group" => $group_name,
                    "heading" => esc_html__("Spacing & Transform", "salient-core" ),
                    "param_name" => "group_header_2",
                    "edit_field_class" => "",
                    "value" => ''
                ),
                array(
                    "type" => "nectar_numerical",
                    "class" => "",
                    "group" => $group_name,
                    "edit_field_class" => "col-md-6 desktop row-padding-device-group constrain_group_1",
                    "heading" => '<span class="group-title">' . esc_html__("Padding", "salient-core") . "</span><span class='attr-title'>" . esc_html__("Top", "salient-core") . "</span>",
                    "value" => "",
                    "placeholder" => esc_html__("Top",'salient-core'),
                    "param_name" => "top_padding",
                    "description" => ''
                ),
                array(
                    'type' => 'checkbox',
                    "group" => $group_name,
                    'heading' => esc_html__( 'Constrain 1', 'salient-core' ),
                    'param_name' => 'constrain_group_1',
                    'description' => '',
                    "edit_field_class" => "desktop row-padding-device-group constrain-icon left",
                    'value' => array( esc_html__( 'Yes', 'salient-core' ) => 'yes' ),
                ),
                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "placeholder" => esc_html__("Bottom",'salient-core'),
                    "edit_field_class" => "col-md-6 col-md-6-last desktop row-padding-device-group constrain_group_1",
                    "heading" => "<span class='attr-title'>" . esc_html__("Bottom", "salient-core") . "</span>",
                    "value" => "",
                    "param_name" => "bottom_padding",
                    "description" => ''
                ),


                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "placeholder" => esc_html__("Left",'salient-core'),
                    "edit_field_class" => "col-md-6 desktop col-md-6-last row-padding-device-group constrain_group_2",
                    "heading" => "<span class='attr-title'>" . esc_html__("Left", "salient-core") . "</span>",
                    "value" => "",
                    "param_name" => "left_padding_desktop",
                    "description" => ''
                ),
                array(
                    'type' => 'checkbox',
                    "group" => $group_name,
                    'heading' => esc_html__( 'Constrain 2', 'salient-core' ),
                    'param_name' => 'constrain_group_2',
                    "edit_field_class" => "desktop row-padding-device-group constrain-icon right",
                    'description' => '',
                    'value' => array( esc_html__( 'Yes', 'salient-core' ) => 'yes' ),
                ),
                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "placeholder" => esc_html__("Right",'salient-core'),
                    "edit_field_class" => "col-md-6 col-md-6-last desktop row-padding-device-group constrain_group_2",
                    "heading" => "<span class='attr-title'>" . esc_html__("Right", "salient-core") . "</span>",
                    "value" => "",
                    "param_name" => "right_padding_desktop",
                    "description" => ''
                ),



                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "placeholder" => esc_html__("Top",'salient-core'),
                    "edit_field_class" => "col-md-6 tablet row-padding-device-group constrain_group_3",
                    "heading" => "<span class='attr-title'>" . esc_html__("Top", "salient-core") . "</span>",
                    "value" => "",
                    "param_name" => "top_padding_tablet",
                    "description" => ''
                ),
                array(
                    'type' => 'checkbox',
                    "group" => $group_name,
                    'heading' => esc_html__( 'Constrain 3', 'salient-core' ),
                    'param_name' => 'constrain_group_3',
                    "edit_field_class" => "tablet row-padding-device-group constrain-icon left",
                    'description' => '',
                    'value' => array( esc_html__( 'Yes', 'salient-core' ) => 'yes' ),
                ),
                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "placeholder" => esc_html__("Bottom",'salient-core'),
                    "edit_field_class" => "col-md-6 col-md-6-last tablet row-padding-device-group constrain_group_3",
                    "heading" => "<span class='attr-title'>" . esc_html__("Bottom", "salient-core") . "</span>",
                    "value" => "",
                    "param_name" => "bottom_padding_tablet",
                    "description" => ''
                ),


                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "placeholder" => esc_html__("Left",'salient-core'),
                    "edit_field_class" => "col-md-6 tablet col-md-6-last row-padding-device-group constrain_group_4",
                    "heading" => "<span class='attr-title'>" . esc_html__("Left", "salient-core") . "</span>",
                    "value" => "",
                    "param_name" => "left_padding_tablet",
                    "description" => ''
                ),
                array(
                    'type' => 'checkbox',
                    "group" => $group_name,
                    'heading' => esc_html__( 'Constrain 4', 'salient-core' ),
                    'param_name' => 'constrain_group_4',
                    "edit_field_class" => "tablet row-padding-device-group constrain-icon right",
                    'description' => '',
                    'value' => array( esc_html__( 'Yes', 'salient-core' ) => 'yes' ),
                ),
                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "placeholder" => esc_html__("Right",'salient-core'),
                    "edit_field_class" => "col-md-6 col-md-6-last tablet row-padding-device-group constrain_group_4",
                    "heading" => "<span class='attr-title'>" . esc_html__("Right", "salient-core") . "</span>",
                    "value" => "",
                    "param_name" => "right_padding_tablet",
                    "description" => ''
                ),


                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "placeholder" => esc_html__("Top",'salient-core'),
                    "edit_field_class" => "col-md-6 phone row-padding-device-group constrain_group_5",
                    "heading" => "<span class='attr-title'>" . esc_html__("Top", "salient-core") . "</span>",
                    "value" => "",
                    "param_name" => "top_padding_phone",
                    "description" => ''
                ),
                array(
                    'type' => 'checkbox',
                    "group" => $group_name,
                    "edit_field_class" => "phone row-padding-device-group constrain-icon left",
                    'heading' => esc_html__( 'Constrain 5', 'salient-core' ),
                    'param_name' => 'constrain_group_5',
                    'description' => '',
                    'value' => array( esc_html__( 'Yes', 'salient-core' ) => 'yes' ),
                ),
                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "placeholder" => esc_html__("Bottom",'salient-core'),
                    "edit_field_class" => "col-md-6 col-md-6-last phone row-padding-device-group constrain_group_5",
                    "heading" => "<span class='attr-title'>" . esc_html__("Bottom", "salient-core") . "</span>",
                    "value" => "",
                    "param_name" => "bottom_padding_phone",
                    "description" => ''
                ),




                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "placeholder" => esc_html__("Left",'salient-core'),
                    "edit_field_class" => "col-md-6 phone col-md-6-last row-padding-device-group constrain_group_6",
                    "heading" => "<span class='attr-title'>" . esc_html__("Left", "salient-core") . "</span>",
                    "value" => "",
                    "param_name" => "left_padding_phone",
                    "description" => ''
                ),
                array(
                    'type' => 'checkbox',
                    "group" => $group_name,
                    'heading' => esc_html__( 'Constrain 6', 'salient-core' ),
                    'param_name' => 'constrain_group_6',
                    "edit_field_class" => "phone row-padding-device-group constrain-icon right",
                    'description' => '',
                    'value' => array( esc_html__( 'Yes', 'salient-core' ) => 'yes' ),
                ),
                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "placeholder" => esc_html__("Right",'salient-core'),
                    "edit_field_class" => "col-md-6 col-md-6-last phone row-padding-device-group constrain_group_6",
                    "heading" => "<span class='attr-title'>" . esc_html__("Right", "salient-core") . "</span>",
                    "value" => "",
                    "param_name" => "right_padding_phone",
                    "description" => ''
                ),


                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "edit_field_class" => "col-md-6 desktop row-margin-device-group constrain_group_7",
                    "heading" => '<span class="group-title">' . esc_html__("Margin", "salient-core") . "</span><span class='attr-title'>" . esc_html__("Top", "salient-core") . "</span>",
                    "value" => "",
                    "placeholder" => esc_html__("Top",'salient-core'),
                    "param_name" => "top_margin",
                    "description" => ''
                ),
                array(
                    'type' => 'checkbox',
                    "group" => $group_name,
                    'heading' => esc_html__( 'Constrain 7', 'salient-core' ),
                    'param_name' => 'constrain_group_7',
                    'description' => '',
                    "edit_field_class" => "desktop row-margin-device-group constrain-icon left",
                    'value' => array( esc_html__( 'Yes', 'salient-core' ) => 'yes' ),
                ),
                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "placeholder" => esc_html__("Bottom",'salient-core'),
                    "edit_field_class" => "col-md-6 col-md-6-last desktop row-margin-device-group constrain_group_7",
                    "heading" => "<span class='attr-title'>" . esc_html__("Bottom", "salient-core") . "</span>",
                    "value" => "",
                    "param_name" => "bottom_margin",
                    "description" => ''
                ),


                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "placeholder" => esc_html__("Left",'salient-core'),
                    "edit_field_class" => "col-md-6 desktop col-md-6-last row-margin-device-group constrain_group_8",
                    "heading" => "<span class='attr-title'>" . esc_html__("Left", "salient-core") . "</span>",
                    "value" => "",
                    "param_name" => "left_margin",
                    "description" => ''
                ),
                array(
                    'type' => 'checkbox',
                    "group" => $group_name,
                    'heading' => esc_html__( 'Constrain 8', 'salient-core' ),
                    'param_name' => 'constrain_group_8',
                    "edit_field_class" => "desktop row-margin-device-group constrain-icon right",
                    'description' => '',
                    'value' => array( esc_html__( 'Yes', 'salient-core' ) => 'yes' ),
                ),
                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "placeholder" => esc_html__("Right",'salient-core'),
                    "edit_field_class" => "col-md-6 col-md-6-last desktop row-margin-device-group constrain_group_8",
                    "heading" => "<span class='attr-title'>" . esc_html__("Right", "salient-core") . "</span>",
                    "value" => "",
                    "param_name" => "right_margin",
                    "description" => ''
                ),



                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "placeholder" => esc_html__("Top",'salient-core'),
                    "edit_field_class" => "col-md-6 tablet row-margin-device-group constrain_group_9",
                    "heading" => "<span class='attr-title'>" . esc_html__("Top", "salient-core") . "</span>",
                    "value" => "",
                    "param_name" => "top_margin_tablet",
                    "description" => ''
                ),
                array(
                    'type' => 'checkbox',
                    "group" => $group_name,
                    'heading' => esc_html__( 'Constrain 9', 'salient-core' ),
                    'param_name' => 'constrain_group_9',
                    "edit_field_class" => "tablet row-margin-device-group constrain-icon left",
                    'description' => '',
                    'value' => array( esc_html__( 'Yes', 'salient-core' ) => 'yes' ),
                ),
                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "placeholder" => esc_html__("Bottom",'salient-core'),
                    "edit_field_class" => "col-md-6 col-md-6-last tablet row-margin-device-group constrain_group_9",
                    "heading" => "<span class='attr-title'>" . esc_html__("Bottom", "salient-core") . "</span>",
                    "value" => "",
                    "param_name" => "bottom_margin_tablet",
                    "description" => ''
                ),


                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "placeholder" => esc_html__("Left",'salient-core'),
                    "edit_field_class" => "col-md-6 tablet col-md-6-last row-margin-device-group constrain_group_10",
                    "heading" => "<span class='attr-title'>" . esc_html__("Left", "salient-core") . "</span>",
                    "value" => "",
                    "param_name" => "left_margin_tablet",
                    "description" => ''
                ),
                array(
                    'type' => 'checkbox',
                    "group" => $group_name,
                    'heading' => esc_html__( 'Constrain 10', 'salient-core' ),
                    'param_name' => 'constrain_group_10',
                    "edit_field_class" => "tablet row-margin-device-group constrain-icon right",
                    'description' => '',
                    'value' => array( esc_html__( 'Yes', 'salient-core' ) => 'yes' ),
                ),
                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "placeholder" => esc_html__("Right",'salient-core'),
                    "edit_field_class" => "col-md-6 col-md-6-last tablet row-margin-device-group constrain_group_10",
                    "heading" => "<span class='attr-title'>" . esc_html__("Right", "salient-core") . "</span>",
                    "value" => "",
                    "param_name" => "right_margin_tablet",
                    "description" => ''
                ),


                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "placeholder" => esc_html__("Top",'salient-core'),
                    "edit_field_class" => "col-md-6 phone row-margin-device-group constrain_group_11",
                    "heading" => "<span class='attr-title'>" . esc_html__("Top", "salient-core") . "</span>",
                    "value" => "",
                    "param_name" => "top_margin_phone",
                    "description" => ''
                ),
                array(
                    'type' => 'checkbox',
                    "group" => $group_name,
                    "edit_field_class" => "phone row-margin-device-group constrain-icon left",
                    'heading' => esc_html__( 'Constrain 11', 'salient-core' ),
                    'param_name' => 'constrain_group_11',
                    'description' => '',
                    'value' => array( esc_html__( 'Yes', 'salient-core' ) => 'yes' ),
                ),
                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "placeholder" => esc_html__("Bottom",'salient-core'),
                    "edit_field_class" => "col-md-6 col-md-6-last phone row-margin-device-group constrain_group_11",
                    "heading" => "<span class='attr-title'>" . esc_html__("Bottom", "salient-core") . "</span>",
                    "value" => "",
                    "param_name" => "bottom_margin_phone",
                    "description" => ''
                ),



                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "placeholder" => esc_html__("Left",'salient-core'),
                    "edit_field_class" => "col-md-6 phone col-md-6-last row-margin-device-group constrain_group_12",
                    "heading" => "<span class='attr-title'>" . esc_html__("Left", "salient-core") . "</span>",
                    "value" => "",
                    "param_name" => "left_margin_phone",
                    "description" => ''
                ),
                array(
                    'type' => 'checkbox',
                    "group" => $group_name,
                    'heading' => esc_html__( 'Constrain 12', 'salient-core' ),
                    'param_name' => 'constrain_group_12',
                    "edit_field_class" => "phone row-margin-device-group constrain-icon right",
                    'description' => '',
                    'value' => array( esc_html__( 'Yes', 'salient-core' ) => 'yes' ),
                ),
                array(
                    "type" => "nectar_numerical",
                    "group" => $group_name,
                    "class" => "",
                    "placeholder" => esc_html__("Right",'salient-core'),
                    "edit_field_class" => "col-md-6 col-md-6-last phone row-margin-device-group constrain_group_12",
                    "heading" => "<span class='attr-title'>" . esc_html__("Right", "salient-core") . "</span>",
                    "value" => "",
                    "param_name" => "right_margin_phone",
                    "description" => ''
                ),

            );

            return $spacing_arr;
        }

        public static function position_group($group_name, $zindex = true)
        {

            $position_arr = array(
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'group' => $group_name,
                    'edit_field_class' => 'desktop position-display-device-group',
                    'heading' => '<span class="group-title">' . esc_html__('Position', 'salient-core') . '</span>',
                    'param_name' => 'position_desktop',
                    'value' => array(
                        esc_html__('Default', 'salient-core') => 'default',
                        esc_html__('Relative', 'salient-core') => 'relative',
                        esc_html__('Absolute', 'salient-core') => 'absolute'
                    )
                ),

                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'group' => $group_name,
                    'edit_field_class' => 'tablet position-display-device-group',
                    'heading' => '',
                    'param_name' => 'position_tablet',
                    'value' => array(
                        esc_html__('Inherit', 'salient-core') => 'inherit',
                        esc_html__('Relative', 'salient-core') => 'relative',
                        esc_html__('Absolute', 'salient-core') => 'absolute'
                    )
                ),

                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'group' => $group_name,
                    'edit_field_class' => 'phone position-display-device-group',
                    'heading' => '',
                    'param_name' => 'position_phone',
                    'value' => array(
                        esc_html__('Inherit', 'salient-core') => 'inherit',
                        esc_html__('Relative', 'salient-core') => 'relative',
                        esc_html__('Absolute', 'salient-core') => 'absolute'
                    )
                ),

                array(
                    'type' => 'nectar_numerical',
                    'class' => '',
                    'group' => $group_name,
                    'edit_field_class' => 'nectar-col-25 col-25-first desktop position-device-group',
                    'heading' => '<span class="group-title">' . esc_html__('Positioning', 'salient-core') . "</span><span class='attr-title'>" . esc_html__('Top', 'salient-core') . '</span>',
                    'value' => '',
                    'placeholder' => esc_html__('Top', 'salient-core'),
                    'param_name' => 'top_position_desktop',
                    'description' => ''
                ),
                array(
                    'type' => 'nectar_numerical',
                    'class' => '',
                    'group' => $group_name,
                    'placeholder' => esc_html__('Bottom', 'salient-core'),
                    'edit_field_class' => 'nectar-col-25 desktop position-device-group',
                    'heading' => "<span class='attr-title'>" . esc_html__('Bottom', 'salient-core') . '</span>',
                    'value' => '',
                    'param_name' => 'bottom_position_desktop',
                    'description' => ''
                ),
                array(
                    'type' => 'nectar_numerical',
                    'class' => '',
                    'group' => $group_name,
                    'placeholder' => esc_html__('Left', 'salient-core'),
                    'edit_field_class' => 'nectar-col-25 desktop position-device-group',
                    'heading' => "<span class='attr-title'>" . esc_html__('Left', 'salient-core') . '</span>',
                    'value' => '',
                    'param_name' => 'left_position_desktop',
                    'description' => ''
                ),
                array(
                    'type' => 'nectar_numerical',
                    'class' => '',
                    'group' => $group_name,
                    'placeholder' => esc_html__('Right', 'salient-core'),
                    'edit_field_class' => 'nectar-col-25 col-25-last desktop position-device-group',
                    'heading' => "<span class='attr-title'>" . esc_html__('Right', 'salient-core') . '</span>',
                    'value' => '',
                    'param_name' => 'right_position_desktop',
                    'description' => ''
                ),

                array(
                    'type' => 'nectar_numerical',
                    'class' => '',
                    'group' => $group_name,
                    'placeholder' => esc_html__('Top', 'salient-core'),
                    'edit_field_class' => 'nectar-col-25 col-25-first tablet position-device-group',
                    'heading' => "<span class='attr-title'>" . esc_html__('Top', 'salient-core') . '</span>',
                    'value' => '',
                    'param_name' => 'top_position_tablet',
                    'description' => ''
                ),
                array(
                    'type' => 'nectar_numerical',
                    'class' => '',
                    'group' => $group_name,
                    'placeholder' => esc_html__('Bottom', 'salient-core'),
                    'edit_field_class' => 'nectar-col-25 tablet position-device-group',
                    'heading' => "<span class='attr-title'>" . esc_html__('Bottom', 'salient-core') . '</span>',
                    'value' => '',
                    'param_name' => 'bottom_position_tablet',
                    'description' => ''
                ),
                array(
                    'type' => 'nectar_numerical',
                    'class' => '',
                    'group' => $group_name,
                    'placeholder' => esc_html__('Left', 'salient-core'),
                    'edit_field_class' => 'nectar-col-25 tablet position-device-group',
                    'heading' => "<span class='attr-title'>" . esc_html__('Left', 'salient-core') . '</span>',
                    'value' => '',
                    'param_name' => 'left_position_tablet',
                    'description' => ''
                ),
                array(
                    'type' => 'nectar_numerical',
                    'class' => '',
                    'group' => $group_name,
                    'placeholder' => esc_html__('Right', 'salient-core'),
                    'edit_field_class' => 'nectar-col-25 col-25-last tablet position-device-group',
                    'heading' => "<span class='attr-title'>" . esc_html__('Right', 'salient-core') . '</span>',
                    'value' => '',
                    'param_name' => 'right_position_tablet',
                    'description' => ''
                ),

                array(
                    'type' => 'nectar_numerical',
                    'class' => '',
                    'group' => $group_name,
                    'placeholder' => esc_html__('Top', 'salient-core'),
                    'edit_field_class' => 'nectar-col-25 col-25-first phone position-device-group',
                    'heading' => "<span class='attr-title'>" . esc_html__('Top', 'salient-core') . '</span>',
                    'value' => '',
                    'param_name' => 'top_position_phone',
                    'description' => ''
                ),
                array(
                    'type' => 'nectar_numerical',
                    'class' => '',
                    'group' => $group_name,
                    'placeholder' => esc_html__('Bottom', 'salient-core'),
                    'edit_field_class' => 'nectar-col-25 phone position-device-group',
                    'heading' => "<span class='attr-title'>" . esc_html__('Bottom', 'salient-core') . '</span>',
                    'value' => '',
                    'param_name' => 'bottom_position_phone',
                    'description' => ''
                ),
                array(
                    'type' => 'nectar_numerical',
                    'class' => '',
                    'group' => $group_name,
                    'placeholder' => esc_html__('Left', 'salient-core'),
                    'edit_field_class' => 'nectar-col-25 phone position-device-group',
                    'heading' => "<span class='attr-title'>" . esc_html__('Left', 'salient-core') . '</span>',
                    'value' => '',
                    'param_name' => 'left_position_phone',
                    'description' => ''
                ),
                array(
                    'type' => 'nectar_numerical',
                    'class' => '',
                    'group' => $group_name,
                    'placeholder' => esc_html__('Right', 'salient-core'),
                    'edit_field_class' => 'nectar-col-25 col-25-last phone position-device-group',
                    'heading' => "<span class='attr-title'>" . esc_html__('Right', 'salient-core') . '</span>',
                    'value' => '',
                    'param_name' => 'right_position_phone',
                    'description' => ''
                ),


                array(
                    "type" => "nectar_numerical",
                    "class" => "",
                    'group' => $group_name,
                    "heading" => '<span class="group-title">' . esc_html__("Transform", "salient-core") . "</span><span class='attr-title'>" . esc_html__("Translate Y", "salient-core") . "</span>",
                    "value" => "",
                    "placeholder" => esc_html__("Translate Y", 'salient-core'),
                    "edit_field_class" => "nectar-one-half desktop transform-device-group",
                    "param_name" => "translate_y_desktop",
                    "description" => ""
                ),

                array(
                    "type" => "nectar_numerical",
                    "class" => "",
                    'group' => $group_name,
                    "placeholder" => esc_html__("Translate X", 'salient-core'),
                    "heading" => "<span class='attr-title'>" . esc_html__("Translate X", "salient-core") . "</span>",
                    "value" => "",
                    "edit_field_class" => "nectar-one-half nectar-one-half-last desktop transform-device-group",
                    "param_name" => "translate_x_desktop",
                    "description" => ""
                ),

                array(
                    "type" => "nectar_numerical",
                    "class" => "",
                    'group' => $group_name,
                    "placeholder" => esc_html__("Translate Y", 'salient-core'),
                    "heading" => "<span class='attr-title'>" . esc_html__("Translate Y", "salient-core") . "</span>",
                    "value" => "",
                    "edit_field_class" => "nectar-one-half tablet transform-device-group",
                    "param_name" => "translate_y_tablet",
                    "description" => ""
                ),

                array(
                    "type" => "nectar_numerical",
                    "class" => "",
                    'group' => $group_name,
                    "placeholder" => esc_html__("Translate X", 'salient-core'),
                    "heading" => "<span class='attr-title'>" . esc_html__("Translate X", "salient-core") . "</span>",
                    "value" => "",
                    "edit_field_class" => "nectar-one-half nectar-one-half-last tablet transform-device-group",
                    "param_name" => "translate_x_tablet",
                    "description" => ""
                ),
                array(
                    "type" => "nectar_numerical",
                    "class" => "",
                    'group' => $group_name,
                    "placeholder" => esc_html__("Translate Y", 'salient-core'),
                    "heading" => "<span class='attr-title'>" . esc_html__("Translate Y", "salient-core") . "</span>",
                    "value" => "",
                    "edit_field_class" => "nectar-one-half phone transform-device-group",
                    "param_name" => "translate_y_phone",
                    "description" => ""
                ),

                array(
                    "type" => "nectar_numerical",
                    "class" => "",
                    'group' => $group_name,
                    "placeholder" => esc_html__("Translate X", 'salient-core'),
                    "heading" => "<span class='attr-title'>" . esc_html__("Translate X", "salient-core") . "</span>",
                    "value" => "",
                    "edit_field_class" => "nectar-one-half nectar-one-half-last phone transform-device-group",
                    "param_name" => "translate_x_phone",
                    "description" => ""
                ),

            );

            if( $zindex ) {
                $position_arr[] = array(
					"type" => "textfield",
					"class" => "",
					"heading" => esc_html__("Z-index", "salient-core"),
					'group' => $group_name,
					"param_name" => "zindex",
					"admin_label" => false,
					"description" => esc_html__("If you want to set a custom stacking order on this element, enter it here.", "salient-core"),
				);
            }

            return $position_arr;
        }

        public static function layout_group($group_name = '', $layout_controls = 'all', $edit_field_class = '') {
            // Initialize flexbox options
            self::init_flexbox_options();

            $layout_arr = array();

            // Only add content_layout if multiple layout types are allowed
            if ($layout_controls === 'all') {
                $layout_arr[] = array(
                    "type" => "nectar_radio_tab_selection",
                    "class" => "",
                    'save_always' => true,
                    "heading" => esc_html__("Layout Type", "salient-core"),
                    'edit_field_class' => 'vc_col-xs-12'.$edit_field_class,
                    "param_name" => "content_layout",
                    "options" => array(
                        esc_html__("Default", "salient-core") => "default",
                        esc_html__("Flexbox", "salient-core") => "flexbox",
                    ),
                    'group' => $group_name,
                );
            }

            // Add flexbox layout controls
            if ($layout_controls === 'all' || $layout_controls === 'flexbox') {
                $flex_dependency = $layout_controls === 'all' ?
                    array('element' => 'content_layout', 'value' => 'flexbox') :
                    array();

                // Flexbox Layout
                $layout_arr[] = array(
                    "type" => "nectar_checkbox_tab_selection",
                    "class" => "",
                    'heading' => '<span class="group-title">' . esc_html__( 'Flexbox Layout', 'salient-core' ) . '</span>',
                    'edit_field_class' => 'desktop flexbox-layout-device-group' . $edit_field_class,
                    "param_name" => "flex_layout_desktop",
                    'min_items' => 1,
                    'max_items' => 1,
                    "options" => array(
                        esc_html__("Horizontal", "salient-core") => "row",
                        esc_html__("Vertical", "salient-core") => "column",
                    ),
                    'group' => $group_name,
                    'dependency' => $flex_dependency,
                );

                $layout_arr[] = array(
                    "type" => "nectar_checkbox_tab_selection",
                    'heading' => '',
                    'edit_field_class' => 'tablet flexbox-layout-device-group' . $edit_field_class,
                    "param_name" => "flex_layout_tablet",
                    "value" => "",
                    'start_empty' => true,
                    'min_items' => 0,
                    'max_items' => 1,
                    "options" => array(
                        esc_html__("Row", "salient-core") => "row",
                        esc_html__("Column", "salient-core") => "column",
                    ),
                    'dependency' => $flex_dependency,
                    'group' => $group_name,
                );

                $layout_arr[] = array(
                    "type" => "nectar_checkbox_tab_selection",
                    'heading' => '',
                    'edit_field_class' => 'phone flexbox-layout-device-group' . $edit_field_class,
                    "param_name" => "flex_layout_phone",
                    'start_empty' => true,
                    'min_items' => 0,
                    'max_items' => 1,
                    "options" => array(
                        esc_html__("Row", "salient-core") => "row",
                        esc_html__("Column", "salient-core") => "column",
                    ),
                    'dependency' => $flex_dependency,
                    'group' => $group_name,
                );

                // Flexbox Justify Content
                $layout_arr[] = array(
                    "type" => "nectar_checkbox_tab_selection",
                    "class" => "",
                    "heading" => '<span class="group-title">' . esc_html__("Justify Content", "salient-core") . '</span>',
                    'edit_field_class' => 'desktop flexbox-justify-content-device-group' . $edit_field_class,
                    "param_name" => "flex_justify_content_desktop",
                    'min_items' => 1,
                    'max_items' => 1,
                    "options" => self::$flexbox_justify_content_options,
                    'dependency' => $flex_dependency,
                    'group' => $group_name,
                );

                $layout_arr[] = array(
                    "type" => "nectar_checkbox_tab_selection",
                    "class" => "",
                    "heading" => '',
                    'edit_field_class' => 'tablet flexbox-justify-content-device-group' . $edit_field_class,
                    "param_name" => "flex_justify_content_tablet",
                    "value" => "",
                    'start_empty' => true,
                    'min_items' => 0,
                    'max_items' => 1,
                    "options" => self::$flexbox_justify_content_options,
                    'dependency' => $flex_dependency,
                    'group' => $group_name,
                );

                $layout_arr[] = array(
                    "type" => "nectar_checkbox_tab_selection",
                    "class" => "",
                    "heading" => '',
                    'edit_field_class' => 'phone flexbox-justify-content-device-group' . $edit_field_class,
                    "param_name" => "flex_justify_content_phone",
                    'start_empty' => true,
                    'min_items' => 0,
                    'max_items' => 1,
                    "options" => self::$flexbox_justify_content_options,
                    'dependency' => $flex_dependency,
                    'group' => $group_name,
                );

                // Flexbox Align Items
                $layout_arr[] = array(
                    "type" => "nectar_checkbox_tab_selection",
                    "class" => "",
                    "heading" => '<span class="group-title">' . esc_html__("Align Items", "salient-core") . '</span>',
                    'edit_field_class' => 'desktop flexbox-align-items-device-group' . $edit_field_class,
                    "param_name" => "flex_align_items_desktop",
                    'min_items' => 1,
                    'max_items' => 1,
                    "options" => self::$flexbox_align_items_options,
                    'dependency' => $flex_dependency,
                    'group' => $group_name,
                );

                $layout_arr[] = array(
                    "type" => "nectar_checkbox_tab_selection",
                    "class" => "",
                    "heading" => '',
                    'edit_field_class' => 'tablet flexbox-align-items-device-group' . $edit_field_class,
                    "param_name" => "flex_align_items_tablet",
                    "value" => "",
                    'start_empty' => true,
                    'min_items' => 0,
                    'max_items' => 1,
                    "options" => self::$flexbox_align_items_options,
                    'dependency' => $flex_dependency,
                    'group' => $group_name,
                );

                $layout_arr[] = array(
                    "type" => "nectar_checkbox_tab_selection",
                    "class" => "",
                    'save_always' => true,
                    "heading" => '',
                    'edit_field_class' => 'phone flexbox-align-items-device-group' . $edit_field_class,
                    "param_name" => "flex_align_items_phone",
                    'start_empty' => true,
                    'min_items' => 0,
                    'max_items' => 1,
                    "options" => self::$flexbox_align_items_options,
                    'dependency' => $flex_dependency,
                    'group' => $group_name,
                );

                 // wrap
                 $layout_arr[] = array(
                    "type" => "nectar_checkbox_tab_selection",
                    "heading" => '<span class="group-title">' . esc_html__("Wrap", "salient-core") . '</span>',
                    'edit_field_class' => 'desktop flexbox-wrap-device-group' . $edit_field_class,
                    'param_name' => 'flex_wrap_desktop',
                    'min_items' => 1,
                    'max_items' => 1,
                    "options" => self::$flexbox_wrap_options,
                    'dependency' => $flex_dependency,
                    'group' => $group_name,
                );

                $layout_arr[] = array(
                    "type" => "nectar_checkbox_tab_selection",
                    "heading" => '',
                    'edit_field_class' => 'tablet flexbox-wrap-device-group' . $edit_field_class,
                    'param_name' => 'flex_wrap_tablet',
                    'min_items' => 0,
                    'max_items' => 1,
                    'start_empty' => true,
                    "options" => self::$flexbox_wrap_options,
                    'dependency' => $flex_dependency,
                    'group' => $group_name,
                );

                $layout_arr[] = array(
                    "type" => "nectar_checkbox_tab_selection",
                    "heading" => '',
                    'edit_field_class' => 'phone flexbox-wrap-device-group' . $edit_field_class,
                    'param_name' => 'flex_wrap_phone',
                    'min_items' => 0,
                    'max_items' => 1,
                    'start_empty' => true,
                    "options" => self::$flexbox_wrap_options,
                    'dependency' => $flex_dependency,
                    'group' => $group_name,
                );

                // Flexbox Reverse
                $layout_arr[] = array(
                    "type" => "nectar_checkbox_tab_selection",
                    "heading" => '<span class="group-title">' . esc_html__("Reverse", "salient-core") . '</span>',
                    'edit_field_class' => 'desktop flexbox-reverse-device-group' . $edit_field_class,
                    'param_name' => 'flex_reverse_desktop',
                    'min_items' => 1,
                    'max_items' => 1,
                    "options" => array(
                        esc_html__("Disabled", "salient-core") => "false",
                        esc_html__("Enabled", "salient-core") => "true",
                    ),
                    'dependency' => $flex_dependency,
                    'group' => $group_name,
                );

                $layout_arr[] = array(
                    "type" => "nectar_checkbox_tab_selection",
                    "heading" => '',
                    'edit_field_class' => 'tablet flexbox-reverse-device-group' . $edit_field_class,
                    'param_name' => 'flex_reverse_tablet',
                    'min_items' => 0,
                    'max_items' => 1,
                    'start_empty' => true,
                    "options" => array(
                        esc_html__("Disabled", "salient-core") => "false",
                        esc_html__("Enabled", "salient-core") => "true",
                    ),
                    'dependency' => $flex_dependency,
                    'group' => $group_name,
                );

                $layout_arr[] = array(
                    "type" => "nectar_checkbox_tab_selection",
                    "heading" => '',
                    'edit_field_class' => 'phone flexbox-reverse-device-group' . $edit_field_class,
                    'param_name' => 'flex_reverse_phone',
                    'min_items' => 0,
                    'max_items' => 1,
                    'start_empty' => true,
                    "options" => array(
                        esc_html__("Disabled", "salient-core") => "false",
                        esc_html__("Enabled", "salient-core") => "true",
                    ),
                    'dependency' => $flex_dependency,
                    'group' => $group_name,
                );

                // Gap
                $layout_arr[] =  array(
                    "type" => "nectar_numerical",
                    "heading" => '<span class="group-title">' . esc_html__("Gap", "salient-core") . '</span>',
                    'param_name' => 'flex_gap_desktop',
                    'edit_field_class' => 'desktop zero-floor flexbox-gap-device-group' . $edit_field_class,
                    'value' => '10px',
                    'save_always' => true,
                    'dependency' => $flex_dependency,
                    'group' => $group_name,
                );
                $layout_arr[] =  array(
                    "type" => "nectar_numerical",
                    'heading' => '',
                    'param_name' => 'flex_gap_tablet',
                    'edit_field_class' => 'tablet zero-floor flexbox-gap-device-group' . $edit_field_class,
                    'value' => '',
                    'dependency' => $flex_dependency,
                    'group' => $group_name,
                );
                $layout_arr[] =  array(
                    "type" => "nectar_numerical",
                    'heading' => '',
                    'param_name' => 'flex_gap_phone',
                    'edit_field_class' => 'phone zero-floor flexbox-gap-device-group' . $edit_field_class,
                    'value' => '',
                    'dependency' => $flex_dependency,
                    'group' => $group_name,
                );

            }
            return $layout_arr;
        }
        public static function css_animation_group($group_name)
        {
            $css_arr = array(
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'group' => $group_name,
                    "heading" => esc_html__("CSS Animation", "salient-core"),
                    'param_name' => 'css_animation',
                    'value' => array(
                        esc_html__("None", "salient-core") => "none",
                        esc_html__("Fade In", "salient-core") => "fade-in",
                        esc_html__("Fade In From Left", "salient-core") => "fade-in-from-left",
                        esc_html__("Fade In Right", "salient-core") => "fade-in-from-right",
                        esc_html__("Fade In From Bottom", "salient-core") => "fade-in-from-bottom",
                        esc_html__("Grow In", "salient-core") => "grow-in",
                    )
                ),
                array(
					"type" => "checkbox",
					"class" => "",
					'group' => $group_name,
					'edit_field_class' => 'vc_col-xs-12 salient-fancy-checkbox',
					"heading" => esc_html__("Disable CSS Animation On Mobile", "salient-core"),
					"param_name" => "mobile_disable_css_animation",
					"value" => array(esc_html__("Yes", "salient-core") => 'true'),
				),

				array(
					"type" => "textfield",
					"class" => "",
					"heading" => esc_html__("CSS Animation Delay", "salient-core"),
					'group' => $group_name,
					"param_name" => "css_animation_delay",
					"edit_field_class" => "nectar-one-half",
					"admin_label" => false,
					"description" => esc_html__("Optionally enter a delay in milliseconds for when the CSS animation will trigger e.g. 150.", "salient-core"),
				),
				array(
					"type" => "textfield",
					"class" => "",
					'group' => $group_name,
					"edit_field_class" => "nectar-one-half nectar-one-half-last",
					"heading" => esc_html__("CSS Animation Offset", "salient-core" ),
					"param_name" => "css_animation_offset",
					"admin_label" => false,
					"description" => esc_html__("Optionally specify the offset from the top of the screen for when the CSS animation will trigger. Defaults to 95%.", "salient-core"),
				),
            );

            return $css_arr;
        }


        public static function font_sizing_group($desktop_font_name = 'font_size_desktop', $label = 'Custom Font Size', $group = '') {

            $font_sizing_arr = array(
                array(
                    "type" => "textfield",
                    "heading" => '<span class="group-title">' . esc_html__($label, "salient-core") . "</span>",
                    "group" => $group,
                    "edit_field_class" => "desktop font-size-device-group",
                    "param_name" => $desktop_font_name,
                ),
                array(
                    "type" => "textfield",
                    "heading" => '',
                    "group" => $group,
                    "edit_field_class" => "tablet font-size-device-group",
                    "param_name" => "font_size_tablet",
                ),
                array(
                    "type" => "textfield",
                    "heading" => '',
                    "group" => $group,
                    "edit_field_class" => "phone font-size-device-group",
                    "param_name" => "font_size_phone",
                ),
                array(
                    "type" => "textfield",
                    "heading" =>  esc_html__("Line Height", "salient-core"),
                    "group" => $group,
                    "param_name" => "font_line_height",
                ),
                array(
                    "type" => "textfield",
                    "heading" =>  '<span class="group-title">' . esc_html__('Text Indent', 'salient-core') . '</span>',
                    "group" => $group,
                    "param_name" => "font_text_indent_desktop",
                    "edit_field_class" => "desktop font-text-indent-device-group",
                ),
                array(
                    "type" => "textfield",
                    "heading" => '',
                    "group" => $group,
                    "edit_field_class" => "tablet font-text-indent-device-group",
                    "param_name" => "font_text_indent_tablet",
                ),
                array(
                    "type" => "textfield",
                    "heading" => '',
                    "group" => $group,
                    "edit_field_class" => "phone font-text-indent-device-group",
                    "param_name" => "font_text_indent_phone",
                ),

                array(
                    "type" => "nectar_numerical",
                    "class" => "",
                    "heading" => esc_html__("Min Font Size", 'salient-core'),
                    "group" => $group,
                    "value" => "",
                    "placeholder" => '',
                    "edit_field_class" => "nectar-one-half zero-floor",
                    "param_name" => "font_size_min",
                    "description" => ""
                ),
                array(
                    "type" => "nectar_numerical",
                    "class" => "",
                    "heading" => esc_html__("Max Font Size", 'salient-core'),
                    "group" => $group,
                    "value" => "",
                    "placeholder" => '',
                    "edit_field_class" => "nectar-one-half zero-floor",
                    "param_name" => "font_size_max",
                    "description" => ""
                ),
            );

            return $font_sizing_arr;

        }

        public static function theme_color_or_custom_group($group_name = '', $base_param = 'color', $heading = 'Color', $dependency = [], $include_transparent = false, $custom_heading = 'Custom Color', $custom_param_name = '', $toggle_param_name = '') {

            $toggle_param = $toggle_param_name ? $toggle_param_name : $base_param . '_source';
            $palette_param = $base_param . '_palette';

            $group_arr = array(
                array(
                    'type' => 'dropdown',
                    'heading' => '<span class="group-title">' . esc_html__($heading, 'salient-core') . '</span>',
                    'param_name' => $toggle_param,
                    'value' => array(
                        esc_html__('Global Color Scheme', 'salient-core') => 'palette',
                        esc_html__('Custom', 'salient-core') => 'custom',
                    ),
                    'std' => 'custom',
                    'group' => $group_name,
                    'save_always' => true,
                    'dependency' => $dependency,
                ),
                array(
                    'type' => 'nectar_theme_color',
                    'heading' => '',
                    'param_name' => $palette_param,
                    'include_transparent' => $include_transparent,
                    'group' => $group_name,
                    'dependency' => array('element' => $toggle_param, 'value' => 'palette'),
                ),
            );

            // Standardized custom color field.
            $group_arr[] = array(
                'type' => 'colorpicker',
                'heading' => esc_html__($custom_heading, 'salient-core'),
                'param_name' => $base_param,
                'value' => '',
                'group' => $group_name,
                'dependency' => array('element' => $toggle_param, 'value' => 'custom'),
            );

            return $group_arr;
        }

        public static function color_layer_group($group_name, $dependency = [], $edit_field_class = '') {
            $color_layer_arr = array(
                array(
					"type" => "nectar_gradient_selection",
					"class" => "",
					"group" => $group_name,
					"heading" => '',
					"param_name" => "advanced_gradient",
					"value" => "",
                    "edit_field_class" => "generate-color-overlay-preview vc_col-xs-12",
                    "dependency" => $dependency,
					"description" => ''
				),

                array(
                    "type" => "nectar_radio_tab_selection",
                    "class" => "",
                    "edit_field_class" => "col-md-6" . $edit_field_class,
                    'save_always' => true,
                    "group" => $group_name,
                    "heading" => esc_html__("Gradient Type", "salient-core"),
                    "param_name" => "advanced_gradient_display_type",
                    "dependency" => $dependency,
                    "options" => array(
                        esc_html__("Linear", "salient-core") => "linear",
                        esc_html__("Radial", "salient-core") => "radial",
                    ),
                ),

                array(
					"type" => "nectar_angle_selection",
					"class" => "",
					"edit_field_class" => "col-md-6 col-md-6-last" . $edit_field_class,
					"group" => $group_name,
					"heading" => esc_html__("Gradient Angle", "salient-core"),
					"param_name" => "advanced_gradient_angle",
					"value" => "",
                    "dependency" => Array('element' => "advanced_gradient_display_type", 'value' => array('linear')),
					"description" => ''
				),
                array(
					"type" => "dropdown",
					"class" => "",
					"edit_field_class" => "col-md-6 col-md-6-last" . $edit_field_class,
                     "group" => $group_name,
					"heading" => esc_html__("Gradient Position", "salient-core"),
					"param_name" => "advanced_gradient_radial_position",
                    "dependency" => Array('element' => "advanced_gradient_display_type", 'value' => array('radial')),
					'value' => array(
						esc_html__("Center", "salient-core") => "center",
						esc_html__("Top Left", "salient-core") => "top left",
                        esc_html__("Top", "salient-core") => "top",
                        esc_html__("Top Right", "salient-core") => "top right",
                        esc_html__("Right", "salient-core") => "right",
                        esc_html__("Bottom Right", "salient-core") => "bottom right",
                        esc_html__("Bottom", "salient-core") => "bottom",
                        esc_html__("Bottom Left", "salient-core") => "bottom left",
                        esc_html__("Left", "salient-core") => "left",
					)
				),

                array(
					'type' => 'nectar_range_slider',
					'heading' => esc_html__( 'Opacity', 'salient-core' ),
					'param_name' => 'advanced_gradient_opacity',
					"edit_field_class" => "col-md-6 col-md-6-first" . $edit_field_class,
					"dependency" => $dependency,
					"group" => $group_name,
					'value' => '1',
					'options' => array(
						'min' => '0',
						'max' => '1',
						'step' => '0.1',
						'suffix' => ''
					),
					'description' => ''
				),
                array(
					'type' => 'nectar_range_slider',
					'heading' => esc_html__( 'Opacity Hover', 'salient-core' ),
					'param_name' => 'advanced_gradient_opacity_hover',
					"edit_field_class" => "col-md-6 col-md-6-last" . $edit_field_class,
					"dependency" => $dependency,
					"group" => $group_name,
					'value' => '1',
					'options' => array(
						'min' => '0',
						'max' => '1',
						'step' => '0.1',
						'suffix' => ''
					),
					'description' => ''
				),
            );

            return $color_layer_arr;
        }

        public static function mask_group($group_name)
        {

            $alignments = array(
                esc_html__('Default (Center Center)', 'salient-core') => 'default',
                esc_html__('Left Top', 'salient-core') => 'left-top',
                esc_html__('Left Center', 'salient-core') => 'left-center',
                esc_html__('Left Bottom', 'salient-core') => 'left-bottom',
                esc_html__('Center Top', 'salient-core') => 'center-top',
                esc_html__('Center Center', 'salient-core') => 'center-center',
                esc_html__('Center Bottom', 'salient-core') => 'center-bottom',
                esc_html__('Right Top', 'salient-core') => 'right-top',
                esc_html__('Right Center', 'salient-core') => 'right-center',
                esc_html__('Right Bottom', 'salient-core') => 'right-bottom'
            );

            $mask_shape_dep = [
                'circle',
                'circle-rect',
                'triangle',
                'parallelogram',
                'rhombus',
                'star',
                'heptagon',
                'ellipse',
                'lightning',
                'circle-top-left',
                'circle-top-right',
                'circle-bottom-left',
                'circle-bottom-right',
                'x-symbol',
                'custom'
            ];
            $mask_arr = array(
                array(
                    'group' => $group_name,
                    'type' => 'checkbox',
                    'class' => '',
                    'heading' => esc_html__('Enable Mask', 'salient-core'),
                    'param_name' => 'mask_enable',
                    'edit_field_class' => 'vc_col-xs-12 salient-fancy-checkbox',
                    'value' => array(esc_html__('Yes', 'salient-core') => 'true'),
                    'description' => ''
                ),
                array(
                    'group' => $group_name,
                    'type' => 'nectar_radio_html',
                    'class' => '',
                    'heading' => esc_html__('Mask Shape', 'salient-core'),
                    'param_name' => 'mask_shape',
                    'options' => array(
                        '<div style="clip-path: circle(50% at 50% 50%)" class="nectar-shape"></div>' => 'circle',
                        '<div class="nectar-shape nectar-shape-bottom-gradient"></div>' => 'blur-gradient',
                        '<div style="clip-path: polygon(50% 0%, 0% 100%, 100% 100%)" class="nectar-shape"></div>' => 'triangle',
                        '<div style="clip-path: polygon(25% 0%, 100% 0%, 75% 100%, 0% 100%)" class="nectar-shape"></div>' => 'parallelogram',
                        '<div style="clip-path: inset(0px 0px 0px round 100% 100% 0px 0px)" class="nectar-shape"></div>' => 'circle-rect',
                        '<div style="clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%)" class="nectar-shape"></div>' => 'rhombus',
                        '<svg class="svg nectar-shape" viewBox="0 0 100 98.98"><polygon points="50 6.63 57.12 0 62.07 8.37 70.77 4.01 73.17 13.44 82.74 11.7 82.39 21.43 92.06 22.46 88.98 31.69 97.97 35.4 92.42 43.39 100 49.49 92.42 55.59 97.97 63.58 88.98 67.3 92.06 76.52 82.39 77.56 82.74 87.28 73.17 85.55 70.77 94.97 62.07 90.61 57.12 98.98 50 92.35 42.88 98.98 37.93 90.61 29.23 94.97 26.83 85.55 17.26 87.28 17.61 77.56 7.94 76.52 11.02 67.3 2.02 63.58 7.58 55.59 0 49.49 7.58 43.39 2.02 35.4 11.02 31.69 7.94 22.46 17.61 21.43 17.26 11.7 26.83 13.44 29.23 4.01 37.93 8.37 42.88 0 50 6.63"/></svg>' => 'star',
                        '<div style="clip-path: polygon(50% 0%, 90% 20%, 100% 60%, 75% 100%, 25% 100%, 0% 60%, 10% 20%)" class="nectar-shape"></div>' => 'heptagon',
                        '<div style="clip-path: ellipse(25% 40% at 50% 50%);" class="nectar-shape"></div>' => 'ellipse',
                        '<svg class="svg nectar-shape" viewBox="0 0 65.71 100"><polygon points="25.48 100 25.48 59.7 0 59.7 40.23 0 40.23 40.3 65.71 40.3 25.48 100"/></svg>' => 'lightning',
                        '<div style="clip-path: circle(68.5% at 0% 0%)" class="nectar-shape"></div>' => 'circle-top-left',
                        '<div style="clip-path: circle(68.5% at 100% 0%)" class="nectar-shape"></div>' => 'circle-top-right',
                        '<div style="clip-path: circle(68.5% at 0% 100%)" class="nectar-shape"></div>' => 'circle-bottom-left',
                        '<div style="clip-path: circle(68.5% at 100% 100%)" class="nectar-shape"></div>' => 'circle-bottom-right',
                        '<div style="clip-path: polygon(20% 0%, 0% 20%, 30% 50%, 0% 80%, 20% 100%, 50% 70%, 80% 100%, 100% 80%, 70% 50%, 100% 20%, 80% 0%, 50% 30%);" class="nectar-shape"></div>' => 'x-symbol',
                        '<svg class="svg nectar-shape" viewBox="0 0 1 1"><path d="M0.5,0 C0.224,0,0,0.224,0,0.5 s0.224,0.5,0.5,0.5 c0.276,0,0.5,-0.224,0.5,-0.5 S0.776,0,0.5,0 M0.5,0.15 c0.091,0,0.165,0.074,0.165,0.165 c0,0.091,-0.074,0.165,-0.165,0.165 c-0.091,0,-0.165,-0.074,-0.165,-0.165 C0.335,0.224,0.409,0.15,0.5,0.15 M0.5,0.869 c-0.091,0,-0.175,-0.033,-0.239,-0.088 c-0.016,-0.013,-0.025,-0.033,-0.025,-0.054 c0,-0.093,0.075,-0.167,0.168,-0.167 h0.192 c0.093,0,0.167,0.074,0.167,0.167 c0,0.021,-0.009,0.04,-0.025,0.054 C0.675,0.836,0.591,0.869,0.5,0.869"></path></svg>' => 'custom',
                    ),
                    'description' => '',
                    'std' => 'circle',
                ),
                array(
                    'group' => $group_name,
                    'type' => 'fws_image',
                    'heading' => esc_html__('Image', 'salient-core'),
                    'param_name' => 'mask_custom_image',
                    'value' => '',
                    'dependency' => array('element' => 'mask_shape', 'value' => array('custom')),
                    'description' => esc_html__('Select a .png image from media library to use as a mask.', 'salient-core')
                ),
                array(
                    'group' => $group_name,
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => esc_html__('Mask Size', 'salient-core'),
                    'dependency' => array('element' => 'mask_shape', 'value' => $mask_shape_dep),
                    'param_name' => 'mask_size',
                    'value' => array(
                        'Contain' => 'contain',
                        'Cover' => 'cover',
                        'Custom' => 'custom',
                    ),
                    'description' => '',
                    'std' => 'fit',
                ),
                array(
                    'group' => $group_name,
                    'type' => 'nectar_range_slider',
                    'dependency' => array('element' => 'mask_size', 'value' => array('custom')),
                    'heading' => esc_html__('Mask Scale', 'salient-core'),
                    'param_name' => 'mask_scale',
                    'value' => '100',
                    'options' => array(
                        'min' => '0',
                        'max' => '200',
                    ),
                    'description' => ''
                ),
                array(
                    'group' => $group_name,
                    'type' => 'dropdown',
                    'heading' => '<span class="group-title">' . esc_html__('Mask Alignment', 'salient-core') . '</span>',
                    'param_name' => 'mask_alignment_desktop',
                    'edit_field_class' => 'desktop mask-alignment-device-group',
                    'dependency' => array('element' => 'mask_shape', 'value' => $mask_shape_dep),
                    'value' => $alignments,
                    'description' => ''
                ),
                array(
                    'group' => $group_name,
                    'type' => 'dropdown',
                    'heading' => '',
                    'param_name' => 'mask_alignment_tablet',
                    'edit_field_class' => 'tablet mask-alignment-device-group',
                    'dependency' => array('element' => 'mask_shape', 'value' => $mask_shape_dep),
                    'value' => $alignments,
                    'description' => ''
                ),
                array(
                    'group' => $group_name,
                    'type' => 'dropdown',
                    'heading' => '',
                    'param_name' => 'mask_alignment_phone',
                    'edit_field_class' => 'phone mask-alignment-device-group',
                    'dependency' => array('element' => 'mask_shape', 'value' => $mask_shape_dep),
                    'value' => $alignments,
                    'description' => ''
                ),

            );

            // Hide options when not in dedicated mask group
            if ('mask' !== $group_name) {
                foreach ($mask_arr as $index => $array) {
                    if ('mask_enable' !== $array['param_name'] && !isset($array['dependency'])) {
                        $mask_arr[$index]['dependency'] = array('element' => 'mask_enable', 'not_empty' => true);
                    }
                }
            }

            return $mask_arr;
        }

        /**
         * Generate responsive WPBakery field arrays for desktop, tablet, and phone.
         *
         * Creates three field configurations for a responsive device group that automatically
         * handles admin styling and JS initialization.
         *
         * @param array $args {
         *     Configuration arguments for the responsive field.
         *
         *     @type string $type           Field type (e.g., 'dropdown', 'nectar_numerical', 'textfield'). Required.
         *     @type string $heading        Field heading/label. Required.
         *     @type string $param_name     Base parameter name (will be suffixed with _desktop, _tablet, _phone). Required.
         *     @type string $group_name     CSS class name for the device group (without -device-group suffix). Required.
         *     @type mixed  $desktop_value  Default value for desktop. Optional.
         *     @type mixed  $tablet_value   Default value for tablet (falls back to empty). Optional.
         *     @type mixed  $phone_value    Default value for phone (falls back to empty). Optional.
         *     @type array  $dependency     Field dependency configuration. Optional.
         *     @type string $description    Field description. Optional.
         *     @type string $extra_class    Additional CSS classes. Optional.
         *     @type string $group          WPBakery tab group name. Optional.
         *     @type bool   $save_always    Whether to always save the field value. Optional.
         * }
         * @return array Array of three field configurations (desktop, tablet, phone).
         */
        public static function responsive_field( $args ) {

            $defaults = array(
                'type'          => 'dropdown',
                'heading'       => '',
                'param_name'    => '',
                'group_name'    => '',
                'desktop_value' => '',
                'tablet_value'  => '',
                'phone_value'   => '',
                'dependency'    => array(),
                'description'   => '',
                'extra_class'   => '',
                'group'         => '',
                'save_always'   => false,
                'class'         => '',
                'placeholder'   => '',
            );

            $args = wp_parse_args( $args, $defaults );

            $fields = array();
            $devices = array( 'desktop', 'tablet', 'phone' );

            foreach ( $devices as $device ) {

                $field = array(
                    'type'       => $args['type'],
                    'param_name' => $args['param_name'] . '_' . $device,
                    'class'      => $args['class'],
                );

                // Heading only for desktop.
                if ( 'desktop' === $device ) {
                    $field['heading'] = '<span class="group-title">' . esc_html( $args['heading'] ) . '</span>';
                } else {
                    $field['heading'] = '';
                }

                // Value per device
                $field['value'] = $args[ $device . '_value' ];

                // Build edit_field_class with marker class for auto-init targeting.
                $edit_class = 'nectar-resp-field ' . $device . ' ' . $args['group_name'] . '-device-group';
                if ( ! empty( $args['extra_class'] ) ) {
                    $edit_class .= ' ' . $args['extra_class'];
                }
                $field['edit_field_class'] = $edit_class;

                // Optional properties.
                if ( ! empty( $args['dependency'] ) ) {
                    $field['dependency'] = $args['dependency'];
                }

                if ( ! empty( $args['description'] ) && 'desktop' === $device ) {
                    $field['description'] = $args['description'];
                } else {
                    $field['description'] = '';
                }

                if ( ! empty( $args['group'] ) ) {
                    $field['group'] = $args['group'];
                }

                if ( $args['save_always'] ) {
                    $field['save_always'] = true;
                }

                // Type-specific handling.
                if ( 'nectar_numerical' === $args['type'] ) {
                    $field['placeholder'] = $args['placeholder'];
                }

                $fields[] = $field;
            }

            return $fields;
        }
    }

    // init.
    global $SalientWPbakeryParamGroups;
    $SalientWPbakeryParamGroups = Salient_Core::getInstance();
}
