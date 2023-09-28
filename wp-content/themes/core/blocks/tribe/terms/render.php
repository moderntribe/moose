<?php declare(strict_types=1);

use Tribe\Plugin\Blocks\Terms_Block;

/**
 * All of the parameters passed to the function where this file is being required are accessible in this scope:
 *
 * @var array     $attributes     The array of attributes for this block.
 * @var string    $content        Rendered block output. ie. <InnerBlocks.Content />.
 * @var \WP_Block $block          The instance of the WP_Block class that represents the block being rendered.
 */

$terms_block = new Terms_Block( $attributes );
$terms       = $terms_block->get_the_terms();

echo '<div ' . get_block_wrapper_attributes() . '>';

if ( 0 === count( $terms ) ) {
	echo '<!-- Terms block: No terms to list. -->';
	echo '</div>';

	return;
}

if ( count( $terms ) > 1 ) {
	echo  '<ul class="wp-block-tribe-terms__list">';
}

foreach ( $terms as $term ) {
	if ( count( $terms ) > 1 ) {
		echo '<li class="wp-block-tribe-terms__term">';
	}

	echo '<span class="wp-block-tribe-terms__term">';

	if ( $terms_block->display_as_links() ) {
		echo sprintf(
			'<a href="%s" class="wp-block-tribe-terms__link t-category">%s</a>',
			esc_url( get_term_link( $term ) ),
			esc_html( $term->name )
		);
	} else {
		echo sprintf(
			'<span class="wp-block-tribe-terms__link t-category">%s</span>',
			esc_html( $term->name )
		);
	}

	echo '</span>';

	if ( count( $terms ) > 1 ) { // phpcs:ignore SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
		echo '</li>';
	}
}

if ( count( $terms ) > 1 ) {
	echo '</ul>';
}

echo '</div>';
