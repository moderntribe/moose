import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
	const { ariaLabel } = attributes;
	const blockProps = useBlockProps();

	return (
		<>
			<InspectorControls>
				<PanelBody title="Accessibility" initialOpen={ true }>
					<TextControl
						label={ __( 'ARIA label', 'tribe' ) }
						value={ ariaLabel }
						onChange={ ( value ) =>
							setAttributes( { ariaLabel: value } )
						}
						help={ __(
							'Describes the purpose of this navigation for screen readers.',
							'tribe'
						) }
					/>
				</PanelBody>
			</InspectorControls>

			<nav { ...blockProps }>
				<InnerBlocks />
			</nav>
		</>
	);
}
