import createWPControls from 'utils/create-wp-controls';
import { __ } from '@wordpress/i18n';

const settings = {
	attributes: {
		removeMargin: {
			type: 'boolean',
		},
		widthFillsContainer: {
			type: 'boolean',
		},
	},
	blocks: [ 'core/image' ],
	controls: [
		{
			applyClass: 's-remove-margin--vertical',
			attribute: 'removeMargin',
			defaultValue: false,
			helpText: __(
				'Turning this feature on will remove default vertical margins from this block.',
				'tribe'
			),
			label: __( 'Remove Margins', 'tribe' ),
			type: 'toggle',
		},
		{
			applyClass: 'is-full-width',
			attribute: 'widthFillsContainer',
			defaultValue: false,
			helpText: __(
				'Determines if the image should fill the width of the container it sits in. The editor must ensure the uploaded image is large enough to fill the area. If the image is not large enough to fill the area it could look pixelated.',
				'tribe'
			),
			label: __( 'Width Fills Container', 'tribe' ),
			type: 'toggle',
		},
	],
};

createWPControls( settings );
