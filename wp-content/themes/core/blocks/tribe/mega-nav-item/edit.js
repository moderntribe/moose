import { __ } from '@wordpress/i18n';
import { InnerBlocks, useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';

import './editor.pcss';

const TEMPLATE = [
	[
		'core/pattern',
		{
			"slug": "patterns/enhanced-nav-menu"
		}
	]
]

export default function Edit( { attributes, setAttributes, isSelected } ) {
	const blockProps = useBlockProps();

	const { menuToggleLabel } = attributes;

	return (
		<div { ...blockProps }>
			{ menuToggleLabel ? <button type="button" className="tribe-mega-menu-item__toggle" data-js="menu-menu-toggle">{ menuToggleLabel }</button> : '' }
			<div className="site-header__mega-menu-item-wrapper">
				<InnerBlocks template={ TEMPLATE } />
			</div>
			{ isSelected && (
				<InspectorControls>
					<PanelBody title={ __( 'Block Settings', 'tribe' ) }>
						<TextControl
							label={ __( 'Mega Menu Item Label', 'tribe' ) }
							value={ menuToggleLabel }
							help={ __(
								'Text label for top level navigation item. Used to toggle the associated mega menu.',
								'tribe'
							) }
							placeholder={ __(
								'Menu Label',
								'tribe'
							) }
							onChange={ ( value ) =>
								setAttributes( { menuToggleLabel: value } )
							}
						/>
					</PanelBody>
				</InspectorControls>
			) }
		</div>
	);
}
