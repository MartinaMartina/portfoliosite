<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

/* ==========================================================================
	Accent Color
============================================================================= */

if( ! function_exists( 'helium_default_accent_color' ) ):

function helium_default_accent_color() {
	return apply_filters( 'helium_default_accent_color', '#3dc9b3' );
}
endif;

/* ==========================================================================
	Site Identity
============================================================================= */

if( ! function_exists( 'helium_site_identity' ) ):

function helium_site_identity() {

	/* If custom logo is assigned on WordPress 4.5+ */
	if( function_exists( 'has_custom_logo' ) && has_custom_logo() ) : 

		echo '<div class="site-logo site-logo--image">';

			the_custom_logo();

		echo '</div>';

	else : 

		echo '<div class="site-logo site-logo--textual h1">';

			echo '<a href="' . esc_url( home_url( '/' ) ) . '" rel="home">';

				bloginfo( 'name' );

			echo '</a>';

		echo '</div>';

	endif;
}
endif;
