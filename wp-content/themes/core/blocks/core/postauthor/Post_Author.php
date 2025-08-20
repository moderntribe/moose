<?php declare(strict_types=1);

namespace Tribe\Theme\blocks\core\postauthor;

use Tribe\Plugin\Blocks\Block_Base;

class Post_Author extends Block_Base {

	public function get_block_name(): string {
		return 'core/post-author';
	}

	public function get_block_path(): string {
		return 'core/postauthor';
	}

}
