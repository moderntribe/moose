<?php declare(strict_types=1);

namespace Tribe\Plugin\Post_Types\Training;

use Tribe\Libs\Post_Type\Post_Type_Subscriber;

class Training_Subscriber extends Post_Type_Subscriber {

	// phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
	protected $config_class = Config::class;

	public function register(): void {
		parent::register();

		$this->block_templates();
		$this->user_permissions();
		$this->list_table();
	}

	public function block_templates(): void {
		add_action( 'init', function (): void {
			$this->container->get( Config::class )->register_block_template();
		} );
	}

	public function user_permissions(): void {
		add_action( 'init', function (): void {
			$this->container->get( Config::class )->add_user_caps();
		} );

		// Useful for excluding from sitemaps and controlling user access.
		add_filter( 'is_post_type_viewable', function ( $bool, $post_type_object ): bool {
			return $this->container->get( Config::class )->current_user_can_read( $bool, $post_type_object );
		}, 10, 2 );

		// Send 404 for unauthorized users.
		add_action( 'template_redirect', function (): void {
			$this->container->get( Config::class )->send_404_unauthorized();
		} );
	}

	public function list_table(): void {
		// Prevent filtering added by plugins.
		add_action( 'restrict_manage_posts', function ( $post_type ): void {
			$this->container->get( Config::class )->list_table_filters( $post_type );
		}, 0 );

		// Remove columns added by plugins.
		add_filter( 'manage_' . Training::NAME . '_posts_columns', function ( $columns ): array {
			return $this->container->get( Config::class )->list_table_columns();
		}, 100, 2 );
	}

}
