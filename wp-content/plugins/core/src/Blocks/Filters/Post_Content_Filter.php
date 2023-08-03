<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks\Filters;

use Tribe\Plugin\Blocks\Filters\Contracts\Block_Content_Filter;

class Post_Content_Filter extends Block_Content_Filter {

	public const BLOCK = 'core/post-content';

	/**
	 * @function filter_block_content
	 *
	 * @description filters post content block to change wrapping div to main
	 *
	 * @param  string $block_content
	 * @param  array  $block
	 */
	public function filter_block_content( string $block_content, array $block ): string {
		$block_content = substr( $block_content, 4 );
		$block_content = substr( $block_content, 0, -4 );
		$block_content = '<main' . $block_content . 'main>';

		// return the new output
		return $block_content;
	}

}
