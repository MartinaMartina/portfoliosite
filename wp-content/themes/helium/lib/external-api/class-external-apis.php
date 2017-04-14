<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

abstract class Youxi_External_API_Base {

	public $title;
	
	public function __construct( $title ) {
		$this->title = $title;
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function settings_section_callback() {}

	public function settings_field_callback( $args ) {}

	abstract public function register_settings();

	abstract public function display_settings();
}

class Youxi_External_API_Typekit extends Youxi_External_API_Base {

	public function __construct() {

		parent::__construct( esc_html__( 'Typekit', 'helium' ) );

		if( ! class_exists( 'Youxi_Typekit' ) ) {
			require get_template_directory() . '/lib/framework/font/class-typekit.php';
		}

		$this->option_object = get_option( 'youxi_external_api_typekit_option', array() );

		/* User wants to flush cache */
		if( isset( $_GET['action'], $_GET['_wpnonce'] ) && 'flush_cache' == $_GET['action'] ) {

			if( wp_verify_nonce( $_GET['_wpnonce'], 'youxi_external_api_typekit_flush_cache' ) ) {

				Youxi_Typekit::flush_cache();

				$redirect_url = remove_query_arg( array( 'action', '_wpnonce' ) );
				$redirect_url = esc_url_raw( $redirect_url );

				wp_redirect( $redirect_url );
				exit;
			}
		}

		/* User has submitted a kit */
		if( ! empty( $this->option_object['kit_id'] ) ) {			

			$kit = Youxi_Typekit::get_kit( $this->option_object['kit_id'] );

			if( ! empty( $kit['id'] ) && $this->option_object['kit_id'] == $kit['id'] ) {
				$this->current_kit = $kit;
			} else {
				$this->current_kit = 'invalid';
			}

		} else {
			Youxi_Typekit::flush_cache();
		}
	}

	public function register_settings() {

		// If the theme options don't exist, create them.
		if( ! get_option( 'youxi_external_api_typekit_option' ) ) {  
			add_option( 'youxi_external_api_typekit_option', array(), '', 'no' );
		}

		// Add the settings section
		add_settings_section(
			'youxi_external_api_typekit', 
			esc_html__( 'Typekit', 'helium' ), 
			array( $this, 'settings_section_callback' ), 
			'youxi_external_api_typekit_option'
		);

		// Add the settings fields
		add_settings_field(
			'youxi_external_api_typekit_kit_id', 
			esc_html__( 'Kit ID', 'helium' ), 
			array( $this, 'settings_field_callback' ), 
			'youxi_external_api_typekit_option', 
			'youxi_external_api_typekit', 
			array( 'label_for' => 'youxi_external_api_typekit_kit_id' )
		);

		// Register the setting
		register_setting(
			'youxi_external_api_typekit_option', 
			'youxi_external_api_typekit_option'
		);
	}

	public function settings_section_callback() {

		if( isset( $this->current_kit ) ) {

			if( ! empty( $this->current_kit['families'] ) ) {

				echo wpautop( esc_html__( 'Your current Typekit kit contains the following font families.', 'helium' ) );

				echo '<table class="striped widefat">';

					echo '<thead>';

						echo '<tr>';

							echo '<th>' . esc_html__( 'Name', 'helium' ) . '</th>';
							echo '<th>' . esc_html__( 'Variations', 'helium' ) . '</th>';

						echo '</tr>';

					echo '</thead>';

					echo '<tbody>';

						foreach( $this->current_kit['families'] as $family ) {

							echo '<tr>';

								echo '<td>' . $family['name'] . '</td>';
								echo '<td>' . implode( ', ', $family['variations'] ) . '</td>';

							echo '</tr>';
						}

					echo '</tbody>';

				echo '</table>';

				$flush_cache_url = add_query_arg( array(
					'action' => 'flush_cache', 
					'_wpnonce' => wp_create_nonce( 'youxi_external_api_typekit_flush_cache' )
				));
				echo wpautop( sprintf( '<a class="button" href="%s">%s</a>', esc_url( $flush_cache_url ), esc_html__( 'Refresh', 'helium' ) ) );

			} else {

				echo '<div class="settings-error error">';
					echo wpautop( esc_html__( 'The provided Typekit kit id is invalid.', 'helium' ) );
				echo '</div>';
			}
		}
	}

	public function settings_field_callback( $args ) {

		// Can not proceed without the `label_for` argument
		if( empty( $args['label_for'] ) ) {
			return;
		}

		$field_id  = $args['label_for'];
		$field_key = str_replace( 'youxi_external_api_typekit_', '', $field_id );

		$typekit_options = get_option( 'youxi_external_api_typekit_option' );
		$current_value   = empty( $typekit_options[ $field_key ] ) ? '' : $typekit_options[ $field_key ];		

		echo '<input name="youxi_external_api_typekit_option[' . esc_attr( $field_key ) . ']" type="text" id="' . esc_attr( $field_id ) . '" value="' . esc_attr( $current_value ) . '" class="regular-text">';
	}

	public function display_settings() {

		settings_fields( 'youxi_external_api_typekit_option' );
		do_settings_sections( 'youxi_external_api_typekit_option' );
		submit_button();
	}
}

class Youxi_External_API_Twitter extends Youxi_External_API_Base {

	public function __construct() {
		parent::__construct( esc_html__( 'Twitter', 'helium' ) );
	}

	public function register_settings() {

		// If the theme options don't exist, create them.
		if( ! get_option( 'youxi_external_api_twitter_option' ) ) {  
			add_option( 'youxi_external_api_twitter_option', array(), '', 'no' );
		}

		// Add the settings section
		add_settings_section(
			'youxi_external_api_twitter', 
			esc_html__( 'Twitter API Keys', 'helium' ), 
			array( $this, 'settings_section_callback' ), 
			'youxi_external_api_twitter_option'
		);

		// Add the settings fields
		add_settings_field(
			'youxi_external_api_twitter_consumer_key', 
			esc_html__( 'Consumer Key', 'helium' ), 
			array( $this, 'settings_field_callback' ), 
			'youxi_external_api_twitter_option', 
			'youxi_external_api_twitter', 
			array( 'label_for' => 'youxi_external_api_twitter_consumer_key' )
		);
		add_settings_field(
			'youxi_external_api_twitter_consumer_secret', 
			esc_html__( 'Consumer Secret', 'helium' ), 
			array( $this, 'settings_field_callback' ), 
			'youxi_external_api_twitter_option', 
			'youxi_external_api_twitter', 
			array( 'label_for' => 'youxi_external_api_twitter_consumer_secret' )
		);
		add_settings_field(
			'youxi_external_api_twitter_oauth_token', 
			esc_html__( 'OAuth Token', 'helium' ), 
			array( $this, 'settings_field_callback' ), 
			'youxi_external_api_twitter_option', 
			'youxi_external_api_twitter', 
			array( 'label_for' => 'youxi_external_api_twitter_oauth_token' )
		);
		add_settings_field(
			'youxi_external_api_twitter_oauth_token_secret', 
			esc_html__( 'OAuth Token Secret', 'helium' ), 
			array( $this, 'settings_field_callback' ), 
			'youxi_external_api_twitter_option', 
			'youxi_external_api_twitter', 
			array( 'label_for' => 'youxi_external_api_twitter_oauth_token_secret' )
		);

		// Register the setting
		register_setting(
			'youxi_external_api_twitter_option', 
			'youxi_external_api_twitter_option'
		);
	}

	public function settings_section_callback() {
		
		$instructions = array(
			wp_kses_post( __( 'Go to <a href="http://dev.twitter.com/apps" target="_blank">http://dev.twitter.com/apps</a> and sign in with your Twitter account.', 'helium' ) ), 
			wp_kses_post( __( 'Create a new app by clicking the button labeled <strong>Create New App</strong> on the right side.', 'helium' ) ), 
			wp_kses_post( __( 'Fill in the details of your app, leaving the <strong>Callback URL</strong> field blank.', 'helium' ) ), 
			wp_kses_post( __( 'Once you\'ve created the app, go to <strong>Keys and Access Tokens</strong> tab on the app details page.', 'helium' ) ), 
			wp_kses_post( __( 'Copy the <strong>Consumer Key</strong> and <strong>Consumer Secret</strong> into the appropriate fields below.', 'helium' ) ), 
			wp_kses_post( __( 'Then go to the bottom of the page and click on <strong>Create my access token</strong>.', 'helium' ) ), 
			wp_kses_post( __( 'Copy the <strong>Access Token</strong> to the <strong>OAuth Token</strong> field below.', 'helium' ) ), 
			wp_kses_post( __( 'Copy the <strong>Access Token Secret</strong> to the <strong>OAuth Token Secret</strong> field below.', 'helium' ) )
		);

		echo '<ol><li>' . implode( '</li><li>', $instructions ) . '</li></ol>';
	}

	public function settings_field_callback( $args ) {

		// Can not proceed without the `label_for` argument
		if( empty( $args['label_for'] ) ) {
			return;
		}

		$field_id  = $args['label_for'];
		$field_key = str_replace( 'youxi_external_api_twitter_', '', $field_id );

		$twitter_api_keys = get_option( 'youxi_external_api_twitter_option' );
		$current_value    = empty( $twitter_api_keys[ $field_key ] ) ? '' : $twitter_api_keys[ $field_key ];		

		echo '<input name="youxi_external_api_twitter_option[' . esc_attr( $field_key ) . ']" type="text" id="' . esc_attr( $field_id ) . '" value="' . esc_attr( $current_value ) . '" class="regular-text">';
	}

	public function display_settings() {

		settings_fields( 'youxi_external_api_twitter_option' );
		do_settings_sections( 'youxi_external_api_twitter_option' );
		submit_button();
	}
}

class Youxi_External_API_Instagram extends Youxi_External_API_Base {

	public function __construct() {

		parent::__construct( esc_html__( 'Instagram', 'helium' ) );

		/* User wants to remove authentication data */
		if( isset( $_GET['action'], $_GET['_wpnonce'] ) && 'remove_auth' == $_GET['action'] ) {

			if( wp_verify_nonce( $_GET['_wpnonce'], 'youxi_external_api_instagram_remove_auth' ) ) {

				delete_option( 'youxi_external_api_instagram_option' );
			}
		}

		$this->option_object  = get_option( 'youxi_external_api_instagram_option', array() );
		$this->option_haskeys = ! empty( $this->option_object['client_id'] ) && ! empty( $this->option_object['client_secret'] );
		$this->authenticated  = $this->validate_authentication();

		if( ! $this->authenticated && ! empty( $_GET['code'] ) ) {

			/* Looks like we're set */
			if( $this->option_haskeys ) {

				/* The Instagram Access Token URL */
				$request_url  = apply_filters( 'youxi_external_api_instagram_access_token_url', 'https://api.instagram.com/oauth/access_token' );

				/* Construct Request arguments */
				$request_args = array(
					'timeout' => 10, 
					'sslverify' => false, 
					'body' => array(
						'client_id'     => $this->option_object['client_id'], 
						'client_secret' => $this->option_object['client_secret'], 
						'grant_type'    => 'authorization_code', 
						'redirect_uri'  => admin_url( 'options-general.php?page=youxi_external_api&tab=instagram' ), 
						'code'          => $_GET['code']
					)
				);

				/* Send a POST request to Instagram */
				$response = wp_safe_remote_post( $request_url, $request_args );

				/* Looks like we got the access token */
				if( 200 == wp_remote_retrieve_response_code( $response ) ) {

					$response_body   = wp_remote_retrieve_body( $response );
					$response_object = json_decode( $response_body, true );

					/* Valid instagram access_token responses must contain `access_token` and `user` */
					if( isset( $response_object['access_token'], $response_object['user'] ) ) {

						$this->authenticated = true;

						/* Store the access_token and access_token_hash */
						$this->option_object['access_token']      = $response_object['access_token'];
						$this->option_object['access_token_hash'] = md5( $this->option_object['client_id'] . $this->option_object['client_secret'] );

						update_option( 'youxi_external_api_instagram_option', $this->option_object );
					}
				}
			}
		}
	}

	private function validate_authentication() {

		if( empty( $this->option_object['access_token'] ) || empty( $this->option_object['access_token_hash'] ) ) {
			return false;
		}

		if( empty( $this->option_object['client_id'] ) || empty( $this->option_object['client_secret'] ) ) {
			return false;
		}

		if( $this->option_object['access_token_hash'] != md5( $this->option_object['client_id'] . $this->option_object['client_secret'] ) ) {
			return false;
		}

		return true;
	}

	public function register_settings() {

		// If the theme options don't exist, create them.
		if( ! get_option( 'youxi_external_api_instagram_option' ) ) {  
			add_option( 'youxi_external_api_instagram_option', array(), '', 'no' );
		}

		// Add the settings section
		add_settings_section(
			'youxi_external_api_instagram', 
			esc_html__( 'Instagram Authentication', 'helium' ), 
			array( $this, 'settings_section_callback' ), 
			'youxi_external_api_instagram_option'
		);


		// Add the settings fields
		add_settings_field(
			'youxi_external_api_instagram_client_id', 
			esc_html__( 'Client ID', 'helium' ), 
			array( $this, 'settings_field_callback' ), 
			'youxi_external_api_instagram_option', 
			'youxi_external_api_instagram', 
			array( 'label_for' => 'youxi_external_api_instagram_client_id' )
		);
		add_settings_field(
			'youxi_external_api_instagram_client_secret', 
			esc_html__( 'Client Secret', 'helium' ), 
			array( $this, 'settings_field_callback' ), 
			'youxi_external_api_instagram_option', 
			'youxi_external_api_instagram', 
			array( 'label_for' => 'youxi_external_api_instagram_client_secret' )
		);


		// Register the setting
		register_setting(
			'youxi_external_api_instagram_option', 
			'youxi_external_api_instagram_option'
		);
	}

	public function settings_section_callback() {

		if( $this->authenticated ) {

			/* We already have the access token */
			echo '<div class="updated inline">';

				echo wpautop( wp_kses_post( __( '<strong>Congratulations!</strong><br>Your website is right now fully authenticated to Instagram.', 'helium' ) ) . '<br>' . 
					wp_kses_post( __( 'Authentication could expire for many reasons, in that case please click the button below to remove all your authentication data and reauthenticate with Instagram.', 'helium' ) )
				);

				$remove_auth_url = add_query_arg( array(
					'action' => 'remove_auth', 
					'_wpnonce' => wp_create_nonce( 'youxi_external_api_instagram_remove_auth' )
				));
				echo wpautop( sprintf( '<a class="button" href="%s">%s</a>', esc_url( $remove_auth_url ), esc_html__( 'Remove Authentication Data', 'helium' ) ) );

			echo '</div>';

			return;
		}

		 if( $this->option_haskeys ) {

		 	$auth_arg = array(
				'client_id'     => urlencode( $this->option_object['client_id'] ), 
				'redirect_uri'  => urlencode( admin_url( 'options-general.php?page=youxi_external_api&tab=instagram' ) ), 
				'response_type' => 'code'
			);
			$auth_url = add_query_arg( $auth_arg, 'https://api.instagram.com/oauth/authorize/' );

			if( isset( $_GET['error_reason'], $_GET['error_description'] ) ) {

				echo '<div class="error inline">';

					echo wpautop( '<strong>' . esc_html__( 'There was an error in the authentication process', 'helium' ) . '</strong><br>' . 
						esc_html( $_GET['error_description'] )
					);

				echo '</div>';
			}

			echo '<div class="updated inline">';

				echo wpautop( '<strong>' . esc_html__( 'Looks like you have your Client ID and Client Secret', 'helium' ) . '</strong><br>' . 
					esc_html__( 'Click the button below to authenticate your website on Instagram.', 'helium' )
				);

				echo wpautop( sprintf( '<a class="button" href="%s">%s</a>', esc_url( $auth_url ), esc_html__( 'Click Here to Authenticate', 'helium' ) ) );

			echo '</div>';

		} else {

			$instructions = array(
				__( 'Sign in with your Instagram account and go to <a href="https://www.instagram.com/developer/" target="_blank">https://www.instagram.com/developer/</a>', 'helium' ), 
				__( 'Click on <strong>Manage Clients</strong> menu on the top right side.', 'helium' ), 
				__( 'Click on the button labeled <strong>Register a New Client</strong> on the top right side.', 'helium' ), 
				__( 'Fill in the details and make sure the <strong>Valid redirect URIs</strong> field is exactly the value in the <strong>Redirect URI</strong> box below.', 'helium' ), 
				__( 'Once you\'ve created the app, copy <strong>Client ID</strong> and <strong>Client Secret</strong> to the appropriate fields below.', 'helium' ), 
				__( 'Click on <strong>Save Changes</strong>', 'helium' )
			);

			echo '<ol><li>' . implode( '</li><li>', $instructions ) . '</li></ol>';

			echo '<div class="notice inline">' . wpautop( '<strong>' . esc_html__( 'Redirect URI', 'helium' ) . '</strong><br>' . esc_url( admin_url( 'options-general.php?page=youxi_external_api&tab=instagram' ) ) ) . '</div>';
		}
	}

	public function settings_field_callback( $args ) {

		// Can not proceed without the `label_for` argument
		if( empty( $args['label_for'] ) ) {
			return;
		}

		$field_id  = $args['label_for'];
		$field_key = str_replace( 'youxi_external_api_instagram_', '', $field_id );

		$current_value = empty( $this->option_object[ $field_key ] ) ? '' : $this->option_object[ $field_key ];

		echo '<input name="youxi_external_api_instagram_option[' . esc_attr( $field_key ) . ']" type="text" id="' . esc_attr( $field_id ) . '" value="' . esc_attr( $current_value ) . '" class="regular-text">';
	}

	public function display_settings() {

		settings_fields( 'youxi_external_api_instagram_option' );

		do_settings_sections( 'youxi_external_api_instagram_option' );

		submit_button();
	}
}
