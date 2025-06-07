import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { PanelBody, PanelRow, TextControl } from '@wordpress/components';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { ICONS_LIST } from './icons';
import './editor.pcss';

// Color options
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

export default function Edit( { attributes, setAttributes } ) {
	const { selectedIcon, searchQuery, selectedIconColor, selectedBgColor } =
		attributes;
	const [ filteredIcons, setFilteredIcons ] = useState( ICONS_LIST );

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
				<PanelRow className="controls-tribe-icon-picker">
					{ validIcon ? (
						<div
							className="icon-preview preview-sidebar"
							style={ {
								backgroundColor:
									selectedBgColor || 'transparent',
								color: selectedIconColor || 'white',
								margin: '10px',
							} }
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
				</PanelRow>

				<PanelBody
					title={ __( 'Icon', 'tribe' ) }
					className="controls-tribe-icon-picker"
					initialOpen={ true }
				>
					<div className="icon-picker">
						<TextControl
							label={ __( 'Search Icons', 'tribe' ) }
							value={ searchQuery }
							onChange={ ( value ) =>
								setAttributes( { searchQuery: value } )
							}
						/>
						<div className="icon-grid">
							{ filteredIcons.map( ( { name, unicode } ) => (
								<div
									key={ name }
									className={ `icon-item ${
										selectedIcon === name ? 'selected' : ''
									}` }
									role="button"
									tabIndex={ 0 }
									onClick={ () =>
										setAttributes( { selectedIcon: name } )
									}
									onKeyDown={ ( e ) => {
										if (
											e.key === 'Enter' ||
											e.key === ' '
										) {
											setAttributes( {
												selectedIcon: name,
											} );
										}
									} }
								>
									<span
										className="ms-icon"
										style={ {
											fontFamily: 'FabricMDL2Icons',
										} }
									>
										{ String.fromCharCode(
											parseInt( unicode, 16 )
										) }
									</span>
								</div>
							) ) }
						</div>
					</div>
				</PanelBody>

				<PanelBody
					title={ __( 'Icon color', 'tribe' ) }
					className="controls-tribe-icon-picker"
					initialOpen={ true }
				>
					<div className="color-picker">
						<div className="color-grid">
							{ COLORS.map( ( colorObj ) => (
								<div
									key={ colorObj.value }
									role="button"
									title={ colorObj.name }
									tabIndex={ 0 }
									className={ `color-item ${
										selectedIconColor === colorObj.value
											? 'selected'
											: ''
									}` }
									style={ {
										backgroundColor: colorObj.value,
									} }
									onClick={ () =>
										setAttributes( {
											selectedIconColor: colorObj.value,
										} )
									}
									onKeyDown={ ( e ) => {
										if (
											e.key === 'Enter' ||
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
										.replace( / /g, '-' ) }
								></div>
							) ) }
						</div>
					</div>
				</PanelBody>

				<PanelBody
					title={ __( 'Background color', 'tribe' ) }
					className="controls-tribe-icon-picker"
					initialOpen={ true }
				>
					<p style={ { color: '#555', marginBottom: '10px' } }>
						{ __(
							'If ‘transparent’ is selected, the icon’s padding is removed.',
							'tribe'
						) }
					</p>
					<div className="color-picker">
						<div className="color-grid">
							{ COLORS.map( ( colorObj ) => (
								<div
									key={ colorObj.value }
									role="button"
									title={ colorObj.name }
									tabIndex={ 0 }
									className={ `color-item ${
										selectedBgColor === colorObj.value
											? 'selected'
											: ''
									}` }
									style={ {
										backgroundColor: colorObj.value,
									} }
									onClick={ () =>
										setAttributes( {
											selectedBgColor: colorObj.value,
										} )
									}
									onKeyDown={ ( e ) => {
										if (
											e.key === 'Enter' ||
											e.key === ' '
										) {
											setAttributes( {
												selectedBgColor: colorObj.value,
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
				</PanelBody>
			</InspectorControls>
		</div>
	);
}
