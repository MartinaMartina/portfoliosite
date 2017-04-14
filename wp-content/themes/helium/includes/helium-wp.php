<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

/* ==========================================================================
	Text Domain
============================================================================= */

if( ! function_exists( 'helium_load_theme_textdomain' ) ):

function helium_load_theme_textdomain() {
	load_theme_textdomain( 'helium', get_template_directory() . '/languages' );
}
endif;
add_action( 'after_setup_theme', 'helium_load_theme_textdomain' );

/* ==========================================================================
	Theme Support
============================================================================= */

if( ! function_exists( 'helium_add_theme_support' ) ):

function helium_add_theme_support() {

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array( 'image', 'video', 'audio', 'gallery' ) );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for custom logo.
	 */
	add_theme_support( 'custom-logo', array(
		'flex-width' => true, 
		'flex-height' => true
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

	// Add RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	// Enable support for Post Thumbnails
	add_theme_support( 'post-thumbnails' );

	// This theme uses its own gallery styles.
	add_filter( 'use_default_gallery_style', '__return_false' );
}
endif;
add_action( 'init', 'helium_add_theme_support' );

/* ==========================================================================
	Image Sizes
============================================================================= */

if( ! function_exists( 'helium_add_image_sizes' ) ):

function helium_add_image_sizes() {

	$image_sizes = apply_filters( 'helium_wp_image_sizes', array(
		'helium_square' => array(
			'width'  => 640, 
			'height' => 640, 
			'crop'   => true
		), 
		'helium_4by3' => array(
			'width'  => 400, 
			'height' => 300, 
			'crop'   => true
		), 
		'helium_16by9' => array(
			'width'  => 800, 
			'height' => 450, 
			'crop'   => true
		), 
		'helium_portfolio_thumb_4by3' => array(
			'width' => 720, 
			'height' => 540, 
			'crop' => true
		), 
		'helium_portfolio_thumb_square' => array(
			'width' => 720, 
			'height' => 720, 
			'crop' => true
		), 
		'helium_portfolio_thumb' => array(
			'width' => 720
		)
	));

	foreach( $image_sizes as $name => $size ) {

		/* Skip reserved names */
		if( preg_match( '/^((post-)?thumbnail|thumb|medium|large)$/', $name ) ) {
			continue;
		}

		$size = wp_parse_args( $size, array(
			'width'  => 0, 
			'height' => 0, 
			'crop'   => false
		));
		add_image_size( $name, $size['width'], $size['height'], $size['crop'] );
	}
}
endif;
add_action( 'init', 'helium_add_image_sizes' );

/* ==========================================================================
	Widgets
============================================================================= */

if( ! function_exists( 'helium_widgets_init' ) ):

function helium_widgets_init() {

	register_sidebar( array(
		'name'          => esc_html__( 'Header Widget Area', 'helium' ), 
		'id'            => 'header_widget_area', 
		'description'   => esc_html__( 'This is the header widget area.', 'helium' ), 
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>', 
		'before_title'  => '<h4 class="widget-title">', 
		'after_title'   => '</h4>'
	));

	// Register custom widget areas
	if( class_exists( 'Youxi_Widget_Area' ) ) {

		$widget_areas = get_option( Youxi_Widget_Area::option_key(), array() );

		if( is_array( $widget_areas ) && ! empty( $widget_areas ) ) {

			foreach( $widget_areas as $widget_area_id => $widget_area_args ) {

				$widget_area_args = wp_parse_args( $widget_area_args, array(
					'id'          => '', 
					'name'        => '', 
					'description' => ''
				));

				register_sidebar( array(
					'id'            => $widget_area_id, 
					'name'          => $widget_area_args['name'], 
					'description'   => $widget_area_args['description'], 
					'before_widget' => '<div id="%1$s" class="widget %2$s">', 
					'after_widget'  => '</div>', 
					'before_title'  => '<h4 class="widget-title">', 
					'after_title'   => '</h4>'
				));
			}
		}
	}
}
endif;
add_action( 'widgets_init', 'helium_widgets_init' );

/* ==========================================================================
	Other WP Filters
============================================================================= */

/**
 * Deregister Default WordPress MEJS Styles
 */
if( ! function_exists( 'helium_wp_mediaelement' ) ):

function helium_wp_mediaelement() {

	/* Dequeue default wp mediaelement style */
	wp_deregister_style( 'mediaelement' );
	wp_deregister_style( 'wp-mediaelement' );
}
endif;
add_action( 'wp_enqueue_scripts', 'helium_wp_mediaelement' );

/* ==========================================================================
	User Social Profiles
============================================================================= */

if( ! function_exists( 'helium_user_social_profiles' ) ):

function helium_user_social_profiles() {
	return array(
		'twitter'     => esc_html__( 'Twitter', 'helium' ), 
		'facebook'    => esc_html__( 'Facebook', 'helium' ), 
		'googleplus'  => esc_html__( 'Google+', 'helium' ), 
		'pinterest'   => esc_html__( 'Pinterest', 'helium' ), 
		'linkedin'    => esc_html__( 'LinkedIn', 'helium' ), 
		'youtube'     => esc_html__( 'YouTube', 'helium' ), 
		'vimeo'       => esc_html__( 'Vimeo', 'helium' ), 
		'tumblr'      => esc_html__( 'tumblr', 'helium' ), 
		'instagram'   => esc_html__( 'Instagram', 'helium' ), 
		'flickr'      => esc_html__( 'Flickr', 'helium' ), 
		'dribbble'    => esc_html__( 'dribbble', 'helium' ), 
		'foursquare'  => esc_html__( 'Foursquare', 'helium' ), 
		'forrst'      => esc_html__( 'Forrst', 'helium' ), 
		'vkontakte'   => esc_html__( 'VKontakte', 'helium' ), 
		'wordpress'   => esc_html__( 'WordPress', 'helium' ), 
		'stumbleupon' => esc_html__( 'StumbleUpon', 'helium' ), 
		'yahoo'       => esc_html__( 'Yahoo!', 'helium' ), 
		'blogger'     => esc_html__( 'Blogger', 'helium' ), 
		'soundcloud'  => esc_html__( 'SoundCloud', 'helium' )
	);
}
endif;

/**
 * User Contact Methods
 */
if( ! function_exists( 'helium_user_contactmethods' ) ):

function helium_user_contactmethods( $methods ) {
	return array_merge( $methods, helium_user_social_profiles() );
}
endif;
add_filter( 'user_contactmethods', 'helium_user_contactmethods' );

/* ==========================================================================
	Modify Stylesheet URI
============================================================================= */

if( ! function_exists( 'helium_stylesheet_uri' ) ):

function helium_stylesheet_uri( $stylesheet_uri, $stylesheet_dir_uri ) {

	if( ! is_child_theme() ) {
		if( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			return $stylesheet_dir_uri . "/assets/css/helium.css";
		}
		return $stylesheet_dir_uri . "/assets/css/helium.min.css";
	}

	return $stylesheet_uri;	
}
endif;
add_filter( 'stylesheet_uri', 'helium_stylesheet_uri', 10, 2 );

/* ==========================================================================
	Typekit JS rendering
============================================================================= */

if( ! function_exists( 'helium_typekit_wp_head' ) ):

function helium_typekit_wp_head() {

	$option_object = get_option( 'youxi_external_api_typekit_option' );

	/* Load Typekit only when it's used */
	if( ! empty( $option_object['kit_id'] ) && Youxi_Font::has_typekit() ) : ?>
<script>
  (function(d) {
    var config = {
      kitId: '<?php echo $option_object['kit_id'] ?>', 
      scriptTimeout: 3000, 
      async: true
    }, 
    h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src='https://use.typekit.net/'+config.kitId+'.js';tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
  })(document);
</script>
<?php endif;
}
endif;
add_action( 'wp_head', 'helium_typekit_wp_head', 6 );

/* ==========================================================================
	WordPress Custom Logo
============================================================================= */

if( ! function_exists( 'helium_get_custom_logo' ) ) : 

function helium_get_custom_logo( $custom_logo_output ) {

	if( $mobile_logo_attachment_id = Youxi()->option->get( 'custom_logo_mobile' ) ) {

		$custom_logo_mobile = wp_get_attachment_image( $mobile_logo_attachment_id, 'full', false, array(
			'class' => 'custom-logo-mobile'
		));

		$custom_logo_output = str_replace( 'class="custom-logo-link"', 'class="custom-logo-link has-mobile-logo"', $custom_logo_output );
		$custom_logo_output = str_replace( '</a>', $custom_logo_mobile . '</a>', $custom_logo_output );
	}

	return $custom_logo_output;
}
endif;
add_filter( 'get_custom_logo', 'helium_get_custom_logo' );

/* ==========================================================================
	WordPress 4.7+ functions
============================================================================= */

/**
 * Retrieves the URL of a file in the theme.
 *
 * Searches in the stylesheet directory before the template directory so themes
 * which inherit from a parent theme can just override one file.
 *
 * @since 4.7.0
 *
 * @param string $file Optional. File to search for in the stylesheet directory.
 * @return string The URL of the file.
 */
if( ! function_exists( 'get_theme_file_uri' ) ) : 

function get_theme_file_uri( $file = '' ) {
	$file = ltrim( $file, '/' );

	if ( empty( $file ) ) {
		$url = get_stylesheet_directory_uri();
	} elseif ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
		$url = get_stylesheet_directory_uri() . '/' . $file;
	} else {
		$url = get_template_directory_uri() . '/' . $file;
	}

	/**
	 * Filters the URL to a file in the theme.
	 *
	 * @since 4.7.0
	 *
	 * @param string $url  The file URL.
	 * @param string $file The requested file to search for.
	 */
	return apply_filters( 'theme_file_uri', $url, $file );
}
endif;

/**
 * Retrieves the path of a file in the theme.
 *
 * Searches in the stylesheet directory before the template directory so themes
 * which inherit from a parent theme can just override one file.
 *
 * @since 4.7.0
 *
 * @param string $file Optional. File to search for in the stylesheet directory.
 * @return string The path of the file.
 */
if( ! function_exists( 'get_theme_file_path' ) ) : 

function get_theme_file_path( $file = '' ) {
	$file = ltrim( $file, '/' );

	if ( empty( $file ) ) {
		$path = get_stylesheet_directory();
	} elseif ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
		$path = get_stylesheet_directory() . '/' . $file;
	} else {
		$path = get_template_directory() . '/' . $file;
	}

	/**
	 * Filters the path to a file in the theme.
	 *
	 * @since 4.7.0
	 *
	 * @param string $path The file path.
	 * @param string $file The requested file to search for.
	 */
	return apply_filters( 'theme_file_path', $path, $file );
}
endif;

/* ==========================================================================
	Scripts and Styles
============================================================================= */

if( ! function_exists( 'helium_wp_enqueue_script' ) ):

function helium_wp_enqueue_script() {
	
	/* Get theme version */
	$wp_theme = wp_get_theme();
	$theme_version = $wp_theme->exists() ? $wp_theme->get( 'Version' ) : false;

	/* Get script debug status */
	$script_debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
	$suffix = $script_debug ? '' : '.min';

	/* Enqueue Core Styles */
	wp_enqueue_style( 'helium-bootstrap', get_template_directory_uri() . "/assets/bootstrap/css/bootstrap{$suffix}.css", array(), '3.3.7', 'screen' );
	wp_enqueue_style( 'helium-core', get_stylesheet_uri(), array( 'helium-bootstrap' ), $theme_version, 'screen' );

	/* Enqueue Google Fonts */
	if( $google_fonts_url = Youxi_Font::google_font_request_url() ) {
		wp_enqueue_style( 'helium-google-fonts', $google_fonts_url, array(), $theme_version, 'screen' );
	}

	/* Enqueue Icons */
	wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css', array(), '4.4.0', 'screen' );

	/* Make sure the LESS compiler exists */
	if( ! class_exists( 'Youxi_LESS_Compiler' ) ) {
		require_once( get_template_directory() . '/lib/framework/class-less-compiler.php' );
	}
	$less_compiler = Youxi_LESS_Compiler::get();

	/* Prepare variables */
	$theme_options_vars = array();

	/* Get the accent color setting */
	$brand_primary = Youxi()->option->get( 'accent_color', helium_default_accent_color() );

	/* Custom accent color styles */
	if( helium_default_accent_color() !== $brand_primary ) {
		wp_add_inline_style( 'helium-bootstrap', $less_compiler->compile( '/assets/less/mods/bootstrap.less', array( 'bs-override' => array( 'brand-primary' => $brand_primary ) ) ) );
		$theme_options_vars['brand-primary'] = $brand_primary;
	}

	/* Custom theme styles */
	if( $header_logo_height = absint( Youxi()->option->get( 'logo_height' ) ) ) {
		$theme_options_vars['logo-height'] = sprintf( '%dpx', $header_logo_height );
	}

	/* Add custom styles from theme options */
	$theme_options_css = $less_compiler->compile( '/assets/less/mods/theme-options.less', array(
		'theme-options' => $theme_options_vars
	));
	if( ! is_wp_error( $theme_options_css ) ) {
		wp_add_inline_style( 'helium-core', $theme_options_css );
	}

	/* Add custom fonts from theme options */
	$font_less_vars = Youxi_Font::get_vars();

	if( ! empty( $font_less_vars ) ) {
		$theme_fonts_css = $less_compiler->compile( '/assets/less/mods/theme-fonts.less', array(
			'theme-fonts' => $font_less_vars
		));
		if( ! is_wp_error( $theme_fonts_css ) ) {
			wp_add_inline_style( 'helium-core', $theme_fonts_css );
		}
	}

	/* Core */
	if( $script_debug ) {
		wp_enqueue_script( 'helium-plugins', get_template_directory_uri() . "/assets/js/helium.plugins.js", array( 'jquery' ), $theme_version, true );
		wp_enqueue_script( 'helium-gridlist', get_template_directory_uri() . "/assets/js/helium.gridlist.js", array( 'jquery' ), $theme_version, true );
		wp_enqueue_script( 'helium-core', get_template_directory_uri() . "/assets/js/helium.setup.js", array( 'jquery', 'helium-plugins', 'helium-gridlist' ), $theme_version, true );
	} else {
		wp_enqueue_script( 'helium-core', get_template_directory_uri() . "/assets/js/helium.min.js", array( 'jquery' ), $theme_version, true );
	}

	/* AJAX */
	if( $ajax_enabled = Youxi()->option->get( 'ajax_navigation' ) ) {

		wp_enqueue_script( 'helium-ajax', get_template_directory_uri() . "/assets/js/helium.ajax{$suffix}.js", array( 'helium-core' ), $theme_version, true );

		/* Make sure mediaelementjs & playlist is loaded */
		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_script( 'wp-mediaelement' );
		wp_enqueue_script( 'wp-playlist' );

		/* Enqueue all registered shortcode assets */
		if( class_exists( 'Youxi_Shortcode_Manager' ) ) {
			Youxi_Shortcode_Manager::get()->enqueue_shortcodes();
		}
	}

	/* Enqueue Magnific Popup */
	wp_enqueue_script( 'helium-mfp', get_template_directory_uri() . "/assets/plugins/mfp/jquery.mfp-1.0.0{$suffix}.js", array( 'jquery' ), '1.0.0', true );
	wp_enqueue_style( 'helium-mfp', get_template_directory_uri() . "/assets/plugins/mfp/mfp.css", array(), '1.0.0', 'screen' );

	/* Enqueue Isotope */
	wp_enqueue_script( 'helium-isotope', get_template_directory_uri() . "/assets/plugins/isotope/isotope.pkgd{$suffix}.js", array( 'jquery' ), '2.2.0', true );

	/* Enqueue RoyalSlider */
	wp_enqueue_script( 'helium-royalslider', get_template_directory_uri() . "/assets/plugins/royalslider/jquery.royalslider-9.5.7.min.js", array( 'jquery' ), '9.5.7', true );
	wp_enqueue_style( 'helium-royalslider', get_template_directory_uri() . "/assets/plugins/royalslider/royalslider{$suffix}.css", array(), '1.0.5', 'screen' );

	/* Pass configuration to frontend */
	wp_localize_script( 'helium-core', '_helium', apply_filters( 'helium_js_vars', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ), 
		'homeUrl' => home_url( '/' )
	)));

	/* Enqueue AddThis script on singular pages */
	if( $ajax_enabled || is_singular( array( 'post', 'portfolio', 'download' ) ) ) {

		$addthis_config = array( 'ui_delay' => 100 );
		if( $addthis_profile_id = Youxi()->option->get( 'addthis_profile_id' ) ) {
			$addthis_config['pubid'] = $addthis_profile_id;
		}
		wp_enqueue_script( 'helium-addthis', '//s7.addthis.com/js/300/addthis_widget.js', array(), 300, true );
		wp_localize_script( 'helium-addthis', 'addthis_config', $addthis_config );
	}

	/* Enqueue comment-reply */
	if( $ajax_enabled || ( is_singular( array( 'post', 'portfolio', 'download' ) ) && comments_open() && get_option( 'thread_comments' ) ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
endif;
add_action( 'wp_enqueue_scripts', 'helium_wp_enqueue_script' );
