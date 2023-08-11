<?php declare(strict_types=1);

namespace Tribe\Theme\blocks\core\posttemplate;

use Tribe\Plugin\Blocks\Block_Base;

class Post_Template extends Block_Base {

	public function get_block_name(): string {
		return 'core/post-template';
	}

	public function get_block_path(): string {
		return 'core/posttemplate';
	}

}
