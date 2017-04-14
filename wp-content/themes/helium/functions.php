<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

/* ==========================================================================
	Setup Global Vars
============================================================================= */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if( ! isset( $content_width ) ) {
	$content_width = 940;
}

/* ==========================================================================
	Option Tree Setup
============================================================================= */

/**
 * Optional: set 'ot_show_pages' filter to false.
 * This will hide the settings & documentation pages.
 */
add_filter( 'ot_show_pages', defined( 'WP_DEBUG' ) && WP_DEBUG ? '__return_true' : '__return_false' );

/**
 * Optional: set 'ot_show_new_layout' filter to false.
 * This will hide the "New Layout" section on the Theme Options page.
 */
add_filter( 'ot_show_new_layout', '__return_false' );

/**
 * Optional: set 'ot_theme_options_parent_slug' filter to null.
 * This will move the Theme Options menu to the top level menu
 */
add_filter( 'ot_theme_options_parent_slug', '__return_null' );

/**
 * This will determine the Theme Options menu position
 */
add_filter( 'ot_theme_options_position', create_function( '', 'return 50;' ) );

/**
 * Optional: set 'ot_meta_boxes' filter to false.
 * This will disable the inclusion of OT_Meta_Box
 */
add_filter( 'ot_meta_boxes', '__return_false' );

/**
 * Required: set 'ot_theme_mode' filter to true.
 */
add_filter( 'ot_theme_mode', '__return_true' );

/**
 * Required: include OptionTree.
 */
require get_template_directory() . '/option-tree/ot-loader.php';

/**
 * Include OptionTree Theme Options.
 */
require get_template_directory() . '/theme-options.php';

/* ==========================================================================
	TGMPA Setup
============================================================================= */

if( ! class_exists( 'TGM_Plugin_Activation' ) ) {
	require get_template_directory() . '/lib/vendor/tgmpa/class-tgm-plugin-activation.php';
}

add_action( 'tgmpa_register', 'helium_register_required_plugins' );

/**
 * Register the required/recommended plugins.
 */
function helium_register_required_plugins() {

	$plugins = array(
		array(
			'name'     => esc_html__( 'Contact Form 7', 'helium' ), 
			'slug'     => 'contact-form-7',
			'required' => false
		), 
		array(
			'name'     => esc_html__( 'Envato Market', 'helium' ), 
			'slug'     => 'envato-market', 
			'source'   => 'envato-market.zip', 
			'required' => false, 
			'version'  => '1.0'
		), 
		array(
			'name'     => esc_html__( 'Easy Digital Downloads', 'helium' ), 
			'slug'     => 'easy-digital-downloads',
			'required' => false
		), 
		array(
			'name'     => esc_html__( 'Envato Market', 'helium' ), 
			'slug'     => 'envato-market', 
			'source'   => 'envato-market.zip', 
			'required' => false, 
			'version'  => '1.0'
		), 
		array(
			'name'     => esc_html__( 'Youxi Builder', 'helium' ), 
			'slug'     => 'youxi-builder', 
			'source'   => 'youxi-builder.zip', 
			'required' => false, 
			'version'  => '2.5'
		), 
		array(
			'name'     => esc_html__( 'Youxi Core', 'helium' ), 
			'slug'     => 'youxi-core', 
			'source'   => 'youxi-core.zip', 
			'required' => true, 
			'version'  => '1.7.5'
		), 
		array(
			'name'     => esc_html__( 'Youxi Importer', 'helium' ), 
			'slug'     => 'youxi-importer', 
			'source'   => 'youxi-importer.zip', 
			'required' => false, 
			'version'  => '2.0'
		), 
		array(
			'name'     => esc_html__( 'Youxi Portfolio', 'helium' ), 
			'slug'     => 'youxi-portfolio', 
			'source'   => 'youxi-portfolio.zip', 
			'required' => true, 
			'version'  => '1.4.2'
		), 
		array(
			'name'     => esc_html__( 'Youxi Post Format', 'helium' ), 
			'slug'     => 'youxi-post-format', 
			'source'   => 'youxi-post-format.zip', 
			'required' => false, 
			'version'  => '1.2'
		), 
		array(
			'name'     => esc_html__( 'Youxi Shortcode', 'helium' ), 
			'slug'     => 'youxi-shortcode', 
			'source'   => 'youxi-shortcode.zip', 
			'required' => false, 
			'version'  => '4.2.2'
		), 
		array(
			'name'     => esc_html__( 'Youxi Widgets', 'helium' ), 
			'slug'     => 'youxi-widgets', 
			'source'   => 'youxi-widgets.zip', 
			'required' => false, 
			'version'  => '2.0'
		)
	);

	tgmpa( $plugins, array(
		'id'           => 'helium', 
		'default_path' => trailingslashit( get_template_directory() . '/plugins' )
	) );
}

/* ==========================================================================
	Include Framework Classes
============================================================================= */

require get_template_directory() . '/lib/framework/core/class-core.php';

require get_template_directory() . '/lib/framework/font/class-font.php';

require get_template_directory() . '/lib/external-api/class-settings-page.php';

/* ==========================================================================
	Include Plugin Configurations
============================================================================= */

require get_template_directory() . '/plugins-config/config-contact-form-7.php';

require get_template_directory() . '/plugins-config/config-easy-digital-downloads.php';

require get_template_directory() . '/plugins-config/config-youxi-core.php';

require get_template_directory() . '/plugins-config/config-youxi-importer.php';

require get_template_directory() . '/plugins-config/config-youxi-page-builder.php';

require get_template_directory() . '/plugins-config/config-youxi-portfolio.php';

require get_template_directory() . '/plugins-config/config-youxi-post-format.php';

require get_template_directory() . '/plugins-config/config-youxi-shortcodes.php';

require get_template_directory() . '/plugins-config/config-youxi-widgets.php';

/* ==========================================================================
	Include Theme Functions
============================================================================= */

require get_template_directory() . '/includes/helium-addthis.php';

require get_template_directory() . '/includes/helium-ajax.php';

require get_template_directory() . '/includes/helium-comments.php';

require get_template_directory() . '/includes/helium-customizer-sanitize.php';

require get_template_directory() . '/includes/helium-customizer.php';

require get_template_directory() . '/includes/helium-edd.php';

require get_template_directory() . '/includes/helium-entries.php';

require get_template_directory() . '/includes/helium-filters.php';

require get_template_directory() . '/includes/helium-fonts.php';

require get_template_directory() . '/includes/helium-icons.php';

require get_template_directory() . '/includes/helium-layout.php';

require get_template_directory() . '/includes/helium-media.php';

require get_template_directory() . '/includes/helium-nav-menu.php';

require get_template_directory() . '/includes/helium-portfolio.php';

require get_template_directory() . '/includes/helium-post.php';

require get_template_directory() . '/includes/helium-theme-options.php';

require get_template_directory() . '/includes/helium-wp.php';

/* EOF */
