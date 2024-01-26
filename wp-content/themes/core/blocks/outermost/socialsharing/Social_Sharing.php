<?php declare(strict_types=1);

namespace Tribe\Theme\blocks\outermost\socialsharing;

use Tribe\Plugin\Blocks\Block_Base;

class Social_Sharing extends Block_Base {

	public function get_block_name(): string {
		return 'outermost/social-sharing';
	}

	public function get_block_style_handle(): string {
		return 'outermost-social-sharing-style';
	}

	public function get_block_path(): string {
		return 'outermost/socialsharing';
	}

}
