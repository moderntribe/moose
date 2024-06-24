<?php declare(strict_types=1);

namespace Tribe\Theme\bindings;

use Tribe\Plugin\Blocks\Bindings\Binding_Base;

class Query_Results_Count extends Binding_Base {

	public function get_slug(): string {
		return 'tribe/query-results-count';
	}

	public function get_args(): array {
		return [
			Binding_Base::LABEL              => __( 'Query Results Count', 'tribe' ),
			Binding_Base::GET_VALUE_CALLBACK => [ $this, 'tribe_get_query_results_count' ],
		];
	}

	public function tribe_get_query_results_count(): string {
		global $wp_query;
		$is_search = is_search();
		$count     = (int) $wp_query->found_posts;
		$output    = sprintf( _n( '%d result', '%d results', $count, 'tribe' ), number_format_i18n( $count ) );

		if ( $is_search ) {
			$output = sprintf(
				_x(
					'%s %s for <span class="search-term" style="font-weight:var(--wp--custom--font-weight--bold)">&ldquo;%s&rdquo;</span>',
					'First value is the number of results, second is word "result" (pluralized if necessary), third is the search term',
					'tribe'
				),
				number_format_i18n( $count ),
				_n( 'result', 'results', $count, 'tribe' ),
				get_search_query()
			);
		}

		return wp_kses_post( $output );
	}

}
