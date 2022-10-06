<?php declare(strict_types=1);

namespace Tribe\Theme\Blocks;

class Register {

	private const TRIBE_THEME_BLOCKS_DIR = 'dist/';

	private string $blocks_path;

	public function __construct() {
		$this->blocks_path = trailingslashit( get_stylesheet_directory() ) . self::TRIBE_THEME_BLOCKS_DIR;

		add_action( 'init', [ $this, 'register_blocks' ], 10 );
	}

	public function register_blocks(): void {
		register_block_type( $this->blocks_path . 'accordion/block.json' );
	}

}
