/**
 * Admin scripts specific to this block
 */

import { ready } from 'utils/events.js';
import { unregisterBlockStyle } from '@wordpress/blocks';

ready( () => {
	unregisterBlockStyle( 'core/button', 'default' );
	unregisterBlockStyle( 'core/button', 'outline' );
} );
