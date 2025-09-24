<?php declare(strict_types=1);

/**
 * @var object $attributes
 */

$classes               = 'b-icon-card';
$title                 = $attributes['title'] ?: '';
$description           = $attributes['description'] ?: '';
$link_url              = $attributes['linkUrl'] ?: '';
$link_opens_in_new_tab = $attributes['linkOpensInNewTab'] ?: false;
$link_text             = $attributes['linkText'] ?: '';
$link_a11y_label       = $attributes['linkA11yLabel'] ?: '';
?>
<article <?php echo get_block_wrapper_attributes( [ 'class' => esc_attr( $classes ) ] ); ?>>
	<div class="b-icon-card__inner">
		<div class="b-icon-card__media">

		</div>
		<div class="b-icon-card__content">
			<h3 class="t-display-x-small b-icon-card__title"><?php echo esc_html( $title ); ?></h3>
			<?php if ( $description ) : ?>
				<div class="b-icon-card__description">
					<?php echo wp_kses_post( $description ); ?>
				</div>
			<?php endif; ?>
			<?php if ( $link_url ) : ?>
				<div class="wp-block-buttons is-layout-flex wp-block-buttons-is-layout-flex b-icon-card__buttons" aria-hidden="true">
					<div class="wp-block-button is-style-ghost">
						<span class="wp-block-button__link"><?php echo esc_html( $link_text ); ?></span>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php if ( $link_url ) : ?>
		<a href="<?php echo esc_url( $link_url ); ?>" target="<?php echo $link_opens_in_new_tab ? '_blank' : ''; ?>" class="b-icon-card__link-overlay" aria-label="<?php echo esc_attr( $link_a11y_label ); ?>"></a>
	<?php endif; ?>
</article>
