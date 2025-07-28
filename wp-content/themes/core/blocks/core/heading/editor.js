import { __ } from '@wordpress/i18n';
import createWPControls from 'utils/create-wp-controls';

const settings = {
	attributes: {
		useBalancedText: {
			type: 'boolean',
		},
		useHangingPunctuation: {
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
				'Enable to evenly balance text over multiple lines',
				'tribe'
			),
			label: __( 'Use Balanced Text', 'tribe' ),
			type: 'toggle',
		},
		{
			applyClass: 'has-hanging-punctuation',
			attribute: 'useHangingPunctuation',
			defaultValue: false,
			helpText: __(
				'Enable to visually align opening punctuation with the text edge.',
				'tribe'
			),
			label: __( 'Use Hanging Punctuation', 'tribe' ),
			type: 'toggle',
		},
	],
};

createWPControls( settings );
