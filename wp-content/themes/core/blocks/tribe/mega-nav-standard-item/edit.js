import { __ } from '@wordpress/i18n';
import {
	InnerBlocks,
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';

import './editor.pcss';

const TEMPLATE = [ [ 'tribe/navigation-link' ] ];

export default function Edit( { attributes, setAttributes, isSelected } ) {
	const blockProps = useBlockProps();

	const { menuToggleLabel, hasSubMenu } = attributes;

	return (
		<div { ...blockProps }>
			{ menuToggleLabel ? (
				<button type="button" className="tribe-mega-menu-item__toggle">
					{ menuToggleLabel }
				</button>
			) : (
				''
			) }
			<InnerBlocks template={ TEMPLATE } />
			<InspectorControls>
				<PanelBody title={ __( 'Block Settings', 'tribe' ) }>
					<ToggleControl
						label={ __( 'Use submenu', 'tribe' ) }
						checked={ !! hasSubMenu }
						help={ __(
							'Use this menu item as a parent toggle for a set of nested submenu links. Allowed Blocks: Navigation Link',
							'tribe'
						) }
						onChange={ ( value ) =>
							setAttributes( {
								hasSubMenu: value,
								menuToggleLabel: ! hasSubMenu
									? menuToggleLabel
									: '',
							} )
						}
					/>
					{ isSelected && hasSubMenu && (
						<TextControl
							label={ __( 'Menu Item Toggle Label', 'tribe' ) }
							value={ hasSubMenu && menuToggleLabel }
							help={ __(
								'Text label for top level navigation item. Used to toggle the submenu.',
								'tribe'
							) }
							placeholder={ __( 'Menu Label', 'tribe' ) }
							onChange={ ( value ) =>
								setAttributes( { menuToggleLabel: value } )
							}
						/>
					) }
				</PanelBody>
			</InspectorControls>
		</div>
	);
}
