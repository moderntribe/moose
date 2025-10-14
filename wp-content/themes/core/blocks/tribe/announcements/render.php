<?php declare(strict_types=1);

/**
 * @var array $attributes The block attributes.
 */

$post_id     = $attributes['announcement_id'] ?? get_the_ID();
$heading     = $attributes['heading'];
$body        = $attributes['body'];
$cta_label   = $attributes['cta_label'];
$cta_url     = $attributes['cta_link'];
$cta_style   = $attributes['cta_style'];
$align       = $attributes['align'] ?? 'center';
$theme       = $attributes['theme'];
$dismissible = $attributes['dismissible'];
$dark_themes = [ 'brand', 'black', 'error' ];

// Build classes dynamically
$classes = [
	'b-announcement',
	'b-announcement--theme-' . esc_attr( $theme ),
	'b-announcement--align-' . esc_attr( $align ),
];

// Add dark style class for dark themes
if ( in_array( $theme, $dark_themes ) ) {
	$classes[] = 'is-style-dark';
}
?>
<aside <?php echo wp_kses_data( get_block_wrapper_attributes( [
	'class'                => implode( ' ', $classes ),
	'role'                 => 'region',
	'aria-label'           => esc_attr__( 'Site announcement', 'tribe' ),
	'data-announcement-id' => esc_attr( (string) $post_id ),
] ) ); ?>>
	<div class="b-announcement__inner">
		<?php if ( $heading ) : ?>
			<h2 class="b-announcement__heading t-body"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php if ( $body ) : ?>
			<p class="b-announcement__body t-body"><?php echo esc_html( $body ); ?></p>
		<?php endif; ?>

		<?php if ( $cta_label && $cta_url ) : ?>
			<div class="b-announcement__cta-wrapper l-flex">
				<span class="b-announcement__cta">
					<a href="<?php echo esc_url( $cta_url ); ?>" class="a-btn-<?php echo esc_attr( $cta_style ); ?>"><?php echo esc_html( $cta_label ); ?></a>
				</span>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( $dismissible ) : ?>
		<div class="b-announcement__dismiss-wrapper">
			<button type="button" class="b-announcement__dismiss u-button-reset" aria-label="Dismiss announcement">
				<span class="b-announcement__dismiss-text"><?php echo esc_html__( 'Dismiss', 'tribe' ); ?></span>
			</button>
		</div>
	<?php endif; ?>
</aside>

