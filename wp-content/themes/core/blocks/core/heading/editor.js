import createWPControls from 'utils/create-wp-controls';
import { __ } from '@wordpress/i18n';

const settings = {
	attributes: {
		useBalancedText: {
			type: 'boolean',
		},
	},
	blocks: [ 'core/heading' ],
	controls: [
		{
			applyClass: 'has-balanced-text',
			attribute: 'useBalancedText',
			defaultValue: false,
			helpText: __(
				'Turning this feature on sets text-wrap to balance to allow better responsive text',
				'tribe'
			),
			label: __( 'Use Balanced Text', 'tribe' ),
			type: 'toggle',
		},
	],
};

createWPControls( settings );
