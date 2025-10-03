<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks\Helpers;

class Icon_Picker {

	private string $icon_key;
	private string $icon_color;
	private string $bg_color;
	private int $padding;
	private int $size;
	private bool $is_rounded;
	private string $icon_label;

	public function __construct( array $block_attributes ) {
		$this->icon_key   = $block_attributes['selectedIcon'] ?? '';
		$this->icon_color = $block_attributes['selectedIconColor'] ?? 'currentcolor';
		$this->bg_color   = $block_attributes['selectedBgColor'] ?? 'transparent';
		$this->padding    = intval( $block_attributes['iconPadding'] ?? 0 );
		$this->size       = intval( $block_attributes['iconSize'] ?? 24 );
		$this->is_rounded = ! empty( $block_attributes['isRounded'] );
		$this->icon_label = $block_attributes['iconLabel'] ?? '';
	}

	public function get_icon_wrapper_styles(): string {
		return sprintf(
			'--icon-picker--background-color:%s;
			--icon-picker--icon-color:%s;
			--icon-picker--icon-size:%dpx;
			--icon-picker--icon-padding:%dpx;
			--icon-picker--border-radius:%s;',
			esc_attr( $this->bg_color ),
			esc_attr( $this->icon_color ),
			$this->size,
			$this->padding,
			$this->is_rounded ? '50%' : '0'
		);
	}

	public function get_svg(): string {
		$icon_path = get_template_directory() . '/blocks/tribe/icon-picker/icons/svg/' . $this->icon_key . '.svg';
		$svg       = '';

		if ( file_exists( $icon_path ) ) {
			$svg = file_get_contents( $icon_path );

			if ( $this->icon_label ) {
				$svg = preg_replace(
					'/<svg\b([^>]*)>/',
					'<svg$1 aria-label="' . esc_attr( $this->icon_label ) . '" role="img">',
					$svg,
					1
				);
			}
		}

		return $svg;
	}

}
