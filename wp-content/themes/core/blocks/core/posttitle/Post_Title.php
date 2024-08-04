<?php declare(strict_types=1);

namespace Tribe\Theme\blocks\core\posttitle;

use Tribe\Plugin\Blocks\Block_Base;

class Post_Title extends Block_Base {

	public function get_block_name(): string {
		return 'core/post-title';
	}

	public function get_block_path(): string {
		return 'core/posttitle';
	}

	public function get_block_styles(): array {
		return [
			'page' => esc_html__( 'Page', 'tribe' ),
		];
	}

}
