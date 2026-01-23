<?php declare(strict_types=1);

use Tribe\Plugin\Blocks\Helpers\Block_Animation_Attributes;

/**
 * @var array $attributes
 */

$animation_attributes  = new Block_Animation_Attributes( $attributes );
$classes               = 'b-image-card';
$media_id              = $attributes['mediaId'] ? (int) $attributes['mediaId'] : 0; // this returns a float by default so we need to cast it to int
$media_url             = $attributes['mediaUrl'] ?? '';
$title                 = $attributes['title'] ?? '';
$description           = $attributes['description'] ?? '';
$link_url              = $attributes['linkUrl'] ?? '';
$link_opens_in_new_tab = $attributes['linkOpensInNewTab'] ?? false;
$link_text             = $attributes['linkText'] ?? '';
$link_a11y_label       = $attributes['linkA11yLabel'] ?? '';

if ( $animation_attributes->get_classes() !== '' ) {
	$classes .= ' ' . $animation_attributes->get_classes();
}
?>
<article <?php echo get_block_wrapper_attributes( [ 'class' => esc_attr( $classes ), 'style' => $animation_attributes->get_styles() ] ); ?>>
	<div class="b-image-card__inner">
		<?php if ( $media_id !== 0 ) : ?>
			<div class="aspect-ratio-cover aspect-ratio-3-2 b-image-card__media">
				<?php echo wp_get_attachment_image( $media_id, 'large' ); ?>
			</div>
		<?php elseif ( $media_url ) : ?>
			<div class="aspect-ratio-cover aspect-ratio-3-2 b-image-card__media">
				<img src="<?php echo esc_url( $media_url ); ?>" alt="<?php echo esc_attr__( 'Block placeholder image', 'tribe' ); ?>">
			</div>
		<?php endif; ?>
		<div class="b-image-card__content">
			<div class="b-image-card__content-top">
				<div class="b-image-card__title-wrap">
					<h3 class="b-image-card__title t-display-x-small t-animated-underline"><?php echo esc_html( $title ); ?></h3>
				</div>
				<?php if ( $description ) : ?>
					<div class="t-body-small b-image-card__description">
						<?php echo wp_kses_post( nl2br( $description ) ); ?>
					</div>
				<?php endif; ?>
			</div>
			<?php if ( $link_url ) : ?>
				<div class="b-image-card__buttons l-flex" aria-hidden="true">
					<div class="b-image-card__button">
						<span class="a-btn-ghost"><?php echo esc_html( $link_text ); ?></span>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php if ( $link_url ) : ?>
		<a href="<?php echo esc_url( $link_url ); ?>"<?php echo $link_opens_in_new_tab ? ' target="_blank" rel="noopener noreferrer"' : ''; ?> class="b-image-card__link-overlay" aria-label="<?php echo esc_attr( $link_a11y_label ); ?>"></a>
	<?php endif; ?>
</article>
