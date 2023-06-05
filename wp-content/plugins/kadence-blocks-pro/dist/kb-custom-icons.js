/**
 * Filter in Custom Icon names
 *
 * @param {object} options the icon names
 * @returns {object} options the icon names.
 */
function kadenceAddCustomIconsName( options ) {
	if ( kadence_custom_icons && kadence_custom_icons.icon_names ) {
		if ( kadence_custom_icons.only_custom_sets || kadence_custom_icons.only_custom ) {
			options = kadence_custom_icons.icon_names.icon_cats;
		} else {
			options = { ...kadence_custom_icons.icon_names.icon_cats, ...options };
		}
	}
	return options;
}
wp.hooks.addFilter( 'kadence.icon_options_names', 'kadence_custom_icons/add_names_icons', kadenceAddCustomIconsName );
/**
 * Filter in Custom Icon svg content
 *
 * @param {object} options the icon options
 * @returns {object} options the icon options.
 */
function kadenceAddCustomIcons( options ) {
	if ( kadence_custom_icons && kadence_custom_icons.icons && kadence_custom_icons.icons.icons ) {
		if ( kadence_custom_icons.only_custom_sets || kadence_custom_icons.only_custom ) {
			options = kadence_custom_icons.icons.icons;
		} else {
			options = { ...kadence_custom_icons.icons.icons, ...options };
		}
	}
	return options;
}
wp.hooks.addFilter( 'kadence.icon_options', 'kadence_custom_icons/add_icons', kadenceAddCustomIcons );
