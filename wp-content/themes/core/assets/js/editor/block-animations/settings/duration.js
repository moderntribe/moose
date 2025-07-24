/**
 * @module duration
 *
 * @description pulls animation duration settings from theme.json or sets default settings
 *
 * theme.json settings:
 *
 * "animationDuration": [
 * 		{ "label": "200ms", "value": "0.2s" },
 * 		{ "label": "800ms", "value": "0.8s" }
 * ],
 */

import { __ } from '@wordpress/i18n';
import themeJson from '../../../../../theme.json';

const duration = themeJson?.settings?.animationDuration ?? [
	{ label: __( '300ms', 'tribe' ), value: '0.3s' },
	{ label: __( '600ms', 'tribe' ), value: '0.6s' },
	{ label: __( '900ms', 'tribe' ), value: '0.9s' },
	{ label: __( '1200ms', 'tribe' ), value: '1.2s' },
	{ label: __( '1400ms', 'tribe' ), value: '1.4s' },
];

export default duration;
