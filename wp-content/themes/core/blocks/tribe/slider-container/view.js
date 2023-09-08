import { ready } from 'utils/events';
import { Navigation, Pagination, A11y } from 'swiper/modules';
import Swiper from 'swiper';
import swiperSettings from './swiper.json';

ready( () => {
	new Swiper( '.swiper', {
		modules: [ Navigation, Pagination, A11y ],
		...swiperSettings,
	} );
} );
