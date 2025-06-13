import { useBlockProps } from '@wordpress/block-editor';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import * as solidIcons from '@fortawesome/free-solid-svg-icons';

export default function Save( { attributes } ) {
	const {
		selectedIcon,
		iconLabel,
		isRounded,
		iconPadding,
		iconWidth,
		selectedIconColor,
		selectedBgColor,
	} = attributes;

	// Ensure selectedIcon is valid and retrieve its Unicode value
	const icon = solidIcons[ selectedIcon ];
	const label = iconLabel || ( icon && icon.iconName );

	return (
		<div { ...useBlockProps.save() }>
			{ icon && (
				<div
					className="icon-wrapper"
					style={ {
						backgroundColor: selectedBgColor || 'transparent',
						color: selectedIconColor || 'white',
						borderRadius: isRounded ? '50%' : '0',
						width: iconWidth || '100%',
						height: iconWidth || '100%',
						padding: `${ iconPadding }px`,
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
