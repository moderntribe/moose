import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';

import fullStar from './icons/full-star.svg';
import halfStar from './icons/half-star.svg';
import emptyStar from './icons/empty-star.svg';

export default function Edit( { attributes, setAttributes } ) {
	const { rating, containerSize } = attributes;

	const renderStars = () => {
		const stars = [];
		let remaining = rating;

		for ( let i = 0; i < 5; i++ ) {
			if ( remaining >= 1 ) {
				stars.push(
					<img key={ i } src={ fullStar } alt="Full Star" />
				);
				remaining -= 1;
			} else if ( remaining >= 0.5 ) {
				stars.push(
					<img key={ i } src={ halfStar } alt="Half Star" />
				);
				remaining -= 0.5;
			} else {
				stars.push(
					<img key={ i } src={ emptyStar } alt="Empty Star" />
				);
			}
		}
		return stars;
	};

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
						min={ 100 }
						max={ 840 }
						step={ 5 }
						afterIcon={ () => <span>px</span> }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...useBlockProps() }>
				<div
					className="stars-wrapper"
					style={ {
						'--rating-stars--size':
							`${ containerSize }px` || '100px',
					} }
				>
					{ renderStars() }
				</div>
			</div>
		</Fragment>
	);
}
