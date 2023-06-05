<?php
/**
 * Entries Schema Class.
 *
 * @package Kadence Blocks Pro
 */

namespace KBP\Schemas;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

use KBP\BerlinDB\Schema;

/**
 * Entries Schema Class.
 *
 */
class Entries extends Schema {

	/**
	 * Array of database column objects
	 *
	 * @access public
	 * @var array
	 */
	public $columns = array(

		// id.
		array(
			'name'     => 'id',
			'type'     => 'bigint',
			'length'   => '20',
			'unsigned' => true,
			'extra'    => 'auto_increment',
			'primary'  => true,
			'sortable' => true
		),
		// name.
		array(
			'name'    => 'name',
			'type'    => 'varchar',
			'length'   => '255',
			'default' => '',
			'searchable' => true,
		),
		// form_id.
		array(
			'name'     => 'form_id',
			'type'    => 'varchar',
			'length'   => '50',
			'default' => '',
		),

		// post_id.
		array(
			'name'     => 'post_id',
			'type'     => 'bigint',
			'length'   => '20',
			'unsigned' => true,
			'default'  => '0'
		),

		// user_id.
		array(
			'name'     => 'user_id',
			'type'     => 'bigint',
			'length'   => '20',
			'unsigned' => true,
			'default'  => '0'
		),

		// date_created.
		array(
			'name'       => 'date_created',
			'type'       => 'datetime',
			'default'    => '0000-00-00 00:00:00',
			'created'    => true,
			'date_query' => true,
			'sortable'   => true,
		),
		// user_ip.
		array(
			'name'     => 'user_ip',
			'type'     => 'varchar',
			'length'   => '100',
			'unsigned' => true,
			'default'  => '0'
		),
		// referer.
		array(
			'name'    => 'referer',
			'type'    => 'varchar',
			'length'   => '255',
			'default' => '',
			'searchable' => true,
		),
		// referer.
		array(
			'name'    => 'status',
			'type'    => 'varchar',
			'length'   => '10',
			'default' => 'publish',
			'searchable' => true,
		),
		// user_device.
		array(
			'name'    => 'user_device',
			'type'    => 'varchar',
			'length'   => '50',
			'default' => '',
		),
		// uuid.
		array(
			'uuid' => true,
		),

	);

}
