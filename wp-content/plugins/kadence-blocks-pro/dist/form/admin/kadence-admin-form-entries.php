<?php
/**
 * Admin Form Entries page
 *
 * @package Kadence Blocks Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Admin Form Entries list
 */
class KB_Form_Admin_Entries {

	/**
	 * Instance Control
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Entry WP_List_Table object
	 *
	 * @var object
	 */
	public $entries_table_list;
	/**
	 * KB_Form_Admin_Entries constructor.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	/**
	 * KB_Form_Admin_Entries constructor.
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'add_menu' ), 20 );
			add_filter( 'set-screen-option', array( $this, 'entries_page_set_option' ), 10, 3 );
			add_action( 'wp_ajax_kadence_form_entries_export', array( $this, 'do_ajax_entries_export' ) );
			add_action( 'admin_init', array( $this, 'download_export_file' ) );
		}

	}
	/**
	 * Add option page menu
	 */
	public function add_menu() {
		$page = add_submenu_page( 'kadence-blocks',  __( 'Kadence Blocks - Form Entries', 'kadence-blocks-pro' ), __( 'Form Entries', 'kadence-blocks-pro' ), 'edit_pages', 'kadence-blocks-entries', array( $this, 'page_output' ) );
		//$page = add_options_page( __( 'Kadence Blocks - Form Entries', 'kadence-blocks-pro' ), __( 'Kadence Form Entries', 'kadence-blocks-pro' ), 'edit_pages', 'kadence-blocks-entries', array( $this, 'page_output' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'scripts' ) );
		add_action( 'load-' . $page, array( $this, 'entries_page_init' ) );
	}
	/**
	 * Loads entries screen option.
	 */
	public function entries_page_init() {
		if ( ! isset( $_GET['view-entry'] ) ) {
			$this->entries_table_list = new KB_Form_Admin_Entries_Table_List();

			// Add screen option.
			add_screen_option(
				'per_page',
				array(
					'default' => 20,
					'option'  => 'kb_entries_per_page',
				)
			);
		}
	}
	/**
	 * Save the screen option setting.
	 *
	 * @param string $status The default value for the filter. Using anything other than false assumes you are handling saving the option.
	 * @param string $option The option name.
	 * @param array  $value  Whatever option you're setting.
	 */
	public function entries_page_set_option( $status, $option, $value ) {
		if ( isset( $_POST['wp_screen_options_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wp_screen_options_nonce'] ) ), 'wp_screen_options_nonce' ) ) {
			if ( 'kb_entries_per_page' === $option ) {
				$value = isset( $_POST['kb_entries_per_page'] ) ? $_POST['kb_entries_per_page'] : []; // WPCS: Sanitization ok.
			}
		}

		return $value;
	}
	/**
	 * Page output.
	 */
	public function page_output() {
		?>
		<div class="kt_plugin_welcome_title_head">
			<div class="kt_plugin_welcome_head_container">
				<div class="kt_plugin_welcome_logo">
					<img src="<?php echo KBP_URL . 'dist/settings/img/kadence-logo.png'; ?>">
				</div>
				<div class="kadence_blocks_dash_version">
					<span>
						<?php echo esc_html( apply_filters( 'kadence_blocks_brand_name', 'Kadence Blocks' ) ); ?>
					</span>
				</div>
			</div>
		</div>
		<?php
		if ( isset( $_GET['view-entry'] ) ) {
			$form_id  = isset( $_GET['form_id'] ) ? absint( $_GET['form_id'] ) : 0;
			$entry_id = isset( $_GET['view-entry'] ) ? absint( $_GET['view-entry'] ) : 0;
			$entries = new KBP\Queries\Entry();
			$entry = $entries->get_item( $entry_id );
			$this->single_view_output( $entry );
		} else {
			$this->table_list_output();
		}
	}
	/**
	 * Table list output.
	 */
	public function single_view_output( $entry ) {
		?>
		<div id="kb-form-entries-single" class="kb-form-entries wrap">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Form Entry', 'kadence-blocks-pro' ); ?></h1>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=kadence-blocks-entries' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Back to All Entries', 'kadence-blocks-pro' ); ?></a>
			<hr class="wp-header-end">

			<?php settings_errors(); ?>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder">
					<div class="kadence-blocks-single-entry-view">
						<div id="kadence-blocks-entry-fields" class="postbox">
							<h2 class="hndle">
								<?php // translators: #%2$s refers to the entry name. ?>
								<span><?php printf( __( '%1$s : Entry #%2$s', 'kadence-blocks-pro' ), esc_html( $entry->get_name() ), absint( $entry->get_id() ) ); ?></span>
							</h2>
							<table class="kb-form-entries-single-table wp-list-table widefat fixed striped posts">
								<tbody>
								<?php
									$meta = get_metadata( 'kbp_form_entry', $entry->get_id() );
									if ( $meta ) {
										if ( is_array( $meta ) ) {
											foreach ( $meta as $meta_key => $meta_value ) {
												if ( 'kb_field_' === substr( $meta_key, 0, 9 ) ) {
													$field = maybe_unserialize( $meta[ $meta_key ][ 0 ] );
													$value = ( is_array( $field['value'] ) ? implode( ', ', $field['value'] ) : $field['value'] );
													echo '<tr class="kadence-forms-entry-field"><th>';
													echo '<strong>' . $field['label'] . ':</strong> ' . ( ! empty( $value ) ? $value : __( 'No Data', 'kadence-blocks-pro' ) );
													echo '</th></tr>';
												}
											}
										} else {
											echo '<tr class="kadence-forms-entry-field"><th>';
											echo '<strong>' . __( 'No Data', 'kadence-blocks-pro' ) . '</strong>';
											echo '</th></tr>';
										}
									} else {
										echo '<tr class="kadence-forms-entry-field"><th>';
										echo '<strong>' . __( 'No Data', 'kadence-blocks-pro' ) . '</strong>';
										echo '</th></tr>';
									}
								?>
								</tbody>
							</table>
						</div>
						<div id="postbox-container-1" class="postbox-container kadence-blocks-entry-details">
							<div id="kadence-blocks-entry-details" class="postbox">
									<h2 class="hndle">
										<span><?php esc_html_e( 'Entry Details', 'kadence-blocks-pro' ); ?></span>
									</h2>
									<div class="inside">
										<div class="kadence-blocks-entry-details-meta">
											<p class="kadence-blocks-entry-id">
												<span class="dashicons dashicons-admin-network"></span>
												<?php esc_html_e( 'Entry ID:', 'kadence-blocks-pro' ); ?>
												<strong><?php echo absint( $entry->get_id() ); ?></strong>
											</p>
											<p class="kadence-blocks-entry-form-id" data-form-id="<?php echo esc_attr( $entry->get_form_id() ); ?>">
												<span class="dashicons dashicons-id"></span>
												<?php esc_html_e( 'Form ID:', 'kadence-blocks-pro' ); ?>
												<strong><?php echo esc_html( $entry->get_form_id() ); ?></strong>
											</p>
											<p class="kadence-blocks-entry-form-name" data-form-id="<?php echo esc_attr( $entry->get_form_id() ); ?>">
												<span class="dashicons dashicons-nametag"></span>
												<?php esc_html_e( 'Form Name:', 'kadence-blocks-pro' ); ?>
												<strong><?php echo esc_html( $entry->get_name() ); ?></strong>
											</p>
											<p class="kadence-blocks-entry-date">
												<span class="dashicons dashicons-calendar"></span>
												<?php esc_html_e( 'Submitted:', 'kadence-blocks-pro' ); ?>
												<strong><?php echo $entry->get_date_created(); ?></strong>
											</p>

											<?php if ( ! empty( $entry->get_user_id() ) && 0 !== $entry->get_user_id() ) : ?>
												<p class="kadence-blocks-entry-user">
													<span class="dashicons dashicons-admin-users"></span>
													<?php
													esc_html_e( 'User:', 'kadence-blocks-pro' );
													$user      = get_userdata( $entry->get_user_id() );
													$user_name = esc_html( ! empty( $user->display_name ) ? $user->display_name : $user->user_login );
													$user_url  = esc_url(
														add_query_arg(
															array(
																'user_id' => absint( $user->ID ),
															),
															admin_url( 'user-edit.php' )
														)
													);
													?>
													<strong><a href="<?php echo $user_url; ?>"><?php echo $user_name; ?></a></strong>
												</p>
											<?php endif; ?>
											<?php if ( ! empty( $entry->get_referer() ) ) : ?>
												<p class="kadence-blocks-entry-referer">
													<span class="dashicons dashicons-text-page"></span>
													<?php esc_html_e( 'Page:', 'kadence-blocks-pro' ); ?>
													<strong><?php echo '<a href="' . esc_url( $entry->get_referer() ) . '"> ' . esc_html( $entry->get_referer() ) . '</a>'; ?></strong>
												</p>
											<?php endif; ?>
											<?php if ( ! empty( $entry->get_user_ip() ) ) : ?>
												<p class="kadence-blocks-entry-ip">
													<span class="dashicons dashicons-location"></span>
													<?php esc_html_e( 'User IP:', 'kadence-blocks-pro' ); ?>
													<strong><?php echo esc_html( $entry->get_user_ip() ); ?></strong>
												</p>
											<?php endif; ?>
											<?php if ( ! empty( $entry->get_user_device() ) ) : ?>
												<p class="kadence-blocks-entry-device">
													<span class="dashicons dashicons-laptop"></span>
													<?php esc_html_e( 'User Device:', 'kadence-blocks-pro' ); ?>
													<strong><?php echo esc_html( $entry->get_user_device() ); ?></strong>
												</p>
											<?php endif; ?>
										</div>

										<div id="major-publishing-actions">
											<div id="delete-action">
												<?php
												echo sprintf(
													'<a class="button button-large button-secondary" href="%s"><span class="dashicons dashicons-trash"></span>%s</a>',
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
													__( 'Move to trash', 'kadence-blocks-pro' )
												);
												?>
											</div>
											<div class="clear"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<?php
	}
	/**
	 * Table list output.
	 */
	public function table_list_output() {
		$this->entries_table_list->prepare_start();
		$this->entries_table_list->process_bulk_action();
		$this->entries_table_list->prepare_items();
		?>
		<div id="kb-form-entries-list" class="kb-form-entries wrap">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Form Entries', 'kadence-blocks-pro' ); ?></h1>
			<hr class="wp-header-end">

			<?php settings_errors(); ?>
			<div id="poststuff">
				<form id="kb-form-entries-form" method="post">
					<?php
						$this->entries_table_list->views();
						$this->entries_table_list->search_box( __( 'Search entries', 'kadence-blocks-pro' ), 'kbp_form_entry' );
						$this->entries_table_list->display();
					?>
				</form>
				<br class="clear">
			</div>
		</div>
		<?php
	}
	/**
	 * Loads admin style sheets and scripts
	 */
	public function scripts() {
		wp_enqueue_style( 'kadence-blocks-admin-css', KBP_URL . '/dist/form/admin/entries-style.css', array(), KBP_VERSION, 'all' );
		wp_enqueue_script( 'kadence-blocks-form-admin-js', KBP_URL . '/dist/form/admin/export/kb-form-export.js', array( 'jquery', 'jquery-ui-dialog' ), KBP_VERSION, true );
		wp_localize_script(
			'kadence-blocks-form-admin-js',
			'kb_admin_form_params',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'wpnonce' => wp_create_nonce( 'kb_forms' ),
			)
		);
	}
	/**
	 * AJAX callback for doing the actual export to the CSV file.
	 */
	public function do_ajax_entries_export() {
		check_ajax_referer( 'kb_forms', 'security' );
		if ( ! current_user_can( 'export' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient privileges to export entries.', 'kadence-blocks-pro' ) ) );
		}
		require_once KBP_PATH . 'dist/form/admin/export/class-kb-entry-csv-exporter.php';

		$step     = isset( $_POST['step'] ) ? absint( $_POST['step'] ) : 1;
		$exporter = new KB_Entry_CSV_Exporter();

		if ( ! empty( $_POST['columns'] ) ) {
			$exporter->set_column_names( wp_unslash( $_POST['columns'] ) );
		}

		if ( ! empty( $_POST['selected_columns'] ) ) {
			$exporter->set_columns_to_export( wp_unslash( $_POST['selected_columns'] ) );
		}

		if ( ! empty( $_POST['export_extra_meta'] ) ) {
			$exporter->enable_extra_meta_export( true );
		}

		if ( ! empty( $_POST['export_form_id'] ) ) {
			$exporter->set_form_id_to_export( wp_unslash( $_POST['export_form_id'] ) );
		}

		if ( ! empty( $_POST['filename'] ) ) {
			$exporter->set_filename( wp_unslash( $_POST['filename'] ) );
		}

		$exporter->set_page( $step );
		$exporter->generate_file();

		$query_args = apply_filters(
			'kadence_blocks_export_get_ajax_query_args',
			array(
				'nonce'    => wp_create_nonce( 'entry-csv' ),
				'action'   => 'download_entries_csv',
				'filename' => $exporter->get_filename(),
			)
		);
		if ( 100 === absint( $exporter->get_percent_complete() ) ) {
			wp_send_json_success(
				array(
					'step'       => 'done',
					'percentage' => 100,
					'url'        => add_query_arg( $query_args, admin_url( 'admin.php?page=kadence-blocks-entries' ) ),
				)
			);
		} else {
			wp_send_json_success(
				array(
					'step'       => ++$step,
					'percentage' => $exporter->get_percent_complete(),
					'columns'    => $exporter->get_column_names(),
				)
			);
		}
	}
	/**
	 * Serve the generated file.
	 */
	public function download_export_file() {
		if ( isset( $_GET['action'], $_GET['nonce'] ) && wp_verify_nonce( wp_unslash( $_GET['nonce'] ), 'entry-csv' ) && 'download_entries_csv' === wp_unslash( $_GET['action'] ) ) { // WPCS: input var ok, sanitization ok.
			require_once KBP_PATH . 'dist/form/admin/export/class-kb-entry-csv-exporter.php';
			$exporter = new KB_Entry_CSV_Exporter();

			if ( ! empty( $_GET['filename'] ) ) { // WPCS: input var ok.
				$exporter->set_filename( wp_unslash( $_GET['filename'] ) ); // WPCS: input var ok, sanitization ok.
			}

			$exporter->export();
		}
	}
}
KB_Form_Admin_Entries::get_instance();
