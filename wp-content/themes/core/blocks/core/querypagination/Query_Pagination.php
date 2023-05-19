<?php declare(strict_types=1);

namespace Tribe\Theme\blocks\core\querypagination;

use Tribe\Plugin\Blocks\Block_Base;

class Query_Pagination extends Block_Base {

	public function get_block_name(): string {
		return 'core/query-pagination';
	}

	public function get_block_path(): string {
		return 'core/querypagination';
	}

}
