<?php
/**
 * Initialize the custom theme options.
 */
add_action( 'init', 'custom_theme_options' );

/**
 * Build the custom settings & update OptionTree.
 */
function custom_theme_options() {
  
  /* OptionTree is not loaded yet, or this is not an admin request */
  if ( ! function_exists( 'ot_settings_id' ) || ! is_admin() )
    return false;
    
  /**
   * Get a copy of the saved settings array. 
   */
  $saved_settings = get_option( ot_settings_id(), array() );
  
  /**
   * Custom settings array that will eventually be 
   * passes to the OptionTree Settings API Class.
   */
  $custom_settings = array( 
    'contextual_help' => array( 
      'sidebar'       => ''
    ),
    'sections'        => array( 
      array(
        'id'          => 'addthis',
        'title'       => __( 'AddThis', 'helium' )
      ),
      array(
        'id'          => 'miscellaneous',
        'title'       => __( 'Miscellaneous', 'helium' )
      )
    ),
    'settings'        => array( 
      array(
        'id'          => 'addthis_sharing_buttons',
        'label'       => __( 'Sharing Buttons', 'helium' ),
        'desc'        => __( 'Enter a comma separated list of AddThis social media sharing buttons to show at the end of each item page.
See this for available buttons: <a href="http://www.addthis.com/services/list">www.addthis.com/services/list</a>', 'helium' ),
        'std'         => 'facebook, twitter, email, compact',
        'type'        => 'text',
        'section'     => 'addthis',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => '',
        'operator'    => 'and'
      ),
      array(
        'id'          => 'addthis_profile_id',
        'label'       => __( 'Profile ID', 'helium' ),
        'desc'        => __( 'Specify here your AddThis profile ID if you want to track your AddThis sharing data.', 'helium' ),
        'std'         => '',
        'type'        => 'text',
        'section'     => 'addthis',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => '',
        'operator'    => 'and'
      ),
      array(
        'id'          => 'custom_css',
        'label'       => __( 'Custom CSS', 'helium' ),
        'desc'        => __( 'This custom CSS field has been deprecated. Move your custom CSS to the Additional CSS section in the WordPress customizer to keep using them.', 'helium' ),
        'std'         => '',
        'type'        => 'css',
        'section'     => 'miscellaneous',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'min_max_step'=> '',
        'class'       => '',
        'condition'   => '',
        'operator'    => 'and'
      )
    )
  );
  
  /* allow settings to be filtered before saving */
  $custom_settings = apply_filters( ot_settings_id() . '_args', $custom_settings );
  
  /* settings are not the same update the DB */
  if ( $saved_settings !== $custom_settings ) {
    update_option( ot_settings_id(), $custom_settings ); 
  }
  
  /* Lets OptionTree know the UI Builder is being overridden */
  global $ot_has_custom_theme_options;
  $ot_has_custom_theme_options = true;
  
}