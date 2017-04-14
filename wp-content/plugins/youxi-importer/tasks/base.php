<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

abstract class Youxi_Importer_Task {

	protected $js_params;

	protected $args;

	public function __construct( $args ) {
		$this->args = $args;
	}

	public function priority() {
		return 10;
	}

	public function js_params() {
		return $this->js_params;
	}

	public function messages() {
		return array(
			'status' => esc_html__( 'Importing', 'youxi' )
		);
	}

	abstract public function run( $params );
}
