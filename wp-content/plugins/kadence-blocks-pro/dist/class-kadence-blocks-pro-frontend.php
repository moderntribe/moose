<?php
/**
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Enqueue CSS/JS of all the blocks.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Frontend {

	/**
	 * Google fonts to enqueue
	 *
	 * @var array
	 */
	public static $gfonts = array();


	/**
	 * Instance of this class
	 *
	 * @var null
	 */
	private static $instance = null;
	/**
	 * Instance Control
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * Class Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'on_init' ) );
		add_action( 'enqueue_block_assets', array( $this, 'blocks_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_inline_css' ), 80 );
		add_action( 'wp_head', array( $this, 'frontend_gfonts' ), 85 );
		// Log evergreen end time.
		add_action( 'wp_ajax_kadence_evergreen_timestamp', array( $this, 'save_evergreen_end_time' ) );
		add_action( 'wp_ajax_nopriv_kadence_evergreen_timestamp', array( $this, 'save_evergreen_end_time' ) );
		add_action( 'wp_ajax_kadence_get_evergreen', array( $this, 'get_evergreen_end_time' ) );
		add_action( 'wp_ajax_nopriv_kadence_get_evergreen', array( $this, 'get_evergreen_end_time' ) );
		add_filter( 'kadence_blocks_countdown_evergreen_config', array( $this, 'setup_evergreen_time' ), 10, 4 );
	}
	/**
	 * Adds evergreen info into the page. This is good unless there is page caching.
	 *
	 * @param string $timestamp the timestamp to set.
	 * @param string $campaign_id the campaign id.
	 * @param string $site_slug the site slug for cookies.
	 * @param string $reset the amount in days to wait before resetting.
	 */
	public function setup_evergreen_time( $timestamp, $campaign_id, $site_slug, $reset ) {
		if ( apply_filters( 'kadence_blocks_evergreen_countdown_no_cache_mode', false, $campaign_id ) ) {
			$campaign = new Kadence_Blocks_Pro_Countdown( $campaign_id, $site_slug, $reset );
			$timestamp = $campaign->get_end_date();
		}
		return $timestamp;
	}
	/**
	 * Uses ajax to save end time for the given visitor.
	 * This used to bypass cookie cache.
	 */
	public function get_evergreen_end_time() {
		check_ajax_referer( 'kadence_blocks_countdown', 'nonce' );
		if ( ! isset( $_POST['countdown_id'] ) || ! isset( $_POST['site_slug'] ) || ! isset( $_POST['reset'] ) ) {
			wp_die();
		}
		$campaign = new Kadence_Blocks_Pro_Countdown( sanitize_text_field( $_POST['countdown_id'] ), sanitize_text_field( $_POST['site_slug'] ), sanitize_text_field( $_POST['reset'] ) );
		$timestamp = $campaign->get_end_date();
		wp_die( $timestamp );
	}
	/**
	 * Uses ajax to save end time for the given visitor.
	 * This used to bypass cookie cache.
	 */
	public function save_evergreen_end_time() {
		check_ajax_referer( 'kadence_blocks_countdown', 'nonce' );
		if ( ! isset( $_POST['timestamp'] ) || ! isset( $_POST['countdown_id'] ) || ! isset( $_POST['site_slug'] ) ) {
			wp_die();
		}
		$campaign = new Kadence_Blocks_Pro_Countdown( sanitize_text_field( $_POST['countdown_id'] ), sanitize_text_field( $_POST['site_slug'] ) );
		$campaign->set_end_date( sanitize_text_field( $_POST['timestamp'] ) );
		wp_die( 'Success!' );
	}
	/**
	 * Build on Init
	 */
	public function on_init() {
		// Only load if Gutenberg is available.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		register_block_type(
			'kadence/imageoverlay',
			array(
				'render_callback' => array( $this, 'render_image_overlay_css' ),
				'editor_script'   => 'kadence-blocks-pro-js',
				'editor_style'    => 'kadence-blocks-pro-editor-css',
			)
		);

		register_block_type(
			'kadence/splitcontent',
			array(
				'render_callback' => array( $this, 'render_split_content_css' ),
				'editor_script'   => 'kadence-blocks-pro-js',
				'editor_style'    => 'kadence-blocks-pro-editor-css',
			)
		);

		register_block_type(
			'kadence/modal',
			array(
				'render_callback' => array( $this, 'render_modal_css' ),
				'editor_script'   => 'kadence-blocks-pro-js',
				'editor_style'    => 'kadence-blocks-pro-editor-css',
			)
		);

		register_block_type(
			'kadence/videopopup',
			array(
				'render_callback' => array( $this, 'render_video_popup_block' ),
				'editor_script'   => 'kadence-blocks-pro-js',
				'editor_style'    => 'kadence-blocks-pro-editor-css',
			)
		);

		register_block_type(
			'kadence/slider',
			array(
				'render_callback' => array( $this, 'render_slider_css' ),
				'editor_script'   => 'kadence-blocks-pro-js',
				'editor_style'    => 'kadence-blocks-pro-editor-css',
			)
		);
		register_block_type(
			'kadence/slide',
			array(
				'render_callback' => array( $this, 'render_slide_css' ),
				'editor_script'   => 'kadence-blocks-pro-js',
				'editor_style'    => 'kadence-blocks-pro-editor-css',
			)
		);
	}
	/**
	 * Render Inline CSS helper function
	 *
	 * @param array  $css the css for each rendered block.
	 * @param string $style_id the unique id for the rendered style
	 */
	public function render_inline_css( $css, $style_id, $in_content = false ) {
		if ( ! is_admin() ) {
			wp_register_style( $style_id, false );
			wp_enqueue_style( $style_id );
			wp_add_inline_style( $style_id, $css );
			if ( 1 === did_action( 'wp_head' ) && $in_content ) {
				wp_print_styles( $style_id );
			}
		}
	}
	/**
	 * Render Image Overlay CSS In Head
	 *
	 * @param array  $attributes the blocks attributes.
	 */
	public function render_image_overlay_css_head( $attributes ) {
		if ( ! wp_style_is( 'kadence-blocks-image-overlay', 'enqueued' ) ) {
			wp_enqueue_style( 'kadence-blocks-image-overlay' );
		}
		if ( isset( $attributes['uniqueID'] ) ) {
			$unique_id = $attributes['uniqueID'];
			$style_id = 'kt-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) && apply_filters( 'kadence_blocks_render_head_css', true, 'imageoverlay', $attributes ) ) {
				$attributes = apply_filters( 'kadence_blocks_image_overlay_render_block_attributes', $attributes );
				$css = $this->blocks_image_overlay_array( $attributes, $unique_id );
				if ( ! empty( $css ) ) {
					$this->render_inline_css( $css, $style_id );
				}
			}
		}
	}
	/**
	 * Render Image Overlay CSS Inline
	 *
	 * @param array  $attributes the blocks attributes.
	 * @param string $content the blocks content.
	 */
	public function render_image_overlay_css( $attributes, $content ) {
		if ( ! wp_style_is( 'kadence-blocks-image-overlay', 'enqueued' ) ) {
			wp_enqueue_style( 'kadence-blocks-image-overlay' );
		}
		if ( isset( $attributes['uniqueID'] ) ) {
			$unique_id = $attributes['uniqueID'];
			$style_id = 'kt-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) && apply_filters( 'kadence_blocks_render_inline_css', true, 'imageoverlay', $unique_id ) ) {
				$attributes = apply_filters( 'kadence_blocks_image_overlay_render_block_attributes', $attributes );
				$css = $this->blocks_image_overlay_array( $attributes, $unique_id );
				if ( ! empty( $css ) ) {
					if ( doing_filter( 'the_content' ) || apply_filters( 'kadence_blocks_force_render_inline_css_in_content', false, 'imageoverlay', $unique_id ) ) {
						$content = '<style id="' . $style_id . '" type="text/css">' . $css . '</style>' . $content;
					} else {
						$this->render_inline_css( $css, $style_id, true );
					}
				}
			}
		}
		return $content;
	}
	/**
	 * Render Slider CSS In Head
	 *
	 * @param array $attributes the blocks attributes.
	 */
	public function render_slider_css_head( $attributes ) {
		if ( ! wp_style_is( 'kadence-blocks-slider', 'enqueued' ) ) {
			wp_enqueue_style( 'kadence-blocks-slider' );
		}
		if ( isset( $attributes['uniqueID'] ) ) {
			$unique_id = $attributes['uniqueID'];
			$style_id = 'kt-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) && apply_filters( 'kadence_blocks_render_head_css', true, 'slider', $attributes ) ) {
				$attributes = apply_filters( 'kadence_blocks_slider_render_block_attributes', $attributes );
				$css = $this->blocks_slider_array( $attributes, $unique_id );
				if ( ! empty( $css ) ) {
					$this->render_inline_css( $css, $style_id );
				}
			}
		}
	}
	/**
	 * Render Slider CSS Inline
	 *
	 * @param array  $attributes the blocks attributes.
	 * @param string $content the blocks content.
	 */
	public function render_slider_css( $attributes, $content ) {
		if ( ! wp_style_is( 'kadence-blocks-slider', 'enqueued' ) ) {
			wp_enqueue_style( 'kadence-blocks-slider' );
		}
		wp_enqueue_style( 'kadence-blocks-pro-slick' );
		wp_enqueue_script( 'kadence-blocks-pro-slider-init' );
		if ( isset( $attributes['uniqueID'] ) ) {
			$unique_id = $attributes['uniqueID'];
			$style_id = 'kt-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) && apply_filters( 'kadence_blocks_render_inline_css', true, 'slider', $unique_id ) ) {
				$css = $this->blocks_slider_array( $attributes, $unique_id );
				if ( ! empty( $css ) ) {
					if ( doing_filter( 'the_content' ) || apply_filters( 'kadence_blocks_force_render_inline_css_in_content', false, 'slider', $unique_id ) ) {
						$content = '<style id="' . $style_id . '" type="text/css">' . $css . '</style>' . $content;
					} else {
						$this->render_inline_css( $css, $style_id, true );
					}
				}
			}
		}
		return $content;
	}
	/**
	 * Render Slider CSS In Head
	 *
	 * @param array $attributes the blocks attributes.
	 */
	public function render_slide_css_head( $attributes ) {
		if ( isset( $attributes['uniqueID'] ) && ( ( isset( $attributes['textColor'] ) && ! empty( $attributes['textColor'] ) ) || ( isset( $attributes['linkColor'] ) && ! empty( $attributes['linkColor'] ) ) || ( isset( $attributes['linkHoverColor'] ) && ! empty( $attributes['linkHoverColor'] ) ) ) ) {
			$unique_id = $attributes['uniqueID'];
			$style_id = 'kt-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) && apply_filters( 'kadence_blocks_render_head_css', true, 'slide', $attributes ) ) {
				$attributes = apply_filters( 'kadence_blocks_slide_render_block_attributes', $attributes );
				$css = $this->blocks_slide_array( $attributes, $unique_id );
				if ( ! empty( $css ) ) {
					$this->render_inline_css( $css, $style_id );
				}
			}
		}
	}
	/**
	 * Render Post Carousel CSS in the header.
	 *
	 * @param array $attributes the blocks attributes.
	 */
	public function render_post_grid_carousel_css_head( $attributes ) {
		if ( ! class_exists( 'Kadence_Blocks_Pro_Post_Grid' ) ) {
			return false;
		}
		$post_grid = Kadence_Blocks_Pro_Post_Grid::get_instance();
		$post_grid->enqueue_style( 'kadence-blocks-post-grid' );
		$layout = ( ! empty( $attributes['layout'] ) ? $attributes['layout'] : 'grid' );
		if ( ( 'masonry' === $layout || 'grid' === $layout ) && isset( $attributes['displayFilter'] ) && true === $attributes['displayFilter'] ) {
			$post_grid->enqueue_script( 'kadence-blocks-pro-iso-post-init' );
		} elseif ( 'masonry' === $layout ) {
			$post_grid->enqueue_script( 'kadence-blocks-pro-masonry-init' );
		} elseif ( 'carousel' === $layout ) {
			$post_grid->enqueue_style( 'kad-splide' );
			$post_grid->enqueue_script( 'kadence-blocks-pro-splide-init' );
			if ( isset( $attributes['autoScroll'] ) && true === $attributes['autoScroll'] ) {
				$post_grid->enqueue_script( 'kadence-splide-auto-scroll' );
				global $wp_scripts;
				$script = $wp_scripts->query( 'kadence-blocks-pro-splide-init', 'registered' );
				if ( $script ) {
					if ( ! in_array( 'kadence-splide-auto-scroll', $script->deps ) ) {
						$script->deps[] = 'kadence-splide-auto-scroll';
					}
				}
			}
		}
		if ( isset( $attr['uniqueID'] ) ) {
			$unique_id = $attr['uniqueID'];
			$style_id = 'kt-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) ) {
				$css = $post_grid->output_css( $attr, $unique_id );
				if ( ! empty( $css ) ) {
					$this->render_inline_css( $css, $style_id );
				}
			}
		}
		if ( ! empty( $post_grid::$gfonts ) ) {
			foreach ( $post_grid::$gfonts as $key => $gfont_values ) {
				if ( ! in_array( $key, self::$gfonts, true ) ) {
					$add_font = array(
						'fontfamily' => $gfont_values['fontfamily'],
						'fontvariants' => ( isset( $gfont_values['fontvariants'] ) && ! empty( $gfont_values['fontvariants'] ) ? $gfont_values['fontvariants'] : array() ),
						'fontsubsets' => ( isset( $gfont_values['fontsubsets'] ) && ! empty( $gfont_values['fontsubsets'] ) ? $gfont_values['fontsubsets'] : array() ),
					);
					self::$gfonts[ $key ] = $add_font;
				} else {
					foreach ( $gfont_values['fontvariants'] as $gfontvariant_values ) {
						if ( ! in_array( $gfontvariant_values, self::$gfonts[ $key ]['fontvariants'], true ) ) {
							self::$gfonts[ $key ]['fontvariants'] = $gfontvariant_values;
						}
					}
					foreach ( $gfont_values['fontsubsets'] as $gfontsubset_values ) {
						if ( ! in_array( $gfontsubset_values, self::$gfonts[ $key ]['fontsubsets'], true ) ) {
							self::$gfonts[ $key ]['fontsubsets'] = $gfontsubset_values;
						}
					}
				}
			}
		}
	}
	/**
	 * Render Product Carousel Css in the header.
	 *
	 * @param array $attributes the blocks attributes.
	 */
	public function render_product_carousel_css_head( $attributes ) {
		$this->enqueue_style( 'kadence-blocks-product-carousel' );
		$this->enqueue_style( 'kad-splide' );
		$this->enqueue_script( 'kadence-blocks-pro-splide-init' );
		if ( isset( $attributes['autoScroll'] ) && true === $attributes['autoScroll'] ) {
			$this->enqueue_script( 'kadence-splide-auto-scroll' );
			global $wp_scripts;
			$script = $wp_scripts->query( 'kadence-blocks-pro-splide-init', 'registered' );
			if ( $script ) {
				if ( ! in_array( 'kadence-splide-auto-scroll', $script->deps ) ) {
					$script->deps[] = 'kadence-splide-auto-scroll';
				}
			}
		}
		if ( isset( $attributes['uniqueID'] ) ) {
			$unique_id = $attributes['uniqueID'];
			$style_id = 'kt-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) ) {
				$css = $this->blocks_product_carousel_array( $attributes, $unique_id );
				if ( ! empty( $css ) ) {
					$this->render_inline_css( $css, $style_id );
				}
			}
		}
	}
	/**
	 * Render Slide CSS Inline
	 *
	 * @param array  $attributes the blocks attributes.
	 * @param string $content the blocks content.
	 */
	public function render_slide_css( $attributes, $content ) {
		if ( isset( $attributes['uniqueID'] ) && ( ( isset( $attributes['textColor'] ) && ! empty( $attributes['textColor'] ) ) || ( isset( $attributes['linkColor'] ) && ! empty( $attributes['linkColor'] ) ) || ( isset( $attributes['linkHoverColor'] ) && ! empty( $attributes['linkHoverColor'] ) ) ) ) {
			$unique_id = $attributes['uniqueID'];
			$style_id = 'kt-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) && apply_filters( 'kadence_blocks_render_inline_css', true, 'slide', $unique_id ) ) {
				$attributes = apply_filters( 'kadence_blocks_slide_render_block_attributes', $attributes );
				$css = $this->blocks_slide_array( $attributes, $unique_id );
				if ( ! empty( $css ) ) {
					if ( doing_filter( 'the_content' ) ) {
						$content = '<style id="' . $style_id . '" type="text/css">' . $css . '</style>' . $content;
					} else {
						$this->render_inline_css( $css, $style_id, true );
					}
				}
			}
		}
		return $content;
	}
	/**
	 * Render split Content CSS In Head
	 *
	 * @param array $attributes the blocks attributes.
	 */
	public function render_split_content_css_head( $attributes ) {
		if ( ! wp_style_is( 'kadence-blocks-split-content', 'enqueued' ) ) {
			wp_enqueue_style( 'kadence-blocks-split-content' );
		}
		if ( isset( $attributes['uniqueID'] ) ) {
			$unique_id = $attributes['uniqueID'];
			$style_id = 'kt-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) && apply_filters( 'kadence_blocks_render_head_css', true, 'splitcontent', $attributes ) ) {
				$attributes = apply_filters( 'kadence_blocks_split_content_render_block_attributes', $attributes );
				$css = $this->blocks_splitcontent_array( $attributes, $unique_id );
				if ( ! empty( $css ) ) {
					$this->render_inline_css( $css, $style_id );
				}
			}
		}
	}
	/**
	 * Render Split Content CSS Inline
	 *
	 * @param array  $attributes the blocks attributes.
	 * @param string $content the blocks content.
	 */
	public function render_split_content_css( $attributes, $content ) {
		if ( ! wp_style_is( 'kadence-blocks-split-content', 'enqueued' ) ) {
			wp_enqueue_style( 'kadence-blocks-split-content' );
		}
		if ( isset( $attributes['uniqueID'] ) ) {
			$unique_id = $attributes['uniqueID'];
			$style_id = 'kt-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) ) {
				$attributes = apply_filters( 'kadence_blocks_split_content_render_block_attributes', $attributes );
				$css = $this->blocks_splitcontent_array( $attributes, $unique_id );
				if ( ! empty( $css ) ) {
					if ( doing_filter( 'the_content' ) ) {
						$content = '<style id="' . $style_id . '" type="text/css">' . $css . '</style>' . $content;
					} else {
						$this->render_inline_css( $css, $style_id, true );
					}
				}
			}
		}
		return $content;
	}
	/**
	 * Render Modal CSS In Head
	 *
	 * @param array $attributes the blocks attributes.
	 */
	public function render_modal_css_head( $attributes ) {
		if ( ! wp_style_is( 'kadence-blocks-modal', 'enqueued' ) ) {
			wp_enqueue_style( 'kadence-blocks-modal' );
		}
		if ( isset( $attributes['uniqueID'] ) ) {
			$unique_id = $attributes['uniqueID'];
			$style_id = 'kt-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) && apply_filters( 'kadence_blocks_render_head_css', true, 'modal', $attributes ) ) {
				$attributes = apply_filters( 'kadence_blocks_modal_render_block_attributes', $attributes );
				$css = $this->blocks_modal_array( $attributes, $unique_id );
				if ( ! empty( $css ) ) {
					$this->render_inline_css( $css, $style_id );
				}
			}
		}
	}
	/**
	 * Render Modal CSS Inline
	 *
	 * @param array  $attributes the blocks attributes.
	 * @param string $content the blocks content.
	 */
	public function render_modal_css( $attributes, $content ) {
		if ( ! wp_style_is( 'kadence-blocks-modal', 'enqueued' ) ) {
			wp_enqueue_style( 'kadence-blocks-modal' );
		}
		if ( isset( $attributes['uniqueID'] ) ) {
			$unique_id = $attributes['uniqueID'];
			$style_id = 'kt-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) && apply_filters( 'kadence_blocks_render_inline_css', true, 'modal', $unique_id ) ) {
				wp_enqueue_script( 'kadence-modal' );
				$attributes = apply_filters( 'kadence_blocks_modal_render_block_attributes', $attributes );
				$css = $this->blocks_modal_array( $attributes, $unique_id );
				if ( ! empty( $css ) ) {
					if ( doing_filter( 'the_content' ) || apply_filters( 'kadence_blocks_force_render_inline_css_in_content', false, 'modal', $unique_id ) ) {
						$content = '<style id="' . $style_id . '" type="text/css">' . $css . '</style>' . $content;
					} else {
						$this->render_inline_css( $css, $style_id, true );
					}
				}
			}
			if ( isset( $attributes['loadFooter'] ) && true === $attributes['loadFooter'] ) {
				if ( $content ) {
					preg_match( '/<div class="kadence-block-pro-modal-load-footer"><\/div>(.*?)<div class="kadence-block-pro-modal-load-footer-end"><\/div>/s', $content, $match );
					if ( isset( $match ) && isset( $match[0] ) && !empty( $match[0] ) ) {
						$modal_content = $match[0];
						$modal_content = str_replace( '<div class="kadence-block-pro-modal-load-footer"></div>', '', $modal_content );
						$modal_content = str_replace( '<div class="kadence-block-pro-modal-load-footer-end"></div>', '', $modal_content );
						$content = str_replace( $match[0], '', $content );
						add_action(
							'wp_footer',
							function() use( $modal_content, $unique_id ) {
								echo '<!-- [pro-modal-' . esc_attr( $unique_id ) . '] -->';
								echo apply_filters( 'kadence_blocks_pro_modal_footer_output', do_shortcode( $modal_content ) );
								echo '<!-- [/pro-modal-' . esc_attr( $unique_id ) . '] -->';
							},
							9
						);
					}
				}
			}
		}
		return $content;
	}
	/**
	 * Render Video Popup CSS In Head
	 *
	 * @param array $attributes the blocks attributes.
	 */
	public function render_video_popup_css_head( $attributes ) {
		if ( ! wp_style_is( 'kadence-blocks-video-popup', 'enqueued' ) ) {
			wp_enqueue_style( 'kadence-blocks-video-popup' );
		}
		if ( isset( $attributes['uniqueID'] ) ) {
			$unique_id = $attributes['uniqueID'];
			$style_id = 'kt-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) && apply_filters( 'kadence_blocks_render_head_css', true, 'videopopup', $attributes ) ) {
				$attributes = apply_filters( 'kadence_blocks_video_render_block_attributes', $attributes );
				$css = $this->blocks_video_popup_array( $attributes, $unique_id );
				if ( ! empty( $css ) ) {
					$this->render_inline_css( $css, $style_id );
				}
			}
		}
	}
	/**
	 * Render Video Popup CSS Inline
	 *
	 * @param array  $attributes the blocks attributes.
	 * @param string $content the blocks content.
	 */
	public function render_video_popup_block( $attributes, $content, $block ) {
		if ( ! wp_style_is( 'kadence-blocks-video-popup', 'enqueued' ) ) {
			wp_enqueue_style( 'kadence-blocks-video-popup' );
		}
		if ( isset( $attributes['uniqueID'] ) ) {
			$unique_id = $attributes['uniqueID'];
			$style_id = 'kt-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) && apply_filters( 'kadence_blocks_render_inline_css', true, 'videopopup', $unique_id ) ) {
				$attributes = apply_filters( 'kadence_blocks_video_render_block_attributes', $attributes );
				wp_enqueue_style( 'kadence-glightbox' );
				wp_enqueue_script( 'kadence-blocks-pro-glight-video-init' );
				$css = $this->blocks_video_popup_array( $attributes, $unique_id );
				if ( ! empty( $css ) ) {
					if ( doing_filter( 'the_content' ) || apply_filters( 'kadence_blocks_force_render_inline_css_in_content', false, 'videopopup', $unique_id ) ) {
						$style_id = $style_id . get_the_ID();
						$content = '<style id="' . $style_id . '" type="text/css">' . $css . '</style>' . $content;
					} else {
						$this->render_inline_css( $css, $style_id, true );
					}
				}
			}
		}
		return $content;
	}
	/**
	 * Hex to RGBA
	 *
	 * @param string $hex string hex code.
	 * @param number $alpha alpha number.
	 */
	public function hex2rgba( $hex, $alpha ) {
		if ( empty( $hex ) ) {
			return '';
		}
		if ( 'transparent' === $hex ) {
			return $hex;
		}
		$hex = str_replace( '#', '', $hex );

		if ( strlen( $hex ) == 3 ) {
			$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
			$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
			$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
		} else {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		}
		$rgba = 'rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $alpha . ')';
		return $rgba;
	}
	/**
	 * Enqueue Gutenberg block assets
	 *
	 * @since 1.0.0
	 */
	public function blocks_assets() {
		// If in the backend, bail out.
		if ( is_admin() ) {
			return;
		}
		$this->register_scripts();
	}
	/**
	 * Enqueue Frontend Fonts
	 */
	public function frontend_gfonts() {
		if ( empty( self::$gfonts ) ) {
			return;
		}
		if ( class_exists( 'Kadence_Blocks_Frontend' ) ) {
			$ktblocks_instance = Kadence_Blocks_Frontend::get_instance();
			foreach ( self::$gfonts as $key => $gfont_values ) {
				if ( ! in_array( $key, $ktblocks_instance::$gfonts, true ) ) {
					$add_font = array(
						'fontfamily' => $gfont_values['fontfamily'],
						'fontvariants' => ( isset( $gfont_values['fontvariants'] ) && ! empty( $gfont_values['fontvariants'] ) ? $gfont_values['fontvariants'] : array() ),
						'fontsubsets' => ( isset(  $gfont_values['fontsubsets'] ) && !empty(  $gfont_values['fontsubsets'] ) ? $gfont_values['fontsubsets'] : array() ),
					);
					$ktblocks_instance::$gfonts[ $key ] = $add_font;
				} else {
					foreach ( $gfont_values['fontvariants'] as $gfontvariant_values ) {
						if ( ! in_array( $gfontvariant_values, $ktblocks_instance::$gfonts[ $key ]['fontvariants'], true ) ) {
							$ktblocks_instance::$gfonts[ $key ]['fontvariants'] = $gfontvariant_values;
						}
					}
					foreach ( $gfont_values['fontsubsets'] as $gfontsubset_values ) {
						if ( ! in_array( $gfontsubset_values, $ktblocks_instance::$gfonts[ $key ]['fontsubsets'], true ) ) {
							$ktblocks_instance::$gfonts[ $key ]['fontsubsets'] = $gfontsubset_values;
						}
					}
				}
			}
		}
	}
	/**
	 * Gets the parsed blocks, need to use this becuase wordpress 5 doesn't seem to include gutenberg_parse_blocks
	 */
	public function kadence_parse_blocks( $content ) {
		$parser_class = apply_filters( 'block_parser_class', 'WP_Block_Parser' );
		if ( class_exists( $parser_class ) ) {
			$parser = new $parser_class();
			return $parser->parse( $content );
		} elseif ( function_exists( 'gutenberg_parse_blocks' ) ) {
			return gutenberg_parse_blocks( $content );
		} else {
			return false;
		}
	}
	/**
	 * Outputs extra css for blocks.
	 */
	public function frontend_inline_css() {
		if ( function_exists( 'has_blocks' ) && has_blocks( get_the_ID() ) ) {
			global $post;
			if ( ! is_object( $post ) ) {
				return;
			}
			$this->frontend_build_css( $post );
		}
	}
	/**
	 * Registers scripts and styles.
	 */
	public function register_scripts() {
		// If in the backend, bail out.
		if ( is_admin() ) {
			return;
		}
		// Lets register all the block styles.
		wp_register_style( 'kadence-blocks-gallery-pro', KBP_URL . 'dist/build/block-css/style-gallery-styles.css', array(), KBP_VERSION );
		wp_register_style( 'kadence-blocks-image-overlay', KBP_URL . 'dist/build/block-css/style-image-overlay-styles.css', array(), KBP_VERSION );
		wp_register_style( 'kadence-blocks-modal', KBP_URL . 'dist/build/block-css/style-modal-styles.css', array(), KBP_VERSION );
		wp_register_style( 'kadence-blocks-portfolio-grid', KBP_URL . 'dist/build/block-css/style-portfolio-grid-styles.css', array(), KBP_VERSION );
		wp_register_style( 'kadence-blocks-product-carousel', KBP_URL . 'dist/build/block-css/style-product-carousel-styles.css', array(), KBP_VERSION );
		wp_register_style( 'kadence-blocks-slider', KBP_URL . 'dist/build/block-css/style-slider-styles.css', array(), KBP_VERSION );
		wp_register_style( 'kadence-blocks-split-content', KBP_URL . 'dist/build/block-css/style-split-content-styles.css', array(), KBP_VERSION );
		wp_register_style( 'kadence-blocks-video-popup', KBP_URL . 'dist/build/block-css/style-video-popup-styles.css', array(), KBP_VERSION );
		//wp_enqueue_style( 'kadence-blocks-pro-style-css', KBP_URL . 'dist/blocks.style.build.css', array(), KBP_VERSION );

		wp_register_script( 'kadence-modal', KBP_URL . 'dist/kt-modal-init.js', array(), KBP_VERSION, true );
		wp_register_style( 'kadence-blocks-pro-aos', KBP_URL . 'dist/assets/css/aos.css', array(), KBP_VERSION );
		wp_register_script( 'kadence-aos', KBP_URL . 'dist/aos.js', array(), KBP_VERSION, true );
		$configs = json_decode( get_option( 'kadence_blocks_config_blocks' ), true );
		wp_localize_script(
			'kadence-aos',
			'kadence_aos_params',
			array(
				'offset'   => ( isset( $configs ) && isset( $configs['kadence/aos'] ) && isset( $configs['kadence/aos']['offset'] ) && ! empty( $configs['kadence/aos']['offset'] ) ? $configs['kadence/aos']['offset'] : 120 ),
				'duration' => ( isset( $configs ) && isset( $configs['kadence/aos'] ) && isset( $configs['kadence/aos']['duration'] ) && ! empty( $configs['kadence/aos']['duration'] ) ? $configs['kadence/aos']['duration'] : 400 ),
				'easing'   => ( isset( $configs ) && isset( $configs['kadence/aos'] ) && isset( $configs['kadence/aos']['ease'] ) ? $configs['kadence/aos']['ease'] : 'ease' ),
				'delay'    => ( isset( $configs ) && isset( $configs['kadence/aos'] ) && isset( $configs['kadence/aos']['delay'] ) ? $configs['kadence/aos']['delay'] : 0 ),
				'once'     => ( isset( $configs ) && isset( $configs['kadence/aos'] ) && isset( $configs['kadence/aos']['once'] ) ? $configs['kadence/aos']['once'] : false ),
			)
		);
		// $aos_script = "AOS.init();";
		// wp_add_inline_script( 'kadence-aos', $aos_script, 'after' );
		//wp_register_script( 'kadence-blocks-pro-aos-init', KBP_URL . 'dist/kt-init-aos.js', array( 'kadence-aos' ), KBP_VERSION, true );
		// wp_register_style( 'kadence-simplelightbox-css', KBP_URL . 'dist/assets/css/simplelightbox.css', array(), KBP_VERSION );
		// wp_register_script( 'kadence-simplelightbox', KBP_URL . 'dist/assets/js/simplelightbox.min.js', array(), KBP_VERSION, true );
		// wp_register_script( 'kadence-blocks-pro-videolight-js', KBP_URL . 'dist/assets/js/kb-video-pop-init.min.js', array( 'kadence-simplelightbox' ), KBP_VERSION, true );
		wp_register_script( 'kadence-slick', KBP_URL . 'dist/vendor/slick.min.js', array( 'jquery' ), KBP_VERSION, true );
		wp_register_script( 'kad-splide', KBP_URL . 'dist/assets/js/splide.min.js', array(), KBP_VERSION, true );
		wp_register_script( 'kadence-splide-auto-scroll', KBP_URL . 'dist/assets/js/splide-auto-scroll.min.js', array(), KBP_VERSION, true );
		wp_register_script( 'kadence-flickity', KBP_URL . 'dist/assets/js/flickity.min.js', array(), KBP_VERSION, true );
		wp_register_script( 'kadence-blocks-pro-slick-init', KBP_URL . 'dist/kt-slick-init.js', array( 'jquery', 'kadence-slick' ), KBP_VERSION, true );
		wp_register_script( 'kadence-blocks-pro-splide-init', KBP_URL . 'dist/assets/js/kb-splide-init.min.js', array( 'kad-splide' ), KBP_VERSION, true );
		wp_register_script( 'kadence-blocks-pro-slider-init', KBP_URL . 'dist/kb-slider-init.js', array( 'jquery', 'kadence-slick' ), KBP_VERSION, true );
		wp_register_style( 'kadence-blocks-magnific-css', KBP_URL . 'dist/magnific.css', array(), KBP_VERSION );
		wp_register_script( 'magnific-popup', KBP_URL . 'dist/magnific.js', array( 'jquery' ), KBP_VERSION, true );
		wp_register_script( 'kadence-blocks-pro-video-pop-init', KBP_URL . 'dist/kb-video-pop-init.js', array( 'jquery', 'magnific-popup' ), KBP_VERSION, true );

		wp_register_style( 'kadence-glightbox', KBP_URL . 'dist/assets/css/kb-glightbox.min.css', array(), KBP_VERSION );
		wp_register_script( 'kadence-glightbox', KBP_URL . 'dist/assets/js/glightbox.min.js', array(), KBP_VERSION, true );
		wp_register_script( 'kadence-blocks-pro-glight-video-init', KBP_URL . 'dist/assets/js/kb-glight-video-pop-init.min.js', array( 'kadence-glightbox' ), KBP_VERSION, true );
		$pop_video_array = array(
			'plyr_js'          => KBP_URL . 'dist/assets/js/plyr.min.js',
			'plyr_css'         => KBP_URL . 'dist/assets/css/plyr.min.css',
		);
		wp_localize_script( 'kadence-blocks-pro-glight-video-init', 'kadence_pro_video_pop', $pop_video_array );

		wp_register_script( 'kadence-blocks-isotope', KBP_URL . 'dist/vendor/isotope.pkgd.min.js', array(), KBP_VERSION, true );
		wp_register_script( 'kadence-blocks-pro-iso-init', KBP_URL . 'dist/kb-iso-init.js', array( 'kadence-blocks-isotope' ), KBP_VERSION, true );
		wp_register_script( 'kadence-blocks-pro-iso-post-init', KBP_URL . 'dist/kb-iso-post-init.js', array( 'kadence-blocks-isotope' ), KBP_VERSION, true );
		wp_register_script( 'kadence-blocks-pro-masonry-init', KBP_URL . 'dist/kt-masonry-init.js', array( 'masonry' ), KBP_VERSION, true );
		wp_register_style( 'kadence-blocks-pro-slick', KBP_URL . 'dist/vendor/kt-blocks-slick.css', array(), KBP_VERSION );
		wp_register_style( 'kadence-flickity', KBP_URL . 'dist/assets/css/flickity.min.css', array(), KBP_VERSION );
		wp_register_style( 'kad-splide', KBP_URL . 'dist/assets/css/kadence-splide.min.css', array(), KBP_VERSION );
	}
	/**
	 * Registers and enqueue's script.
	 *
	 * @param string  $handle the handle for the script.
	 */
	public function enqueue_script( $handle ) {
		if ( ! wp_script_is( $handle, 'registered' ) ) {
			$this->register_scripts();
		}
		wp_enqueue_script( $handle );
	}
	/**
	 * Registers and enqueue's styles.
	 *
	 * @param string  $handle the handle for the script.
	 */
	public function enqueue_style( $handle ) {
		if ( ! wp_style_is( $handle, 'registered' ) ) {
			$this->register_scripts();
		}
		wp_enqueue_style( $handle );
	}
	/**
	 * Outputs extra css for blocks.
	 *
	 * @param $post_object object of WP_Post.
	 */
	public function frontend_build_css( $post_object ) {
		if ( ! is_object( $post_object ) ) {
			return;
		}
		if ( ! method_exists( $post_object, 'post_content' ) ) {
			$blocks = $this->kadence_parse_blocks( $post_object->post_content );
			//print_r($blocks );
			if ( ! is_array( $blocks ) || empty( $blocks ) ) {
				return;
			}
			foreach ( $blocks as $indexkey => $block ) {
				$block = apply_filters( 'kadence_blocks_frontend_build_css', $block );
				if ( ! is_object( $block ) && is_array( $block ) && isset( $block['blockName'] ) ) {
					if ( 'kadence/imageoverlay' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							$this->render_image_overlay_css_head( $blockattr );
							$this->blocks_image_overlay_googlefont_check( $blockattr );
						}
					}
					if ( 'kadence/userinfo' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							$this->blocks_userinfo_scripts_check( $blockattr );
						}
					}
					if ( 'kadence/dynamiclist' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							$this->blocks_dynamiclist_scripts_check( $blockattr );
						}
					}
					if ( 'kadence/dynamichtml' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							$this->blocks_dynamichtml_scripts_check( $blockattr );
						}
					}
					if ( 'kadence/splitcontent' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							$this->render_split_content_css_head( $blockattr );
						}
					}
					if ( 'kadence/modal' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							$this->blocks_modal_scripts_check( $blockattr );
							$this->render_modal_css_head( $blockattr );
						}
					}
					if ( 'kadence/videopopup' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							$this->blocks_video_popup_scripts_check( $blockattr );
							$this->render_video_popup_css_head( $blockattr );
						}
					}
					if ( 'kadence/slider' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							$this->blocks_slider_scripts_check( $blockattr );
							$this->render_slider_css_head( $blockattr );
						}
					}
					if ( 'kadence/slide' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							$this->render_slide_css_head( $blockattr );
						}
					}
					if ( 'kadence/advancedgallery' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							if ( isset( $blockattr['type'] ) && ( 'thumbslider' === $blockattr['type'] || 'tiles' === $blockattr['type'] ) ) {
								if ( ! wp_style_is( 'kadence-blocks-gallery-pro', 'enqueued' ) ) {
									$this->enqueue_style( 'kadence-blocks-gallery-pro' );
								}
							}
						}
					}
					if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) && isset( $block['attrs']['kadenceAnimation'] ) && ! empty( $block['attrs']['kadenceAnimation'] ) ) {
						$this->enqueue_script( 'kadence-aos' );
						$this->enqueue_style( 'kadence-blocks-pro-aos' );
					}
					if ( 'kadence/postgrid' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							$this->render_post_grid_carousel_css_head( $blockattr );
						}
					}
					if ( 'kadence/portfoliogrid' === $block['blockName'] ) {
						if ( ! wp_style_is( 'kadence-blocks-portfolio-grid', 'enqueued' ) ) {
							$this->enqueue_style( 'kadence-blocks-portfolio-grid' );
						}
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							$this->blocks_portfolio_googlefont_scripts_check( $blockattr );
						}
					}
					if ( 'kadence/productcarousel' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							$this->render_product_carousel_css_head( $blockattr );
						}
					}
					if ( 'core/block' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							if ( isset( $blockattr['ref'] ) ) {
								$reusable_block = get_post( $blockattr['ref'] );
								if ( $reusable_block && 'wp_block' == $reusable_block->post_type ) {
									$reuse_data_block = $this->kadence_parse_blocks( $reusable_block->post_content );
									$this->blocks_cycle_through( $reuse_data_block );
								}
							}
						}
					}
					if ( isset( $block['innerBlocks'] ) && ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
						$this->blocks_cycle_through( $block['innerBlocks'] );
					}
				}
			}
		}
	}
	/**
	 * Builds css for inner blocks
	 *
	 * @param array $inner_blocks array of inner blocks.
	 */
	public function blocks_cycle_through( $inner_blocks ) {
		foreach ( $inner_blocks as $in_indexkey => $inner_block ) {
			$inner_block = apply_filters( 'kadence_blocks_frontend_build_css', $inner_block );
			if ( ! is_object( $inner_block ) && is_array( $inner_block ) && isset( $inner_block['blockName'] ) ) {
				if ( isset( $inner_block['blockName'] ) ) {
					if ( 'kadence/imageoverlay' === $inner_block['blockName'] ) {
						if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
							$blockattr = $inner_block['attrs'];
							$this->render_image_overlay_css_head( $blockattr );
							$this->blocks_image_overlay_googlefont_check( $blockattr );
						}
					}
					if ( 'kadence/userinfo' === $inner_block['blockName'] ) {
						if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
							$blockattr = $inner_block['attrs'];
							$this->blocks_userinfo_scripts_check( $blockattr );
						}
					}
					if ( 'kadence/dynamiclist' === $inner_block['blockName'] ) {
						if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
							$blockattr = $inner_block['attrs'];
							$this->blocks_dynamiclist_scripts_check( $blockattr );
						}
					}
					if ( 'kadence/dynamichtml' === $inner_block['blockName'] ) {
						if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
							$blockattr = $inner_block['attrs'];
							$this->blocks_dynamichtml_scripts_check( $blockattr );
						}
					}
					if ( 'kadence/splitcontent' === $inner_block['blockName'] ) {
						if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
							$blockattr = $inner_block['attrs'];
							$this->render_split_content_css_head( $blockattr );
						}
					}
					if ( 'kadence/modal' === $inner_block['blockName'] ) {
						if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
							$blockattr = $inner_block['attrs'];
							$this->blocks_modal_scripts_check( $blockattr );
							$this->render_modal_css_head( $blockattr );
						}
					}
					if ( 'kadence/videopopup' === $inner_block['blockName'] ) {
						if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
							$blockattr = $inner_block['attrs'];
							$this->blocks_video_popup_scripts_check( $blockattr );
							$this->render_video_popup_css_head( $blockattr );
						}
					}
					if ( 'kadence/slider' === $inner_block['blockName'] ) {
						if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
							$blockattr = $inner_block['attrs'];
							$this->blocks_slider_scripts_check( $blockattr );
							$this->render_slider_css_head( $blockattr );
						}
					}
					if ( 'kadence/slide' === $inner_block['blockName'] ) {
						if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
							$blockattr = $inner_block['attrs'];
							$this->render_slide_css_head( $blockattr );
						}
					}
					if ( 'kadence/postgrid' === $inner_block['blockName'] ) {
						if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
							$blockattr = $inner_block['attrs'];
							$this->render_post_grid_carousel_css_head( $blockattr );
						}
					}
					if ( 'kadence/portfoliogrid' === $inner_block['blockName'] ) {
						if ( ! wp_style_is( 'kadence-blocks-portfolio-grid', 'enqueued' ) ) {
							$this->enqueue_style( 'kadence-blocks-portfolio-grid' );
						}
						if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
							$blockattr = $inner_block['attrs'];
							$this->blocks_portfolio_googlefont_scripts_check( $blockattr );
						}
					}
					if ( 'kadence/productcarousel' === $inner_block['blockName'] ) {
						if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
							$blockattr = $inner_block['attrs'];
							$this->render_product_carousel_css_head( $blockattr );
						}
					}
					if ( 'kadence/advancedgallery' === $inner_block['blockName'] ) {
						if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
							$blockattr = $inner_block['attrs'];
							if ( isset( $blockattr['type'] ) && ( 'thumbslider' === $blockattr['type'] || 'tiles' === $blockattr['type'] ) ) {
								if ( ! wp_style_is( 'kadence-blocks-gallery-pro', 'enqueued' ) ) {
									$this->enqueue_style( 'kadence-blocks-gallery-pro' );
								}
							}
						}
					}
					if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) && isset( $inner_block['attrs']['kadenceAnimation'] ) && ! empty( $inner_block['attrs']['kadenceAnimation'] ) ) {
						$this->enqueue_style( 'kadence-blocks-pro-aos' );
						$this->enqueue_script( 'kadence-aos' );
					}
					if ( 'core/block' === $inner_block['blockName'] ) {
						if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
							$blockattr = $inner_block['attrs'];
							if ( isset( $blockattr['ref'] ) ) {
								$reusable_block = get_post( $blockattr['ref'] );
								if ( $reusable_block && 'wp_block' == $reusable_block->post_type ) {
									$reuse_data_block = $this->kadence_parse_blocks( $reusable_block->post_content );
									$this->blocks_cycle_through( $reuse_data_block );
								}
							}
						}
					}
				}
				if ( isset( $inner_block['innerBlocks'] ) && ! empty( $inner_block['innerBlocks'] ) && is_array( $inner_block['innerBlocks'] ) ) {
					$this->blocks_cycle_through( $inner_block['innerBlocks'] );
				}
			}
		}
	}
	/**
	 * Grabs the scripts that are needed so we can load in the head.
	 *
	 * @param array $attr the blocks attr.
	 */
	public function blocks_dynamichtml_scripts_check( $attr ) {
		if ( ! class_exists( 'Kadence_Blocks_Pro_Dynamic_HTML_Block' ) ) {
			return false;
		}
		$dynamic_html = Kadence_Blocks_Pro_Dynamic_HTML_Block::get_instance();
		$dynamic_html->enqueue_style( 'kadence-blocks-dynamic-html' );
		if ( isset( $attr['uniqueID'] ) ) {
			$unique_id = $attr['uniqueID'];
			$style_id = 'kt-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) ) {
				$css = $dynamic_html->output_css( $attr, $unique_id );
				if ( ! empty( $css ) ) {
					$this->render_inline_css( $css, $style_id );
				}
			}
		}
		if ( ! empty( $dynamic_html::$gfonts ) ) {
			foreach ( $dynamic_html::$gfonts as $key => $gfont_values ) {
				if ( ! in_array( $key, self::$gfonts, true ) ) {
					$add_font = array(
						'fontfamily' => $gfont_values['fontfamily'],
						'fontvariants' => ( isset( $gfont_values['fontvariants'] ) && ! empty( $gfont_values['fontvariants'] ) ? $gfont_values['fontvariants'] : array() ),
						'fontsubsets' => ( isset( $gfont_values['fontsubsets'] ) && ! empty( $gfont_values['fontsubsets'] ) ? $gfont_values['fontsubsets'] : array() ),
					);
					self::$gfonts[ $key ] = $add_font;
				} else {
					foreach ( $gfont_values['fontvariants'] as $gfontvariant_values ) {
						if ( ! in_array( $gfontvariant_values, self::$gfonts[ $key ]['fontvariants'], true ) ) {
							self::$gfonts[ $key ]['fontvariants'] = $gfontvariant_values;
						}
					}
					foreach ( $gfont_values['fontsubsets'] as $gfontsubset_values ) {
						if ( ! in_array( $gfontsubset_values, self::$gfonts[ $key ]['fontsubsets'], true ) ) {
							self::$gfonts[ $key ]['fontsubsets'] = $gfontsubset_values;
						}
					}
				}
			}
		}
	}
	/**
	 * Grabs the scripts that are needed so we can load in the head.
	 *
	 * @param array $attr the blocks attr.
	 */
	public function blocks_dynamiclist_scripts_check( $attr ) {
		if ( ! class_exists( 'Kadence_Blocks_Pro_Dynamic_List' ) ) {
			return false;
		}
		$dynamic_list = Kadence_Blocks_Pro_Dynamic_List::get_instance();
		$dynamic_list->enqueue_style( 'kadence-blocks-dynamic-list' );
		if ( isset( $attr['uniqueID'] ) ) {
			$unique_id = $attr['uniqueID'];
			$style_id = 'kt-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) ) {
				$css = $dynamic_list->output_css( $attr, $unique_id );
				if ( ! empty( $css ) ) {
					$this->render_inline_css( $css, $style_id );
				}
			}
		}
		if ( ! empty( $dynamic_list::$gfonts ) ) {
			foreach ( $dynamic_list::$gfonts as $key => $gfont_values ) {
				if ( ! in_array( $key, self::$gfonts, true ) ) {
					$add_font = array(
						'fontfamily' => $gfont_values['fontfamily'],
						'fontvariants' => ( isset( $gfont_values['fontvariants'] ) && ! empty( $gfont_values['fontvariants'] ) ? $gfont_values['fontvariants'] : array() ),
						'fontsubsets' => ( isset( $gfont_values['fontsubsets'] ) && ! empty( $gfont_values['fontsubsets'] ) ? $gfont_values['fontsubsets'] : array() ),
					);
					self::$gfonts[ $key ] = $add_font;
				} else {
					foreach ( $gfont_values['fontvariants'] as $gfontvariant_values ) {
						if ( ! in_array( $gfontvariant_values, self::$gfonts[ $key ]['fontvariants'], true ) ) {
							self::$gfonts[ $key ]['fontvariants'] = $gfontvariant_values;
						}
					}
					foreach ( $gfont_values['fontsubsets'] as $gfontsubset_values ) {
						if ( ! in_array( $gfontsubset_values, self::$gfonts[ $key ]['fontsubsets'], true ) ) {
							self::$gfonts[ $key ]['fontsubsets'] = $gfontsubset_values;
						}
					}
				}
			}
		}
	}
	/**
	 * Grabs the scripts that are needed so we can load in the head.
	 *
	 * @param array $attr the blocks attr.
	 */
	public function blocks_userinfo_scripts_check( $attr ) {
		if ( ! class_exists( 'Kadence_Blocks_Pro_User_Info' ) ) {
			return false;
		}
		$user_info = Kadence_Blocks_Pro_User_Info::get_instance();
		$user_info->enqueue_style( 'kadence-blocks-user-info' );
		if ( isset( $attr['uniqueID'] ) ) {
			$unique_id = $attr['uniqueID'];
			$style_id = 'kt-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) ) {
				$css = $user_info->output_css( $attr, $unique_id );
				if ( ! empty( $css ) ) {
					$this->render_inline_css( $css, $style_id );
				}
			}
		}
		if ( ! empty( $user_info::$gfonts ) ) {
			foreach ( $user_info::$gfonts as $key => $gfont_values ) {
				if ( ! in_array( $key, self::$gfonts, true ) ) {
					$add_font = array(
						'fontfamily' => $gfont_values['fontfamily'],
						'fontvariants' => ( isset( $gfont_values['fontvariants'] ) && ! empty( $gfont_values['fontvariants'] ) ? $gfont_values['fontvariants'] : array() ),
						'fontsubsets' => ( isset( $gfont_values['fontsubsets'] ) && ! empty( $gfont_values['fontsubsets'] ) ? $gfont_values['fontsubsets'] : array() ),
					);
					self::$gfonts[ $key ] = $add_font;
				} else {
					foreach ( $gfont_values['fontvariants'] as $gfontvariant_values ) {
						if ( ! in_array( $gfontvariant_values, self::$gfonts[ $key ]['fontvariants'], true ) ) {
							self::$gfonts[ $key ]['fontvariants'] = $gfontvariant_values;
						}
					}
					foreach ( $gfont_values['fontsubsets'] as $gfontsubset_values ) {
						if ( ! in_array( $gfontsubset_values, self::$gfonts[ $key ]['fontsubsets'], true ) ) {
							self::$gfonts[ $key ]['fontsubsets'] = $gfontsubset_values;
						}
					}
				}
			}
		}
	}

	/**
	 * Grabs the Google Fonts that are needed so we can load in the head.
	 *
	 * @param array $attr the blocks attr.
	 */
	public function blocks_portfolio_googlefont_scripts_check( $attr ) {
		if ( ( isset( $attr['layout'] ) && 'masonry' === $attr['layout'] || isset( $attr['layout'] ) && 'grid' === $attr['layout'] || ! isset( $attr['layout'] ) ) && isset( $attr['displayFilter'] ) && true === $attr['displayFilter'] ) {
			$this->enqueue_script( 'kadence-blocks-pro-iso-init' );
		} elseif ( isset( $attr['layout'] ) && 'masonry' === $attr['layout'] ) {
			$this->enqueue_script( 'kadence-blocks-pro-masonry-init' );
		} elseif ( isset( $attr['layout'] ) && ( 'carousel' === $attr['layout'] || 'fluidcarousel' === $attr['layout'] ) ) {
			$this->enqueue_style( 'kadence-blocks-pro-slick' );
			$this->enqueue_script( 'kadence-blocks-pro-slick-init' );
		}
		if ( isset( $attr['titleFont'] ) && is_array( $attr['titleFont'] ) && isset( $attr['titleFont'][0] ) && is_array( $attr['titleFont'][0] ) && isset( $attr['titleFont'][0]['google'] ) && $attr['titleFont'][0]['google'] && ( ! isset( $attr['titleFont'][0]['loadGoogle'] ) || true === $attr['titleFont'][0]['loadGoogle'] ) && isset( $attr['titleFont'][0]['family'] ) ) {
			$title_font = $attr['titleFont'][0];
			// Check if the font has been added yet.
			if ( ! array_key_exists( $title_font['family'], self::$gfonts ) ) {
				$add_font = array(
					'fontfamily' => $title_font['family'],
					'fontvariants' => ( isset( $title_font['variant'] ) && ! empty( $title_font['variant'] ) ? array( $title_font['variant'] ) : array() ),
					'fontsubsets' => ( isset( $title_font['subset'] ) && ! empty( $title_font['subset'] ) ? array( $title_font['subset'] ) : array() ),
				);
				self::$gfonts[ $title_font['family'] ] = $add_font;
			} else {
				if ( ! in_array( $title_font['variant'], self::$gfonts[ $title_font['family'] ]['fontvariants'], true ) ) {
					array_push( self::$gfonts[ $title_font['family'] ]['fontvariants'], $title_font['variant'] );
				}
				if ( ! in_array( $title_font['subset'], self::$gfonts[ $title_font['family'] ]['fontsubsets'], true ) ) {
					array_push( self::$gfonts[ $title_font['family'] ]['fontsubsets'], $title_font['subset'] );
				}
			}
		}
		if ( isset( $attr['taxFont'] ) && is_array( $attr['taxFont'] ) && isset( $attr['taxFont'][0] ) && is_array( $attr['taxFont'][0] ) && isset( $attr['taxFont'][0]['google'] ) && $attr['taxFont'][0]['google'] && ( ! isset( $attr['taxFont'][0]['loadGoogle'] ) || true === $attr['taxFont'][0]['loadGoogle'] ) && isset( $attr['taxFont'][0]['family'] ) ) {
			$tax_font = $attr['taxFont'][0];
			// Check if the font has been added yet.
			if ( ! array_key_exists( $tax_font['family'], self::$gfonts ) ) {
				$add_font = array(
					'fontfamily' => $tax_font['family'],
					'fontvariants' => ( isset( $tax_font['variant'] ) && ! empty( $tax_font['variant'] ) ? array( $tax_font['variant'] ) : array() ),
					'fontsubsets' => ( isset( $tax_font['subset'] ) && ! empty( $tax_font['subset'] ) ? array( $tax_font['subset'] ) : array() ),
				);
				self::$gfonts[ $tax_font['family'] ] = $add_font;
			} else {
				if ( ! in_array( $tax_font['variant'], self::$gfonts[ $tax_font['family'] ]['fontvariants'], true ) ) {
					array_push( self::$gfonts[ $tax_font['family'] ]['fontvariants'], $tax_font['variant'] );
				}
				if ( ! in_array( $tax_font['subset'], self::$gfonts[ $tax_font['family'] ]['fontsubsets'], true ) ) {
					array_push( self::$gfonts[ $tax_font['family'] ]['fontsubsets'], $tax_font['subset'] );
				}
			}
		}
		if ( isset( $attr['excerptFont'] ) && is_array( $attr['excerptFont'] ) && isset( $attr['excerptFont'][0] ) && is_array( $attr['excerptFont'][0] ) && isset( $attr['excerptFont'][0]['google'] ) && $attr['excerptFont'][0]['google'] && ( ! isset( $attr['excerptFont'][0]['loadGoogle'] ) || true === $attr['excerptFont'][0]['loadGoogle'] ) && isset( $attr['excerptFont'][0]['family'] ) ) {
			$excerpt_font = $attr['excerptFont'][0];
			// Check if the font has been added yet.
			if ( ! array_key_exists( $excerpt_font['family'], self::$gfonts ) ) {
				$add_font = array(
					'fontfamily' => $excerpt_font['family'],
					'fontvariants' => ( isset( $excerpt_font['variant'] ) && ! empty( $excerpt_font['variant'] ) ? array( $excerpt_font['variant'] ) : array() ),
					'fontsubsets' => ( isset( $excerpt_font['subset'] ) && ! empty( $excerpt_font['subset'] ) ? array( $excerpt_font['subset'] ) : array() ),
				);
				self::$gfonts[ $excerpt_font['family'] ] = $add_font;
			} else {
				if ( ! in_array( $excerpt_font['variant'], self::$gfonts[ $excerpt_font['family'] ]['fontvariants'], true ) ) {
					array_push( self::$gfonts[ $excerpt_font['family'] ]['fontvariants'], $excerpt_font['variant'] );
				}
				if ( ! in_array( $excerpt_font['subset'], self::$gfonts[ $excerpt_font['family'] ]['fontsubsets'], true ) ) {
					array_push( self::$gfonts[ $excerpt_font['family'] ]['fontsubsets'], $excerpt_font['subset'] );
				}
			}
		}
		if ( isset( $attr['filterFont'] ) && is_array( $attr['filterFont'] ) && isset( $attr['filterFont'][0] ) && is_array( $attr['filterFont'][0] ) && isset( $attr['filterFont'][0]['google'] ) && $attr['filterFont'][0]['google'] && ( ! isset( $attr['filterFont'][0]['loadGoogle'] ) || true === $attr['filterFont'][0]['loadGoogle'] ) && isset( $attr['filterFont'][0]['family'] ) ) {
			$filter_font = $attr['filterFont'][0];
			// Check if the font has been added yet.
			if ( ! array_key_exists( $filter_font['family'], self::$gfonts ) ) {
				$add_font = array(
					'fontfamily' => $filter_font['family'],
					'fontvariants' => ( isset( $filter_font['variant'] ) && ! empty( $filter_font['variant'] ) ? array( $filter_font['variant'] ) : array() ),
					'fontsubsets' => ( isset( $filter_font['subset'] ) && ! empty( $filter_font['subset'] ) ? array( $filter_font['subset'] ) : array() ),
				);
				self::$gfonts[ $filter_font['family'] ] = $add_font;
			} else {
				if ( ! in_array( $filter_font['variant'], self::$gfonts[ $filter_font['family'] ]['fontvariants'], true ) ) {
					array_push( self::$gfonts[ $filter_font['family'] ]['fontvariants'], $filter_font['variant'] );
				}
				if ( ! in_array( $filter_font['subset'], self::$gfonts[ $filter_font['family'] ]['fontsubsets'], true ) ) {
					array_push( self::$gfonts[ $filter_font['family'] ]['fontsubsets'], $filter_font['subset'] );
				}
			}
		}
	}
	/**
	 * Grabs the Google Fonts and scripts that are needed so we can load in the head.
	 *
	 * @param array $attr the blocks attr.
	 */
	public function blocks_modal_scripts_check( $attr ) {
		$this->enqueue_script( 'kadence-modal' );
		if ( isset( $attr['modalLinkStyles'] ) && is_array( $attr['modalLinkStyles'] ) && isset( $attr['modalLinkStyles'][0] ) && is_array( $attr['modalLinkStyles'][0] ) && isset( $attr['modalLinkStyles'][0]['google'] ) && $attr['modalLinkStyles'][0]['google'] && ( ! isset( $attr['modalLinkStyles'][0]['loadGoogle'] ) || true === $attr['modalLinkStyles'][0]['loadGoogle'] ) && isset( $attr['modalLinkStyles'][0]['family'] ) ) {
			$modal_link_font = $attr['modalLinkStyles'][0];
			// Check if the font has been added yet.
			if ( ! array_key_exists( $modal_link_font['family'], self::$gfonts ) ) {
				$add_font = array(
					'fontfamily' => $modal_link_font['family'],
					'fontvariants' => ( isset( $modal_link_font['variant'] ) && ! empty( $modal_link_font['variant'] ) ? array( $modal_link_font['variant'] ) : array() ),
					'fontsubsets' => ( isset( $modal_link_font['subset'] ) && ! empty( $modal_link_font['subset'] ) ? array( $modal_link_font['subset'] ) : array() ),
				);
				self::$gfonts[ $modal_link_font['family'] ] = $add_font;
			} else {
				if ( ! in_array( $modal_link_font['variant'], self::$gfonts[ $modal_link_font['family'] ]['fontvariants'], true ) ) {
					array_push( self::$gfonts[ $modal_link_font['family'] ]['fontvariants'], $modal_link_font['variant'] );
				}
				if ( ! in_array( $modal_link_font['subset'], self::$gfonts[ $modal_link_font['family'] ]['fontsubsets'], true ) ) {
					array_push( self::$gfonts[ $modal_link_font['family'] ]['fontsubsets'], $modal_link_font['subset'] );
				}
			}
		}
	}
	/**
	 * Grabs the Google Fonts and scripts that are needed so we can load in the head.
	 *
	 * @param array $attr the blocks attr.
	 */
	public function blocks_video_popup_scripts_check( $attr ) {
		$this->enqueue_style( 'kadence-glightbox' );
		$this->enqueue_script( 'kadence-blocks-pro-glight-video-init' );
		if ( isset( $attr['modalLinkStyles'] ) && is_array( $attr['modalLinkStyles'] ) && isset( $attr['modalLinkStyles'][0] ) && is_array( $attr['modalLinkStyles'][0] ) && isset( $attr['modalLinkStyles'][0]['google'] ) && $attr['modalLinkStyles'][0]['google'] && ( ! isset( $attr['modalLinkStyles'][0]['loadGoogle'] ) || true === $attr['modalLinkStyles'][0]['loadGoogle'] ) &&  isset( $attr['modalLinkStyles'][0]['family'] ) ) {
			$modal_link_font = $attr['modalLinkStyles'][0];
			// Check if the font has been added yet
			if ( ! array_key_exists( $modal_link_font['family'], self::$gfonts ) ) {
				$add_font = array(
					'fontfamily' => $modal_link_font['family'],
					'fontvariants' => ( isset( $modal_link_font['variant'] ) && ! empty( $modal_link_font['variant'] ) ? array( $modal_link_font['variant'] ) : array() ),
					'fontsubsets' => ( isset( $modal_link_font['subset'] ) && !empty( $modal_link_font['subset'] ) ? array( $modal_link_font['subset'] ) : array() ),
				);
				self::$gfonts[ $modal_link_font['family'] ] = $add_font;
			} else {
				if ( ! in_array( $modal_link_font['variant'], self::$gfonts[ $modal_link_font['family'] ]['fontvariants'], true ) ) {
					array_push( self::$gfonts[ $modal_link_font['family'] ]['fontvariants'], $modal_link_font['variant'] );
				}
				if ( ! in_array( $modal_link_font['subset'], self::$gfonts[ $modal_link_font['family'] ]['fontsubsets'], true ) ) {
					array_push( self::$gfonts[ $modal_link_font['family'] ]['fontsubsets'], $modal_link_font['subset'] );
				}
			}
		}
	}
	/**
	 * Builds CSS for slide block.
	 *
	 * @param array  $attr the blocks attr.
	 * @param string $unique_id the blocks attr ID.
	 */
	public function blocks_slide_array( $attr, $unique_id ) {
		$css = '';
		if ( isset( $attr['textColor'] ) && ! empty( $attr['textColor'] ) ) {
			$css .= '.wp-block-kadence-slider .kb-slide-' . $unique_id . ' h1, .wp-block-kadence-slider .kb-slide-' . $unique_id . ' h2, .wp-block-kadence-slider .kb-slide-' . $unique_id . ' h3, .wp-block-kadence-slider .kb-slide-' . $unique_id . ' h4, .wp-block-kadence-slider .kb-slide-' . $unique_id . ' h5, .wp-block-kadence-slider .kb-slide-' . $unique_id . ' h6, .wp-block-kadence-slider .kb-slide-' . $unique_id . ' {';
			$css .= 'color:' . $this->kadence_color_output( $attr['textColor'] ) . ';';
			$css .= '}';
		}
		if ( isset( $attr['linkColor'] ) && ! empty( $attr['linkColor'] ) ) {
			$css .= '.wp-block-kadence-slider .kb-slide-' . $unique_id . ' a {';
			$css .= 'color:' . $this->kadence_color_output( $attr['linkColor'] ) . ';';
			$css .= '}';
		}
		if ( isset( $attr['linkHoverColor'] ) && ! empty( $attr['linkHoverColor'] ) ) {
			$css .= '.wp-block-kadence-slider .kb-slide-' . $unique_id . ' a:hover {';
			$css .= 'color:' . $this->kadence_color_output( $attr['linkHoverColor'] ) . ';';
			$css .= '}';
		}
		return $css;
	}
	/**
	 * Grabs the Google Fonts and scripts that are needed so we can load in the head.
	 *
	 * @param array $attr the blocks attr.
	 */
	public function blocks_slider_scripts_check( $attr ) {
		$this->enqueue_style( 'kadence-blocks-pro-slick' );
		$this->enqueue_script( 'kadence-blocks-pro-slider-init' );
	}
	/**
	 * Builds CSS for slider block.
	 *
	 * @param array  $attr the blocks attr.
	 * @param string $unique_id the blocks attr ID.
	 */
	public function blocks_slider_array( $attr, $unique_id ) {
		$css = '';
		$margin_unit = ( isset( $attr['marginUnit'] ) && ! empty( $attr['marginUnit'] ) ? $attr['marginUnit'] : 'px' );
		$padding_unit = ( isset( $attr['paddingUnit'] ) && ! empty( $attr['paddingUnit'] ) ? $attr['paddingUnit'] : 'px' );
		$height_unit = ( isset( $attr['heightUnit'] ) && ! empty( $attr['heightUnit'] ) ? $attr['heightUnit'] : 'px' );
		if ( isset( $attr['heightType'] ) && 'fixed' === $attr['heightType'] && isset( $attr['minHeight'] ) && is_array( $attr['minHeight'] ) && ! empty( $attr['minHeight'][0] ) ) {
			$css .= '.kb-advanced-slider-' . $unique_id . ' .kb-slider-size-fixed .kb-advanced-slide-inner-wrap {';
			$css .= 'min-height: ' . $attr['minHeight'][0] . $height_unit . ';';
			$css .= '}';
			if ( ! empty( $attr['minHeight'][1] ) ) {
				$css .= '@media (max-width: 1024px) {';
				$css .= '.kb-advanced-slider-' . $unique_id . ' .kb-slider-size-fixed .kb-advanced-slide-inner-wrap {';
				$css .= 'min-height: ' . $attr['minHeight'][1] . $height_unit . ';';
				$css .= '}';
				$css .= '}';
			}
			if ( ! empty( $attr['minHeight'][2] ) ) {
				$css .= '@media (max-width: 767px) {';
				$css .= '.kb-advanced-slider-' . $unique_id . ' .kb-slider-size-fixed .kb-advanced-slide-inner-wrap {';
				$css .= 'min-height: ' . $attr['minHeight'][2] . $height_unit . ';';
				$css .= '}';
				$css .= '}';
			}
		}
		if ( isset( $attr['maxWidth'] ) && is_array( $attr['maxWidth'] ) && ! empty( $attr['maxWidth'][0] ) ) {
			$css .= '.kb-advanced-slider-' . $unique_id . ' .kb-advanced-slide-inner {';
			$css .= 'max-width: ' . $attr['maxWidth'][0] . ( isset( $attr['widthUnit'] ) && ! empty( $attr['widthUnit'] ) ? $attr['widthUnit'] : 'px' ) . ';';
			$css .= '}';
			if ( ! empty( $attr['maxWidth'][1] ) ) {
				$css .= '@media (max-width: 1024px) {';
				$css .= '.kb-advanced-slider-' . $unique_id . ' .kb-advanced-slide-inner {';
				$css .= 'max-width: ' . $attr['maxWidth'][1] . ( isset( $attr['widthUnit'] ) && ! empty( $attr['widthUnit'] ) ? $attr['widthUnit'] : 'px' ) . ';';
				$css .= '}';
				$css .= '}';
			}
			if ( ! empty( $attr['maxWidth'][2] ) ) {
				$css .= '@media (max-width: 767px) {';
				$css .= '.kb-advanced-slider-' . $unique_id . ' .kb-advanced-slide-inner {';
				$css .= 'max-width: ' . $attr['maxWidth'][2] . ( isset( $attr['widthUnit'] ) && ! empty( $attr['widthUnit'] ) ? $attr['widthUnit'] : 'px' ) . ';';
				$css .= '}';
				$css .= '}';
			}
		}
		if ( isset( $attr['padding'] ) && is_array( $attr['padding'] ) && is_array( $attr['padding'][0] ) ) {
			$padding = $attr['padding'][0];
			if ( isset( $padding['desk'] ) && is_array( $padding['desk'] ) && is_numeric( $padding['desk'][0] ) ) {
				$css .= '.kb-advanced-slider-' . $unique_id . ' .kb-advanced-slide-inner-wrap {';
				$css .= 'padding:' . $padding['desk'][0] . $padding_unit . ' ' . $padding['desk'][1] . $padding_unit . ' ' . $padding['desk'][2] . $padding_unit . ' ' . $padding['desk'][3] . $padding_unit . ';';
				$css .= '}';
			}
		}
		if ( isset( $attr['margin'] ) && is_array( $attr['margin'] ) && is_array( $attr['margin'][0] ) ) {
			$margin = $attr['margin'][0];
			if ( isset( $margin['desk'] ) && is_array( $margin['desk'] ) && is_numeric( $margin['desk'][0] ) ) {
				$css .= '.kb-advanced-slider-' . $unique_id . ' {';
				$css .= 'margin:' . $margin['desk'][0] . $margin_unit . ' ' . $margin['desk'][1] . $margin_unit . ' ' . $margin['desk'][2] . $margin_unit . ' ' . $margin['desk'][3] . $margin_unit . ';';
				$css .= '}';
			}
		}
		if ( isset( $attr['padding'] ) && is_array( $attr['padding'] ) && is_array( $attr['padding'][0] ) ) {
			$padding = $attr['padding'][0];
			if ( isset( $padding['tablet'] ) && is_array( $padding['tablet'] ) && is_numeric( $padding['tablet'][0] ) ) {
				$css .= '@media (max-width: 1024px) {';
				$css .= '.kb-advanced-slider-' . $unique_id . ' .kb-advanced-slide-inner-wrap {';
				$css .= 'padding:' . $padding['tablet'][0] . $padding_unit . ' ' . $padding['tablet'][1] . $padding_unit . ' ' . $padding['tablet'][2] . $padding_unit . ' ' . $padding['tablet'][3] . $padding_unit . ';';
				$css .= '}';
				$css .= '}';
			}
		}
		if ( isset( $attr['margin'] ) && is_array( $attr['margin'] ) && is_array( $attr['margin'][0] ) ) {
			$margin = $attr['margin'][0];
			if ( isset( $margin['tablet'] ) && is_array( $margin['tablet'] ) && is_numeric( $margin['tablet'][0] ) ) {
				$css .= '@media (max-width: 1024px) {';
				$css .= '.kb-advanced-slider-' . $unique_id . ' {';
				$css .= 'margin:' . $margin['tablet'][0] . $margin_unit . ' ' . $margin['tablet'][1] . $margin_unit . ' ' . $margin['tablet'][2] . $margin_unit . ' ' . $margin['tablet'][3] . $margin_unit . ';';
				$css .= '}';
				$css .= '}';
			}
		}
		if ( isset( $attr['padding'] ) && is_array( $attr['padding'] ) && is_array( $attr['padding'][0] ) ) {
			$padding = $attr['padding'][0];
			if ( isset( $padding['mobile'] ) && is_array( $padding['mobile'] ) && is_numeric( $padding['mobile'][0] ) ) {
				$css .= '@media (max-width: 767px) {';
				$css .= '.kb-advanced-slider-' . $unique_id . ' .kb-advanced-slide-inner-wrap {';
				$css .= 'padding:' . $padding['mobile'][0] . $padding_unit . ' ' . $padding['mobile'][1] . $padding_unit . ' ' . $padding['mobile'][2] . $padding_unit . ' ' . $padding['mobile'][3] . $padding_unit . ';';
				$css .= '}';
				$css .= '}';
			}
		}
		if ( isset( $attr['margin'] ) && is_array( $attr['margin'] ) && is_array( $attr['margin'][0] ) ) {
			$margin = $attr['margin'][0];
			if ( isset( $margin['mobile'] ) && is_array( $margin['mobile'] ) && is_numeric( $margin['mobile'][0] ) ) {
				$css .= '@media (max-width: 767px) {';
				$css .= '.kb-advanced-slider-' . $unique_id . ' {';
				$css .= 'margin:' . $margin['mobile'][0] . $margin_unit . ' ' . $margin['mobile'][1] . $margin_unit . ' ' . $margin['mobile'][2] . $margin_unit . ' ' . $margin['mobile'][3] . $margin_unit . ';';
				$css .= '}';
				$css .= '}';
			}
		}
		return $css;
	}
	/**
	 * Builds CSS for modal block.
	 *
	 * @param array  $attr the blocks attr.
	 * @param string $unique_id the blocks attr ID.
	 */
	public function blocks_modal_array( $attr, $unique_id ) {
		$css = '';
		if ( isset( $attr['modalLinkStyles'] ) && is_array( $attr['modalLinkStyles'] ) && is_array( $attr['modalLinkStyles'][ 0 ] ) ) {
			$modal_link_styles = $attr['modalLinkStyles'][ 0 ];
			$css .= '#kt-modal' . $unique_id . ' .kt-blocks-modal-link {';
			if ( isset( $modal_link_styles['color'] ) && ! empty( $modal_link_styles['color'] ) ) {
				$css .= 'color:' . $this->kadence_color_output( $modal_link_styles['color'] ) . ';';
			}
			if ( isset( $modal_link_styles['background'] ) && ! empty( $modal_link_styles['background'] ) ) {
				$css .= 'background:' . $this->kadence_color_output( $modal_link_styles['background'] ) . ';';
			}
			if ( isset( $modal_link_styles['border'] ) && ! empty( $modal_link_styles['border'] ) ) {
				$css .= 'border-color:' . $this->kadence_color_output( $modal_link_styles['border'] ) . ';';
			}
			if ( isset( $modal_link_styles['borderRadius'] ) && is_numeric( $modal_link_styles['borderRadius'] ) ) {
				$css .= 'border-radius:' . $modal_link_styles['borderRadius'] . 'px;';
			}
			if ( isset( $modal_link_styles['size'] ) && is_array( $modal_link_styles['size'] ) && ! empty( $modal_link_styles['size'][0] ) ) {
				$css .= 'font-size:' . $modal_link_styles['size'][0] . ( ! isset( $modal_link_styles['sizeType'] ) ? 'px' : $modal_link_styles['sizeType'] ) . ';';
			}
			if ( isset( $modal_link_styles['lineHeight'] ) && is_array( $modal_link_styles['lineHeight'] ) && ! empty( $modal_link_styles['lineHeight'][0] ) ) {
				$css .= 'line-height:' . $modal_link_styles['lineHeight'][0] . ( ! isset( $modal_link_styles['lineType'] ) ? 'px' : $modal_link_styles['lineType'] ) . ';';
			}
			if ( isset( $modal_link_styles['letterSpacing'] ) && ! empty( $modal_link_styles['letterSpacing'] ) ) {
				$css .= 'letter-spacing:' . $modal_link_styles['letterSpacing'] .  'px;';
			}
			if ( isset( $modal_link_styles['family'] ) && ! empty( $modal_link_styles['family'] ) ) {
				$css .= 'font-family:' . $modal_link_styles['family'] .  ';';
			}
			if ( isset( $modal_link_styles['style'] ) && ! empty( $modal_link_styles['style'] ) ) {
				$css .= 'font-style:' . $modal_link_styles['style'] .  ';';
			}
			if ( isset( $modal_link_styles['weight'] ) && ! empty( $modal_link_styles['weight'] ) ) {
				$css .= 'font-weight:' . $modal_link_styles['weight'] .  ';';
			}
			if ( isset( $modal_link_styles['borderWidth'] ) && is_array( $modal_link_styles['borderWidth'] ) ) {
				$css .= 'border-width:' . $modal_link_styles['borderWidth'][0] . 'px ' . $modal_link_styles['borderWidth'][1] . 'px ' . $modal_link_styles['borderWidth'][2] . 'px ' . $modal_link_styles['borderWidth'][3] . 'px;';
			}
			if ( isset( $modal_link_styles['padding'] ) && is_array( $modal_link_styles['padding'] ) ) {
				$css .= 'padding:' . $modal_link_styles['padding'][0] . 'px ' . $modal_link_styles['padding'][1] . 'px ' . $modal_link_styles['padding'][2] . 'px ' . $modal_link_styles['padding'][3] . 'px;';
			}
			if ( isset( $modal_link_styles['margin'] ) && is_array( $modal_link_styles['margin'] ) ) {
				$css .= 'margin:' . $modal_link_styles['margin'][0] . 'px ' . $modal_link_styles['margin'][1] . 'px ' . $modal_link_styles['margin'][2] . 'px ' . $modal_link_styles['margin'][3] . 'px;';
			}
			$css .= '}';
			if ( isset( $modal_link_styles['colorHover'] ) || isset( $modal_link_styles['colorHover'] ) || isset( $modal_link_styles['borderHover'] ) ) {
				$css .= '#kt-modal' . $unique_id . ' .kt-blocks-modal-link:hover, #kt-modal' . $unique_id . ' .kt-blocks-modal-link:focus {';
				if ( isset( $modal_link_styles['colorHover'] ) && ! empty( $modal_link_styles['colorHover'] ) ) {
					$css .= 'color:' . $this->kadence_color_output( $modal_link_styles['colorHover'] ) . ';';
				}
				if ( isset( $modal_link_styles['backgroundHover'] ) && ! empty( $modal_link_styles['backgroundHover'] ) ) {
					$css .= 'background:' . $this->kadence_color_output( $modal_link_styles['backgroundHover'] ) . ';';
				}
				if ( isset( $modal_link_styles['borderHover'] ) && ! empty( $modal_link_styles['borderHover'] ) ) {
					$css .= 'border-color:' . $this->kadence_color_output( $modal_link_styles['borderHover'] ) . ';';
				}
				$css .= '}';
			}
		}
		if ( isset( $attr['displayLinkShadow'] ) && true == $attr['displayLinkShadow'] ) {
			if ( isset( $attr['linkShadow'] ) && is_array( $attr['linkShadow'] ) && isset( $attr['linkShadow'][0] ) && is_array( $attr['linkShadow'][0] ) ) {
				$link_shadow = $attr['linkShadow'][0];
				$css .= '#kt-modal' . $unique_id . ' .kt-blocks-modal-link {';
				$css .= 'box-shadow:' . $link_shadow['hOffset'] . 'px ' . $link_shadow['vOffset'] . 'px ' . $link_shadow['blur'] . 'px ' . $link_shadow['spread'] . 'px ' . $this->kadence_color_output( $link_shadow['color'] ) . ';';
				$css .= '}';
			} else {
				$css .= '#kt-modal' . $unique_id . ' .kt-blocks-modal-link {';
				$css .= 'box-shadow:rgba(0, 0, 0, 0.2) 1px 1px 2px 0px;';
				$css .= '}';
			}
		}
		if ( isset( $attr['displayLinkHoverShadow'] ) && true == $attr['displayLinkHoverShadow'] ) {
			if ( isset( $attr['linkHoverShadow'] ) && is_array( $attr['linkHoverShadow'] ) && isset( $attr['linkHoverShadow'][0] ) && is_array( $attr['linkHoverShadow'][0] ) ) {
				$link_hover_shadow = $attr['linkHoverShadow'][0];
				$css .= '#kt-modal' . $unique_id . ' .kt-blocks-modal-link:hover, #kt-modal' . $unique_id . ' .kt-blocks-modal-link:focus {';
				$css .= 'box-shadow:' . $link_hover_shadow['hOffset'] . 'px ' . $link_hover_shadow['vOffset'] . 'px ' . $link_hover_shadow['blur'] . 'px ' . $link_hover_shadow['spread'] . 'px ' . $this->kadence_color_output( $link_hover_shadow['color'] ) . ';';
				$css .= '}';
			} else {
				$css .= '#kt-modal' . $unique_id . ' .kt-blocks-modal-link:hover, #kt-modal' . $unique_id . ' .kt-blocks-modal-link:focus {';
				$css .= 'box-shadow:rgba(0, 0, 0, 0.4) 2px 2px 3px 0px;';
				$css .= '}';
			}
		}
		if ( isset( $attr['modalLinkStyles'] ) && is_array( $attr['modalLinkStyles'] ) && isset( $attr['modalLinkStyles'][0] ) && is_array( $attr['modalLinkStyles'][0] ) && ( ( isset( $attr['modalLinkStyles'][0]['size'] ) && is_array( $attr['modalLinkStyles'][0]['size'] ) && isset( $attr['modalLinkStyles'][0]['size'][1] ) && ! empty( $attr['modalLinkStyles'][0]['size'][1] ) ) || ( isset( $attr['modalLinkStyles'][0]['lineHeight'] ) && is_array( $attr['modalLinkStyles'][0]['lineHeight'] ) && isset( $attr['modalLinkStyles'][0]['lineHeight'][1] ) && ! empty( $attr['modalLinkStyles'][0]['lineHeight'][1] ) ) ) ) {
			$css .= '@media (min-width: 767px) and (max-width: 1024px) {';
			$css .= '#kt-modal' . $unique_id . ' .kt-blocks-modal-link {';
			if ( isset( $attr['modalLinkStyles'][0]['size'][1] ) && ! empty( $attr['modalLinkStyles'][0]['size'][1] ) ) {
				$css .= 'font-size:' . $attr['modalLinkStyles'][0]['size'][1] . ( ! isset( $attr['modalLinkStyles'][0]['sizeType'] ) ? 'px' : $attr['modalLinkStyles'][0]['sizeType'] ) . ';';
			}
			if ( isset( $attr['modalLinkStyles'][0]['lineHeight'][1] ) && ! empty( $attr['modalLinkStyles'][0]['lineHeight'][1] ) ) {
				$css .= 'line-height:' . $attr['modalLinkStyles'][0]['lineHeight'][1] . ( ! isset( $attr['modalLinkStyles'][0]['lineType'] ) ? 'px' : $attr['modalLinkStyles'][0]['lineType'] ) . ';';
			}
			$css .= '}';
			$css .= '#kt-modal' . $unique_id . ' .kt-blocks-modal-link svg {';
				if ( isset( $attr['modalLinkStyles'][0]['size'][1] ) && ! empty( $attr['modalLinkStyles'][0]['size'][1] ) ) {
					$css .= 'width:' . $attr['modalLinkStyles'][0]['size'][1] . ( ! isset( $attr['modalLinkStyles'][0]['sizeType'] ) ? 'px' : $attr['modalLinkStyles'][0]['sizeType'] ) . ';';
				}
			$css .= '}';

			$css .= '}';
		}
		if ( isset( $attr['modalLinkStyles'] ) && is_array( $attr['modalLinkStyles'] ) && isset( $attr['modalLinkStyles'][0] ) && is_array( $attr['modalLinkStyles'][0] ) && ( ( isset( $attr['modalLinkStyles'][0]['size'] ) && is_array( $attr['modalLinkStyles'][0]['size'] ) && isset( $attr['modalLinkStyles'][0]['size'][2] ) && ! empty( $attr['modalLinkStyles'][0]['size'][2] ) ) || ( isset( $attr['modalLinkStyles'][0]['lineHeight'] ) && is_array( $attr['modalLinkStyles'][0]['lineHeight'] ) && isset( $attr['modalLinkStyles'][0]['lineHeight'][2] ) && ! empty( $attr['modalLinkStyles'][0]['lineHeight'][2] ) ) ) ) {
			$css .= '@media (max-width: 767px) {';
			$css .= '#kt-modal' . $unique_id . ' .kt-blocks-modal-link {';
				if ( isset( $attr['modalLinkStyles'][0]['size'][2] ) && ! empty( $attr['modalLinkStyles'][0]['size'][2] ) ) {
					$css .= 'font-size:' . $attr['modalLinkStyles'][0]['size'][2] . ( ! isset( $attr['modalLinkStyles'][0]['sizeType'] ) ? 'px' : $attr['modalLinkStyles'][0]['sizeType'] ) . ';';
				}
				if ( isset( $attr['modalLinkStyles'][0]['lineHeight'][2] ) && ! empty( $attr['modalLinkStyles'][0]['lineHeight'][2] ) ) {
					$css .= 'line-height:' . $attr['modalLinkStyles'][0]['lineHeight'][2] . ( ! isset( $attr['modalLinkStyles'][0]['lineType'] ) ? 'px' : $attr['modalLinkStyles'][0]['lineType'] ) . ';';
				}
			$css .= '}';
			$css .= '#kt-modal' . $unique_id . ' .kt-blocks-modal-link svg {';
				if ( isset( $attr['modalLinkStyles'][0]['size'][2] ) && ! empty( $attr['modalLinkStyles'][0]['size'][2] ) ) {
					$css .= 'width:' . $attr['modalLinkStyles'][0]['size'][2] . ( ! isset( $attr['modalLinkStyles'][0]['sizeType'] ) ? 'px' : $attr['modalLinkStyles'][0]['sizeType'] ) . ';';
				}
			$css .= '}';
			$css .= '}';
		}
		if ( isset( $attr['modalOverlay'] ) ) {
			$css .= '#kt-modal' . $unique_id . ' .kt-modal-overlay, #kt-target-modal' . $unique_id . ' .kt-modal-overlay, .kb-modal-content' . $unique_id . ' .kt-modal-overlay {';
			if ( isset( $attr['modalOverlay'] ) && ! empty( $attr['modalOverlay'] ) ) {
				$css .= 'background:' . $this->kadence_color_output( $attr['modalOverlay'], ( isset( $attr['modalOverlayOpacity'] ) ? $attr['modalOverlayOpacity'] : 0.6 ) ) . ';';
			}
			$css .= '}';
		}
		if ( isset( $attr['modalHAlign'] ) || isset( $attr['modalVAlign'] ) ) {
			$css .= '#kt-modal' . $unique_id . ' .kt-modal-overlay, #kt-target-modal' . $unique_id . ' .kt-modal-overlay, .kb-modal-content' . $unique_id . ' .kt-modal-overlay {';
			if ( isset( $attr['modalHAlign'] ) && ! empty( $attr['modalHAlign'] ) ) {
				if ( 'center' === $attr['modalHAlign'] ) {
					$css .= '-ms-flex-pack:center;';
					$css .= 'justify-content:center;';
				} elseif ( 'left' === $attr['modalHAlign'] ) {
					$css .= '-ms-flex-pack:flex-start;';
					$css .= 'justify-content:flex-start;';
				} elseif ( 'right' === $attr['modalHAlign'] ) {
					$css .= '-ms-flex-pack:flex-end;';
					$css .= 'justify-content:flex-end;';
				}
			}
			if ( isset( $attr['modalVAlign'] ) && ! empty( $attr['modalVAlign'] ) ) {
				if ( 'middle' === $attr['modalVAlign'] ) {
					$css .= '-ms-flex-align:center;';
					$css .= 'align-items:center;';
				} elseif ( 'top' === $attr['modalVAlign'] ) {
					$css .= '-ms-flex-align:flex-start;';
					$css .= 'align-items:flex-start;';
				} elseif ( 'bottom' === $attr['modalVAlign'] ) {
					$css .= '-ms-flex-align:flex-end;';
					$css .= 'align-items:flex-end;';
				}
			}
			$css .= '}';
		}
		if ( isset( $attr['modalWidth'] ) || isset( $attr['modalMaxWidth'] ) || isset( $attr['modalHeight'] ) || isset( $attr['modalInnerHAlign'] ) || isset( $attr['modalInnerVAlign'] ) ) {
			$css .= '#kt-modal' . $unique_id . ' .kt-modal-container, #kt-target-modal' . $unique_id . ' .kt-modal-container, .kb-modal-content' . $unique_id . ' .kt-modal-container {';
			if ( isset( $attr['modalWidth'] ) && is_array( $attr['modalWidth'] ) && isset( $attr['modalWidth'][0] ) && ! empty( $attr['modalWidth'][0] )  ) {
				$css .= 'width:' . $attr['modalWidth'][0] . '%;';
			}
			if ( isset( $attr['modalMaxWidth'] ) && ! empty( $attr['modalMaxWidth'] ) ) {
				$css .= 'max-width:' . $attr['modalMaxWidth'] . 'px;';
			}
			if ( isset( $attr['modalHeight'] ) && ! empty( $attr['modalHeight'] ) && 'fixed' === $attr['modalHeight'] ) {
				$css .= 'min-height:' . ( isset( $attr['modalCustomHeight'] ) ? $attr['modalCustomHeight'] : '400' ) . 'px;';
			}
			if ( isset( $attr['modalInnerHAlign'] ) && ! empty( $attr['modalInnerHAlign'] ) ) {
				if ( 'center' === $attr['modalInnerHAlign'] ) {
					$css .= '-ms-flex-pack:center;';
					$css .= 'justify-content:center;';
					$css .= 'text-align:center;';
				} elseif ( 'left' === $attr['modalInnerHAlign'] ) {
					$css .= '-ms-flex-pack:flex-start;';
					$css .= 'justify-content:flex-start;';
					$css .= 'text-align:left;';
				} elseif ( 'right' === $attr['modalInnerHAlign'] ) {
					$css .= '-ms-flex-pack:flex-end;';
					$css .= 'justify-content:flex-end;';
					$css .= 'text-align:right;';
				}
			}
			if ( isset( $attr['modalInnerVAlign'] ) && ! empty( $attr['modalInnerVAlign'] ) ) {
				if ( 'middle' === $attr['modalInnerVAlign'] ) {
					$css .= '-ms-flex-align:center;';
					$css .= 'align-items:center;';
				} elseif ( 'top' === $attr['modalInnerVAlign'] ) {
					$css .= '-ms-flex-align:flex-start;';
					$css .= 'align-items:flex-start;';
				} elseif ( 'bottom' === $attr['modalInnerVAlign'] ) {
					$css .= '-ms-flex-align:flex-end;';
					$css .= 'align-items:flex-end;';
				}
			}
			$css .= '}';
		}
		if ( isset( $attr['modalWidth'] ) && is_array( $attr['modalWidth'] ) && isset( $attr['modalWidth'][1] ) && ! empty( $attr['modalWidth'][1] ) ) {
			$css .= '@media (min-width: 767px) and (max-width: 1024px) {';
			$css .= '#kt-modal' . $unique_id . ' .kt-modal-container, #kt-target-modal' . $unique_id . ' .kt-modal-container, .kb-modal-content' . $unique_id . ' .kt-modal-container {';
			$css .= 'width:' . $attr['modalWidth'][1] . '%;';
			$css .= '}';
			$css .= '}';
		}
		if ( isset( $attr['modalWidth'] ) && is_array( $attr['modalWidth'] ) && isset( $attr['modalWidth'][2] ) && ! empty( $attr['modalWidth'][2] ) ) {
			$css .= '@media (max-width: 767px) {';
			$css .= '#kt-modal' . $unique_id . ' .kt-modal-container, #kt-target-modal' . $unique_id . ' .kt-modal-container, .kb-modal-content' . $unique_id . ' .kt-modal-container {';
			$css .= 'width:' . $attr['modalWidth'][2] . '%;';
			$css .= '}';
			$css .= '}';
		}
		if ( isset( $attr['modalHeight'] ) && ! empty( $attr['modalHeight'] ) && 'full' === $attr['modalHeight'] ) {
			$margin_inner = false;
			if ( isset( $attr['modalMargin']  ) && is_array( $attr['modalMargin'] ) ) {
				$css .= '#kt-modal' . $unique_id . ' .kt-modal-overlay, #kt-target-modal' . $unique_id . ' .kt-modal-overlay, .kb-modal-content' . $unique_id . ' .kt-modal-overlay {';
				$css .= 'padding:' . $attr['modalMargin'] [0] . 'px ' . $attr['modalMargin'] [1] . 'px ' . $attr['modalMargin'] [2] . 'px ' . $attr['modalMargin'] [3] . 'px;';
				$css .= '}';
			}
		} else {
			$margin_inner = true;
		}
		if ( isset( $attr['modalBackground'] ) || isset( $attr['modalBackgroundOpacity'] ) || isset( $attr['modalBorderColor'] ) || isset( $attr['modalBorderWidth'] ) || isset( $attr['modalBorderRadius'] ) || isset( $attr['modalPadding'] ) || isset( $attr['modalMargin'] ) ) {
			$css .= '#kt-modal' . $unique_id . ' .kt-modal-container, #kt-target-modal' . $unique_id . ' .kt-modal-container, .kb-modal-content' . $unique_id . ' .kt-modal-container {';
			if ( ( isset( $attr['modalBackground'] ) && ! empty( $attr['modalBackground'] ) ) || ( isset( $attr['modalBackgroundOpacity'] ) && is_numeric( $attr['modalBackgroundOpacity'] ) ) ) {
				$css .= 'background:' . $this->kadence_color_output( ( isset( $attr['modalBackground'] ) ? $attr['modalBackground'] : '#fff' ), ( isset( $attr['modalBackgroundOpacity'] ) ? $attr['modalBackgroundOpacity'] : 1 ) ) . ';';
			}
			if ( isset( $attr['modalBorderColor'] ) && ! empty( $attr['modalBorderColor'] ) ) {
				$css .= 'border-color:' . $this->kadence_color_output( $attr['modalBorderColor'], ( isset( $attr['modalBorderOpacity'] ) ? $attr['modalBorderOpacity'] : 1 ) ) . ';';
			}
			if ( isset( $attr['modalBorderWidth'] ) && is_array( $attr['modalBorderWidth'] ) ) {
				$css .= 'border-width:' . $attr['modalBorderWidth'] [0] . 'px ' . $attr['modalBorderWidth'] [1] . 'px ' . $attr['modalBorderWidth'] [2] . 'px ' . $attr['modalBorderWidth'] [3] . 'px;';
			}
			if ( isset( $attr['modalBorderRadius'] ) && ! empty( $attr['modalBorderRadius'] ) ) {
				$css .= 'border-radius:' . $attr['modalBorderRadius'] . 'px;';
			}
			if ( isset( $attr['modalPadding'] ) && is_array( $attr['modalPadding'] ) ) {
				$css .= 'padding:' . $attr['modalPadding'] [0] . 'px ' . $attr['modalPadding'] [1] . 'px ' . $attr['modalPadding'] [2] . 'px ' . $attr['modalPadding'] [3] . 'px;';
			}
			if ( isset( $attr['modalMargin'] ) && is_array( $attr['modalMargin'] ) && $margin_inner ) {
				$css .= 'margin:' . $attr['modalMargin'] [0] . 'px ' . $attr['modalMargin'] [1] . 'px ' . $attr['modalMargin'] [2] . 'px ' . $attr['modalMargin'] [3] . 'px;';
			}
			$css .= '}';
		}
		if ( isset( $attr['displayShadow'] ) && ! empty( $attr['displayShadow'] ) && true === $attr['displayShadow'] ) {
			if ( isset( $attr['shadow'] ) && is_array( $attr['shadow'] ) && is_array( $attr['shadow'][ 0 ] ) ) {
				$shadow = $attr['shadow'][ 0 ];
				$css .= '#kt-modal' . $unique_id . ' .kt-modal-container, #kt-target-modal' . $unique_id . ' .kt-modal-container, .kb-modal-content' . $unique_id . ' .kt-modal-container {';
				$css .= 'box-shadow:' . $shadow['hOffset'] . 'px ' . $shadow['vOffset'] . 'px ' . $shadow['blur'] . 'px ' . $shadow['spread'] . 'px ' . $this->kadence_color_output( $shadow['color'], $shadow['opacity'] ) . ';';
				$css .= '}';
			} else {
				$css .= '#kt-modal' . $unique_id . ' .kt-modal-container, #kt-target-modal' . $unique_id . ' .kt-modal-container, .kb-modal-content' . $unique_id . ' .kt-modal-container {';
				$css .= 'box-shadow:0px 0px 14px 0px rgba(0,0,0,0.2);';
				$css .= '}';
			}
		}
		if ( isset( $attr['closeColor'] ) || isset( $attr['closeBackground'] ) ) {
			$css .= '#kt-modal' . $unique_id . ' .kt-modal-close, #kt-target-modal' . $unique_id . ' .kt-modal-close, .kb-modal-content' . $unique_id . ' .kt-modal-close {';
			if ( isset( $attr['closeColor'] ) && ! empty( $attr['closeColor'] ) ) {
				$css .= 'color:' . $this->kadence_color_output( $attr['closeColor'] ) . ';';
			}
			if ( isset( $attr['closeBackground'] ) && ! empty( $attr['closeBackground'] ) ) {
				$css .= 'background:' . $this->kadence_color_output( $attr['closeBackground'] ) . ';';
			}
			$css .= '}';
		}
		if ( isset( $attr['closeSize'] ) && is_array(  $attr['closeSize'] ) && isset( $attr['closeSize'][0] ) && ! empty( $attr['closeSize'][0] ) ) {
			$css .= '#kt-modal' . $unique_id . ' .kt-modal-close svg, #kt-target-modal' . $unique_id . ' .kt-modal-close svg, .kb-modal-content' . $unique_id . ' .kt-modal-close svg {';
			$css .= 'width:' . $attr['closeSize'][0] . 'px;';
			$css .= 'height:' . $attr['closeSize'][0] . 'px;';
			$css .= '}';
		}
		if ( isset( $attr['closeHoverColor'] ) || isset( $attr['closeHoverBackground'] ) ) {
			$css .= '#kt-modal' . $unique_id . ' .kt-modal-close:hover, #kt-target-modal' . $unique_id . ' .kt-modal-close:hover, .kb-modal-content' . $unique_id . ' .kt-modal-close:hover, body:not(.hide-focus-outline) #kt-modal' . $unique_id . ' .kt-modal-close:focus, body:not(.hide-focus-outline) #kt-target-modal' . $unique_id . ' .kt-modal-close:focus,body:not(.hide-focus-outline)  .kb-modal-content' . $unique_id . ' .kt-modal-close:focus {';
			if ( isset( $attr['closeHoverColor'] ) && ! empty( $attr['closeHoverColor'] ) ) {
				$css .= 'color:' . $this->kadence_color_output( $attr['closeHoverColor'] ) . ';';
			}
			if ( isset( $attr['closeHoverBackground'] ) && ! empty( $attr['closeHoverBackground'] ) ) {
				$css .= 'background:' . $this->kadence_color_output( $attr['closeHoverBackground'] ) . ';';
			}
			$css .= '}';
		}
		if ( isset( $attr['closeSize'] ) && is_array( $attr['closeSize'] ) && isset( $attr['closeSize'][1] ) && ! empty( $attr['closeSize'][1] ) ) {
			$css .= '@media (min-width: 767px) and (max-width: 1024px) {';
			$css .= '#kt-modal' . $unique_id . ' .kt-modal-close svg, #kt-target-modal' . $unique_id . ' .kt-modal-close svg, .kb-modal-content' . $unique_id . ' .kt-modal-close svg {';
			$css .= 'width:' . $attr['closeSize'][1] . 'px;';
			$css .= 'height:' . $attr['closeSize'][1] . 'px;';
			$css .= '}';
			$css .= '}';
		}
		if ( isset( $attr['closeSize'] ) && is_array( $attr['closeSize'] ) && isset( $attr['closeSize'][2] ) && ! empty( $attr['closeSize'][2] ) ) {
			$css .= '@media (max-width: 767px) {';
			$css .= '#kt-modal' . $unique_id . ' .kt-modal-close svg, #kt-target-modal' . $unique_id . ' .kt-modal-close svg, .kb-modal-content' . $unique_id . ' .kt-modal-close svg {';
			$css .= 'width:' . $attr['closeSize'][2] . 'px;';
			$css .= 'height:' . $attr['closeSize'][2] . 'px;';
			$css .= '}';
			$css .= '}';
		}
		return $css;
	}
	/**
	 * Builds CSS for Video Popup block.
	 *
	 * @param array  $attr the blocks attr.
	 * @param string $unique_id the blocks attr ID.
	 */
	public function blocks_video_popup_array( $attr, $unique_id ) {
		$css = '';
		if ( isset( $attr['backgroundOverlay'] ) && is_array( $attr['backgroundOverlay'] ) && is_array( $attr['backgroundOverlay'][0] ) ) {
			$overlay = $attr['backgroundOverlay'][0];
			$css .= '.kadence-video-popup' . $unique_id . ' .kadence-video-popup-wrap .kadence-video-overlay {';
			if ( isset( $overlay['opacity'] ) && is_numeric( $overlay['opacity'] ) ) {
				$css .= 'opacity:' . $overlay['opacity'] . ';';
			}
			if ( isset( $overlay['blendMode'] ) && ! empty( $overlay['blendMode'] ) && 'normal' !== $overlay['blendMode'] ) {
				$css .= 'mix-blend-mode:' . $overlay['blendMode'] . ';';
			}
			if ( ! isset( $overlay['type'] ) || 'gradient' !== $overlay['type'] ) {
				if ( isset( $overlay['fill'] ) || isset( $overlay['fillOpacity'] ) ) {
					$css .= 'background:' . $this->kadence_color_output( ( ! empty( $overlay['fill'] ) ? $overlay['fill'] : '#000000' ), ( ! empty( $overlay['fillOpacity'] ) ? $overlay['fillOpacity'] : '1' ) ) . ';';
				}
			} else {
				$type = ( isset( $overlay['gradType'] ) ? $overlay['gradType'] : 'linear' );
				if ( 'radial' === $type ) {
					$angle = ( isset( $overlay['gradPosition'] ) ? 'at ' . $overlay['gradPosition'] : 'at center center' );
				} else {
					$angle = ( isset( $overlay['gradAngle'] ) ? $overlay['gradAngle'] . 'deg' : '180deg' );
				}
				$loc = ( isset( $overlay['gradLoc'] ) ? $overlay['gradLoc'] : '0' );
				$color = $this->kadence_color_output( ( isset( $overlay['fill'] ) ? $overlay['fill'] : '#000000' ), ( ! empty( $overlay['fillOpacity'] ) ? $overlay['fillOpacity'] : '1' ) );
				$locsecond = ( isset( $overlay['gradLocSecond'] ) ? $overlay['gradLocSecond'] : '100' );
				$colorsecond = $this->kadence_color_output( ( isset( $overlay['secondFill'] ) ? $overlay['secondFill'] : '#000000' ), ( ! empty( $overlay['secondFillOpacity'] ) ? $overlay['secondFillOpacity'] : '0' ) );
				$css .= 'background: ' . $type . '-gradient(' . $angle. ', ' . $color . ' ' . $loc . '%, ' . $colorsecond . ' ' . $locsecond . '%);';
			}
			$css .= '}';
			if ( isset( $overlay['opacityHover'] ) && is_numeric( $overlay['opacityHover'] ) ) {
				$css .= '.kadence-video-popup' . $unique_id . ' .kadence-video-popup-wrap:hover .kadence-video-overlay {';
				$css .= 'opacity:' . $overlay['opacityHover'] . ';';
				$css .= '}';
			}
		}
		if ( isset( $attr['kadenceDynamic'] ) && is_array( $attr['kadenceDynamic'] ) ) {
			if ( isset( $attr['ratio'] ) && 'custom' === $attr['ratio'] ) {
				if ( isset( $attr['background'] ) && is_array( $attr['background'] ) && is_array( $attr['background'][0] ) ) {
					$background = $attr['background'][0];
					if ( ! empty( $background['imageHeight'] ) && ! empty( $background['imgWidth'] ) ) {
						$css .= '.kadence-video-popup' . $unique_id . ' .kadence-video-popup-wrap .kadence-video-intrinsic.kadence-video-set-ratio-custom {';
						$css .= 'padding-bottom:' . floor( $background['imageHeight'] / $background['imgWidth'] * 100 ) . '% !important;';
						$css .= '}';
					}
				}
			}
		}
		$css .= '.kadence-video-popup' . $unique_id . ' .kadence-video-popup-wrap {';
		if ( isset( $attr['displayShadow'] ) && ! empty( $attr['displayShadow'] ) && true === $attr['displayShadow'] ) {
			if ( isset( $attr['shadow'] ) && is_array( $attr['shadow'] ) && is_array( $attr['shadow'][0] ) ) {
				$shadow = $attr['shadow'][ 0 ];
				$css .= 'box-shadow:' . $shadow['hOffset'] . 'px ' . $shadow['vOffset'] . 'px ' . $shadow['blur'] . 'px ' . $shadow['spread'] . 'px ' . $this->kadence_color_output( $shadow['color'], $shadow['opacity'] ) . ';';
			} else {
				$css .= 'box-shadow:4px 2px 14px 0px ' . $this->kadence_color_output( '#000000', 0.2 ) . ';';
			}
		}
		if ( isset( $attr['borderWidth'] ) && is_array( $attr['borderWidth'] ) && is_numeric( $attr['borderWidth'][0] ) ) {
			$css .= 'border-width:' . $attr['borderWidth'][0] . 'px ' . $attr['borderWidth'][1] . 'px ' . $attr['borderWidth'][2] . 'px ' . $attr['borderWidth'][3] . 'px;';
		}
		if ( isset( $attr['borderColor'] ) && ! empty( $attr['borderColor'] ) ) {
			$css .= 'border-color:' . $this->kadence_color_output( $attr['borderColor'], ( isset( $attr['borderOpacity'] ) ? $attr['borderOpacity'] : 1 ) ) . ';';
		}
		if ( isset( $attr['borderRadius'] ) && is_array( $attr['borderRadius'] ) && is_numeric( $attr['borderRadius'][0] ) ) {
			$css .= 'border-radius:' . $attr['borderRadius'][0] . 'px ' . $attr['borderRadius'][1] . 'px ' . $attr['borderRadius'][2] . 'px ' . $attr['borderRadius'][3] . 'px;';
		}
		$css .= '}';
		if ( isset( $attr['displayShadow'] ) && ! empty( $attr['displayShadow'] ) && true === $attr['displayShadow'] ) {
			if ( isset( $attr['shadowHover'] ) && is_array( $attr['shadowHover'] ) && is_array( $attr['shadow'][0] ) ) {
				$css .= '.kadence-video-popup' . $unique_id . ' .kadence-video-popup-wrap:hover {';
				$shadow = $attr['shadowHover'][0];
				$css .= 'box-shadow:' . $shadow['hOffset'] . 'px ' . $shadow['vOffset'] . 'px ' . $shadow['blur'] . 'px ' . $shadow['spread'] . 'px ' . $this->kadence_color_output( $shadow['color'], $shadow['opacity'] ) . ';';
				$css .= '}';
			} else {
				$css .= '.kadence-video-popup' . $unique_id . ' .kadence-video-popup-wrap:hover {';
				$css .= 'box-shadow:4px 2px 14px 0px ' . $this->kadence_color_output( '#000000', 0.2 ) . ';';
				$css .= '}';
			}
		}
		if ( ! empty( $attr['maxWidth'] ) ) {
			$css .= '.kb-section-dir-horizontal > .kt-inside-inner-col > .kadence-video-popup' . $unique_id . '{';
			$css .= 'max-width:' . $attr['maxWidth'] . 'px;';
			$css .= 'width:100%;';
			$css .= '}';
		}
		$css .= '.kadence-video-popup' . $unique_id . '{';
		if ( isset( $attr['padding'] ) && is_array( $attr['padding'] ) && is_array( $attr['padding'][0] ) ) {
			$padding = $attr['padding'][0];
			if ( isset( $padding['desk'] ) && is_array( $padding['desk'] ) && is_numeric( $padding['desk'][0] ) ) {
				$css .= 'padding:' . $padding['desk'][0] . 'px ' . $padding['desk'][1] . 'px ' . $padding['desk'][2] . 'px ' . $padding['desk'][3] . 'px;';
			}
		}
		if ( isset( $attr['margin'] ) && is_array( $attr['margin'] ) && is_array( $attr['margin'][0] ) ) {
			$margin = $attr['margin'][0];
			if ( isset( $margin['desk'] ) && is_array( $margin['desk'] ) && is_numeric( $margin['desk'][0] ) ) {
				$css .= 'margin:' . $margin['desk'][0] . 'px ' . $margin['desk'][1] . 'px ' . $margin['desk'][2] . 'px ' . $margin['desk'][3] . 'px;';
			}
		}
		$css .= '}';
		if ( isset( $attr['background'] ) && is_array( $attr['background'] ) && is_array( $attr['background'][ 0 ] ) ) {
			$background = $attr['background'][0];
			if ( isset( $background['color'] ) && ! empty( $background['color'] ) ) {
				$css .= '.kadence-video-popup' . $unique_id . ' .kadence-video-popup-wrap .kadence-video-intrinsic {';
				$css .= 'background-color:' . $this->kadence_color_output( $background['color'], ( isset( $background['colorOpacity'] ) ? $background['colorOpacity'] : 1 ) ) . ';';
				$css .= '}';
			}
		}
		if ( isset( $attr['playBtn'] ) && is_array( $attr['playBtn'] ) && is_array( $attr['playBtn'][ 0 ] ) ) {
			$play_btn = $attr['playBtn'][0];
			if ( isset( $play_btn['color'] ) && ! empty( $play_btn['color'] ) ) {
				$css .= '.kadence-video-popup' . $unique_id . ' .kadence-video-popup-wrap .kt-video-svg-icon {';
				$css .= 'color:' . $this->kadence_color_output( $play_btn['color'], ( isset( $play_btn['opacity'] ) ? $play_btn['opacity'] : 1 ) ) . ';';
				$css .= '}';
			}
			if ( isset( $play_btn['colorHover'] ) && ! empty( $play_btn['colorHover'] ) ) {
				$css .= '.kadence-video-popup' . $unique_id . ' .kadence-video-popup-wrap:hover .kt-video-svg-icon {';
				$css .= 'color:' . $this->kadence_color_output( $play_btn['colorHover'], ( isset( $play_btn['opacityHover'] ) ? $play_btn['opacityHover'] : 1 ) ) . ';';
				$css .= '}';
			}
			if ( isset( $play_btn['style'] ) && 'stacked' === $play_btn['style'] ) {
				$css .= '.kadence-video-popup' . $unique_id . ' .kadence-video-popup-wrap .kt-video-svg-icon.kt-video-svg-icon-style-stacked {';
				if ( isset( $play_btn['background'] ) && ! empty( $play_btn['background'] ) ) {
					$css .= 'background:' . $this->kadence_color_output( $play_btn['background'], ( isset( $play_btn['backgroundOpacity'] ) ? $play_btn['backgroundOpacity'] : 1 ) ) . ';';
				}
				if ( isset( $play_btn['border'] ) && ! empty( $play_btn['border'] ) ) {
					$css .= 'border-color:' . $this->kadence_color_output( $play_btn['border'], ( isset( $play_btn['borderOpacity'] ) ? $play_btn['borderOpacity'] : 1 ) ) . ';';
				}
				if ( isset( $play_btn['borderRadius'] ) && is_array( $play_btn['borderRadius'] ) && is_numeric( $play_btn['borderRadius'][0] ) ) {
					$css .= 'border-radius:' . $play_btn['borderRadius'][0] . '% ' . $play_btn['borderRadius'][1] . '% ' . $play_btn['borderRadius'][2] . '% ' . $play_btn['borderRadius'][3] . '%;';
				}
				if ( isset( $play_btn['borderWidth'] ) && is_array( $play_btn['borderWidth'] ) && is_numeric( $play_btn['borderWidth'][0] ) ) {
					$css .= 'border-width:' . $play_btn['borderWidth'][0] . 'px ' . $play_btn['borderWidth'][1] . 'px ' . $play_btn['borderWidth'][2] . 'px ' . $play_btn['borderWidth'][3] . 'px;';
				}
				if ( isset( $play_btn['padding'] ) && ! empty( $play_btn['padding'] ) ) {
					$css .= 'padding:' . $play_btn['padding'] . 'px;';
				}
				$css .= '}';
				// Hover.
				$css .= '.kadence-video-popup' . $unique_id . ' .kadence-video-popup-wrap:hover .kt-video-svg-icon.kt-video-svg-icon-style-stacked {';
				if ( isset( $play_btn['backgroundHover'] ) && ! empty( $play_btn['backgroundHover'] ) ) {
					$css .= 'background:' . $this->kadence_color_output( $play_btn['backgroundHover'], ( isset( $play_btn['backgroundOpacityHover'] ) ? $play_btn['backgroundOpacityHover'] : 1 ) ) . ';';
				}
				if ( isset( $play_btn['borderHover'] ) && ! empty( $play_btn['borderHover'] ) ) {
					$css .= 'border-color:' . $this->kadence_color_output( $play_btn['borderHover'], ( isset( $play_btn['borderOpacityHover'] ) ? $play_btn['borderOpacityHover'] : 1 ) ) . ';';
				}
				$css .= '}';
			}
		}
		$css .= '@media (min-width: 767px) and (max-width: 1024px) {';
		$css .= '.kadence-video-popup' . $unique_id . '{';
		if ( isset( $attr['padding'] ) && is_array( $attr['padding'] ) && is_array( $attr['padding'][0] ) ) {
			$padding = $attr['padding'][0];
			if ( isset( $padding['tablet'] ) && is_array( $padding['tablet'] ) && is_numeric( $padding['tablet'][0] ) ) {
				$css .= 'padding:' . $padding['tablet'][0] . 'px ' . $padding['tablet'][1] . 'px ' . $padding['tablet'][2] . 'px ' . $padding['tablet'][3] . 'px;';
			}
		}
		if ( isset( $attr['margin'] ) && is_array( $attr['margin'] ) && is_array( $attr['margin'][0] ) ) {
			$margin = $attr['margin'][0];
			if ( isset( $margin['tablet'] ) && is_array( $margin['tablet'] ) && is_numeric( $margin['tablet'][0] ) ) {
				$css .= 'margin:' . $margin['tablet'][0] . 'px ' . $margin['tablet'][1] . 'px ' . $margin['tablet'][2] . 'px ' . $margin['tablet'][3] . 'px;';
			}
		}
		$css .= '}';
		$css .= '}';
		$css .= '@media (max-width: 767px) {';
		$css .= '.kadence-video-popup' . $unique_id . '{';
		if ( isset( $attr['padding'] ) && is_array( $attr['padding'] ) && is_array( $attr['padding'][0] ) ) {
			$padding = $attr['padding'][0];
			if ( isset( $padding['mobile'] ) && is_array( $padding['mobile'] ) && is_numeric( $padding['mobile'][0] ) ) {
				$css .= 'padding:' . $padding['mobile'][0] . 'px ' . $padding['mobile'][1] . 'px ' . $padding['mobile'][2] . 'px ' . $padding['mobile'][3] . 'px;';
			}
		}
		if ( isset( $attr['margin'] ) && is_array( $attr['margin'] ) && is_array( $attr['margin'][0] ) ) {
			$margin = $attr['margin'][0];
			if ( isset( $margin['mobile'] ) && is_array( $margin['mobile'] ) && is_numeric( $margin['mobile'][0] ) ) {
				$css .= 'margin:' . $margin['mobile'][0] . 'px ' . $margin['mobile'][1] . 'px ' . $margin['mobile'][2] . 'px ' . $margin['mobile'][3] . 'px;';
			}
		}
		$css .= '}';
		$css .= '}';
		if ( isset( $attr['popup'] ) && is_array( $attr['popup'] ) && is_array( $attr['popup'][0] ) ) {
			$popup = $attr['popup'][0];
			if ( ( isset( $popup['background'] ) && ! empty( $popup['background'] ) ) || isset( $popup['backgroundOpacity'] ) && ! empty( $popup['backgroundOpacity'] ) ) {
				$css .= '.glightbox-kadence-dark.kadence-popup-' . $unique_id . ' .goverlay {';
				if ( isset( $popup['background'] ) && ! empty( $popup['background'] ) ) {
					$css .= 'background:' . $this->kadence_color_output( $popup['background'] ) . ';';
				}
				if ( isset( $popup['backgroundOpacity'] ) && ! empty( $popup['backgroundOpacity'] ) ) {
					$css .= 'opacity:' . $popup['backgroundOpacity'] . ';';
				}
				$css .= '}';
				$css .= '.glightbox-container.kadence-popup-' . $unique_id . ' .gclose, .glightbox-container.kadence-popup-' . $unique_id . ' .gnext, .glightbox-container.kadence-popup-' . $unique_id . ' .gprev{';
					if ( isset( $popup['background'] ) && ! empty( $popup['background'] ) ) {
						$css .= 'background:' . $this->kadence_color_output( $popup['background'] ) . ';';
					}
				$css .= '}';
			}
			if ( isset( $popup['closeColor'] ) && ! empty( $popup['closeColor'] ) ) {
				$css .= '.glightbox-container.kadence-popup-' . $unique_id . ' .gclose path, .glightbox-container.kadence-popup-' . $unique_id . ' .gnext path, .glightbox-container.kadence-popup-' . $unique_id . ' .gprev path{';
				$css .= 'fill:' . $this->kadence_color_output( $popup['closeColor'] ) . ';';
				$css .= '}';
			}
			if ( isset( $popup['maxWidth'] ) && ! empty( $popup['maxWidth'] ) ) {
				$css .= '.glightbox-container.kadence-popup-' . $unique_id . ' .gslide-video, .glightbox-container.kadence-popup-' . $unique_id . ' .gvideo-local {';
				$css .= 'max-width:' . $popup['maxWidth'] . ( $popup['maxWidthUnit'] ? $popup['maxWidthUnit'] : 'px' ) . ' !important;';
				$css .= '}';
			}
		}
		return $css;
	}
	/**
	 * Grabs the Google Fonts that are needed so we can load in the head.
	 *
	 * @param array $attr the blocks attr.
	 */
	public function blocks_image_overlay_googlefont_check( $attr ) {
		if ( isset( $attr['googleFont'] ) && $attr['googleFont'] && ( ! isset( $attr['loadGoogleFont'] ) || $attr['loadGoogleFont'] == true ) && isset( $attr['typography'] ) ) {
			// Check if the font has been added yet
			if ( ! array_key_exists( $attr['typography'], self::$gfonts ) ) {
				$add_font = array(
					'fontfamily' => $attr['typography'],
					'fontvariants' => ( isset( $attr['fontVariant'] ) && ! empty( $attr['fontVariant'] ) ? array( $attr['fontVariant'] ) : array() ),
					'fontsubsets' => ( isset( $attr['fontSubset'] ) && !empty( $attr['fontSubset'] ) ? array( $attr['fontSubset'] ) : array() ),
				);
				self::$gfonts[$attr['typography']] = $add_font;
			} else {
				if ( ! in_array( $attr['fontVariant'], self::$gfonts[ $attr['typography'] ]['fontvariants'], true ) ) {
					array_push( self::$gfonts[ $attr['typography'] ]['fontvariants'], $attr['fontVariant'] );
				}
				if ( ! in_array( $attr['fontSubset'], self::$gfonts[ $attr['typography'] ]['fontsubsets'], true ) ) {
					array_push( self::$gfonts[ $attr['typography'] ]['fontsubsets'], $attr['fontSubset'] );
				}
			}
		}
		if ( isset( $attr['sgoogleFont'] ) && $attr['sgoogleFont'] && ( ! isset( $attr['sloadGoogleFont'] ) || $attr['sloadGoogleFont'] == true ) && isset( $attr['stypography'] ) ) {
			// Check if the font has been added yet
			if ( ! array_key_exists( $attr['stypography'], self::$gfonts ) ) {
				$add_font = array(
					'fontfamily' => $attr['stypography'],
					'fontvariants' => ( isset( $attr['sfontVariant'] ) && ! empty( $attr['sfontVariant'] ) ? array( $attr['sfontVariant']) : array() ),
					'fontsubsets' => ( isset( $attr['sfontSubset'] ) && !empty( $attr['sfontSubset'] ) ? array( $attr['sfontSubset'] ) : array() ),
				);
				self::$gfonts[$attr['stypography']] = $add_font;
			} else {
				if ( isset( $attr['sfontVariant'] ) && ! in_array( $attr['sfontVariant'], self::$gfonts[ $attr['stypography'] ]['fontvariants'], true ) ) {
					array_push( self::$gfonts[ $attr['stypography'] ]['fontvariants'], $attr['sfontVariant'] );
				}
				if ( isset( $attr['sfontSubset'] ) && ! in_array( $attr['sfontSubset'], self::$gfonts[ $attr['stypography'] ]['fontsubsets'], true ) ) {
					array_push( self::$gfonts[ $attr['stypography'] ]['fontsubsets'], $attr['sfontSubset'] );
				}
			}
		}
	}
	/**
	 * Builds CSS for Image Overlay block.
	 *
	 * @param array  $attr the blocks attr.
	 * @param string $unique_id the blocks attr ID.
	 */
	public function blocks_image_overlay_array( $attr, $unique_id ) {
		$css = '';
		$align_prop = isset( $attr['align'] ) ? $attr['align'] : '';
		if ( empty( $align_prop ) && isset( $attr['blockAlignment'] ) ) {
			$align_prop = $attr['blockAlignment'];
		}
		if ( isset( $attr['maxWidth'] ) && is_numeric( $attr['maxWidth'] ) ) {
			$css .= '.kt-img-overlay' . $unique_id . ' {';
			$css .= 'max-width:' . $attr['maxWidth'] . ( ! empty( $attr['maxWidthUnit'] ) ? $attr['maxWidthUnit'] : 'px' ) . ';';
			$css .= 'width:100%;';
			$css .= '}';
			$css .= '.kb-section-dir-horizontal > .kt-inside-inner-col > .kt-img-overlay' . $unique_id . ' {';
			$css .= 'margin-left:unset;';
			$css .= 'margin-right:unset;';
			$css .= '}';
		} elseif ( isset( $attr['imgWidth'] ) && ! empty( $attr['imgWidth'] ) && ( ! isset( $align_prop ) || ( isset( $align_prop ) && 'wide' !== $align_prop && 'full' !== $align_prop ) ) ) {
			$css .= '.kt-img-overlay' . $unique_id . ' .kt-image-overlay-wrap {';
			$css .= 'max-width:' . $attr['imgWidth'] . 'px;';
			$css .= '}';
		}
		if ( isset( $attr['useSizeRatio'] ) && $attr['useSizeRatio'] ) {
			$ratio = '100';
			if ( isset( $attr['sizeRatio'] ) && ! empty( $attr['sizeRatio'] ) ) {
				$ratio = $attr['sizeRatio'];
			}
		} else {
			$ratio = '62.5';
			if ( isset( $attr['imgWidth'] ) && ! empty( $attr['imgWidth'] ) && isset( $attr['imgHeight'] ) && ! empty( $attr['imgHeight'] ) ) {
				$ratio = round( ( absint( $attr['imgHeight'] ) / absint( $attr['imgWidth'] ) ) * 100, 4 );
			}
		}
		$css .= '.kt-img-overlay' . $unique_id . ' .kt-image-overlay-wrap .kt-block-intrisic {';
		$css .= 'padding-bottom:' . $ratio . '%;';
		$css .= '}';
		if ( isset( $attr['overlayHoverOpacity'] ) && is_numeric( $attr['overlayHoverOpacity'] ) ) {
			$css .= '.kt-img-overlay' . $unique_id . ' .kt-image-overlay-wrap:hover .kt-image-overlay-color-wrapper, .kt-image-overlay-wrap:has(:focus-visible) .kt-image-overlay-color-wrapper {';
			$css .= 'opacity:' . $attr['overlayHoverOpacity'] . ' !important;';
			$css .= '}';
		}
		if ( isset( $attr['titleSize'] ) || isset( $attr['titleLineHeight'] ) || isset( $attr['typography'] ) || isset( $attr['fontWeight'] ) || isset( $attr['titleTextTransform'] ) || isset( $attr['letterSpacing'] ) ) {
			$css .= '.kt-img-overlay' . $unique_id . ' .kt-image-overlay-message .image-overlay-title {';
			if ( isset( $attr['titleSize'] ) && is_array( $attr['titleSize'] ) && !empty( $attr['titleSize'][ 0 ] ) ) {
				$css .= 'font-size:' . $attr['titleSize'][0] . ( ! isset( $attr['sizeType'] ) ? 'px' : $attr['sizeType'] ) . ';';
			}
			if ( isset( $attr['titleLineHeight'] ) && is_array( $attr['titleLineHeight'] ) && !empty( $attr['titleLineHeight'][ 0 ] ) ) {
				$css .= 'line-height:' . $attr['titleLineHeight'][0] . ( ! isset( $attr['lineType'] ) ? 'px' : $attr['lineType'] ) . ';';
			}
			if ( isset( $attr['typography'] ) && ! empty( $attr['typography'] ) ) {
				$css .= 'font-family:' . $attr['typography'] . ';';
			}
			if ( isset( $attr['letterSpacing'] ) && ! empty( $attr['letterSpacing'] ) ) {
				$css .= 'letter-spacing:' . $attr['letterSpacing'] . 'px;';
			}
			if ( ! empty( $attr['titleTextTransform'] ) ) {
				$css .= 'text-transform:' . $attr['titleTextTransform'] . ';';
			}
			if ( isset( $attr['fontWeight'] ) && ! empty( $attr['fontWeight'] ) ) {
				$css .= 'font-weight:' . $attr['fontWeight'] . ';';
			}
			if ( isset( $attr['fontStyle'] ) && ! empty( $attr['fontStyle'] ) ) {
				$css .= 'font-style:' . $attr['fontStyle'] . ';';
			}
			$css .= '}';
		}
		if ( isset( $attr['subtitleSize'] ) || isset( $attr['subtitleLineHeight'] ) || isset( $attr['sfontWeight'] ) || isset( $attr['stypography'] ) || isset( $attr['sTextTransform'] ) || isset( $attr['sletterSpacing'] ) ) {
			$css .= '.kt-img-overlay' . $unique_id . ' .kt-image-overlay-message .image-overlay-subtitle {';
				if ( isset( $attr['subtitleSize'] ) && is_array( $attr['subtitleSize'] ) && ! empty( $attr['subtitleSize'][ 0 ] ) ) {
					$css .= 'font-size:' . $attr['subtitleSize'][0] . ( ! isset( $attr['subSizeType'] ) ? 'px' : $attr['subSizeType'] ) . ';';
				}
				if ( isset( $attr['subtitleLineHeight'] ) && is_array( $attr['subtitleLineHeight'] ) && !empty( $attr['subtitleLineHeight'][ 0 ] ) ) {
					$css .= 'line-height:' . $attr['subtitleLineHeight'][0] . ( ! isset( $attr['subLineType'] ) ? 'px' : $attr['subLineType'] ) . ';';
				}
				if ( isset( $attr['stypography'] ) && ! empty( $attr['stypography'] ) ) {
					$css .= 'font-family:' . $attr['stypography'] . ';';
				}
				if ( isset( $attr['sletterSpacing'] ) && ! empty( $attr['sletterSpacing'] ) ) {
					$css .= 'letter-spacing:' . $attr['sletterSpacing'] . 'px;';
				}
				if ( ! empty( $attr['sTextTransform'] ) ) {
					$css .= 'text-transform:' . $attr['sTextTransform'] . ';';
				}
				if ( isset( $attr['sfontWeight'] ) && ! empty( $attr['sfontWeight'] ) ) {
					$css .= 'font-weight:' . $attr['sfontWeight'] . ';';
				}
				if ( isset( $attr['sfontStyle'] ) && ! empty( $attr['sfontStyle'] ) ) {
					$css .= 'font-style:' . $attr['sfontStyle'] . ';';
				}
			$css .= '}';
		}
		if ( ( isset( $attr['titleSize'] ) && is_array( $attr['titleSize'] ) && ! empty( $attr['titleSize'][ 1 ] ) ) || isset( $attr['titleLineHeight'] ) && is_array( $attr['titleLineHeight'] ) && ! empty( $attr['titleLineHeight'][ 1 ] ) ) {
			$css .= '@media (min-width: 767px) and (max-width: 1024px) {';
				$css .= '.kt-img-overlay' . $unique_id . ' .kt-image-overlay-message .image-overlay-title {';
				if ( isset( $attr['titleSize'] ) && is_array( $attr['titleSize'] ) && !empty( $attr['titleSize'][ 1 ] ) ) {
					$css .= 'font-size:' . $attr['titleSize'][ 1 ] . ( ! isset( $attr['sizeType'] ) ? 'px' : $attr['sizeType'] ) . ';';
				}
				if ( isset( $attr['titleLineHeight'] ) && is_array( $attr['titleLineHeight'] ) && !empty( $attr['titleLineHeight'][ 1 ] ) ) {
					$css .= 'line-height:' . $attr['titleLineHeight'][ 1 ] . ( ! isset( $attr['lineType'] ) ? 'px' : $attr['lineType'] ) . ';';
				}
				$css .= '}';
			$css .= '}';
		}
		if ( ( isset( $attr['subtitleSize'] ) && is_array( $attr['subtitleSize'] ) && ! empty( $attr['subtitleSize'][ 1 ] ) ) || isset( $attr['subtitleLineHeight'] ) && is_array( $attr['subtitleLineHeight'] ) && ! empty( $attr['subtitleLineHeight'][ 1 ] ) ) {
			$css .= '@media (min-width: 767px) and (max-width: 1024px) {';
				$css .= '.kt-img-overlay' . $unique_id . ' .kt-image-overlay-message .image-overlay-subtitle {';
				if ( isset( $attr['subtitleSize'] ) && is_array( $attr['subtitleSize'] ) && !empty( $attr['subtitleSize'][ 1 ] ) ) {
					$css .= 'font-size:' . $attr['subtitleSize'][ 1 ] . ( ! isset( $attr['subSizeType'] ) ? 'px' : $attr['subSizeType'] ) . ';';
				}
				if ( isset( $attr['subtitleLineHeight'] ) && is_array( $attr['subtitleLineHeight'] ) && !empty( $attr['subtitleLineHeight'][ 1 ] ) ) {
					$css .= 'line-height:' . $attr['subtitleLineHeight'][ 1 ] . ( ! isset( $attr['subLineType'] ) ? 'px' : $attr['subLineType'] ) . ';';
				}
				$css .= '}';
			$css .= '}';
		}
		if ( ( isset( $attr['titleSize'] ) && is_array( $attr['titleSize'] ) && ! empty( $attr['titleSize'][ 2 ] ) ) || isset( $attr['titleLineHeight'] ) && is_array( $attr['titleLineHeight'] ) && ! empty( $attr['titleLineHeight'][ 2 ] ) ) {
			$css .= '@media (max-width: 767px) {';
				$css .= '.kt-img-overlay' . $unique_id . ' .kt-image-overlay-message .image-overlay-title {';
				if ( isset( $attr['titleSize'] ) && is_array( $attr['titleSize'] ) && !empty( $attr['titleSize'][ 2 ] ) ) {
					$css .= 'font-size:' . $attr['titleSize'][ 2 ] . ( ! isset( $attr['sizeType'] ) ? 'px' : $attr['sizeType'] ) . ';';
				}
				if ( isset( $attr['titleLineHeight'] ) && is_array( $attr['titleLineHeight'] ) && !empty( $attr['titleLineHeight'][ 2 ] ) ) {
					$css .= 'line-height:' . $attr['titleLineHeight'][ 2 ] . ( ! isset( $attr['lineType'] ) ? 'px' : $attr['lineType'] ) . ';';
				}
				$css .= '}';
			$css .= '}';
		}
		if ( ( isset( $attr['subtitleSize'] ) && is_array( $attr['subtitleSize'] ) && ! empty( $attr['subtitleSize'][ 2 ] ) ) || isset( $attr['subtitleLineHeight'] ) && is_array( $attr['subtitleLineHeight'] ) && ! empty( $attr['subtitleLineHeight'][ 2 ] ) ) {
			$css .= '@media (max-width: 767px) {';
				$css .= '.kt-img-overlay' . $unique_id . ' .kt-image-overlay-message .image-overlay-subtitle {';
				if ( isset( $attr['subtitleSize'] ) && is_array( $attr['subtitleSize'] ) && !empty( $attr['subtitleSize'][ 2 ] ) ) {
					$css .= 'font-size:' . $attr['subtitleSize'][ 2 ] . ( ! isset( $attr['subSizeType'] ) ? 'px' : $attr['subSizeType'] ) . ';';
				}
				if ( isset( $attr['subtitleLineHeight'] ) && is_array( $attr['subtitleLineHeight'] ) && !empty( $attr['subtitleLineHeight'][ 2 ] ) ) {
					$css .= 'line-height:' . $attr['subtitleLineHeight'][ 2 ] . ( ! isset( $attr['subLineType'] ) ? 'px' : $attr['subLineType'] ) . ';';
				}
				$css .= '}';
			$css .= '}';
		}
		return $css;
	}
	/**
	 * Builds CSS for product carousel.
	 *
	 * @param array  $attr the blocks attr.
	 * @param string $unique_id the blocks attr ID.
	 */
	public function blocks_product_carousel_array( $attr, $unique_id ) {
		$css = '';
		// if ( isset( $attr['columnGap'] ) ) {
		// 	$css .= '.kt-blocks-carousel' . $unique_id . ' .products li.product.slick-slide {';
		// 		$css .= 'padding:0px;';
		// 		$css .= 'margin-right:' . $attr['columnGap'] / 2 . 'px;';
		// 		$css .= 'margin-left:' . $attr['columnGap'] / 2 . 'px;';
		// 	$css .= '}';
		// 	$css .= '.kt-blocks-carousel' . $unique_id . ' .kt-product-carousel-wrap {';
		// 		$css .= 'margin-left:-' . $attr['columnGap'] / 2 . 'px;';
		// 		$css .= 'margin-right:-' . $attr['columnGap'] / 2 . 'px;';
		// 	$css .= '}';
		// 	$css .= '.kt-blocks-carousel' . $unique_id . ' .kt-product-carousel-wrap .slick-prev {';
		// 		$css .= 'left:' . $attr['columnGap'] / 2 . 'px;';
		// 	$css .= '}';
		// 	$css .= '.kt-blocks-carousel' . $unique_id . ' .kt-product-carousel-wrap .slick-next {';
		// 		$css .= 'right:' . $attr['columnGap'] / 2 . 'px;';
		// 	$css .= '}';
		// }
		return $css;
	}
	/**
	 * Builds CSS for SplitContent block.
	 *
	 * @param array  $attr the blocks attr.
	 * @param string $unique_id the blocks attr ID.
	 */
	public function blocks_splitcontent_array( $attr, $unique_id ) {
		$css = '';
		$css .= '.kt-sc' . $unique_id . ' .kt-sc-imgcol {';
		$min_height = ( isset( $attr['minHeight'] ) && is_numeric( $attr['minHeight'] ) ? $attr['minHeight'] : '450' );
		$css .= 'min-height:' . $min_height . 'px;';
		if ( isset( $attr['backgroundColor'] ) && ! empty( $attr['backgroundColor'] ) ) {
			$css .= 'background-color:' . $this->kadence_color_output( $attr['backgroundColor'] ) . ';';
		}
		$media_size = ( isset( $attr['mediaSize'] ) && ! empty( $attr['mediaSize'] ) ? $attr['mediaSize'] : 'auto' );
		if ( 'cover' === $media_size ) {
			$media_type = ( isset( $attr['mediaType'] ) && ! empty( $attr['mediaType'] ) ? $attr['mediaType'] : 'image' );
			$css .= 'background-size:' . $attr['mediaSize'] . ';';
			if ( isset( $attr['mediaUrl'] ) && ! empty( $attr['mediaUrl'] ) && 'video' !== $media_type ) {
				$css .= 'background-image: url(' . $attr['mediaUrl'] . ');';
			}
		}
		$css .= '}';
		if ( 'contain' === $media_size ) {
			$css .= '.kt-sc' . $unique_id . ' .kt-split-content-img {';
			$css .= 'max-height:' . $min_height . 'px;';
			$css .= '}';
		}
		if ( isset( $attr['contentMargin'] ) || isset( $attr['contentPadding'] ) ) {
			$css .= '.kt-sc' . $unique_id . ' .kt-sc-textcol {';
			if ( isset( $attr['contentMargin'] ) && is_array( $attr['contentMargin'] ) ) {
				$css .= 'margin:' . $attr['contentMargin'][0] . 'px ' . $attr['contentMargin'][1] . 'px ' . $attr['contentMargin'][2] . 'px ' . $attr['contentMargin'][3] . 'px;';
			}
			if ( isset( $attr['contentPadding'] ) && is_array( $attr['contentPadding'] ) ) {
				$css .= 'padding:' . $attr['contentPadding'][0] . 'px ' . $attr['contentPadding'][1] . 'px ' . $attr['contentPadding'][2] . 'px ' . $attr['contentPadding'][3] . 'px;';
			}
			$css .= '}';
		}
		if ( isset( $attr['contentMarginTablet'] ) || isset( $attr['contentPaddingTablet'] ) ) {
			$css .= '@media (min-width: 767px) and (max-width: 1024px) {';
				$css .= '.kt-sc' . $unique_id . ' .kt-sc-textcol {';
					if ( isset( $attr['contentMarginTablet'] ) && is_array( $attr['contentMarginTablet'] ) ) {
						$css .= 'margin:' . ( $attr['contentMarginTablet'][0] ? $attr['contentMarginTablet'][0] : '0' ) . 'px ' . ( $attr['contentMarginTablet'][1] ? $attr['contentMarginTablet'][1] : '0' ) . 'px ' . ( $attr['contentMarginTablet'][2] ? $attr['contentMarginTablet'][2] : '0' ) . 'px ' . ( $attr['contentMarginTablet'][3] ? $attr['contentMarginTablet'][3] : '0' ) . 'px;';
					}
					if ( isset( $attr['contentPaddingTablet'] ) && is_array( $attr['contentPaddingTablet'] ) ) {
						$css .= 'padding:' . ( $attr['contentPaddingTablet'][0] ? $attr['contentPaddingTablet'][0] : '0' ) . 'px ' . ( $attr['contentPaddingTablet'][1] ? $attr['contentPaddingTablet'][1] : '0' ) . 'px ' . ( $attr['contentPaddingTablet'][2] ? $attr['contentPaddingTablet'][2] : '0' ) . 'px ' . ( $attr['contentPaddingTablet'][3] ? $attr['contentPaddingTablet'][3] : '0' ) . 'px;';
					}
				$css .= '}';
			$css .= '}';
		}
		if ( isset( $attr['contentMarginMobile'] ) || isset( $attr['contentPaddingMobile'] ) ) {
			$css .= '@media (max-width: 767px) {';
				$css .= '.kt-sc' . $unique_id . ' .kt-sc-textcol {';
					if ( isset( $attr['contentMarginMobile'] ) && is_array( $attr['contentMarginMobile'] ) ) {
						$css .= 'margin:' . ( $attr['contentMarginMobile'][0] ? $attr['contentMarginMobile'][0] : '0' ) . 'px ' . ( $attr['contentMarginMobile'][1] ? $attr['contentMarginMobile'][1] : '0' ) . 'px ' . ( $attr['contentMarginMobile'][2] ? $attr['contentMarginMobile'][2] : '0' ) . 'px ' . ( $attr['contentMarginMobile'][3] ? $attr['contentMarginMobile'][3] : '0' ) . 'px;';
					}
					if ( isset( $attr['contentPaddingMobile'] ) && is_array( $attr['contentPaddingMobile'] ) ) {
						$css .= 'padding:' . ( $attr['contentPaddingMobile'][0] ? $attr['contentPaddingMobile'][0] : '0' ) . 'px ' . ( $attr['contentPaddingMobile'][1] ? $attr['contentPaddingMobile'][1] : '0' ) . 'px ' . ( $attr['contentPaddingMobile'][2] ? $attr['contentPaddingMobile'][2] : '0' ) . 'px ' . ( $attr['contentPaddingMobile'][3] ? $attr['contentPaddingMobile'][3] : '0' ) . 'px;';
					}
				$css .= '}';
			$css .= '}';
		}
		return $css;
	}
	/**
	 * Adds var to color output if needed.
	 *
	 * @param string $color the output color.
	 */
	public function kadence_color_output( $color, $opacity = null ) {
		if ( strpos( $color, 'palette' ) === 0 ) {
			$color = 'var(--global-' . $color . ')';
		} elseif ( isset( $opacity ) && is_numeric( $opacity ) && 1 !== (int) $opacity ) {
			$color = $this->hex2rgba( $color, $opacity );
		}
		return $color;
	}
}
Kadence_Blocks_Pro_Frontend::get_instance();
