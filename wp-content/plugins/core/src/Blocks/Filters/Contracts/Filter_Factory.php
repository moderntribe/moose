<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks\Filters\Contracts;

class Filter_Factory {

	/**
	 * @var array<class-string<\Tribe\Plugin\Blocks\Filters\Contracts\Block_Content_Filter>>
	 */
	protected array $filters;

	/**
	 * @param array<class-string<\Tribe\Plugin\Blocks\Filters\Contracts\Block_Content_Filter>> $filters
	 */
	public function __construct( array $filters ) {
		$this->filters = $filters;
	}

	/**
	 * @param array<string, mixed>   $block
	 */
	public function make( array $block ): ?Block_Content_Filter {
		if ( empty( $block['blockName'] ) ) {
			return null;
		}

		foreach ( $this->filters as $filter ) {
			if ( $filter::BLOCK === $block['blockName'] ) {
				return new $filter;
			}
		}

		return null;
	}

}
