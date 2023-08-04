<?php declare(strict_types=1);

namespace Tribe\Theme\blocks\core\search;

use Tribe\Plugin\Blocks\Block_Base;

class Search extends Block_Base {

	public function get_block_name(): string {
		return 'core/search';
	}

	public function get_block_path(): string {
		return 'core/search';
	}

}
