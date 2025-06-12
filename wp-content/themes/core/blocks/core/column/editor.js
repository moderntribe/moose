import createWPControls from 'utils/create-wp-controls';
import { __ } from '@wordpress/i18n';

const settings = {
	attributes: {
		stackingOrder: {
			type: 'number',
		},
	},
	blocks: [ 'core/column' ],
	controls: [
		{
			applyClass: 'tribe-has-stacking-order',
			applyStyleProperty: '--tribe-stacking-order',
			attribute: 'stackingOrder',
			defaultValue: 0,
			helpText: __(
				'The stacking order of the element at mobile breakpoints. This setting only applies if the "Stack on mobile" setting for the Columns block is turned on.',
				'tribe'
			),
			label: __( 'Stacking Order', 'tribe' ),
			type: 'number',
		},
	],
};

createWPControls( settings );
