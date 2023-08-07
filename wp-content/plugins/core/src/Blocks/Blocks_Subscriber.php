<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks;

use Tribe\Libs\Container\Abstract_Subscriber;
use Tribe\Plugin\Blocks\Filters\Contracts\Filter_Factory;
use Tribe\Plugin\Blocks\Patterns\Pattern_Category;
use Tribe\Plugin\Blocks\Patterns\Pattern_Registrar;
use Tribe\Plugin\Theme_Config\Theme_Support;

class Blocks_Subscriber extends Abstract_Subscriber {

	public function register(): void {

		add_action( 'init', function (): void {
			// Register blocks.
			foreach ( $this->container->get( Blocks_Definer::TYPES ) as $type ) {
				$this->container->get( Block_Registrar::class )->register( $type );
			}

			// Register block styles
			foreach ( $this->container->get( Blocks_Definer::EXTENDED ) as $block ) {
				$block->register_block_style();
			}

			// Register block CSS stylesheets.
			foreach ( $this->container->get( Blocks_Definer::EXTENDED ) as $block ) {
				$block->enqueue_front_style();
			}

			// Register block pattern categories.
			$this->container->get( Pattern_Category::class )->register_pattern_categories();

			// Register block patterns.
			foreach ( $this->container->get( Blocks_Definer::PATTERNS ) as $pattern ) {
				$this->container->get( Pattern_Registrar::class )->register( $pattern );
			}
		}, 10, 0 );

		/* Enqueue block editor styles / scripts */
		add_action( 'enqueue_block_editor_assets', function (): void {
			foreach ( $this->container->get( Blocks_Definer::EXTENDED ) as $block ) {
				$block->enqueue_editor_style();
				$block->enqueue_editor_scripts();
			}
		}, 10, 0 );

		/**
		 * Register block categories.
		 */
		add_filter( 'block_categories_all', function ( array $categories ): array {
			return $this->container->get( Block_Category::class )->custom_block_category( $categories );
		} );

		/**
		 * Filter block content using the render_block filter
		 */
		add_filter( 'render_block', function ( string $block_content, array $block ): string {
			$filter = $this->container->get( Filter_Factory::class )->make( $block );

			return $filter ? $filter->filter_block_content( $block_content ) : $block_content;
		}, 10, 2 );

		/**
		 * Disable default WP block patterns.
		 */
		add_action( 'after_setup_theme', function (): void {
			$this->container->get( Theme_Support::class )->disable_block_patterns();
		}, 10, 0 );

		/**
		 * Disable the WordPress patterns directory.
		 */
		add_filter( 'should_load_remote_block_patterns', '__return_false' );

		/**
		 * Disable openverse media category.
		 */
		add_filter( 'block_editor_settings_all', function ( array $settings ): array {
			return $this->container->get( Theme_Support::class )->disable_openverse_media_category( $settings );
		} );
	}

}
