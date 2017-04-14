<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * [button] handler
 */
function youxi_shortcode_button_handler( $atts, $content, $tag ) {

	extract( $atts, EXTR_PREFIX_ALL, 'button' );

	$button_classes = 'btn';

	if( in_array( $button_type, array( 'primary', 'secondary', 'success', 'info', 'warning', 'danger' ), true ) ) {

		$button_classes .= ' btn-' . $button_type;
		
		if( wp_validate_boolean( $button_outline ) ) {
			$button_classes .= '-outline';
		}
	}

	if( in_array( $button_size, array( 'lg', 'sm', 'block' ), true ) ) {
		$button_classes .= ' btn-' . $button_size;
	}

	return '<a href="' . esc_url( $button_url ) . '" class="' . esc_attr( $button_classes ) . '">' . strip_tags( $content ) . '</a>';
}

/**
 * [dropcap] handler
 */
function youxi_shortcode_dropcap_handler( $atts, $content, $tag ) {

	$content = strip_tags( $content );

	return '<strong>' . substr( $content, 0, 1 ) . '</strong>' . substr( $content, 1 );
}

/**
 * [badge] handler
 */
function youxi_shortcode_badge_handler( $atts, $content, $tag ) {

	extract( $atts, EXTR_PREFIX_ALL, 'badge' );

	$classes = 'badge';

	if( wp_validate_boolean( $badge_pill ) ) {
		$classes .= ' badge-pill';
	}

	if( in_array( $badge_type, array( 'default', 'primary', 'success', 'info', 'warning', 'danger' ), true ) ) {
		$classes .= ' badge-' . $badge_type;
	}

	return '<span class="' . esc_attr( $classes ) . '">' . strip_tags( $content ) . '</span>';
}

/**
 * [lead_text] handler
 */
function youxi_shortcode_lead_text_handler( $atts, $content, $tag ) {

	/* Remove all <p> tags first */
	$content = preg_replace( '/<\/?p[^>]*>/', '', $content );

	/* Fix shortcodes */
	$content = Youxi_Shortcode_Manager::get()->shortcode_unautop( $content );

	/* do_shortcode */
	$content = do_shortcode( $content );

	/* apply wpautop */
	return wpautop( '<p class="lead">' . $content . '</p>' );
}

/**
 * [tooltip] handler
 */
function youxi_shortcode_tooltip_handler( $atts, $content, $tag ) {

	extract( $atts, EXTR_PREFIX_ALL, 'tooltip' );

	$tooltip_tag = 'link' == $tooltip_type ? 'a' : 'span';

	if( is_string( $tooltip_trigger ) ) {
		$tooltip_trigger = explode( ',', $tooltip_trigger );
	}

	$attributes = array(
		'data-toggle'    => 'tooltip', 
		'title'          => $tooltip_title, 
		'data-trigger'   => implode( ' ', array_map( 'trim', $tooltip_trigger ) ), 
		'data-placement' => $tooltip_placement
	);
	if( 'link' == $tooltip_type ) {
		$attributes['href'] = esc_url( $tooltip_url );
	}

	$html = '';
	foreach( $attributes as $name => $value ) {
		$html .= " {$name}=\"" . esc_attr( $value ) . '"';
	}

	return '<' . $tooltip_tag . $html . '>' . wp_kses_post( $content ) . '</' . $tooltip_tag . '>';
}

/**
 * Define Uncategorized Shortcodes
 */
function youxi_define_uncategorized_shortcodes( $manager ) {

	/**
	 * [button] shortcode
	 */
	$manager->add_shortcode( 'button', array(
		'label' => esc_html__( 'Button', 'youxi' ), 
		'priority' => 10, 
		'inline' => true, 
		'insert_nl' => false, 
		'atts' => array(
			'type' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Type', 'youxi' ), 
				'description' => esc_html__( 'Choose here the button type.', 'youxi' ), 
				'choices' => array(
					'primary'   => esc_html__( 'Primary', 'youxi' ), 
					'secondary' => esc_html__( 'Secondary', 'youxi' ), 
					'success'   => esc_html__( 'Success', 'youxi' ), 
					'info'      => esc_html__( 'Info', 'youxi' ), 
					'warning'   => esc_html__( 'Warning', 'youxi' ), 
					'danger'    => esc_html__( 'Danger', 'youxi' ), 
				), 
				'std' => 'primary'
			), 
			'outline' => array(
				'type' => 'switch', 
				'label' => esc_html__( 'Outline Style', 'youxi' ), 
				'description' => esc_html__( 'Switch to display the button in an outline style.', 'youxi' ), 
				'std' => false, 
				'criteria' => 'type:not(default)'
			), 
			'size' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Size', 'youxi' ), 
				'description' => esc_html__( 'Choose here the button size.', 'youxi' ), 
				'choices' => array(
					0       => esc_html__( 'Default', 'youxi' ), 
					'lg'    => esc_html__( 'Large', 'youxi' ), 
					'sm'    => esc_html__( 'Small', 'youxi' ), 
					'block' => esc_html__( 'Block', 'youxi' )
				), 
				'std' => 0
			), 
			'url' => array(
				'type' => 'url', 
				'label' => esc_html__( 'URL', 'youxi' ), 
				'description' => esc_html__( 'Enter here the URL to visit after clicking the button.', 'youxi' ), 
				'std' => '#'
			)
		), 
		'content' => array(
			'type' => 'text', 
			'label' => esc_html__( 'Text', 'youxi' ), 
			'description' => esc_html__( 'Enter here the button text.', 'youxi' )
		), 
		'callback' => 'youxi_shortcode_button_handler'
	));

	/**
	 * [dropcap] shortcode
	 */
	$manager->add_shortcode( 'dropcap', array(
		'label' => esc_html__( 'Dropcap', 'youxi' ), 
		'priority' => 20, 
		'inline' => true, 
		'insert_nl' => false, 
		'content' => array(
			'type' => 'text', 
			'label' => esc_html__( 'Text', 'youxi' ), 
			'description' => esc_html__( 'Enter here the dropcap text.', 'youxi' )
		), 	
		'callback' => 'youxi_shortcode_dropcap_handler'
	));

	/**
	 * [badge] shortcode
	 */
	$manager->add_shortcode( 'badge', array(
		'label' => esc_html__( 'Badge', 'youxi' ), 
		'priority' => 30, 
		'inline' => true, 
		'insert_nl' => false, 
		'atts' => array(
			'type' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Type', 'youxi' ), 
				'description' => esc_html__( 'Specify the badge type.', 'youxi' ), 
				'choices' => array(
					'default' => esc_html__( 'Default', 'youxi' ), 
					'primary' => esc_html__( 'Primary', 'youxi' ), 
					'success' => esc_html__( 'Success', 'youxi' ), 
					'info'    => esc_html__( 'Info', 'youxi' ), 
					'warning' => esc_html__( 'Warning', 'youxi' ), 
					'danger'  => esc_html__( 'Danger', 'youxi' )
				), 
				'std' => 'default'
			), 
			'pill' => array(
				'type' => 'switch', 
				'label' => esc_html__( 'Display as Pill', 'youxi' ), 
				'description' => esc_html__( 'Switch to enable pill badges.', 'youxi' ), 
				'std' => false
			)
		), 
		'content' => array(
			'type' => 'text', 
			'label' => esc_html__( 'Text', 'youxi' ), 
			'description' => esc_html__( 'Enter here the badge text.', 'youxi' )
		), 
		'callback' => 'youxi_shortcode_badge_handler'
	));

	/**
	 * [lead_text] shortcode
	 */
	$manager->add_shortcode( 'lead_text', array(
		'label' => esc_html__( 'Lead Text', 'youxi' ), 
		'priority' => 40, 
		'callback' => 'youxi_shortcode_lead_text_handler'
	));

	/**
	 * [tooltip] shortcode
	 */
	$manager->add_shortcode( 'tooltip', array(
		'label' => esc_html__( 'Tooltip', 'youxi' ), 
		'priority' => 50, 
		'inline' => true, 
		'insert_nl' => false, 
		'atts' => array(
			'title' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Title', 'youxi' ), 
				'description' => esc_html__( 'Enter here the tooltip title.', 'youxi' )
			), 
			'placement' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Placement', 'youxi' ), 
				'description' => esc_html__( 'Choose here the tooltip position.', 'youxi' ), 
				'choices' => array(
					'top' => esc_html__( 'Top', 'youxi' ), 
					'bottom' => esc_html__( 'Bottom', 'youxi' ), 
					'left' => esc_html__( 'Left', 'youxi' ), 
					'right' => esc_html__( 'Right', 'youxi' )
				), 
				'std' => 'top'
			), 
			'trigger' => array(
				'type' => 'checkboxlist', 
				'label' => esc_html__( 'Trigger', 'youxi' ), 
				'description' => esc_html__( 'Choose here what action triggers the tooltip.', 'youxi' ), 
				'choices' => array(
					'click' => esc_html__( 'Click', 'youxi' ), 
					'hover' => esc_html__( 'Hover', 'youxi' ), 
					'focus' => esc_html__( 'Focus', 'youxi' )
				), 
				'std' => array( 'hover', 'focus' )
			), 
			'type' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Type', 'youxi' ), 
				'description' => esc_html__( 'Choose here what element to use to display the tooltip.', 'youxi' ), 
				'choices' => array(
					'link' => esc_html__( 'Link', 'youxi' ), 
					'text' => esc_html__( 'Text', 'youxi' )
				), 
				'std' => 'text'
			), 
			'url' => array(
				'type' => 'url', 
				'label' => esc_html__( 'URL', 'youxi' ), 
				'description' => esc_html__( 'Enter here the URL for the link tooltip.', 'youxi' ), 
				'criteria' => 'type:is(link)'
			)
		), 
		'content' => array(
			'type' => 'text', 
			'label' => esc_html__( 'Content', 'youxi' ), 
			'description' => esc_html__( 'Enter the content of the tooltip.', 'youxi' )
		), 
		'callback' => 'youxi_shortcode_tooltip_handler'
	));
}

/**
 * Hook to 'youxi_shortcode_register'
 */
add_action( 'youxi_shortcode_register', 'youxi_define_uncategorized_shortcodes', 1 );
