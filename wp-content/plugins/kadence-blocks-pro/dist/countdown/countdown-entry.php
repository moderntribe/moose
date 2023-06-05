<?php
/**
 * Entry Object Class for Countdowns.
 *
 * @package Kadence Blocks Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class KBP_Countdown_Entry {

	/**
	 * Entry ID.
	 *
	 * @var int
	 */
	protected $id = 0;

	/**
	 * Corresponding campaign id.
	 *
	 * @var int
	 */
	protected $campaign = '';

	/**
	 * Date this entry posted.
	 *
	 * @var string
	 */
	protected $end_date = '';

	/**
	 * Date this entry posted.
	 *
	 * @var string
	 */
	protected $remove_date = '';

	/**
	 * The user IP address.
	 *
	 * @var int
	 */
	protected $user_ip = 0;

	/**
	 * User ID.
	 *
	 * @var int
	 */
	protected $user_id = 0;

	/**
	 * KBP_Countdown_Entry constructor.
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
			// switch ( $key ) {
			// 	case 'user_ip' :
			// 		$value = maybe_unserialize( $value );
			// 		break;
			// }

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
	 * @type int    $campaign           The ID of the corresponding Campaign
	 * @type string $end_date           Optional. Timestamp.
	 * @type string $remove_date        Optional. Date this customer registered in MySQL format.
	 * @type ini    $user_ip            Optional. IP address of entry.
	 *                    }
	 *
	 * @access public
	 * @return bool True if update was successful, false on failure.
	 */
	public function update( $data = array() ) {

		$entries = new KBP\Queries\Countdown_Entry();

		$updated = $entries->update_item( $this->get_id(), $data );

		if ( $updated ) {
			// foreach ( $data as $key => $value ) {
			// 	$this->{$key} = maybe_unserialize( $value );
			// }

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
	public function get_campaign() {
		return $this->campaign;
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
	public function get_end_date( $formatted = false ) {

		return $this->end_date;

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
	public function get_remove_date( $formatted = false ) {

		$remove_date = $this->remove_date;

		if ( $formatted && ! empty( $remove_date ) ) {
			$remove_date = date_i18n( get_option( 'date_format' ), strtotime( $remove_date, current_time( 'timestamp' ) ) ) . ' ' . date_i18n( get_option( 'time_format' ), strtotime( $remove_date, current_time( 'timestamp' ) ) );
		}

		return $remove_date;

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

}
