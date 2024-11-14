<?php declare(strict_types=1);

namespace Tribe\Plugin\Object_Meta;

/**
 * Class Meta_Map
 *
 * Maps requests for meta keys to the Meta_Group responsible for handling it
 */
class Meta_Map {

	// @phpstan-ignore-next-line
	private string $object_type = '';

	/** @var \Tribe\Plugin\Object_Meta\Meta_Group[] */
	private array $keys = [];

	public function __construct( string $object_type ) {
		$this->object_type = $object_type;
	}

	/**
	 * Add the Meta_Group as the handler for its declared keys.
	 * Any keys that are already handled will be taken over by
	 * this group.
	 *
	 * @param \Tribe\Plugin\Object_Meta\Meta_Group $group
	 */
	public function add( Meta_Group $group ): void {
		foreach ( $group->get_keys() as $key ) {
			$this->keys[ $key ] = $group;
		}
	}

	/**
	 * @return array All the keys that will be mapped
	 */
	public function get_keys(): array {
		return array_keys( $this->keys );
	}

	/**
	 * @param int|string $object_id
	 * @param string $key
	 *
	 * @return mixed The value for the given key
	 */
	public function get_value( int|string $object_id, string $key ): mixed {
		if ( isset( $this->keys[ $key ] ) ) {
			return $this->keys[ $key ]->get_value( $object_id, $key );
		}

		return null;
	}

}
