<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

if( ! class_exists( 'Youxi_Portfolio_Permalinks' ) ):

class Youxi_Portfolio_Permalinks {

	public static function init() {
		add_action( 'current_screen', array( __CLASS__, 'maybe_init' ) );
	}

	/**
	 * Initialize settings on the right screen
	 */
	public static function maybe_init() {

		if( ! $screen = get_current_screen() ) {
			return;
		}

		if( 'options-permalink' == $screen->id ) {
			self::settings_init();
			self::settings_save();
		}
	}

	/**
	 * Add portfolio permalink settings
	 */
	public static function settings_init() {

		// Add a section to the permalinks page
		add_settings_section(
			'youxi-portfolio-permalink', 
			esc_html__( 'Portfolio Permalinks', 'youxi' ), 
			array( __CLASS__, 'settings' ), 
			'permalink'
		);

		add_settings_field(
			'youxi_portfolio_category_slug',           // id
			esc_html__( 'Portfolio category base', 'youxi' ),  // setting title
			array( __CLASS__, 'category_slug_input' ), // display callback
			'permalink',                               // settings page
			'optional'                                 // settings section
		);
	}

	public static function settings() {

		$permalinks = get_option( 'youxi_portfolio_permalinks' );
		$portfolio_permalink = isset( $permalinks['portfolio_base'] ) ? $permalinks['portfolio_base'] : '';

		echo wpautop( esc_html__( 'These settings control the permalinks used specifically for portfolio.', 'youxi' ) );
		
		?>
		<table class="form-table portfolio-permalink-structure">
			<tr>
				<th><label><input name="youxi_portfolio_permalink" type="radio" value="" <?php checked( empty( $portfolio_permalink ), true ); ?> /> <?php _e( 'Default', 'youxi' ); ?></label></th>
				<td><code><?php echo esc_html( home_url() ) ?>/<?php echo esc_html( Youxi_Portfolio::post_type_name() ); ?>/<?php echo _x( 'sample-portfolio', 'sample permalink structure', 'youxi' ); ?>/</code></td>
			</tr>
			<tr>
				<th><label><input name="youxi_portfolio_permalink" type="radio" value="custom" <?php checked( empty( $portfolio_permalink ), false ); ?> /> <?php _e( 'Custom Structure', 'youxi' ); ?></label></th>
				<td><code><?php echo esc_html( home_url() ); ?></code> <input name="youxi_portfolio_permalink_structure" id="youxi_portfolio_permalink_structure" type="text" value="<?php echo esc_attr( $portfolio_permalink ); ?>" class="regular-text code" /></td>
			</tr>
		</table><?php
	}

	/**
	 * Save the settings.
	 */
	public static function settings_save() {

		if ( ! is_admin() ) {
			return;
		}

		// We need to save the options ourselves; settings api does not trigger save for the permalinks page
		if ( isset( $_POST['permalink_structure'] ) || isset( $_POST['category_base'] ) && isset( $_POST['youxi_portfolio_permalink'] ) ) {

			// Actual permalinks setting
			if ( ! $permalinks = get_option( 'youxi_portfolio_permalinks' ) ) {
				$permalinks = array();
			}

			$portfolio_permalink = sanitize_text_field( $_POST['youxi_portfolio_permalink'] );
			if ( 'custom' === $portfolio_permalink ) {
				$portfolio_permalink = trim( sanitize_text_field( $_POST['youxi_portfolio_permalink_structure'] ), '/' );
				if( ! empty( $portfolio_permalink ) ) {
					$portfolio_permalink = '/' . trailingslashit( $portfolio_permalink );
				}
			}

			$permalinks = array(
				'category_base'  => untrailingslashit( sanitize_text_field( $_POST['youxi_portfolio_category_slug'] ) ), 
				'portfolio_base' => empty( $portfolio_permalink ) ? false : $portfolio_permalink
			);

			update_option( 'youxi_portfolio_permalinks', $permalinks );
		}
	}

	public static function category_slug_input() {
		$permalinks = get_option( 'youxi_portfolio_permalinks' );
		?>
		<input name="youxi_portfolio_category_slug" type="text" class="regular-text code" value="<?php if( isset( $permalinks['category_base'] ) ) echo esc_attr( $permalinks['category_base'] ); ?>" placeholder="<?php echo esc_attr( Youxi_Portfolio::taxonomy_name() ) ?>" />
		<?php
	}
}
endif;

Youxi_Portfolio_Permalinks::init();
