<?php
/*
Plugin Name: Youxi Importer
Plugin URI: http://www.themeforest.net/user/nagaemas
Description: This plugin helps importing a WordPress theme demo content. The plugin is also able to import front page displays, active nav menus, customizer options and widgets.
Version: 2.0
Author: YouxiThemes
Author URI: http://www.themeforest.net/user/nagaemas
License: Envato Marketplace Licence

Changelog:
2.0 - October 29, 2016
- Improvement: Complete rewrite for better task handling
- Improvement: Attachments are now imported using a single request per attachment preventing timeout failures

1.0
- Initial release
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

define( 'YOUXI_IMPORTER_VERSION', '2.0' );

define( 'YOUXI_IMPORTER_DIR', plugin_dir_path( __FILE__ ) );

define( 'YOUXI_IMPORTER_URL', plugin_dir_url( __FILE__ ) );

define( 'YOUXI_IMPORTER_LANG_DIR', dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

final class Youxi_Importer {

	private $page_hook;

	private $demo_tasks;

	private static $instance;

	private function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		add_action( 'wp_ajax_youxi_importer_do_import', array( $this, 'do_import' ) );

		add_action( 'admin_init', array( $this, 'prepare_demos' ) );
	}

	public static function run() {

		if( ! is_a( self::$instance, get_class() ) ) {
			self::$instance = new self();
		}
	}

	public function admin_menu() {

		$this->page_hook = add_management_page(
			esc_html__( 'Youxi Importer', 'youxi' ), esc_html__( 'Youxi Importer', 'youxi' ), 
			'import', 'youxi-importer', array( $this, 'importer_page_callback' )
		);
	}

	public function admin_enqueue_scripts( $hook ) {

		if( $hook != $this->page_hook ) {
			return;
		}

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'youxi-importer', YOUXI_IMPORTER_URL . "assets/css/youxi.importer.css", array(), YOUXI_IMPORTER_VERSION );
		wp_enqueue_script( 'youxi-importer', YOUXI_IMPORTER_URL . "assets/js/youxi.importer{$suffix}.js", array(), YOUXI_IMPORTER_VERSION, true );

		wp_localize_script( 'youxi-importer', '_youxiImporterSettings', $this->get_importer_settings() );
	}

	public function do_import() {

		if( isset( $_POST['demo_id'], $_POST['task_id'] ) ) {

			$demo_id   = $_POST['demo_id'];
			$task_id   = $_POST['task_id'];
			$task_args = isset( $_POST['task_args'] ) ? $_POST['task_args'] : array();

			if( ! isset( $this->demo_tasks[ $demo_id ], $this->demo_tasks[ $demo_id ][ $task_id ] ) ) {
				wp_send_json_error( new WP_Error( esc_html__( 'An invalid demo or task was provided', 'youxi' ) ) );
			}

			check_ajax_referer( "youxi_importer_nonce_{$demo_id}_{$task_id}" );

			$task   = $this->demo_tasks[ $demo_id ][ $task_id ];
			$result = $task->run( $task_args );

			if( is_wp_error( $result ) ) {
				wp_send_json_error( $result );
			}

			wp_send_json_success( $result );
		}

		wp_send_json_error( new WP_Error( esc_html__( 'Invalid request, demo parameters were not provided', 'youxi' ) ) );
	}

	public function prepare_demos() {

		$this->demo_tasks = array();

		foreach( $this->get_demos() as $demo_id => $demo_args ) {

			if( ! empty( $demo_args['tasks'] ) && is_array( $demo_args['tasks'] ) ) {

				$this->demo_tasks[ $demo_id ] = array();

				foreach( $demo_args['tasks'] as $task_id => $task_args ) {

					$task = $this->get_task_from_id( $task_id, $task_args );

					if( is_a( $task, 'Youxi_Importer_Task' ) ) {
						$this->demo_tasks[ $demo_id ][ $task_id ] = $task;
					}
				}
			}
		}
	}

	public function get_demos() {
		return apply_filters( 'youxi_importer_demos', array() );
	}

	public function get_demos_js_vars() {

		$demos_js_vars = array();
		foreach( $this->demo_tasks as $demo_id => $demo_tasks ) {
			
			$demos_js_vars[ $demo_id ] = array();

			foreach( $demo_tasks as $task_id => $task ) {

				$demos_js_vars[ $demo_id ][] = array(
					'id'       => $task_id, 
					'demo_id'  => $demo_id, 
					'nonce'    => wp_create_nonce( "youxi_importer_nonce_{$demo_id}_{$task_id}" ), 
					'priority' => $task->priority(), 
					'params'   => $task->js_params(), 
					'messages' => $task->messages()
				);
			}
		}

		return $demos_js_vars;
	}

	public function get_importer_settings() {

		return apply_filters( 'youxi_importer_settings', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ), 
			'ajaxAction' => 'youxi_importer_do_import', 
			'demos' => $this->get_demos_js_vars(), 
			'completionMessage' => esc_html__( 'Import completed with {count} failure(s)', 'youxi' ), 
			'importFinishTimeout' => 2000
		));
	}

	public function get_task_from_id( $task_id, $task_args ) {

		switch( $task_id ) {
			case 'customizer':
				return new Youxi_Importer_Task_Customizer( $task_args );
			case 'frontpage_displays':
				return new Youxi_Importer_Task_Frontpage_Displays( $task_args );
			case 'nav_menu_locations':
				return new Youxi_Importer_Task_Nav_Menu_Locations( $task_args );
			case 'widgets':
				return new Youxi_Importer_Task_Widgets( $task_args );
			case 'wordpress':
				return new Youxi_Importer_Task_WordPress( $task_args );
		}
	}

	public function importer_page_callback() {
		
		?>
		<div class="wrap">

			<h2><?php esc_html_e( 'Youxi Importer', 'youxi' ) ?></h2>

			<?php if( $available_demos = $this->get_demos() ): ?>

			<div class="theme-browser demo-browser rendered">

				<div class="themes demos">

					<?php foreach( $available_demos as $id => $args ):
						$args = wp_parse_args( $args, array(
							'screenshot' => '', 
							'name' => ''
						));
					?>

					<div class="theme demo-content active" tabindex="0" data-demo-id="<?php echo esc_attr( $id ) ?>">

						<div class="theme-screenshot demo-screenshot">
							<img src="<?php echo esc_url( $args['screenshot'] ) ?>" alt="<?php echo esc_attr( $args['name'] ) ?>">
						</div>

						<span class="more-details"></span>

						<h3 class="theme-name demo-name"><?php echo esc_html( $args['name'] ) ?></h3>

						<div class="theme-actions demo-actions">
							<button type="button" class="button button-primary"><?php esc_html_e( 'Import', 'youxi' ) ?></button>
						</div>

					</div>

					<?php endforeach; ?>

				</div>

				<br class="clear">

			</div>

			<?php else:
				echo '<div class="error settings-error"><p>' . esc_html__( 'There are no available demo content to import.', 'youxi' ) . '</p></div>';
			endif;
			?>
		</div>
		<?php
	}
}

function youxi_importer_init() {

	/* Load Language File */
	load_plugin_textdomain( 'youxi', false, YOUXI_IMPORTER_LANG_DIR );

	require YOUXI_IMPORTER_DIR . 'tasks/base.php';
	require YOUXI_IMPORTER_DIR . 'tasks/customizer.php';
	require YOUXI_IMPORTER_DIR . 'tasks/frontpage-displays.php';
	require YOUXI_IMPORTER_DIR . 'tasks/nav-menu-locations.php';
	require YOUXI_IMPORTER_DIR . 'tasks/widgets.php';
	require YOUXI_IMPORTER_DIR . 'tasks/wordpress.php';

	Youxi_Importer::run();
}
add_action( 'plugins_loaded', 'youxi_importer_init' );
