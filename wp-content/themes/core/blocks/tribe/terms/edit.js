/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';

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
export default function Edit( {
	attributes,
	setAttributes,
	context,
	isSelected,
} ) {
	const blockProps = useBlockProps();
	const { taxonomyToUse, onlyPrimaryTerm, hasLinks } = attributes;
	const { postType } = context;

	const taxonomies = useSelect( ( select ) =>
		select( 'core' ).getTaxonomies( {
			type: postType,
			per_page: -1,
		} )
	);

	return (
		<div { ...blockProps }>
			<ServerSideRender block="tribe/terms" attributes={ attributes } />
			{ isSelected && (
				<InspectorControls>
					<PanelBody title={ __( 'Block Settings', 'tribe' ) }>
						<SelectControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Taxonomy', 'tribe' ) }
							value={ taxonomyToUse ?? '' }
							help={ __(
								'The taxonomy to pull terms from.',
								'tribe'
							) }
							onChange={ ( newValue ) => {
								setAttributes( {
									taxonomyToUse: newValue,
								} );
							} }
							options={
								taxonomies
									? taxonomies.map( ( taxonomy ) => {
											return {
												label: taxonomy.name,
												value: taxonomy.slug,
											};
									  } )
									: []
							}
						/>
						<ToggleControl
							__nextHasNoMarginBottom
							label={ __(
								'Should only grab the primary term',
								'tribe'
							) }
							help={ __(
								'Default functionality is to grab all terms in the taxonomy.',
								'tribe'
							) }
							checked={ !! onlyPrimaryTerm }
							onChange={ ( newValue ) =>
								setAttributes( {
									onlyPrimaryTerm: newValue,
								} )
							}
						/>
						<ToggleControl
							__nextHasNoMarginBottom
							label={ __(
								'Terms should display as links',
								'tribe'
							) }
							help={ __(
								'Default functionality is that terms do not display as links.',
								'tribe'
							) }
							checked={ !! hasLinks }
							onChange={ ( newValue ) =>
								setAttributes( {
									hasLinks: newValue,
								} )
							}
						/>
					</PanelBody>
				</InspectorControls>
			) }
		</div>
	);
}
