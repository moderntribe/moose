/**
 * @module attributes
 *
 * @description creates attributes & their defaults for each animated block
 */

const attributes = {
	animationType: {
		type: 'string',
		default: 'none',
	},
	animationDirection: {
		type: 'string',
		default: 'bottom',
	},
	showAdvancedControls: {
		type: 'boolean',
		default: false,
	},
	animationDuration: {
		type: 'string',
		default: '0.6s',
	},
	animationDelay: {
		type: 'string',
		default: '0s',
	},
	animationMobileDisableDelay: {
		type: 'boolean',
		default: false,
	},
	animationEasing: {
		type: 'string',
		default: 'cubic-bezier(0.390, 0.575, 0.565, 1.000)',
	},
	animationTrigger: {
		type: 'boolean',
		default: false,
	},
	animationPosition: {
		type: 'string',
		default: '25',
	},
};

export default attributes;
