<?php declare(strict_types=1);

namespace Tribe\Plugin\Assets;

use Tribe\Plugin\Blocks\Blocks_Definer;

class Block_Assets_Enqueuer extends Assets_Enqueuer {

	public const BLOCKS_FILE   = 'index.asset.php';
	public const GLOBAL_BLOCKS = [];

	public function __construct( string $assets_folder = 'dist/blocks/' ) {
		parent::__construct( $assets_folder );
	}

	public function register(): void {
		$this->register_custom_block_styles();
		$this->enqueue_block_styles();
		$this->admin_enqueue_block_styles();
	}

	public function register_custom_block_styles(): void {
		$blocks = tribe_project()->container()->get( Blocks_Definer::TYPES );
		if ( empty( $blocks ) ) {
			return;
		}

		foreach ( $blocks as $block ) {
			wp_register_style( "{$block}-styles", $this->assets_path_uri . "{$block}/style-index.css" );
		}
	}

	public function enqueue_block_styles( string $filename = 'style-index', string $prefix = 'tribe' ): void {
		$blocks = tribe_project()->container()->get( Blocks_Definer::CORE );

		if ( empty( $blocks ) ) {
			return;
		}

		foreach ( $blocks as $block ) {
			// Handle block name overrides
			switch ( $block ) {
				case 'core/lists':
					// we have to override core/lists because "list" is a reserved word in PHP
					$block_name = 'core/list';
					break;
				default:
					$block_name = '';
					break;
			}

			$file_path            = $block;
			$handle               = sanitize_title( $block );
			$args                 = $this->get_asset_file_args( $this->assets_path . trailingslashit( $block ) . self::BLOCKS_FILE );
			$args['dependencies'] = [];

			wp_enqueue_block_style(
				( ! empty( $block_name ) ) ? $block_name : $block,
				[
					'handle' => "{$prefix}-{$handle}-styles",
					'src'    => $this->assets_path_uri . "{$file_path}/{$filename}.css",
					'deps'   => $args['dependencies'],
					'ver'    => $args['version'] ?? false,
				]
			);
		}
	}

	public function admin_enqueue_block_styles(): void {
		if ( ! is_admin() ) {
			return;
		}

		$this->enqueue_block_styles( 'index', 'admin-tribe' );
	}

}
