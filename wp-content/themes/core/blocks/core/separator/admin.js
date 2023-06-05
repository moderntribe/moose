import domReady from '@wordpress/dom-ready';
import { unregisterBlockStyle } from '@wordpress/blocks';

domReady( () => {
	unregisterBlockStyle( 'core/separator', 'default' );
	unregisterBlockStyle( 'core/separator', 'wide' );
	unregisterBlockStyle( 'core/separator', 'dots' );
} );
