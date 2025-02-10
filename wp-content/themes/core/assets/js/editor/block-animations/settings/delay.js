/**
 * @module delay
 *
 * @description pulls animation delay settings from theme.json or sets default settings
 *
 * theme.json settings:
 *
 * "animationDelay": [
 * 		{ "label": "0", "value": "0s" },
 * 		{ "label": "200ms", "value": "0.2s" },
 * 		{ "label": "800ms", "value": "0.8s" }
 * ],
 */

import { __ } from '@wordpress/i18n';
import themeJson from '../../../../../theme.json';

const delay = themeJson?.settings?.animationDelay ?? [
	{ label: __( '0', 'tribe' ), value: '0s' },
	{ label: __( '300ms', 'tribe' ), value: '0.3s' },
	{ label: __( '600ms', 'tribe' ), value: '0.6s' },
	{ label: __( '900ms', 'tribe' ), value: '0.9s' },
	{ label: __( '1200ms', 'tribe' ), value: '1.2s' },
	{ label: __( '1500ms', 'tribe' ), value: '1.5s' },
];

export default delay;
