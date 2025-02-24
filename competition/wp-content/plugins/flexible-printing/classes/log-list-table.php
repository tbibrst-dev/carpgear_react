<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit();

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once (ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Flexible_Printing_Log_List_Table extends WP_List_Table {

	private $_plugin;
	private $_integrations;

	public function __construct( Flexible_Printing_Plugin $plugin ) {
		parent::__construct( array (
			'singular' => __( 'log', 'flexible-printing' ),
			'plural' => __( 'logs', 'flexible-printing' ),
		) );
		$this->_plugin = $plugin;
		$this->_integrations = $this->_plugin->get_integrations()->get_integrations();
	}

	function column_default( $item, $column_name ) {
		return $item[$column_name];
	}

	function column_time( $item ) {
		$time = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), intval( $item['time'] ) );
		return $time;
	}

	function column_integration( $item ) {
		if ( isset( $this->_integrations[$item['integration']] ) ) {
			return $this->_integrations[$item['integration']]->title;
		}
		if ( $item['integration'] == 'fp' ) {
			return __( 'Flexible Printing', 'flexible-printing' );
		}
		return $item['integration'];
	}

	function column_details( $item ) {
		return '<pre>' . $item['details'] . '</pre>';
	}

	function column_user_login( $item ) {
		return $item['user_login'];
	}

	function column_type( $item ) {
		if ( $item['type'] == 'job' ) {
			return __( 'Print Job', 'flexible-printing' );
		}
		if ( $item['type'] == 'error' ) {
			return __( 'Error', 'flexible-printing' );
		}
		return $item['type'];
	}

	function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="id[]" value="%s" />', $item['id'] );
	}

	function get_columns() {
		$columns = array (
			'cb' 			=> '<input type="checkbox" />', // Render a checkbox instead of text
//			'id' 			=> __( 'ID', 'flexible-printing' ),
			'time' 			=> __( 'Time', 'flexible-printing' ),
			'type' 			=> __( 'Type', 'flexible-printing' ),
			'user_login'	=> __( 'User', 'flexible-printing' ),
			'integration'   => __( 'Integration', 'flexible-printing' ),
			'printer'       => __( 'Printer', 'flexible-printing' ),
			'title'         => __( 'Title', 'flexible-printing' ),
			'message'       => __( 'Message', 'flexible-printing' ),
		);
		return $columns;
	}

	function get_sortable_columns() {
		$sortable_columns = array (
			'user_login' => array (
				'user_login',
				true
			),
			'time' => array (
				'time',
				true
			),
			'id' => array (
				'id',
				true
			),
		);
		return $sortable_columns;
	}

	function extra_tablenav( $which ) {
		parent::extra_tablenav( $which );
		if ( $which == 'top' ) {
			$selected_type = '';
			if ( isset( $_REQUEST['type'] ) ) {
				$selected_type = esc_attr($_REQUEST['type']);
			}
			$selected_integration = '';
			if ( isset( $_REQUEST['integration'] ) ) {
				$selected_integration = esc_attr($_REQUEST['integration']);
			}
			?>
			<div class="alignleft actions">
                <select name="type">
                    <option value=""><?php _e( 'All types', 'flexible-printing' ); ?></option>
                    <option value="job" <?php selected( $selected_type, 'job' ); ?>><?php _e( 'Print Job', 'flexible-printing' ); ?></option>
                    <option value="error" <?php selected( $selected_type, 'error' ); ?>><?php _e( 'Error', 'flexible-printing' ); ?></option>
                </select>
				<select name="integration">
					<option value=""><?php _e( 'All integrations', 'flexible-printing' ); ?></option>
					<?php foreach ( $this->_integrations as $integration ) : ?>
						<option
							value="<?php echo $integration->id; ?>" <?php selected( $selected_integration, $integration->id ); ?>><?php echo $integration->title; ?></option>
					<?php endforeach; ?>
					<option value="fp" <?php selected( $selected_integration, 'fp' ); ?>><?php _e( 'Flexible Printing', 'flexible-printing' ); ?></option>
				</select>
				<input type="submit" name="filter_action" id="post-query-submit" class="button"
				       value="<?php _e( 'Filter', 'flexible-printing' ); ?>">
			</div>
			<?php
		}
	}

	function get_bulk_actions() {
		$actions = array (
			'delete' => 'Delete'
		);
		return $actions;
	}

	function process_bulk_action() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'fp_log'; // do not forget about tables prefix

		if ( 'delete' === $this->current_action() && isset( $_GET[ Flexible_Printing_Log::NONCE_NAME ] ) && wp_verify_nonce( $_GET[ Flexible_Printing_Log::NONCE_NAME ], Flexible_Printing_Log::NONCE_ACTION ) && current_user_can( 'manage_options' ) ) {
			$ids = isset( $_REQUEST['id'] ) ? array_map("sanitize_text_field", $_REQUEST['id'] ) : array ();
			if ( is_array( $ids ) )
				$ids = implode( ',', $ids );

			if ( ! empty( $ids ) ) {
				$wpdb->query( "DELETE FROM $table_name WHERE id IN($ids)" );
			}
		}
	}

	function prepare_items() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'fp_log'; // do not forget about tables prefix
		$per_page = 20;

		$columns = $this->get_columns();
		$hidden = array (
//				'event'
		);
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array (
			$columns,
			$hidden,
			$sortable
		);

		$this->process_bulk_action();

		$where   = ' where 1 = 1 ';
		$paged   = 0;
		$orderby = 'id';
		$order   = 'desc';

		if ( isset( $_GET[ Flexible_Printing_Log::NONCE_NAME ] ) && wp_verify_nonce( $_GET[ Flexible_Printing_Log::NONCE_NAME ], Flexible_Printing_Log::NONCE_ACTION ) && current_user_can( 'manage_options' ) ) {
			if ( isset( $_REQUEST['where'] ) && isset( $_REQUEST['where_value'] ) ) {
				$where .= " and " . sanitize_text_field( $_REQUEST['where'] ) . " = '" . sanitize_text_field( $_REQUEST['where_value'] ) . "'";
			}
			if ( isset( $_REQUEST['integration'] ) && $_REQUEST['integration'] != '' ) {
				$where .= " and integration = '" . sanitize_text_field( $_REQUEST['integration'] ) . "'";
			}
			if ( isset( $_REQUEST['type'] ) && $_REQUEST['type'] != '' ) {
				$where .= " and type = '" . sanitize_text_field( $_REQUEST['type'] ) . "'";
			}

			$paged   = isset( $_REQUEST['paged'] ) ? max( 0, intval( $_REQUEST['paged'] ) - 1 ) : $paged;
			$orderby = ( isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array_keys( $this->get_sortable_columns() ) ) ) ? sanitize_text_field( $_REQUEST['orderby'] ) : $orderby;
			// prepare query params, as usual current page, order by and order direction

			$order = ( isset( $_REQUEST['order'] ) && in_array( $_REQUEST['order'], array(
					'asc',
					'desc'
				) ) ) ? sanitize_text_field( $_REQUEST['order'] ) : $order;
			//
		}

		// will be used in pagination settings
		$total_items = $wpdb->get_var( "SELECT COUNT(id) FROM $table_name $where" );

		$this->items = $wpdb->get_results( $wpdb->prepare( "SELECT id, type, integration, printer, title, time, user_login, job_id, message,	details text
        FROM $table_name
        $where
        ORDER BY $orderby $order
        LIMIT %d OFFSET %d", $per_page, $paged * $per_page ), ARRAY_A );

		$this->set_pagination_args(
			array (
				'total_items' => $total_items, // total items defined above
				'per_page' => $per_page, // per page constant defined at top of method
				'total_pages' => ceil( $total_items / $per_page )
			) // calculate pages count
		);
	}

}
