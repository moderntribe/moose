<?php
/**
 * Entry Meta Table.
 *
 * @package Kadence Blocks Pro
 * @since   1.2.8
 */

namespace KBP\Tables;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

use KBP\BerlinDB\Table;

/**
 * Setup the "kbp_form_entry" database table
 *
 * @since 1.2.8
 */
final class Entries_Meta extends Table {

	/**
	 * @var string Table name
	 */
	protected $name = 'form_entrymeta';

	/**
	 * @var string Database version
	 */
	protected $version = 2019101021;

	/**
	 * @var array Array of upgrade versions and methods
	 */
	protected $upgrades = array();

	/**
	 * Membership Meta constructor.
	 *
	 * @access public
	 * @since  3.0
	 * @return void
	 */
	public function __construct() {

		parent::__construct();

	}

	/**
	 * Setup the database schema
	 *
	 * @access protected
	 * @since  3.0
	 * @return void
	 */
	protected function set_schema() {
		$this->schema = "meta_id bigint(20) unsigned NOT NULL auto_increment,
		kbp_form_entry_id bigint(20) unsigned NOT NULL default '0',
		meta_key varchar(255) DEFAULT NULL,
		meta_value longtext DEFAULT NULL,
		PRIMARY KEY (meta_id),
		KEY entry_id (kbp_form_entry_id),
		KEY meta_key (meta_key)";
	}

}
