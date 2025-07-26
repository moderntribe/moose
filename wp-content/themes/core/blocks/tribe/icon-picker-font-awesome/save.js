import { useBlockProps } from '@wordpress/block-editor';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import * as solidIcons from '@fortawesome/free-solid-svg-icons';
import { formatIconName } from './utils';

export default function Save( { attributes } ) {
	const {
		selectedIcon,
		iconLabel,
		isRounded,
		iconPadding,
		iconSize,
		selectedIconColor,
		selectedBgColor,
	} = attributes;

	// Ensure selectedIcon is valid and retrieve its Unicode value
	const icon = solidIcons[ selectedIcon ];
	const label = iconLabel || ( icon && formatIconName( icon.iconName ) );

	return (
		<div { ...useBlockProps.save() }>
			{ icon && (
				<div
					className="icon-wrapper"
					style={ {
						'--icon-picker--background-color':
							selectedBgColor || 'transparent',
						'--icon-picker--icon-color':
							selectedIconColor || 'white',
						'--icon-picker--border-radius': isRounded ? '50%' : '0',
						'--icon-picker--icon-size': `${ iconSize }px` || '100%',
						'--icon-picker--icon-padding': `${ iconPadding }px`,
					} }
					{ ...( selectedBgColor === 'transparent'
						? { 'data-transparent': true }
						: {} ) }
				>
					<FontAwesomeIcon
						icon={ icon }
						aria-label={ label }
						style={ { color: selectedIconColor } }
					/>
				</div>
			) }
		</div>
	);
}
