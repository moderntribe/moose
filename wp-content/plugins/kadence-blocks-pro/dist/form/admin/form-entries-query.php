<?php
/**
 * Form Query Class.
 *
 * @package Kadence Blocks Pro
 */

namespace KBP\Queries;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

use KBP\BerlinDB\Query;

/**
 * Class used for querying form entries.
 */
class Entry extends Query {

	/** Table Properties ******************************************************/

	/**
	 * Name of the database table to query.
	 *
	 * @access public
	 * @var string
	 */
	protected $table_name = 'form_entry';

	/**
	 * String used to alias the database table in MySQL statement.
	 *
	 * @access public
	 * @var string
	 */
	protected $table_alias = 'kbform';

	/**
	 * Name of class used to setup the database schema
	 *
	 * @access public
	 * @var string
	 */
	protected $table_schema = '\\KBP\\Schemas\\Entries';

	/** Item ******************************************************************/

	/**
	 * Name for a single item
	 *
	 * @access public
	 * @var string
	 */
	protected $item_name = 'entry';

	/**
	 * Plural version for a group of items.
	 *
	 * @access public
	 * @var string
	 */
	protected $item_name_plural = 'entries';

	/**
	 * Callback function for turning IDs into objects
	 *
	 * @since  3.0
	 * @access public
	 * @var mixed
	 */
	protected $item_shape = '\\KBP_Entry';

	/**
	 * Group to cache queries and queried items in.
	 *
	 * @access public
	 * @var string
	 */
	protected $cache_group = 'entries';

	/**
	 * Sets up the entry query, based on the query vars passed.
	 *
	 * @param string|array $query Optional. Array or query string of entry query parameters. Default empty.
	 */
	public function __construct( $query = array() ) {
		parent::__construct( $query );
	}

}
