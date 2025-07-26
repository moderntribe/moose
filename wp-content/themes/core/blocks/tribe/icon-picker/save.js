import { useBlockProps } from '@wordpress/block-editor';
import { formatIconName } from './utils';
import { ICONS_LIST } from './icons-list';

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
	const iconEntry = ICONS_LIST.find( ( { key } ) => key === selectedIcon );
	const IconComponent = iconEntry?.component || null;
	const label =
		iconLabel || ( iconEntry && formatIconName( iconEntry.name ) );

	return (
		<div { ...useBlockProps.save() }>
			{ IconComponent && (
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
					<IconComponent
						aria-label={ label }
						style={ { color: selectedIconColor } }
					/>
				</div>
			) }
		</div>
	);
}
