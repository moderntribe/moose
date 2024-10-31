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
	}

	public function block_templates(): void {
		add_action( 'init', function (): void {
			$this->container->get( Config::class )->register_block_template();
		} );

		add_filter ( 'manage_training_posts_columns', function ( $columns ) {
				unset(
					$columns['wpseo-score'],
					$columns['wpseo-title'],
					$columns['wpseo-metadesc'],
					$columns['wpseo-focuskw'],
					$columns['wpseo-score-readability'],
					$columns['wpseo-links'],
					$columns['wpseo-linked'],
				);

				return $columns;
			}, 99 );
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

}
