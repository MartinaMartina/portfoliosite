<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

// Make sure the plugin is active
if( ! defined( 'YOUXI_SHORTCODE_VERSION' ) ) {
	return;
}

/* ==========================================================================
	Youxi Shortcode plugin config
============================================================================= */

/**
 * Disable enqueueing bootstrap
 */
add_filter( 'youxi_shortcode_enqueue_bootstrap', '__return_false' );

/**
 * Mapbox Access Token
 */
add_filter( 'youxi_shortcode_mapbox_access_token', 'helium_mapbox_access_token' );

/**
 * Column types
 */
if( ! function_exists( 'helium_youxi_shortcode_column_types' ) ) : 

function helium_youxi_shortcode_column_types() {
	return array(
		'xs' => esc_html__( 'Extra Small (&lt; 768px)', 'helium' ), 
		'sm' => esc_html__( 'Small (&ge; 768px)', 'helium' ), 
		'md' => esc_html__( 'Medium (&ge; 992px)', 'helium' ), 
		'lg' => esc_html__( 'Large (&ge; 1200px)', 'helium' )
	);
}
endif;
add_filter( 'youxi_shortcode_column_types', 'helium_youxi_shortcode_column_types' );

/**
 * Column default type
 */
if( ! function_exists( 'helium_youxi_shortcode_column_default_type' ) ) : 

function helium_youxi_shortcode_column_default_type( $default ) {
	return 'md';
}
endif;
add_filter( 'youxi_shortcode_column_default_type', 'helium_youxi_shortcode_column_default_type' );

/**
 * Add the tinymce and page builder to posts
 */
if( ! function_exists( 'helium_shortcode_tinymce_post_types' ) ) {

	function helium_shortcode_tinymce_post_types( $post_types ) {

		if( ! is_array( $post_types ) ) {
			$post_types = array();
		}

		$post_types[] = 'post';
		
		if( function_exists( 'youxi_portfolio_cpt_name' ) ) {
			$post_types[] = youxi_portfolio_cpt_name();
		}

		if( class_exists( 'Easy_Digital_Downloads' ) ) {
			$post_types[] = 'download';
		}

		return $post_types;
	}
}
add_filter( 'youxi_shortcode_tinymce_post_types', 'helium_shortcode_tinymce_post_types' );

/**
 * Hook to modify some shortcodes
 */
if( ! function_exists( 'helium_youxi_shortcode_register' ) ) {

	function helium_youxi_shortcode_register( $manager ) {

		$remove = array(
			'posts', 
			'service', 
			'widget_area', 
			'container', 
			'slider', 
			'twitter', 
			'counter', 
			'testimonials', 
			'testimonial', 
			'call_to_action'
		);
		foreach( $remove as $r ) {
			$manager->remove_shortcode( $r );
		}
	}
}
add_action( 'youxi_shortcode_register', 'helium_youxi_shortcode_register' );

/* ==========================================================================
	Tabs
============================================================================= */

/**
 * Tabs shortcode callback
 */
if( ! function_exists( 'helium_tabs_shortcode_cb' ) ):

	function helium_tabs_shortcode_cb( $atts, $content, $tag ) {

		extract( $atts, EXTR_PREFIX_ALL, 'tabs' );

		$tabs = Youxi_Shortcode::to_array( $content, true );
		
		if( is_array( $tabs ) && ! empty( $tabs ) ) {

			$o = '<ul class="nav nav-' . esc_attr( $tabs_type ) . '">';
				
			foreach( $tabs as $index => $tab ) {

				if( isset( $tab['tag'], $tab['atts'] ) && Youxi_Shortcode::prefix( 'tab' ) == $tab['tag'] ) {

					extract( $tab['atts'], EXTR_PREFIX_ALL, 'tab' );

					$tab_id = sanitize_key( $tab_title . Youxi_Shortcode::count( 'tabs' ) . $index );

					$o .= '<li' . ( $index == 0 ? ' class="active"' : '' ) . '>';

						$o .= '<a href="#' . esc_attr( $tab_id ) . '" data-toggle="tab">';

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
endif;
add_filter( 'youxi_shortcode_tabs_callback', create_function( '', 'return "helium_tabs_shortcode_cb";' ) );

/**
 * Tab shortcode callback
 */
if( ! function_exists( 'helium_tab_shortcode_cb' ) ):

	function helium_tab_shortcode_cb( $atts, $content, $tag ) {

		extract( $atts, EXTR_PREFIX_ALL, 'tab' );

		$tab_classes = 'tab-pane fade';

		if( 0 == Youxi_Shortcode::count( 'tab' ) ) {
			$tab_classes .= ' in active';
		}

		$tab_id = sanitize_key( $tab_title . Youxi_Shortcode::count( 'tabs' ) . Youxi_Shortcode::count( 'tab' ) );

		$o = '<div class="' . esc_attr( $tab_classes ) . '" id="' . esc_attr( $tab_id ) . '" role="tabpanel">';

			$o .= wpautop( Youxi_Shortcode_Manager::get()->shortcode_unautop( do_shortcode( wp_kses_post( $content ) ) ) );

		$o .= '</div>';
		
		return $o;
	}
endif;
add_filter( 'youxi_shortcode_tab_callback', create_function( '', 'return "helium_tab_shortcode_cb";' ) );

/* ==========================================================================
	Accordion
============================================================================= */

/**
 * Accordion shortcode callback
 */
if( ! function_exists( 'helium_accordion_shortcode_cb' ) ):

	function helium_accordion_shortcode_cb( $atts, $content, $tag ) {

		$accordion_id = Youxi_Shortcode::uniqid( 'accordion' );

		$o = '<div class="panel-group" id="' . esc_attr( $accordion_id ) . '">';

			$o .= do_shortcode( $content );

		$o .= '</div>';

		return $o;
	}
endif;
add_filter( 'youxi_shortcode_accordion_callback', create_function( '', 'return "helium_accordion_shortcode_cb";' ) );

/**
 * Accordion Group shortcode callback
 */
if( ! function_exists( 'helium_accordion_group_shortcode_cb' ) ):

	function helium_accordion_group_shortcode_cb( $atts, $content, $tag ) {

		extract( $atts, EXTR_PREFIX_ALL, 'accordion_group' );

		$parent_id    = Youxi_Shortcode::uniqid( 'accordion' );
		$accordion_id = Youxi_Shortcode::uniqid( $tag );
		
		$o = '<div class="panel panel-default">';

			$o .= '<div class="panel-heading">';

				$o .= '<h4 class="panel-title">';

					$o .= '<a class="collapsed" data-toggle="collapse" data-parent="#' . esc_attr( $parent_id ) . '" href="#' . esc_attr( $accordion_id ) . '">';

						$o .= $accordion_group_title;

					$o .= '</a>';

				$o .= '</h4>';

			$o .= '</div>';

			$o .= '<div id="' . esc_attr( $accordion_id ) . '" class="panel-collapse collapse">';

				$o .= '<div class="panel-body">';

					$o .= wpautop( Youxi_Shortcode_Manager::get()->shortcode_unautop( do_shortcode( wp_kses_post( $content ) ) ) );

				$o .= '</div>';

			$o .= '</div>';

		$o .= '</div>';
		
		return $o;
	}
endif;
add_filter( 'youxi_shortcode_accordion_group_callback', create_function( '', 'return "helium_accordion_group_shortcode_cb";' ) );

/* ==========================================================================
	Clients
============================================================================= */

/**
 * Clients shortcode callback
 */
if( ! function_exists( 'helium_clients_shortcode_cb' ) ):

	function helium_clients_shortcode_cb( $atts, $content, $tag ) {

		$output = '<div class="client-list"><ul class="clearfix plain-list">' . do_shortcode( $content ) . '</ul></div>';

		return $output;
	}
endif;
add_filter( 'youxi_shortcode_clients_callback', create_function( '', 'return "helium_clients_shortcode_cb";' ) );

/**
 * Client shortcode callback
 */
if( ! function_exists( 'helium_client_shortcode_cb' ) ):

	function helium_client_shortcode_cb( $atts, $content, $tag ) {

		extract( $atts, EXTR_SKIP );
		
		$o = '<li class="client">';

			$o .= '<div class="client-logo">';

				$o .= '<div class="logo">';

					if( $url ):
					$o .= '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $name ) . '">';
						$o .= '<img src="' . esc_url( $logo ) . '" alt="' . esc_attr( $name ) . '">';
					$o .= '</a>';
					else:
					$o .= '<span>';
						$o .= '<img src="' . esc_url( $logo ) . '" alt="' . esc_attr( $name ) . '">';
					$o .= '</span>';
					endif;

				$o .= '</div>';

			$o .= '</div>';

		$o .= '</li>';

		return $o;
	}
endif;
add_filter( 'youxi_shortcode_client_callback', create_function( '', 'return "helium_client_shortcode_cb";' ) );

/* ==========================================================================
	Heading
============================================================================= */

/**
 * Heading shortcode callback
 */
if( ! function_exists( 'helium_heading_shortcode_cb' ) ):

	function helium_heading_shortcode_cb( $atts, $content, $tag ) {

		extract( $atts, EXTR_SKIP );

		$content = strip_tags( $content );
		$classes = array();

		if( preg_match( '/^(bordered|striped)$/', $style ) ) {
			$classes[] = $style;
		}

		if( preg_match( '/^(center|right)$/', $style ) ) {
			$classes[] = 'text-' . $alignment;
		}

		if( $uppercase ) {
			$classes[] = 'text-uppercase';
		}

		if( is_string( $remove_margins ) ) {
			$remove_margins = array_unique( explode( ',', $remove_margins ) );
			foreach( $remove_margins as $remove ) {
				if( in_array( $remove, array( 'top', 'bottom' ) ) ) {
					$classes[] = 'no-margin-' . trim( $remove );
				}
			}
		}

		$classes[] = sanitize_html_class( trim( $extra_classes ) );

		if( $classes ) {
			$classes = ' class="' . esc_attr( implode( ' ', array_filter( $classes ) ) ) . '"';
		} else {
			$classes = '';
		}

		return '<' . $element . $classes . '>' . $content . '</' . $element . '>';
	}
endif;
add_filter( 'youxi_shortcode_heading_callback', create_function( '', 'return "helium_heading_shortcode_cb";' ) );

/**
 * Heading shortcode atts
 */
if( ! function_exists( 'helium_heading_shortcode_atts' ) ):

	function helium_heading_shortcode_atts( $atts ) {

		return array_merge( $atts, array(
			'alignment' => array(
				'type' => 'radio', 
				'label' => esc_html__( 'Heading Alignment', 'helium' ), 
				'description' => esc_html__( 'Choose here the heading text alignment.', 'helium' ), 
				'choices' => array(
					'left' => esc_html__( 'Left', 'helium' ), 
					'center' => esc_html__( 'Center', 'helium' ), 
					'right' => esc_html__( 'Right', 'helium' )
				), 
				'std' => 'left', 
				'fieldset' => 'style'
			), 
			'style' => array(
				'type' => 'radio', 
				'label' => esc_html__( 'Heading Style', 'helium' ), 
				'description' => esc_html__( 'Choose the heading style.', 'helium' ), 
				'choices' => array(
					0 => esc_html__( 'Default', 'helium' ), 
					'bordered' => esc_html__( 'Bordered', 'helium' ), 
					'striped' => esc_html__( 'Striped', 'helium' )
				), 
				'std' => 0
			), 
			'uppercase' => array(
				'type' => 'switch', 
				'label' => esc_html__( 'Uppercase Letters', 'helium' ), 
				'description' => esc_html__( 'Switch to make the text uppercase.', 'helium' ), 
				'std' => false, 
				'fieldset' => 'style'
			), 
			'remove_margins' => array(
				'type' => 'checkboxlist', 
				'label' => esc_html__( 'Remove Margins', 'helium' ), 
				'uncheckable' => true, 
				'description' => esc_html__( 'Choose here which margins to remove from the heading.', 'helium' ), 
				'choices' => array(
					'top' => esc_html__( 'Top', 'helium' ), 
					'bottom' => esc_html__( 'Bottom', 'helium' ), 
				), 
				'serialize' => 'js:function( data ) {
					return $.map( data, function( data, key ) {
						if( !! parseInt( data ) )
							return key;
					});
				}', 
				'deserialize' => 'js:function( data ) {
					var temp = {};
					_.each( ( data + "" ).split( "," ), function( c ) {
						temp[ c ] = 1;
					});
					return temp;
				}', 
				'fieldset' => 'style'
			), 
			'extra_classes' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Extra CSS Classes', 'helium' ), 
				'description' => esc_html__( 'Enter here your custom CSS classes to apply to the heading.', 'helium' ), 
				'std' => '', 
				'fieldset' => 'style'
			)
		));
	}
endif;
add_filter( 'youxi_shortcode_heading_atts', 'helium_heading_shortcode_atts' );

/**
 * Heading shortcode fieldsets
 */
if( ! function_exists( 'helium_heading_shortcode_fieldsets' ) ):

function helium_heading_shortcode_fieldsets( $fieldsets ) {
	return array_merge( $fieldsets, array(
		'style' => array(
			'id' => 'style', 
			'title' => esc_html__( 'Styling', 'helium' )
		)
	));
}
endif;
add_filter( 'youxi_shortcode_heading_fieldsets', 'helium_heading_shortcode_fieldsets' );

/* ==========================================================================
	Pricing Table
============================================================================= */

/**
 * Pricing Table shortcode callback
 */
if( ! function_exists( 'helium_pricing_table_shortcode_cb' ) ):

	function helium_pricing_table_shortcode_cb( $atts, $content, $tag ) {
		extract( $atts, EXTR_SKIP );

		switch( $btn_action ) {
			case 'page':
				$url = get_permalink( $post_id );
				$url = $url ? $url : '#';
				break;
		}

		$o = '<div class="pricing-table' . ( $featured ? ' featured' : '' ) . '">';

			$o .= '<div class="table-header">';

				$o .= '<div class="name">';

					$o .= $title;

					if( $subtitle ):
						$o .= '<small>' . $subtitle . '</small>';
					endif;

				$o .= '</div>';

			$o .= '</div>';

			if( $show_price ):

				$o .= '<div class="table-price">';

					$o .= '<div class="price text-' . esc_attr( $color ) . '">';
						$o .= '<span>' . esc_html( $currency ) . '</span>';
						$o .= esc_html( $price );
					$o .= '</div>';

					$o .= '<div class="price-description">';
						$o .= esc_html( $price_description );
					$o .= '</div>';

				$o .= '</div>';

			endif;

			$o .= '<div class="table-features">';
				$o .= $content;
			$o .= '</div>';

			if( $show_btn ):

				$o .= '<div class="table-footer">';
					$o .= '<a href="' . esc_url( $url ) . '" class="btn btn-' . esc_attr( $color ) . '">' . $btn_text . '</a>';
				$o .= '</div>';

			endif;

		$o .= '</div>';

		return $o;
	}
endif;
add_filter( 'youxi_shortcode_pricing_table_callback', create_function( '', 'return "helium_pricing_table_shortcode_cb";' ) );

/**
 * Pricing Table shortcode atts
 */
if( ! function_exists( 'helium_pricing_table_shortcode_atts' ) ):

	function helium_pricing_table_shortcode_atts( $atts ) {

		$p1 = array_slice( $atts, 0, 1 );
		$p2 = array_slice( $atts, 1 );
		$pn = array(
			'subtitle' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Subtitle', 'helium' ), 
				'description' => esc_html__( 'Specify the subtitle to show below the title.', 'helium' ), 
				'std' => ''
			)
		);

		return array_merge( $p1, $pn, $p2 );
	}
endif;
add_filter( 'youxi_shortcode_pricing_table_atts', 'helium_pricing_table_shortcode_atts' );

/**
 * Make pricing table shortcode external
 */
add_filter( 'youxi_shortcode_pricing_table_internal', '__return_false' );

/* ==========================================================================
	Progressbar
============================================================================= */

/**
 * Progressbar shortcode atts
 */
if( ! function_exists( 'helium_progressbar_shortcode_atts' ) ):

	function helium_progressbar_shortcode_atts( $atts ) {

		return array_merge( array(
			'label' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Label', 'helium' ), 
				'description' => esc_html__( 'Enter here the progressbar label.', 'helium' ), 
				'std' => ''
			)
		), $atts );
	}
endif;
add_filter( 'youxi_shortcode_progressbar_atts', 'helium_progressbar_shortcode_atts' );

/**
 * Progressbar shortcode callback
 */
if( ! function_exists( 'helium_progressbar_shortcode_cb' ) ):

	function helium_progressbar_shortcode_cb( $atts, $content, $tag ) {

		extract( $atts, EXTR_PREFIX_ALL, 'progressbar' );

		$container_classes = array( 'progress' );
		$bar_classes       = array( 'progress-bar' );

		if( in_array( $progressbar_type, array( 'success', 'info', 'warning', 'danger' ), true ) ) {
			$bar_classes[] = "progress-bar-{$progressbar_type}";
		}
		if( wp_validate_boolean( $progressbar_striped ) ) {
			$container_classes[] = "progress-striped";
		}
		if( wp_validate_boolean( $progressbar_animated ) ) {
			$container_classes[] = 'active';
		}

		$container_classes = implode( ' ', $container_classes );
		$bar_classes       = implode( ' ', $bar_classes );

		$o = '<div class="progress-counter">';

			if( $progressbar_label ):
				$o .= '<span class="progress-label">' . esc_html( $progressbar_label ) . '</span>';
			endif;

			$o .= '<div class="' . esc_attr( $container_classes ) . '">';
				
				$o .= '<div class="' . esc_attr( $bar_classes ) . '" role="progressbar" aria-valuenow="' . esc_attr( $progressbar_value ) . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . esc_attr( $progressbar_value ) . '%"></div>';
			
			$o .= '</div>';

		$o .= '</div>';

		return $o;
	}
endif;
add_filter( 'youxi_shortcode_progressbar_callback', create_function( '', 'return "helium_progressbar_shortcode_cb";' ) );

/* ==========================================================================
	Team
============================================================================= */

/**
 * Team shortcode callback
 */
if( ! function_exists( 'helium_team_shortcode_cb' ) ):

	function helium_team_shortcode_cb( $atts, $content, $tag ) {

		extract( $atts, EXTR_SKIP );

		/* Parse social profiles */
		$social_profiles = array();
		if( $profiles = explode( '|', $social ) ) {

			foreach( $profiles as $key => $profile ) {

				$profile = explode( ',', $profile );

				if( count( $profile ) >= 2 ) {

					$social_profiles[ $key ] = array(
						'icon' => $profile[0], 
						'url' => $profile[1]
					);
				} else {
					unset( $social_profiles[ $key ] );
				}
			}
		}

		/* Prepare Team Data */
		$team_data = array(
			'name' => $name, 
			'role' => $role, 
			'photo' => $photo, 
			'has_social' => is_array( $social_profiles ) && ! empty( $social_profiles ), 
			'social_profiles' => is_array( $social_profiles ) ? $social_profiles : array(), 
			'content' => $content
		);

		$o = '<div class="team" data-team-data="' . esc_attr( json_encode( $team_data ) ) . '">';

			$o .= '<div class="team-photo">';

				$o .= '<a href="#" title="' . esc_attr( $name ) . '">';

					$o .= '<img src="' . esc_url( $photo ) . '" alt="' . esc_attr( $name ) . '">';

				$o .= '</a>';

			$o .= '</div>';

			$o .= '<div class="team-info">';
			
				$o .= '<h4 class="team-name">' . esc_html( $name ) . '</h4>';
				$o .= '<p class="team-role">' . esc_html( $role ) . '</p>';

			$o .= '</div>';

		$o .= '</div>';

		return $o;
	}
endif;
add_filter( 'youxi_shortcode_team_callback', create_function( '', 'return "helium_team_shortcode_cb";' ) );

/**
 * Team shortcode atts
 */
if( ! function_exists( 'helium_team_shortcode_atts' ) ):

	function helium_team_shortcode_atts( $atts ) {

		return array_merge( $atts, array(
			'social' => array(
				'type' => 'repeater', 
				'fieldset' => 'social', 
				'label' => esc_html__( 'Social Profiles', 'helium' ), 
				'description' => esc_html__( 'Specify the social profiles of this team member.', 'helium' ), 
				'preview_template' => '{{ data.url }}', 
				'min' => 0, 
				'fields' => array(
					'icon' => array(
						'type' => 'select', 
						'label' => esc_html__( 'Icon', 'helium' ), 
						'choices' => helium_socicon_choices(), 
						'description' => esc_html__( 'Choose here the profile icon.', 'helium' )
					), 
					'url' => array(
						'type' => 'text', 
						'label' => esc_html__( 'URL', 'helium' ), 
						'description' => esc_html__( 'Enter here the profile URL.', 'helium' )
					)
				), 
				'serialize' => 'js:function( data ) {
					return $.map( data, function( p ) {
						return $.map( p, function( v ) {
							return v;
						}).join( "," );
					}).join( "|" );
				}', 
				'deserialize' => 'js:function( data ) {
					return $.map( ( data + "" ).split( "|" ), function( p ) {
						p = ( p + "" ).split( "," );
						if( p.length >= 2 ) {
							return { icon: p[0], url: p[1] };
						}
					});
				}'
			)
		));
	}
endif;
add_filter( 'youxi_shortcode_team_atts', 'helium_team_shortcode_atts' );

/**
 * Team shortcode fieldsets
 */
if( ! function_exists( 'helium_team_shortcode_fieldsets' ) ):

function helium_team_shortcode_fieldsets( $fieldsets ) {
	return array_merge( $fieldsets, array(
		'social' => array(
			'id' => 'social', 
			'title' => esc_html__( 'Social', 'helium' )
		)
	));
}
endif;
add_filter( 'youxi_shortcode_team_fieldsets', 'helium_team_shortcode_fieldsets' );

/* ==========================================================================
	Row
============================================================================= */

/**
 * Row shortcode callback
 */
if( ! function_exists( 'helium_row_shortcode_cb' ) ):

	function helium_row_shortcode_cb( $atts, $content, $tag ) {

		extract( $atts, EXTR_SKIP );

		$attributes = array(
			'class' => 'row'
		);

		if( ! empty( $extra_classes ) ) {
			$attributes['class'] .= ' ' . sanitize_html_class( trim( $extra_classes ) );
		}

		$html = '';
		foreach( $attributes as $key => $option ) {
			$html .= " {$key}=\"" . esc_attr( $option ) . "\"";
		}

		$o = '';

		$wrap_method = preg_match( '/^(fullwidth|inner)$/', $wrap_method ) ? $wrap_method : false;

		if( $wrap_method ):

		$o .= '<div class="content-wrap-' . $wrap_method . '">';

			$o .= '<div class="container">';

		endif;

				$o .= '<div' . $html . '>' . do_shortcode( $content ) . '</div>';

		if( $wrap_method ):

			$o .= '</div>';

		$o .= '</div>';

		endif;

		return $o;
	}
endif;
add_filter( 'youxi_shortcode_row_callback', create_function( '', 'return "helium_row_shortcode_cb";' ) );

/**
 * Row shortcode atts
 */
if( ! function_exists( 'helium_row_shortcode_atts' ) ):

	function helium_row_shortcode_atts( $atts ) {

		return array_merge( $atts, array(
			'wrap_method' => array(
				'type' => 'select', 
				'label' => esc_html__( 'Wrap Mode', 'helium' ), 
				'description' => esc_html__( 'Choose the row wrapping method. When choosing values other than `none`, make sure the page layout setting has `Wrap Content` disabled.', 'helium' ), 
				'choices' => array(
					0 => esc_html__( 'None', 'helium' ), 
					'fullwidth' => esc_html__( 'Fullwidth', 'helium' ), 
					'inner' => esc_html__( 'Inner', 'helium' )
				), 
				'std' => 0
			), 
			'extra_classes' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Extra CSS Classes', 'helium' ), 
				'description' => esc_html__( 'Enter here your custom CSS classes to apply to the row.', 'helium' ), 
				'std' => ''
			)
		));
	}
endif;
add_filter( 'youxi_shortcode_row_atts', 'helium_row_shortcode_atts' );

/* ==========================================================================
	Column
============================================================================= */

/**
 * Column shortcode callback
 */
if( ! function_exists( 'helium_column_shortcode_cb' ) ):

	function helium_column_shortcode_cb( $atts, $content, $tag ) {

		foreach( array_keys( Youxi_Shortcode::get_column_types() ) as $type ) {

			if( isset( $atts["size_{$type}"], $atts["offset_{$type}"] ) ) {

				$size   = $atts["size_{$type}"];
				$offset = $atts["offset_{$type}"];

				if( 'inherit' !== $size && is_numeric( $size ) ) {
					$size = min( max( absint( $atts["size_{$type}"] ), 1 ), Youxi_Shortcode::get_column_count() );
					$classes[] = "col-{$type}-{$size}";
				}

				if( 'inherit' !== $offset && is_numeric( $offset ) ) {
					$offset = min( max( absint( $atts["offset_{$type}"] ), 0 ), Youxi_Shortcode::get_column_count() );
					$classes[] = "col-{$type}-offset-{$offset}";
				}
			}
		}

		return '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">' . do_shortcode( $content ) . '</div>';
	}
endif;
add_filter( 'youxi_shortcode_col_callback', create_function( '', 'return "helium_column_shortcode_cb";' ) );

/* ==========================================================================
	Separator
============================================================================= */

/**
 * Separator shortcode callback
 */
if( ! function_exists( 'helium_separator_shortcode_cb' ) ):

	function helium_separator_shortcode_cb( $atts, $content, $tag ) {
		$classes = array(
			'spacer-' . $atts['size'], 
			$atts['extra_classes']
		);
		return '<div class="' . esc_attr( implode( ' ', $classes ) ) . '"></div>';
	}
endif;
add_filter( 'youxi_shortcode_separator_callback', create_function( '', 'return "helium_separator_shortcode_cb";' ) );

/**
 * Separator shortcode atts
 */
if( ! function_exists( 'helium_separator_shortcode_atts' ) ):

	function helium_separator_shortcode_atts( $atts ) {

		return array_merge( $atts, array(
			'size' => array(
				'type' => 'uislider', 
				'label' => esc_html__( 'Separator Size', 'helium' ), 
				'description' => esc_html__( 'Choose the height of the separator.', 'helium' ), 
				'widgetopts' => array(
					'min' => 10, 
					'max' => 140, 
					'step' => 10
				), 
				'std' => 10
			), 
			'extra_classes' => array(
				'type' => 'text', 
				'label' => esc_html__( 'Extra CSS Classes', 'helium' ), 
				'description' => esc_html__( 'Enter here your custom CSS classes to apply to the separator.', 'helium' ), 
				'std' => ''
			)
		));
	}
endif;
add_filter( 'youxi_shortcode_separator_atts', 'helium_separator_shortcode_atts' );
