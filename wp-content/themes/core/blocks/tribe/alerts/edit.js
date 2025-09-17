import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';

import './editor.pcss';

export default function Edit( { attributes, setAttributes, isSelected } ) {
	const blockProps = useBlockProps();

	const { exampleTextControl } = attributes;

	return (
		<div { ...blockProps }>
			<p>{ __( 'Alert – hello from the editor!', 'tribe' ) }</p>
			{ exampleTextControl ? <p>{ exampleTextControl }</p> : '' }
			{ isSelected && (
				<InspectorControls>
					<PanelBody title={ __( 'Block Settings', 'tribe' ) }>
						<TextControl
							label={ __( 'Example Text Control', 'tribe' ) }
							value={ exampleTextControl }
							help={ __(
								'Some helpful text describing the text control.',
								'tribe'
							) }
							placeholder={ __(
								'Helpful placeholder text',
								'tribe'
							) }
							onChange={ ( value ) =>
								setAttributes( { exampleTextControl: value } )
							}
						/>
					</PanelBody>
				</InspectorControls>
			) }
		</div>
	);
}
