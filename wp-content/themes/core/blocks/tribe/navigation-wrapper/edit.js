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
						label="ARIA label"
						value={ ariaLabel }
						onChange={ ( value ) =>
							setAttributes( { ariaLabel: value } )
						}
						help="Describes the purpose of this navigation for screen readers."
					/>
				</PanelBody>
			</InspectorControls>

			<nav { ...blockProps }>
				<InnerBlocks />
			</nav>
		</>
	);
}
