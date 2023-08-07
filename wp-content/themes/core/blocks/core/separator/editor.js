import { ready } from 'utils/events.js';
import { unregisterBlockStyle } from '@wordpress/blocks';

ready( () => {
	unregisterBlockStyle( 'core/separator', 'default' );
	unregisterBlockStyle( 'core/separator', 'wide' );
	unregisterBlockStyle( 'core/separator', 'dots' );
} );
