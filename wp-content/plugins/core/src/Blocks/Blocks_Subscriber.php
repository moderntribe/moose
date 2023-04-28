<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks;

use Tribe\Libs\Container\Abstract_Subscriber;
use Tribe\Plugin\Blocks\Filters\Contracts\Filter_Factory;
use Tribe\Plugin\Blocks\Patterns\Pattern_Category;
use Tribe\Plugin\Blocks\Patterns\Pattern_Registrar;
use Tribe\Plugin\Config\Theme_Support;

class Blocks_Subscriber extends Abstract_Subscriber {

	public function register(): void {

		/**
		 * Register block categories.
		 */
		add_filter( 'block_categories_all', function ( array $categories ): array {
			return $this->container->get( Block_Category::class )->custom_block_category( $categories );
		} );

		/**
		 * Disable the WordPress patterns directory.
		 */
		add_filter( 'should_load_remote_block_patterns', '__return_false' );

		/**
		 * Render blocks content.
		 */
		add_filter( 'render_block', function ( string $block_content, array $block ): string {
			$filter = $this->container->get( Filter_Factory::class )->make( $block );

			return $filter ? $filter->filter_block_content( $block_content ) : $block_content;
		}, 10, 2 );

		add_action( 'after_setup_theme', function (): void {
			$this->container->get( Theme_Support::class )->disable_block_patterns();

			// Enqueue block styles.
			foreach ( $this->container->get( Blocks_Definer::EXTENDED ) as $block ) {
				$block->enqueue_block_style();
			}
		}, 10, 0 );

		add_action( 'init', function (): void {
			// Register blocks.
			foreach ( $this->container->get( Blocks_Definer::TYPES ) as $type ) {
				$this->container->get( Block_Registrar::class )->register( $type );
			}

			// Register block styles.
			foreach ( $this->container->get( Blocks_Definer::EXTENDED ) as $block ) {
				$block->register_block_style();
				$block->register_assets();
			}

			// Register patterns category.
			$this->container->get( Pattern_Category::class )->register_pattern_category();

			// Register patterns category.
			foreach ( $this->container->get( Blocks_Definer::PATTERNS ) as $pattern ) {
				$this->container->get( Pattern_Registrar::class )->register( $pattern );
			}
		}, 10, 0 );
	}

}
