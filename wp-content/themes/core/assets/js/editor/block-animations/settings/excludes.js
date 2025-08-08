/**
 * @module excludes
 *
 * @description pulls animation excludes settings from theme.json or sets default settings
 *
 * theme.json settings:
 *
 * "animationExcludes": [
 * 		"core/group",
 * 		"core/heading"
 * ],
 */

import themeJson from '../../../../../theme.json';

const excludes = themeJson.settings.animationExcludes ?? [];

export default excludes;
