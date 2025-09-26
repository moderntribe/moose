import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

import './editor.pcss';

export default function Edit( { attributes, setAttributes, isSelected } ) {
	const blockProps = useBlockProps();

	// const { exampleTextControl } = attributes;

	return (
		<div { ...blockProps }>
			<ServerSideRender
				block="tribe/image-card"
				attributes={ attributes }
			/>
			{ isSelected && (
				<InspectorControls>
					<PanelBody
						title={ __( 'Block Settings', 'tribe' ) }
					></PanelBody>
				</InspectorControls>
			) }
		</div>
	);
}
