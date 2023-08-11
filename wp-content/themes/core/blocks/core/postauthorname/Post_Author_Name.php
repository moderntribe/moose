<?php declare(strict_types=1);

namespace Tribe\Theme\blocks\core\postauthorname;

use Tribe\Plugin\Blocks\Block_Base;

class Post_Author_Name extends Block_Base {

	public function get_block_name(): string {
		return 'core/post-author-name';
	}

	public function get_block_path(): string {
		return 'core/postauthorname';
	}

}
