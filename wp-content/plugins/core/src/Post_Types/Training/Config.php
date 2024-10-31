<?php declare(strict_types=1);

namespace Tribe\Plugin\Post_Types\Training;

use Tribe\Libs\Post_Type\Post_Type_Config;

class Config extends Post_Type_Config {

	// phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
	protected $post_type = Training::NAME;

	public function get_args(): array {
		return [
			'capability_type'     => $this->post_type,
			'delete_with_user'    => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'hierarchical'        => false,
			'map_meta_cap'        => false,
			'menu_icon'           => 'dashicons-welcome-learn-more',
			'menu_position'       => 19,
			'public'              => true,
			'publicly_queryable'  => true,
			'rewrite'             => [ 'slug' => $this->post_type, 'with_front' => false ],
			'show_in_nav_menus'   => false,
			'show_in_rest'        => current_user_can( 'read_' . $this->post_type ),
			'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ],
		];
	}

	public function get_labels(): array {
		return [
			'singular' => __( 'Training Doc', 'tribe' ),
			'plural'   => __( 'Training Docs', 'tribe' ),
			'slug'     => $this->post_type,
		];
	}

	/**
	 * Define block templates for initial editor state
	 */
	public function register_block_template(): void {
		$post_type_object           = get_post_type_object( $this->post_type );
		$post_type_object->template = [
			[
				'core/pattern',
				[
					'slug' => 'patterns/page', // Use patterns for Pages.
				],
			],
		];
	}

	/**
	 * Add capabilities to user roles.
	 */
	public function add_user_caps(): void {
		global $wp_roles;

		if ( ! $wp_roles || ! is_object( $wp_roles ) || ! property_exists( $wp_roles, 'roles' ) || ! is_array( $wp_roles->roles ) ) {
			return;
		}

		$post_type_object = get_post_type_object( $this->post_type );
		$adjust_roles     = [
			'administrator',
			'editor',
			'author',
			'contributor',
		];

		foreach ( $adjust_roles as $role ) {
			$role = get_role( $role );

			foreach ( (array) $post_type_object->cap as $cap ) {
				$role->add_cap( $cap );
			}
		}
	}

	/**
	 * Check the user is authorized to read the post;
	 * if not, send and display 404.
	 */
	public function send_404_unauthorized(): void {
		if ( is_post_type_viewable( $this->post_type ) ) {
			return;
		}

		global $wp_query;

		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();

		require_once get_query_template( '404' );
		exit;
	}

	/**
	 * Indicate if the post type is viewable.
	 */
	public function current_user_can_read( bool $bool, \WP_Post_Type $post_type_object ): bool {
		if ( ! $bool ) {
			return false;
		}

		if ( $post_type_object->name !== $this->post_type ) {
			return true;
		}

		return current_user_can( $post_type_object->cap->read_post );
	}

	/**
	 * Remove filters from list table.
	 */
	public function list_table_filters( string $post_type ): void {
		if ( $post_type !== $this->post_type ) {
			return;
		}

		if ( ! has_action( current_action() ) ) {
			return;
		}

		remove_all_actions( current_action() );
	}

	/**
	 * Limit list table columns to checkbox and title.
	 */
	public function list_table_columns(): array {
		return [
			'cb'    => '<input type="checkbox" />',
			'title' => 'Title',
		];

		return $columns;
	}

}
