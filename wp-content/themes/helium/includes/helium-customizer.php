<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

if( ! class_exists( 'Youxi_Customize_Manager' ) ) {
	require( get_template_directory() . '/lib/framework/customizer/class-manager.php' );
}

class Helium_Customize_Manager extends Youxi_Customize_Manager {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct();

		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ) );

		add_action( 'customize_register', array( $this, 'title_tagline_customizer' ) );

		add_action( 'customize_register', array( $this, 'site_customizer' ) );

		add_action( 'customize_register', array( $this, 'ajax_customizer' ) );

		add_action( 'customize_register', array( $this, 'color_customizer' ) );

		add_action( 'customize_register', array( $this, 'typography_customizer' ) );

		add_action( 'customize_register', array( $this, 'blog_customizer' ) );

		if( defined( 'YOUXI_PORTFOLIO_VERSION' ) ) {
			add_action( 'customize_register', array( $this, 'portfolio_customizer' ) );
		}

		if( class_exists( 'Easy_Digital_Downloads' ) ) {
			add_action( 'customize_register', array( $this, 'edd_customizer' ) );
		}
	}

	public function enqueue_control_scripts() {

		/* Get theme version */
		$wp_theme = wp_get_theme();
		$theme_version = $wp_theme->exists() ? $wp_theme->get( 'Version' ) : false;

		wp_enqueue_script( 'helium-customize-controls', get_template_directory_uri() . '/assets/admin/js/helium.customize-controls.js', array( 'customize-controls' ), $theme_version, true );
		wp_localize_script( 'helium-customize-controls', '_heliumCustomizeControls', array( 'prefix' => $this->prefix() ) );
	}

	public function title_tagline_customizer( $wp_customize ) {

		$prefix = $this->prefix();

		/* Identity Settings */

		$wp_customize->add_setting( $prefix . '[custom_logo_mobile]', array(
			'theme_supports' => array( 'custom-logo' ), 
			'sanitize_callback' => 'helium_customizer_sanitize_noop'
		));

		/* Identity Controls */

		$custom_logo_args = get_theme_support( 'custom-logo' );

		$wp_customize->add_control( new WP_Customize_Cropped_Image_Control(
			$wp_customize, $prefix . '[custom_logo_mobile]', array(
				'label'         => esc_html__( 'Mobile Logo', 'helium' ),
				'section'       => 'title_tagline',
				'priority'      => 9,
				'height'        => $custom_logo_args[0]['height'],
				'width'         => $custom_logo_args[0]['width'],
				'flex_height'   => $custom_logo_args[0]['flex-height'],
				'flex_width'    => $custom_logo_args[0]['flex-width'],
				'button_labels' => array(
					'select'       => esc_html__( 'Select logo', 'helium' ),
					'change'       => esc_html__( 'Change logo', 'helium' ),
					'remove'       => esc_html__( 'Remove', 'helium' ),
					'default'      => esc_html__( 'Default', 'helium' ),
					'placeholder'  => esc_html__( 'No logo selected', 'helium' ),
					'frame_title'  => esc_html__( 'Select logo', 'helium' ),
					'frame_button' => esc_html__( 'Choose logo', 'helium' ),
				)
			)
		));
	}

	public function color_customizer( $wp_customize ) {

		$prefix = $this->prefix();

		/* Styling Settings */

		$wp_customize->add_setting( $prefix . '[accent_color]', array(
			'default' => '#3dc9b3', 
			'sanitize_callback' => 'sanitize_hex_color'
		));

		/* Styling Controls */

		$priority = 0;

		$wp_customize->add_control( new WP_Customize_Color_Control(
			$wp_customize, $prefix . '[accent_color]', array(
				'label' => esc_html__( 'Accent Color', 'helium' ), 
				'section' => 'colors', 
				'priority' => $priority++
			)
		));
	}

	public function site_customizer( $wp_customize ) {

		$prefix = $this->prefix();

		/* Section: Header */

		$wp_customize->add_section( $prefix . '_header', array(
			'title' => esc_html__( 'Header', 'helium' ), 
			'priority' => 41
		));

		/* Header Settings */

		$wp_customize->add_setting( $prefix . '[logo_height]', array(
			'default' => 25, 
			'sanitize_callback' => 'absint'
		));
		$wp_customize->add_setting( $prefix . '[show_search]', array(
			'default' => true, 
			'sanitize_callback' => 'wp_validate_boolean'
		));
		$wp_customize->add_setting( $prefix . '[copyright_text]', array(
			'sanitize_callback' => 'wp_kses_post', 
			'default' => esc_html__( '&copy; Youxi Themes. 2012-2014. All Rights Reserved.', 'helium' )
		));

		/* Header Controls */

		$priority = 0;

		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[logo_height]', array(
				'label' => esc_html__( 'Max Logo Height', 'helium' ), 
				'section' => $prefix . '_header', 
				'min' => 0, 
				'max' => 640, 
				'step' => 1, 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[show_search]', array(
				'label' => esc_html__( 'Show Search', 'helium' ), 
				'section' => $prefix . '_header', 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( $prefix . '[copyright_text]', array(
			'label' => esc_html__( 'Copyright Text', 'helium' ), 
			'section' => $prefix . '_header', 
			'type' => 'text', 
			'priority' => $priority++
		));
	}

	public function ajax_customizer( $wp_customize ) {

		$prefix = $this->prefix();

		/* Section: AJAX */

		$wp_customize->add_section( $prefix . '_ajax_navigation', array(
			'title' =>  esc_html__( 'AJAX Navigation', 'helium' ), 
			'priority' => 51
		));

		/* AJAX Settings */

		$wp_customize->add_setting( $prefix . '[ajax_navigation]', array(
			'default' => true, 
			'sanitize_callback' => 'wp_validate_boolean'
		));
		$wp_customize->add_setting( $prefix . '[ajax_navigation_scroll_top]', array(
			'default' => true, 
			'sanitize_callback' => 'wp_validate_boolean'
		));
		$wp_customize->add_setting( $prefix . '[ajax_navigation_loading_text]', array(
			'default' => esc_html__( 'Loading', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[ajax_exclude_urls]', array(
			'default' => '', 
			'sanitize_callback' => 'sanitize_textarea'
		));


		/* AJAX Controls */

		$priority = 0;

		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[ajax_navigation]', array(
				'label' => esc_html__( 'Enabled', 'helium' ), 
				'section' => $prefix . '_ajax_navigation', 
				'priority' => ++$priority
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[ajax_navigation_scroll_top]', array(
				'label' => esc_html__( 'Scroll to Top Before Navigation', 'helium' ), 
				'section' => $prefix . '_ajax_navigation', 
				'priority' => ++$priority
			)
		));
		$wp_customize->add_control( $prefix . '[ajax_navigation_loading_text]', array(
			'label' => esc_html__( 'Loading Text', 'helium' ), 
			'section' => $prefix . '_ajax_navigation', 
			'type' => 'text', 
			'priority' => $priority++
		));
		$wp_customize->add_control( $prefix . '[ajax_exclude_urls]', array(
			'label' => esc_html__( 'Excluded URLs', 'helium' ), 
			'description' => esc_html__( 'Type here the URLs to exclude from AJAX requests', 'helium' ), 
			'section' => $prefix . '_ajax_navigation', 
			'type' => 'textarea', 
			'priority' => ++$priority
		));
	}

	public function typography_customizer( $wp_customize ) {

		$prefix = $this->prefix();

		/* Section: Typography */

		$wp_customize->add_section( $prefix . '_typography', array(
			'title' => esc_html__( 'Typography', 'helium' ), 
			'priority' => 61
		));

		/* Typography Settings */

		$wp_customize->add_setting( $prefix . '[body_font]', array(
			'default' => '', 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[headings_1234_font]', array(
			'default' => '', 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[headings_56_font]', array(
			'default' => '', 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[menu_font]', array(
			'default' => '', 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[blockquote_font]', array(
			'default' => '', 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[gridlist_filter_font]', array(
			'default' => '', 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[gridlist_title_font]', array(
			'default' => '', 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[gridlist_subtitle_font]', array(
			'default' => '', 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[content_title_font]', array(
			'default' => '', 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[content_nav_font]', array(
			'default' => '', 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[widget_title_font]', array(
			'default' => '', 
			'sanitize_callback' => 'sanitize_text_field'
		));

		/* Typography Controls */

		$priority = 0;

		$wp_customize->add_control( new Youxi_Customize_WebFont_Control(
			$wp_customize, $prefix . '[body_font]', array(
				'label' => esc_html__( 'Body Font', 'helium' ), 
				'section' => $prefix . '_typography', 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( new Youxi_Customize_WebFont_Control(
			$wp_customize, $prefix . '[headings_1234_font]', array(
				'label' => esc_html__( 'H1, H2, H3, H4 Font', 'helium' ), 
				'section' => $prefix . '_typography', 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( new Youxi_Customize_WebFont_Control(
			$wp_customize, $prefix . '[headings_56_font]', array(
				'label' => esc_html__( 'H5, H6 Font', 'helium' ), 
				'section' => $prefix . '_typography', 
				'priority' => $priority++
			)
		));		
		$wp_customize->add_control( new Youxi_Customize_WebFont_Control(
			$wp_customize, $prefix . '[menu_font]', array(
				'label' => esc_html__( 'Menu Font', 'helium' ), 
				'section' => $prefix . '_typography', 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( new Youxi_Customize_WebFont_Control(
			$wp_customize, $prefix . '[blockquote_font]', array(
				'label' => esc_html__( 'Blockquote Font', 'helium' ), 
				'section' => $prefix . '_typography', 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( new Youxi_Customize_WebFont_Control(
			$wp_customize, $prefix . '[gridlist_filter_font]', array(
				'label' => esc_html__( 'Gridlist Filter Font', 'helium' ), 
				'section' => $prefix . '_typography', 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( new Youxi_Customize_WebFont_Control(
			$wp_customize, $prefix . '[gridlist_title_font]', array(
				'label' => esc_html__( 'Gridlist Title Font', 'helium' ), 
				'section' => $prefix . '_typography', 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( new Youxi_Customize_WebFont_Control(
			$wp_customize, $prefix . '[gridlist_subtitle_font]', array(
				'label' => esc_html__( 'Gridlist Subtitle Font', 'helium' ), 
				'section' => $prefix . '_typography', 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( new Youxi_Customize_WebFont_Control(
			$wp_customize, $prefix . '[content_title_font]', array(
				'label' => esc_html__( 'Content Title Font', 'helium' ), 
				'section' => $prefix . '_typography', 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( new Youxi_Customize_WebFont_Control(
			$wp_customize, $prefix . '[content_nav_font]', array(
				'label' => esc_html__( 'Content Navigation Font', 'helium' ), 
				'section' => $prefix . '_typography', 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( new Youxi_Customize_WebFont_Control(
			$wp_customize, $prefix . '[widget_title_font]', array(
				'label' => esc_html__( 'Widget Title Font', 'helium' ), 
				'section' => $prefix . '_typography', 
				'priority' => $priority++
			)
		));
	}

	public function blog_customizer( $wp_customize ) {

		$prefix = $this->prefix();

		/* Panel: Blog */

		if( method_exists( $wp_customize, 'add_panel' ) ) {
			$section_priority = 0;
			$section_title_prefix = '';
			$wp_customize->add_panel( $prefix . '_blog', array(
				'title' => esc_html__( 'Blog', 'helium' ), 
				'priority' => 71
			));
		} else {
			$section_priority = 71;
			$section_title_prefix = esc_html__( 'Blog', 'helium' ) . ' ';
		}

		/* Section: Entries */

		$wp_customize->add_section( $prefix . '_blog_entries', array(
			'title' => $section_title_prefix . esc_html__( 'Entries', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_blog'
		));

		/* Entries Settings */

		$wp_customize->add_setting( $prefix . '[hidden_post_meta]', array(
			'default' => array(), 
			'sanitize_callback' => 'helium_customizer_sanitize_post_meta'
		));

		/* Entries Controls */

		$priority = 0;

		$wp_customize->add_control( new Youxi_Customize_Multicheck_Control(
			$wp_customize, $prefix . '[hidden_post_meta]', array(
				'label' => esc_html__( 'Hide Post Meta', 'helium' ), 
				'section' => $prefix . '_blog_entries', 
				'choices' => array(
					'author' => esc_html__( 'Author', 'helium' ), 
					'category' => esc_html__( 'Category', 'helium' ), 
					'tags' => esc_html__( 'Tags', 'helium' ), 
					'comments' => esc_html__( 'Comments', 'helium' ), 
					'permalink' => esc_html__( 'Permalink', 'helium' )
				), 
				'priority' => $priority++
			)
		));

		/* Section: Posts */

		$wp_customize->add_section( $prefix . '_blog_posts', array(
			'title' => $section_title_prefix . esc_html__( 'Posts', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_blog'
		));

		/* Posts Settings */

		$wp_customize->add_setting( $prefix . '[blog_show_tags]', array(
			'default' => true, 
			'sanitize_callback' => 'wp_validate_boolean'
		));
		$wp_customize->add_setting( $prefix . '[blog_sharing]', array(
			'default' => true, 
			'sanitize_callback' => 'wp_validate_boolean'
		));
		$wp_customize->add_setting( $prefix . '[blog_show_author]', array(
			'default' => true, 
			'sanitize_callback' => 'wp_validate_boolean'
		));
		$wp_customize->add_setting( $prefix . '[blog_related_posts]', array(
			'default' => true, 
			'sanitize_callback' => 'wp_validate_boolean'
		));
		$wp_customize->add_setting( $prefix . '[blog_related_posts_count]', array(
			'default' => 3, 
			'sanitize_callback' => 'absint'
		));
		$wp_customize->add_setting( $prefix . '[blog_related_posts_behavior]', array(
			'default' => 'lightbox', 
			'sanitize_callback' => 'helium_customizer_sanitize_related_behavior'
		));

		/* Posts Controls */

		$priority = 0;

		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[blog_show_tags]', array(
				'label' => esc_html__( 'Show Tags', 'helium' ), 
				'section' => $prefix . '_blog_posts', 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[blog_sharing]', array(
				'label' => esc_html__( 'Show Sharing Buttons', 'helium' ), 
				'section' => $prefix . '_blog_posts', 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[blog_show_author]', array(
				'label' => esc_html__( 'Show Author', 'helium' ), 
				'section' => $prefix . '_blog_posts', 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[blog_related_posts]', array(
				'label' => esc_html__( 'Show Related Posts', 'helium' ), 
				'section' => $prefix . '_blog_posts', 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[blog_related_posts_count]', array(
				'label' => esc_html__( 'Related Posts Count', 'helium' ), 
				'section' => $prefix . '_blog_posts', 
				'min' => 3, 
				'max' => 4, 
				'step' => 1, 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( $prefix . '[blog_related_posts_behavior]', array(
			'label' => esc_html__( 'Related Posts Behavior', 'helium' ), 
			'section' => $prefix . '_blog_posts', 
			'type' => 'select', 
			'choices' => array(
				'lightbox' => esc_html__( 'Show Lightbox', 'helium' ), 
				'permalink' => esc_html__( 'Go to Post', 'helium' )
			), 
			'priority' => $priority++
		));

		/* Section: Summary */

		$wp_customize->add_section( $prefix . '_blog_summary', array(
			'title' => $section_title_prefix . esc_html__( 'Summary', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_blog'
		));

		/* Summary Settings */

		$wp_customize->add_setting( $prefix . '[blog_summary]', array(
			'default' => 'the_excerpt', 
			'sanitize_callback' => 'helium_customizer_sanitize_blog_summary'
		));
		$wp_customize->add_setting( $prefix . '[blog_excerpt_length]', array(
			'default' => 100, 
			'sanitize_callback' => 'absint'
		));

		/* Summary Controls */

		$priority = 0;

		$wp_customize->add_control( $prefix . '[blog_summary]', array(
			'label' => esc_html__( 'Summary Display', 'helium' ), 
			'section' => $prefix . '_blog_summary', 
			'type' => 'radio', 
			'choices' => array(
				'the_excerpt' => esc_html__( 'Excerpt', 'helium' ), 
				'the_content' => esc_html__( 'More Tag', 'helium' ), 
			), 
			'priority' => $priority++
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[blog_excerpt_length]', array(
				'label' => esc_html__( 'Excerpt Length', 'helium' ), 
				'section' => $prefix . '_blog_summary', 
				'min' => 55, 
				'max' => 250, 
				'step' => 1, 
				'priority' => $priority++
			)
		));

		/* Section: Layout */

		$wp_customize->add_section( $prefix . '_blog_layout', array(
			'title' => $section_title_prefix . esc_html__( 'Layout', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_blog'
		));

		/* Layout Settings */

		$wp_customize->add_setting( $prefix . '[blog_index_layout]', array(
			'default' => 'boxed', 
			'sanitize_callback' => 'helium_customizer_sanitize_blog_layout'
		));
		$wp_customize->add_setting( $prefix . '[blog_archive_layout]', array(
			'default' => 'boxed', 
			'sanitize_callback' => 'helium_customizer_sanitize_blog_layout'
		));
		$wp_customize->add_setting( $prefix . '[blog_single_layout]', array(
			'default' => 'boxed', 
			'sanitize_callback' => 'helium_customizer_sanitize_blog_layout'
		));

		/* Layout Controls */

		$priority = 0;

		$wp_customize->add_control( $prefix . '[blog_index_layout]', array(
			'label' => esc_html__( 'Index', 'helium' ), 
			'section' => $prefix . '_blog_layout', 
			'type' => 'select', 
			'choices' => array(
				'boxed' => esc_html__( 'Boxed', 'helium' ), 
				'fullwidth' => esc_html__( 'Fullwidth', 'helium' ), 
			), 
			'priority' => $priority++
		));
		$wp_customize->add_control( $prefix . '[blog_archive_layout]', array(
			'label' => esc_html__( 'Archive', 'helium' ), 
			'section' => $prefix . '_blog_layout', 
			'type' => 'select', 
			'choices' => array(
				'boxed' => esc_html__( 'Boxed', 'helium' ), 
				'fullwidth' => esc_html__( 'Fullwidth', 'helium' ), 
			), 
			'priority' => $priority++
		));
		$wp_customize->add_control( $prefix . '[blog_single_layout]', array(
			'label' => esc_html__( 'Single', 'helium' ), 
			'section' => $prefix . '_blog_layout', 
			'type' => 'select', 
			'choices' => array(
				'boxed' => esc_html__( 'Boxed', 'helium' ), 
				'fullwidth' => esc_html__( 'Fullwidth', 'helium' ), 
			), 
			'priority' => $priority++
		));


		/* Section: Titles */

		$wp_customize->add_section( $prefix . '_blog_titles', array(
			'title' => $section_title_prefix . esc_html__( 'Titles', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_blog'
		));

		/* Titles Settings */

		$wp_customize->add_setting( $prefix . '[blog_index_title]', array(
			'sanitize_callback' => 'wp_kses_post', 
			'default' => esc_html__( 'Welcome to Our Blog', 'helium' )
		));
		$wp_customize->add_setting( $prefix . '[blog_single_title]', array(
			'sanitize_callback' => 'wp_kses_post', 
			'default' => esc_html__( 'Currently Reading', 'helium' )
		));
		$wp_customize->add_setting( $prefix . '[blog_category_title]', array(
			'sanitize_callback' => 'wp_kses_post', 
			'default' => esc_html__( 'Category: {category}', 'helium' )
		));
		$wp_customize->add_setting( $prefix . '[blog_tag_title]', array(
			'sanitize_callback' => 'wp_kses_post', 
			'default' => esc_html__( 'Posts Tagged &lsquo;{tag}&rsquo;', 'helium' )
		));
		$wp_customize->add_setting( $prefix . '[blog_author_title]', array(
			'sanitize_callback' => 'wp_kses_post', 
			'default' => esc_html__( 'Posts by {author}', 'helium' )
		));
		$wp_customize->add_setting( $prefix . '[blog_date_title]', array(
			'sanitize_callback' => 'wp_kses_post', 
			'default' => esc_html__( 'Archive for {date}', 'helium' )
		));

		/* Titles Controls */

		$priority = 0;

		$wp_customize->add_control( $prefix . '[blog_index_title]', array(
			'label' => esc_html__( 'Index', 'helium' ), 
			'section' => $prefix . '_blog_titles', 
			'type' => 'text', 
			'priority' => $priority++
		));
		$wp_customize->add_control( $prefix . '[blog_single_title]', array(
			'label' => esc_html__( 'Single', 'helium' ), 
			'section' => $prefix . '_blog_titles', 
			'type' => 'text', 
			'description' => wp_kses( __( 'Use <strong>{title}</strong> for the post title.', 'helium' ), array( 'strong' => array() ) ), 
			'priority' => $priority++
		));
		$wp_customize->add_control( $prefix . '[blog_category_title]', array(
			'label' => esc_html__( 'Category Archive', 'helium' ), 
			'section' => $prefix . '_blog_titles', 
			'type' => 'text', 
			'description' => wp_kses( __( 'Use <strong>{category}</strong> for the category name.', 'helium' ), array( 'strong' => array() ) ), 
			'priority' => $priority++
		));
		$wp_customize->add_control( $prefix . '[blog_tag_title]', array(
			'label' => esc_html__( 'Tag Archive', 'helium' ), 
			'section' => $prefix . '_blog_titles', 
			'type' => 'text', 
			'description' => wp_kses( __( 'Use <strong>{tag}</strong> for the tag name.', 'helium' ), array( 'strong' => array() ) ), 
			'priority' => $priority++
		));
		$wp_customize->add_control( $prefix . '[blog_author_title]', array(
			'label' => esc_html__( 'Author Archive', 'helium' ), 
			'section' => $prefix . '_blog_titles', 
			'type' => 'text', 
			'description' => wp_kses( __( 'Use <strong>{author}</strong> for the author name.', 'helium' ), array( 'strong' => array() ) ), 
			'priority' => $priority++
		));
		$wp_customize->add_control( $prefix . '[blog_date_title]', array(
			'label' => esc_html__( 'Date Archive', 'helium' ), 
			'section' => $prefix . '_blog_titles', 
			'type' => 'text', 
			'description' => wp_kses( __( 'Use <strong>{date}</strong> for the date.', 'helium' ), array( 'strong' => array() ) ), 
			'priority' => $priority++
		));
	}

	public function portfolio_customizer( $wp_customize ) {

		$prefix = $this->prefix();

		if( method_exists( $wp_customize, 'add_panel' ) ) {
			$section_priority  = 0;
			$section_title_prefix = '';
			$wp_customize->add_panel( $prefix . '_portfolio', array(
				'title' => esc_html__( 'Portfolio', 'helium' ), 
				'priority' => 81
			));
		} else {
			$section_priority = 81;
			$section_title_prefix = esc_html__( 'Portfolio', 'helium' ) . ' ';
		}

		/* Section: Single */

		$wp_customize->add_section( $prefix . '_portfolio_single', array(
			'title' => $section_title_prefix . esc_html__( 'Single Item', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_portfolio'
		));

		/* Single Settings */

		$wp_customize->add_setting( $prefix . '[portfolio_show_related_items]', array(
			'default' => true, 
			'sanitize_callback' => 'wp_validate_boolean'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_related_items_count]', array(
			'default' => 3, 
			'sanitize_callback' => 'absint'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_related_items_behavior]', array(
			'default' => 'lightbox', 
			'sanitize_callback' => 'helium_customizer_sanitize_related_behavior'
		));

		/* Single Controls */

		$priority = 0;

		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[portfolio_show_related_items]', array(
				'label' => esc_html__( 'Show Related Items', 'helium' ), 
				'section' => $prefix . '_portfolio_single', 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[portfolio_related_items_count]', array(
				'label' => esc_html__( 'Related Items Count', 'helium' ), 
				'section' => $prefix . '_portfolio_single', 
				'min' => 3, 
				'max' => 4, 
				'step' => 1, 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( $prefix . '[portfolio_related_items_behavior]', array(
			'label' => esc_html__( 'Related Items Behavior', 'helium' ), 
			'section' => $prefix . '_portfolio_single', 
			'type' => 'select', 
			'choices' => array(
				'lightbox' => esc_html__( 'Show Lightbox', 'helium' ), 
				'permalink' => esc_html__( 'Go to Post', 'helium' )
			), 
			'priority' => $priority++
		));

		/* Section: Archive */

		$wp_customize->add_section( $prefix . '_portfolio_archive', array(
			'title' => $section_title_prefix . esc_html__( 'Archive', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_portfolio'
		));

		/* Archive Settings */

		$wp_customize->add_setting( $prefix . '[portfolio_archive_page_title]', array(
			'default' => esc_html__( 'Portfolio Archive', 'helium' ), 
			'sanitize_callback' => 'wp_kses_post'
		));

		/* Archive Controls */

		$priority = 0;

		$wp_customize->add_control( $prefix . '[portfolio_archive_page_title]', array(
			'label' => esc_html__( 'Page Title', 'helium' ), 
			'section' => $prefix . '_portfolio_archive', 
			'type' => 'text', 
			'priority' => $priority++
		));

		/* Section: Grid */

		$wp_customize->add_section( $prefix . '_portfolio_grid', array(
			'title' => $section_title_prefix . esc_html__( 'Grid Settings', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_portfolio'
		));

		/* Grid Settings */

		$wp_customize->add_setting( $prefix . '[portfolio_grid_show_filter]', array(
			'default' => true, 
			'sanitize_callback' => 'wp_validate_boolean'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_pagination]', array(
			'default' => 'ajax', 
			'sanitize_callback' => 'helium_customizer_sanitize_pagination'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_ajax_button_text]', array(
			'default' => esc_html__( 'Load More', 'helium' ), 
			'sanitize_callback' => 'wp_kses_post'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_ajax_button_complete_text]', array(
			'default' => esc_html__( 'No More Items', 'helium' ), 
			'sanitize_callback' => 'wp_kses_post'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_posts_per_page]', array(
			'default' => get_option( 'posts_per_page' ), 
			'sanitize_callback' => 'absint'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_include]', array(
			'default' => array(), 
			'sanitize_callback' => 'helium_customizer_sanitize_portfolio_categories'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_behavior]', array(
			'default' => 'lightbox', 
			'sanitize_callback' => 'helium_customizer_sanitize_grid_behavior'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_meta_text]', array(
			'default' => 'taxonomy', 
			'sanitize_callback' => 'helium_customizer_sanitize_grid_meta_text'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_orderby]', array(
			'default' => 'date', 
			'sanitize_callback' => 'helium_customizer_sanitize_grid_orderby'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_order]', array(
			'default' => 'DESC', 
			'sanitize_callback' => 'helium_customizer_sanitize_grid_order'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_layout]', array(
			'default' => 'masonry', 
			'sanitize_callback' => 'helium_customizer_sanitize_portfolio_layout'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_columns]', array(
			'default' => 4, 
			'sanitize_callback' => 'absint'
		));

		/* Grid Controls */

		$priority = 0;

		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[portfolio_grid_show_filter]', array(
				'label' => esc_html__( 'Show Filter', 'helium' ), 
				'section' => $prefix . '_portfolio_grid', 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( $prefix . '[portfolio_grid_pagination]', array(
			'label' => esc_html__( 'Pagination', 'helium' ), 
			'section' => $prefix . '_portfolio_grid', 
			'type' => 'select', 
			'choices' => array(
				'ajax' => esc_html__( 'AJAX', 'helium' ), 
				'infinite' => esc_html__( 'Infinite', 'helium' ), 
				'numbered' => esc_html__( 'Numbered', 'helium' ), 
				'prev_next' => esc_html__( 'Prev/Next', 'helium' ), 
				'show_all' => esc_html__( 'None (Show all)', 'helium' )
			), 
			'priority' => $priority++
		));
		$wp_customize->add_control( $prefix . '[portfolio_grid_ajax_button_text]', array(
			'label' => esc_html__( 'AJAX Button Text', 'helium' ), 
			'section' => $prefix . '_portfolio_grid', 
			'type' => 'text', 
			'priority' => $priority++
		));
		$wp_customize->add_control( $prefix . '[portfolio_grid_ajax_button_complete_text]', array(
			'label' => esc_html__( 'AJAX Button Complete Text', 'helium' ), 
			'section' => $prefix . '_portfolio_grid', 
			'type' => 'text', 
			'priority' => $priority++
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[portfolio_grid_posts_per_page]', array(
				'label' => esc_html__( 'Items per Page', 'helium' ), 
				'section' => $prefix . '_portfolio_grid', 
				'min' => 1, 
				'max' => 20, 
				'step' => 1, 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Multicheck_Control(
			$wp_customize, $prefix . '[portfolio_grid_include]', array(
				'label' => esc_html__( 'Included Categories', 'helium' ), 
				'section' => $prefix . '_portfolio_grid', 
				'choices' => get_terms( Youxi_Portfolio::taxonomy_name(), array( 'fields' => 'id=>name', 'hide_empty' => false ) ), 
				'description' => esc_html__( 'Uncheck all to include all categories.', 'helium' ), 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( $prefix . '[portfolio_grid_behavior]', array(
			'label' => esc_html__( 'Behavior', 'helium' ), 
			'section' => $prefix . '_portfolio_grid', 
			'type' => 'select', 
			'choices' => array(
				'none' => esc_html__( 'None', 'helium' ), 
				'lightbox' => esc_html__( 'Show Image in Lightbox', 'helium' ), 
				'page' => esc_html__( 'Go to Detail Page', 'helium' )
			), 
			'priority' => $priority++
		));
		$wp_customize->add_control( $prefix . '[portfolio_grid_meta_text]', array(
			'label' => esc_html__( 'Meta Text', 'helium' ), 
			'section' => $prefix . '_portfolio_grid', 
			'type' => 'select', 
			'choices' => array(
				'taxonomy' => esc_html__( 'Taxonomy', 'helium' ), 
				'excerpt'  => esc_html__( 'Excerpt', 'helium' )
			), 
			'priority' => $priority++
		));
		$wp_customize->add_control( $prefix . '[portfolio_grid_orderby]', array(
			'label' => esc_html__( 'Order By', 'helium' ), 
			'section' => $prefix . '_portfolio_grid', 
			'type' => 'select', 
			'choices' => array(
				'date' => esc_html__( 'Date', 'helium' ), 
				'menu_order' => esc_html__( 'Menu Order', 'helium' ), 
				'title' => esc_html__( 'Title', 'helium' ), 
				'ID' => esc_html__( 'ID', 'helium' )
			), 
			'priority' => $priority++
		));
		$wp_customize->add_control( $prefix . '[portfolio_grid_order]', array(
			'label' => esc_html__( 'Order', 'helium' ), 
			'section' => $prefix . '_portfolio_grid', 
			'type' => 'select', 
			'choices' => array(
				'DESC' => esc_html__( 'Descending', 'helium' ), 
				'ASC' => esc_html__( 'Ascending', 'helium' )
			), 
			'priority' => $priority++
		));
		$wp_customize->add_control( $prefix . '[portfolio_grid_layout]', array(
			'label' => esc_html__( 'Layout', 'helium' ), 
			'section' => $prefix . '_portfolio_grid', 
			'type' => 'select', 
			'choices' => array(
				'masonry' => esc_html__( 'Masonry', 'helium' ), 
				'classic' => esc_html__( 'Classic', 'helium' ), 
				'justified' => esc_html__( 'Justified', 'helium' )
			), 
			'priority' => $priority++
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[portfolio_grid_columns]', array(
				'label' => esc_html__( 'Columns (Masonry/Classic)', 'helium' ), 
				'section' => $prefix . '_portfolio_grid', 
				'min' => 3, 
				'max' => 5, 
				'step' => 1, 
				'priority' => $priority++
			)
		));
	}

	public function edd_customizer( $wp_customize ) {

		$prefix = $this->prefix();

		if( method_exists( $wp_customize, 'add_panel' ) ) {
			$section_priority = 0;
			$section_title_prefix = '';
			$wp_customize->add_panel( $prefix . '_edd', array(
				'title' => esc_html__( 'Easy Digital Downloads', 'helium' ), 
				'priority' => 91
			));
		} else {
			$section_priority = 91;
			$section_title_prefix = esc_html__( 'EDD', 'helium' ) . ' ';
		}

		/* Section: General */

		$wp_customize->add_section( $prefix . '_edd_general', array(
			'title' => $section_title_prefix . esc_html__( 'General', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_edd'
		));

		/* General Settings */

		$wp_customize->add_setting( $prefix . '[edd_show_cart]', array(
			'default' => true, 
			'sanitize_callback' => 'wp_validate_boolean'
		));

		/* General Controls */

		$priority = 0;

		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[edd_show_cart]', array(
				'label' => esc_html__( 'Show Cart in Header', 'helium' ), 
				'section' => $prefix . '_edd_general', 
				'priority' => $priority++
			)
		));

		/* Section: Single */

		$wp_customize->add_section( $prefix . '_edd_single', array(
			'title' => $section_title_prefix . esc_html__( 'Single Downloads', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_edd'
		));

		/* Single Settings */
		
		$wp_customize->add_setting( $prefix . '[edd_show_categories]', array(
			'default' => true, 
			'sanitize_callback' => 'wp_validate_boolean'
		));
		$wp_customize->add_setting( $prefix . '[edd_show_tags]', array(
			'default' => true, 
			'sanitize_callback' => 'wp_validate_boolean'
		));
		$wp_customize->add_setting( $prefix . '[edd_show_sharing_buttons]', array(
			'default' => true, 
			'sanitize_callback' => 'wp_validate_boolean'
		));
		$wp_customize->add_setting( $prefix . '[edd_show_related_items]', array(
			'default' => true, 
			'sanitize_callback' => 'wp_validate_boolean'
		));
		$wp_customize->add_setting( $prefix . '[edd_related_items_count]', array(
			'default' => 3, 
			'sanitize_callback' => 'absint'
		));
		$wp_customize->add_setting( $prefix . '[edd_related_items_behavior]', array(
			'default' => 'lightbox', 
			'sanitize_callback' => 'helium_customizer_sanitize_related_behavior'
		));
		
		/* Single Controls */

		$priority = 0;

		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[edd_show_categories]', array(
				'label' => esc_html__( 'Show Categories', 'helium' ), 
				'section' => $prefix . '_edd_single', 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[edd_show_tags]', array(
				'label' => esc_html__( 'Show Tags', 'helium' ), 
				'section' => $prefix . '_edd_single', 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[edd_show_sharing_buttons]', array(
				'label' => esc_html__( 'Show Sharing Buttons', 'helium' ), 
				'section' => $prefix . '_edd_single', 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[edd_show_related_items]', array(
				'label' => esc_html__( 'Show Related Items', 'helium' ), 
				'section' => $prefix . '_edd_single', 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[edd_related_items_count]', array(
				'label' => esc_html__( 'Related Items Count', 'helium' ), 
				'section' => $prefix . '_edd_single', 
				'min' => 3, 
				'max' => 4, 
				'step' => 1, 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( $prefix . '[edd_related_items_behavior]', array(
			'label' => esc_html__( 'Related Items Behavior', 'helium' ), 
			'section' => $prefix . '_edd_single', 
			'type' => 'select', 
			'choices' => array(
				'lightbox' => esc_html__( 'Show Lightbox', 'helium' ), 
				'permalink' => esc_html__( 'Go to Post', 'helium' )
			), 
			'priority' => $priority++
		));

		/* Section: Archive */

		$wp_customize->add_section( $prefix . '_edd_archive', array(
			'title' => $section_title_prefix . esc_html__( 'Archive', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_edd'
		));

		/* Archive Settings */

		$wp_customize->add_setting( $prefix . '[edd_archive_page_title]', array(
			'default' => esc_html__( 'Downloads Archive', 'helium' ), 
			'sanitize_callback' => 'wp_kses_post'
		));

		/* Archive Controls */

		$priority = 0;

		$wp_customize->add_control( $prefix . '[edd_archive_page_title]', array(
			'label' => esc_html__( 'Page Title', 'helium' ), 
			'section' => $prefix . '_edd_archive', 
			'type' => 'text', 
			'priority' => $priority++
		));

		/* Section: Grid */

		$wp_customize->add_section( $prefix . '_edd_grid', array(
			'title' => $section_title_prefix . esc_html__( 'Grid Settings', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_edd'
		));

		/* Grid Settings */

		$wp_customize->add_setting( $prefix . '[edd_grid_pagination]', array(
			'default' => 'ajax', 
			'sanitize_callback' => 'helium_customizer_sanitize_pagination'
		));
		$wp_customize->add_setting( $prefix . '[edd_grid_ajax_button_text]', array(
			'default' => esc_html__( 'Load More', 'helium' ), 
			'sanitize_callback' => 'wp_kses_post'
		));
		$wp_customize->add_setting( $prefix . '[edd_grid_ajax_button_complete_text]', array(
			'default' => esc_html__( 'No More Items', 'helium' ), 
			'sanitize_callback' => 'wp_kses_post'
		));
		$wp_customize->add_setting( $prefix . '[edd_grid_posts_per_page]', array(
			'default' => get_option( 'posts_per_page' ), 
			'sanitize_callback' => 'absint'
		));
		$wp_customize->add_setting( $prefix . '[edd_grid_include]', array(
			'default' => array(), 
			'sanitize_callback' => 'helium_customizer_sanitize_edd_categories'
		));
		$wp_customize->add_setting( $prefix . '[edd_grid_behavior]', array(
			'default' => 'lightbox', 
			'sanitize_callback' => 'helium_customizer_sanitize_grid_behavior'
		));
		$wp_customize->add_setting( $prefix . '[edd_grid_columns]', array(
			'default' => 4, 
			'sanitize_callback' => 'absint'
		));

		/* Archive Controls */

		$priority = 0;

		$wp_customize->add_control( $prefix . '[edd_grid_pagination]', array(
			'label' => esc_html__( 'Pagination', 'helium' ), 
			'section' => $prefix . '_edd_grid', 
			'type' => 'select', 
			'choices' => array(
				'ajax' => esc_html__( 'AJAX', 'helium' ), 
				'infinite' => esc_html__( 'Infinite', 'helium' ), 
				'numbered' => esc_html__( 'Numbered', 'helium' ), 
				'prev_next' => esc_html__( 'Prev/Next', 'helium' ), 
				'show_all' => esc_html__( 'None (Show all)', 'helium' )
			), 
			'priority' => $priority++
		));
		$wp_customize->add_control( $prefix . '[edd_grid_ajax_button_text]', array(
			'label' => esc_html__( 'AJAX Button Text', 'helium' ), 
			'section' => $prefix . '_edd_grid', 
			'type' => 'text', 
			'priority' => $priority++
		));
		$wp_customize->add_control( $prefix . '[edd_grid_ajax_button_complete_text]', array(
			'label' => esc_html__( 'AJAX Button Complete Text', 'helium' ), 
			'section' => $prefix . '_edd_grid', 
			'type' => 'text', 
			'priority' => $priority++
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[edd_grid_posts_per_page]', array(
				'label' => esc_html__( 'Items per Page', 'helium' ), 
				'section' => $prefix . '_edd_grid', 
				'min' => 1, 
				'max' => 20, 
				'step' => 1, 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Multicheck_Control(
			$wp_customize, $prefix . '[edd_grid_include]', array(
				'label' => esc_html__( 'Included Categories', 'helium' ), 
				'section' => $prefix . '_edd_grid', 
				'choices' => get_terms( 'download_category', array( 'fields' => 'id=>name', 'hide_empty' => false ) ), 
				'description' => esc_html__( 'Uncheck all to include all categories.', 'helium' ), 
				'priority' => $priority++
			)
		));
		$wp_customize->add_control( $prefix . '[edd_grid_behavior]', array(
			'label' => esc_html__( 'Behavior', 'helium' ), 
			'section' => $prefix . '_edd_grid', 
			'type' => 'select', 
			'choices' => array(
				'none' => esc_html__( 'None', 'helium' ), 
				'lightbox' => esc_html__( 'Show Image in Lightbox', 'helium' ), 
				'page' => esc_html__( 'Go to Detail Page', 'helium' )
			), 
			'priority' => $priority++
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[edd_grid_columns]', array(
				'label' => esc_html__( 'Number of Columns', 'helium' ), 
				'section' => $prefix . '_edd_grid', 
				'min' => 3, 
				'max' => 5, 
				'step' => 1, 
				'priority' => $priority++
			)
		));

		// foreach( $wp_customize->settings() as $setting ) {
		// 	if( preg_match( '/^helium_settings\[(.+)\]$/', $setting->id, $matches ) ) {
		// 		if( is_array( $setting->default ) ) {
		// 			printf( "'%s' => %s, \n", $matches[1], print_r( $setting->default, true ) );
		// 		} elseif( is_bool( $setting->default ) ) {
		// 			printf( "'%s' => %s, \n", $matches[1], wp_validate_boolean( $setting->default ) ? 'true' : 'false' );
		// 		} elseif( is_string( $setting->default ) ) {
		// 			printf( "'%s' => '%s', \n", $matches[1], $setting->default );
		// 		} else {
		// 			printf( "'%s' => %s, \n", $matches[1], $setting->default );
		// 		}
		// 	}
		// }
	}
}
new Helium_Customize_Manager();
