<?php declare(strict_types=1);

use Tribe\Plugin\Blocks\Helpers\Block_Animation_Attributes;
use Tribe\Plugin\Blocks\Helpers\Icon_Picker;

/**
 * @var array $attributes
 */

$animation_attributes  = new Block_Animation_Attributes( $attributes );
$animation_styles      = $animation_attributes->get_styles();
$animation_classes     = $animation_attributes->get_classes();
$icon_picker           = new Icon_Picker( $attributes );
$icon_wrapper_styles   = $icon_picker->get_icon_wrapper_styles();
$icon_svg              = $icon_picker->get_svg();
$classes               = 'b-icon-card';
$title                 = $attributes['title'] ?? '';
$description           = $attributes['description'] ?? '';
$link_url              = $attributes['linkUrl'] ?? '';
$link_opens_in_new_tab = $attributes['linkOpensInNewTab'] ?? false;
$link_text             = $attributes['linkText'] ?? '';
$link_a11y_label       = $attributes['linkA11yLabel'] ?? '';

if ( $animation_classes !== '' ) {
	$classes .= ' ' . $animation_classes;
}
?>
<article <?php echo get_block_wrapper_attributes( [ 'class' => esc_attr( $classes ), 'style' => $animation_styles ] ); ?>>
	<div class="b-icon-card__inner">
		<div class="b-icon-card__top">
			<?php if ( ! empty( $icon_svg ) ) : ?>
				<div class="b-icon-card__media">
					<div class="b-icon-card__icon-wrapper" style="<?php echo esc_attr( $icon_wrapper_styles ); ?>">
						<?php echo $icon_svg; ?>
					</div>
				</div>
			<?php endif; ?>
			<div class="b-icon-card__content">
				<h3 class="t-display-x-small b-icon-card__title"><?php echo esc_html( $title ); ?></h3>
				<?php if ( $description ) : ?>
					<div class="b-icon-card__description t-body-small">
						<?php echo wp_kses_post( nl2br( $description ) ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php if ( $link_url ) : ?>
			<div class="wp-block-buttons is-layout-flex wp-block-buttons-is-layout-flex b-icon-card__buttons" aria-hidden="true">
				<div class="wp-block-button is-style-ghost">
					<span class="wp-block-button__link"><?php echo esc_html( $link_text ); ?></span>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php if ( $link_url ) : ?>
		<a href="<?php echo esc_url( $link_url ); ?>"<?php echo $link_opens_in_new_tab ? ' target="_blank" rel="noopener noreferrer"' : ''; ?> class="b-icon-card__link-overlay" aria-label="<?php echo esc_attr( $link_a11y_label ); ?>"></a>
	<?php endif; ?>
</article>
