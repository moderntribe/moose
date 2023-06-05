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
final class Entries extends Table {

	/**
	 * @var string Table name
	 */
	protected $name = 'form_entry';

	/**
	 * @var string Database version
	 */
	protected $version = 2021101212;

	protected $upgrades = array(
		'2021101211' => 2021101212,
	);
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
	 * @return void
	 */
	protected function set_schema() {
		$this->schema = "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			name varchar(255) DEFAULT NULL,
			form_id varchar(55) DEFAULT NULL,
			post_id bigint(20) unsigned NOT NULL DEFAULT '0',
			user_id bigint(20) unsigned NOT NULL DEFAULT '0',
			date_created datetime NOT NULL,
			user_ip varchar(100) NOT NULL default '',
			user_device varchar(55) DEFAULT NULL,
			referer varchar(255) DEFAULT NULL,
			status varchar(10) DEFAULT 'publish',
			uuid varchar(100) NOT NULL default '',
			PRIMARY KEY (id),
			KEY post_id (post_id)";
	}
	/**
	 * Upgrade to 2021101211
	 *      - Update `user_ip` type to `varchar`.
	 *
	 * @return bool
	 */
	protected function __2021101212() {
		$result = $this->get_db()->query( "ALTER TABLE {$this->table_name} MODIFY user_ip varchar(100) NOT NULL default '';" );
		$success = $this->is_success( $result );
		return $success;
	}
}
