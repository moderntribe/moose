<?php declare(strict_types=1);

// get block settings (attributes)
$taxonomy         = $attributes['taxonomyToUse'] ?? 'category';
$only_primay_term = $attributes['onlyPrimaryTerm'] ?? false;
$has_links        = $attributes['hasLinks'] ?? false;
$style            = $attributes['termStyle'] ?? 'default';
$terms            = false;
$primary_term     = false;

/**
 * If we should only display the primary term:
 * - If we have yoast we can get the primary term that way
 * - Otherwise, we'll have to grab the first term in the list
 *
 * If we should display all terms:
 * - Grab all terms for the taxonomy
 */
if ( $taxonomy === 'category' && $only_primay_term && class_exists( 'WPSEO_Primary_Term' ) ) {
	$wpseo_primary_term = new WPSEO_Primary_Term( $taxonomy, get_the_ID() );
	$wpseo_primary_term = $wpseo_primary_term->get_primary_term();
	if ( $wpseo_primary_term ) {
		$primary_term = get_term( $wpseo_primary_term );
	}
} elseif ( $only_primay_term ) {
	$terms        = get_the_terms( get_the_ID(), $taxonomy );
	$primary_term = $terms ? $terms[0] : false;
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
			esc_attr( "wp-block-tribe-terms__term is-style-$style" ),
			esc_url( get_term_link( $primary_term ) ),
			esc_attr( '_self' ),
			esc_attr( 'wp-block-tribe-terms__link' ),
			esc_html( $primary_term->name )
		) : sprintf(
			'<span class="%s"><span class="%s">%s</span></span>',
			esc_attr( "wp-block-tribe-terms__term is-style-$style" ),
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
				esc_attr( "wp-block-tribe-terms__term is-style-$style" ),
				esc_url( get_term_link( $term ) ),
				esc_attr( '_self' ),
				esc_attr( 'wp-block-tribe-terms__link' ),
				esc_html( $term->name )
			) : sprintf(
				'<li class="%s"><span class="%s">%s</span></li>',
				esc_attr( "wp-block-tribe-terms__term is-style-$style" ),
				esc_attr( 'wp-block-tribe-terms__link' ),
				esc_html( $term->name )
			);
		}
		echo '</ul>';
	} else {
		// display if no terms are available
		echo 'No terms to list.';
	} ?>
</div>
