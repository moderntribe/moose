<?php declare(strict_types=1);

use Tribe\Plugin\Blocks\Terms_Block;

/**
 * All of the parameters passed to the function where this file is being required are accessible in this scope:
 *
 * @var array     $attributes     The array of attributes for this block.
 * @var string    $content        Rendered block output. ie. <InnerBlocks.Content />.
 * @var \WP_Block $block          The instance of the WP_Block class that represents the block being rendered.
 */

$terms_block       = new Terms_Block( $attributes );
$terms_block_terms = $terms_block->get_the_terms();

echo '<div ' .  wp_kses_data( get_block_wrapper_attributes() ) . '>';

if ( 0 === count( $terms_block_terms ) ) {
	if ( strpos( $_SERVER['REQUEST_URI'], 'wp-admin' ) !== false || strpos( $_SERVER['REQUEST_URI'], 'wp-json' ) !== false ) {
		echo '<ul class="wp-block-tribe-terms__list">';
		echo '<li class="wp-block-tribe-terms__term">';
		echo '<span class="wp-block-tribe-terms__link t-category">'. esc_html__( 'Terms Display Here', 'tribe' ) .'</span>';
		echo '</li>';
		echo '</ul>';
	} else {
		echo '<!-- Terms block: No terms to list. -->';
	}

	echo '</div>';

	return;
}

if ( count( $terms_block_terms ) > 1 ) {
	echo  '<ul class="wp-block-tribe-terms__list">';
}

foreach ( $terms_block_terms as $term ) {
	if ( count( $terms_block_terms ) > 1 ) {
		echo '<li class="wp-block-tribe-terms__term">';
	}

	echo '<span class="wp-block-tribe-terms__term">';

	if ( $terms_block->display_as_links() ) {
		echo sprintf(
			'<a href="%s" class="wp-block-tribe-terms__link t-category">%s</a>',
			esc_url( get_term_link( $term ) ?? '' ),
			esc_html( $term->name )
		);
	} else {
		echo sprintf(
			'<span class="wp-block-tribe-terms__link t-category">%s</span>',
			esc_html( $term->name )
		);
	}

	echo '</span>';

	if ( count( $terms_block_terms ) > 1 ) { // phpcs:ignore SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
		echo '</li>';
	}
}

if ( count( $terms_block_terms ) > 1 ) {
	echo '</ul>';
}

echo '</div>';
