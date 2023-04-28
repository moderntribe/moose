/**
 * unregister some block styles we don't need
 */

wp.domReady( () => {
	if ( wp.blocks ) {
		wp.blocks.unregisterBlockStyle( 'core/separator', 'default' );
		wp.blocks.unregisterBlockStyle( 'core/separator', 'wide' );
		wp.blocks.unregisterBlockStyle( 'core/separator', 'dots' );
		wp.blocks.unregisterBlockStyle( 'core/quote', 'default' );
		wp.blocks.unregisterBlockStyle( 'core/quote', 'plain' );
	}
} );

console.info( 'tribe-admin js loaded' );
