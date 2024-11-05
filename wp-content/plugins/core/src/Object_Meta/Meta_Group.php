<?php declare(strict_types=1);

namespace Tribe\Plugin\Object_Meta;

abstract class Meta_Group {

	public const NAME = '';

	/**
	 * @var mixed[]
	 */
	protected array $post_types = [ ];

	/**
	 * @var mixed[]
	 */
	protected array $object_types = [ ];

	/**
	 * @return array The meta keys that this field will handle.
	 *               While these will probably directly correspond
	 *               to meta keys in the database, there is no
	 *               guaranteed, as the key may correspond to
	 *               a computed/aggregate value.
	 */
	abstract public function get_keys(): array;

	/**
	 * @param int    $object_id
	 * @param string $key
	 *
	 * @return mixed The value for the given key
	 */
	abstract public function get_value( int $object_id, string $key ): mixed;

	/**
	 * Meta_Group constructor.
	 *
	 * @param array $object_types The object types the meta group applies to
	 */
	public function __construct( array $object_types ) {
		// Allow backwards compatibility with the former method of assigning post types to meta groups.
		$types = [ 'post_types', 'taxonomies', 'settings_pages', 'users', 'nav_menus', 'nav_menu_items', 'widget', 'block' ];
		if ( empty( array_intersect( $types, array_keys( $object_types ) ) ) ) {
			$this->post_types = $object_types;
			$object_types     = [ 'post_types' => $object_types ];
		}

		$this->object_types = $object_types;
	}

	/**
	 * @return array Return the post types for this meta group. This method exists purely to allow backwards compatibility with
	 *               older versions of the Post_Meta class.
	 *
	 * @deprecated Object meta should be registered using an array of key=>value pairs for object types. E.g. [ 'post_types' => [ 'page' ], 'taxonomies' => ['category'] ]
	 */
	public function get_post_types(): array {
		return $this->object_types['post_types'];
	}

	/**
	 * @return array The post types this meta group applies to
	 */
	public function get_object_types(): array {
		return $this->object_types;
	}

}
