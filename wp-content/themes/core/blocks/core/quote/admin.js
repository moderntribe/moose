import domReady from '@wordpress/dom-ready';
import { unregisterBlockStyle } from '@wordpress/blocks';

domReady( () => {
	unregisterBlockStyle( 'core/quote', 'default' );
	unregisterBlockStyle( 'core/quote', 'plain' );
} );
