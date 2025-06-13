import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import {
	PanelBody,
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
import { ICONS_LIST } from './icons';
import './editor.pcss';

export default function Edit( { attributes, setAttributes } ) {
	const {
		selectedIcon,
		isRounded,
		iconPadding,
		iconLabel,
		iconWidth,
		searchQuery,
		selectedIconColor,
		selectedBgColor,
	} = attributes;
	const [ filteredIcons, setFilteredIcons ] = useState( ICONS_LIST );

	// Option 1: Use theme.json color palette
	// const themeColors = useSetting( 'color.palette' );
	// const COLORS = Array.isArray( themeColors )
	// 	? [
	// 			...themeColors.map( ( { name, color } ) => ( {
	// 				name,
	// 				value: color,
	// 			} ) ),
	// 			{ name: __( 'Transparent', 'tribe' ), value: 'transparent' },
	// 	  ]
	// 	: [];

	//Option 2: Use custom collors
	const COLORS = [
		{ name: __( 'Blue', 'tribe' ), value: '#0078d4' },
		{ name: __( 'Purple', 'tribe' ), value: '#8661c5' },
		{ name: __( 'Gray', 'tribe' ), value: '#737373' },
		{ name: __( 'Light Gray', 'tribe' ), value: '#d2d2d2' },
		{ name: __( 'Dark Gray', 'tribe' ), value: '#505050' },
		{ name: __( 'Teal', 'tribe' ), value: '#008575' },
		{ name: __( 'White', 'tribe' ), value: '#ffffff' },
		{ name: __( 'Transparent', 'tribe' ), value: 'transparent' },
	];

	// Warn developer if themeColors is not properly set
	if ( COLORS.length === 0 ) {
		console.error(
			'[tribe/icon-picker] No colors found. ' +
				'Ensure your theme defines `settings.color.palette` in theme.json, or switch to a custom color list.'
		);
	}

	// Ensure selectedIcon is valid
	const validIcon =
		ICONS_LIST.find( ( icon ) => icon.name === selectedIcon ) || null;

	// Filter icons based on search query
	useEffect( () => {
		setFilteredIcons(
			ICONS_LIST.filter( ( { name } ) =>
				name.toLowerCase().includes( searchQuery.toLowerCase() )
			)
		);
	}, [ searchQuery ] );

	return (
		<div { ...useBlockProps() }>
			{ validIcon ? (
				<div
					className="icon-wrapper"
					style={ {
						backgroundColor: selectedBgColor || 'transparent',
						color: selectedIconColor || 'white',
						borderRadius: isRounded ? '50%' : '0',
						width: iconWidth || '100%',
						height: iconWidth || '100%',
						padding: `${ iconPadding }px`,
					} }
					{ ...( selectedBgColor === 'transparent'
						? { 'data-transparent': true }
						: {} ) }
				>
					<span className="ms-icon">
						{ String.fromCharCode(
							parseInt( validIcon.unicode, 16 )
						) }
					</span>
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
								<span className="ms-icon">
									{ String.fromCharCode(
										parseInt( validIcon.unicode, 16 )
									) }
								</span>
							</div>
							<p className="icon-name">{ validIcon.name }</p>
						</>
					) : (
						__( 'No Icon Selected', 'tribe' )
					) }
				</PanelRow>

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
									<PanelBody
										title={ __( 'Icon', 'tribe' ) }
										className="controls-tribe-icon-picker"
										initialOpen={ true }
									>
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
													( { name, unicode } ) => (
														<div
															key={ name }
															className={ `icon-item ${
																selectedIcon ===
																name
																	? 'selected'
																	: ''
															}` }
															role="button"
															tabIndex={ 0 }
															onClick={ () =>
																setAttributes( {
																	selectedIcon:
																		name,
																} )
															}
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
																				name,
																		}
																	);
																}
															} }
														>
															<span
																className="ms-icon"
																style={ {
																	fontFamily:
																		'FabricMDL2Icons',
																} }
															>
																{ String.fromCharCode(
																	parseInt(
																		unicode,
																		16
																	)
																) }
															</span>
														</div>
													)
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
									</PanelBody>
								</>
							);
						}

						if ( tab.name === 'colors' ) {
							return (
								<>
									<PanelBody
										title={ __( 'Colors', 'tribe' ) }
										className="controls-tribe-icon-picker"
										initialOpen={ true }
									>
										<h4 style={ { margin: '0 0 6px' } }>
											{ __( 'Icon color', 'tribe' ) }
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
															selectedIconColor ===
															colorObj.value
																? 'selected'
																: ''
														}` }
														style={ {
															backgroundColor:
																colorObj.value,
														} }
														onClick={ () =>
															setAttributes( {
																selectedIconColor:
																	colorObj.value,
															} )
														}
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
														onClick={ () =>
															setAttributes( {
																selectedBgColor:
																	colorObj.value,
															} )
														}
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
									</PanelBody>
								</>
							);
						}

						if ( tab.name === 'dimensions' ) {
							return (
								<>
									<PanelBody
										title={ __( 'Dimensions', 'tribe' ) }
										className="controls-tribe-icon-picker"
										initialOpen={ true }
									>
										<ToggleControl
											label={ __(
												'Rounded Icon Background',
												'tribe'
											) }
											checked={ isRounded }
											onChange={ ( value ) =>
												setAttributes( {
													isRounded: value,
												} )
											}
										/>
										<RangeControl
											label={ __( 'Padding', 'tribe' ) }
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
											label={ __( 'Width', 'tribe' ) }
											value={ iconWidth }
											onChange={ ( value ) =>
												setAttributes( {
													iconWidth: value,
												} )
											}
											min={ 20 }
											max={ 300 }
											step={ 1 }
											afterIcon={ () => <span>px</span> }
										/>
									</PanelBody>
								</>
							);
						}
					} }
				</TabPanel>
			</InspectorControls>
		</div>
	);
}
