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
		value: 'cubic-bezier(0.390, 0.575, 0.565, 1.000)',
	},
	{
		label: __( 'Ease In Sine', 'tribe' ),
		value: 'cubic-bezier(0.470, 0.000, 0.745, 0.715)',
	},
	{
		label: __( 'Ease In Out Sine', 'tribe' ),
		value: 'cubic-bezier(0.445, 0.050, 0.550, 0.950)',
	},
	{
		label: __( 'Ease Out Quad', 'tribe' ),
		value: 'cubic-bezier(0.250, 0.460, 0.450, 0.940)',
	},
	{
		label: __( 'Ease In Quad', 'tribe' ),
		value: 'cubic-bezier(0.550, 0.085, 0.680, 0.530)',
	},
	{
		label: __( 'Ease In Out Quad', 'tribe' ),
		value: 'cubic-bezier(0.455, 0.030, 0.515, 0.955)',
	},
];

export default easing;
