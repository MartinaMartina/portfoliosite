<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/* Make sure WooCommerce is installed and activated */
if( ! class_exists( 'WooCommerce' ) ) {
	return;
}

if( ! function_exists( 'youxi_shortcode_woocommerce_product_cats' ) ) :

function youxi_shortcode_woocommerce_product_cats( $field, $index_key = null, $placeholder = null ) {
	$product_categories = get_terms( 'product_cat', array( 'hide_empty' => true ) );
	$product_categories = wp_list_pluck( $product_categories, $field, $index_key );
	return $placeholder ? ( array( $placeholder ) + $product_categories ) : $product_categories;
}
endif;

/**
 * Define WooCommerce Shortcodes
 */
function youxi_define_woocommerce_shortcodes( $manager ) {

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	/**
	 * WooCommerce Category
	 */
	$manager->add_category( 'woocommerce', array(
		'label' => esc_html__( 'WooCommerce Shortcodes', 'youxi' ), 
		'priority' => 50
	));

	/**
	 * [product] shortcode (done)
	 */
	$manager->add_shortcode( 'product', array(
		'label' => esc_html__( 'Product', 'youxi' ), 
		'category' => 'woocommerce', 
		'priority' => 10, 
		'icon' => 'fa fa-shopping-cart', 
		'third_party' => true, 
		'atts' => array(
			'id' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Product ID', 'youxi' ), 
				'description' => esc_html__( 'Specify the product ID you want to display.', 'youxi' )
			), 
			'sku' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Product SKU', 'youxi' ), 
				'description' => esc_html__( 'Specify the product SKU you want to display.', 'youxi' )
			)
		)
	));

	/**
	 * [product_page] shortcode (done)
	 */
	$manager->add_shortcode( 'product_page', array(
		'label' => esc_html__( 'Product Page', 'youxi' ), 
		'category' => 'woocommerce', 
		'priority' => 20, 
		'icon' => 'fa fa-shopping-cart', 
		'third_party' => true, 
		'atts' => array(
			'id' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Product ID', 'youxi' ), 
				'description' => esc_html__( 'Specify the product ID you want to display.', 'youxi' )
			), 
			'sku' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Product SKU', 'youxi' ), 
				'description' => esc_html__( 'Specify the product SKU you want to display.', 'youxi' )
			)
		)
	));

	/**
	 * [product_category] shortcode (done)
	 */
	$manager->add_shortcode( 'product_category', array(
		'label' => esc_html__( 'Product Category', 'youxi' ), 
		'category' => 'woocommerce', 
		'priority' => 30, 
		'icon' => 'fa fa-shopping-cart', 
		'third_party' => true, 
		'atts' => array(
			'per_page' => array(
				'type' => 'number', 
				'label' => esc_html__( 'Per Page', 'youxi' ), 
				'description' => esc_html__( 'Specify the number of products to show.', 'youxi' ), 
				'min' => 1, 
				'std' => 12
			), 
			'columns' => array(
				'type' => 'number', 
				'label' => esc_html__( 'Columns', 'youxi' ), 
				'description' => esc_html__( 'Specify the in how many columns the products should be displayed.', 'youxi' ), 
				'min' => 2, 
				'max' => 5, 
				'std' => 4
			), 
			'orderby' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Order By', 'youxi' ), 
				'description' => esc_html__( 'Choose the field used to order the products.', 'youxi' ), 
				'choices' => array(
					'menu_order' => esc_html__( 'Menu Order', 'youxi' ), 
					'title' => esc_html__( 'Product Title', 'youxi' ), 
					'date' => esc_html__( 'Product Date', 'youxi' ), 
					'rand' => esc_html__( 'Random', 'youxi' ), 
					'id' => esc_html__( 'Product ID', 'youxi' )
				), 
				'std' => 'title'
			), 
			'order' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Order', 'youxi' ), 
				'description' => esc_html__( 'Choose the way the products are ordered.', 'youxi' ), 
				'choices' => array(
					'asc' => esc_html__( 'Ascending', 'youxi' ), 
					'desc' => esc_html__( 'Descending', 'youxi' )
				), 
				'std' => 'asc'
			), 
			'category' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Product Category', 'youxi' ), 
				'description' => esc_html__( 'Specify the product category to display.', 'youxi' ), 
				'choices' => array( 'youxi_shortcode_woocommerce_product_cats', 'name', 'slug' )
			)
		)
	));

	/**
	 * [product_categories] shortcode
	 */
	$manager->add_shortcode( 'product_categories', array(
		'label' => esc_html__( 'Product Categories', 'youxi' ), 
		'category' => 'woocommerce', 
		'priority' => 40, 
		'icon' => 'fa fa-shopping-cart', 
		'third_party' => true, 
		'atts' => array(
			'number' => array(
				'type' => 'number', 
				'label' => esc_html__( 'Number', 'youxi' ), 
				'description' => esc_html__( 'Specify the number of product categories you want to display.', 'youxi' ), 
				'min' => 1
			), 
			'orderby' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Order By', 'youxi' ), 
				'description' => esc_html__( 'Choose the field used to order the product categories.', 'youxi' ), 
				'choices' => array(
					'count'   => esc_html__( 'Product Category Count', 'terschelling' ), 
					'name'    => esc_html__( 'Product Category Name', 'terschelling' ), 
					'slug'    => esc_html__( 'Product Category Slug', 'terschelling' ), 
					'term_id' => esc_html__( 'Product Category ID', 'terschelling' )
				), 
				'std' => 'name'
			), 
			'order' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Order', 'youxi' ), 
				'description' => esc_html__( 'Choose the way the product categories are ordered.', 'youxi' ), 
				'choices' => array(
					'asc' => esc_html__( 'Ascending', 'youxi' ), 
					'desc' => esc_html__( 'Descending', 'youxi' )
				), 
				'std' => 'asc'
			), 
			'columns' => array(
				'type' => 'number', 
				'label' => esc_html__( 'Columns', 'youxi' ), 
				'description' => esc_html__( 'Specify the in how many columns the products should be displayed.', 'youxi' ), 
				'min' => 2, 
				'max' => 5, 
				'std' => 4
			), 
			'parent' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Parent Category', 'youxi' ), 
				'description' => esc_html__( 'Specify the parent of the product categories to display.', 'youxi' ), 
				'choices' => array( 'youxi_shortcode_woocommerce_product_cats', 'name', 'term_id', esc_html__( 'None', 'youxi' ) ), 
				'serialize' => 'js:function( data ) {
					return 0 == data ? "" : data;
				}'
			), 
			'ids' => array(
				'type' => 'checkboxlist', 
				'label' => esc_html__( 'Categories', 'youxi' ), 
				'description' => esc_html__( 'Specify the product categories to display.', 'youxi' ), 
				'choices' => array( 'youxi_shortcode_woocommerce_product_cats', 'name', 'term_id' ), 
				'serialize' => 'js:function( data ) {
					return ( data || [] ).join( "," );
				}', 
				'deserialize' => 'js:function( data ) {
					return ( data + "" ).split( "," )
				}'
			)
		)
	));

	/**
	 * [add_to_cart] shortcode (done)
	 */
	$manager->add_shortcode( 'add_to_cart', array(
		'label' => esc_html__( 'Add to Cart', 'youxi' ), 
		'category' => 'woocommerce', 
		'priority' => 50, 
		'icon' => 'fa fa-shopping-cart', 
		'third_party' => true, 
		'atts' => array(
			'id' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Product ID', 'youxi' ), 
				'description' => esc_html__( 'Specify the product ID of the add to cart element.', 'youxi' )
			), 
			'class' => array(
				'type' => 'text', 
				'label' => esc_html__( 'CSS Class', 'youxi' ), 
				'description' => esc_html__( 'Specify the CSS class of the add to cart element.', 'youxi' )
			), 
			'sku' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Product SKU', 'youxi' ), 
				'description' => esc_html__( 'Specify the product SKU of the add to cart element.', 'youxi' )
			), 
			'style' => array(
				'type' => 'text', 
				'label' => esc_html__( 'CSS Styles', 'youxi' ), 
				'description' => esc_html__( 'Specify the CSS styles of the add to cart element.', 'youxi' ), 
				'std' => 'border:4px solid #ccc; padding: 12px;'
			), 
			'show_price' => array(
				'type' => 'switch', 
				'label' => esc_html__( 'Show Price', 'youxi' ), 
				'description' => esc_html__( 'Specify whether to show the product price on the add to cart element.', 'youxi' ), 
				'std' => true
			)
		)
	));

	/**
	 * [add_to_cart_url] shortcode (done)
	 */
	$manager->add_shortcode( 'add_to_cart_url', array(
		'label' => esc_html__( 'Add to Cart URL', 'youxi' ), 
		'category' => 'woocommerce', 
		'priority' => 60, 
		'icon' => 'fa fa-shopping-cart', 
		'third_party' => true, 
		'atts' => array(
			'id' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Product ID', 'youxi' ), 
				'description' => esc_html__( 'Specify the product ID of the add to cart URL.', 'youxi' )
			), 
			'sku' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Product SKU', 'youxi' ), 
				'description' => esc_html__( 'Specify the product SKU of the add to cart URL.', 'youxi' )
			)
		)
	));

	/**
	 * [products] shortcode (done)
	 */
	$manager->add_shortcode( 'products', array(
		'label' => esc_html__( 'Products', 'youxi' ), 
		'category' => 'woocommerce', 
		'priority' => 70, 
		'icon' => 'fa fa-shopping-cart', 
		'third_party' => true, 
		'atts' => array(
			'columns' => array(
				'type' => 'number', 
				'label' => esc_html__( 'Columns', 'youxi' ), 
				'description' => esc_html__( 'Specify the in how many columns the products should be displayed.', 'youxi' ), 
				'min' => 2, 
				'max' => 5, 
				'std' => 4
			), 
			'orderby' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Order By', 'youxi' ), 
				'description' => esc_html__( 'Choose the field used to order the products.', 'youxi' ), 
				'choices' => array(
					'menu_order' => esc_html__( 'Menu Order', 'youxi' ), 
					'title' => esc_html__( 'Product Title', 'youxi' ), 
					'date' => esc_html__( 'Product Date', 'youxi' ), 
					'rand' => esc_html__( 'Random', 'youxi' ), 
					'id' => esc_html__( 'Product ID', 'youxi' )
				), 
				'std' => 'title'
			), 
			'order' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Order', 'youxi' ), 
				'description' => esc_html__( 'Choose the way the products are ordered.', 'youxi' ), 
				'choices' => array(
					'asc' => esc_html__( 'Ascending', 'youxi' ), 
					'desc' => esc_html__( 'Descending', 'youxi' )
				), 
				'std' => 'asc'
			), 
			'ids' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Product IDs', 'youxi' ), 
				'description' => esc_html__( 'Enter here the product IDs you want to display.', 'youxi' )
			), 
			'skus' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Product SKUs', 'youxi' ), 
				'description' => esc_html__( 'Enter here the product SKUs you want to display.', 'youxi' )
			)
		)
	));

	/**
	 * [recent_products] shortcode (done)
	 */
	$manager->add_shortcode( 'recent_products', array(
		'label' => esc_html__( 'Recent Products', 'youxi' ), 
		'category' => 'woocommerce', 
		'priority' => 80, 
		'icon' => 'fa fa-shopping-cart', 
		'third_party' => true, 
		'atts' => array(
			'per_page' => array(
				'type' => 'number', 
				'label' => esc_html__( 'Per Page', 'youxi' ), 
				'description' => esc_html__( 'Specify the number of recent products to show.', 'youxi' ), 
				'min' => 1, 
				'std' => 12
			), 
			'columns' => array(
				'type' => 'number', 
				'label' => esc_html__( 'Columns', 'youxi' ), 
				'description' => esc_html__( 'Specify the in how many columns the recent products should be displayed.', 'youxi' ), 
				'min' => 2, 
				'max' => 5, 
				'std' => 4
			), 
			'category' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Product Category', 'youxi' ), 
				'description' => esc_html__( 'Specify the product category to display.', 'youxi' ), 
				'choices' => array( 'youxi_shortcode_woocommerce_product_cats', 'name', 'slug', esc_html__( 'All', 'youxi' ) )
			)
		)
	));

	/**
	 * [sale_products] shortcode (done)
	 */
	$manager->add_shortcode( 'sale_products', array(
		'label' => esc_html__( 'Sale Products', 'youxi' ), 
		'category' => 'woocommerce', 
		'priority' => 90, 
		'icon' => 'fa fa-shopping-cart', 
		'third_party' => true, 
		'atts' => array(
			'per_page' => array(
				'type' => 'number', 
				'label' => esc_html__( 'Per Page', 'youxi' ), 
				'description' => esc_html__( 'Specify the number of sale products to show.', 'youxi' ), 
				'min' => 1, 
				'std' => 12
			), 
			'columns' => array(
				'type' => 'number', 
				'label' => esc_html__( 'Columns', 'youxi' ), 
				'description' => esc_html__( 'Specify the in how many columns the sale products should be displayed.', 'youxi' ), 
				'min' => 2, 
				'max' => 5, 
				'std' => 4
			), 
			'orderby' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Order By', 'youxi' ), 
				'description' => esc_html__( 'Choose the field used to order the sale products.', 'youxi' ), 
				'choices' => array(
					'menu_order' => esc_html__( 'Menu Order', 'youxi' ), 
					'title' => esc_html__( 'Product Title', 'youxi' ), 
					'date' => esc_html__( 'Product Date', 'youxi' ), 
					'rand' => esc_html__( 'Random', 'youxi' ), 
					'id' => esc_html__( 'Product ID', 'youxi' )
				), 
				'std' => 'title'
			), 
			'order' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Order', 'youxi' ), 
				'description' => esc_html__( 'Choose the way the sale products are ordered.', 'youxi' ), 
				'choices' => array(
					'asc' => esc_html__( 'Ascending', 'youxi' ), 
					'desc' => esc_html__( 'Descending', 'youxi' )
				), 
				'std' => 'asc'
			), 
			'category' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Product Category', 'youxi' ), 
				'description' => esc_html__( 'Specify the product category to display.', 'youxi' ), 
				'choices' => array( 'youxi_shortcode_woocommerce_product_cats', 'name', 'slug', esc_html__( 'All', 'youxi' ) )
			)
		)
	));

	/**
	 * [best_selling_products] shortcode (done)
	 */
	$manager->add_shortcode( 'best_selling_products', array(
		'label' => esc_html__( 'Best Selling Products', 'youxi' ), 
		'category' => 'woocommerce', 
		'priority' => 100, 
		'icon' => 'fa fa-shopping-cart', 
		'third_party' => true, 
		'atts' => array(
			'per_page' => array(
				'type' => 'number', 
				'label' => esc_html__( 'Per Page', 'youxi' ), 
				'description' => esc_html__( 'Specify the number of best selling products to show.', 'youxi' ), 
				'min' => 1, 
				'std' => 12
			), 
			'columns' => array(
				'type' => 'number', 
				'label' => esc_html__( 'Columns', 'youxi' ), 
				'description' => esc_html__( 'Specify the in how many columns the best selling products should be displayed.', 'youxi' ), 
				'min' => 2, 
				'max' => 5, 
				'std' => 4
			), 
			'category' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Product Category', 'youxi' ), 
				'description' => esc_html__( 'Specify the product category to display.', 'youxi' ), 
				'choices' => array( 'youxi_shortcode_woocommerce_product_cats', 'name', 'slug', esc_html__( 'All', 'youxi' ) )
			)
		)
	));

	/**
	 * [top_rated_products] shortcode (done)
	 */
	$manager->add_shortcode( 'top_rated_products', array(
		'label' => esc_html__( 'Top Rated Products', 'youxi' ), 
		'category' => 'woocommerce', 
		'priority' => 110, 
		'icon' => 'fa fa-shopping-cart', 
		'third_party' => true, 
		'atts' => array(
			'per_page' => array(
				'type' => 'number', 
				'label' => esc_html__( 'Per Page', 'youxi' ), 
				'description' => esc_html__( 'Specify the number of top rated products to show.', 'youxi' ), 
				'min' => 1, 
				'std' => 12
			), 
			'columns' => array(
				'type' => 'number', 
				'label' => esc_html__( 'Columns', 'youxi' ), 
				'description' => esc_html__( 'Specify the in how many columns the top rated products should be displayed.', 'youxi' ), 
				'min' => 2, 
				'max' => 5, 
				'std' => 4
			), 
			'orderby' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Order By', 'youxi' ), 
				'description' => esc_html__( 'Choose the field used to order the top rated products.', 'youxi' ), 
				'choices' => array(
					'menu_order' => esc_html__( 'Menu Order', 'youxi' ), 
					'title' => esc_html__( 'Product Title', 'youxi' ), 
					'date' => esc_html__( 'Product Date', 'youxi' ), 
					'rand' => esc_html__( 'Random', 'youxi' ), 
					'id' => esc_html__( 'Product ID', 'youxi' )
				), 
				'std' => 'title'
			), 
			'order' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Order', 'youxi' ), 
				'description' => esc_html__( 'Choose the way the top rated products are ordered.', 'youxi' ), 
				'choices' => array(
					'asc' => esc_html__( 'Ascending', 'youxi' ), 
					'desc' => esc_html__( 'Descending', 'youxi' )
				), 
				'std' => 'asc'
			), 
			'category' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Product Category', 'youxi' ), 
				'description' => esc_html__( 'Specify the product category to display.', 'youxi' ), 
				'choices' => array( 'youxi_shortcode_woocommerce_product_cats', 'name', 'slug', esc_html__( 'All', 'youxi' ) )
			)
		)
	));

	/**
	 * [featured_products] shortcode (done)
	 */
	$manager->add_shortcode( 'featured_products', array(
		'label' => esc_html__( 'Featured Products', 'youxi' ), 
		'category' => 'woocommerce', 
		'priority' => 120, 
		'icon' => 'fa fa-shopping-cart', 
		'third_party' => true, 
		'atts' => array(
			'per_page' => array(
				'type' => 'number', 
				'label' => esc_html__( 'Per Page', 'youxi' ), 
				'description' => esc_html__( 'Specify the number of featured products to show.', 'youxi' ), 
				'min' => 1, 
				'std' => 12
			), 
			'columns' => array(
				'type' => 'number', 
				'label' => esc_html__( 'Columns', 'youxi' ), 
				'description' => esc_html__( 'Specify the in how many columns the featured products should be displayed.', 'youxi' ), 
				'min' => 2, 
				'max' => 5, 
				'std' => 4
			), 
			'orderby' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Order By', 'youxi' ), 
				'description' => esc_html__( 'Choose the field used to order the featured products.', 'youxi' ), 
				'choices' => array(
					'menu_order' => esc_html__( 'Menu Order', 'youxi' ), 
					'title' => esc_html__( 'Product Title', 'youxi' ), 
					'date' => esc_html__( 'Product Date', 'youxi' ), 
					'rand' => esc_html__( 'Random', 'youxi' ), 
					'id' => esc_html__( 'Product ID', 'youxi' )
				), 
				'std' => 'date'
			), 
			'order' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Order', 'youxi' ), 
				'description' => esc_html__( 'Choose the way the featured products are ordered.', 'youxi' ), 
				'choices' => array(
					'asc' => esc_html__( 'Ascending', 'youxi' ), 
					'desc' => esc_html__( 'Descending', 'youxi' )
				), 
				'std' => 'desc'
			), 
			'category' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Product Category', 'youxi' ), 
				'description' => esc_html__( 'Specify the product category to display.', 'youxi' ), 
				'choices' => array( 'youxi_shortcode_woocommerce_product_cats', 'name', 'slug', esc_html__( 'All', 'youxi' ) )
			)
		)
	));

	/**
	 * [product_attribute] shortcode (done)
	 */
	$manager->add_shortcode( 'product_attribute', array(
		'label' => esc_html__( 'Product Attribute', 'youxi' ), 
		'category' => 'woocommerce', 
		'priority' => 130, 
		'icon' => 'fa fa-shopping-cart', 
		'third_party' => true, 
		'atts' => array(
			'per_page' => array(
				'type' => 'number', 
				'label' => esc_html__( 'Per Page', 'youxi' ), 
				'description' => esc_html__( 'Specify the number of products to show.', 'youxi' ), 
				'min' => 1, 
				'std' => 12
			), 
			'columns' => array(
				'type' => 'number', 
				'label' => esc_html__( 'Columns', 'youxi' ), 
				'description' => esc_html__( 'Specify the in how many columns the products should be displayed.', 'youxi' ), 
				'min' => 2, 
				'max' => 5, 
				'std' => 4
			), 
			'orderby' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Order By', 'youxi' ), 
				'description' => esc_html__( 'Choose the field used to order the products.', 'youxi' ), 
				'choices' => array(
					'menu_order' => esc_html__( 'Menu Order', 'youxi' ), 
					'title' => esc_html__( 'Product Title', 'youxi' ), 
					'date' => esc_html__( 'Product Date', 'youxi' ), 
					'rand' => esc_html__( 'Random', 'youxi' ), 
					'id' => esc_html__( 'Product ID', 'youxi' )
				), 
				'std' => 'title'
			), 
			'order' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Order', 'youxi' ), 
				'description' => esc_html__( 'Choose the way the products are ordered.', 'youxi' ), 
				'choices' => array(
					'asc' => esc_html__( 'Ascending', 'youxi' ), 
					'desc' => esc_html__( 'Descending', 'youxi' )
				), 
				'std' => 'asc'
			), 
			'attribute' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Product Attribute', 'youxi' ), 
				'description' => esc_html__( 'Specify the product attribute to apply the filter.', 'youxi' )
			), 
			'filter' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Product Attribute Filter', 'youxi' ), 
				'description' => esc_html__( 'Specify the product attribute filter.', 'youxi' )
			)
		)
	));

	/**
	 * [related_products] shortcode (done)
	 */
	$manager->add_shortcode( 'related_products', array(
		'label' => esc_html__( 'Related Products', 'youxi' ), 
		'category' => 'woocommerce', 
		'priority' => 140, 
		'icon' => 'fa fa-shopping-cart', 
		'third_party' => true, 
		'atts' => array(
			'per_page' => array(
				'type' => 'number', 
				'label' => esc_html__( 'Per Page', 'youxi' ), 
				'description' => esc_html__( 'Specify the number of related products to show.', 'youxi' ), 
				'min' => 1, 
				'std' => 4
			), 
			'columns' => array(
				'type' => 'number', 
				'label' => esc_html__( 'Columns', 'youxi' ), 
				'description' => esc_html__( 'Specify the in how many columns the related products should be displayed.', 'youxi' ), 
				'min' => 2, 
				'max' => 5, 
				'std' => 4
			)
		)
	));
}

/**
 * Hook to 'youxi_shortcode_register'
 */
add_action( 'youxi_shortcode_register', 'youxi_define_woocommerce_shortcodes', 1 );
