import { useBlockProps } from '@wordpress/block-editor';
import fullStar from './icons/full-star.svg';
import halfStar from './icons/half-star.svg';
import emptyStar from './icons/empty-star.svg';

export default function save( { attributes } ) {
	const { rating, containerSize } = attributes;

	const renderStars = () => {
		const stars = [];
		let remaining = rating;

		for ( let i = 0; i < 5; i++ ) {
			let src;
			if ( remaining >= 1 ) {
				src = fullStar;
				remaining -= 1;
			} else if ( remaining >= 0.5 ) {
				src = halfStar;
				remaining -= 0.5;
			} else {
				src = emptyStar;
			}

			stars.push(
				<img
					key={ i }
					src={ src }
					alt=""
					role="presentation"
					aria-hidden="true"
				/>
			);
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
