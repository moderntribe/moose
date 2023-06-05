/**
 * Init Slick Slider on jquery.
 */
jQuery( document ).ready( function( $ ) {
	/**
	 * init Slick Slider
	 *
	 * @param {object} container the slider container.
	 */
	function kbProSlickSliderInit( container ) {
		var sliderSpeed = parseInt( container.data( 'slider-speed' ) ),
			sliderAnimationSpeed = parseInt( container.attr( 'data-slider-anim-speed' ) ),
			sliderArrows = container.data( 'slider-arrows' ),
			sliderDots = container.data( 'slider-dots' ),
			sliderPause = container.data( 'slider-hover-pause' ),
			sliderAuto = container.data( 'slider-auto' ),
			sliderFade = container.attr('data-slider-fade');
		var slickRtl = false;
		if ( 'false' == sliderFade ) {
			sliderFade = false;
		} else {
			sliderFade = true;
		}
		if ( $( 'html[dir="rtl"]' ).length ) {
			slickRtl = true;
		}
		container.on( 'init', function() {
			container.removeClass( 'kb-slider-loading' );
		} );
		container.slick( {
			slidesToShow: 1,
			slidesToScroll: 1,
			arrows: sliderArrows,
			speed: sliderAnimationSpeed,
			autoplay: sliderAuto,
			autoplaySpeed: sliderSpeed,
			fade: sliderFade,
			pauseOnHover: sliderPause,
			rtl: slickRtl,
			adaptiveHeight: true,
			dots: sliderDots,
		} );
		$( window ).on( 'kadence-tabs-open', function( e ) {
			container.slick( 'refresh' );
		} );
		// $( window ).on( 'resize', function( e ) {
		// 	container.slick( 'refresh' );
		// } );
		// On before slide change
		container.on( 'beforeChange', function( event, slick, currentSlide, nextSlide ) {
			container.find( '.aos-animate' ).each( function() {
				$( this ).removeClass( 'aos-animate' );
			} );
		} );
		container.on( 'afterChange', function( event, slick, currentSlide ) {
			var the_slide = container.find( '[data-slick-index="' + currentSlide + '"]' );
			if ( the_slide ) {
				the_slide.find( '.aos-init' ).each( function() {
					AOS.refresh();
					$( this ).addClass( 'aos-animate' );
				} );
			}
		} );
	}
	$( '.kb-blocks-advanced-slider-init' ).each( function() {
		var container = $( this );
		kbProSlickSliderInit( container );
	} );
} );
