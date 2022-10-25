<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks\Styles;

interface Block_Styles_Base {

	public function get_block_name(): string;

	/**
	 * Style name as a key and label as value.
	 *
	 * @return array<string, string>
	 */
	public function get_block_styles(): array;

}
