<?php declare(strict_types=1);

namespace Tribe\Theme\blocks\core\quote;

use Tribe\Plugin\Blocks\Block_Base;

class Quote extends Block_Base {

	public function get_block_name(): string {
		return 'core/quote';
	}

}
