<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

/**
 * Customizer Sanitize Functions
 */
function helium_customizer_sanitize_post_meta( $post_meta ) {
	return array_intersect( $post_meta, array( 'author', 'category', 'tags', 'comments', 'permalinks' ) );
}

function helium_customizer_sanitize_related_behavior( $behavior ) {
	if( ! in_array( $behavior, array( 'lightbox', 'permalink' ) ) ) {
		$behavior = 'lightbox';
	}

	return $behavior;
}

function helium_customizer_sanitize_blog_summary( $blog_summary ) {
	if( ! in_array( $blog_summary, array( 'the_excerpt', 'the_content' ) ) ) {
		$blog_summary = 'the_excerpt';
	}

	return $blog_summary;
}

function helium_customizer_sanitize_blog_layout( $blog_layout ) {
	if( ! in_array( $blog_layout, array( 'boxed', 'fullwidth' ) ) ) {
		$blog_layout = 'boxed';
	}

	return $blog_layout;
}

function helium_customizer_sanitize_pagination( $pagination ) {
	if( ! in_array( $pagination, array( 'ajax', 'infinite', 'numbered', 'prev_next', 'show_all' ) ) ) {
		$pagination = 'ajax';
	}

	return $pagination;
}

function helium_customizer_sanitize_grid_behavior( $grid_behavior ) {
	if( ! in_array( $grid_behavior, array( 'none', 'lightbox', 'page' ) ) ) {
		$grid_behavior = 'lightbox';
	}

	return $grid_behavior;
}

function helium_customizer_sanitize_grid_meta_text( $meta_text ) {
	if( ! in_array( $meta_text, array( 'taxonomy', 'excerpt' ) ) ) {
		$meta_text = 'taxonomy';
	}

	return $meta_text;
}

function helium_customizer_sanitize_grid_orderby( $orderby ) {
	if( ! in_array( $orderby, array( 'date', 'menu_order', 'title', 'ID' ) ) ) {
		$orderby = 'date';
	}

	return $orderby;
}

function helium_customizer_sanitize_grid_order( $order ) {
	if( ! in_array( strtolower( $order ), array( 'ASC', 'DESC' ) ) ) {
		$order = 'DESC';
	}

	return $order;
}

function helium_customizer_sanitize_portfolio_layout( $portfolio_layout ) {
	if( ! in_array( $portfolio_layout, array( 'masonry', 'classic', 'justified' ) ) ) {
		$portfolio_layout = 'masonry';
	}

	return $portfolio_layout;
}

function helium_customizer_sanitize_portfolio_categories( $categories ) {
	$terms = get_terms( Youxi_Portfolio::taxonomy_name(), array( 'fields' => 'id=>name', 'hide_empty' => false ) );
	return array_intersect_key( $categories, $terms );
}

function helium_customizer_sanitize_edd_categories( $categories ) {
	$terms = get_terms( 'download_category', array( 'fields' => 'id=>name', 'hide_empty' => false ) );
	return array_intersect_key( $categories, $terms );
}

function helium_customizer_sanitize_noop( $var ) {
	return $var;
}
