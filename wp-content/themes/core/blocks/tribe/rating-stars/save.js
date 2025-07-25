import { useBlockProps } from '@wordpress/block-editor';

import FullStarIcon from './icons/FullStarIcon';
import HalfStarIcon from './icons/HalfStarIcon';
import EmptyStarIcon from './icons/EmptyStarIcon';

export default function save( { attributes } ) {
	const { rating, containerSize } = attributes;

	const renderStars = () => {
		const stars = [];
		let remaining = rating;

		for ( let i = 0; i < 5; i++ ) {
			if ( remaining >= 1 ) {
				stars.push( <FullStarIcon key={ i } /> );
				remaining -= 1;
			} else if ( remaining >= 0.5 ) {
				stars.push( <HalfStarIcon key={ i } /> );
				remaining -= 0.5;
			} else {
				stars.push( <EmptyStarIcon key={ i } /> );
			}
		}
		return stars;
	};

	return (
		<div
			{ ...useBlockProps.save() }
			role="img"
			aria-label={ `Rated ${ rating } out of 5 stars` }
		>
			<div
				className="stars-wrapper"
				style={ {
					'--rating-stars--size': `${ containerSize || 100 }px`,
				} }
			>
				{ renderStars() }
			</div>
		</div>
	);
}
