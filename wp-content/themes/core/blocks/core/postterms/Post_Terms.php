<?php declare(strict_types=1);

namespace Tribe\Theme\blocks\core\postterms;

use Tribe\Plugin\Blocks\Block_Base;

class Post_Terms extends Block_Base {

	public function get_block_name(): string {
		return 'core/post-terms';
	}

	public function get_block_path(): string {
		return 'core/postterms';
	}

}
