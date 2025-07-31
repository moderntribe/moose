/**
 * @module easing
 *
 * @description pulls animation easing settings from theme.json or sets default settings
 *
 * theme.json settings:
 *
 * "animationEasing": [
 * 		{ "label": "Ease In", "value": "ease-in" },
 * 		{ "label": "Ease Out", "value": "ease-out" }
 * ],
 */

import { __ } from '@wordpress/i18n';
import themeJson from '../../../../../theme.json';

const easing = themeJson?.settings?.animationEasing ?? [
	{
		label: __( 'Ease Out Sine', 'tribe' ),
		value: 'var(--easing--ease-out-sine)',
	},
	{
		label: __( 'Ease In Sine', 'tribe' ),
		value: 'var(--easing--ease-in-sine)',
	},
	{
		label: __( 'Ease In Out Sine', 'tribe' ),
		value: 'var(--easing--ease-in-out-sine)',
	},
	{
		label: __( 'Ease Out Quad', 'tribe' ),
		value: 'var(--easing--ease-out-quad)',
	},
	{
		label: __( 'Ease In Quad', 'tribe' ),
		value: 'var(--easing--ease-in-quad)',
	},
	{
		label: __( 'Ease In Out Quad', 'tribe' ),
		value: 'var(--easing--ease-in-out-quad)',
	},
];

export default easing;
