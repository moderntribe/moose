/**
 * Admin scripts specific to this block
 */

import { ready } from 'utils/events.js';
import {
	registerBlockVariation,
	unregisterBlockStyle,
} from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

ready( () => {
	unregisterBlockStyle( 'core/button', 'default' );
	unregisterBlockStyle( 'core/button', 'fill' );
	unregisterBlockStyle( 'core/button', 'outline' );

	registerBlockVariation( 'core/button', {
		name: 'primary-button',
		title: __( 'Primary', 'tribe' ),
		scope: [ 'block', 'inserter', 'transform' ],
		attributes: {
			className: 'is-style-primary',
		},
		icon: 'button',
		isDefault: true,
	} );

	registerBlockVariation( 'core/button', {
		name: 'secondary-button',
		title: __( 'Secondary', 'tribe' ),
		scope: [ 'block', 'inserter', 'transform' ],
		attributes: {
			className: 'is-style-secondary',
		},
		icon: 'button',
		isDefault: false,
	} );

	registerBlockVariation( 'core/button', {
		name: 'ghost-button',
		title: __( 'Ghost', 'tribe' ),
		scope: [ 'block', 'inserter', 'transform' ],
		attributes: {
			className: 'is-style-ghost',
		},
		icon: 'button',
		isDefault: false,
	} );
} );
