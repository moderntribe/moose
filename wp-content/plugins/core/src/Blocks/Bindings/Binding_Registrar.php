<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks\Bindings;

class Binding_Registrar {

	public function register( Binding_Interface $binding ): void {
		if ( ! function_exists( 'register_block_bindings_source' ) ) {
			return;
		}

		register_block_bindings_source( $binding->get_slug(), $binding->get_properties() );
	}

}
