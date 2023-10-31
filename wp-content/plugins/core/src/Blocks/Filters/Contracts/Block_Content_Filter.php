<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks\Filters\Contracts;

abstract class Block_Content_Filter {

	public const BLOCK = '';

	abstract public function filter_block_content( string $block_content, array $parsed_block, object $block ): string;

}
