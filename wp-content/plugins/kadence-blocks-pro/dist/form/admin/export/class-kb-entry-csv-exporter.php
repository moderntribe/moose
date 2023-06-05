<?php
/**
 * Handles Entry CSV export. This is a near copy from the woocommerce csv exporter.
 *
 * @package  Kadence Blocks Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Include dependencies.
 */
if ( ! class_exists( 'KB_CSV_Batch_Exporter', false ) ) {
	require_once KBP_PATH . 'dist/form/admin/export/abstract-kb-csv-batch-exporter.php';
}

/**
 * KB_Entry_CSV_Exporter Class.
 */
class KB_Entry_CSV_Exporter extends KB_CSV_Batch_Exporter {

	/**
	 * Entries Class.
	 *
	 * @var class object
	 */
	public $entries;
	/**
	 * Type of export used in filter names.
	 *
	 * @var string
	 */
	protected $export_type = 'entry';

	/**
	 * Should extra meta be exported?
	 *
	 * @var boolean
	 */
	protected $enable_extra_meta_export = false;

	/**
	 * Which form is being exported.
	 *
	 * @var string
	 */
	protected $form_id_to_export = '';

	/**
	 * Which Dates are being exported.
	 *
	 * @var array
	 */
	protected $post_dates = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Should extra meta be exported?
	 *
	 * @param bool $enable_extra_meta_export Should meta be exported.
	 */
	public function enable_extra_meta_export( $enable_extra_meta_export ) {
		$this->enable_extra_meta_export = (bool) $enable_extra_meta_export;
	}

	/**
	 * Form id to export
	 *
	 * @param string $form_id_to_export Form ids to export, empty string exports all.
	 * @return void
	 */
	public function set_form_id_to_export( $form_id_to_export ) {
		$this->form_id_to_export = $form_id_to_export;
	}

	/**
	 * Return an array of columns to export.
	 *
	 * @since  3.1.0
	 * @return array
	 */
	public function get_default_column_names() {
		return apply_filters(
			"kadence_blocks_export_{$this->export_type}_default_columns",
			array(
				'form_name' => __( 'Form Name', 'kadence-blocks-pro' ),
				'date'      => __( 'Date Created', 'kadence-blocks-pro' ),
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
		$result = $this->entries->query( $args );
		return $result;
	}

	/**
	 * Handles getting entry class.
	 */
	public function entries() {
		if ( $this->entries ) {
			return $this->entries;
		}
		$this->entries = new KBP\Queries\Entry();
		return $this->entries;
	}
	/**
	 * Prepare data for export.
	 */
	public function prepare_data_to_export() {
		global $form_id;
		$this->entries();
		$entry_status = $this->get_status();
		if ( ! in_array( $entry_status, array( 'all', 'trash' ) ) ) {
			$entry_status = 'all';
		}
		$per_page     = $this->get_limit();
		$current_page = $this->get_page();
		$args = array(
			'status' => ( 'all' === $entry_status ? 'publish' : $entry_status ),
			'number' => $per_page,
			'offset' => ( $current_page - 1 ) * $per_page,
		);
		if ( ! empty( $this->form_id_to_export ) ) {
			$form_id = $this->form_id_to_export;
			$args['form_id'] = $this->form_id_to_export;
		}
		$this->total_rows = $this->record_count( ( 'all' === $entry_status ? 'publish' : $entry_status ) );
		$entries          = $this->get_entries( $args );
		$this->row_data   = array();

		foreach ( $entries as $entry ) {
			$this->row_data[] = $this->generate_row_data( $entry );
		}
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
		if ( $form_id ) {
			$args['form_id'] = $form_id;
		}
		$result  = $this->entries->query( $args );

		return $result;
	}
	/**
	 * Get status.
	 *
	 * @return string
	 */
	public function get_status() {
		return apply_filters( "kadence_blocks_{$this->export_type}_export_status", 'all' );
	}
	/**
	 * Take an entry and generate row data from it for export.
	 *
	 * @param object $entry Entry object.
	 *
	 * @return array
	 */
	protected function generate_row_data( $entry ) {
		$columns = $this->get_column_names();
		$row     = array();
		$this->prepare_fields_for_export( $entry, $row );
		foreach ( $columns as $column_id => $column_name ) {
			$column_id = strstr( $column_id, ':' ) ? current( explode( ':', $column_id ) ) : $column_id;
			$value     = '';

			// Skip some columns if dynamically handled later or if we're being selective.
			if ( ! $this->is_column_exporting( $column_id ) ) {
				continue;
			}
			if ( is_callable( array( $this, "get_column_value_{$column_id}" ) ) ) {
				// Handle special columns which don't map 1:1 to entry data.
				$value = $this->{"get_column_value_{$column_id}"}( $entry );

			} elseif ( is_callable( array( $entry, "get_{$column_id}" ) ) ) {
				// Default and custom handling.
				$value = $entry->{"get_{$column_id}"}( 'edit' );
			}
			$row[ $column_id ] = $value;
		}
		if ( $this->enable_extra_meta_export ) {
			$extras = array(
				'id'          => __( 'Entry ID', 'kadence-blocks-pro' ),
				'form_id'     => __( 'Form ID', 'kadence-blocks-pro' ),
				'referer'     => __( 'Page', 'kadence-blocks-pro' ),
				'user_ip'     => __( 'User IP', 'kadence-blocks-pro' ),
				'user_device' => __( 'User Device', 'kadence-blocks-pro' )
			);
			foreach ( $extras as $key => $label ) {
				$this->column_names[ $key ] = $label;
				$value     = '';
				if ( is_callable( array( $this, "get_column_value_{$key}" ) ) ) {
					// Handle special columns which don't map 1:1 to entry data.
					$value = $this->{"get_column_value_{$key}"}( $entry );
				} elseif ( is_callable( array( $entry, "get_{$key}" ) ) ) {
					// Default and custom handling.
					$value = $entry->{"get_{$key}"}( 'edit' );
				}
				$row[ $key ] = $value;
			}
		}
		return apply_filters( 'kadence_blocks_entry_export_row_data', $row, $entry );
	}
	/**
	 * Method for date column
	 *
	 * @param object $entry the entry.
	 *
	 * @return string
	 */
	protected function get_column_value_date( $entry ) {
		return $entry->get_date_created();
	}
	/**
	 * Method for form id column
	 *
	 * @param object $entry the entry.
	 *
	 * @return string
	 */
	protected function get_column_value_form_id( $entry ) {
		return $entry->get_form_id();
	}

	/**
	 * Method for form name column
	 *
	 * @param object $entry the entry.
	 *
	 * @return string
	 */
	protected function get_column_value_form_name( $entry ) {

		return $entry->get_name();
	}
	/**
	 * Export fields data.
	 *
	 * @param object $entry entry being exported.
	 * @param array  $row   Row being exported.
	 */
	protected function prepare_fields_for_export( $entry, &$row ) {
		$meta = get_metadata( 'kbp_form_entry', $entry->get_id() );
		$prevent_duplicates = array();
		if ( $meta ) {
			if ( is_array( $meta ) ) {
				$i = 1;
				foreach ( $meta as $meta_key => $meta_value ) {
					if ( 'kb_field_' === substr( $meta_key, 0, 9 ) ) {
						$field = maybe_unserialize( $meta[ $meta_key ][ 0 ] );
						$value = ( is_array( $field['value'] ) ? implode( ', ', $field['value'] ) : $field['value'] );
						if ( in_array( 'fields:' . sanitize_title_with_dashes( $field['label'] ), $prevent_duplicates, true ) ) {
							$this->column_names[ 'fields:' . $i . sanitize_title_with_dashes( $field['label'] ) ] = $field['label'];
							$row[ 'fields:' . $i . sanitize_title_with_dashes( $field['label'] ) ] = ( ! empty( $value ) ? $value : __( 'No Data', 'kadence-blocks-pro' ) );
						} else {
							$this->column_names[ 'fields:' . sanitize_title_with_dashes( $field['label'] ) ] = $field['label'];
							$row[ 'fields:' . sanitize_title_with_dashes( $field['label'] ) ] = ( ! empty( $value ) ? $value : __( 'No Data', 'kadence-blocks-pro' ) );
						}
					}
					$i++;
				}
			}
		}
	}
}
