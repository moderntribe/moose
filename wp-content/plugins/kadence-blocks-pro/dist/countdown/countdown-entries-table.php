<?php
/**
 * Entries Schema Class.
 *
 * @package Kadence Blocks Pro
 */

namespace KBP\Tables;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

use KBP\BerlinDB\Table;

/**
 * Entries Schema Class.
 */
final class Countdown_Entries extends Table {

	/**
	 * @var string Table name
	 */
	protected $name = 'countdown_entry';

	/**
	 * @var string Database version
	 */
	protected $version = 2021031602;

	// protected $upgrades = array(
	// 	'2019101613' => 2019101613
	// );
	/**
	 * Customers constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		parent::__construct();

	}

	/**
	 * Setup the database schema
	 *
	 * @access protected
	 * @since  1.2.8
	 * @return void
	 */
	protected function set_schema() {
		$this->schema = "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			campaign varchar(255) DEFAULT NULL,
			end_date varchar(255) DEFAULT NULL,
			remove_date varchar(255) DEFAULT NULL,
			user_id bigint(20) unsigned NOT NULL DEFAULT '0',
			user_ip varchar(50) NOT NULL default '',
			uuid varchar(100) NOT NULL default '',
			PRIMARY KEY (id),
			KEY user_ip (user_ip)";
	}
}
