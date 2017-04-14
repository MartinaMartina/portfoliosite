<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * [counter] handler
 */
function youxi_shortcode_counter_handler( $atts, $content, $tag ) {

	extract( $atts, EXTR_PREFIX_ALL, 'counter' );

	return esc_html( $content ) . '<br>' . esc_html( $counter_label );
}

/**
 * [progressbar] handler
 */
function youxi_shortcode_progressbar_handler( $atts, $content, $tag ) {

	extract( $atts, EXTR_PREFIX_ALL, 'progressbar' );

	$bar_classes = array( 'progress-bar' );

	if( wp_validate_boolean( $progressbar_striped ) ) {
		$bar_classes[] = "progress-bar-striped";
	}
	if( wp_validate_boolean( $progressbar_animated ) ) {
		$bar_classes[] = 'progress-bar-animated';
	}
	if( in_array( $progressbar_type, array( 'success', 'info', 'warning', 'danger' ), true ) ) {
		$bar_classes[] = "bg-{$progressbar_type}";
	}

	$bar_classes = implode( ' ', $bar_classes );

	$o = '<div class="progress">';

		$o .= '<div class="' . esc_attr( $bar_classes ) . '" role="progressbar" aria-valuenow="' . esc_attr( $progressbar_value ) . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . esc_attr( $progressbar_value ) . '%"></div>';

	$o .= '</div>';

	return $o;
}

/**
 * Define Statistic Shortcodes
 */
function youxi_define_statistic_shortcodes( $manager ) {

	/**
	 * Statistic Category
	 */
	$manager->add_category( 'statistic', array(
		'label' => esc_html__( 'Statistic Shortcodes', 'youxi' ), 
		'priority' => 30
	));

	/**
	 * [counter] shortcode
	 */
	$manager->add_shortcode( 'counter', array(
		'label' => esc_html__( 'Counter', 'youxi' ), 
		'category' => 'statistic', 
		'priority' => 10, 
		'icon' => 'fa fa-clock-o', 
		'insert_nl' => false, 
		'atts' => array(
			'label' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Label', 'youxi' ), 
				'description' => esc_html__( 'Specify the counter label.', 'youxi' )
			)
		), 
		'content' => array(
			'type' => 'number', 
			'label' => esc_html__( 'Value', 'youxi' ), 
			'description' => esc_html__( 'Specify the counter value.', 'youxi' ), 
			'min' => 0, 
			'step' => 0.1, 
			'std' => 0
		), 
		'callback' => 'youxi_shortcode_counter_handler'
	));

	/**
	 * [progressbar] shortcode
	 */
	$manager->add_shortcode( 'progressbar', array(
		'label' => esc_html__( 'Progressbar', 'youxi' ), 
		'category' => 'statistic', 
		'priority' => 20, 
		'icon' => 'fa fa-tasks', 
		'atts' => array(
			'type' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Type', 'youxi' ), 
				'description' => esc_html__( 'Choose the type of the progressbar.', 'youxi' ), 
				'choices' => array(
					0         => esc_html__( 'Default', 'youxi' ), 
					'success' => esc_html__( 'Success', 'youxi' ), 
					'info'    => esc_html__( 'Info', 'youxi' ), 
					'warning' => esc_html__( 'Warning', 'youxi' ), 
					'danger'  => esc_html__( 'Danger', 'youxi' )
				), 
				'std' => 0
			), 
			'value' => array(
				'type' => 'uislider', 
				'label' => esc_html__( 'Value', 'youxi' ), 
				'description' => esc_html__( 'Specify the value of the progressbar.', 'youxi' ), 
				'std' => '100'
			), 
			'striped' => array(
				'type' => 'switch', 
				'label' => esc_html__( 'Show Stripes', 'youxi' ), 
				'description' => esc_html__( 'Switch to show stripes on the progressbar.', 'youxi' ), 
				'std' => true
			), 
			'animated' => array(
				'type' => 'switch', 
				'label' => esc_html__( 'Animate', 'youxi' ), 
				'description' => esc_html__( 'Switch to animate the progressbar.', 'youxi' ), 
				'std' => true
			)
		), 
		'callback' => 'youxi_shortcode_progressbar_handler'
	));
}

/**
 * Hook to 'youxi_shortcode_register'
 */
add_action( 'youxi_shortcode_register', 'youxi_define_statistic_shortcodes', 1 );
