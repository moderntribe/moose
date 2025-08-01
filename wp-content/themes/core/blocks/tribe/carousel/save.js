import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function save( props ) {
	const blockProps = useBlockProps.save();
	const {
		attributes: {
			autoplay,
			autoplayDelay,
			centeredSlides,
			loop,
			navigation,
			pagination,
			paginationClickable,
			slidesPerGroup,
			slidesPerViewAuto,
			slidesPerView,
			spaceBetween,
			speed,
		},
	} = props;

	const settings = {};

	// handle autoplay (we take some setting liberties here)
	if ( autoplay ) {
		settings.autoplay = {
			delay: parseInt( autoplayDelay ),
			disableOnInteraction: false,
			pauseOnMouseEnter: true,
		};
	}

	if ( centeredSlides ) {
		settings.centeredSlides = true;
	}

	if ( loop ) {
		settings.loop = true;
	}

	if ( slidesPerGroup > 1 ) {
		settings.slidesPerGroup = parseInt( slidesPerGroup );
	}

	if ( slidesPerView > 1 ) {
		settings.slidesPerView = parseInt( slidesPerView );
	}

	if ( slidesPerViewAuto ) {
		settings.slidesPerView = 'auto';
	}

	if ( spaceBetween > 0 ) {
		settings.spaceBetween = parseInt( spaceBetween );
	}

	if ( speed > 0 ) {
		settings.speed = parseInt( speed );
	}

	return (
		<div { ...blockProps }>
			<div
				className="swiper"
				data-swiper-settings={ JSON.stringify( settings ) }
			>
				<div className="swiper-wrapper">
					<InnerBlocks.Content />
				</div>
				{ navigation ? (
					<div className="swiper-navigation">
						<button
							type="button"
							className="swiper-button-prev"
						></button>
						<button
							type="button"
							className="swiper-button-next"
						></button>
					</div>
				) : (
					''
				) }
				{ pagination ? (
					<div
						className="swiper-pagination"
						data-clickable={ paginationClickable }
					></div>
				) : (
					''
				) }
			</div>
		</div>
	);
}
