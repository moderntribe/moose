<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks\Patterns;

class Pattern_Registrar {

	public function register( Pattern_Interface $pattern ): void {
		if ( ! function_exists( 'register_block_pattern' ) ) {
			return;
		}

		register_block_pattern( $pattern->get_name(), $pattern->get_properties() );
	}

}
