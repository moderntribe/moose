/**
 * unregister some block styles we don't need
 */

wp.domReady( () => {
	if ( wp.blocks ) {
		wp.blocks.unregisterBlockStyle( 'core/separator', 'default' );
		wp.blocks.unregisterBlockStyle( 'core/separator', 'wide' );
		wp.blocks.unregisterBlockStyle( 'core/separator', 'dots' );
	}
} );

console.info( 'tribe-admin js loaded' );
