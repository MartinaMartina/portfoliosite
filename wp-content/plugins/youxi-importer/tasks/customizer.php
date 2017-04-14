<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

class Youxi_Importer_Task_Customizer extends Youxi_Importer_Task {

	public function __construct( $args ) {

		parent::__construct( $args );

		$this->args = wp_parse_args( $this->args, array(
			'data' => '', 
			'type' => 'theme_mod', 
			'key'  => ''
		));
	}

	public function priority() {
		return 12;
	}

	public function messages() {
		return array(
			'status' => esc_html__( 'Importing customizer options', 'youxi' )
		);
	}

	public function run( $params ) {

		try {

			$options = unserialize( $this->args['data'] );

			if( is_array( $options ) ) {
				if( 'theme_mod' == $this->args['type'] ) {
					set_theme_mod( $this->args['key'], $options );
				} else {
					update_option( $this->args['key'], $options );
				}
			} else {
				return new WP_Error( 'customizer_options_invalid_data', esc_html__( 'The supplied customizer options data is invalid.', 'youxi' ) );	
			}

		} catch( Exception $e ) {
			return new WP_Error( 'customizer_options_unknown_error', $e->getMessage() );
		}

		return esc_html__( 'Customizer options successfully imported.', 'youxi' );
	}
}
