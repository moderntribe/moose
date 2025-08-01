import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';

// Constants for container size control
const MIN_SIZE = 100;
const MAX_SIZE = 840;
const STEP_SIZE = 5;

export default function Edit( { attributes, setAttributes } ) {
	const { rating, containerSize } = attributes;

	let remaining = rating;
	const stars = [];

	for ( let i = 0; i < 5; i++ ) {
		if ( remaining >= 1 ) {
			stars.push( <span key={ i } className="star star--full" /> );
			remaining -= 1;
		} else if ( remaining >= 0.5 ) {
			stars.push( <span key={ i } className="star star--half" /> );
			remaining -= 0.5;
		} else {
			stars.push( <span key={ i } className="star star--empty" /> );
		}
	}

	return (
		<Fragment>
			<InspectorControls>
				<PanelBody
					title={ __( 'Star Rating Settings', 'tribe' ) }
					initialOpen={ true }
				>
					<RangeControl
						label={ __( 'Rating', 'tribe' ) }
						value={ rating }
						onChange={ ( value ) =>
							setAttributes( { rating: value } )
						}
						min={ 0 }
						max={ 5 }
						step={ 0.5 }
					/>

					<RangeControl
						label={ __( 'Size', 'tribe' ) }
						value={ containerSize }
						onChange={ ( value ) =>
							setAttributes( {
								containerSize: value,
							} )
						}
						min={ MIN_SIZE }
						max={ MAX_SIZE }
						step={ STEP_SIZE }
						afterIcon={ () => <span>px</span> }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...useBlockProps() }>
				<div
					className="stars-wrapper"
					style={ { '--rating-stars--size': `${ containerSize }px` } }
					aria-hidden="true"
				>
					{ stars }
				</div>
			</div>
		</Fragment>
	);
}
