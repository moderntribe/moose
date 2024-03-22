import createWPControls from 'utils/create-wp-controls';
import { __ } from '@wordpress/i18n';

const settings = {
	attributes: {
		customGap: {
			type: 'string',
		},
	},
	blocks: [ 'core/columns' ],
	controls: [
		{
			applyClass: 'tribe-has-custom-gap',
			applyStyleProperty: '--tribe-custom-gap',
			attribute: 'customGap',
			defaultValue: '',
			helpText: __(
				'The spacing variable the Columns block should use to override the default column/row gap',
				'tribe'
			),
			label: __( 'Custom Spacing Gap', 'tribe' ),
			type: 'select',
			selectOptions: [
				{
					label: __( 'No custom gap', 'tribe' ),
					value: '',
				},
				{
					label: __( 'Spacer 10', 'tribe' ),
					value: 'var(--spacer-10)',
				},
				{
					label: __( 'Spacer 20', 'tribe' ),
					value: 'var(--spacer-20)',
				},
				{
					label: __( 'Spacer 30', 'tribe' ),
					value: 'var(--spacer-30)',
				},
				{
					label: __( 'Spacer 40', 'tribe' ),
					value: 'var(--spacer-40)',
				},
				{
					label: __( 'Spacer 50', 'tribe' ),
					value: 'var(--spacer-50)',
				},
				{
					label: __( 'Spacer 60', 'tribe' ),
					value: 'var(--spacer-60)',
				},
				{
					label: __( 'Spacer 70', 'tribe' ),
					value: 'var(--spacer-70)',
				},
			],
		},
	],
};

createWPControls( settings );
