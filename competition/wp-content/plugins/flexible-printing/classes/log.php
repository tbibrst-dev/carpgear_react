<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Flexible_Printing_Log {

	const NONCE_NAME       = 'fp_security';
	const NONCE_ACTION     = 'fp_save_settings';

	private $_plugin;

	public function __construct( Flexible_Printing_Plugin $plugin ) {
		$this->_plugin = $plugin;
		$this->hooks();
	}

	public function hooks() {
		add_action( 'flexible_printing_log', array( $this, 'flexible_printing_log' ), 10, 6 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 2 );
	}

	function admin_menu() {
		add_submenu_page(
			'flexible-printing',
			__( 'Logs', 'flexible-printing' ),
            __( 'Logs', 'flexible-printing' ),
			'manage_options',
			'flexible-printing',
			array( $this, 'log_page' )
		);
	}

	public function log_page() {
		$table = new Flexible_Printing_Log_List_Table( $this->_plugin );
		$table->prepare_items();
		?>
		<div class="wrap flexible-printing-logs">
			<h1><?php _e( 'Flexible Printing - Log', 'flexible-printing' ); ?></h1>
			<form action="<?php echo admin_url( 'admin.php?page=flexible-printing' ); ?>" method="get">
				<?php wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME ); ?>
				<input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ) ?>"/>
				<?php
				$table->display();
				?>
			</form>
		</div>
		<?php
	}

	public function flexible_printing_log( $integration, $printer, $title, $job_id, $message, $details ) {
		global $wpdb;
		$table_name   = $wpdb->prefix . 'fp_log';
		$current_user = wp_get_current_user();
		if ( $job_id != '' ) {
			$type = 'job';
		} else {
			$type = 'error';
		}
		if ( $printer === false ) {
			$printer = __( 'Not set!', 'flexible-printing' );
		}
		$wpdb->insert(
			$table_name,
			array(
				'type'          => $type,
				'integration'   => $integration,
				'printer'       => $printer,
				'title'         => $title,
				'time'          => current_time( 'timestamp' ),
				'user_login'    => $current_user->user_login,
				'job_id'        => $job_id,
				'message'       => $message,
				'details'       => print_r( $details, true ),
			),
			array(
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
			)
		);
	}

}
