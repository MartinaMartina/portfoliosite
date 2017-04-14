<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * [accordion] handler
 */
function youxi_shortcode_accordion_handler( $atts, $content, $tag ) {

	$accordion_id = Youxi_Shortcode::uniqid( 'accordion' );

	$o = '<div id="' . esc_attr( $accordion_id ) . '" class="accordion" role="tablist" aria-multiselectable="true">';

		$o .= do_shortcode( $content );

	$o .= '</div>';

	return $o;
}

/**
 * [accordion_group] handler
 */
function youxi_shortcode_accordion_group_handler( $atts, $content, $tag ) {

	extract( $atts, EXTR_PREFIX_ALL, 'accordion_group' );

	$parent_id    = Youxi_Shortcode::uniqid( 'accordion' );
	$accordion_id = Youxi_Shortcode::uniqid( $tag );
	
	$o = '<div class="accordion-panel panel card">';

		$o .= '<div class="accordion-header" role="tab" id="' . esc_attr( $accordion_id ) . '_label">';

			$o .= '<h4 class="accordion-title">';

				$o .= '<a data-toggle="collapse" class="collapsed" data-parent="#' . esc_attr( $parent_id ) . '"' . 
					' href="#' . esc_attr( $accordion_id ) . '" aria-controls="' . esc_attr( $accordion_id ) . '">';

				$o .= $accordion_group_title;

				$o .= '</a>';

			$o .= '</h4>';

		$o .= '</div>';

		$o .= '<div id="' . esc_attr( $accordion_id ) . '" class="accordion-content collapse" role="tabpanel" aria-labelledby="' . esc_attr( $accordion_id ) . '_label">';

			$o .= '<div class="accordion-content-inner">';

				$o .= wpautop( Youxi_Shortcode_Manager::get()->shortcode_unautop( do_shortcode( wp_kses_post( $content ) ) ) );

			$o .= '</div>';

		$o .= '</div>';

	$o .= '</div>';
	
	return $o;
}

/**
 * [alert] handler
 */
function youxi_shortcode_alert_handler( $atts, $content, $tag ) {

	extract( $atts, EXTR_PREFIX_ALL, 'alert' );

	$alert_classes = 'alert';

	if( in_array( $alert_type, array( 'success', 'info', 'warning', 'danger' ), true ) ) {
		$alert_classes .= " alert-{$alert_type}";
	}
	
	$o = '<div class="' . esc_attr( $alert_classes ) . '" role="alert">';

		$o .= '<button type="button" class="close" data-dismiss="alert" aria-label="' . esc_attr__( 'Close', 'youxi' ) . '">';
			$o .= '<span aria-hidden="true">&times;</span>';
		$o .= '</button>';

		$o .= wpautop( wp_kses_post( $content ) );

	$o .= '</div>';

	return $o;
}

/**
 * [call_to_action] handler
 */
function youxi_shortcode_call_to_action_handler( $atts, $content, $tag ) {

	extract( $atts, EXTR_PREFIX_ALL, 'call_to_action' );

	$call_to_action_btn_classes ='btn';

	if( in_array( $call_to_action_btn_type, array( 'primary', 'secondary', 'success', 'info', 'warning', 'danger' ), true ) ) {
		$call_to_action_btn_classes .= ' btn-' . $call_to_action_btn_type;
	}

	if( in_array( $call_to_action_btn_size, array( 'sm', 'lg', 'block' ), true ) ) {
		$call_to_action_btn_classes .= ' btn-' . $call_to_action_btn_size;
	}

	if( 'page' == $call_to_action_btn_action ):
		$call_to_action_url = get_permalink( $call_to_action_post_id );
		$call_to_action_url = $call_to_action_url ? $call_to_action_url : '#';
	endif;
	
	$o = '<div class="card card-block">';

		$o .= '<h3 class="card-title">' . $call_to_action_title . '</h3>';

		$o .= wpautop( wp_kses_post( $content ) );

		$o .= '<a href="' . esc_url( $call_to_action_url ) . '" class="' . esc_attr( $call_to_action_btn_classes ) . '">';

			$o .= $call_to_action_btn_text;

		$o .= '</a>';

	$o .= '</div>';
	
	return $o;
}

/**
 * [clients] handler
 */
function youxi_shortcode_clients_handler( $atts, $content, $tag ) {
	return '<ul class="list-group">' . do_shortcode( $content ) . '</ul>';
}

/**
 * [client] handler
 */
function youxi_shortcode_client_handler( $atts, $content, $tag ) {

	extract( $atts, EXTR_PREFIX_ALL, 'client' );

	$o = '<li class="list-group-item">';

		if( ! empty( $client_url ) ):

			$o .= '<a href="' . esc_url( $client_url ) . '" title="' . esc_attr( $client_name ) . '">';

				$o .= '<img src="' . esc_url( $client_logo ) . '" alt="' . esc_attr( $client_name ) . '">';

			$o .= '</a>';

		else:

			$o .= '<img src="' . esc_url( $client_logo ) . '" alt="' . esc_attr( $client_name ) . '">';

		endif;

	$o .= '</li>';

	return $o;
}

/**
 * [heading] handler
 */
function youxi_shortcode_heading_handler( $atts, $content, $tag ) {

	extract( $atts, EXTR_PREFIX_ALL, 'heading' );

	return '<' . $heading_element . '>' . wp_kses_post( $content ) . '</' . $heading_element . '>';
}

/**
 * [pricing_table] handler
 */
function youxi_shortcode_pricing_table_handler( $atts, $content, $tag ) {

	extract( $atts, EXTR_PREFIX_ALL, 'pricing_table' );

	$o = '<div class="card">';

		if( $pricing_table_featured ):

			$o .= '<div class="card-header">';

				$o .= esc_html_x( 'Featured', 'pricing table featured label', 'youxi' );

			$o .= '</div>';

		endif;

		$o .= '<div class="card-block">';

			$o .= '<h3 class="card-title">' . $pricing_table_title . '</h3>';

			if( $pricing_table_show_price ):

				$o .= '<p class="card-text text-' . esc_attr( $pricing_table_color ) . '">';

					$o .= esc_html( $pricing_table_currency );

					$o .= esc_html( $pricing_table_price );

					$o .= ' <small class="text-muted">' . esc_html( $pricing_table_price_description ) . '</small>';

				$o .= '</p>';

			endif;

		$o .= '</div>';

		$o .= $content;

		if( $pricing_table_show_btn ):

			if( 'page' == $pricing_table_btn_action ):
				$pricing_table_url = get_permalink( $pricing_table_post_id );
				$pricing_table_url = $pricing_table_url ? $pricing_table_url : '#';
			endif;

			$o .= '<div class="card-block">';

				$o .= '<a href="' . esc_url( $pricing_table_url ) . '" class="btn btn-' . esc_attr( $pricing_table_color ) . '">';

					$o .= $pricing_table_btn_text;

				$o .= '</a>';

			$o .= '</div>';

		endif;

	$o .= '</div>';

	return $o;
}

/**
 * [service] handler
 */
function youxi_shortcode_service_handler( $atts, $content, $tag ) {

	extract( $atts, EXTR_PREFIX_ALL, 'service' );

	$o = '<div class="card card-block">';

		$o .= '<h3 class="card-title">' . $service_title . '</h3>';

		$o .= wpautop( wp_kses_post( $content ) );

		if( $service_show_btn ):

			if( 'page' == $service_btn_action ):
				$service_url = get_permalink( $service_post_id );
				$service_url = $service_url ? $service_url : '#';
			endif;

			/* Compile button classes */
			$service_btn_classes ='btn';

			if( in_array( $service_btn_type, array( 'primary', 'secondary', 'success', 'info', 'warning', 'danger' ), true ) ) {
				$service_btn_classes .= ' btn-' . $service_btn_type;
			}

			if( in_array( $service_btn_size, array( 'sm', 'lg', 'block' ), true ) ) {
				$service_btn_classes .= ' btn-' . $service_btn_size;
			}

			$o .= '<a href="' . esc_url( $service_url ) . '" class="' . esc_attr( $service_btn_classes ) . '">';

				$o .= $service_btn_text;

			$o .= '</a>';

		endif;

	$o .= '</div>';

	return $o;
}

/**
 * [table] handler
 */
function youxi_shortcode_table_handler( $atts, $content, $tag ) {
	$tags = array(
		'table' => array(
			'tag' => 'table', 
			'allowed_tags' => array(
				'thead' => true, 
				'tbody' => true, 
				'tr' => true, 
				'td' => true, 
				'th' => true, 
				'a' => array(
					'href' => true, 
					'target' => true, 
					'title' => true
				), 
				'i' => true, 
				'em' => true, 
				'b' => true, 
				'strong' => true, 
				'strike' => true, 
				'ul' => true, 
				'ol' => true, 
				'li' => true
			)
		), 
		'table_head' => array(
			'tag' => 'thead', 
			'allowed_tags' => array(
				'tr' => true, 
				'th' => true
			)
		), 
		'table_body' => array(
			'tag' => 'tbody', 
			'allowed_tags' => array(
				'tbody' => true, 
				'tr' => true, 
				'td' => true, 
				'a' => array(
					'href' => true, 
					'target' => true, 
					'title' => true
				), 
				'i' => true, 
				'em' => true, 
				'b' => true, 
				'strong' => true, 
				'strike' => true, 
				'ul' => true, 
				'ol' => true, 
				'li' => true
			)
		), 
		'table_row' => array(
			'tag' => 'tr', 
			'allowed_tags' => array(
				'td' => true, 
				'th' => true
			)
		), 
		'table_cell' => array(
			'tag' => 'td', 
			'allowed_tags' => array(
				'a' => array(
					'href' => true, 
					'target' => true, 
					'title' => true
				), 
				'i' => true, 
				'em' => true, 
				'b' => true, 
				'strong' => true, 
				'strike' => true, 
				'ul' => true, 
				'ol' => true, 
				'li' => true
			)
		), 
		'table_header' => array(
			'tag' => 'th', 
			'allowed_tags' => array()
		)
	);

	if( isset( $tags[ $tag ] ) ) {

		extract( $tags[ $tag ] );

		$html = '';

		if( 'table' == $tag ) {

			extract( $atts, EXTR_PREFIX_ALL, 'table' );

			$table_class = 'table';

			foreach( explode( ',',  trim( $table_styles ) ) as $style ) {
				$table_class .= " table-{$style}";
			}

			$html = '<table class="' . esc_attr( $table_class ) . '">';

				$html .= do_shortcode( wp_kses_post( $content ) );

			$html .= '</table>';

			if( $table_responsive ) {

				$html = '<div class="table-responsive">' . $html . '</div>';
			}

		} else {

			$html = '<' . $tag . $html . '>';

				$html .= do_shortcode( wp_kses_post( $content ) );

			$html .= '</' . $tag . '>';

		}

		return $html;
	}
}

/**
 * [tabs] handler
 */
function youxi_shortcode_tabs_handler( $atts, $content, $tag ) {

	extract( $atts, EXTR_PREFIX_ALL, 'tabs' );

	$tabs = Youxi_Shortcode::to_array( $content, true );
	
	if( is_array( $tabs ) && ! empty( $tabs ) ) {

		$o = '<ul class="nav nav-' . esc_attr( $tabs_type ) . '">';
			
		foreach( $tabs as $index => $tab ) {

			if( isset( $tab['tag'], $tab['atts'] ) && Youxi_Shortcode::prefix( 'tab' ) == $tab['tag'] ) {

				extract( $tab['atts'], EXTR_PREFIX_ALL, 'tab' );

				$tab_id = sanitize_key( $tab_title . Youxi_Shortcode::count( 'tabs' ) . $index );
				
				$o .= '<li class="nav-item">';

					$o .= '<a class="nav-link' . esc_attr( $index ? '' : ' active' ) . '" data-toggle="tab"' . 
						' href="#' . esc_attr( $tab_id ) . '" role="tab" aria-controls="' . esc_attr( $tab_id ) . '">';

						$o .= $tab_title;

					$o .= '</a>';

				$o .= '</li>';
			}
		}

		$o .= '</ul>';

		/* Recount before rendering tabs */
		Youxi_Shortcode::recount( 'tab' );

		$o .= '<div class="tab-content">';

			$o .= do_shortcode( $content );

		return $o . '</div>';
	}

	return '';
}

/**
 * [tab] handler
 */
function youxi_shortcode_tab_handler( $atts, $content, $tag ) {

	extract( $atts, EXTR_PREFIX_ALL, 'tab' );

	$tab_classes = 'tab-pane fade';

	if( 0 == Youxi_Shortcode::count( 'tab' ) ) {
		$tab_classes .= ' in active show';
	}

	$tab_id = sanitize_key( $tab_title . Youxi_Shortcode::count( 'tabs' ) . Youxi_Shortcode::count( 'tab' ) );

	$o = '<div class="' . esc_attr( $tab_classes ) . '" id="' . esc_attr( $tab_id ) . '" role="tabpanel">';

		$o .= wpautop( Youxi_Shortcode_Manager::get()->shortcode_unautop( do_shortcode( wp_kses_post( $content ) ) ) );

	$o .= '</div>';
	
	return $o;
}

/**
 * [team] handler
 */
function youxi_shortcode_team_handler( $atts, $content, $tag ) {

	extract( $atts, EXTR_PREFIX_ALL, 'team' );
	
	$o = '<div class="card">';

		$o .= '<img class="card-img-top img-fluid" src="' . esc_url( $team_photo ) . '" alt="' . esc_attr( $team_name ) . '">';

		$o .= '<div class="card-block">';

			$o .= '<h3 class="card-title">' . $team_name . '</h3>';

			if( ! empty( $team_role ) ):

				$o .= '<p class="card-text text-muted">' . $team_role . '</p>';

			endif;

			$o .= wpautop( wp_kses_post( $content ) );

		$o .= '</div>';

	$o .= '</div>';

	return $o;
}

/**
 * [testimonials] handler
 */
function youxi_shortcode_testimonials_handler( $atts, $content, $tag ) {
	return do_shortcode( $content );
}

/**
 * [testimonial] handler
 */
function youxi_shortcode_testimonial_handler( $atts, $content, $tag ) {

	extract( $atts, EXTR_PREFIX_ALL, 'testimonial' );

	$o = '<blockquote>';

		$o .= wpautop( $content );

		if( ! empty( $testimonial_author ) ) : 

			$o .= '<footer>';

				$o .= '<cite>' . esc_html( $testimonial_author ) . '</cite>';

				if( ! empty( $testimonial_source ) ) : 

					if( ! empty( $testimonial_source_url ) ) : 
						$o .= ', <a href="' . esc_url( $testimonial_source_url ) . '">' . esc_html( $testimonial_source ) . '</a>';
					else:
						$o .= ', ' . esc_html( $testimonial_source );
					endif;

				endif;

			$o .= '</footer>';

		endif;

	$o .= '</blockquote>';

	return $o;
}

/**
 * [text_widget] handler
 */
function youxi_shortcode_text_widget_handler( $atts, $content, $tag ) {
	return do_shortcode( Youxi_Shortcode_Manager::get()->shortcode_unautop( wpautop( $content ) ) );
}

/**
 * [widget_area] handler
 */
function youxi_shortcode_widget_area_handler( $atts, $content, $tag ) {
	
	if( is_dynamic_sidebar( $atts['id'] ) ) {

		ob_start();

		dynamic_sidebar( $atts['id'] );

		return ob_get_clean();
	}

	return '';
}

/**
 * Fetch pages
 */
function youxi_shortcode_page_choices() {
	return wp_list_pluck( get_pages(), 'post_title', 'ID' );
}

/**
 * Fetch post categories
 */
function youxi_shortcode_post_categories() {
	return wp_list_pluck( get_categories( array( 'hide_empty' => false ) ), 'name', 'term_id' );
}

/**
 * Fetch post tags
 */
function youxi_shortcode_post_tags() {
	return wp_list_pluck( get_tags( array( 'hide_empty' => false ) ), 'name', 'term_id' );
}

/**
 * Fetch registered widget areas
 */
function youxi_shortcode_widget_area_choices() {
	global $wp_registered_sidebars;
	return apply_filters( 'youxi_shortcode_recognized_widget_areas', wp_list_pluck( $wp_registered_sidebars, 'name' ) );
}

/**
 * Fetch contact form 7 forms
 */
if( defined( 'WPCF7_VERSION' ) ):

function youxi_shortcode_cf7_forms() {

	$array = array();
	$forms = WPCF7_ContactForm::find();

	foreach( $forms as $form ) {
		if( version_compare( WPCF7_VERSION, '3.9' ) >= 0 ) {
			$array[ $form->id() ] = $form->title();
		} else {
			$array[ $form->id ] = $form->title;
		}
	}

	return $array;
}
endif;

/**
 * Define Content Shortcodes
 */
function youxi_define_content_shortcodes( $manager ) {

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	/**
	 * Content Category
	 */
	$manager->add_category( 'content', array(
		'label' => esc_html__( 'Content Shortcodes', 'youxi' ), 
		'priority' => 20
	));

	/**
	 * [accordion] shortcode
	 */
	$manager->add_shortcode( 'accordion', array(
		'label' => esc_html__( 'Accordion', 'youxi' ), 
		'category' => 'content', 
		'priority' => 10, 
		'icon' => 'fa fa-stack-overflow', 
		'content' => array(
			'type' => 'repeater', 
			'label' => esc_html__( 'Groups', 'youxi' ), 
			'description' => esc_html__( 'Enter here the title and content of each accordion.', 'youxi' ), 
			'fields' => array( array( $manager, 'get_shortcode_fields' ), 'accordion_group' ), 
			'preview_template' => '{{ data.title }}', 
			'serialize' => 'js:function( data ) {
				return this.construct( "accordion_group", data );
			}', 
			'deserialize' => 'js:function( data ) {
				return this.deserializeArray( data );
			}'
		), 
		'callback' => 'youxi_shortcode_accordion_handler'
	));
	$manager->add_shortcode( 'accordion_group', array(
		'label' => esc_html__( 'Accordion Group', 'youxi' ), 
		'category' => 'content', 
		'internal' => true, 
		'insert_nl' => false, 
		'atts' => array(
			'title' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Title', 'youxi' ), 
				'description' => esc_html__( 'Enter here the accordion title.', 'youxi' )
			)
		), 
		'content' => array(
			'type' => 'textarea', 
			'label' => esc_html__( 'Content', 'youxi' ), 
			'description' => esc_html__( 'Enter here the accordion content.', 'youxi' )
		), 
		'callback' => 'youxi_shortcode_accordion_group_handler'
	));

	/**
	 * [alert] shortcode
	 */
	$manager->add_shortcode( 'alert', array(
		'label' => esc_html__( 'Alert', 'youxi' ), 
		'category' => 'content', 
		'priority' => 20, 
		'icon' => 'fa fa-warning', 
		'insert_nl' => false, 
		'atts' => array(
			'type' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Alert Type', 'youxi' ), 
				'description' => esc_html__( 'Choose the alert type.', 'youxi' ), 
				'choices' => array(
					'success' => esc_html__( 'Success', 'youxi' ), 
					'info'    => esc_html__( 'Info', 'youxi' ), 
					'warning' => esc_html__( 'Warning', 'youxi' ), 
					'danger'  => esc_html__( 'Danger', 'youxi' )
				), 
				'std' => 'success'
			)
		), 
		'content' => array(
			'type' => 'richtext', 
			'label' => esc_html__( 'Content', 'youxi' ), 
			'description' => esc_html__( 'Enter here the alert\'s content.', 'youxi' ), 
			'tinymce' => array(
				'media_buttons' => false, 
				'tinymce' => false
			)
		), 
		'callback' => 'youxi_shortcode_alert_handler'
	));

	/**
	 * [call_to_action] shortcode
	 */
	$manager->add_shortcode( 'call_to_action', array(
		'label' => esc_html__( 'Call to Action', 'youxi' ), 
		'category' => 'content', 
		'priority' => 30, 
		'icon' => 'fa fa-hand-o-right', 
		'insert_nl' => false, 
		'atts' => array(
			'title' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Title', 'youxi' ), 
				'description' => esc_html__( 'Enter the title for this call to action box.', 'youxi' ), 
				'std' => ''
			), 
			'btn_text' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Button Text', 'youxi' ), 
				'description' => esc_html__( 'Enter the text to display on the button.', 'youxi' ), 
				'std' => ''
			), 
			'btn_type' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Button Type', 'youxi' ), 
				'description' => esc_html__( 'Choose the type of the button.', 'youxi' ), 
				'choices' => array(
					'primary'   => esc_html__( 'Primary', 'youxi' ), 
					'secondary' => esc_html__( 'Secondary', 'youxi' ), 
					'success'   => esc_html__( 'Success', 'youxi' ), 
					'info'      => esc_html__( 'Info', 'youxi' ), 
					'warning'   => esc_html__( 'Warning', 'youxi' ), 
					'danger'    => esc_html__( 'Danger', 'youxi' )
				), 
				'std' => 'primary'
			), 
			'btn_size' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Button Size', 'youxi' ), 
				'description' => esc_html__( 'Choose the size of the button.', 'youxi' ), 
				'choices' => array(
					0       => esc_html__( 'Default', 'youxi' ), 
					'lg'    => esc_html__( 'Large', 'youxi' ), 
					'sm'    => esc_html__( 'Small', 'youxi' ), 
					'block' => esc_html__( 'Block', 'youxi' )
				), 
				'std' => 0
			), 
			'btn_action' => array(
				'type' => 'radio', 
				'label' => esc_html__( 'Button Action', 'youxi' ), 
				'description' => esc_html__( 'Choose the action to execute after clicking the button.', 'youxi' ), 
				'choices' => array(
					'url'  => esc_html__( 'Go to URL', 'youxi' ), 
					'page' => esc_html__( 'Go to Page', 'youxi' )
				), 
				'std' => 'url'
			), 
			'post_id' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Page', 'youxi' ), 
				'description' => esc_html__( 'Choose the page to view after clicking the button.', 'youxi' ), 
				'choices' => 'youxi_shortcode_page_choices', 
				'criteria' => 'btn_action:is(page)'
			), 
			'url' => array(
				'type' => 'text', 
				'label' => esc_html__( 'URL', 'youxi' ), 
				'description' => esc_html__( 'Enter the URL to go to after clicking the button.', 'youxi' ), 
				'std' => '#', 
				'criteria' => 'btn_action:is(url)'
			)
		), 
		'content' => array(
			'type' => 'richtext', 
			'label' => esc_html__( 'Description', 'youxi' ), 
			'description' => esc_html__( 'Enter the content of this call to action box.', 'youxi' ), 
			'tinymce' => array(
				'media_buttons' => false, 
				'tinymce' => false
			)
		), 
		'callback' => 'youxi_shortcode_call_to_action_handler'
	));

	/**
	 * [clients] shortcode
	 */
	$manager->add_shortcode( 'clients', array(
		'label' => esc_html__( 'Clients', 'youxi' ), 
		'category' => 'content', 
		'priority' => 40, 
		'icon' => 'fa fa-group', 
		'content' => array(
			'type' => 'repeater', 
			'label' => esc_html__( 'Clients', 'youxi' ), 
			'description' => esc_html__( 'Enter here the client\'s data.', 'youxi' ), 
			'min' => 1, 
			'preview_template' => '<a href="{{ data.url }}" target="_blank">{{ data.name }}</a>', 
			'fields' => array( array( $manager, 'get_shortcode_fields' ), 'client' ), 
			'serialize' => 'js:function( data ) {
				return this.construct( "client", data );
			}', 
			'deserialize' => 'js:function( data ) {
				return this.deserializeArray( data );
			}'
		), 
		'callback' => 'youxi_shortcode_clients_handler'
	));
	$manager->add_shortcode( 'client', array(
		'label' => esc_html__( 'Client', 'youxi' ), 
		'category' => 'content', 
		'priority' => 40, 
		'icon' => 'fa fa-group', 
		'internal' => true, 
		'atts' => array(
			'name' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Name', 'youxi' ), 
				'description' => esc_html__( 'Enter here the client\'s name.', 'youxi' )
			), 
			'url' => array(
				'type' => 'url', 
				'label' => esc_html__( 'URL', 'youxi' ), 
				'description' => esc_html__( 'Enter here the client\'s URL.', 'youxi' )
			), 
			'logo' => array(
				'type' => 'image', 
				'label' => esc_html__( 'Logo', 'youxi' ), 
				'description' => esc_html__( 'Choose here the client\'s logo image.', 'youxi' ), 
				'return_type' => 'url', 
				'frame_title' => esc_html__( 'Choose an Image', 'youxi' ), 
				'frame_btn_text' => esc_html__( 'Insert Image', 'youxi' ), 
				'upload_btn_text' => esc_html__( 'Choose an Image', 'youxi' )
			)
		), 
		'callback' => 'youxi_shortcode_client_handler'
	));

	/**
	 * [contact-form-7] shortcode
	 */
	if( defined( 'WPCF7_VERSION' ) ) {

		$manager->add_shortcode( 'contact-form-7', array(
			'label' => esc_html__( 'Contact Form 7', 'youxi' ), 
			'category' => 'content', 
			'priority' => 50, 
			'icon' => 'fa fa-envelope-o', 
			'third_party' => true, 
			'atts' => array(
				'title' => array(
					'type' => 'text', 
					'label' => esc_html__( 'Title', 'youxi' ), 
					'description' => esc_html__( 'Enter here the title of the form.', 'youxi' )
				), 
				'id' => array(
					'type' => 'select', 
					'label' => esc_html__( 'Contact Form 7', 'youxi' ), 
					'description' => esc_html__( 'Choose a Contact Form 7 to display.', 'youxi' ), 
					'choices' => 'youxi_shortcode_cf7_forms'
				)
			)
		));
	}

	/**
	 * [heading] shortcode
	 */
	$manager->add_shortcode( 'heading', array(
		'label' => esc_html__( 'Heading', 'youxi' ), 
		'category' => 'content', 
		'priority' => 60, 
		'icon' => 'fa fa-font', 
		'insert_nl' => false, 
		'atts' => array(
			'element' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Heading Element', 'youxi' ), 
				'description' => esc_html__( 'Choose the HTML element to use for the heading.', 'youxi' ), 
				'choices' => array(
					'h1' => 'H1', 
					'h2' => 'H2', 
					'h3' => 'H3', 
					'h4' => 'H4', 
					'h5' => 'H5', 
					'h6' => 'H6'
				), 
				'std' => 'h1'
			)
		), 
		'content' => array(
			'type' => 'text', 
			'label' => esc_html__( 'Text', 'youxi' ), 
			'description' => esc_html__( 'Enter the heading text.', 'youxi' )
		), 
		'callback' => 'youxi_shortcode_heading_handler'
	));

	/**
	 * [icon_box] shortcode
	 */
	$manager->add_shortcode( 'icon_box', array(
		'label' => esc_html__( 'Icon Box', 'youxi' ), 
		'category' => 'content', 
		'priority' => 70, 
		'icon' => 'fa fa-smile-o', 
		'insert_nl' => false, 
		'atts' => array(
			'title' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Title', 'youxi' ), 
				'description' => esc_html__( 'Enter here the icon box\'s title.', 'youxi' )
			), 
			'icon' => array(
				'type' => 'iconchooser', 
				'label' => esc_html__( 'Icon', 'youxi' ), 
				'description' => esc_html__( 'Choose here the icon to display on the icon box.', 'youxi' )
			)
		), 
		'content' => array(
			'type' => 'richtext', 
			'label' => esc_html__( 'Content', 'youxi' ), 
			'description' => esc_html__( 'Enter here the icon box\'s content.', 'youxi' ), 
			'tinymce' => array(
				'media_buttons' => false, 
				'tinymce' => false
			)
		), 
		'callback' => '__return_empty_string'
	));

	/**
	 * [posts] shortcode
	 */
	$manager->add_shortcode( 'posts', array(
		'label' => esc_html__( 'Posts', 'youxi' ), 
		'category' => 'content', 
		'priority' => 80, 
		'icon' => 'fa fa-file-text-o', 
		'atts' => array(
			'category__in' => array(
				'type' => 'checkboxlist', 
				'label' => esc_html__( 'Included Categories', 'youxi' ), 
				'description' => esc_html__( 'Choose here the post categories to include (leave unchecked to include all).', 'youxi' ), 
				'choices' => 'youxi_shortcode_post_categories', 
				'serialize' => 'js:function( data ) {
					return ( data || [] ).join( "," );
				}', 
				'deserialize' => 'js:function( data ) {
					return ( data + "" ).split( "," )
				}'
			), 
			'tag__in' => array(
				'type' => 'checkboxlist', 
				'label' => esc_html__( 'Included Tags', 'youxi' ), 
				'description' => esc_html__( 'Choose here the post tags to include (leave unchecked to include all).', 'youxi' ), 
				'choices' => 'youxi_shortcode_post_tags', 
				'serialize' => 'js:function( data ) {
					return ( data || [] ).join( "," );
				}', 
				'deserialize' => 'js:function( data ) {
					return ( data + "" ).split( "," )
				}'
			), 
			'posts_per_page' => array(
				'type' => 'number', 
				'label' => esc_html__( 'Posts Per Page', 'youxi' ), 
				'description' => esc_html__( 'Choose how many posts to retrieve.', 'youxi' ), 
				'min' => 1, 
				'max' => 20, 
				'std' => 10
			), 
			'orderby' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Order By', 'youxi' ), 
				'description' => esc_html__( 'Choose which parameter to use for ordering the retrieved posts.', 'youxi' ), 
				'choices' => array(
					'ID'    => esc_html__( 'Post ID', 'youxi' ), 
					'title' => esc_html__( 'Post Title', 'youxi' ), 
					'name'  => esc_html__( 'Post Slug', 'youxi' ), 
					'date'  => esc_html__( 'Date', 'youxi' ), 
					'rand'  => esc_html__( 'Random Order', 'youxi' )
				), 
				'std' => 'date'
			), 
			'order' => array(
				'type' => 'radio', 
				'label' => esc_html__( 'Order', 'youxi' ), 
				'description' => esc_html__( 'Choose the ascending/descending order of the orderby parameter.', 'youxi' ), 
				'choices' => array(
					'DESC' => esc_html__( 'Descending', 'youxi' ), 
					'ASC'  => esc_html__( 'Ascending', 'youxi' )
				), 
				'std' => 'DESC'
			)
		), 
		'callback' => '__return_empty_string'
	));
	
	/**
	 * [pricing_table] shortcode
	 */
	$manager->add_shortcode( 'pricing_table', array(
		'label' => esc_html__( 'Pricing Table', 'youxi' ), 
		'category' => 'content', 
		'priority' => 90, 
		'icon' => 'fa fa-usd', 
		'atts' => array(
			'title' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Title', 'youxi' ), 
				'description' => esc_html__( 'Enter here the table title.', 'youxi' )
			), 
			'show_price' => array(
				'type' => 'switch', 
				'label' => esc_html__( 'Show Price', 'youxi' ), 
				'description' => esc_html__( 'Switch to hide/show the price.', 'youxi' ), 
				'std' => true
			), 
			'currency' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Currency Symbol', 'youxi' ), 
				'description' => esc_html__( 'Enter here the currency symbol.', 'youxi' ), 
				'std' => '$', 
				'criteria' => 'show_price:is(1)'
			), 
			'price' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Price', 'youxi' ), 
				'description' => esc_html__( 'Enter here the price value.', 'youxi' ), 
				'std' => 0.0, 
				'criteria' => 'show_price:is(1)'
			), 
			'price_description' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Price Description', 'youxi' ), 
				'description' => esc_html__( 'Enter here a little note to display below the price.', 'youxi' ), 
				'std' => 'per month', 
				'criteria' => 'show_price:is(1)'
			), 
			'show_btn' => array(
				'type' => 'switch', 
				'label' => esc_html__( 'Show Button', 'youxi' ), 
				'description' => esc_html__( 'Switch to hide/show the button.', 'youxi' ), 
				'std' => true
			), 
			'btn_action' => array(
				'type' => 'radio', 
				'label' => esc_html__( 'Button Action', 'youxi' ), 
				'description' => esc_html__( 'Choose the action to execute after clicking the button.', 'youxi' ), 
				'choices' => array(
					'url'  => esc_html__( 'Go to URL', 'youxi' ), 
					'page' => esc_html__( 'Go to Page', 'youxi' )
				), 
				'std' => 'url', 
				'criteria' => 'show_btn:is(1)'
			), 
			'post_id' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Page', 'youxi' ), 
				'description' => esc_html__( 'Choose the page to view after clicking the button.', 'youxi' ), 
				'choices' => 'youxi_shortcode_page_choices', 
				'criteria' => 'btn_action:is(page),show_btn:is(1)'
			), 
			'url' => array(
				'type' => 'text', 
				'label' => esc_html__( 'URL', 'youxi' ), 
				'description' => esc_html__( 'Enter the URL to go to after clicking the button.', 'youxi' ), 
				'std' => '#', 
				'criteria' => 'btn_action:is(url),show_btn:is(1)'
			), 
			'btn_text' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Button Text', 'youxi' ), 
				'description' => esc_html__( 'Enter here the text to display on the button.', 'youxi' ), 
				'std' => 'Sign Up', 
				'criteria' => 'show_btn:is(1)'
			), 
			'color' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Color Scheme', 'youxi' ), 
				'description' => esc_html__( 'Choose the base color of the pricing table.', 'youxi' ), 
				'choices' => array(
					'primary' => esc_html__( 'Primary', 'youxi' ), 
					'success' => esc_html__( 'Success', 'youxi' ), 
					'info'    => esc_html__( 'Info', 'youxi' ), 
					'warning' => esc_html__( 'Warning', 'youxi' ), 
					'danger'  => esc_html__( 'Danger', 'youxi' )
				), 
				'std' => 'primary'
			), 
			'featured' => array(
				'type' => 'switch', 
				'label' => esc_html__( 'Featured', 'youxi' ), 
				'description' => esc_html__( 'Switch to display the pricing table as featured.', 'youxi' ), 
				'std' => false
			)
		), 
		'content' => array(
			'type' => 'repeater', 
			'label' => esc_html__( 'Features', 'youxi' ), 
			'description' => esc_html__( 'Enter here the features to display on this pricing table.', 'youxi' ), 
			'preview_template' => '{{ data.name }}', 
			'button_text' => esc_html__( 'Add Feature', 'youxi' ), 
			'fields' => array(
				'name' => array(
					'type' => 'text', 
					'label' => esc_html__( 'Name', 'youxi' )
				)
			), 
			'serialize' => 'js:function( data ) {
				var li = $("<li/>"), ul = $("<ul/>");
				ul.append( $.map( data || [], function(v) {
					if( v.hasOwnProperty("name") ) {
						return li.clone().text( v.name );
					}
				}));
				return ul[0].outerHTML;
			}', 
			'deserialize' => 'js:function( data ) {
				return $( data ).children("li").map(function() {
					return { name: $( this ).html() };
				}).get();
			}'
		), 
		'callback' => 'youxi_shortcode_pricing_table_handler'
	));

	/**
	 * [service] shortcode
	 */
	$manager->add_shortcode( 'service', array(
		'label' => esc_html__( 'Service', 'youxi' ), 
		'category' => 'content', 
		'priority' => 100, 
		'icon' => 'fa fa-magic', 
		'insert_nl' => false, 
		'atts' => array(
			'title' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Title', 'youxi' ), 
				'description' => esc_html__( 'Enter here the service name.', 'youxi' )
			), 
			'show_btn' => array(
				'type' => 'switch', 
				'label' => esc_html__( 'Show Button', 'youxi' ), 
				'description' => esc_html__( 'Switch to hide/show the button.', 'youxi' ), 
				'std' => true
			), 
			'btn_text' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Button Text', 'youxi' ), 
				'description' => esc_html__( 'Enter the text to display on the button.', 'youxi' ), 
				'criteria' => 'show_btn:is(1)'
			), 
			'btn_type' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Button Type', 'youxi' ), 
				'description' => esc_html__( 'Choose the type of the button.', 'youxi' ), 
				'choices' => array(
					'primary'   => esc_html__( 'Primary', 'youxi' ), 
					'secondary' => esc_html__( 'Secondary', 'youxi' ), 
					'success'   => esc_html__( 'Success', 'youxi' ), 
					'info'      => esc_html__( 'Info', 'youxi' ), 
					'warning'   => esc_html__( 'Warning', 'youxi' ), 
					'danger'    => esc_html__( 'Danger', 'youxi' )
				), 
				'std' => 'primary', 
				'criteria' => 'show_btn:is(1)'
			), 
			'btn_size' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Button Size', 'youxi' ), 
				'description' => esc_html__( 'Choose the size of the button.', 'youxi' ), 
				'choices' => array(
					0       => esc_html__( 'Default', 'youxi' ), 
					'lg'    => esc_html__( 'Large', 'youxi' ), 
					'sm'    => esc_html__( 'Small', 'youxi' ), 
					'block' => esc_html__( 'Block', 'youxi' )
				), 
				'std' => 0, 
				'criteria' => 'show_btn:is(1)'
			), 
			'btn_action' => array(
				'type' => 'radio', 
				'label' => esc_html__( 'Button Action', 'youxi' ), 
				'description' => esc_html__( 'Choose the action to execute after clicking the button.', 'youxi' ), 
				'choices' => array(
					'url'  => 'Go to URL', 
					'page' => 'Go to Page'
				), 
				'std' => 'url', 
				'criteria' => 'show_btn:is(1)'
			), 
			'post_id' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Page', 'youxi' ), 
				'description' => esc_html__( 'Choose the page to view after clicking the button.', 'youxi' ), 
				'choices' => 'youxi_shortcode_page_choices', 
				'criteria' => 'show_btn:is(1),btn_action:is(page)'
			), 
			'url' => array(
				'type' => 'text', 
				'label' => esc_html__( 'URL', 'youxi' ), 
				'description' => esc_html__( 'Enter the URL to go to after clicking the button.', 'youxi' ), 
				'std' => '#', 
				'criteria' => 'show_btn:is(1),btn_action:is(url)'
			)
		), 
		'content' => array(
			'type' => 'richtext', 
			'label' => esc_html__( 'Description', 'youxi' ), 
			'description' => esc_html__( 'Enter the description of this service.', 'youxi' ), 
			'tinymce' => array(
				'media_buttons' => false, 
				'tinymce' => false
			)
		), 
		'callback' => 'youxi_shortcode_service_handler'
	));

	/**
	 * [table] shortcode
	 */
	$manager->add_shortcode( 'table', array(
		'label' => esc_html__( 'Table', 'youxi' ), 
		'priority' => 110, 
		'icon' => 'fa fa-table', 
		'category' => 'content', 
		'atts' => array(
			'styles' => array(
				'type' => 'checkboxlist', 
				'label' => esc_html__( 'Table Styles', 'youxi' ), 
				'description' => esc_html__( 'Choose here the styles to apply to the table.', 'youxi' ), 
				'choices' => array(
					'inverse'    => esc_html__( 'Inverse', 'youxi' ), 
					'striped'    => esc_html__( 'Striped', 'youxi' ), 
					'bordered'   => esc_html__( 'Bordered', 'youxi' ), 
					'hover'      => esc_html__( 'Hoverable', 'youxi' ), 
					'sm'         => esc_html__( 'Small', 'youxi' )
				), 
				'serialize' => 'js:function( data ) {
					return ( data || [] ).join(",");
				}', 
				'deserialize' => 'js:function( data ) {
					return ( data + "" ).split(",");
				}'
			), 
			'responsive' => array(
				'type' => 'switch', 
				'label' => esc_html__( 'Responsive Table', 'youxi' ), 
				'description' => esc_html__( 'Switch to make the table responsive.', 'youxi' ), 
				'std' => false
			)
		), 
		'content' => array(
			'type' => 'tabular', 
			'label' => esc_html__( 'Content', 'youxi' ), 
			'serialize' => 'js:function( data ) {
				var t = this, 
					tags = {
						headers: "table_head", 
						cells: "table_body"
					};
				return $.map( tags, function( tag, key ) {
					if( _.has( data, key ) ) {
						return t.construct( tag, { content: data[ key ] } );
					}
				}).join( "\n\n" );
			}', 
			'deserialize' => 'js:function( data ) {
				var t = this, 
					table = {}, 
					tags = ["table_head", "table_body"], 
					keys = ["headers", "cells"];

				_.each( data, function( data, index ) {
					table[ keys[ index ] ] = t.deserialize( tags[ index ], "content", data );
				});
				return $.extend( true, {}, table );
			}'
		), 	
		'callback' => 'youxi_shortcode_table_handler'
	));
	$manager->add_shortcode( 'table_head', array(
		'label' => esc_html__( 'Table Head', 'youxi' ), 
		'internal' => true, 
		'content' => array(
			'serialize' => 'js:function( data ) {
				var headers = _.map( data, function( header ) {
					return { content: header };
				});
				data = this.construct( "table_header", headers );
				return this.construct( "table_row", { content: data } );
			}', 
			'deserialize' => 'js:function( data ) {
				if( _.has( data, "content" ) && _.isArray( data.content ) ) {
					var mapped = _.map( data.content, function( data ) {
						return this.deserialize( data.tag, "content", data );
					}, this );

					// Take only the first <tr> in <thead>
					data = mapped.length ? mapped[0] : data;
				}
				return data;
			}'
		), 
		'callback' => 'youxi_shortcode_table_handler'
	));
	$manager->add_shortcode( 'table_body', array(
		'label' => esc_html__( 'Table Body', 'youxi' ), 
		'internal' => true, 
		'content' => array(
			'serialize' => 'js:function( data ) {
				if( _.isArray( data ) ) {
					var rows = _.map( data, function( cell ) {
						cell = _.map( cell, function( content ) {
							return { content: content };
						});
						return { content: this.construct( "table_cell", cell ) };
					}, this );

					data = this.construct( "table_row", rows );
				}
				return data;
			}', 
			'deserialize' => 'js:function( data ) {
				if( _.has( data, "content" ) && _.isArray( data.content ) ) {
					data = _.map( data.content, function( data ) {
						return this.deserialize( data.tag, "content", data );
					}, this );
				}
				return data;
			}'
		), 
		'callback' => 'youxi_shortcode_table_handler'
	));
	$manager->add_shortcode( 'table_header', array(
		'label' => esc_html__( 'Table Header', 'youxi' ), 
		'internal' => true, 
		'insert_nl' => false, 
		'callback' => 'youxi_shortcode_table_handler'
	));
	$manager->add_shortcode( 'table_cell', array(
		'label' => esc_html__( 'Table Cell', 'youxi' ), 
		'internal' => true, 
		'insert_nl' => false, 
		'callback' => 'youxi_shortcode_table_handler'
	));
	$manager->add_shortcode( 'table_row', array(
		'label' => esc_html__( 'Table Row', 'youxi' ), 
		'internal' => true, 
		'content' => array(
			'deserialize' => 'js:function( data ) {
				if( _.has( data, "content" ) && _.isArray( data.content ) ) {
					data = $.map( data.content, function( data ) {
						if( _.has( data, "content" ) ) {
							return data.content;
						}
					});
				}
				return data;
			}'
		), 
		'callback' => 'youxi_shortcode_table_handler'
	));

	/**
	 * [tabs] shortcode
	 */
	$manager->add_shortcode( 'tabs', array(
		'label' => esc_html__( 'Tabs', 'youxi' ), 
		'category' => 'content', 
		'priority' => 120, 
		'icon' => 'fa fa-folder-o', 
		'atts' => array(
			'type' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Tab Type', 'youxi' ), 
				'description' => esc_html__( 'Choose here the tabs type.', 'youxi' ), 
				'choices' => array(
					'tabs' => esc_html__( 'Tabs', 'youxi' ), 
					'pills' => esc_html__( 'Pills', 'youxi' )
				), 
				'std' => 'tabs'
			)
		), 
		'content' => array(
			'type' => 'repeater', 
			'label' => esc_html__( 'Tabs', 'youxi' ), 
			'description' => esc_html__( 'Enter here the title and content of each tab.', 'youxi' ), 
			'fields' => array( array( $manager, 'get_shortcode_fields' ), 'tab' ), 
			'preview_template' => '{{ data.title }}', 
			'serialize' => 'js:function( data ) {
				return this.construct( "tab", data );
			}', 
			'deserialize' => 'js:function( data ) {
				return this.deserializeArray( data );
			}'
		), 	
		'callback' => 'youxi_shortcode_tabs_handler'
	));
	$manager->add_shortcode( 'tab', array(
		'label' => esc_html__( 'Tab', 'youxi' ), 
		'category' => 'content', 
		'internal' => true, 
		'insert_nl' => false, 
		'atts' => array(
			'title' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Title', 'youxi' ), 
				'description' => esc_html__( 'Enter here the tab title.', 'youxi' )
			)
		), 
		'content' => array(
			'type' => 'textarea', 
			'label' => esc_html__( 'Content', 'youxi' ), 
			'description' => esc_html__( 'Enter here the tab content.', 'youxi' )
		), 
		'callback' => 'youxi_shortcode_tab_handler'
	));

	/**
	 * [team] shortcode
	 */
	$manager->add_shortcode( 'team', array(
		'label' => esc_html__( 'Team', 'youxi' ), 
		'category' => 'content', 
		'priority' => 130, 
		'icon' => 'fa fa-user', 
		'insert_nl' => false, 
		'atts' => array(
			'name' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Name', 'youxi' ), 
				'description' => esc_html__( 'Enter here the team member\'s name.', 'youxi' )
			), 
			'role' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Role', 'youxi' ), 
				'description' => esc_html__( 'Enter here the role of the team member.', 'youxi' )
			), 
			'photo' => array(
				'type' => 'image', 
				'label' => esc_html__( 'Photo', 'youxi' ), 
				'description' => esc_html__( 'Choose a photo for the team member.', 'youxi' ), 
				'return_type' => 'url', 
				'frame_title' => esc_html__( 'Choose a Photo', 'youxi' ), 
				'frame_btn_text' => esc_html__( 'Insert URL', 'youxi' ), 
				'upload_btn_text' => esc_html__( 'Choose a Photo', 'youxi' )
			)
		), 
		'content' => array(
			'type' => 'textarea', 
			'label' => esc_html__( 'About', 'youxi' ), 
			'description' => esc_html__( 'Enter here a short description about the team member.', 'youxi' )
		), 
		'callback' => 'youxi_shortcode_team_handler'
	));

	/**
	 * [testimonials] shortcode
	 */
	$manager->add_shortcode( 'testimonials', array(
		'label' => esc_html__( 'Testimonials', 'youxi' ), 
		'category' => 'content', 
		'priority' => 140, 
		'icon' => 'fa fa-comments', 
		'content' => array(
			'type' => 'repeater', 
			'label' => esc_html__( 'Testimonials', 'youxi' ), 
			'description' => esc_html__( 'Enter here the testimonials to display.', 'youxi' ), 
			'fields' => array( array( $manager, 'get_shortcode_fields' ), 'testimonial' ), 
			'preview_template' => '{{ data.author }}', 
			'serialize' => 'js:function( data ) {
				return this.construct( "testimonial", data );
			}', 
			'deserialize' => 'js:function( data ) {
				return this.deserializeArray( data );
			}'
		), 
		'callback' => 'youxi_shortcode_testimonials_handler'
	));

	/**
	 * [testimonial] shortcode
	 */
	$manager->add_shortcode( 'testimonial', array(
		'label' => esc_html__( 'Testimonial', 'youxi' ), 
		'category' => 'content', 
		'priority' => 150, 
		'icon' => 'fa fa-comment', 
		'insert_nl' => false, 
		'atts' => array(
			'author' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Author', 'youxi' ), 
				'description' => esc_html__( 'Enter here the testimonial author.', 'youxi' )
			), 
			'source' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Source', 'youxi' ), 
				'description' => esc_html__( 'Enter here the testimonial source.', 'youxi' )
			), 
			'source_url' => array(
				'type' => 'url', 
				'label' => esc_html__( 'Source URL', 'youxi' ), 
				'description' => esc_html__( 'Enter here the testimonial source URL.', 'youxi' ), 
				'std' => '#'
			)
		), 
		'content' => array(
			'type' => 'textarea', 
			'label' => esc_html__( 'Content', 'youxi' ), 
			'description' => esc_html__( 'Enter here the testimonial content.', 'youxi' )
		), 
		'callback' => 'youxi_shortcode_testimonial_handler'
	));

	/**
	 * [text_widget] shortcode
	 */
	$manager->add_shortcode( 'text_widget', array(
		'label' => esc_html__( 'Text Widget', 'youxi' ), 
		'category' => 'content', 
		'priority' => 160, 
		'icon' => 'fa fa-paragraph', 
		'content' => array(
			'type' => 'richtext', 
			'label' => esc_html__( 'Content', 'youxi' ), 
			'description' => esc_html__( 'Enter here the text to display.', 'youxi' )
		), 
		'callback' => 'youxi_shortcode_text_widget_handler'
	));

	/**
	 * [widget_area] shortcode
	 */
	$manager->add_shortcode( 'widget_area', array(
		'label' => esc_html__( 'Widget Area', 'youxi' ), 
		'category' => 'content', 
		'priority' => 170, 
		'icon' => 'fa fa-columns', 
		'atts' => array(
			'id' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Widget Area', 'youxi' ), 
				'description' => esc_html__( 'Choose the widget area to display.', 'youxi' ), 
				'choices' => 'youxi_shortcode_widget_area_choices'
			)
		), 
		'callback' => 'youxi_shortcode_widget_area_handler'
	));
}

/**
 * Hook to 'youxi_shortcode_register'
 */
add_action( 'youxi_shortcode_register', 'youxi_define_content_shortcodes', 1 );
