<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * Youxi Widget Area List Table class
 *
 * This is a WordPress list table class to display widget areas
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2016, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Widget_Area_List_Table' ) ) :

	if( ! class_exists( 'WP_List_Table' ) )
		require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

	class Youxi_Widget_Area_List_Table extends WP_List_Table {

		public function __construct() {

			parent::__construct( array(
				'singular' => 'widget-area', 
				'plural'   => 'widget-areas', 
				'ajax'     => false
			));
		}

		public function no_items() {
			esc_html_e( 'No widget areas found.', 'youxi' );
		}

		public function get_columns() {
			return array(
				'cb'          => '<input type="checkbox" />', 
				'name'        => esc_html__( 'Name', 'youxi' ), 
				'description' => esc_html__( 'Description', 'youxi' )
			);
		}

		public function prepare_items() {

			$total_items  = Youxi_Widget_Area::count();
			$per_page     = $this->get_items_per_page( 'youxi_widget_areas_per_page', 10 );
			$current_page = $this->get_pagenum();

			$this->set_pagination_args( compact( 'total_items', 'per_page' ) );

			$this->items = Youxi_Widget_Area::get( $per_page, $current_page );
		}

		protected function column_default( $item, $column_name ) {
			return $item[ $column_name ];
		}

		protected function get_bulk_actions() {
			return array( 'bulk-delete' => esc_html__( 'Delete', 'youxi' ) );
		}

		protected function column_cb( $item ) {
			return sprintf( '<input type="checkbox" name="widget_areas[]" value="%s" />', esc_attr( $item['id'] ) );
		}

		protected function get_sortable_columns() {
			return array( 'name' => 'name' );
		}

		protected function handle_row_actions( $item, $column_name, $primary ) {

			if( $column_name != $primary ) {
				return '';
			}

			$delete_url = add_query_arg( array(
				'action' => 'delete', 
				'widget_area_id' => $item['id']
			));

			return $this->row_actions( array(
				'delete' => sprintf(
					'<a href="%s" class="delete-widget-area aria-button-if-js" aria-label="%s">%s</a>',
					esc_url( wp_nonce_url( $delete_url, 'delete-widget-area_' . $item['id'] ) ), 
					esc_attr( sprintf( __( 'Delete &#8220;%s&#8221;', 'youxi' ), $item['name'] ) ),
					esc_html__( 'Delete', 'youxi' )
				)
			));
		}
	}
endif;
