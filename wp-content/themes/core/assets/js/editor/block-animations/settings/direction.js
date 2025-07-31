/**
 * @module direction
 *
 * @description pulls animation direction settings from theme.json or sets default settings
 *
 * theme.json settings:
 *
 * "animationDirection": {
 * 		"fade-in": [
 * 			{ "label": "Top", "value": "top" },
 * 			{ "label": "Bottom", "value": "bottom" }
 * 		]
 * },
 */

import { __ } from '@wordpress/i18n';
import themeJson from '../../../../../theme.json';

// direction is an object with keys for each animation type
const direction = themeJson?.settings?.animationDirection ?? {
	'fade-in': [
		{ label: __( 'Bottom', 'tribe' ), value: 'bottom' },
		{ label: __( 'Right', 'tribe' ), value: 'right' },
		{ label: __( 'Top Right', 'tribe' ), value: 'top-right' },
		{ label: __( 'Bottom Right', 'tribe' ), value: 'bottom-right' },
		{ label: __( 'Left', 'tribe' ), value: 'left' },
		{ label: __( 'Top Left', 'tribe' ), value: 'top-left' },
		{ label: __( 'Bottom Left', 'tribe' ), value: 'bottom-left' },
		{ label: __( 'Forward', 'tribe' ), value: 'forward' },
		{ label: __( 'Back', 'tribe' ), value: 'back' },
		{ label: __( 'Top', 'tribe' ), value: 'top' },
		{ label: __( 'Simple', 'tribe' ), value: 'simple' },
	],
};

export default direction;
