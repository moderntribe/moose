<?php declare(strict_types=1);

namespace Tribe\Theme\blocks\core\querynoresults;

use Tribe\Plugin\Blocks\Block_Base;

class Query_No_Results extends Block_Base {

	public function get_block_name(): string {
		return 'core/query-no-results';
	}

	public function get_block_path(): string {
		return 'core/querynoresults';
	}

}
