import { __ } from '@wordpress/i18n';
import { useEffect, useMemo, useState } from '@wordpress/element';
import {
	RangeControl,
	TabPanel,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { useSettings } from '@wordpress/block-editor';
import { formatIconName } from './utils';
import { ICONS_LIST } from './icons/icons-list';

export default function IconPicker( {
	selectedIcon,
	isRounded,
	iconPadding,
	iconLabel,
	iconSize,
	searchQuery,
	selectedIconColor,
	selectedBgColor,
	onChange,
} ) {
	const sortedIcons = useMemo(
		() =>
			[ ...ICONS_LIST ].sort( ( a, b ) => a.key.localeCompare( b.key ) ),
		[]
	);
	const [ filteredIcons, setFilteredIcons ] = useState( sortedIcons );

	// Option 1: Use theme.json color palette
	const themeColors = useSettings( 'color.palette' );
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

	useEffect( () => {
		setFilteredIcons(
			sortedIcons.filter( ( { key } ) =>
				key
					.toLowerCase()
					.includes( ( searchQuery || '' ).toLowerCase() )
			)
		);
	}, [ searchQuery, sortedIcons ] );

	return (
		<TabPanel
			className="tribe-icon-picker-tab-panel"
			activeClass="active-tab"
			tabs={ [
				{ name: 'icon', title: __( 'Icon', 'tribe' ) },
				{ name: 'colors', title: __( 'Colors', 'tribe' ) },
				{ name: 'dimensions', title: __( 'Dimensions', 'tribe' ) },
			] }
		>
			{ ( tab ) => {
				if ( tab.name === 'icon' ) {
					return (
						<div className="icon-picker">
							<TextControl
								__next40pxDefaultSize
								__nextHasNoMarginBottom
								label={ __( 'Search Icons', 'tribe' ) }
								value={ searchQuery }
								onChange={ ( value ) =>
									onChange( { searchQuery: value } )
								}
							/>
							<div className="icon-grid">
								{ filteredIcons.map(
									( {
										key,
										component: IconComponent,
										name,
									} ) =>
										IconComponent && (
											<div
												key={ key }
												className={ `icon-item ${
													selectedIcon === key
														? 'selected'
														: ''
												}` }
												role="button"
												tabIndex={ 0 }
												onClick={ () =>
													onChange( {
														selectedIcon: key,
													} )
												}
												onKeyDown={ ( e ) => {
													if (
														e.key === 'Enter' ||
														e.key === ' '
													) {
														onChange( {
															selectedIcon: key,
														} );
													}
												} }
											>
												<IconComponent
													title={ formatIconName(
														name
													) }
												/>
											</div>
										)
								) }
							</div>
							<TextControl
								__next40pxDefaultSize
								__nextHasNoMarginBottom
								label={ __( 'Custom label', 'tribe' ) }
								value={ iconLabel }
								onChange={ ( value ) =>
									onChange( { iconLabel: value } )
								}
								help={ __(
									'Add a custom label to describe the icon to help screen reader users.',
									'tribe'
								) }
							/>
						</div>
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
										( c ) => c.value !== 'transparent'
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
												backgroundColor: colorObj.value,
											} }
											onClick={ () =>
												onChange( {
													selectedIconColor:
														colorObj.value,
												} )
											}
											onKeyDown={ ( e ) => {
												if (
													e.key === 'Enter' ||
													e.key === ' '
												) {
													onChange( {
														selectedIconColor:
															colorObj.value,
													} );
												}
											} }
											data-color={ colorObj.name
												.toLowerCase()
												.replace( / /g, '-' ) }
										></div>
									) ) }
								</div>
							</div>
							<h4 style={ { margin: '20px 0 6px' } }>
								{ __( 'Background Color', 'tribe' ) }
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
												backgroundColor: colorObj.value,
											} }
											onClick={ () =>
												onChange( {
													selectedBgColor:
														colorObj.value,
												} )
											}
											onKeyDown={ ( e ) => {
												if (
													e.key === 'Enter' ||
													e.key === ' '
												) {
													onChange( {
														selectedBgColor:
															colorObj.value,
													} );
												}
											} }
											data-color={ colorObj.name
												.toLowerCase()
												.replace( / /g, '-' ) }
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
								__next40pxDefaultSize
								__nextHasNoMarginBottom
								label={ __( 'Container Padding', 'tribe' ) }
								value={ iconPadding }
								onChange={ ( value ) =>
									onChange( { iconPadding: value } )
								}
								min={ 0 }
								max={ 150 }
								afterIcon={ () => <span>px</span> }
							/>
							<RangeControl
								__next40pxDefaultSize
								__nextHasNoMarginBottom
								label={ __( 'Container Size', 'tribe' ) }
								value={ iconSize }
								onChange={ ( value ) =>
									onChange( { iconSize: value } )
								}
								min={ 20 }
								max={ 300 }
								step={ 1 }
								afterIcon={ () => <span>px</span> }
							/>
							<ToggleControl
								__nextHasNoMarginBottom
								label={ __( 'Rounded Icon', 'tribe' ) }
								checked={ isRounded }
								onChange={ ( value ) =>
									onChange( { isRounded: value } )
								}
							/>
						</>
					);
				}
			} }
		</TabPanel>
	);
}
