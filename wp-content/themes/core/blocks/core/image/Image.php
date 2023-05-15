<?php declare(strict_types=1);

namespace Tribe\Theme\blocks\core\image;

use Tribe\Plugin\Blocks\Block_Base;

class Image extends Block_Base {

	public function get_block_name(): string {
		return 'core/image';
	}

}
