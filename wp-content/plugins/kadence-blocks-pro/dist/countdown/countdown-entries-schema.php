<?php
/**
 * Entries Schema Class.
 *
 * @package     Kadence Blocks Pro
 * @since  1.2.8
 */

namespace KBP\Schemas;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

use KBP\BerlinDB\Schema;

/**
 * Entries Schema Class.
 *
 * @since  1.2.8
 */
class Countdown_Entries extends Schema {

	/**
	 * Array of database column objects
	 *
	 * @since  1.2.8
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
		// campaign.
		array(
			'name'    => 'campaign',
			'type'    => 'varchar',
			'length'   => '255',
			'default' => '',
			'searchable' => true,
		),
		// campaign.
		array(
			'name'    => 'end_date',
			'type'    => 'varchar',
			'length'   => '255',
			'default' => '',
			'searchable' => true,
		),
		// remove_date.
		array(
			'name'       => 'remove_date',
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
			'length'   => '50',
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
		// uuid.
		array(
			'uuid' => true,
		),

	);

}
