<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks\Styles;

class Block_Styles_Registrar {

	/**
	 * @param \Tribe\Plugin\Blocks\Styles\Block_Styles_Base $block_styles
	 */
	public function register( Block_Styles_Base $block_styles ): void {
		if ( ! function_exists( 'register_block_style' ) ) {
			return;
		}

		foreach ( $block_styles->get_block_styles() as $name => $label ) {
			register_block_style( $block_styles->get_block_name(), [
				'name'  => $name,
				'label' => $label,
			] );
		}
	}

}
