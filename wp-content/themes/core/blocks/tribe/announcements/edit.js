import {
	useBlockProps,
	InspectorControls,
	RichText,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import './editor.pcss';

export default function Edit( { attributes, setAttributes } ) {
	const blockProps = useBlockProps();

	const { heading, body, ctaLabel, ctaLink, ctaStyle, theme, dismissible } =
		attributes;

	const darkThemes = [ 'brand', 'black', 'error' ];
	const isDark = darkThemes.includes( theme );

	const classes = [
		'b-announcement',
		`b-announcement--theme-${ theme }`,
		isDark ? 'is-style-dark' : '',
	]
		.filter( Boolean )
		.join( ' ' );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Call to Action', 'tribe' ) }
					initialOpen={ true }
				>
					<TextControl
						label={ __( 'CTA Label', 'tribe' ) }
						value={ ctaLabel }
						onChange={ ( value ) =>
							setAttributes( { ctaLabel: value } )
						}
					/>
					<TextControl
						label={ __( 'CTA URL', 'tribe' ) }
						value={ ctaLink }
						onChange={ ( value ) =>
							setAttributes( { ctaLink: value } )
						}
						type="url"
					/>
					<SelectControl
						label={ __( 'CTA Style', 'tribe' ) }
						value={ ctaStyle }
						options={ [
							{
								label: __( 'Primary', 'tribe' ),
								value: 'primary',
							},
							{
								label: __( 'Secondary', 'tribe' ),
								value: 'secondary',
							},
							{
								label: __( 'Tertiary', 'tribe' ),
								value: 'tertiary',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { ctaStyle: value } )
						}
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Display Settings', 'tribe' ) }
					initialOpen={ false }
				>
					<SelectControl
						label={ __( 'Theme', 'tribe' ) }
						value={ theme }
						options={ [
							{ label: __( 'Light', 'tribe' ), value: 'light' },
							{ label: __( 'Brand', 'tribe' ), value: 'brand' },
							{ label: __( 'Black', 'tribe' ), value: 'black' },
							{ label: __( 'Error', 'tribe' ), value: 'error' },
						] }
						onChange={ ( value ) =>
							setAttributes( { theme: value } )
						}
					/>
					<ToggleControl
						label={ __( 'Dismissible', 'tribe' ) }
						checked={ dismissible }
						onChange={ ( value ) =>
							setAttributes( { dismissible: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<aside { ...blockProps } className={ classes }>
				<div className="b-announcement__inner">
					<RichText
						tagName="h2"
						className="b-announcement__heading t-body"
						value={ heading }
						onChange={ ( value ) =>
							setAttributes( { heading: value } )
						}
						placeholder={ __(
							'Enter announcement heading…',
							'tribe'
						) }
					/>

					<RichText
						tagName="p"
						className="b-announcement__body t-body"
						value={ body }
						onChange={ ( value ) =>
							setAttributes( { body: value } )
						}
						placeholder={ __(
							'Enter announcement body text…',
							'tribe'
						) }
					/>

					{ ctaLabel && ctaLink && (
						<div className="b-announcement__cta-wrapper l-flex">
							<span className="b-announcement__cta">
								<a
									href={ ctaLink }
									className={ `a-btn-${ ctaStyle }` }
								>
									{ ctaLabel }
								</a>
							</span>
						</div>
					) }
				</div>

				{ dismissible && (
					<div className="b-announcement__dismiss-wrapper">
						<button
							type="button"
							className="b-announcement__dismiss u-button-reset"
							aria-label="Dismiss announcement"
						>
							<span className="b-announcement__dismiss-text">
								{ __( 'Dismiss', 'tribe' ) }
							</span>
						</button>
					</div>
				) }
			</aside>
		</>
	);
}
