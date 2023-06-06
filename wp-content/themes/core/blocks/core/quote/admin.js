import { ready } from 'utils/events.js';
import { unregisterBlockStyle } from '@wordpress/blocks';

ready( () => {
	unregisterBlockStyle( 'core/quote', 'default' );
	unregisterBlockStyle( 'core/quote', 'plain' );
} );
