<?php
/**
 * Countdown Query Class.
 */

namespace KBP\Queries;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

use KBP\BerlinDB\Query;

/**
 * Class used for querying countdowns.
 */
class Countdown_Entry extends Query {

	/** Table Properties ******************************************************/

	/**
	 * Name of the database table to query.
	 *
	 * @access public
	 * @var string
	 */
	protected $table_name = 'countdown_entry';

	/**
	 * String used to alias the database table in MySQL statement.
	 *
	 * @access public
	 * @var string
	 */
	protected $table_alias = 'kbcountdown';

	/**
	 * Name of class used to setup the database schema
	 *
	 * @access public
	 * @var string
	 */
	protected $table_schema = '\\KBP\\Schemas\\Countdown_Entries';

	/** Item ******************************************************************/

	/**
	 * Name for a single item
	 *
	 * @access public
	 * @var string
	 */
	protected $item_name = 'countdown_entry';

	/**
	 * Plural version for a group of items.
	 *
	 * @access public
	 * @var string
	 */
	protected $item_name_plural = 'countdown_entries';

	/**
	 * Callback function for turning IDs into objects
	 *
	 * @access public
	 * @var mixed
	 */
	protected $item_shape = '\\KBP_Countdown_Entry';

	/**
	 * Group to cache queries and queried items in.
	 *
	 * @since  3.0
	 * @access public
	 * @var string
	 */
	protected $cache_group = 'countdown_entries';

	/**
	 * Sets up the entry query, based on the query vars passed.
	 *
	 * @param string|array $query Optional. Array or query string of entry query parameters. Default empty.
	 */
	public function __construct( $query = array() ) {
		parent::__construct( $query );
	}

}
