/**
 * @module block-animation
 *
 * @description handles setting up animation settings for blocks
 *
 * theme.json settings:
 * "animationType": [
 * 		{ "label": "None", "value": "none" },
 * 		{ "label": "Fade In", "value": "fade-in" }
 * ],
 * "animationDirection": {
 * 		"fade-in": [
 * 			{ "label": "Top", "value": "top" },
 * 			{ "label": "Bottom", "value": "bottom" }
 * 		]
 * },
 * "animationDuration": [
 * 		{ "label": "200ms", "value": "0.2s" },
 * 		{ "label": "800ms", "value": "0.8s" }
 * ],
 * "offsetDistance": {
 * 		"0.2s": "20px",
 * 		"0.8s": "50px"
 * },
 * "animationDelay": [
 * 		{ "label": "0", "value": "0s" },
 * 		{ "label": "200ms", "value": "0.2s" },
 * 		{ "label": "800ms", "value": "0.8s" }
 * ],
 * "animationEasing": [
 * 		{ "label": "Ease In", "value": "ease-in" },
 * 		{ "label": "Ease Out", "value": "ease-out" }
 * ],
 * "animationPosition": [
 * 		{ "label": "25%", "value": "25" },
 * 		{ "label": "50%", "value": "50" },
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
import {
	Button,
	PanelBody,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';
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
		animationType,
		animationDirection,
		animationDuration,
		animationDelay,
		animationMobileDisableDelay,
		animationEasing,
		animationTrigger,
		animationPosition,
	} = attributes;

	if ( animationType === undefined || animationType === 'none' ) {
		return props;
	}

	if ( props.className === undefined ) {
		props.className = '';
	}

	props.className = `${
		props.className !== '' ? props.className + ' ' : ''
	} is-animated-on-scroll-${ animationPosition } tribe-animation-type-${ animationType } tribe-animation-direction-${ animationDirection }`;

	if ( animationDuration !== undefined && animationDuration ) {
		props.style = {
			...props.style,
			'--tribe-animation-speed': animationDuration,
			'--tribe-animation-offset':
				state.offsetDistance[ animationDuration ],
		};
	}

	if ( animationDelay !== undefined && animationDelay ) {
		props.style = {
			...props.style,
			'--tribe-animation-delay': animationDelay,
		};
	}

	if (
		animationMobileDisableDelay !== undefined &&
		animationMobileDisableDelay
	) {
		props.className = `${ props.className } tribe-animation-mobile-disable-delay`;
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
			animationType,
			animationDirection,
			showAdvancedControls,
			animationDuration,
			animationDelay,
			animationMobileDisableDelay,
			animationEasing,
			animationTrigger,
			animationPosition,
		} = attributes;

		let blockClass =
			attributes.className !== undefined ? attributes.className : '';
		const blockStyles = { ...props.style };

		if ( animationType !== undefined && animationType !== 'none' ) {
			// set block class for animation direction & animation position, if it's not set to the default
			blockClass = `${
				blockClass !== '' ? blockClass + ' ' : ''
			}is-animated-on-scroll-${ animationPosition } tribe-animation-type-${ animationType } tribe-animation-direction-${ animationDirection }`;

			// set block styles for animation duration
			if ( animationDuration !== undefined && animationDuration ) {
				blockStyles[ '--tribe-animation-speed' ] = animationDuration;

				blockStyles[ '--tribe-animation-offset' ] =
					state.offsetDistance[ animationDuration ];
			}

			// set block styles for animation delay
			if ( animationDelay !== undefined && animationDelay ) {
				blockStyles[ '--tribe-animation-delay' ] = animationDelay;
			}

			// set block class for disabling animation delays on mobile
			if (
				animationMobileDisableDelay !== undefined &&
				animationMobileDisableDelay
			) {
				blockClass = `${ blockClass } tribe-animation-mobile-disable-delay`;
			}

			// set block styles for animation easing
			if ( animationEasing !== undefined && animationEasing ) {
				blockStyles[ '--tribe-animation-easing' ] = animationEasing;
			}

			// set block class for triggering animation multiple times
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
								label={ __( 'Animation Type', 'tribe' ) }
								value={ animationType }
								help={ __(
									'Animation Type is the type of animation that should run.',
									'tribe'
								) }
								onChange={ ( newValue ) => {
									setAttributes( {
										animationType: newValue,
									} );
								} }
								options={ state.type }
							/>
							{ animationType === undefined ||
								( animationType !== 'none' && (
									<>
										<SelectControl
											label={ __(
												'Animation Direction',
												'tribe'
											) }
											value={ animationDirection }
											help={ __(
												'Animation direction is the direction you want the animation to run in.',
												'tribe'
											) }
											onChange={ ( newValue ) => {
												setAttributes( {
													animationDirection:
														newValue,
												} );
											} }
											options={
												state.direction[ animationType ]
											}
										/>
										<Button
											__next40pxDefaultSize={ true }
											text={
												showAdvancedControls
													? __(
															'Hide Advanced Controls',
															'tribe'
													  )
													: __(
															'Show Advanced Controls',
															'tribe'
													  )
											}
											variant="secondary"
											onClick={ () => {
												setAttributes( {
													showAdvancedControls:
														! showAdvancedControls,
												} );
											} }
										/>
										{ showAdvancedControls && (
											<div
												style={ { paddingTop: '16px' } }
											>
												<SelectControl
													label={ __(
														'Animation Duration',
														'tribe'
													) }
													value={ animationDuration }
													help={ __(
														'Animation duration is the speed at which the animation should run.'
													) }
													onChange={ ( newValue ) =>
														setAttributes( {
															animationDuration:
																newValue,
														} )
													}
													options={ state.duration }
												/>
												<SelectControl
													label={ __(
														'Animation Delay',
														'tribe'
													) }
													value={ animationDelay }
													help={ __(
														'Animation delay adds extra time before the animation starts.',
														'tribe'
													) }
													onChange={ ( newValue ) =>
														setAttributes( {
															animationDelay:
																newValue,
														} )
													}
													options={ state.delay }
												/>
												<ToggleControl
													label={ __(
														'Animation delays should be disabled on mobile.',
														'tribe'
													) }
													help={ __(
														"Default functionality will not disable animation delays on mobile. This feature is useful for animations that are delayed on desktop, but shouldn't be on mobile.",
														'tribe'
													) }
													checked={
														!! animationMobileDisableDelay
													}
													onChange={ ( newValue ) =>
														setAttributes( {
															animationMobileDisableDelay:
																newValue,
														} )
													}
												/>
												<SelectControl
													label={ __(
														'Animation Easing',
														'tribe'
													) }
													value={ animationEasing }
													help={ __(
														'Animation easing determines what easing function the animation should use.',
														'tribe'
													) }
													onChange={ ( newValue ) =>
														setAttributes( {
															animationEasing:
																newValue,
														} )
													}
													options={ state.easing }
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
													checked={
														!! animationTrigger
													}
													onChange={ ( newValue ) =>
														setAttributes( {
															animationTrigger:
																newValue,
														} )
													}
												/>
												<SelectControl
													label={ __(
														'Animation Trigger Position',
														'tribe'
													) }
													value={ animationPosition }
													help={ __(
														'Animation trigger position determines how much of the element should be in the viewport before the animation triggers.',
														'tribe'
													) }
													onChange={ ( newValue ) =>
														setAttributes( {
															animationPosition:
																newValue,
														} )
													}
													options={ state.position }
												/>
											</div>
										) }
									</>
								) ) }
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
			animationType: {
				type: 'string',
				default: 'none',
			},
			animationDirection: {
				type: 'string',
				default: 'bottom',
			},
			showAdvancedControls: {
				type: 'boolean',
				default: false,
			},
			animationDuration: {
				type: 'string',
				default: '0.6s',
			},
			animationDelay: {
				type: 'string',
				default: '0s',
			},
			animationMobileDisableDelay: {
				type: 'boolean',
				default: false,
			},
			animationEasing: {
				type: 'string',
				default: 'cubic-bezier(0.390, 0.575, 0.565, 1.000)',
			},
			animationTrigger: {
				type: 'boolean',
				default: false,
			},
			animationPosition: {
				type: 'string',
				default: '25',
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
	state.type = themeJson?.settings?.animationType ?? [
		{ label: __( 'None', 'tribe' ), value: 'none' },
		{ label: __( 'Fade In', 'tribe' ), value: 'fade-in' },
	];
	// direction is an object with keys for each animation type
	state.direction = themeJson?.settings?.animationDirection ?? {
		'fade-in': [
			{ label: __( 'Bottom', 'tribe' ), value: 'bottom' },
			{ label: __( 'Right', 'tribe' ), value: 'right' },
			{ label: __( 'Top Right', 'tribe' ), value: 'top-right' },
			{ label: __( 'Bottom Right', 'tribe' ), value: 'bottom-right' },
			{ label: __( 'Left', 'tribe' ), value: 'left' },
			{ label: __( 'Top Left', 'tribe' ), value: 'top-left' },
			{ label: __( 'Bottom Left', 'tribe' ), value: 'bottom-left' },
			{ label: __( 'Forward', 'tribe' ), value: 'forward' },
			{ label: __( 'Back', 'tribe' ), value: 'back' },
			{ label: __( 'Top', 'tribe' ), value: 'top' },
			{ label: __( 'Simple', 'tribe' ), value: 'simple' },
		],
	};
	state.duration = themeJson?.settings?.animationDuration ?? [
		{ label: __( '300ms', 'tribe' ), value: '0.3s' },
		{ label: __( '600ms', 'tribe' ), value: '0.6s' },
		{ label: __( '900ms', 'tribe' ), value: '0.9s' },
		{ label: __( '1200ms', 'tribe' ), value: '1.2s' },
		{ label: __( '1400ms', 'tribe' ), value: '1.4s' },
	];
	state.offsetDistance = themeJson?.settings?.offsetDistance ?? {
		'0.3s': '20px',
		'0.6s': '50px',
		'0.9s': '90px',
		'1.2s': '160px',
		'1.4s': '280px',
	};
	state.delay = themeJson?.settings?.animationDelay ?? [
		{ label: __( '0', 'tribe' ), value: '0s' },
		{ label: __( '300ms', 'tribe' ), value: '0.3s' },
		{ label: __( '600ms', 'tribe' ), value: '0.6s' },
		{ label: __( '900ms', 'tribe' ), value: '0.9s' },
		{ label: __( '1200ms', 'tribe' ), value: '1.2s' },
		{ label: __( '1500ms', 'tribe' ), value: '1.5s' },
	];
	state.easing = themeJson?.settings?.animationEasing ?? [
		{
			label: __( 'Ease Out Sine', 'tribe' ),
			value: 'cubic-bezier(0.390, 0.575, 0.565, 1.000)',
		},
		{
			label: __( 'Ease In Sine', 'tribe' ),
			value: 'cubic-bezier(0.470, 0.000, 0.745, 0.715)',
		},
		{
			label: __( 'Ease In Out Sine', 'tribe' ),
			value: 'cubic-bezier(0.445, 0.050, 0.550, 0.950)',
		},
		{
			label: __( 'Ease Out Quad', 'tribe' ),
			value: 'cubic-bezier(0.250, 0.460, 0.450, 0.940)',
		},
		{
			label: __( 'Ease In Quad', 'tribe' ),
			value: 'cubic-bezier(0.550, 0.085, 0.680, 0.530)',
		},
		{
			label: __( 'Ease In Out Quad', 'tribe' ),
			value: 'cubic-bezier(0.455, 0.030, 0.515, 0.955)',
		},
	];
	state.position = themeJson?.settings?.animationPosition ?? [
		{ label: __( '25%', 'tribe' ), value: '25' },
		{ label: __( '50%', 'tribe' ), value: '50' },
		{ label: __( '75%', 'tribe' ), value: '75' },
		{ label: __( '100%', 'tribe' ), value: '100' },
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
