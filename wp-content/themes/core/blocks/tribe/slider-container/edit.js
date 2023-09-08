/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
/* eslint-disable */
import {
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { useRef, useEffect } from '@wordpress/element';
import { Navigation, Pagination, A11y } from 'swiper/modules';
import Swiper from 'swiper';
import swiperSettings from './swiper.json';
/* eslint-enable */

const TEMPLATE = [
	[
		'tribe/slider-slide',
		{},
		[ [ 'core/paragraph', { placeholder: 'Enter slide content...' } ] ],
	],
	[
		'tribe/slider-slide',
		{},
		[ [ 'core/paragraph', { placeholder: 'Enter slide content...' } ] ],
	],
];

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit() {
	const block = useRef( null );
	const blockProps = useBlockProps();
	const { children } = useInnerBlocksProps( blockProps, {
		allowedBlocks: [ 'tribe/slider-slide' ],
		template: TEMPLATE,
	} );

	useEffect( () => {
		if (
			block.current &&
			! block.current.classList.contains( 'swiper-initialized' )
		) {
			new Swiper( '.swiper', {
				modules: [ Navigation, Pagination, A11y ],
				...swiperSettings,
			} );
		}
	} );

	return (
		<div { ...blockProps }>
			<div ref={ block } className="swiper">
				<div className="swiper-wrapper">{ children }</div>
				<div className="swiper-pagination"></div>
				<div className="swiper-button-next"></div>
				<div className="swiper-button-prev"></div>
			</div>
		</div>
	);
}
