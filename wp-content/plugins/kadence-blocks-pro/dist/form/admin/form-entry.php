<?php
/**
 * Entry Object Class
 *
 * @package Kadence Blocks Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class KBP_Entry {

	/**
	 * Entry ID.
	 *
	 * @var int
	 */
	protected $id = 0;

	/**
	 * Corresponding form name.
	 *
	 * @var int
	 */
	protected $name = '';

	/**
	 * Corresponding form ID.
	 *
	 * @var int
	 */
	protected $form_id = '';

	/**
	 * Corresponding post ID number.
	 *
	 * @var int
	 */
	protected $post_id = 0;

	/**
	 * User ID.
	 *
	 * @var int
	 */
	protected $user_id = 0;

	/**
	 * Date this entry posted.
	 *
	 * @var string
	 */
	protected $date_created = '';

	/**
	 * The user IP address.
	 *
	 * @var int
	 */
	protected $user_ip = 0;

	/**
	 * The page the form was submitted from
	 *
	 * @var string
	 */
	protected $referer = '';

	/**
	 * Date this customer last logged in.
	 *
	 * @var string
	 */
	protected $status = 'publish';
	/**
	 * Entry user device
	 *
	 * @var string
	 */
	protected $user_device = '';

	/**
	 * KBP_Entry constructor.
	 *
	 * @param object $entry_object Entry object row from the database.
	 *
	 * @access public
	 * @since  1.2.8
	 * @return void
	 */
	public function __construct( $entry_object ) {

		if ( ! is_object( $entry_object ) ) {
			return;
		}

		$this->setup_entry( $entry_object );

	}

	/**
	 * Setup properties.
	 *
	 * @param object $entry_object Row from the database.
	 *
	 * @access private
	 * @since  1.2.8
	 * @return bool
	 */
	private function setup_entry( $entry_object ) {

		if ( ! is_object( $entry_object ) ) {
			return false;
		}

		$vars = get_object_vars( $entry_object );

		foreach ( $vars as $key => $value ) {
			switch ( $key ) {
				case 'date_created' :
					if ( '0000-00-00 00:00:00' === $value ) {
						$value = '';
					}
					break;

				// case 'user_ip' :
				// 	$value = maybe_unserialize( $value );
				// 	break;
			}

			$this->{$key} = $value;
		}

		if ( empty( $this->id ) ) {
			return false;
		}

		return true;

	}

	/**
	 * Update the entry data in the database.
	 *
	 * @param array $data               {
	 *                                  Array of arguments.
	 *
	 * @type int    $form_id            ID of the corresponding form.
	 * @type int    $user_id            Optional. ID of the corresponding user account.
	 * @type string $date_created       Optional. Date this customer registered in MySQL format.
	 * @type ini    $user_ip            Optional. IP address of entry.
	 * @type string $referer            Optional. The referer url.
	 * @type string $user_device        Optional. The entry device.
	 *                    }
	 *
	 * @access public
	 * @since  1.2.8
	 * @return bool True if update was successful, false on failure.
	 */
	public function update( $data = array() ) {

		$entries = new KBP\Queries\Entry();

		$updated = $entries->update_item( $this->get_id(), $data );

		if ( $updated ) {
			foreach ( $data as $key => $value ) {
				$this->{$key} = maybe_unserialize( $value );
			}

			return true;
		}

		return false;

	}

	/**
	 * Get the ID of the entry.
	 *
	 * @access public
	 * @since  1.2.8
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get the ID of the corresponding form.
	 *
	 * @access public
	 * @since  1.2.8
	 * @return int
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Get the ID of the corresponding form.
	 *
	 * @access public
	 * @since  1.2.8
	 * @return int
	 */
	public function get_form_id() {
		return $this->form_id;
	}

	/**
	 * Get the ID of the corresponding form.
	 *
	 * @access public
	 * @since  1.2.8
	 * @return int
	 */
	public function get_post_id() {
		return $this->post_id;
	}

	/**
	 * Get the ID of the corresponding user account.
	 *
	 * @access public
	 * @since  1.2.8
	 * @return int
	 */
	public function get_user_id() {
		return $this->user_id;
	}

	/**
	 * Returns the date the form entry was posted.
	 *
	 * @param bool $formatted Whether or not to format the returned date.
	 *
	 * @access public
	 * @since  1.2.8
	 * @return string
	 */
	public function get_date_created( $formatted = true ) {

		$date_created = $this->date_created;

		if ( $formatted && ! empty( $date_created ) ) {
			$date_created = date_i18n( get_option( 'date_format' ), strtotime( $date_created, current_time( 'timestamp' ) ) ) . ' ' . date_i18n( get_option( 'time_format' ), strtotime( $date_created, current_time( 'timestamp' ) ) );
		}

		return $date_created;

	}

	/**
	 * Return the user ip
	 *
	 * @access public
	 * @since  1.2.8
	 * @return array
	 */
	public function get_user_ip() {

		$user_ip = $this->user_ip;

		return $user_ip;
	}
	/**
	 * Get the referer of the corresponding entry.
	 *
	 * @access public
	 * @since  1.2.8
	 * @return int
	 */
	public function get_referer() {
		return $this->referer;
	}
	/**
	 * Get the user device of the corresponding entry.
	 *
	 * @access public
	 * @since  1.2.8
	 * @return int
	 */
	public function get_user_device() {
		return $this->user_device;
	}
	/**
	 * Return the user ip
	 *
	 * @access public
	 * @since  1.2.8
	 * @return array
	 */
	public function get_status() {

		$status = $this->status;

		return $status;
	}

	/**
	 * Add meta data field to a entry
	 *
	 * @param int    $entry_id      entry ID.
	 * @param string $meta_key      Meta data name.
	 * @param mixed  $meta_value    Meta data value. Must be serializable if non-scalar.
	 * @param bool   $unique        Optional. Whether the same key should not be added. Default false.
	 *
	 * @since 1.2.8
	 * @return false|int
	 */
	public function add_field( $entry_id, $meta_key, $meta_value, $unique = false ) {
		return add_metadata( 'kbp_form_entry', $entry_id, $meta_key, $meta_value, $unique );
	}

	/**
	 * Remove meta data field from entry.
	 *
	 * @param int    $entry_id      entry ID.
	 * @param string $meta_key      Meta data name.
	 * @param mixed  $meta_value    Meta data value. Must be serializable if non-scalar.
	 *
	 * @since 1.2.8
	 * @return false|int
	 */
	public function delete_field( $entry_id, $meta_key, $meta_value = '' ) {
		return delete_metadata( 'kbp_form_entry', $entry_id, $meta_key, $meta_value );
	}


	/**
	 * Retrieve entry meta field for a entry.
	 *
	 * @param  int   $entry_id Entry ID.
	 * @param string $key      Optional. The meta key to retrieve. By default, returns data for all keys. Default
	 *                         empty.
	 * @param bool   $single   Optional, default is false. If true, return only the first value of the specified
	 *                         meta_key. This parameter has no effect if meta_key is not specified.
	 *
	 * @since 1.2.8
	 * @return mixed Will be an array if $single is false. Will be value of meta data field if $single is true.
	 */
	public function get_field( $entry_id, $key = '', $single = false ) {
		return get_metadata( 'kbp_form_entry', $entry_id, $key, $single );
	}

}
