<?php declare(strict_types=1);

$icon_key   = $attributes['selectedIcon'] ?? '';
$icon_color = $attributes['selectedIconColor'] ?? 'currentColor';
$bg_color   = $attributes['selectedBgColor'] ?? 'transparent';
$padding    = intval( $attributes['iconPadding'] ?? 0 );
$size       = intval( $attributes['iconSize'] ?? 24 );
$is_rounded = ! empty( $attributes['isRounded'] );
$icon_label = $attributes['iconLabel'] ?? '';

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
}

if ( ! empty( $svg ) ) : ?>
<div class="wp-block-tribe-icon-picker">
	<div class="icon-wrapper" style="<?php echo esc_attr( $style ); ?>">
		<?php echo $svg; ?>
	</div>
</div>
<?php endif; ?>
