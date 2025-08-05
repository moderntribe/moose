import { __ } from '@wordpress/i18n';
import createWPControls from 'utils/create-wp-controls';

const settings = {
	attributes: {
		useBalancedText: {
			type: 'boolean',
		},
	},
	blocks: [ 'core/paragraph' ],
	controls: [
		{
			applyClass: 'has-balanced-text',
			attribute: 'useBalancedText',
			defaultValue: false,
			helpText: __(
				'Distribute text evenly across multiple lines.',
				'tribe'
			),
			label: __( 'Use Balanced Text', 'tribe' ),
			type: 'toggle',
		},
	],
};

createWPControls( settings );
