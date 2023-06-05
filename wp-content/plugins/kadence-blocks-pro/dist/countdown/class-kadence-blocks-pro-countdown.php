<?php
/**
 * Manage Evergreen Campaigns.
 *
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manage Evergreen Campaigns.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Countdown {

	/**
	 * @var string $campaign_id
	 */
	public $campaign_id;

	/**
	 * @var string $slug
	 */
	public $slug;

	/**
	 * @var string $reset
	 */
	public $reset = 30;

	/**
	 * @var string $user_ip
	 */
	protected $user_ip;

	/**
	 * @var string $cookie_path
	 */
	protected $cookie_path;

	/**
	 * @var string $cookie_domain
	 */
	protected $cookie_domain;
	/**
	 * Entries Class.
	 *
	 * @var class object
	 */
	public $entries;

	/**
	 * Class Constructor.
	 *
	 * @param string $id the campaign id.
	 * @param string $slug the site slug.
	 */
	public function __construct( $id = null, $slug = null, $reset = null ) {
		$this->campaign_id = $id;
		$this->slug        = $slug;
		if ( $reset ) {
			$this->reset = $reset;
		}
		$this->user_ip       = $this->get_client_ip();
		$this->cookie_path   = defined( 'COOKIEPATH' ) ? COOKIEPATH : '';
		$this->cookie_domain = defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '';
	}
	/**
	 * Get the cookie name.
	 *
	 * @param string $cookie_name the cookie name for the campaign.
	 */
	public function cookie_name( $cookie_name ) {
		return $this->slug . '-' . $cookie_name;
	}
	/**
	 * Update the cookie.
	 *
	 * @param string $timestamp the timestamp for the end date.
	 */
	public function cookie_update( $timestamp ) {
		setcookie(
			$this->cookie_name( $this->campaign_id ),
			$timestamp,
			time() + MONTH_IN_SECONDS,
			$this->cookie_path,
			$this->cookie_domain
		);
	}
	/**
	 * Find cookie.
	 */
	public function get_cookie_date() {
		$cookie_name = $this->cookie_name( $this->campaign_id );

		if ( ! isset( $_COOKIE[ $cookie_name ] ) || empty( $_COOKIE[ $cookie_name ] ) ) {
			return null;
		}
		return sanitize_text_field( wp_unslash( $_COOKIE[ $cookie_name ] ) );
	}
	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function get_entries() {
		if ( $this->entries ) {
			return $this->entries;
		}
		$this->entries = new KBP\Queries\Countdown_Entry();
		return $this->entries;
	}
	/**
	 * Update the database entry.
	 *
	 * @param string $timestamp the timestamp for the end date.
	 */
	public function ip_update( $timestamp ) {
		$entry = $this->get_campaign_entry_by_ip();
		if ( $entry ) {
			$entries = $this->get_entries();
			$success = $entries->update_item( $entry->get_id(), array( 'end_date' => $timestamp ) );
		} else {
			$data = array(
				'campaign'     => $this->campaign_id,
				'end_date'     => $timestamp,
				'user_id'      => get_current_user_id(),
				'remove_date'  => date( 'Y-m-d H:i:s', strtotime( current_time( 'mysql' ) ) + MONTH_IN_SECONDS ),
				'user_ip'      => $this->get_client_ip(),
			);
			$entries = $this->get_entries();
			$entry_id = $entries->add_item( $data );
		}
	}
	/**
	 * Get the database entry.
	 */
	public function get_ip_date() {
		$entry = $this->get_campaign_entry_by_ip();
		if ( ! $entry ) {
			return null;
		}
		return $entry->get_end_date();
	}

	/**
	 * Get Campaign Entry
	 */
	public function get_campaign_entry_by_ip() {
		global $wpdb;
		try {
			$sql = $wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}kbp_countdown_entry
				WHERE campaign = %s
				AND user_ip = %s",
				$this->campaign_id,
				$this->user_ip
			);
			$found = $wpdb->get_row( $sql );
			if ( ! $found ) {
				return null;
			}
			$entries = $this->get_entries();
			$entry = $entries->get_item( $found->id );
			if ( ! $entry ) {
				return null;
			}
			return $entry;
		} catch ( Exception $e ) {
			return null;
		}
	}
	/**
	 * Update the database entry.
	 *
	 * @param string $timestamp the timestamp for the end date.
	 */
	public function user_account_update( $timestamp ) {
		$entry = get_campaign_entry_by_user();
		if ( $entry ) {
			$entries = $this->get_entries();
			$success = $entries->update_item( $entry->get_id(), array( 'end_date' => $timestamp ) );
		} else {
			$data = array(
				'campaign'     => $this->campaign_id,
				'end_date'     => $timestamp,
				'user_id'      => get_current_user_id(),
				'remove_date'  => date( 'Y-m-d H:i:s', strtotime( current_time( 'mysql' ) ) + MONTH_IN_SECONDS ),
				'user_ip'      => $this->get_client_ip(),
			);
			$entries  = $this->get_entries();
			$entry_id = $entries->add_item( $data );
		}
	}
	/**
	 * Get the database entry.
	 */
	public function get_user_account_date() {
		$entry = $this->get_campaign_entry_by_user();
		if ( ! $entry ) {
			return null;
		}
		return $entry->get_end_date();
	}
	/**
	 * Get Campaign Entry
	 */
	public function get_campaign_entry_by_user() {
		global $wpdb;
		try {
			$sql = $wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}kbp_countdown_entry
				WHERE campaign = %s
				AND user_id = %s",
				$this->campaign_id,
				get_current_user_id()
			);
			$found = $wpdb->get_row( $sql );
			if ( ! $found ) {
				return null;
			}
			$entries = $this->get_entries();
			$entry = $entries->get_item( $found->id );
			if ( ! $entry ) {
				return null;
			}
			return $entry;
		} catch ( Exception $e ) {
			return null;
		}
	}
	/**
	 * Set the end date.
	 *
	 * @param string $timestamp the timestamp for the end date.
	 */
	public function set_end_date( $timestamp ) {
		if ( apply_filters( 'kadence_evergreen_use_cookies', true ) ) {
			$this->cookie_update( $timestamp );
		}
		if ( apply_filters( 'kadence_evergreen_use_database', true ) ) {
			$this->ip_update( $timestamp );
		}
		if ( apply_filters( 'kadence_evergreen_use_account', false ) ) {
			$this->user_account_update( $timestamp );
		}
		return $timestamp;
	}
	/**
	 * Get the end date.
	 */
	public function get_end_date() {
		$cookie_end_date   = ( apply_filters( 'kadence_evergreen_use_cookies', true ) ? $this->get_cookie_date() : null );
		$database_end_date = ( apply_filters( 'kadence_evergreen_use_database', true ) ? $this->get_ip_date() : null );
		$account_end_date  = ( apply_filters( 'kadence_evergreen_use_account', false ) ? $this->get_user_account_date() : null );

		$timestamp = max( $cookie_end_date, $database_end_date, $account_end_date );
		// First visit, load empty.
		if ( empty( $timestamp ) ) {
			return null;
		}
		$php_timestamp = floor( $timestamp / 1000 );
		$now           = strtotime( get_date_from_gmt( current_time( 'Y-m-d H:i:s' ) ) );
		if ( $php_timestamp < $now ) {
			// Expired.
			$reset_date = strtotime( ' +' . $this->reset . ' day', $php_timestamp );
			// load empty to force a reset.
			if ( $reset_date < $now ) {
				return null;
			}
		}
		return $timestamp;
	}
	/**
	 * Get the client IP address
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_client_ip() {
		$ipaddress = '';

		if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED'] ) ) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		} elseif ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		} elseif ( isset( $_SERVER['HTTP_FORWARDED'] ) ) {
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		} else {
			$ipaddress = 'UNKNOWN';
		}

		return $ipaddress;
	}
}
