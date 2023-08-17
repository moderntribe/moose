/**
 * @module block-animation
 *
 * @description handles setting up animation settings for blocks
 *
 * theme.json settings:
 * "animation": [
 * 		{ "label": "Test", "value": "test" },
 * 		{ "label": "Test 2", "value": "test-2" }
 * ],
 * "animationSpeeds": [
 * 		{ "label": "Fast", "value": "0.2s" },
 * 		{ "label": "Slow", "value": "0.8s" }
 * ],
 * "animationDelays": [
 * 		{ "label": "Short", "value": "0.2s" },
 * 		{ "label": "Long", "value": "0.8s" }
 * ],
 * "animationEasings": [
 * 		{ "label": "Ease In", "value": "ease-in" },
 * 		{ "label": "Ease Out", "value": "ease-out" }
 * ],
 * "animationIncludes": [
 * 		"core/group",
 * 		"core/heading"
 * ],
 * "animationExcludes": [
 * 		"core/group",
 * 		"core/heading"
 * ],
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl } from '@wordpress/components';
import { createHigherOrderComponent } from '@wordpress/compose';
import { Fragment } from '@wordpress/element';
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';
import themeJson from '../../../theme.json';

const state = {};

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
const applyAnimationProps = ( props, block, attributes ) => {
	// return default props if block isn't in the includes array
	if (
		state.includes.length > 0 &&
		! state.includes.includes( block.name )
	) {
		return props;
	}

	// return default props if block is in the excludes array
	if ( state.excludes.length > 0 && state.excludes.includes( block.name ) ) {
		return props;
	}

	const {
		animationStyle,
		animationSpeed,
		animationDelay,
		animationEasing,
		animationTrigger,
		animationPosition,
	} = attributes;

	if ( animationStyle === undefined || animationStyle === 'none' ) {
		return props;
	}

	props.className =
		animationPosition !== undefined && animationPosition
			? `${ props.className } tribe-animation-style-${ animationStyle } is-animated-on-scroll-full`
			: `${ props.className } tribe-animation-style-${ animationStyle } is-animated-on-scroll`;

	if ( animationSpeed !== undefined && animationSpeed ) {
		props.style = {
			...props.style,
			'--tribe-animation-speed': animationSpeed,
		};
	}

	if ( animationDelay !== undefined && animationDelay ) {
		props.style = {
			...props.style,
			'--tribe-animation-delay': animationDelay,
		};
	}

	if ( animationEasing !== undefined && animationEasing ) {
		props.style = {
			...props.style,
			'--tribe-animation-easing': animationEasing,
		};
	}

	if ( animationTrigger !== undefined && animationTrigger ) {
		props.className = `${ props.className } tribe-animate-multiple`;
	}

	return props;
};

/**
 * @function animationControls
 *
 * @description creates component that overrides the edit functionality of the block with new animation controls
 */
const animationControls = createHigherOrderComponent( ( BlockEdit ) => {
	return ( props ) => {
		const { attributes, setAttributes, isSelected, name } = props;

		// return default Edit function if block isn't in the includes array
		if ( state.includes.length > 0 && ! state.includes.includes( name ) ) {
			return <BlockEdit { ...props } />;
		}

		// return default Edit function if block is in the excludes array
		if ( state.excludes.length > 0 && state.excludes.includes( name ) ) {
			return <BlockEdit { ...props } />;
		}

		const {
			animationStyle,
			animationSpeed,
			animationDelay,
			animationEasing,
			animationTrigger,
			animationPosition,
		} = attributes;

		let blockClass =
			attributes.className !== undefined ? attributes.className : '';
		const blockStyles = { ...props.style };

		if ( animationStyle !== undefined && animationStyle !== 'none' ) {
			blockClass =
				animationPosition !== undefined && animationPosition
					? `${ blockClass } tribe-animation-style-${ animationStyle } is-animated-on-scroll-full`
					: `${ blockClass } tribe-animation-style-${ animationStyle } is-animated-on-scroll`;

			if ( animationSpeed !== undefined && animationSpeed ) {
				blockStyles[ '--tribe-animation-speed' ] = animationSpeed;
			}

			if ( animationDelay !== undefined && animationDelay ) {
				blockStyles[ '--tribe-animation-delay' ] = animationDelay;
			}

			if ( animationEasing !== undefined && animationEasing ) {
				blockStyles[ '--tribe-animation-easing' ] = animationEasing;
			}

			if ( animationTrigger !== undefined && animationTrigger ) {
				blockClass = `${ blockClass } tribe-animate-multiple`;
			}
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
					<InspectorControls>
						<PanelBody
							title={ __( 'Block Animations', 'tribe' ) }
							initialOpen={ false }
						>
							<SelectControl
								label={ __( 'Animation Style', 'tribe' ) }
								value={ animationStyle ?? 'none' }
								help={ __(
									'Animation style is the type of animation you want to display.',
									'tribe'
								) }
								onChange={ ( newValue ) => {
									setAttributes( {
										animationStyle: newValue,
									} );
								} }
								options={ state.animations }
							/>
							<SelectControl
								label={ __( 'Animation Speed', 'tribe' ) }
								value={ animationSpeed ?? '0.3s' }
								help={ __(
									'Animation speed is the speed at which the animation should run.'
								) }
								onChange={ ( newValue ) =>
									setAttributes( {
										animationSpeed: newValue,
									} )
								}
								options={ state.speed }
							/>
							<SelectControl
								label={ __( 'Animation Delay', 'tribe' ) }
								value={ animationDelay ?? '0s' }
								help={ __(
									'Animation delay adds extra time before the animation starts.',
									'tribe'
								) }
								onChange={ ( newValue ) =>
									setAttributes( {
										animationDelay: newValue,
									} )
								}
								options={ state.delays }
							/>
							<SelectControl
								label={ __( 'Animation Easing', 'tribe' ) }
								value={ animationEasing ?? 'ease' }
								help={ __(
									'Animation easing determines what easing function the animation should use.',
									'tribe'
								) }
								onChange={ ( newValue ) =>
									setAttributes( {
										animationEasing: newValue,
									} )
								}
								options={ state.easings }
							/>
							<ToggleControl
								label={ __(
									'Animation should trigger every time the element is in the viewport',
									'tribe'
								) }
								help={ __(
									'Default functionality is to trigger the animation once.',
									'tribe'
								) }
								checked={ !! animationTrigger }
								onChange={ ( newValue ) =>
									setAttributes( {
										animationTrigger: newValue,
									} )
								}
							/>
							<ToggleControl
								label={ __(
									'Animation should trigger when the element is completely in the viewport',
									'tribe'
								) }
								help={ __(
									'Default functionality is to trigger the animation when 25% of it is in the viewport.',
									'tribe'
								) }
								checked={ !! animationPosition }
								onChange={ ( newValue ) =>
									setAttributes( {
										animationPosition: newValue,
									} )
								}
							/>
						</PanelBody>
					</InspectorControls>
				) }
			</Fragment>
		);
	};
}, 'animationControls' );

/**
 * @function addAnimationAttributes
 *
 * @description add new attributes to blocks for animation settings
 *
 * @param {Object} settings
 * @param {string} name
 *
 * @return {Object} returns updates settings object
 */
const addAnimationAttributes = ( settings, name ) => {
	// return default settings if block isn't in the includes array
	if ( state.includes.length > 0 && ! state.includes.includes( name ) ) {
		return settings;
	}

	// return default settings if block is in the excludes array
	if ( state.excludes.length > 0 && state.excludes.includes( name ) ) {
		return settings;
	}

	if ( settings?.attributes !== undefined ) {
		settings.attributes = {
			...settings.attributes,
			animationStyle: {
				type: 'string',
			},
			animationSpeed: {
				type: 'string',
			},
			animationDelay: {
				type: 'string',
			},
			animationEasing: {
				type: 'string',
			},
			animationTrigger: {
				type: 'boolean',
			},
			animationPosition: {
				type: 'boolean',
			},
		};
	}

	return settings;
};

/**
 * @function registerFilters
 *
 * @description register block filters for adding animation controls
 */
const registerFilters = () => {
	addFilter(
		'blocks.registerBlockType',
		'tribe/add-animation-options',
		addAnimationAttributes
	);

	addFilter(
		'editor.BlockEdit',
		'tribe/animation-advanced-control',
		animationControls
	);

	addFilter(
		'blocks.getSaveContent.extraProps',
		'tribe/apply-animation-classes',
		applyAnimationProps
	);
};

/**
 * @function initializeSettings
 *
 * @description pull settings from theme.json or add default settings
 *
 * @todo work with design to handle defining these defaults
 * @todo do we need to handle translations if pulling from theme.json?
 */
const initializeSettings = () => {
	state.animations = themeJson?.settings?.animations ?? [
		{ label: __( 'None', 'tribe' ), value: 'none' },
		{ label: __( 'Fade In', 'tribe' ), value: 'fade-in' },
		{ label: __( 'Fade In Up', 'tribe' ), value: 'fade-in-up' },
		{ label: __( 'Fade In Down', 'tribe' ), value: 'fade-in-down' },
		{ label: __( 'Fade In Right', 'tribe' ), value: 'fade-in-right' },
		{ label: __( 'Fade In Left', 'tribe' ), value: 'fade-in-left' },
	];
	state.speeds = themeJson?.settings?.animationSpeeds ?? [
		{ label: __( 'Extra Slow', 'tribe' ), value: '0.7s' },
		{ label: __( 'Slow', 'tribe' ), value: '0.5s' },
		{ label: __( 'Normal', 'tribe' ), value: '0.3s' },
		{ label: __( 'Fast', 'tribe' ), value: '0.2s' },
		{ label: __( 'Extra Fast', 'tribe' ), value: '0.1s' },
	];
	state.delays = themeJson?.settings?.animationDelays ?? [
		{ label: __( 'None', 'tribe' ), value: '0s' },
		{ label: __( 'Extra Short', 'tribe' ), value: '0.1s' },
		{ label: __( 'Short', 'tribe' ), value: '0.2s' },
		{ label: __( 'Medium', 'tribe' ), value: '0.3s' },
		{ label: __( 'Long', 'tribe' ), value: '0.5s' },
		{ label: __( 'Extra Long', 'tribe' ), value: '0.7s' },
	];
	state.easings = themeJson?.settings?.animationEasings ?? [
		{ label: __( 'Ease', 'tribe' ), value: 'ease' },
		{ label: __( 'Ease In', 'tribe' ), value: 'ease-in' },
		{ label: __( 'Ease Out', 'tribe' ), value: 'ease-out' },
		{ label: __( 'Ease In Out', 'tribe' ), value: 'ease-in-out' },
		{ label: __( 'Linear', 'tribe' ), value: 'linear' },
	];
	state.includes = themeJson.settings.animationIncludes ?? [];
	state.excludes = themeJson.settings.animationExcludes ?? [];
};

/**
 * @function init
 *
 * @description initializes this modules functions
 */
const init = () => {
	// initialize settings
	initializeSettings();

	// handle registering block filters
	registerFilters();
};

export default init;
