<?php declare(strict_types=1);

namespace Tribe\Theme\blocks\core\lists;

use Tribe\Plugin\Blocks\Block_Base;

class Lists extends Block_Base {

	public function get_block_name(): string {
		return 'core/list';
	}

	public function get_block_path(): string {
		return 'core/lists';
	}

	public function get_block_styles(): array {
		return [
			'arrow' => __( 'Arrow', 'tribe' ),
			'check' => __( 'Check', 'tribe' ),
		];
	}

}
