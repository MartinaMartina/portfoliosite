<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

class Youxi_Importer_Task_Nav_Menu_Locations extends Youxi_Importer_Task {

	public function messages() {
		return array(
			'status' => esc_html__( 'Importing nav menu locations', 'youxi' ), 
		);
	}

	public function run( $params ) {

		$menu_locations = get_nav_menu_locations();
		$new_menu_locations = array();

		foreach( (array) $this->args as $menu_location => $slug ) {
			if( ! is_string( $slug ) ) {
				continue;
			}
			$menu = wp_get_nav_menus( compact( 'slug' ) );
			if( ! empty( $menu ) && isset( $menu[0] ) ) {
				$new_menu_locations[ $menu_location ] = $menu[0]->term_id;
			}
		}

		if( empty( $new_menu_locations ) ) {
			return esc_html__( 'No nav menu locations updated', 'youxi' );
		}

		set_theme_mod( 'nav_menu_locations', 
			array_merge( $menu_locations, $new_menu_locations ) );

		$result = array();
		foreach( $new_menu_locations as $location => $id ) {
			$result[] =  $location . ': ' . $id;
		}

		return empty( $result ) ? 
			esc_html__( 'No nav menu locations updated', 'youxi' ) : implode( ', ', $result );
	}
}
