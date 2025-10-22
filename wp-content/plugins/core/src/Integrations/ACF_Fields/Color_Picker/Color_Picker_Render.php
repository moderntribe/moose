<?php declare(strict_types=1);

namespace Tribe\Plugin\Integrations\ACF_Fields\Color_Picker;

class Color_Picker_Render {

	public function render_color_picker( array $field ): void {
		$value = esc_attr( $field['value'] ?? '' );
		$name  = esc_attr( $field['name'] );

		$colors = $field['colors'];

		echo '<div class="acf-header-color-picker">';
		echo '<div class="acf-header-color-swatches">';

		foreach ( $colors as $slug => $background ) {
			$class   = 'is-header--' . sanitize_title( $slug );
			$checked = $value === $class ? 'aria-checked="true"' : '';
			echo sprintf(
				'<button type="button" class="acf-header-swatch %s" style="background:%s" data-value="%s" %s></button>',
				esc_attr( $class ),
				esc_attr( $background ),
				esc_attr( $class ),
				$checked
			);
		}

		echo '</div>';
		echo sprintf( '<p class="description">%s</p>', esc_html__( 'Choose a color theme for the header on this page.', 'tribe' ) );
		echo sprintf( '<input type="hidden" name="%s" value="%s" />', $name, $value );
		echo '</div>';
	}

}
