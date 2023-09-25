<?php declare(strict_types=1);

use Tribe\Plugin\Blocks\Terms_Block;

$terms_block = new Terms_Block( $attributes );
$terms       = $terms_block->get_the_terms();

echo '<div ' . get_block_wrapper_attributes() . '>';

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
			'<a href="%s" class="wp-block-tribe-terms__link">%s</a>',
			esc_url( get_term_link( $term ) ),
			esc_html( $term->name )
		);
	} else {
		echo sprintf(
			'<span class="wp-block-tribe-terms__link">%s</span></span>',
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
