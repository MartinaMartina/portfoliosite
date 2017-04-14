<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

if( ! function_exists( 'helium_youxi_importer_demos' ) ):

function helium_youxi_importer_demos( $demos ) {

	return array_merge( $demos, array(
		'default' => array(
			'screenshot' => get_template_directory_uri() . '/screenshot.png', 
			'name' => esc_html__( 'Default', 'helium' ), 
			'tasks' => array(
				'wordpress' => array(
					'xml' => get_template_directory() . '/demo/helium.wordpress.2016-10-15.xml', 
					'attachments_directory' => get_template_directory() . '/demo/attachments'
				), 
				'widgets' => '{"header_widget_area":{"text-1":{"title":"About","text":"We\u2019re Helium, a web design agency. We love design and we try to make the web a better place.","filter":false},"social-widget-1":{"title":"We\'re Social","items":[{"url":"#","title":"Facebook","icon":"facebook"},{"url":"#","title":"Twitter","icon":"twitter"},{"url":"#","title":"Google+","icon":"googleplus"},{"url":"#","title":"Pinterest","icon":"pinterest"},{"url":"#","title":"RSS","icon":"rss"}]},"flickr-widget-1":{"title":"My Flickr Feed","flickr_id":"","limit":8},"instagram-widget-1":{"title":"Instagram Feed","username":"kinfolk","count":8},"twitter-widget-1":{"title":"Recent Tweets","username":"envato","count":2}}}', 
				'frontpage_displays' => array(
					'show_on_front'  => 'page', 
					'page_on_front'  => 1411, 
					'page_for_posts' => 845
				), 
				'nav_menu_locations' => array(
					'main-menu' => 'the-menu'
				)
			)
		)
	));
}
endif;
add_filter( 'youxi_importer_demos', 'helium_youxi_importer_demos' );
