<?php declare(strict_types=1);

use enshrined\svgSanitize\Sanitizer;

/**
 * @var object $attributes
 */

$classes               = 'b-icon-card';
$icon_key              = $attributes['selectedIcon'] ?? '';
$icon_color            = $attributes['selectedIconColor'] ?? 'currentColor';
$bg_color              = $attributes['selectedBgColor'] ?? 'transparent';
$padding               = intval( $attributes['iconPadding'] ?? 0 );
$size                  = intval( $attributes['iconSize'] ?? 24 );
$is_rounded            = ! empty( $attributes['isRounded'] );
$icon_label            = $attributes['iconLabel'] ?? '';
$title                 = $attributes['title'] ?: '';
$description           = $attributes['description'] ?: '';
$link_url              = $attributes['linkUrl'] ?: '';
$link_opens_in_new_tab = $attributes['linkOpensInNewTab'] ?: false;
$link_text             = $attributes['linkText'] ?: '';
$link_a11y_label       = $attributes['linkA11yLabel'] ?: '';

$style = sprintf(
	'--icon-picker--background-color:%s;
	--icon-picker--icon-color:%s;
	--icon-picker--icon-size:%dpx;
	--icon-picker--icon-padding:%dpx;
	--icon-picker--border-radius:%s;',
	esc_attr( $bg_color ),
	esc_attr( $icon_color ),
	$size,
	$padding,
	$is_rounded ? '50%' : '0'
);

$icon_path = get_template_directory() . '/blocks/tribe/icon-picker/icons/svg/' . $icon_key . '.svg';
$svg       = '';

if ( file_exists( $icon_path ) ) {
	$svg = file_get_contents( $icon_path );

	if ( $icon_label ) {
		$svg = preg_replace(
			'/<svg\b([^>]*)>/',
			'<svg$1 aria-label="' . esc_attr( $icon_label ) . '" role="img">',
			$svg,
			1
		);
	}

	// Sanitize the SVG.
	$sanitizer = new Sanitizer();
	$svg       = $sanitizer->sanitize( $svg );
}
?>
<article <?php echo get_block_wrapper_attributes( [ 'class' => esc_attr( $classes ) ] ); ?>>
	<div class="b-icon-card__inner">
		<div class="b-icon-card__top">
			<?php if ( $svg !== '' && $svg !== false ) : ?>
				<div class="b-icon-card__media">
					<div class="b-icon-card__icon-wrapper" style="<?php echo esc_attr( $style ); ?>">
						<?php echo $svg; ?>
					</div>
				</div>
			<?php endif; ?>
			<div class="b-icon-card__content">
				<h3 class="t-display-x-small b-icon-card__title"><?php echo esc_html( $title ); ?></h3>
				<?php if ( $description ) : ?>
					<div class="b-icon-card__description t-body-small">
						<?php echo wp_kses_post( $description ); ?>
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
