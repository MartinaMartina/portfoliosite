<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

if( ! class_exists( 'Youxi_Portfolio' ) ):

class Youxi_Portfolio {

	private static $__registered = false;

	/* Register portfolio post type */
	public static function register() {

		/* Make sure to register only once */
		if( self::$__registered ) {
			return;
		}
		self::$__registered = true;

		/* Get the post type settings */
		$settings = wp_parse_args( self::post_type_args(), array(
			'args' => array(), 
			'metaboxes' => array(), 
			'taxonomies' => array()
		));

		extract( $settings, EXTR_SKIP );

		/* Create the post type object */
		$post_type_object = Youxi_Post_Type::get( self::post_type_name(), $args );

		/* Add the metaboxes */
		foreach( $metaboxes as $metabox_id => $metabox ) {
			$post_type_object->add_meta_box( new Youxi_Metabox( $metabox_id, $metabox ) );
		}

		/* Add the taxonomies */
		foreach( $taxonomies  as $taxonomy_id => $taxonomy ) {
			if( version_compare( YOUXI_CORE_VERSION, '1.6' ) < 0 ) {
				$post_type_object->add_taxonomy( new Youxi_Taxonomy( $taxonomy_id, $taxonomy ) );
			} else {
				$post_type_object->add_taxonomy( Youxi_Taxonomy::get( $taxonomy_id, $taxonomy ) );
			}
		}

		if( is_admin() ) {

			/* Attach post type ordering page */
			$ordering_page = new Youxi_Post_Order_Page(
				esc_html__( 'Order Portfolio', 'youxi' ), 
				esc_html__( 'Order Portfolio', 'youxi' ), 
				'youxi-portfolio-order-page'
			);
			$post_type_object->add_submenu_page( $ordering_page );
		}

		/* Register the post type */
		$post_type_object->register();
	}

	/* The post type name for portfolio */
	public static function post_type_name() {
		return apply_filters( 'youxi_portfolio_post_type_name', 'portfolio' );
	}

	/* The default taxonomy name for portfolio */
	public static function taxonomy_name() {
		return apply_filters( 'youxi_portfolio_taxonomy_name', 'portfolio-category' );
	}

	/* The one page post type arguments */
	public static function post_type_args() {

		global $wp_rewrite;

		$permalinks = get_option( 'youxi_portfolio_permalinks' );

		$taxonomies = array();
		$taxonomies[ self::taxonomy_name() ] = array(
			'labels' => array(
				'name'                       => esc_html__( 'Portfolio Categories', 'youxi' ), 
				'singular_name'              => esc_html__( 'Portfolio Category', 'youxi' ), 
				'all_items'                  => esc_html__( 'All Portfolio Categories', 'youxi' ), 
				'edit_item'                  => esc_html__( 'Edit Portfolio Category', 'youxi' ), 
				'view_item'                  => esc_html__( 'View Portfolio Category', 'youxi' ), 
				'update_item'                => esc_html__( 'Update Portfolio Category', 'youxi' ), 
				'add_new_item'               => esc_html__( 'Add New Portfolio Category', 'youxi' ), 
				'new_item_name'              => esc_html__( 'New Portfolio Category Name', 'youxi' ), 
				'parent_item'                => esc_html__( 'Parent Portfolio Category', 'youxi' ), 
				'parent_item_colon'          => esc_html__( 'Parent Portfolio Category: ', 'youxi' ), 
				'search_items'               => esc_html__( 'Search Portfolio Categories', 'youxi' ), 
				'popular_items'              => esc_html__( 'Popular Portfolio Categories', 'youxi' ), 
				'separate_items_with_commas' => esc_html__( 'Separate portfolio categories with commas', 'youxi' ), 
				'add_or_remove_items'        => esc_html__( 'Add or remove portfolio categories', 'youxi' ), 
				'choose_from_most_used'      => esc_html__( 'Choose from most used portfolio categories', 'youxi' ), 
				'not_found'                  => esc_html__( 'No portfolio categories found.', 'youxi' )
			), 
			'show_admin_column' => true, 
			'hierarchical'      => true, 
			'rewrite'           => empty( $permalinks['category_base'] ) ? true : array(
				'slug'         => untrailingslashit( $permalinks['category_base'] ), 
				'with_front'   => $wp_rewrite->using_index_permalinks(), 
				'hierarchical' => true
			)
		);

		/* Return the settings for the portfolio cpt */
		return array(

			'args' => apply_filters( 'youxi_portfolio_cpt_args', array(
				'labels' => apply_filters( 'youxi_portfolio_cpt_labels', array(
					'name'               => esc_html__( 'Portfolio', 'youxi' ), 
					'singular_name'      => esc_html__( 'Portfolio', 'youxi' ), 
					'all_items'          => esc_html__( 'All Portfolio', 'youxi' ), 
					'add_new'            => esc_html__( 'Add New Portfolio', 'youxi' ),
					'add_new_item'       => esc_html__( 'Add New Portfolio', 'youxi' ),
					'edit_item'          => esc_html__( 'Edit Portfolio', 'youxi' ),
					'view_item'          => esc_html__( 'View Portfolio', 'youxi' ),
					'search_items'       => esc_html__( 'Search Portfolio', 'youxi' ),
					'not_found'          => esc_html__( 'Portfolio not found', 'youxi' ),
					'not_found_in_trash' => esc_html__( 'Portfolio not found in trash', 'youxi' ),
					'parent_item_colon'  => esc_html__( 'Portfolio: ', 'youxi' )
				) ), 
				'description'       => esc_html__( 'This is where you can add portfolio to your site.', 'youxi' ), 
				'public'            => true, 
				'menu_icon'         => 'dashicons-portfolio', 
				'capability_type'   => 'post', 
				'map_meta_cap'      => true, 
				'supports'          => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'revisions', 'page-attributes' ), 
				'has_archive'       => true, 
				'rewrite'           => empty( $permalinks['portfolio_base'] ) ? true : array(
					'slug'       => untrailingslashit( $permalinks['portfolio_base'] ), 
					'with_front' => $wp_rewrite->using_index_permalinks()
				)
			) ), 

			'metaboxes' => apply_filters( 'youxi_portfolio_cpt_metaboxes', array() ), 

			'taxonomies' => apply_filters( 'youxi_portfolio_cpt_taxonomies', $taxonomies )
		);
	}
}
endif;

function youxi_portfolio_cpt_name() {
	return apply_filters( 'youxi_portfolio_cpt_name', Youxi_Portfolio::post_type_name() );
}

function youxi_portfolio_tax_name() {
	return apply_filters( 'youxi_portfolio_tax_name', Youxi_Portfolio::taxonomy_name() );
}

function youxi_portfolio_settings() {
	return Youxi_Portfolio::post_type_args();
}

add_action( 'init', array( 'Youxi_Portfolio', 'register' ) );
