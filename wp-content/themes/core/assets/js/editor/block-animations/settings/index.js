/**
 * @module settings
 *
 * @description pulls animation attributes / settings
 */

import attributes from './attributes.js';
import delay from './delay.js';
import direction from './direction.js';
import duration from './duration.js';
import easing from './easing.js';
import excludes from './excludes.js';
import includes from './includes.js';
import offset from './offset.js';
import position from './position.js';
import { default as type } from './type.js'; // type is a reserved word so we need to use default as alias

const settings = {
	attributes,
	delay,
	direction,
	duration,
	easing,
	excludes,
	includes,
	offset,
	position,
	type,
};

export default settings;
