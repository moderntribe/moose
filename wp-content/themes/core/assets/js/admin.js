/**
 * unregister some block styles we don't need
 */

wp.domReady( () => {
	if ( wp.blocks ) {
		wp.blocks.unregisterBlockStyle( 'core/quote', 'default' );
		wp.blocks.unregisterBlockStyle( 'core/quote', 'plain' );
	}
} );

console.info( 'tribe-admin js loaded' );
