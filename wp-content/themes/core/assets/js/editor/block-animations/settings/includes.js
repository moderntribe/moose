/**
 * @module includes
 *
 * @description pulls animation includes settings from theme.json or sets default settings
 *
 * theme.json settings:
 *
 * "animationIncludes": [
 * 		"core/group",
 * 		"core/heading"
 * ],
 */

import themeJson from '../../../../../theme.json';

const includes = themeJson.settings.animationIncludes ?? [];

export default includes;
