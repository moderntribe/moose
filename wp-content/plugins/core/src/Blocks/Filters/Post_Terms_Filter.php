<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks\Filters;

use Tribe\Plugin\Blocks\Filters\Contracts\Block_Content_Filter;

class Post_Terms_Filter extends Block_Content_Filter {

	public const BLOCK = 'core/post-terms';

	public function filter_block_content( string $block_content, array $block ): string {
		// if we don't have any filter classes (or classes at all), return early
		if ( ! array_key_exists( 'className', $block['attrs'] ) ) {
			return $block_content;
		}

		// determine what we should be filtering
		$should_remove_links      = str_contains( $block['attrs']['className'], 'filter-remove-links' );
		$should_remove_separators = str_contains( $block['attrs']['className'], 'filter-remove-separators' );
		$should_display_one_term  = str_contains( $block['attrs']['className'], 'filter-display-one-term' );

		// setup output variable
		$output = '';

		// remove all content between the block wrapper
		$wrapper = preg_replace( '#(<div[^>]*>).*?(</div>)#', '$1$2', $block_content );

		// split the wrapper so we can reassemble it later
		$explode_wrapper = explode( '><', $wrapper );

		// setup the start of the wrapper
		$wrapper_start = $explode_wrapper[0] . '>';

		// setup the end of the wrapper
		$wrapper_end = '<' . $explode_wrapper[1];

		// remove the start & end wrapper from the block content (so we're only left with the contents)
		$removed_wrapper = str_replace( $wrapper_start, '', str_replace( $wrapper_end, '', $block_content ) );

		// determine what we should split the contents by (separator)
		$explode_by = $block['attrs']['separator'] !== '' ? $block['attrs']['separator'] : ' ';

		// setup a separator variable so we can easily use it later
		$separator = sprintf( '<span class="wp-block-post-terms__separator">%s</span>', $explode_by );

		// change the contents into an array of items
		$explode = explode( $separator, $removed_wrapper );

		// rebuild block content

		// start with the wrapper
		$output .= $wrapper_start;

		// determine if we should display one term or all of them
		if ( $should_display_one_term ) {
			// if we should remove links, strip all tags from the item
			// if we're displaying one term, we don't have to worry abouot separators
			$output .= $should_remove_links
				? sprintf(
					'<span class="%s">%s</span>',
					esc_attr( 'wp-block-post-terms__term' ),
					strip_tags( $explode[0] )
				)
				: sprintf( '%s', $explode[0] );
		} else {
			// if we should show all terms, loop through them
			// if we should remove links, strip all tags from the item
			// if we should show separators, don't show the last one
			foreach ( $explode as $key => $item ) {
				$output .= $should_remove_links
					? sprintf(
						'<span class="%s">%s</span>%s',
						esc_attr( 'wp-block-post-terms__term' ),
						strip_tags( $item ),
						$should_remove_separators ? '' : ( $key + 1 < count( $explode ) ? $separator : '' )
					)
					: sprintf( '%s%s', $item, $should_remove_separators ? '' : $separator );
			}
		}

		// end with the wrapper
		// phpcs:ignore
		$output .= $wrapper_end;

		// return the new output
		return $output;
	}

}
