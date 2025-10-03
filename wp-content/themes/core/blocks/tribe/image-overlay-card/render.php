<?php declare(strict_types=1);

use Tribe\Plugin\Blocks\Helpers\Block_Animation_Attributes;

/**
 * @var object $attributes
 */

$animation_attributes  = new Block_Animation_Attributes();
$classes               = 'b-image-overlay-card';
$animation_styles      = $animation_attributes->get_styles();
$animation_classes     = $animation_attributes->get_classes();
$media_id              = $attributes['mediaId'] ? (int) $attributes['mediaId'] : 0; // this returns a float by default so we need to cast it to int
$media_url             = $attributes['mediaUrl'] ?? '';
$overlay_color         = $attributes['overlayColor'] ?? '#0000001C';
$overlay_hover_color   = $attributes['overlayHoverColor'] ?? '#00000033';
$card_uses_dark_theme  = $attributes['cardUsesDarkTheme'];
$title                 = $attributes['title'] ?? '';
$link_url              = $attributes['linkUrl'] ?? '';
$link_opens_in_new_tab = $attributes['linkOpensInNewTab'] ?? false;
$link_text             = $attributes['linkText'] ?? '';
$link_a11y_label       = $attributes['linkA11yLabel'] ?? '';

// add overlay color as CSS custom property
$styles  = $animation_styles;
$styles .= "--card-image-overlay-color: {$overlay_color};";
$styles .= "--card-image-overlay-hover-color: {$overlay_hover_color};";

// add dark theme class if applicable
if ( $card_uses_dark_theme ) {
	$classes .= ' b-image-overlay-card--dark-theme';
}

// add animation attribute classes
if ( $animation_classes !== '' ) {
	$classes .= ' ' . $animation_classes;
}
?>
<article <?php echo get_block_wrapper_attributes( [ 'class' => esc_attr( $classes ), 'style' => esc_attr( $styles ) ] ); ?>>
	<?php if ( $media_id !== 0 ) : ?>
		<div class="b-image-overlay-card__media">
			<?php echo wp_get_attachment_image( $media_id, 'large' ); ?>
		</div>
	<?php elseif ( $media_url ) : ?>
		<div class="b-image-overlay-card__media">
			<img src="<?php echo esc_url( $media_url ); ?>" alt="<?php echo esc_attr__( 'Block placeholder image', 'tribe' ); ?>" />
		</div>
	<?php endif; ?>
	<div class="b-image-overlay-card__content">
		<h3 class="b-image-overlay-card__title t-display-x-small"><?php echo esc_html( $title ); ?></h3>
		<?php if ( $link_url ) : ?>
			<div class="wp-block-buttons is-layout-flex wp-block-buttons-is-layout-flex b-image-overlay-card__buttons" aria-hidden="true">
				<div class="wp-block-button is-style-ghost">
					<span class="wp-block-button__link"><?php echo esc_html( $link_text ); ?></span>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php if ( $link_url ) : ?>
		<a href="<?php echo esc_url( $link_url ); ?>"<?php echo $link_opens_in_new_tab ? ' target="_blank" rel="noopener noreferrer"' : ''; ?> class="b-image-overlay-card__link-overlay" aria-label="<?php echo esc_attr( $link_a11y_label ); ?>"></a>
	<?php endif; ?>
</article>
