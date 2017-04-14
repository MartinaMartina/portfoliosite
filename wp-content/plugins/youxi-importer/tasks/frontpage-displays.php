<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

class Youxi_Importer_Task_Frontpage_Displays extends Youxi_Importer_Task {

	public function __construct( $args ) {

		parent::__construct( $args );

		$this->args = wp_parse_args( $this->args, array(
			'show_on_front'  => 'posts', 
			'page_on_front'  => '', 
			'page_for_posts' => ''
		));
	}

	public function messages() {
		return array(
			'status' => esc_html__( 'Importing front page displays options', 'youxi' )
		);
	}

	public function run( $params ) {

		$result = array();
		
		foreach( array( 'show_on_front', 'page_on_front', 'page_for_posts' ) as $option ) {

			if( ! empty( $this->args[ $option ] ) ) {

				update_option( $option, $this->args[ $option ] );
				$result[] = $option . ': ' . $this->args[ $option ];
			}
		}

		return empty( $result ) ? 
			esc_html__( 'Frontpage display settings not imported.', 'youxi' ) : implode( ', ', $result );
	}
}
