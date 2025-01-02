import createWPControls from 'utils/create-wp-controls';
import { __ } from '@wordpress/i18n';

const settings = {
	attributes: {
		removeThemeBackground: {
			type: 'boolean',
			default: false,
		},
	},
	blocks: [ 'core/group' ],
	controls: [
		{
			applyClass: 'tribe-remove-theme-background',
			attribute: 'removeThemeBackground',
			defaultValue: false,
			helpText: __(
				'Use this setting to remove the background color of a Group block when using a theme.',
				'tribe'
			),
			label: __( 'Remove theme background', 'tribe' ),
			type: 'toggle',
		},
	],
};

createWPControls( settings );
