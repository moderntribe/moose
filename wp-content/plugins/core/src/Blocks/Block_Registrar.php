<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks;

class Block_Registrar {

	public const BLOCKS_DIR = 'dist/';

	public function register( string $block_name, string $blocks_dir = self::BLOCKS_DIR ): void {
		register_block_type(
			trailingslashit( get_stylesheet_directory() ) . $blocks_dir . $block_name . '/block.json'
		);
	}

}
