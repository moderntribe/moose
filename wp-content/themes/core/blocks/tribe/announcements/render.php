<?php declare(strict_types=1);

// Example block attributes (these would come from your block settings):
$heading     = 'Important Update';
$body        = 'We’ve launched a new feature you should check out.';
$cta_label   = 'Learn More';
$cta_url     = '#';
$cta_style   = 'outlined'; // outlined | ghost
$align       = 'left';   // left | center
$theme       = 'error';    // brand | black | error | warning
$dismissible = true;       // true | false
$dark_themes = [ 'brand', 'black', 'error' ];

// Build classes dynamically
$classes = [
	'b-announcement',
	'b-announcement--theme-' . esc_attr( $theme ),
	'b-announcement--align-' . esc_attr( $align ),
];

// @phpstan-ignore-next-line
if ( $dismissible ) {
	$classes[] = 'b-announcement--is-dismissible';
}

if ( in_array( $theme, $dark_themes ) ) {
	$classes[] = 'is-style-dark';
}
?>
<section <?php echo wp_kses_data( get_block_wrapper_attributes( [
	'class'      => implode( ' ', $classes ),
	'role'       => 'region',
	'aria-label' => esc_attr__( 'Site announcement', 'tribe' ),
] ) ); ?>>
	<div class="b-announcement__inner">
		<?php // @phpstan-ignore-next-line
		if ( $heading ) : ?>
			<h2 class="b-announcement__heading t-body"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php // @phpstan-ignore-next-line
		if ( $body ) : ?>
			<p class="b-announcement__body t-body"><?php echo esc_html( $body ); ?></p>
		<?php endif; ?>

		<?php // @phpstan-ignore-next-line
		if ( $cta_label && $cta_url ) : ?>
			<div class="wp-block-buttons b-announcement__cta-wrapper">
				<span class="wp-block-button is-style-<?php echo esc_attr( $cta_style ); ?> tribe-button-has-icon">
					<a href="<?php echo esc_url( $cta_url ); ?>" class="wp-block-button__link wp-element-button b-announcement__cta u-<?php echo esc_attr( $cta_style ); ?>-button-reset"><?php echo esc_html( $cta_label ); ?></a>
				</span>
			</div>
		<?php endif; ?>
	</div>

	<?php // @phpstan-ignore-next-line
	if ( $dismissible ) : ?>
		<div class="b-announcement__dismiss-wrapper">
			<button type="button" class="b-announcement__dismiss u-button-reset" aria-label="Dismiss announcement">
				<span class="b-announcement__dismiss-text"><?php echo esc_html__( 'Dismiss', 'tribe' ); ?></span>
			</button>
		</div>
	<?php endif; ?>
</section>

