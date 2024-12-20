/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Server-side rendering of the block in the editor view
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-server-side-render/
 */
import ServerSideRender from '@wordpress/server-side-render';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @ignore
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, isSelected, setAttributes } ) {
	const blockProps = useBlockProps();
	const { showStartingYear, startingYear, copyrightText } = attributes;
	const currentYear = new Date().getFullYear().toString();

	let displayDate;
	let title;

	if ( showStartingYear && startingYear ) {
		displayDate = startingYear + '–' + currentYear;
	} else {
		displayDate = currentYear;
	}

	title = copyrightText ?? wp.data.select( 'core' ).getSite().title;

	return (
		<div { ...blockProps }>
			{ isSelected && (
				<>
					<InspectorControls>
						<PanelBody title={ __( 'Block Settings', 'tribe' ) }>
							<TextControl
								label={ __( 'Copyright Text', 'tribe' ) }
								value={ title }
								onChange={ ( value ) =>
									setAttributes( { copyrightText: value } )
								}
							/>
							<ToggleControl
								checked={ !! showStartingYear }
								label={ __( 'Show starting year', 'tribe' ) }
								onChange={ () =>
									setAttributes( {
										showStartingYear: ! showStartingYear,
									} )
								}
							/>
							{ showStartingYear && (
								<TextControl
									__nextHasNoMarginBottom
									__next40pxDefaultSize
									label={ __( 'Starting year', 'tribe' ) }
									value={ startingYear || '' }
									onChange={ ( value ) =>
										setAttributes( { startingYear: value } )
									}
								/>
							) }
						</PanelBody>
					</InspectorControls>
				</>
			) }
			<p>
				{ __( 'Copyright', 'tribe' ) } © { displayDate } { title }
			</p>
		</div>
	);
}
