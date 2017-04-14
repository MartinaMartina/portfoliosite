<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * [container] handler
 */
function youxi_shortcode_container_handler( $atts, $content, $tag ) {
	return '<div class="container">' . do_shortcode( $content ) . '</div>';
}

/**
 * [row] handler
 */
function youxi_shortcode_row_handler( $atts, $content, $tag ) {
	return '<div class="row">' . do_shortcode( $content ) . '</div>';
}

/**
 * [column] handler
 */
function youxi_shortcode_column_handler( $atts, $content, $tag ) {

	foreach( array_keys( Youxi_Shortcode::get_column_types() ) as $type ) {

		if( isset( $atts["size_{$type}"], $atts["offset_{$type}"] ) ) {

			$size   = $atts["size_{$type}"];
			$offset = $atts["offset_{$type}"];

			if( 'inherit' !== $size && is_numeric( $size ) ) {
				$size = min( max( absint( $atts["size_{$type}"] ), 1 ), Youxi_Shortcode::get_column_count() );
				if( 'xs' !== $type ) {
					$classes[] = "col-{$type}-{$size}";
				} else {
					$classes[] = "col-{$size}";
				}
			}

			if( 'inherit' !== $offset && is_numeric( $offset ) ) {
				$offset = min( max( absint( $atts["offset_{$type}"] ), 0 ), Youxi_Shortcode::get_column_count() );
				if( 'xs' !== $type ) {
					$classes[] = "offset-{$type}-{$offset}";
				} else {
					$classes[] = "offset-{$offset}";
				}
			}
		}
	}

	return '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">' . do_shortcode( $content ) . '</div>';
}

/**
 * [separator] handler
 */
function youxi_shortcode_separator_handler( $atts, $content, $tag ) {
	return '<hr>';
}

/**
 * Define Layout Shortcodes
 */
function youxi_define_layout_shortcodes( $manager ) {

	/**
	 * Layout Category
	 */
	$manager->add_category( 'layout', array(
		'label' => esc_html__( 'Layout Shortcodes', 'youxi' ), 
		'priority' => 10
	));

	/**
	 * [container] shortcode
	 */
	$manager->add_shortcode( 'container', array(
		'label' => esc_html__( 'Container', 'youxi' ), 
		'category' => 'layout', 
		'priority' => 20, 
		'icon' => 'fa fa-align-justify', 
		'callback' => 'youxi_shortcode_container_handler'
	));

	/**
	 * [col] shortcode
	 */
	$manager->add_shortcode( 'col', array(
		'label' => esc_html__( 'Column', 'youxi' ), 
		'category' => 'layout', 
		'priority' => 40, 
		'icon' => 'fa fa-th', 
		'callback' => 'youxi_shortcode_column_handler', 
		'atts' => Youxi_Shortcode::get_column_atts(), 
		'fieldsets' => Youxi_Shortcode::get_column_fieldsets()
	));

	/**
	 * [row] shortcode
	 */
	$manager->add_shortcode( 'row', array(
		'label' => esc_html__( 'Row', 'youxi' ), 
		'category' => 'layout', 
		'priority' => 160, 
		'icon' => 'fa fa-align-justify', 
		'callback' => 'youxi_shortcode_row_handler'
	));

	/**
	 * [separator] shortcode
	 */
	$manager->add_shortcode( 'separator', array(
		'label' => esc_html__( 'Separator', 'youxi' ), 
		'category' => 'layout', 
		'priority' => 170, 
		'icon' => 'fa fa-arrows-v', 
		'callback' => 'youxi_shortcode_separator_handler'
	));
}

/**
 * Hook to 'youxi_shortcode_register'
 */
add_action( 'youxi_shortcode_register', 'youxi_define_layout_shortcodes', 1 );
