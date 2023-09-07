<?php declare(strict_types=1);

use Tribe\Plugin\Post_Types\Post\Post;

// get block settings (attributes)
$taxonomy         = $attributes['taxonomyToUse'] ?? 'category';
$only_primay_term = $attributes['onlyPrimaryTerm'] ?? false;
$has_links        = $attributes['hasLinks'] ?? false;
$terms            = false;
$primary_term     = false;

/**
 * the Post class contains the trait to grab the primary term
 *
 * @see https://github.com/moderntribe/moose/blob/main/wp-content/plugins/core/src/Templates/Traits/Primary_Term.php
 */
$post = new Post();

/**
 * If we should only display the primary term:
 * - If we have yoast we can get the primary term that way
 * - Otherwise, we'll have to grab the first term in the list
 *
 * If we should display all terms:
 * - Grab all terms for the taxonomy
 */
if ( $only_primay_term ) {
	$primary_term = $post->get_primary_term( get_the_ID(), $taxonomy );
} else {
	$terms = get_the_terms( get_the_ID(), $taxonomy );
}

if ( ! $terms && ! $primary_term && ! is_admin() ) {
	return;
}
?>

<div <?php echo get_block_wrapper_attributes(); ?>>
	<?php if ( $primary_term ) {
		// display only primary term
		// displays differently if we should have links or not
		echo $has_links ? sprintf(
			'<span class="%s"><a href="%s" target="%s" class="%s">%s</a></span>',
			esc_attr( "wp-block-tribe-terms__term" ),
			esc_url( get_term_link( $primary_term ) ),
			esc_attr( '_self' ),
			esc_attr( 'wp-block-tribe-terms__link' ),
			esc_html( $primary_term->name )
		) : sprintf(
			'<span class="%s"><span class="%s">%s</span></span>',
			esc_attr( "wp-block-tribe-terms__term" ),
			esc_attr( 'wp-block-tribe-terms__link' ),
			esc_html( $primary_term->name )
		);
	} elseif ( $terms ) {
		// display terms in a list
		echo sprintf(
			'<ul class="%s">',
			esc_attr( 'wp-block-tribe-terms__list' )
		);
		// loop through terms to create a list
		// displays differently if we should have links or not
		foreach ( $terms as $term ) {
			echo $has_links ? sprintf(
				'<li class="%s"><a href="%s" target="%s" class="%s">%s</a></li>',
				esc_attr( "wp-block-tribe-terms__term" ),
				esc_url( get_term_link( $term ) ),
				esc_attr( '_self' ),
				esc_attr( 'wp-block-tribe-terms__link' ),
				esc_html( $term->name )
			) : sprintf(
				'<li class="%s"><span class="%s">%s</span></li>',
				esc_attr( "wp-block-tribe-terms__term" ),
				esc_attr( 'wp-block-tribe-terms__link' ),
				esc_html( $term->name )
			);
		}
		echo '</ul>';
	} else {
		// display if no terms are available
		echo '<!-- Terms block: No terms to list. -->';
	} ?>
</div>
