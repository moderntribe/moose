/**
 * @module offset
 *
 * @description pulls animation offset settings from theme.json or sets default settings
 *
 * theme.json settings:
 *
 * "animationOffset": {
 * 		"0.2s": "20px",
 * 		"0.8s": "50px"
 * },
 */

import themeJson from '../../../../../theme.json';

const offset = themeJson?.settings?.animationOffset ?? {
	'0.3s': '20px',
	'0.6s': '50px',
	'0.9s': '90px',
	'1.2s': '160px',
	'1.4s': '280px',
};

export default offset;
