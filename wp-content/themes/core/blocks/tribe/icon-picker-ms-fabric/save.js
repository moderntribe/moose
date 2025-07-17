import { useBlockProps } from '@wordpress/block-editor';
import { ICONS_LIST } from './icons';

export default function Save( { attributes } ) {
	const {
		selectedIcon,
		isRounded,
		iconPadding,
		iconSize,
		selectedIconColor,
		selectedBgColor,
	} = attributes;

	// Ensure selectedIcon is valid and retrieve its Unicode value
	const iconObj = ICONS_LIST.find( ( icon ) => icon.name === selectedIcon );
	const iconCharacter = iconObj
		? String.fromCharCode( parseInt( iconObj.unicode, 16 ) )
		: null;

	return (
		<div { ...useBlockProps.save() }>
			{ iconCharacter && (
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
					<span className="ms-icon" aria-label={ iconObj.name }>
						{ iconCharacter }
					</span>
				</div>
			) }
		</div>
	);
}
