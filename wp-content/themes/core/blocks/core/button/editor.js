/**
 * Admin scripts specific to this block
 */

import createWPControls from 'utils/create-wp-controls';
import { ready } from 'utils/events.js';
import { unregisterBlockStyle } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

const settings = {
	attributes: {
		hasIcon: {
			type: 'boolean',
		},
	},
	blocks: [ 'core/button' ],
	controls: [
		{
			applyClass: 'tribe-button-has-icon',
			attribute: 'hasIcon',
			defaultValue: false,
			helpText: __(
				'Toggle to display the button with an arrow icon. Note that this setting does not apply to the "Ghost" button style.',
				'tribe'
			),
			label: __( 'Has Arrow Icon', 'tribe' ),
			type: 'toggle',
		},
	],
};

createWPControls( settings );

ready( () => {
	unregisterBlockStyle( 'core/button', 'fill' );
	unregisterBlockStyle( 'core/button', 'outline' );
} );
