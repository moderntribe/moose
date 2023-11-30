/**
 * @module stacking-order
 *
 * @description handles setting up stacking order settings for columns block
 */

import { InspectorAdvancedControls } from '@wordpress/block-editor';
// eslint-disable-next-line
import { __experimentalNumberControl as NumberControl } from '@wordpress/components';
import { createHigherOrderComponent } from '@wordpress/compose';
import { Fragment } from '@wordpress/element';
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

/**
 * @function applyAnimationProps
 *
 * @description updates props on the block with new animation settings
 *
 * @param {Object} props
 * @param {Object} block
 * @param {Object} attributes
 *
 * @return {Object} updated props object
 */
const applyStackingOrderProps = ( props, block, attributes ) => {
	// return default props if block isn't in the includes array
	if ( block.name !== 'core/column' ) {
		return props;
	}

	const { stackingOrder } = attributes;

	if ( stackingOrder === undefined || stackingOrder === 0 ) {
		return props;
	}

	props.className = `${ props.className } tribe-has-stacking-order`;
	props.style = {
		...props.style,
		'--tribe-stacking-order': stackingOrder,
	};

	return props;
};

/**
 * @function stackingOrderControls
 *
 * @description creates component that overrides the edit functionality of the block with new stacking order controls
 */
const stackingOrderControls = createHigherOrderComponent( ( BlockEdit ) => {
	return ( props ) => {
		const { attributes, setAttributes, isSelected, name } = props;

		// return default Edit function if block isn't a column block
		if ( name !== 'core/column' ) {
			return <BlockEdit { ...props } />;
		}

		const { stackingOrder } = attributes;

		let blockClass =
			attributes.className !== undefined
				? attributes.className
				: 'wp-block-column';
		const blockStyles = { ...props.style };

		if ( stackingOrder !== undefined && stackingOrder !== 0 ) {
			blockClass = `${ blockClass } tribe-has-stacking-order`;
			blockStyles[ '--tribe-stacking-order' ] = stackingOrder;
		}

		const blockProps = {
			...props,
			attributes: {
				...attributes,
				className: blockClass,
			},
			style: blockStyles,
		};

		return (
			<Fragment>
				<BlockEdit { ...blockProps } />
				{ isSelected && (
					<InspectorAdvancedControls>
						<NumberControl
							label={ __( 'Stacking Order', 'tribe' ) }
							value={ stackingOrder ?? 0 }
							help={ __(
								'The stacking order of the element at mobile breakpoints. This setting only applies if the "Stack on mobile" setting for the Columns block is turned on.',
								'tribe'
							) }
							onChange={ ( newValue ) => {
								setAttributes( {
									stackingOrder: newValue,
								} );
							} }
							min={ 0 }
							isShiftStepEnabled={ false }
						/>
					</InspectorAdvancedControls>
				) }
			</Fragment>
		);
	};
}, 'stackingOrderControls' );

/**
 * @function addStackingOrderAttributes
 *
 * @description add new attributes to blocks for stacking order settings
 *
 * @param {Object} settings
 * @param {string} name
 *
 * @return {Object} returns updates settings object
 */
const addStackingOrderAttributes = ( settings, name ) => {
	// return default settings if block isn't a column block
	if ( name !== 'core/column' ) {
		return settings;
	}

	if ( settings?.attributes !== undefined ) {
		settings.attributes = {
			...settings.attributes,
			stackingOrder: {
				type: 'string',
			},
		};
	}

	return settings;
};

// register block filters for adding stacking order controls
addFilter(
	'blocks.registerBlockType',
	'tribe/add-stacking-order-options',
	addStackingOrderAttributes
);

addFilter(
	'editor.BlockEdit',
	'tribe/stacking-order-advanced-control',
	stackingOrderControls
);

addFilter(
	'blocks.getSaveContent.extraProps',
	'tribe/apply-stacking-order-classes',
	applyStackingOrderProps
);
