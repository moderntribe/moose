/**
 * Add a toggle setting to image-based blocks to allow editors to control lazy-loading.
 */

/**
 * WordPress Dependencies
 */
const { __ } = wp.i18n;
const { addFilter } = wp.hooks;
const { Fragment, RawHTML } = wp.element;
const { InspectorAdvancedControls } = wp.editor;
const { createHigherOrderComponent } = wp.compose;
const { ToggleControl } = wp.components;

// Restrict this feature to specific blocks
const allowedBlocks = [ 'core/image', 'core/cover' ];

/**
 * Add custom attribute for lazy-loading images.
 *
 * @param {Object} settings Settings for the block.
 *
 * @return {Object} settings Modified settings.
 */
function addLazyloadAttribute( settings ) {
	settings.attributes = Object.assign( settings.attributes, {
		lazyload: {
			type: 'boolean',
			default: false,
		},
	} );

	return settings;
}

/**
 * Add lazyload control on in the block inspector/editor panel.
 *
 * @param {Function} BlockEdit Block edit component.
 *
 * @return {Function} BlockEdit Modified block edit component.
 */
const withLazyloadControl = createHigherOrderComponent( ( BlockEdit ) => {
	return ( props ) => {
		const { name, attributes, setAttributes, isSelected } = props;

		const { lazyload } = attributes;

		return (
			<Fragment>
				<BlockEdit { ...props } />
				{ isSelected && allowedBlocks.includes( name ) && (
					<InspectorAdvancedControls>
						<ToggleControl
							label={ __( 'Lazy-load image?' ) }
							checked={ !! lazyload }
							onChange={ () =>
								setAttributes( {
									lazyload: ! lazyload,
								} )
							}
							help={
								!! lazyload
									? __( 'Image is lazy-loaded.', 'tribe' )
									: __( 'Image is not lazy-loaded.', 'tribe' )
							}
						/>
					</InspectorAdvancedControls>
				) }
			</Fragment>
		);
	};
}, 'withAdvancedControls' );

/**
 * Inject custom props to a block on save.
 *
 * @param {Object} element    Block element.
 * @param {Object} blockType  Blocks object.
 * @param {Object} attributes Blocks attributes.
 *
 * @return {Object} element Modified block element.
 */
function withLazyloadProps( element, blockType, attributes ) {
	const { lazyload } = attributes;

	if ( ! allowedBlocks.includes( blockType.name ) || ! lazyload ) {
		return element;
	}

	const customProps = [
		{
			attribute: 'loading',
			value: 'lazy',
		},
	];

	let elementAsString = wp.element.renderToString( element );

	customProps.forEach( ( { attribute, value } ) => {
		elementAsString = elementAsString.replace(
			'<img ',
			`<img ${ attribute }="${ value }" `
		);
	} );

	return <RawHTML>{ elementAsString }</RawHTML>;
}

addFilter(
	'blocks.registerBlockType',
	'tribe/add-lazyload-attribute',
	addLazyloadAttribute
);

addFilter(
	'editor.BlockEdit',
	'tribe/add-lazyload-control',
	withLazyloadControl
);

addFilter(
	'blocks.getSaveElement',
	'tribe/inject-lazyload-attribute',
	withLazyloadProps
);
