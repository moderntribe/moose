<?php
/**
 * Admin Form Entries List
 *
 * @package Kadence Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
/**
 * Admin Form Entries list
 *
 * @package Kadence Blocks
 */
class KB_Form_Admin_Entries_Table_List extends WP_List_Table {

	/**
	 * Form ID.
	 *
	 * @var int
	 */
	public $form_id;

	/**
	 * Entries Class.
	 *
	 * @var class object
	 */
	public $entries;

	/**
	 * KB_Form_Admin_Entries_Table_List constructor.
	 */
	public function __construct() {

		parent::__construct(
			array(
				'singular' => __( 'Entry', 'kadence-blocks-pro' ), //singular name of the listed records
				'plural'   => __( 'Entries', 'kadence-blocks-pro' ), //plural name of the listed records
				'ajax'     => false //should this table support ajax?.
			)
		);
	}
	/**
	 * Retrieve entries data from the database
	 *
	 * @param array $args query args.
	 *
	 * @return mixed
	 */
	public function get_entries( $args ) {
		global $form_id;
		$search = $this->get_search();
		$status_options = array(
			'publish' => __( 'Published', 'kadence-blocks-pro' ),
			'trash'   => __( 'Trash', 'kadence-blocks-pro' ),
		);
		if ( ! empty( $search ) ) {
			global $wpdb;
			$query = array();
			$query[] = "SELECT DISTINCT {$wpdb->prefix}kbp_form_entry.id FROM {$wpdb->prefix}kbp_form_entry INNER JOIN {$wpdb->prefix}kbp_form_entrymeta";
			$like    = '%' . $wpdb->esc_like( $search ) . '%';
			$query[] = $wpdb->prepare( 'WHERE meta_value LIKE %s', $like );
			$query[] = "AND {$wpdb->prefix}kbp_form_entry.id = {$wpdb->prefix}kbp_form_entrymeta.kbp_form_entry_id";
			if ( ! empty( $args['status'] ) ) {
				$query[] = $wpdb->prepare( 'AND `status` = %s', isset( $status_options[ $args['status'] ] ) ? $args['status'] : 'publish' );
			}
			if ( -1 < $args['number'] ) {
				$query[] = $wpdb->prepare( 'LIMIT %d', absint( $args['number'] ) );
			}
			if ( 0 < $args['offset'] ) {
				$query[] = $wpdb->prepare( 'OFFSET %d', absint( $args['offset'] ) );
			}
			$results = $wpdb->get_results( implode( ' ', $query ), ARRAY_A );
			$results = $this->entries->override_get_objects( wp_list_pluck( $results, 'id' ) );
			return $results;
		}
		$result  = $this->entries->query( $args );
		return $result;
	}
	/**
	 * Retrieve entry data from the database
	 *
	 * @return mixed
	 */
	public function get_entry( $id ) {
		$item = $this->entries->get_item( $id );
		return $item;
	}

	/**
	 * Retrieve forms data from the database
	 *
	 * @return mixed
	 */
	public function get_all_forms() {
		global $wpdb;
		$query[] = "SELECT DISTINCT {$wpdb->prefix}kbp_form_entry.form_id FROM {$wpdb->prefix}kbp_form_entry";
		$results  = $wpdb->get_results( implode( ' ', $query ), ARRAY_A );
		$form_ids = wp_list_pluck( $results, 'form_id' );
		$forms = array();
		foreach ( $form_ids as $id ) {
			$item = $this->entries->get_item_by( 'form_id', $id );
			if ( $item ) {
				$forms[ $id ] = array(
					'id' => $id,
					'name' => $item->get_name(),
				);
			}
		}
		return $forms;
	}

	/**
	 * Get a request var, or return the default if not set.
	 *
	 * @param string $var
	 * @param mixed  $default
	 *
	 * @return mixed Un-sanitized request var
	 */
	public function get_request_var( $var = '', $default = false ) {
		return isset( $_REQUEST[$var] )
			? sanitize_text_field( wp_unslash( $_REQUEST[$var] ) )
			: $default;
	}

	/**
	 * Retrieve the current page number.
	 *
	 * @return int Current page number.
	 */
	protected function get_search() {
		return rawurldecode( trim( $this->get_request_var( 's', '' ) ) );
	}
	/**
	 * Delete a Entry record.
	 *
	 * @param int $entry_id the Entry ID.
	 */
	public function delete_entry( $entry_id ) {
		$success = $this->entries->delete_item( $entry_id );
	}
	/**
	 * Trash a Entry record.
	 *
	 * @param int $entry_id the Entry ID.
	 */
	public function trash_entry( $entry_id ) {
		$success = $this->entries->update_item( $entry_id, array( 'status' => 'trash' ) );
	}
	/**
	 * Restore a Entry record.
	 *
	 * @param int $entry_id the Entry ID.
	 */
	public function restore_entry( $entry_id ) {
		$success = $this->entries->update_item( $entry_id, array( 'status' => 'publish' ) );
	}
	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public function record_count( $status = 'publish' ) {
		global $form_id;
		$args = array(
			'count'  => true,
			'status' => $status,
		);
		$search = $this->get_search();
		$status_options = array(
			'publish' => __( 'Published', 'kadence-blocks-pro' ),
			'trash'   => __( 'Trash', 'kadence-blocks-pro' ),
		);
		if ( ! empty( $search ) ) {
				global $wpdb;
				$query = array();
				$query[] = "SELECT DISTINCT {$wpdb->prefix}kbp_form_entry.id FROM {$wpdb->prefix}kbp_form_entry INNER JOIN {$wpdb->prefix}kbp_form_entrymeta WHERE {$wpdb->prefix}kbp_form_entry.id = {$wpdb->prefix}kbp_form_entrymeta.kbp_form_entry_id";
				$like    = '%' . $wpdb->esc_like( $search ) . '%';
				$query[] = $wpdb->prepare( 'AND meta_value LIKE %s', $like );
				if ( ! empty( $status ) ) {
					$query[] = $wpdb->prepare( "AND {$wpdb->prefix}kbp_form_entry.status = %s", isset( $status_options[ $status ] ) ? $status : 'publish' );
				}
				$results = $wpdb->get_results( implode( ' ', $query ), ARRAY_A );
				$results = count( wp_list_pluck( $results, 'id' ) );
				return $results;
		}
		if ( $form_id ) {
			$args['form_id'] = $form_id;
		}
		$result  = $this->entries->query( $args );

		return $result;
	}

	/**
	 * Text displayed when no entry data is available
	 */
	public function no_items() {
		esc_html_e( 'No entries avaliable.', 'kadence-blocks-pro' );
	}
	/**
	 * Render the bulk edit checkbox
	 *
	 * @param object $entry the entry
	 *
	 * @return string
	 */
	public function column_cb( $entry ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />',
			$entry->get_id()
		);
	}
	/**
	 * Method for name column
	 *
	 * @param object $entry the entry
	 *
	 * @return string
	 */
	public function column_id( $entry ) {
		global $entry_status, $form_id;
		if ( 'trash' !== $entry_status ) {
			$actions = array(
				'view' => sprintf(
					'<a href="%s">%s</a>',
					esc_url(
						add_query_arg(
							array(
								'view-entry' => $entry->get_id(),
								'form_id'  => $form_id,
							),
							admin_url( 'admin.php?page=kadence-blocks-entries' )
						)
					),
					__( 'View', 'kadence-blocks-pro' )
				),
				'trash' => sprintf(
					'<a href="%s">%s</a>',
					esc_url(
						wp_nonce_url(
							add_query_arg(
								array(
									'action' => 'trash',
									'entry'  => $entry->get_id(),
								),
								admin_url( 'admin.php?page=kadence-blocks-entries' )
							),
							'kb_form_delete_entry'
						)
					),
					__( 'Trash', 'kadence-blocks-pro' )
				),
			);
		} else {
			$actions = array(
				'untrash' => sprintf(
					'<a href="%s">%s</a>',
					esc_url(
						wp_nonce_url(
							add_query_arg(
								array(
									'entry_status' => 'trash',
									'action'       => 'untrash',
									'entry'        => $entry->get_id(),
								),
								admin_url( 'admin.php?page=kadence-blocks-entries' )
							),
							'kb_form_delete_entry'
						)
					),
					__( 'Restore', 'kadence-blocks-pro' )
				),
				'delete' => sprintf(
					'<a href="%s">%s</a>',
					esc_url(
						wp_nonce_url(
							add_query_arg(
								array(
									'entry_status' => 'trash',
									'action'       => 'delete',
									'entry'        => $entry->get_id(),
								),
								admin_url( 'admin.php?page=kadence-blocks-entries' )
							),
							'kb_form_delete_entry'
						)
					),
					__( 'Delete Permanently', 'kadence-blocks-pro' )
				),
			);
		}

		return implode( ' <span class="sep">|</span> ', $actions );
	}
	/**
	 * Method for name column
	 *
	 * @param object $entry the entry
	 *
	 * @return string
	 */
	public function column_date( $entry ) {

		return $entry->get_date_created();
	}
	/**
	 * Method for name column
	 *
	 * @param object $entry the entry
	 *
	 * @return string
	 */
	public function column_field_1( $entry ) {

		$field_1 = get_metadata( 'kbp_form_entry', $entry->get_id(), 'kb_field_0', true );
		if ( $field_1 ) {
			$field_1 = maybe_unserialize( $field_1 );
			$value = ( is_array( $field_1['value'] ) ? implode( ', ', $field_1['value'] ) : $field_1['value'] );
			$title = '<strong>' . $field_1['label'] . '</strong> ' . $value;
		} else {
			$title = __( 'No Data', 'kadence_blocks' );
		}

		return $title;
	}

	/**
	 * Method for name column
	 *
	 * @param object $entry the entry
	 *
	 * @return string
	 */
	public function column_field_2( $entry ) {

		$field_2 = get_metadata( 'kbp_form_entry', $entry->get_id(), 'kb_field_1', true );
		if ( $field_2 ) {
			$field_2 = maybe_unserialize( $field_2 );
			$value = ( is_array( $field_2['value'] ) ? implode( ', ', $field_2['value'] ) : $field_2['value'] );
			$title = '<strong>' . $field_2['label'] . '</strong> ' . $value;
		} else {
			$title = __( 'No Data', 'kadence_blocks' );
		}

		return $title;
	}
	/**
	 * Method for form id column
	 *
	 * @param object $entry the entry
	 *
	 * @return string
	 */
	public function column_form_id( $entry ) {

		$title = $entry->get_form_id();

		return $title;
	}

	/**
	 * Method for form name column
	 *
	 * @param object $entry the entry
	 *
	 * @return string
	 */
	public function column_name( $entry ) {

		$title = $entry->get_name();

		return $title;
	}
	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param object $entry the entry
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $entry, $column_name ) {
		return print_r( $entry, true ); //Show the whole array for troubleshooting purposes.
	}

	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'cb'      => '<input type="checkbox" />',
			'id'      => __( 'Actions', 'kadence-blocks-pro' ),
			'form_id' => __( 'Form ID', 'kadence-blocks-pro' ),
			'date'    => __( 'Date Created', 'kadence-blocks-pro' ),
			'name'    => __( 'Form Name', 'kadence-blocks-pro' ),
			'field_1' => __( 'First Field', 'kadence-blocks-pro' ),
			'field_2' => __( 'Second Field', 'kadence-blocks-pro' ),
		);

		return $columns;
	}
	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array( 'name', true ),
			'date' => array( 'date', true ),
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		global $entry_status;
		if ( 'trash' === $entry_status ) {
			$actions['bulk-untrash'] = __( 'Restore' );
		}

		if ( 'trash' === $entry_status ) {
			$actions['bulk-delete'] = __( 'Delete Permanently' );
		} else {
			$actions['bulk-trash'] = __( 'Move to Trash' );
		}
		return $actions;
	}
	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function entries() {
		if ( $this->entries ) {
			return $this->entries;
		}
		$this->entries = new KBP\Queries\Entry();
		return $this->entries;
	}
	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_start() {
		$this->entries();
	}
	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {
		global $form_id, $entry_status;
		$this->_column_headers = $this->get_column_info();

		$entry_status = $this->get_request_var( 'entry_status', 'all' );
		if ( ! in_array( $entry_status, array( 'all', 'trash' ) ) ) {
			$entry_status = 'all';
		}
		$form_id      = $this->get_request_var( 'form_id' );
		$per_page     = $this->get_items_per_page( 'kb_entries_per_page', 20 );
		$current_page = $this->get_pagenum();
		$args = array(
			'status' => ( 'all' === $entry_status ? 'publish' : $entry_status ),
			'number' => $per_page,
			'offset' => ( $current_page - 1 ) * $per_page,
		);
		if ( $form_id ) {
			$args['form_id'] = $form_id;
		}
		$total_items  = $this->record_count();
		$this->set_pagination_args(
			array(
				'total_items' => $total_items, //WE have to calculate the total number of items.
				'per_page'    => $per_page //WE have to determine how many items to show on a page.
			)
		);
		$this->items = $this->get_entries( $args );

	}
	/**
	 * @return string|false
	 */
	public function current_action() {
		if ( isset( $_REQUEST['delete_all'] ) || isset( $_REQUEST['delete_all2'] ) ) {
			return 'delete_all';
		}

		return parent::current_action();
	}

	/**
	 * Handles Bulk Action
	 */
	public function process_bulk_action() {
		// Bail if a nonce was not supplied.
		if ( ! isset( $_REQUEST['_wpnonce'] ) ) {
			return;
		}
		$action      = $this->current_action();
		$page_number = $this->get_pagenum();
		$count       = 0;
		if ( $action ) {
			switch ( $action ) {
				case 'trash':
					check_admin_referer( 'kb_form_delete_entry' );
					$this->trash_entry( absint( $_GET['entry'] ) );
					$count ++;
					$this->show_admin_notice( $action, $count );
					break;
				case 'bulk-trash':
					check_admin_referer( 'bulk-entries' );
					$trash_ids = esc_sql( $_POST['bulk-delete'] );
					foreach ( $trash_ids as $id ) {
						$this->trash_entry( $id );
						$count ++;
					}
					$this->show_admin_notice( $action, $count );
					break;
				case 'untrash':
					check_admin_referer( 'kb_form_delete_entry' );
					$this->restore_entry( absint( $_GET['entry'] ) );
					$count ++;
					$this->show_admin_notice( $action, $count );
					break;
				case 'bulk-untrash':
					check_admin_referer( 'bulk-entries' );
					$restore_ids = esc_sql( $_POST['bulk-delete'] );
					foreach ( $restore_ids as $id ) {
						$this->restore_entry( $id );
						$count ++;
					}
					$this->show_admin_notice( $action, $count );
					break;
				case 'delete':
					check_admin_referer( 'kb_form_delete_entry' );
					$this->delete_entry( absint( $_GET['entry'] ) );
					$count ++;
					$this->show_admin_notice( $action, $count );
					break;
				case 'bulk-delete':
					check_admin_referer( 'bulk-entries' );
					$delete_ids = esc_sql( $_POST['bulk-delete'] );
					foreach ( $delete_ids as $id ) {
						$this->delete_entry( $id );
						$count ++;
					}
					$this->show_admin_notice( $action, $count );
					break;
				case 'delete_all':
					check_admin_referer( 'bulk-entries' );
					$args = array(
						'status' => 'trash',
					);
					$form_id = esc_sql( $_POST['form_id'] );
					if ( $form_id ) {
						$args['form_id'] = $form_id;
					}
					$delete_ids = $this->get_entries( $args );
					foreach ( $delete_ids as $item ) {
						$this->delete_entry( $item->get_id() );
						$count ++;
					}
					$this->show_admin_notice( $action, $count );
					break;
			}
		}
	}
	/**
	 * Show admin notice for bulk actions.
	 *
	 * @param string $action The action to show the notice for.
	 *
	 * @return void
	 */
	private function show_admin_notice( $action, $count = 1 ) {

		switch ( $action ) {
			case 'untrash':
			case 'bulk-untrash':
				add_settings_error(
					'bulk_action',
					'bulk_action',
					/* translators: %d: number of entries */
					sprintf( _n( '%d entry restored.', '%d entries restored.', $count, 'kadence-blocks-pro' ), $count ),
					'updated'
				);
				break;
			case 'delete':
			case 'bulk-delete':
				add_settings_error(
					'bulk_action',
					'bulk_action',
					/* translators: %d: number of entries */
					sprintf( _n( '%d entry permanently deleted.', '%d entries permanently deleted.', $count, 'kadence-blocks-pro' ), $count ),
					'updated'
				);
				break;
			case 'trash':
			case 'bulk-trash':
				add_settings_error(
					'bulk_action',
					'bulk_action',
					/* translators: %d: number of entries */
					sprintf( _n( '%d entry trashed.', '%d entries trashed.', $count, 'kadence-blocks-pro' ), $count ),
					'updated'
				);
				break;
			case 'delete_all':
				add_settings_error(
					'bulk_action',
					'bulk_action',
					__( 'Trash permanently deleted', 'kadence-blocks-pro' ),
					'updated'
				);
				break;
		}

	}
	/**
	 * Extra controls to be displayed between bulk actions and pagination.
	 *
	 * @param string $which The location of the extra table nav markup.
	 */
	protected function extra_tablenav( $which ) {
		global $entry_status;
		static $has_items;

		if ( ! isset( $has_items ) ) {
			$has_items = $this->has_items();
		}
		?>
		<div class="alignleft actions">
		<?php
		if ( 'top' === $which ) {
			ob_start();
			$this->forms_dropdown();
			$output = ob_get_clean();

			if ( ! empty( $output ) ) {
				echo $output;
				submit_button( __( 'Filter', 'kadence-blocks-pro' ), '', 'filter_action', false, array( 'id' => 'post-query-submit' ) );

				// Export CSV submit button.
				if ( apply_filters( 'kadence_blocks_forms_enable_csv_export', true ) && current_user_can( 'export' ) ) {
					submit_button( __( 'Export CSV', 'kadence-blocks-pro' ), '', 'export_action', false, array( 'id' => 'kb-export-csv-submit' ) );
					echo '<progress class="kadence-exporter-progress" style="display:none; margin:5px;" max="100" value="0"></progress>';
				}
			}
			if ( ( 'trash' === $entry_status ) && current_user_can( 'edit_pages' ) && $has_items ) {
				wp_nonce_field( 'bulk-destroy', '_destroy_nonce' );
				$title = esc_attr__( 'Empty Trash', 'kadence-blocks-pro' );
				submit_button( $title, 'apply', 'delete_all', false );
			}
		}
		?>
		</div>
		<?php
	}
	/**
	 * Display a form dropdown for filtering entries.
	 */
	public function forms_dropdown() {
		global $form_id;
		$forms = $this->get_all_forms();
		?>
		<label for="filter-by-form" class="screen-reader-text"><?php esc_html_e( 'Filter by form', 'kadence-blocks-pro' ); ?></label>
		<select name="form_id" id="filter-by-form">
			<option value="" <?php selected( $form_id, '' ); ?>><?php echo __( 'All', 'kadence-blocks-pro' ); ?></option>
			<?php foreach ( $forms as $id => $form ) : ?>
				<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $form_id, $id ); ?>><?php echo esc_html( $form['name'] ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php

	}
	/**
	 * @global int $post_id
	 * @global string $entry_status
	 * @global string $form_id
	 */
	protected function get_views() {
		global $entry_status, $form_id;

		$status_links = array();
		$num_entries = array(
			'all'   => $this->record_count(),
			'trash' => $this->record_count( 'trash' ),
		);

		$stati = array(
			/* translators: %s: Number of comments. */
			'all'       => _nx_noop(
				'All <span class="count">(%s)</span>',
				'All <span class="count">(%s)</span>',
				'comments'
			), // singular not used

			/* translators: %s: Number of comments. */
			'trash'     => _nx_noop(
				'Trash <span class="count">(%s)</span>',
				'Trash <span class="count">(%s)</span>',
				'comments'
			),
		);

		$link = admin_url( 'admin.php?page=kadence-blocks-entries' );
		if ( ! empty( $form_id ) && 'all' != $form_id ) {
			$link = add_query_arg( 'form_id', $form_id, $link );
		}

		foreach ( $stati as $status => $label ) {
			$current_link_attributes = '';

			if ( $status === $entry_status ) {
				$current_link_attributes = ' class="current" aria-current="page"';
			}

			$link = add_query_arg( 'entry_status', $status, $link );
			/*
			// I toyed with this, but decided against it. Leaving it in here in case anyone thinks it is a good idea. ~ Mark
			if ( !empty( $_REQUEST['s'] ) )
				$link = add_query_arg( 's', esc_attr( wp_unslash( $_REQUEST['s'] ) ), $link );
			*/
			$status_links[ $status ] = "<a href='$link'$current_link_attributes>" . sprintf(
				translate_nooped_plural( $label, $num_entries[ $status ] ),
				sprintf(
					'<span class="%s-count">%s</span>',
					$status,
					number_format_i18n( $num_entries[ $status ] )
				)
			) . '</a>';
		}

		/**
		 * Filters the comment status links.
		 *
		 * @since 2.5.0
		 * @since 5.1.0 The 'Mine' link was added.
		 *
		 * @param string[] $status_links An associative array of fully-formed comment status links. Includes 'All', 'Mine',
		 *                              'Pending', 'Approved', 'Spam', and 'Trash'.
		 */
		return apply_filters( 'comment_status_links', $status_links );
	}

}
