<?php declare(strict_types=1);

namespace Tribe\Plugin\Post_Types;

use Tribe\Plugin\Post_Types\Meta_Box_Handlers\Meta_Box_Handler_Interface;

abstract class Post_Type_Config {

	protected string $post_type = '';

	/**
	 * Get the arguments for post type registration
	 *
	 * @see \register_extended_post_type
	 */
	abstract public function get_args(): array;

	/**
	 * Get the names to use for the post type
	 *
	 * @return array An associative array of labels
	 *               - singular
	 *               - plural
	 *               - slug
	 *
	 * @see \register_extended_post_type
	 */
	abstract public function get_labels(): array;


	/**
	 * @param string $post_type The post type ID
	 */
	public function __construct( string $post_type = '' ) {
		if ( ! $post_type ) {
			return;
		}

		$this->post_type = $post_type;
	}

	/**
	 * Hook into WordPress to register the post type
	 */
	public function register(): void {
		Post_Type_Registration::register( $this, apply_filters( Meta_Box_Handler_Interface::INSTANCE_FILTER, null ) );
	}

	/**
	 * @return string The ID of the post type
	 */
	public function post_type(): string {
		return $this->post_type;
	}

	/**
	 * Get the metabox configuration for the post type
	 *
	 * @see \CMB2
	 */
	public function get_meta_boxes(): array {
		return [];
	}

	/**
	 * Get the ACF metabox configuration for the post type
	 *
	 * @return array An array of field group configurations
	 *               (i.e., an array of arrays).
	 *
	 * @see acf_add_local_field_group()
	 */
	public function get_acf_fields(): array {
		return [];
	}

}
