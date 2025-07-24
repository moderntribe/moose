/**
 * @module position
 *
 * @description pulls animation position settings from theme.json or sets default settings
 *
 * theme.json settings:
 *
 * "animationPosition": [
 * 		{ "label": "25%", "value": "25" },
 * 		{ "label": "50%", "value": "50" },
 * ],
 */

import { __ } from '@wordpress/i18n';
import themeJson from '../../../../../theme.json';

const position = themeJson?.settings?.animationPosition ?? [
	{ label: __( '25%', 'tribe' ), value: '25' },
	{ label: __( '50%', 'tribe' ), value: '50' },
	{ label: __( '75%', 'tribe' ), value: '75' },
	{ label: __( '100%', 'tribe' ), value: '100' },
];

export default position;
