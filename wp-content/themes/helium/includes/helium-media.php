<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

/* ==========================================================================
	Mapbox Access Token
============================================================================= */

if( ! function_exists( 'helium_mapbox_access_token' ) ):

function helium_mapbox_access_token() {
	return apply_filters( 'helium_mapbox_access_token', 
		'pk.eyJ1IjoibWFpbWFpcmVsIiwiYSI6ImNpbzlxZXQzMjAzMmR3M2tqcDE3cTczcGMifQ.3O0JjyrwD2NxYe9PDCqUZQ' );
}
endif;

/* ==========================================================================
	RoyalSlider Settings
============================================================================= */

if( ! function_exists( 'helium_rs_settings' ) ):

function helium_rs_settings( $settings ) {

	$settings = wp_parse_args( $settings, array(
		'autoHeight'           => true, 
		'autoScaleSliderRatio' => array( 'width' => 4, 'height' => 3 ), 
		'imageScaleMode'       => 'fill', 
		'controlNavigation'    => true, 
		'arrowsNav'            => true, 
		'loop'                 => true, 
		'slidesOrientation'    => 'horizontal', 
		'transitionType'       => 'move', 
		'transitionSpeed'      => 600
	));

	if( $settings['autoHeight'] ) {

		$rs_settings = array(
			'autoHeight'       => true, 
			'autoScaleSlider'  => false, 
			'imageScaleMode'   => 'none', 
			'imageAlignCenter' => false
		);

		// Vertical slide orientation is not supported if autoHeight is enabled
		$settings['slidesOrientation'] = 'horizontal';
	} else {
		$rs_settings = array(
			'autoScaleSlider'       => true, 
			'autoScaleSliderWidth'  => $settings['autoScaleSliderRatio']['width'], 
			'autoScaleSliderHeight' => $settings['autoScaleSliderRatio']['height'], 
			'imageScaleMode'        => $settings['imageScaleMode']
		);
	}

	$rs_settings = array_merge( $rs_settings, array(
		'controlNavigation' => $settings['controlNavigation'] ? 'bullets' : 'none', 
		'arrowsNav'         => (bool) $settings['arrowsNav'], 
		'loop'              => (bool) $settings['loop'], 
		'slidesOrientation' => $settings['slidesOrientation'], 
		'transitionType'    => $settings['transitionType'], 
		'transitionSpeed'   => (int) $settings['transitionSpeed']
	));

	return json_encode( $rs_settings );
}
endif;

/* ==========================================================================
	Mapbox Access Token
============================================================================= */

if( ! function_exists( 'helium_mapbox_access_token' ) ):

function helium_mapbox_access_token() {
	return 'pk.eyJ1IjoibWFpbWFpcmVsIiwiYSI6ImNpbzlxZXQzMjAzMmR3M2tqcDE3cTczcGMifQ.3O0JjyrwD2NxYe9PDCqUZQ';
}
endif;
