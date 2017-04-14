<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * [leaflet_map] handler
 */
function youxi_shortcode_leaflet_map_handler( $atts, $content, $tag ) {

	extract( $atts, EXTR_PREFIX_ALL, 'leaflet_map' );

	if( is_string( $leaflet_map_controls ) ) {
		$leaflet_map_controls = explode( ',', $leaflet_map_controls );
		$leaflet_map_controls = array_map( 'trim', $leaflet_map_controls );
	}

	$leaflet_options = array(
		'center'             => array( $leaflet_map_center_lat, $leaflet_map_center_lng ), 
		'zoom'               => absint( $leaflet_map_zoom ), 
		'zoomControl'        => in_array( 'zoom', $leaflet_map_controls ), 
		'scrollWheelZoom'    => wp_validate_boolean( $leaflet_map_scroll_zoom ), 
		'attributionControl' => in_array( 'attribution', $leaflet_map_controls ), 
		'scaleControl'       => in_array( 'scale', $leaflet_map_controls )
	);

	$leaflet_markers = do_shortcode( $content );
	$leaflet_markers = ',"markers":[' . trim( preg_replace( '/}\s*{/', '},{', $leaflet_markers ) ) . ']';

	$leaflet_options_json = json_encode( $leaflet_options );
	$leaflet_options_json = preg_replace( '/}$/', $leaflet_markers . '}', $leaflet_options_json );

	$aspect_ratio_value = 56.25;
	if( preg_match( '/^([0-9]*\.?[0-9]+):([0-9]*\.?[0-9]+)$/', $leaflet_map_aspect_ratio, $ratio_matches ) ) {
		$ratio_w = floatval( $ratio_matches[1] );
		$ratio_h = floatval( $ratio_matches[2] );
		if( $ratio_w != 0 && $ratio_h != 0 ) {
			$aspect_ratio_value = 100.0 / ( $ratio_w / $ratio_h );
		}
	}

	return '<div class="leaflet-map-holder" style="position: relative; padding-top: ' . esc_attr( $aspect_ratio_value ) . '%">' . 
		'<div class="leaflet-map" data-leaflet-options="' . esc_attr( $leaflet_options_json ) . '"' . 
		' data-mapbox-access-token="' . esc_attr( youxi_shortcode_mapbox_access_token() ) . '"' . 
		' style="position: absolute; left: 0; top: 0; width: 100%; height: 100%;"></div></div>';
}

/**
 * [leaflet_marker] handler
 */
function youxi_shortcode_leaflet_marker_handler( $atts, $content, $tag ) {

	extract( $atts, EXTR_PREFIX_ALL, 'leaflet_marker' );

	return json_encode( array( 'lat' => $leaflet_marker_lat, 'lng' => $leaflet_marker_lng, 'description' => $content ) );
}

/**
 * [slider] handler
 */
function youxi_shortcode_slider_handler( $atts, $content, $tag ) {

	extract( $atts, EXTR_PREFIX_ALL, 'slider' );

	$slider_attachment_ids   = explode( ',', $slider_attachment_ids );
	$slider_attachment_count = count( $slider_attachment_ids );

	if( 0 == $slider_attachment_count ) {
		return;
	}

	$id = esc_attr( 'carousel-' . Youxi_Shortcode::count( $tag ) );

	if( is_string( $slider_controls ) ) {
		$slider_controls = explode( ',', $slider_controls );
		$slider_controls = array_map( 'trim', $slider_controls );
	}

	if( is_string( $slider_behaviors ) ) {
		$slider_behaviors = explode( ',', $slider_behaviors );
		$slider_behaviors = array_map( 'trim', $slider_behaviors );
	}

	$slider_data_html = ' data-interval="' . esc_attr( $slider_interval ) . '"';

	if( in_array( 'pause', $slider_behaviors ) ) {
		$slider_data_html .= ' data-pause="hover"';
	}
	if( in_array( 'wrap', $slider_behaviors ) ) {
		$slider_data_html .= ' data-wrap="true"';
	}

	$o = '<div id="' . esc_attr( $id ) . '" class="carousel slide" data-ride="carousel"' . $slider_data_html . '>';

		if( in_array( 'pagers', $slider_controls ) ):

			$o .= '<ol class="carousel-indicators">';

			for( $i = 0; $i < $slider_attachment_count; $i++ ):

				$o .= '<li data-target="#' . esc_attr( $id ) . '" data-slide-to="' . esc_attr( $i ) . '"';
				$o .= ( 0 == $i ? ' class="active"' : '' ) . '></li>';

			endfor;

			$o .= '</ol>';

		endif;

		/* Carousel Content */
		$o .= '<div class="carousel-inner" role="listbox">';

			foreach( $slider_attachment_ids as $index => $attachment_id ):

				$o .= '<div class="carousel-item' . esc_attr( $index ? '' : ' active' ) . '">';

					$o .= wp_get_attachment_image( $attachment_id, 'full' );

					$attachment = get_post( $attachment_id );
					$post_excerpt = trim( $attachment->post_excerpt );

					if( ! empty( $post_excerpt ) ) {
						$o .= '<div class="carousel-caption">' . wpautop( $post_excerpt ) . '</div>';
					}

				$o .= '</div>';

			endforeach;

		$o .= '</div>';

		if( in_array( 'arrows', $slider_controls ) ):

			$o .= '<a class="left carousel-control" href="#' . esc_attr( $id ) . '" role="button" data-slide="prev">';
				$o .= '<span class="icon-prev" aria-hidden="true"></span>';
				$o .= '<span class="sr-only">' . esc_html__( 'Previous', 'youxi' ) . '</span>';
			$o .= '</a>';

			$o .= '<a class="right carousel-control" href="#' . esc_attr( $id ) . '" role="button" data-slide="next">';
				$o .= '<span class="icon-next" aria-hidden="true"></span>';
				$o .= '<<span class="sr-only">' . esc_html__( 'Next', 'youxi' ) . '</span>';
			$o .= '</a>';

		endif;

	$o .= '</div>';

	return $o;
}

/**
 * Mapbox Access Token
 */
function youxi_shortcode_mapbox_access_token() {
	return apply_filters( 'youxi_shortcode_mapbox_access_token', '' );
}

/**
 * Define Media Shortcodes
 */
function youxi_define_media_shortcodes( $manager ) {

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	/**
	 * Media Category
	 */
	$manager->add_category( 'media', array(
		'label' => esc_html__( 'Media Shortcodes', 'youxi' ), 
		'priority' => 40
	));

	/**
	 * [audio] shortcode
	 */
	$manager->add_shortcode( 'audio', array(
		'label' => esc_html__( 'Audio', 'youxi' ), 
		'category' => 'media', 
		'priority' => 0, 
		'icon' => 'fa fa-music', 
		'third_party' => true, 
		'atts' => array(
			'src' => array(
				'type' => 'upload', 
				'label' => esc_html__( 'Source', 'youxi' ), 
				'description' => esc_html__( 'Choose here the audio source.', 'youxi' ), 
				'return_type' => 'url', 
				'library_type' => 'audio'
			), 
			'loop' => array(
				'type' => 'switch', 
				'label' => esc_html__( 'Loop', 'youxi' ), 
				'description' => esc_html__( 'Switch whether the audio will start over again, every time it is finished.', 'youxi' )
			), 
			'autoplay' => array(
				'type' => 'switch', 
				'label' => esc_html__( 'Autoplay', 'youxi' ), 
				'description' => esc_html__( 'Switch whether the audio will start playing as soon as it is ready.', 'youxi' )
			), 
			'preload' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Preload', 'youxi' ), 
				'description' => esc_html__( 'Choose part of the audio to preload when the page loads.', 'youxi' ), 
				'choices' => array(
					'none' => esc_html__( 'None', 'youxi' ), 
					'auto' => esc_html__( 'Auto', 'youxi' ), 
					'metadata' => esc_html__( 'Metadata', 'youxi' )
				), 
				'std' => 'none'
			)
		)
	));

	/**
	 * [embed] shortcode
	 */
	$manager->add_shortcode( 'embed', array(
		'label' => esc_html__( 'Embed', 'youxi' ), 
		'category' => 'media', 
		'priority' => 10, 
		'icon' => 'fa fa-code', 
		'insert_nl' => false, 
		'third_party' => true, 
		'content' => array(
			'type' => 'text', 
			'label' => esc_html__( 'Embed URL', 'youxi' ), 
			'description' => __( 'Enter here the embed URL. See <a href="http://codex.wordpress.org/Embeds" target="_blank">http://codex.wordpress.org/Embeds</a> for a list of supported providers.', 'youxi' )
		)
	));

	/**
	 * [leaflet_map] shortcode
	 */
	$manager->add_shortcode( 'leaflet_map', array(
		'label' => esc_html__( 'Leaflet Map', 'youxi' ), 
		'category' => 'media', 
		'priority' => 20, 
		'icon' => 'fa fa-map-marker', 
		'scripts' => array(
			'leaflet' => array(
				'src' => YOUXI_SHORTCODE_URL . "assets/frontend/plugins/leaflet/leaflet.js", 
				'ver' => '0.7.7', 
				'in_footer' => true
			)
		), 
		'styles' => array(
			'leaflet' => array(
				'src' => YOUXI_SHORTCODE_URL . "assets/frontend/plugins/leaflet/leaflet.css", 
				'ver' => '0.7.7', 
				'media' => 'screen'
			)
		), 
		'atts' => array(
			'center_lat' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Center Latitude', 'youxi' ), 
				'description' => esc_html__( 'Enter here the map center latitude.', 'youxi' ), 
				'std' => 0
			), 
			'center_lng' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Center Longitude', 'youxi' ), 
				'description' => esc_html__( 'Enter here the map center longitude.', 'youxi' ), 
				'std' => 0
			), 
			'zoom' => array(
				'type' => 'number', 
				'label' => esc_html__( 'Zoom', 'youxi' ), 
				'description' => esc_html__( 'Enter here the map zoom level.', 'youxi' ), 
				'min' => 0, 
				'max' => 20, 
				'std' => 15
			), 
			'scroll_zoom' => array(
				'type' => 'switch', 
				'label' => esc_html__( 'Scroll Zoom', 'youxi' ), 
				'description' => esc_html__( 'Switch to enable zoom in/out using the mouse wheel.', 'youxi' ), 
				'std' => false
			), 
			'controls' => array(
				'type' => 'checkboxlist', 
				'label' => esc_html__( 'Controls', 'youxi' ), 
				'description' => esc_html__( 'Choose here the controls to display on the map.', 'youxi' ), 
				'choices' => array(
					'zoom' => esc_html__( 'Pan', 'youxi' ), 
					'attribution' => esc_html__( 'Zoom', 'youxi' ), 
					'scale' => esc_html__( 'Scale', 'youxi' )
				), 
				'std' => array(), 
				'serialize' => 'js:function( data ) {
					return ( data || [] ).join(",");
				}', 
				'deserialize' => 'js:function( data ) {
					return ( data + "" ).split(",");
				}'
			), 
			'aspect_ratio' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Aspect Ratio', 'youxi' ), 
				'description' => esc_html__( 'Enter the map aspect ratio in w:h format.', 'youxi' ), 
				'std' => '16:9'
			)
		), 
		'content' => array(
			'type' => 'repeater', 
			'label' => esc_html__( 'Markers', 'youxi' ), 
			'description' => esc_html__( 'Specify markers to put on the map.', 'youxi' ), 
			'fields' => array( array( $manager, 'get_shortcode_fields' ), 'leaflet_marker' ), 
			'preview_template' => '{{ data.lat }}, {{ data.lng }}', 
			'min' => 0, 
			'serialize' => 'js:function( data ) {
				return this.construct( "leaflet_marker", data );
			}', 
			'deserialize' => 'js:function( data ) {
				return this.deserializeArray( data );
			}'
		), 
		'callback' => 'youxi_shortcode_leaflet_map_handler'
	));
	$manager->add_shortcode( 'leaflet_marker', array(
		'label' => esc_html__( 'Leaflet Marker', 'youxi' ), 
		'category' => 'content', 
		'internal' => true, 
		'insert_nl' => false, 
		'atts' => array(
			'lat' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Latitude', 'youxi' ), 
				'description' => esc_html__( 'Enter here the marker\'s center latitude.', 'youxi' ), 
				'std' => 0
			), 
			'lng' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Longitude', 'youxi' ), 
				'description' => esc_html__( 'Enter here the marker\'s center longitude.', 'youxi' ), 
				'std' => 0
			), 
		), 
		'content' => array(
			'type' => 'textarea', 
			'label' => esc_html__( 'Description', 'youxi' ), 
			'description' => esc_html__( 'Enter here the marker description.', 'youxi' )
		), 
		'callback' => 'youxi_shortcode_leaflet_marker_handler'
	));

	/**
	 * [slider] shortcode
	 */
	$manager->add_shortcode( 'slider', array(
		'label' => esc_html__( 'Slider', 'youxi' ), 
		'category' => 'media', 
		'priority' => 30, 
		'icon' => 'fa fa-photo', 
		'atts' => array(
			'controls' => array(
				'type' => 'checkboxlist', 
				'label' => esc_html__( 'Controls', 'youxi' ), 
				'description' => esc_html__( 'Choose here the slider controls to display.', 'youxi' ), 
				'choices' => array(
					'pagers' => esc_html__( 'Pagers', 'youxi' ), 
					'arrows' => esc_html__( 'Arrows', 'youxi' )
				), 
				'std' => array( 'pagers' , 'arrows' ), 
				'serialize' => 'js:function( data ) {
					return ( data || [] ).join(",");
				}', 
				'deserialize' => 'js:function( data ) {
					return ( data + "" ).split(",");
				}'
			), 
			'interval' => array(
				'type' => 'uislider', 
				'label' => esc_html__( 'Interval', 'youxi' ), 
				'description' => esc_html__( 'Enter the amount of time to delay between automatically cycling an item.', 'youxi' ), 
				'std' => 5000, 
				'widgetopts' => array(
					'min' => 0, 
					'max' => 10000, 
					'step' => 50
				)
			), 
			'behaviors' => array(
				'type' => 'checkboxlist', 
				'label' => esc_html__( 'Behaviors', 'youxi' ), 
				'description' => esc_html__( 'Choose here the behaviors of the slider.', 'youxi' ), 
				'choices' => array(
					'pause' => esc_html__( 'Pause on Hover', 'youxi' ), 
					'wrap'  => esc_html__( 'Allow Wrapping', 'youxi' )
				), 
				'std' => array( 'wrap' ), 
				'serialize' => 'js:function( data ) {
					return ( data || [] ).join(",");
				}', 
				'deserialize' => 'js:function( data ) {
					return ( data + "" ).split(",");
				}'
			), 
			'attachment_ids' => array(
				'type' => 'gallery', 
				'label' => esc_html__( 'Slider Images', 'youxi' ), 
				'description' => esc_html__( 'Choose here the slider images.', 'youxi' ), 
				'multiple' => true, 
				'serialize' => 'js:function( data ) {
					return ( data || [] ).join(",");
				}', 
				'deserialize' => 'js:function( data ) {
					return ( data + "" ).split(",");
				}'
			)
		), 
		'callback' => 'youxi_shortcode_slider_handler'
	));

	/**
	 * [twitter] shortcode
	 */
	$manager->add_shortcode( 'twitter', array(
		'label' => esc_html__( 'Twitter', 'youxi' ), 
		'category' => 'media', 
		'priority' => 40, 
		'icon' => 'fa fa-twitter', 
		'atts' => array(
			'username' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Username', 'youxi' ), 
				'description' => esc_html__( 'Enter here the Twitter username.', 'youxi' )
			)
		), 

		// Do nothing.
		'callback' => '__return_empty_string'
	));

	/**
	 * [video] shortcode
	 */
	$manager->add_shortcode( 'video', array(
		'label' => esc_html__( 'Video', 'youxi' ), 
		'category' => 'media', 
		'priority' => 50, 
		'icon' => 'fa fa-video-camera', 
		'third_party' => true, 
		'atts' => array(
			'src' => array(
				'type' => 'upload', 
				'label' => esc_html__( 'Source', 'youxi' ), 
				'description' => esc_html__( 'Choose here the video source.', 'youxi' ), 
				'return_type' => 'url', 
				'library_type' => 'video'
			), 
			'poster' => array(
				'type' => 'image', 
				'label' => esc_html__( 'Poster', 'youxi' ), 
				'description' => esc_html__( 'Choose an image to be shown while the video is downloading.', 'youxi' ), 
				'return_type' => 'url'
			), 
			'loop' => array(
				'type' => 'switch', 
				'label' => esc_html__( 'Loop', 'youxi' ), 
				'description' => esc_html__( 'Switch whether the video will start over again, every time it is finished.', 'youxi' )
			), 
			'autoplay' => array(
				'type' => 'switch', 
				'label' => esc_html__( 'Autoplay', 'youxi' ), 
				'description' => esc_html__( 'Switch whether the video will start playing as soon as it is ready.', 'youxi' )
			), 
			'preload' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Preload', 'youxi' ), 
				'description' => esc_html__( 'Choose part of the video to preload when the page loads.', 'youxi' ), 
				'choices' => array(
					'none' => esc_html__( 'None', 'youxi' ), 
					'auto' => esc_html__( 'Auto', 'youxi' ), 
					'metadata' => esc_html__( 'Metadata', 'youxi' )
				), 
				'std' => 'metadata'
			)
		)
	));
}

/**
 * Hook to 'youxi_shortcode_register'
 */
add_action( 'youxi_shortcode_register', 'youxi_define_media_shortcodes', 1 );
