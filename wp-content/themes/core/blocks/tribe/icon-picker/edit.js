import { __ } from '@wordpress/i18n';
import { useState, useEffect, useMemo } from '@wordpress/element';
import {
	PanelRow,
	TextControl,
	ToggleControl,
	RangeControl,
	TabPanel,
} from '@wordpress/components';
import {
	useBlockProps,
	InspectorControls,
	useSetting, // This is only needed if you want to use theme.json color palette.
} from '@wordpress/block-editor';
import { formatIconName } from './utils';
import './editor.pcss';

import { ICONS_LIST } from './icons/icons-list';

export default function Edit( { attributes, setAttributes } ) {
	const {
		selectedIcon,
		isRounded,
		iconPadding,
		iconLabel,
		iconSize,
		searchQuery,
		selectedIconColor,
		selectedBgColor,
	} = attributes;

	const sortedIcons = useMemo(
		() =>
			[ ...ICONS_LIST ].sort( ( a, b ) => a.key.localeCompare( b.key ) ),
		[]
	);
	const [ filteredIcons, setFilteredIcons ] = useState( sortedIcons );

	const SelectedIconComponent =
		ICONS_LIST.find( ( icon ) => icon.key === selectedIcon )?.component ||
		null;

	// Option 1: Use theme.json color palette
	const themeColors = useSetting( 'color.palette' );
	const COLORS = Array.isArray( themeColors )
		? [
				...themeColors.map( ( { name, color } ) => ( {
					name,
					value: color,
				} ) ),
				{ name: __( 'Transparent', 'tribe' ), value: 'transparent' },
		  ]
		: [];

	//Option 2: Use custom collors
	// const COLORS = [
	// 	{ name: __( 'Blue', 'tribe' ), value: '#0078d4' },
	// 	{ name: __( 'Purple', 'tribe' ), value: '#8661c5' },
	// 	{ name: __( 'Gray', 'tribe' ), value: '#737373' },
	// 	{ name: __( 'Light Gray', 'tribe' ), value: '#d2d2d2' },
	// 	{ name: __( 'Dark Gray', 'tribe' ), value: '#505050' },
	// 	{ name: __( 'Teal', 'tribe' ), value: '#008575' },
	// 	{ name: __( 'White', 'tribe' ), value: '#ffffff' },
	// 	{ name: __( 'Transparent', 'tribe' ), value: 'transparent' },
	// ];

	// Warn developer if themeColors is not properly set
	if ( COLORS.length === 0 ) {
		console.error(
			'[tribe/icon-picker] No colors found. ' +
				'Ensure your theme defines `settings.color.palette` in theme.json, or switch to a custom color list.'
		);
	}

	// Ensure selectedIcon is valid
	const validIcon =
		ICONS_LIST.find( ( { key } ) => key === selectedIcon ) || null;

	// Filter icons based on search query
	useEffect( () => {
		setFilteredIcons(
			sortedIcons.filter( ( { key } ) =>
				key.toLowerCase().includes( searchQuery.toLowerCase() )
			)
		);
	}, [ searchQuery, sortedIcons ] );

	return (
		<div { ...useBlockProps() }>
			{ validIcon ? (
				<div
					className="icon-wrapper"
					style={ {
						'--icon-picker--background-color':
							selectedBgColor || 'transparent',
						'--icon-picker--icon-color':
							selectedIconColor || 'white',
						'--icon-picker--border-radius': isRounded ? '50%' : '0',
						'--icon-picker--icon-size': `${ iconSize }px` || '100%',
						'--icon-picker--icon-padding': `${ iconPadding }px`,
					} }
					{ ...( selectedBgColor === 'transparent'
						? { 'data-transparent': true }
						: {} ) }
				>
					<SelectedIconComponent
						style={ { color: selectedIconColor } }
						aria-label={
							iconLabel || formatIconName( validIcon.name )
						}
					/>
				</div>
			) : (
				__( 'No Icon Selected', 'tribe' )
			) }

			<InspectorControls>
				<PanelRow className="controls-tribe-icon-picker icon-preview">
					{ validIcon ? (
						<>
							<div
								className="icon-image"
								style={ {
									backgroundColor:
										selectedBgColor || 'transparent',
									color: selectedIconColor || 'white',
									borderRadius: isRounded ? '50%' : '0',
								} }
							>
								<SelectedIconComponent
									style={ { color: selectedIconColor } }
								/>
							</div>
							<p className="icon-name">
								{ formatIconName( validIcon.name ) }
							</p>
						</>
					) : (
						__( 'No Icon Selected', 'tribe' )
					) }
				</PanelRow>

				<div className="controls-tribe-icon-picker">
					<TabPanel
						className="tribe-icon-picker-tab-panel"
						activeClass="active-tab"
						tabs={ [
							{
								name: 'icon',
								title: __( 'Icon', 'tribe' ),
							},
							{
								name: 'colors',
								title: __( 'Colors', 'tribe' ),
							},
							{
								name: 'dimensions',
								title: __( 'Dimensions', 'tribe' ),
							},
						] }
					>
						{ ( tab ) => {
							if ( tab.name === 'icon' ) {
								return (
									<>
										<div className="icon-picker">
											<TextControl
												label={ __(
													'Search Icons',
													'tribe'
												) }
												value={ searchQuery }
												onChange={ ( value ) =>
													setAttributes( {
														searchQuery: value,
													} )
												}
											/>

											<div className="icon-grid">
												{ filteredIcons.map(
													( {
														key,
														component:
															IconComponent,
														name,
													} ) => {
														if ( ! IconComponent ) {
															return null;
														}

														return (
															<div
																key={ key }
																className={ `icon-item ${
																	selectedIcon ===
																	key
																		? 'selected'
																		: ''
																}` }
																role="button"
																tabIndex={ 0 }
																onClick={ () => {
																	setAttributes(
																		{
																			selectedIcon:
																				key,
																		}
																	);
																} }
																onKeyDown={ (
																	e
																) => {
																	if (
																		e.key ===
																			'Enter' ||
																		e.key ===
																			' '
																	) {
																		setAttributes(
																			{
																				selectedIcon:
																					key,
																			}
																		);
																	}
																} }
															>
																<IconComponent
																	title={ formatIconName(
																		name
																	) }
																/>
															</div>
														);
													}
												) }
											</div>

											<TextControl
												label={ __(
													'Custom label',
													'tribe'
												) }
												value={ iconLabel }
												onChange={ ( value ) =>
													setAttributes( {
														iconLabel: value,
													} )
												}
												help={ __(
													'Add a custom label to describe the icon to help screen reader users.',
													'tribe'
												) }
											/>
										</div>
									</>
								);
							}

							if ( tab.name === 'colors' ) {
								return (
									<>
										<h4 style={ { margin: '0 0 6px' } }>
											{ __( 'Icon color', 'tribe' ) }
										</h4>
										<div className="color-picker">
											<div className="color-grid">
												{ COLORS.filter(
													( colorObj ) =>
														colorObj.value !==
														'transparent'
												).map( ( colorObj ) => (
													<div
														key={ colorObj.value }
														role="button"
														title={ colorObj.name }
														tabIndex={ 0 }
														className={ `color-item ${
															selectedIconColor ===
															colorObj.value
																? 'selected'
																: ''
														}` }
														style={ {
															backgroundColor:
																colorObj.value,
														} }
														onClick={ () => {
															setAttributes( {
																selectedIconColor:
																	colorObj.value,
															} );
														} }
														onKeyDown={ ( e ) => {
															if (
																e.key ===
																	'Enter' ||
																e.key === ' '
															) {
																setAttributes( {
																	selectedIconColor:
																		colorObj.value,
																} );
															}
														} }
														data-color={ colorObj.name
															.toLowerCase()
															.replace(
																/ /g,
																'-'
															) }
													></div>
												) ) }
											</div>
										</div>

										<h4 style={ { margin: '20px 0 6px' } }>
											{ __(
												'Background Color',
												'tribe'
											) }
										</h4>
										<div className="color-picker">
											<div className="color-grid">
												{ COLORS.map( ( colorObj ) => (
													<div
														key={ colorObj.value }
														role="button"
														title={ colorObj.name }
														tabIndex={ 0 }
														className={ `color-item ${
															selectedBgColor ===
															colorObj.value
																? 'selected'
																: ''
														}` }
														style={ {
															backgroundColor:
																colorObj.value,
														} }
														onClick={ () => {
															setAttributes( {
																selectedBgColor:
																	colorObj.value,
															} );
														} }
														onKeyDown={ ( e ) => {
															if (
																e.key ===
																	'Enter' ||
																e.key === ' '
															) {
																setAttributes( {
																	selectedBgColor:
																		colorObj.value,
																} );
															}
														} }
														data-color={ colorObj.name
															.toLowerCase()
															.replace(
																/ /g,
																'-'
															) }
													></div>
												) ) }
											</div>
										</div>
									</>
								);
							}

							if ( tab.name === 'dimensions' ) {
								return (
									<>
										<RangeControl
											label={ __(
												'Container Padding',
												'tribe'
											) }
											value={ iconPadding }
											onChange={ ( value ) =>
												setAttributes( {
													iconPadding: value,
												} )
											}
											min={ 0 }
											max={ 150 }
											afterIcon={ () => <span>px</span> }
										/>
										<RangeControl
											label={ __(
												'Container Size',
												'tribe'
											) }
											value={ iconSize }
											onChange={ ( value ) =>
												setAttributes( {
													iconSize: value,
												} )
											}
											min={ 20 }
											max={ 300 }
											step={ 1 }
											afterIcon={ () => <span>px</span> }
										/>
										<ToggleControl
											label={ __(
												'Rounded Icon',
												'tribe'
											) }
											checked={ isRounded }
											onChange={ ( value ) =>
												setAttributes( {
													isRounded: value,
												} )
											}
										/>
									</>
								);
							}
						} }
					</TabPanel>
				</div>
			</InspectorControls>
		</div>
	);
}
