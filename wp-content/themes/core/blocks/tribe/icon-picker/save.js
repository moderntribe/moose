import { useBlockProps } from '@wordpress/block-editor';
import { ICONS_LIST } from './icons';

export default function Save( { attributes } ) {
	const { selectedIcon, selectedIconColor, selectedBgColor } = attributes;

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
						backgroundColor: selectedBgColor || 'transparent',
						color: selectedIconColor || 'white',
					} }
					{ ...( selectedBgColor === 'transparent'
						? { 'data-transparent': true }
						: {} ) }
				>
					<span className="ms-icon" aria-hidden="true">
						{ iconCharacter }
					</span>
				</div>
			) }
		</div>
	);
}
