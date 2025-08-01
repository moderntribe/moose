import { ready } from 'utils/events';
import { Navigation, A11y, Pagination } from 'swiper/modules';
import Swiper from 'swiper';

const el = {
	swipers: null,
};

const bindEvents = () => {
	el.swipers.forEach( ( swiper ) => {
		const args = JSON.parse(
			swiper.getAttribute( 'data-swiper-settings' )
		);
		const prevButton = swiper.querySelector( '.swiper-button-prev' );
		const nextButton = swiper.querySelector( '.swiper-button-next' );
		const pagination = swiper.querySelector( '.swiper-pagination' );

		if ( prevButton || nextButton ) {
			args.navigation = {
				nextEl: nextButton,
				prevEl: prevButton,
			};
		}

		if ( pagination ) {
			args.pagination = {
				el: pagination,
				clickable:
					pagination.getAttribute( 'data-clickable' ) === 'true',
			};
		}

		new Swiper( swiper, {
			...args,
			modules: [ Navigation, A11y, Pagination ],
		} );
	} );
};

const cacheElements = () => {
	el.swipers = document.querySelectorAll( '[data-swiper-settings]' );
};

const init = () => {
	cacheElements();
	bindEvents();
};

ready( init );
