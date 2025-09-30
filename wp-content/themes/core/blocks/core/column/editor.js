import createWPControls from 'utils/create-wp-controls';
import { __ } from '@wordpress/i18n';

const settings = {
	attributes: {
		isSticky: {
			type: 'boolean',
			default: false,
		},
		stackingOrder: {
			type: 'number',
		},
	},
	blocks: [ 'core/column' ],
	controls: [
		{
			applyClass: 'tribe-is-sticky',
			attribute: 'isSticky',
			defaultValue: false,
			helpText: __(
				'Makes column stick to top when scrolling. Only one column should be set to sticky per row and the "sticky" effect only applies on screens larger than tablet size (782px).',
				'tribe'
			),
			label: __( 'Stick Column on Scroll', 'tribe' ),
			type: 'toggle',
		},
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
