/* eslint-disable no-var */
/* global Splide */
/**
 * File kb-carousel-init.js.
 * Gets carousel working for post carousel blocks.
 */
 ( function() {
	'use strict';
	var kadenceBlocksProSplide = {
		cache: [],
		inTab: [],
		theTab: [],
		trigger: [],
		/**
		 * Initiate the script to process all
		 */
		initAll: function() {
			var splideSliders = document.querySelectorAll( '.kadence-splide-slider-init' );
			for ( let i = 0; i < splideSliders.length; i++ ) {
				const sliderSpeed = parseFloat( splideSliders[ i ].parentNode.getAttribute( 'data-slider-speed' ) ),
					sliderAnimationSpeed = parseInt( splideSliders[ i ].parentNode.getAttribute( 'data-slider-anim-speed' ) ),
					sliderArrows = splideSliders[ i ].parentNode.getAttribute( 'data-slider-arrows' ),
					sliderDots = splideSliders[ i ].parentNode.getAttribute( 'data-slider-dots' ),
					sliderAuto = splideSliders[ i ].parentNode.getAttribute( 'data-slider-auto' ),
					sliderAutoScroll = splideSliders[ i ].parentNode.getAttribute( 'data-slider-auto-scroll' ),
					sliderPause = splideSliders[ i ].parentNode.getAttribute( 'data-slider-hover-pause' ),
					scroll = parseInt( splideSliders[ i ].parentNode.getAttribute( 'data-slider-scroll' ) ),
					xxl = parseInt( splideSliders[ i ].parentNode.getAttribute( 'data-columns-xxl' ) ),
					xl = parseInt( splideSliders[ i ].parentNode.getAttribute( 'data-columns-xl' ) ),
					md = parseInt( splideSliders[ i ].parentNode.getAttribute( 'data-columns-md' ) ),
					sm = parseInt( splideSliders[ i ].parentNode.getAttribute( 'data-columns-sm' ) ),
					xs = parseInt( splideSliders[ i ].parentNode.getAttribute( 'data-columns-xs' ) ),
					ss = parseInt( splideSliders[ i ].parentNode.getAttribute( 'data-columns-ss' ) ),
					gap = parseInt( splideSliders[ i ].parentNode.getAttribute( 'data-slider-gap' ) ),
					gapTablet = parseInt( splideSliders[ i ].parentNode.getAttribute( 'data-slider-gap-tablet' ) ),
					gapMobile = parseInt( splideSliders[ i ].parentNode.getAttribute( 'data-slider-gap-mobile' ) ),
					gapUnit = splideSliders[ i ].parentNode.getAttribute( 'data-slider-gap-unit' );
				splideSliders[ i ].parentNode.classList.add( 'splide-initial' );
				let slideShowSpeed = ( undefined !== sliderSpeed ? sliderSpeed : 7000 );
				const sliderScrollAuto = ( undefined !== sliderAutoScroll && 'true' === sliderAutoScroll ? true : false );
				kadenceBlocksProSplide.trigger[ i ] = false;
				if ( sliderAuto == 'true' ) {
					if ( ! sliderScrollAuto && slideShowSpeed < ( undefined !== sliderAnimationSpeed ? sliderAnimationSpeed : 400 ) ) {
						kadenceBlocksProSplide.trigger[ i ] = true;
						slideShowSpeed = slideShowSpeed + ( undefined !== sliderAnimationSpeed ? sliderAnimationSpeed : 400 );
					}
				}
				let sliderDirection = 'ltr';
				let scrollSxxl = xxl,
					scrollSxl = xl,
					scrollSmd = md,
					scrollSsm = sm,
					scrollSxs = xs,
					scrollSss = ss;
				if ( document.querySelector( 'html[dir="rtl"]' ) ) {
					sliderDirection = 'rtl';
				}
				if ( 1 === scroll ) {
					scrollSxxl = 1;
					scrollSxl = 1;
					scrollSmd = 1;
					scrollSsm = 1;
					scrollSxs = 1;
					scrollSss = 1;
				}
				if ( ! splideSliders[ i ].firstChild ) {
					return;
				}
				// Hack to remove extra span tag added by third party plugin.
				if ( splideSliders[ i ].firstChild.classList.contains('wpfHidden')) {
					splideSliders[ i ].removeChild(splideSliders[ i ].firstChild);
					if ( ! splideSliders[ i ].firstChild ) {
						return;
					}
				}
				splideSliders[ i ].firstChild.classList.add( 'splide__list' );
				for ( let n = 0; n < splideSliders[ i ].firstChild.children.length; n++ ) {
					splideSliders[ i ].firstChild.children[ n ].classList.add( 'splide__slide' );
					if ( splideSliders[ i ].firstChild.children[ n ].classList.contains( 'last' ) ) {
						splideSliders[ i ].firstChild.children[ n ].classList.remove( 'last' );
					}
				}
				const options = {
					perPage: xxl,
					type: 'loop',
					slideFocus: false,
					easing: undefined !== sliderAnimationSpeed && sliderAnimationSpeed > 1000 ? 'linear' : 'cubic-bezier(0.25, 1, 0.5, 1)',
					pauseOnHover: ( sliderPause == 'false' ? false : true ),
					autoplay: ( sliderAuto == 'false' ? false : true ),
					interval: slideShowSpeed,
					speed: ( undefined !== sliderAnimationSpeed ? sliderAnimationSpeed : 400 ),
					arrows: ( sliderArrows == 'false' ? false : true ),
					pagination: ( sliderDots == 'false' ? false : true ),
					gap: gap + gapUnit,
					focus: 0,
					perMove: scrollSxxl,
					direction: sliderDirection,
					breakpoints: {
						543: {
							perPage: ss,
							perMove: scrollSss,
							gap: gapMobile + gapUnit,
						},
						767: {
							perPage: xs,
							perMove: scrollSxs,
							gap: gapMobile + gapUnit,
						},
						991: {
							perPage: sm,
							perMove: scrollSsm,
							gap: gapTablet + gapUnit,
						},
						1199: {
							perPage: md,
							perMove: scrollSmd,
							gap: gapTablet + gapUnit,
						},
						1499: {
							perPage: xl,
							perMove: scrollSxl,
							gap: gap + gapUnit,
						},
					},
				};
				if ( undefined !== window.splide && undefined !== window.splide.Extensions && sliderScrollAuto ) {
					options.autoplay = false;
					options.autoScroll = {
						speed: slideShowSpeed,
						pauseOnHover: ( sliderPause == 'false' ? false : true ),
					};
				}
				kadenceBlocksProSplide.cache[ i ] = new Splide( splideSliders[ i ].parentElement, options );
				kadenceBlocksProSplide.cache[ i ].on( 'mounted', function () {
					var event = new CustomEvent( 'splideMounted' );
					splideSliders[ i ].dispatchEvent( event );
				} );
				kadenceBlocksProSplide.cache[ i ].on( 'overflow', function ( isOverflow ) {
					// Reset the carousel position
					kadenceBlocksProSplide.cache[ i ].go( 0 );
				  
					kadenceBlocksProSplide.cache[ i ].options = {
					  arrows    : sliderArrows !== 'false' ? isOverflow : false,
					  pagination: sliderDots !== 'false' ? isOverflow : false,
					  drag      : isOverflow,
					  clones    : isOverflow ? undefined : 0, // Toggle clones
					};
				} );
				kadenceBlocksProSplide.inTab[ i ] = splideSliders[ i ].closest( '.wp-block-kadence-tab' ) !== null;
				if ( ! kadenceBlocksProSplide.inTab[ i ] ) {
					if ( undefined !== window.splide && undefined !== window.splide.Extensions && sliderScrollAuto ) {
						kadenceBlocksProSplide.cache[ i ].mount( window.splide.Extensions );
					} else {
						kadenceBlocksProSplide.cache[ i ].mount();
					}
					if ( kadenceBlocksProSplide.trigger[ i ] ) {
						kadenceBlocksProSplide.cache[ i ].go( '+1' );
					}
				} else {
					kadenceBlocksProSplide.theTab[ i ] = splideSliders[ i ].closest( '.wp-block-kadence-tab' );
					setTimeout( function() {
						if ( kadenceBlocksProSplide.theTab[ i ].getAttribute( 'aria-hidden' ) === 'false' ) {
							if ( undefined !== window.splide && undefined !== window.splide.Extensions && sliderScrollAuto ) {
								kadenceBlocksProSplide.cache[ i ].mount( window.splide.Extensions );
							} else {
								kadenceBlocksProSplide.cache[ i ].mount();
							}
							if ( kadenceBlocksProSplide.trigger[ i ] ) {
								kadenceBlocksProSplide.cache[ i ].go( '+1' );
							}
						}
					}, 200 );
				}
				window.addEventListener( 'kadence-tabs-open', function( e ) {
					if ( kadenceBlocksProSplide.inTab[ i ] ) {
						if ( undefined !== kadenceBlocksProSplide.theTab[ i ] && kadenceBlocksProSplide.theTab[ i ].getAttribute( 'aria-hidden' ) === 'false' ) {
							if ( kadenceBlocksProSplide.cache[ i ].state.is( Splide.STATES.CREATED ) || kadenceBlocksProSplide.cache[ i ].state.is( Splide.STATES.DESTROYED ) ) {
								kadenceBlocksProSplide.cache[ i ].mount();
								if ( kadenceBlocksProSplide.trigger[ i ] ) {
									kadenceBlocksProSplide.cache[ i ].go( '+1' );
								}
							} else if ( kadenceBlocksProSplide.cache[ i ].state.is( Splide.STATES.MOVING ) ) {
								kadenceBlocksProSplide.cache[ i ].destroy();
								kadenceBlocksProSplide.cache[ i ].mount();
								if ( kadenceBlocksProSplide.trigger[ i ] ) {
									kadenceBlocksProSplide.cache[ i ].go( '+1' );
								}
							}
						}
					}
				} );
			}
		},
		// Initiate the menus when the DOM loads.
		init: function() {
			if ( typeof Splide === 'function' ) {
				kadenceBlocksProSplide.initAll();
			} else {
				// eslint-disable-next-line vars-on-top
				var initLoadDelay = setInterval( function() {
					if ( typeof Splide === 'function' ) {
						kadenceBlocksProSplide.initAll();
						clearInterval( initLoadDelay );
					}
				}, 200 );
			}
		},
	};
	if ( 'loading' === document.readyState ) {
		// The DOM has not yet been loaded.
		document.addEventListener( 'DOMContentLoaded', kadenceBlocksProSplide.init );
	} else {
		// The DOM has already been loaded.
		kadenceBlocksProSplide.init();
	}
}() );
