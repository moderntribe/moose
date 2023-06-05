<?php
/**
 * Dynamic_Content Render
 *
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the Dynamic_Content
 *
 * @category class
 */
class Kadence_Blocks_Pro_Dynamic_HTML_Block {
	/**
	 * Google fonts to enqueue
	 *
	 * @var array
	 */
	public static $gfonts = array();

	/**
	 * Seen IDs.
	 *
	 * @var array
	 */
	public static $seen_ids = array();

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
		add_action( 'init', array( $this, 'dynamic_content_block' ), 20 );
		//add_action( 'init', array( $this, 'setup_content_filter' ), 9 );
		add_action( 'enqueue_block_assets', array( $this, 'blocks_assets' ) );
		add_action( 'wp_head', array( $this, 'frontend_gfonts' ), 85 );
	}
	/**
	 * Add filters for element content output.
	 */
	public function setup_content_filter() {
		global $wp_embed;
		add_filter( 'dynamic_html_content_filter', array( $wp_embed, 'run_shortcode' ), 8 );
		add_filter( 'dynamic_html_content_filter', array( $wp_embed, 'autoembed'     ), 8 );
		add_filter( 'dynamic_html_content_filter', 'do_blocks' );
		add_filter( 'dynamic_html_content_filter', 'wptexturize' );
		add_filter( 'dynamic_html_content_filter', 'convert_chars' );
		// Don't use this unless classic editor add_filter( 'dynamic_html_content_filter', 'wpautop' );
		add_filter( 'dynamic_html_content_filter', 'shortcode_unautop' );
		add_filter( 'dynamic_html_content_filter', 'wp_filter_content_tags' );
		add_filter( 'dynamic_html_content_filter', 'do_shortcode', 11 );
		add_filter( 'dynamic_html_content_filter', 'convert_smilies', 20 );
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
						'fontsubsets' => ( isset( $gfont_values['fontsubsets'] ) && !empty( $gfont_values['fontsubsets'] ) ? $gfont_values['fontsubsets'] : array() ),
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
	 * Render Inline CSS helper function
	 *
	 * @param array  $css the css for each rendered block.
	 * @param string $style_id the unique id for the rendered style.
	 * @param bool   $in_content the bool for whether or not it should run in content.
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
	 *
	 * Register and Enqueue block assets
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
	 * Registers scripts and styles.
	 */
	public function register_scripts() {
		// If in the backend, bail out.
		if ( is_admin() ) {
			return;
		}
		// Lets register all the block styles.
		wp_register_style( 'kadence-blocks-dynamic-html', KBP_URL . 'dist/build/block-css/style-dynamic-html-styles.css', array(), KBP_VERSION );
	}
	/**
	 * Register the dynamic block.
	 *
	 * @return void
	 */
	public function dynamic_content_block() {

		// Only load if Gutenberg is available.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		// Hook server side rendering into render callback.
		register_block_type(
			'kadence/dynamichtml',
			array(
				'attributes' => array(
					'uniqueID' => array(
						'type' => 'string',
					),
					'wrapTag' => array(
						'type' => 'string',
						'default' => 'div',
					),
					'innerWrap' => array(
						'type' => 'string',
						'default' => '',
					),
					'field' => array(
						'type' => 'string',
						'default' => '',
					),
					'source' => array(
						'type' => 'string',
						'default' => '',
					),
					'metaField' => array(
						'type' => 'string',
						'default' => '',
					),
					'customMeta' => array(
						'type' => 'string',
						'default' => '',
					),
					'relate' => array(
						'type' => 'string',
						'default' => '',
					),
					'relcustom' => array(
						'type' => 'string',
						'default' => '',
					),
					'alignment' => array(
						'type' => 'array',
						'default' => array( 'left', '', '' ),
						'items'   => array(
							'type' => 'string',
						),
					),
					'linkStyle' => array(
						'type' => 'string',
					),
					// Color.
					'textColor' => array(
						'type' => 'string',
					),
					'headingColor' => array(
						'type' => 'string',
					),
					'linkColor' => array(
						'type' => 'string',
					),
					'linkHoverColor' => array(
						'type' => 'string',
					),
					// Container.
					'padding' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
						'items'   => array(
							'type' => 'integer',
						),
					),
					'tabletPadding' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
						'items'   => array(
							'type' => 'integer',
						),
					),
					'mobilePadding' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
						'items'   => array(
							'type' => 'integer',
						),
					),
					'paddingType' => array(
						'type' => 'string',
						'default' => 'px',
					),
					'margin' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
						'items'   => array(
							'type' => 'integer',
						),
					),
					'tabletMargin' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
						'items'   => array(
							'type' => 'integer',
						),
					),
					'mobileMargin' => array(
						'type' => 'array',
						'default' => array( '', '', '', '' ),
						'items'   => array(
							'type' => 'integer',
						),
					),
					'marginType' => array(
						'type' => 'string',
						'default' => 'px',
					),
					'textTypography'=> array(
						'type' => 'array',
						'default' => array(
							array(
								'size' => array( '', '', '' ),
								'sizeType' => 'px',
								'lineHeight' => array( '', '', '' ),
								'lineType' => 'px',
								'letterSpacing' => array( '', '', '' ),
								'letterSpacingType' => 'px',
								'textTransform' => '',
								'family' => '',
								'google' => '',
								'style' => '',
								'weight' => '',
								'variant' => '',
								'subset' => '',
								'loadGoogle' => true,
							)
						),
						'items'   => array(
							'type' => 'object',
						),
					),
					'h1Typography'=> array(
						'type' => 'array',
						'default' => array(
							array(
								'size' => array( '', '', '' ),
								'sizeType' => 'px',
								'lineHeight' => array( '', '', '' ),
								'lineType' => 'px',
								'letterSpacing' => array( '', '', '' ),
								'letterSpacingType' => 'px',
								'textTransform' => '',
								'family' => '',
								'google' => '',
								'style' => '',
								'weight' => '',
								'variant' => '',
								'subset' => '',
								'loadGoogle' => true,
							)
						),
						'items'   => array(
							'type' => 'object',
						),
					),
					'h2Typography'=> array(
						'type' => 'array',
						'default' => array(
							array(
								'size' => array( '', '', '' ),
								'sizeType' => 'px',
								'lineHeight' => array( '', '', '' ),
								'lineType' => 'px',
								'letterSpacing' => array( '', '', '' ),
								'letterSpacingType' => 'px',
								'textTransform' => '',
								'family' => '',
								'google' => '',
								'style' => '',
								'weight' => '',
								'variant' => '',
								'subset' => '',
								'loadGoogle' => true,
							)
						),
						'items'   => array(
							'type' => 'object',
						),
					),
					'h3Typography'=> array(
						'type' => 'array',
						'default' => array(
							array(
								'size' => array( '', '', '' ),
								'sizeType' => 'px',
								'lineHeight' => array( '', '', '' ),
								'lineType' => 'px',
								'letterSpacing' => array( '', '', '' ),
								'letterSpacingType' => 'px',
								'textTransform' => '',
								'family' => '',
								'google' => '',
								'style' => '',
								'weight' => '',
								'variant' => '',
								'subset' => '',
								'loadGoogle' => true,
							)
						),
						'items'   => array(
							'type' => 'object',
						),
					),
					'h4Typography'=> array(
						'type' => 'array',
						'default' => array(
							array(
								'size' => array( '', '', '' ),
								'sizeType' => 'px',
								'lineHeight' => array( '', '', '' ),
								'lineType' => 'px',
								'letterSpacing' => array( '', '', '' ),
								'letterSpacingType' => 'px',
								'textTransform' => '',
								'family' => '',
								'google' => '',
								'style' => '',
								'weight' => '',
								'variant' => '',
								'subset' => '',
								'loadGoogle' => true,
							)
						),
						'items'   => array(
							'type' => 'object',
						),
					),
					'h5Typography'=> array(
						'type' => 'array',
						'default' => array(
							array(
								'size' => array( '', '', '' ),
								'sizeType' => 'px',
								'lineHeight' => array( '', '', '' ),
								'lineType' => 'px',
								'letterSpacing' => array( '', '', '' ),
								'letterSpacingType' => 'px',
								'textTransform' => '',
								'family' => '',
								'google' => '',
								'style' => '',
								'weight' => '',
								'variant' => '',
								'subset' => '',
								'loadGoogle' => true,
							)
						),
						'items'   => array(
							'type' => 'object',
						),
					),
					'h6Typography'=> array(
						'type' => 'array',
						'default' => array(
							array(
								'size' => array( '', '', '' ),
								'sizeType' => 'px',
								'lineHeight' => array( '', '', '' ),
								'lineType' => 'px',
								'letterSpacing' => array( '', '', '' ),
								'letterSpacingType' => 'px',
								'textTransform' => '',
								'family' => '',
								'google' => '',
								'style' => '',
								'weight' => '',
								'variant' => '',
								'subset' => '',
								'loadGoogle' => true,
							)
						),
						'items'   => array(
							'type' => 'object',
						),
					),
					'enableH1' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'enableH2' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'enableH3' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'enableH4' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'enableH5' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'enableH6' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'showAllFields' => array(
						'type' => 'boolean',
						'default' => false,
					),
				),
				'render_callback' => array( $this, 'render_dynamic_content' ),
				'editor_script'   => 'kadence-blocks-pro-js',
				'editor_style'    => 'kadence-blocks-pro-editor-css',
			)
		);
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
	 * Server rendering for Post Block
	 */
	public function render_dynamic_content( $attributes, $content, $block ) {
		if ( ! wp_style_is( 'kadence-blocks-dynamic-html', 'enqueued' ) ) {
			wp_enqueue_style( 'kadence-blocks-dynamic-html' );
		}
		global $post;
		// Current || Post id.
		$source = ! empty( $attributes['source'] ) ? $attributes['source'] : '';
		$field_src = ! empty( $attributes['field'] ) ? $attributes['field'] : '';
		// Bail if nothing to show.
		if ( empty( $field_src ) ) {
			return '';
		}
		if ( 'post|post_content' === $field_src ) {
			$source = ! empty( $source ) ? $source : $post->ID;
			if ( isset( self::$seen_ids[ $source ] ) ) {
				$is_debug = defined( 'WP_DEBUG' ) && WP_DEBUG &&
				defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY;

				return $is_debug ?
					// translators: Visible only in the front end, this warning takes the place of a faulty block.
					__( '[block rendering halted, block creates an endless loop]', 'kadence_blocks_pro' ) :
					'';
			}
			if ( 'post|post_content' === $field_src ) {
				self::$seen_ids[ $source ] = true;
			}
		}
		$group = 'post';
		if ( ! empty( $field_src ) && strpos( $field_src, '|' ) !== false ) {
			$field_split = explode( '|', $field_src, 2 );
			$group = ( isset( $field_split[0] ) && ! empty( $field_split[0] ) ? $field_split[0] : 'post' );
			$field = ( isset( $field_split[1] ) && ! empty( $field_split[1] ) ? $field_split[1] : '' );
		}
		$args = array(
			'source'       => ! empty( $source ) ? $source : 'current',
			'group'        => $group,
			'type'         => 'html',
			'field'        => $field,
			'custom'       => ! empty( $attributes['customMeta'] ) ? $attributes['customMeta'] : '',
			'para'         => ! empty( $attributes['metaField'] ) ? $attributes['metaField'] : '',
			'relate'       => ! empty( $attributes['relate'] ) ? $attributes['relate'] : '',
			'relcustom'    => ! empty( $attributes['relcustom'] ) ? $attributes['relcustom'] : '',
		);
		$dynamic_class = Kadence_Blocks_Pro_Dynamic_Content::get_instance();
		$the_content   = $dynamic_class->get_content( $args );
		// Bail if nothing to show.
		if ( empty( $the_content ) ) {
			return '';
		}
		$classes        = array( 'wp-block-kadence-dynamichtml', 'kb-dynamic-html' );
		if ( ! empty( $attributes['uniqueID'] ) ) {
			$classes[] = 'kb-dynamic-html-id-' . $attributes['uniqueID'];
		}
		if ( ! empty( $attributes['linkStyle'] ) ) {
			$classes[] = 'kb-dynamic-html-link-style-' . $attributes['linkStyle'];
		}
		if ( ! empty( $attributes['alignment'][0] ) ) {
			$classes[] = 'kb-dynamic-html-alignment-' . $attributes['alignment'][0];
		}
		if ( ! empty( $attributes['alignment'][1] ) ) {
			$classes[] = 'kb-dynamic-html-tablet-alignment-' . $attributes['alignment'][1];
		}
		if ( ! empty( $attributes['alignment'][2] ) ) {
			$classes[] = 'kb-dynamic-html-mobile-alignment-' . $attributes['alignment'][2];
		}
		if ( ! empty( $attributes['className'] ) ) {
			$classes[] = $attributes['className'];
		}
		$wrap_tag = ( ! empty( $attributes['wrapTag'] ) ? $attributes['wrapTag'] : 'div' );
		ob_start();
		echo '<' . esc_attr( $wrap_tag ) . ' class="' . esc_attr( implode( ' ', $classes ) ) . '">';
		if ( ! empty( $attributes['innerWrap'] ) ) {
			echo '<' . esc_attr( $attributes['innerWrap'] ) . ' class="kb-dynamic-html-inner-wrap">';
		}
		echo $the_content;
		if ( ! empty( $attributes['innerWrap'] ) ) {
			echo '</' . esc_attr( $attributes['innerWrap'] ) . '>';
		}
		echo '</' . esc_attr( $wrap_tag ) . '>';

		$content = ob_get_contents();
		ob_end_clean();
		if ( isset( $attributes['uniqueID'] ) ) {
			$unique_id = $attributes['uniqueID'];
			$style_id = 'kt-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) && apply_filters( 'kadence_blocks_render_inline_css', true, 'dynamichtml', $unique_id ) ) {
				if ( ! doing_filter( 'the_content' ) ) {
					if ( ! wp_style_is( 'kadence-blocks-dynamic-content', 'done' ) ) {
						wp_print_styles( 'kadence-blocks-dynamic-content' );
					}
				} else {
					if ( ! wp_style_is( 'kadence-blocks-dynamic-content', 'done' ) ) {
						ob_start();
							wp_print_styles( 'kadence-blocks-dynamic-content' );
						$content = ob_get_clean() . $content;
					}
				}
				$css = $this->output_css( $attributes, $unique_id );
				if ( ! empty( $css ) ) {
					if ( doing_filter( 'the_content' ) || apply_filters( 'kadence_blocks_force_render_inline_css_in_content', false, 'dynamichtml', $unique_id ) ) {
						$content = '<style id="' . $style_id . '" type="text/css">' . $css . '</style>' . $content;
					} else {
						$this->render_inline_css( $css, $style_id, true );
					}
				}
			}
		}
		if ( 'post|post_content' === $field_src ) {
			unset( self::$seen_ids[ $source ] );
		}
		return $content;
	}
	/**
	 * Output CSS styling for user info.
	 *
	 * @param array $attributes the block attributes
	 * @param array $unique_id the css attributes
	 */
	public function output_css( $attributes, $unique_id ) {
		if ( ! class_exists( 'Kadence_Blocks_Pro_CSS' ) ) {
			return '';
		}
		$css                    = new Kadence_Blocks_Pro_CSS();
		$media_query            = array();
		$media_query['mobile']  = apply_filters( 'kadence_mobile_media_query', '(max-width: 767px)' );
		$media_query['tablet']  = apply_filters( 'kadence_tablet_media_query', '(max-width: 1024px)' );
		$media_query['desktop'] = apply_filters( 'kadence_desktop_media_query', '(min-width: 1025px)' );
		// Container.
		$css->set_selector( '.wp-block-kadence-dynamichtml.kb-dynamic-html-id-' . $unique_id . '.kb-dynamic-html:not(.added-for-specificity)' );
		if ( isset( $attributes['padding'] ) && isset( $attributes['padding'][0] ) ) {
			if ( is_numeric( $attributes['padding'][0] ) ) {
				$css->add_property( 'padding-top', $attributes['padding'][0] . ( isset( $attributes['paddingType'] ) && ! empty( $attributes['paddingType'] ) ? $attributes['paddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['padding'][1] ) ) {
				$css->add_property( 'padding-right', $attributes['padding'][1] . ( isset( $attributes['paddingType'] ) && ! empty( $attributes['paddingType'] ) ? $attributes['paddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['padding'][2] ) ) {
				$css->add_property( 'padding-bottom', $attributes['padding'][2] . ( isset( $attributes['paddingType'] ) && ! empty( $attributes['paddingType'] ) ? $attributes['paddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['padding'][3] ) ) {
				$css->add_property( 'padding-left', $attributes['padding'][3] . ( isset( $attributes['paddingType'] ) && ! empty( $attributes['paddingType'] ) ? $attributes['paddingType'] : 'px' ) );
			}
		}
		if ( isset( $attributes['margin'] ) && isset( $attributes['margin'][0] ) ) {
			if ( is_numeric( $attributes['margin'][0] ) ) {
				$css->add_property( 'margin-top', $attributes['margin'][0] . ( isset( $attributes['marginType'] ) && ! empty( $attributes['marginType'] ) ? $attributes['marginType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['margin'][1] ) ) {
				$css->add_property( 'margin-right', $attributes['margin'][1] . ( isset( $attributes['marginType'] ) && ! empty( $attributes['marginType'] ) ? $attributes['marginType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['margin'][2] ) ) {
				$css->add_property( 'margin-bottom', $attributes['margin'][2] . ( isset( $attributes['marginType'] ) && ! empty( $attributes['marginType'] ) ? $attributes['marginType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['margin'][3] ) ) {
				$css->add_property( 'margin-left', $attributes['margin'][3] . ( isset( $attributes['marginType'] ) && ! empty( $attributes['marginType'] ) ? $attributes['marginType'] : 'px' ) );
			}
		}
		// Text Typography.
		$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ', .kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' p' );
		if ( isset( $attributes['textColor'] ) && ! empty( $attributes['textColor'] ) ) {
			$css->add_property( 'color', $css->render_color( $attributes['textColor'] ) );
		}
		if ( isset( $attributes['textTypography'] ) && is_array( $attributes['textTypography'] ) && isset( $attributes['textTypography'][0] ) && is_array( $attributes['textTypography'][0] ) ) {
			$text_font = $attributes['textTypography'][0];
			if ( isset( $text_font['size'] ) && isset( $text_font['size'][0] ) && is_numeric( $text_font['size'][0] ) ) {
				$css->add_property( 'font-size', $text_font['size'][0] . ( isset( $text_font['sizeType'] ) && ! empty( $text_font['sizeType'] ) ? $text_font['sizeType'] : 'px' ) );
			}
			if ( isset( $text_font['lineHeight'] ) && isset( $text_font['lineHeight'][0] ) && is_numeric( $text_font['lineHeight'][0] ) ) {
				$css->add_property( 'line-height', $text_font['lineHeight'][0] . ( isset( $text_font['lineType'] ) && ! empty( $text_font['lineType'] ) ? $text_font['lineType'] : 'px' ) );
			}
			if ( isset( $text_font['letterSpacing'] ) && isset( $text_font['letterSpacing'][0] ) && is_numeric( $text_font['letterSpacing'][0] ) ) {
				$css->add_property( 'letter-spacing', $text_font['letterSpacing'][0] . ( isset( $text_font['letterSpacingType'] ) && ! empty( $text_font['letterSpacingType'] ) ? $text_font['letterSpacingType'] : 'px' ) );
			}
			if ( isset( $text_font['family'] ) && ! empty( $text_font['family'] ) ) {
				$google = isset( $text_font['google'] ) && $text_font['google'] ? true : false;
				$google = $google && ( isset( $text_font['loadGoogle'] ) && $text_font['loadGoogle'] || ! isset( $text_font['loadGoogle'] ) ) ? true : false;
				$css->add_property( 'font-family', $css->render_font_family( $text_font['family'], $google, ( isset( $text_font['variation'] ) ? $text_font['variation'] : '' ), ( isset( $text_font['subset'] ) ? $text_font['subset'] : '' ) ) );
			}
			if ( isset( $text_font['weight'] ) && ! empty( $text_font['weight'] ) ) {
				$css->add_property( 'font-weight', $css->render_string( $text_font['weight'] ) );
			}
			if ( isset( $text_font['style'] ) && ! empty( $text_font['style'] ) ) {
				$css->add_property( 'font-style', $css->render_string( $text_font['style'] ) );
			}
			if ( isset( $text_font['textTransform'] ) && ! empty( $text_font['textTransform'] ) ) {
				$css->add_property( 'text-transform', $css->render_string( $text_font['textTransform'] ) );
			}
		}
		if ( isset( $attributes['headingColor'] ) && ! empty( $attributes['headingColor'] ) ) {
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h1, .kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h2, .kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h3, .kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h4, .kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h5, .kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h6' );
			$css->add_property( 'color', $css->render_color( $attributes['headingColor'] ) );
		}
		if ( isset( $attributes['linkColor'] ) && ! empty( $attributes['linkColor'] ) ) {
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' a' );
			$css->add_property( 'color', $css->render_color( $attributes['linkColor'] ) );
		}
		if ( isset( $attributes['linkHoverColor'] ) && ! empty( $attributes['linkHoverColor'] ) ) {
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' a:hover' );
			$css->add_property( 'color', $css->render_color( $attributes['linkColor'] ) );
		}
		// H1 Font.
		if ( isset( $attributes['enableH1'] ) && true === $attributes['enableH1'] && isset( $attributes['h1Typography'] ) && is_array( $attributes['h1Typography'] ) && isset( $attributes['h1Typography'][0] ) && is_array( $attributes['h1Typography'][0] ) ) {
			$h1_font = $attributes['h1Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h1' );
			if ( isset( $h1_font['size'] ) && isset( $h1_font['size'][0] ) && is_numeric( $h1_font['size'][0] ) ) {
				$css->add_property( 'font-size', $h1_font['size'][0] . ( isset( $h1_font['sizeType'] ) && ! empty( $h1_font['sizeType'] ) ? $h1_font['sizeType'] : 'px' ) );
			}
			if ( isset( $h1_font['lineHeight'] ) && isset( $h1_font['lineHeight'][0] ) && is_numeric( $h1_font['lineHeight'][0] ) ) {
				$css->add_property( 'line-height', $h1_font['lineHeight'][0] . ( isset( $h1_font['lineType'] ) && ! empty( $h1_font['lineType'] ) ? $h1_font['lineType'] : 'px' ) );
			}
			if ( isset( $h1_font['letterSpacing'] ) && isset( $h1_font['letterSpacing'][0] ) && is_numeric( $h1_font['letterSpacing'][0] ) ) {
				$css->add_property( 'letter-spacing', $h1_font['letterSpacing'][0] . ( isset( $h1_font['letterSpacingType'] ) && ! empty( $h1_font['letterSpacingType'] ) ? $h1_font['letterSpacingType'] : 'px' ) );
			}
			if ( isset( $h1_font['family'] ) && ! empty( $h1_font['family'] ) ) {
				$google = isset( $h1_font['google'] ) && $h1_font['google'] ? true : false;
				$google = $google && ( isset( $h1_font['loadGoogle'] ) && $h1_font['loadGoogle'] || ! isset( $h1_font['loadGoogle'] ) ) ? true : false;
				$css->add_property( 'font-family', $css->render_font_family( $h1_font['family'], $google, ( isset( $h1_font['variation'] ) ? $h1_font['variation'] : '' ), ( isset( $h1_font['subset'] ) ? $h1_font['subset'] : '' ) ) );
			}
			if ( isset( $h1_font['weight'] ) && ! empty( $h1_font['weight'] ) ) {
				$css->add_property( 'font-weight', $css->render_string( $h1_font['weight'] ) );
			}
			if ( isset( $h1_font['style'] ) && ! empty( $h1_font['style'] ) ) {
				$css->add_property( 'font-style', $css->render_string( $h1_font['style'] ) );
			}
			if ( isset( $h1_font['textTransform'] ) && ! empty( $h1_font['textTransform'] ) ) {
				$css->add_property( 'text-transform', $css->render_string( $h1_font['textTransform'] ) );
			}
		}
		// H2 Font.
		if ( isset( $attributes['enableH2'] ) && true === $attributes['enableH2'] && isset( $attributes['h2Typography'] ) && is_array( $attributes['h2Typography'] ) && isset( $attributes['h2Typography'][0] ) && is_array( $attributes['h2Typography'][0] ) ) {
			$h2_font = $attributes['h2Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h2' );
			if ( isset( $h2_font['size'] ) && isset( $h2_font['size'][0] ) && is_numeric( $h2_font['size'][0] ) ) {
				$css->add_property( 'font-size', $h2_font['size'][0] . ( isset( $h2_font['sizeType'] ) && ! empty( $h2_font['sizeType'] ) ? $h2_font['sizeType'] : 'px' ) );
			}
			if ( isset( $h2_font['lineHeight'] ) && isset( $h2_font['lineHeight'][0] ) && is_numeric( $h2_font['lineHeight'][0] ) ) {
				$css->add_property( 'line-height', $h2_font['lineHeight'][0] . ( isset( $h2_font['lineType'] ) && ! empty( $h2_font['lineType'] ) ? $h2_font['lineType'] : 'px' ) );
			}
			if ( isset( $h2_font['letterSpacing'] ) && isset( $h2_font['letterSpacing'][0] ) && is_numeric( $h2_font['letterSpacing'][0] ) ) {
				$css->add_property( 'letter-spacing', $h2_font['letterSpacing'][0] . ( isset( $h2_font['letterSpacingType'] ) && ! empty( $h2_font['letterSpacingType'] ) ? $h2_font['letterSpacingType'] : 'px' ) );
			}
			if ( isset( $h2_font['family'] ) && ! empty( $h2_font['family'] ) ) {
				$google = isset( $h2_font['google'] ) && $h2_font['google'] ? true : false;
				$google = $google && ( isset( $h2_font['loadGoogle'] ) && $h2_font['loadGoogle'] || ! isset( $h2_font['loadGoogle'] ) ) ? true : false;
				$css->add_property( 'font-family', $css->render_font_family( $h2_font['family'], $google, ( isset( $h2_font['variation'] ) ? $h2_font['variation'] : '' ), ( isset( $h2_font['subset'] ) ? $h2_font['subset'] : '' ) ) );
			}
			if ( isset( $h2_font['weight'] ) && ! empty( $h2_font['weight'] ) ) {
				$css->add_property( 'font-weight', $css->render_string( $h2_font['weight'] ) );
			}
			if ( isset( $h2_font['style'] ) && ! empty( $h2_font['style'] ) ) {
				$css->add_property( 'font-style', $css->render_string( $h2_font['style'] ) );
			}
			if ( isset( $h2_font['textTransform'] ) && ! empty( $h2_font['textTransform'] ) ) {
				$css->add_property( 'text-transform', $css->render_string( $h2_font['textTransform'] ) );
			}
		}
		if ( isset( $attributes['enableH3'] ) && true === $attributes['enableH3'] && isset( $attributes['h3Typography'] ) && is_array( $attributes['h3Typography'] ) && isset( $attributes['h3Typography'][0] ) && is_array( $attributes['h3Typography'][0] ) ) {
			$h3_font = $attributes['h3Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h3' );
			if ( isset( $h3_font['size'] ) && isset( $h3_font['size'][0] ) && is_numeric( $h3_font['size'][0] ) ) {
				$css->add_property( 'font-size', $h3_font['size'][0] . ( isset( $h3_font['sizeType'] ) && ! empty( $h3_font['sizeType'] ) ? $h3_font['sizeType'] : 'px' ) );
			}
			if ( isset( $h3_font['lineHeight'] ) && isset( $h3_font['lineHeight'][0] ) && is_numeric( $h3_font['lineHeight'][0] ) ) {
				$css->add_property( 'line-height', $h3_font['lineHeight'][0] . ( isset( $h3_font['lineType'] ) && ! empty( $h3_font['lineType'] ) ? $h3_font['lineType'] : 'px' ) );
			}
			if ( isset( $h3_font['letterSpacing'] ) && isset( $h3_font['letterSpacing'][0] ) && is_numeric( $h3_font['letterSpacing'][0] ) ) {
				$css->add_property( 'letter-spacing', $h3_font['letterSpacing'][0] . ( isset( $h3_font['letterSpacingType'] ) && ! empty( $h3_font['letterSpacingType'] ) ? $h3_font['letterSpacingType'] : 'px' ) );
			}
			if ( isset( $h3_font['family'] ) && ! empty( $h3_font['family'] ) ) {
				$google = isset( $h3_font['google'] ) && $h3_font['google'] ? true : false;
				$google = $google && ( isset( $h3_font['loadGoogle'] ) && $h3_font['loadGoogle'] || ! isset( $h3_font['loadGoogle'] ) ) ? true : false;
				$css->add_property( 'font-family', $css->render_font_family( $h3_font['family'], $google, ( isset( $h3_font['variation'] ) ? $h3_font['variation'] : '' ), ( isset( $h3_font['subset'] ) ? $h3_font['subset'] : '' ) ) );
			}
			if ( isset( $h3_font['weight'] ) && ! empty( $h3_font['weight'] ) ) {
				$css->add_property( 'font-weight', $css->render_string( $h3_font['weight'] ) );
			}
			if ( isset( $h3_font['style'] ) && ! empty( $h3_font['style'] ) ) {
				$css->add_property( 'font-style', $css->render_string( $h3_font['style'] ) );
			}
			if ( isset( $h3_font['textTransform'] ) && ! empty( $h3_font['textTransform'] ) ) {
				$css->add_property( 'text-transform', $css->render_string( $h3_font['textTransform'] ) );
			}
		}
		if ( isset( $attributes['enableH4'] ) && true === $attributes['enableH4'] && isset( $attributes['h4Typography'] ) && is_array( $attributes['h4Typography'] ) && isset( $attributes['h4Typography'][0] ) && is_array( $attributes['h4Typography'][0] ) ) {
			$h4_font = $attributes['h4Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h4' );
			if ( isset( $h4_font['size'] ) && isset( $h4_font['size'][0] ) && is_numeric( $h4_font['size'][0] ) ) {
				$css->add_property( 'font-size', $h4_font['size'][0] . ( isset( $h4_font['sizeType'] ) && ! empty( $h4_font['sizeType'] ) ? $h4_font['sizeType'] : 'px' ) );
			}
			if ( isset( $h4_font['lineHeight'] ) && isset( $h4_font['lineHeight'][0] ) && is_numeric( $h4_font['lineHeight'][0] ) ) {
				$css->add_property( 'line-height', $h4_font['lineHeight'][0] . ( isset( $h4_font['lineType'] ) && ! empty( $h4_font['lineType'] ) ? $h4_font['lineType'] : 'px' ) );
			}
			if ( isset( $h4_font['letterSpacing'] ) && isset( $h4_font['letterSpacing'][0] ) && is_numeric( $h4_font['letterSpacing'][0] ) ) {
				$css->add_property( 'letter-spacing', $h4_font['letterSpacing'][0] . ( isset( $h4_font['letterSpacingType'] ) && ! empty( $h4_font['letterSpacingType'] ) ? $h4_font['letterSpacingType'] : 'px' ) );
			}
			if ( isset( $h4_font['family'] ) && ! empty( $h4_font['family'] ) ) {
				$google = isset( $h4_font['google'] ) && $h4_font['google'] ? true : false;
				$google = $google && ( isset( $h4_font['loadGoogle'] ) && $h4_font['loadGoogle'] || ! isset( $h4_font['loadGoogle'] ) ) ? true : false;
				$css->add_property( 'font-family', $css->render_font_family( $h4_font['family'], $google, ( isset( $h4_font['variation'] ) ? $h4_font['variation'] : '' ), ( isset( $h4_font['subset'] ) ? $h4_font['subset'] : '' ) ) );
			}
			if ( isset( $h4_font['weight'] ) && ! empty( $h4_font['weight'] ) ) {
				$css->add_property( 'font-weight', $css->render_string( $h4_font['weight'] ) );
			}
			if ( isset( $h4_font['style'] ) && ! empty( $h4_font['style'] ) ) {
				$css->add_property( 'font-style', $css->render_string( $h4_font['style'] ) );
			}
			if ( isset( $h4_font['textTransform'] ) && ! empty( $h4_font['textTransform'] ) ) {
				$css->add_property( 'text-transform', $css->render_string( $h4_font['textTransform'] ) );
			}
		}
		if ( isset( $attributes['enableH5'] ) && true === $attributes['enableH5'] && isset( $attributes['h5Typography'] ) && is_array( $attributes['h5Typography'] ) && isset( $attributes['h5Typography'][0] ) && is_array( $attributes['h5Typography'][0] ) ) {
			$h5_font = $attributes['h5Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h5' );
			if ( isset( $h5_font['size'] ) && isset( $h5_font['size'][0] ) && is_numeric( $h5_font['size'][0] ) ) {
				$css->add_property( 'font-size', $h5_font['size'][0] . ( isset( $h5_font['sizeType'] ) && ! empty( $h5_font['sizeType'] ) ? $h5_font['sizeType'] : 'px' ) );
			}
			if ( isset( $h5_font['lineHeight'] ) && isset( $h5_font['lineHeight'][0] ) && is_numeric( $h5_font['lineHeight'][0] ) ) {
				$css->add_property( 'line-height', $h5_font['lineHeight'][0] . ( isset( $h5_font['lineType'] ) && ! empty( $h5_font['lineType'] ) ? $h5_font['lineType'] : 'px' ) );
			}
			if ( isset( $h5_font['letterSpacing'] ) && isset( $h5_font['letterSpacing'][0] ) && is_numeric( $h5_font['letterSpacing'][0] ) ) {
				$css->add_property( 'letter-spacing', $h5_font['letterSpacing'][0] . ( isset( $h5_font['letterSpacingType'] ) && ! empty( $h5_font['letterSpacingType'] ) ? $h5_font['letterSpacingType'] : 'px' ) );
			}
			if ( isset( $h5_font['family'] ) && ! empty( $h5_font['family'] ) ) {
				$google = isset( $h5_font['google'] ) && $h5_font['google'] ? true : false;
				$google = $google && ( isset( $h5_font['loadGoogle'] ) && $h5_font['loadGoogle'] || ! isset( $h5_font['loadGoogle'] ) ) ? true : false;
				$css->add_property( 'font-family', $css->render_font_family( $h5_font['family'], $google, ( isset( $h5_font['variation'] ) ? $h5_font['variation'] : '' ), ( isset( $h5_font['subset'] ) ? $h5_font['subset'] : '' ) ) );
			}
			if ( isset( $h5_font['weight'] ) && ! empty( $h5_font['weight'] ) ) {
				$css->add_property( 'font-weight', $css->render_string( $h5_font['weight'] ) );
			}
			if ( isset( $h5_font['style'] ) && ! empty( $h5_font['style'] ) ) {
				$css->add_property( 'font-style', $css->render_string( $h5_font['style'] ) );
			}
			if ( isset( $h5_font['textTransform'] ) && ! empty( $h5_font['textTransform'] ) ) {
				$css->add_property( 'text-transform', $css->render_string( $h5_font['textTransform'] ) );
			}
		}
		if ( isset( $attributes['enableH6'] ) && true === $attributes['enableH6'] && isset( $attributes['h6Typography'] ) && is_array( $attributes['h6Typography'] ) && isset( $attributes['h6Typography'][0] ) && is_array( $attributes['h6Typography'][0] ) ) {
			$h6_font = $attributes['h6Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h6' );
			if ( isset( $h6_font['size'] ) && isset( $h6_font['size'][0] ) && is_numeric( $h6_font['size'][0] ) ) {
				$css->add_property( 'font-size', $h6_font['size'][0] . ( isset( $h6_font['sizeType'] ) && ! empty( $h6_font['sizeType'] ) ? $h6_font['sizeType'] : 'px' ) );
			}
			if ( isset( $h6_font['lineHeight'] ) && isset( $h6_font['lineHeight'][0] ) && is_numeric( $h6_font['lineHeight'][0] ) ) {
				$css->add_property( 'line-height', $h6_font['lineHeight'][0] . ( isset( $h6_font['lineType'] ) && ! empty( $h6_font['lineType'] ) ? $h6_font['lineType'] : 'px' ) );
			}
			if ( isset( $h6_font['letterSpacing'] ) && isset( $h6_font['letterSpacing'][0] ) && is_numeric( $h6_font['letterSpacing'][0] ) ) {
				$css->add_property( 'letter-spacing', $h6_font['letterSpacing'][0] . ( isset( $h6_font['letterSpacingType'] ) && ! empty( $h6_font['letterSpacingType'] ) ? $h6_font['letterSpacingType'] : 'px' ) );
			}
			if ( isset( $h6_font['family'] ) && ! empty( $h6_font['family'] ) ) {
				$google = isset( $h6_font['google'] ) && $h6_font['google'] ? true : false;
				$google = $google && ( isset( $h6_font['loadGoogle'] ) && $h6_font['loadGoogle'] || ! isset( $h6_font['loadGoogle'] ) ) ? true : false;
				$css->add_property( 'font-family', $css->render_font_family( $h6_font['family'], $google, ( isset( $h6_font['variation'] ) ? $h6_font['variation'] : '' ), ( isset( $h6_font['subset'] ) ? $h6_font['subset'] : '' ) ) );
			}
			if ( isset( $h6_font['weight'] ) && ! empty( $h6_font['weight'] ) ) {
				$css->add_property( 'font-weight', $css->render_string( $h6_font['weight'] ) );
			}
			if ( isset( $h6_font['style'] ) && ! empty( $h6_font['style'] ) ) {
				$css->add_property( 'font-style', $css->render_string( $h6_font['style'] ) );
			}
			if ( isset( $h6_font['textTransform'] ) && ! empty( $h6_font['textTransform'] ) ) {
				$css->add_property( 'text-transform', $css->render_string( $h6_font['textTransform'] ) );
			}
		}
		// Tablet.
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '.wp-block-kadence-dynamichtml.kb-dynamic-html-id-' . $unique_id . '.kb-dynamic-html:not(.added-for-specificity)' );
		if ( isset( $attributes['tabletPadding'] ) && isset( $attributes['tabletPadding'][0] ) ) {
			if ( is_numeric( $attributes['tabletPadding'][0] ) ) {
				$css->add_property( 'padding-top', $attributes['tabletPadding'][0] . ( isset( $attributes['paddingType'] ) && ! empty( $attributes['paddingType'] ) ? $attributes['paddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['tabletPadding'][1] ) ) {
				$css->add_property( 'padding-right', $attributes['tabletPadding'][1] . ( isset( $attributes['paddingType'] ) && ! empty( $attributes['paddingType'] ) ? $attributes['paddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['tabletPadding'][2] ) ) {
				$css->add_property( 'padding-bottom', $attributes['tabletPadding'][2] . ( isset( $attributes['paddingType'] ) && ! empty( $attributes['paddingType'] ) ? $attributes['paddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['tabletPadding'][3] ) ) {
				$css->add_property( 'padding-left', $attributes['tabletPadding'][3] . ( isset( $attributes['paddingType'] ) && ! empty( $attributes['paddingType'] ) ? $attributes['paddingType'] : 'px' ) );
			}
		}
		if ( isset( $attributes['tabletMargin'] ) && isset( $attributes['tabletMargin'][0] ) ) {
			if ( is_numeric( $attributes['tabletMargin'][0] ) ) {
				$css->add_property( 'margin-top', $attributes['tabletMargin'][0] . ( isset( $attributes['marginType'] ) && ! empty( $attributes['marginType'] ) ? $attributes['marginType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['tabletMargin'][1] ) ) {
				$css->add_property( 'margin-right', $attributes['tabletMargin'][1] . ( isset( $attributes['marginType'] ) && ! empty( $attributes['marginType'] ) ? $attributes['marginType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['tabletMargin'][2] ) ) {
				$css->add_property( 'margin-bottom', $attributes['tabletMargin'][2] . ( isset( $attributes['marginType'] ) && ! empty( $attributes['marginType'] ) ? $attributes['marginType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['tabletMargin'][3] ) ) {
				$css->add_property( 'margin-left', $attributes['tabletMargin'][3] . ( isset( $attributes['marginType'] ) && ! empty( $attributes['marginType'] ) ? $attributes['marginType'] : 'px' ) );
			}
		}
		// Text Typography.
		$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ', .kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' p' );
		if ( isset( $attributes['textTypography'] ) && is_array( $attributes['textTypography'] ) && isset( $attributes['textTypography'][0] ) && is_array( $attributes['textTypography'][0] ) ) {
			$text_font = $attributes['textTypography'][0];
			if ( isset( $text_font['size'] ) && isset( $text_font['size'][1] ) && is_numeric( $text_font['size'][1] ) ) {
				$css->add_property( 'font-size', $text_font['size'][1] . ( isset( $text_font['sizeType'] ) && ! empty( $text_font['sizeType'] ) ? $text_font['sizeType'] : 'px' ) );
			}
			if ( isset( $text_font['lineHeight'] ) && isset( $text_font['lineHeight'][1] ) && is_numeric( $text_font['lineHeight'][1] ) ) {
				$css->add_property( 'line-height', $text_font['lineHeight'][1] . ( isset( $text_font['lineType'] ) && ! empty( $text_font['lineType'] ) ? $text_font['lineType'] : 'px' ) );
			}
			if ( isset( $text_font['letterSpacing'] ) && isset( $text_font['letterSpacing'][1] ) && is_numeric( $text_font['letterSpacing'][1] ) ) {
				$css->add_property( 'letter-spacing', $text_font['letterSpacing'][1] . ( isset( $text_font['letterSpacingType'] ) && ! empty( $text_font['letterSpacingType'] ) ? $text_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h1 Typography.
		if ( isset( $attributes['enableH1'] ) && true === $attributes['enableH1'] && isset( $attributes['h1Typography'] ) && is_array( $attributes['h1Typography'] ) && isset( $attributes['h1Typography'][0] ) && is_array( $attributes['h1Typography'][0] ) ) {
			$h1_font = $attributes['h1Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h1' );
			if ( isset( $h1_font['size'] ) && isset( $h1_font['size'][1] ) && is_numeric( $h1_font['size'][1] ) ) {
				$css->add_property( 'font-size', $h1_font['size'][1] . ( isset( $h1_font['sizeType'] ) && ! empty( $h1_font['sizeType'] ) ? $h1_font['sizeType'] : 'px' ) );
			}
			if ( isset( $h1_font['lineHeight'] ) && isset( $h1_font['lineHeight'][1] ) && is_numeric( $h1_font['lineHeight'][1] ) ) {
				$css->add_property( 'line-height', $h1_font['lineHeight'][1] . ( isset( $h1_font['lineType'] ) && ! empty( $h1_font['lineType'] ) ? $h1_font['lineType'] : 'px' ) );
			}
			if ( isset( $h1_font['letterSpacing'] ) && isset( $h1_font['letterSpacing'][1] ) && is_numeric( $h1_font['letterSpacing'][1] ) ) {
				$css->add_property( 'letter-spacing', $h1_font['letterSpacing'][1] . ( isset( $h1_font['letterSpacingType'] ) && ! empty( $h1_font['letterSpacingType'] ) ? $h1_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h2 Typography.
		if ( isset( $attributes['enableH2'] ) && true === $attributes['enableH2'] && isset( $attributes['h2Typography'] ) && is_array( $attributes['h2Typography'] ) && isset( $attributes['h2Typography'][0] ) && is_array( $attributes['h2Typography'][0] ) ) {
			$h2_font = $attributes['h2Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h2' );
			if ( isset( $h2_font['size'] ) && isset( $h2_font['size'][1] ) && is_numeric( $h2_font['size'][1] ) ) {
				$css->add_property( 'font-size', $h2_font['size'][1] . ( isset( $h2_font['sizeType'] ) && ! empty( $h2_font['sizeType'] ) ? $h2_font['sizeType'] : 'px' ) );
			}
			if ( isset( $h2_font['lineHeight'] ) && isset( $h2_font['lineHeight'][1] ) && is_numeric( $h2_font['lineHeight'][1] ) ) {
				$css->add_property( 'line-height', $h2_font['lineHeight'][1] . ( isset( $h2_font['lineType'] ) && ! empty( $h2_font['lineType'] ) ? $h2_font['lineType'] : 'px' ) );
			}
			if ( isset( $h2_font['letterSpacing'] ) && isset( $h2_font['letterSpacing'][1] ) && is_numeric( $h2_font['letterSpacing'][1] ) ) {
				$css->add_property( 'letter-spacing', $h2_font['letterSpacing'][1] . ( isset( $h2_font['letterSpacingType'] ) && ! empty( $h2_font['letterSpacingType'] ) ? $h2_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h3 Typography.
		if ( isset( $attributes['enableH3'] ) && true === $attributes['enableH3'] && isset( $attributes['h3Typography'] ) && is_array( $attributes['h3Typography'] ) && isset( $attributes['h3Typography'][0] ) && is_array( $attributes['h3Typography'][0] ) ) {
			$h3_font = $attributes['h3Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h3' );
			if ( isset( $h3_font['size'] ) && isset( $h3_font['size'][1] ) && is_numeric( $h3_font['size'][1] ) ) {
				$css->add_property( 'font-size', $h3_font['size'][1] . ( isset( $h3_font['sizeType'] ) && ! empty( $h3_font['sizeType'] ) ? $h3_font['sizeType'] : 'px' ) );
			}
			if ( isset( $h3_font['lineHeight'] ) && isset( $h3_font['lineHeight'][1] ) && is_numeric( $h3_font['lineHeight'][1] ) ) {
				$css->add_property( 'line-height', $h3_font['lineHeight'][1] . ( isset( $h3_font['lineType'] ) && ! empty( $h3_font['lineType'] ) ? $h3_font['lineType'] : 'px' ) );
			}
			if ( isset( $h3_font['letterSpacing'] ) && isset( $h3_font['letterSpacing'][1] ) && is_numeric( $h3_font['letterSpacing'][1] ) ) {
				$css->add_property( 'letter-spacing', $h3_font['letterSpacing'][1] . ( isset( $h3_font['letterSpacingType'] ) && ! empty( $h3_font['letterSpacingType'] ) ? $h3_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h4 Typography.
		if ( isset( $attributes['enableH4'] ) && true === $attributes['enableH4'] && isset( $attributes['h4Typography'] ) && is_array( $attributes['h4Typography'] ) && isset( $attributes['h4Typography'][0] ) && is_array( $attributes['h4Typography'][0] ) ) {
			$h4_font = $attributes['h4Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h4' );
			if ( isset( $h4_font['size'] ) && isset( $h4_font['size'][1] ) && is_numeric( $h4_font['size'][1] ) ) {
				$css->add_property( 'font-size', $h4_font['size'][1] . ( isset( $h4_font['sizeType'] ) && ! empty( $h4_font['sizeType'] ) ? $h4_font['sizeType'] : 'px' ) );
			}
			if ( isset( $h4_font['lineHeight'] ) && isset( $h4_font['lineHeight'][1] ) && is_numeric( $h4_font['lineHeight'][1] ) ) {
				$css->add_property( 'line-height', $h4_font['lineHeight'][1] . ( isset( $h4_font['lineType'] ) && ! empty( $h4_font['lineType'] ) ? $h4_font['lineType'] : 'px' ) );
			}
			if ( isset( $h4_font['letterSpacing'] ) && isset( $h4_font['letterSpacing'][1] ) && is_numeric( $h4_font['letterSpacing'][1] ) ) {
				$css->add_property( 'letter-spacing', $h4_font['letterSpacing'][1] . ( isset( $h4_font['letterSpacingType'] ) && ! empty( $h4_font['letterSpacingType'] ) ? $h4_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h5 Typography.
		if ( isset( $attributes['enableH5'] ) && true === $attributes['enableH5'] && isset( $attributes['h5Typography'] ) && is_array( $attributes['h5Typography'] ) && isset( $attributes['h5Typography'][0] ) && is_array( $attributes['h5Typography'][0] ) ) {
			$h5_font = $attributes['h5Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h5' );
			if ( isset( $h5_font['size'] ) && isset( $h5_font['size'][1] ) && is_numeric( $h5_font['size'][1] ) ) {
				$css->add_property( 'font-size', $h5_font['size'][1] . ( isset( $h5_font['sizeType'] ) && ! empty( $h5_font['sizeType'] ) ? $h5_font['sizeType'] : 'px' ) );
			}
			if ( isset( $h5_font['lineHeight'] ) && isset( $h5_font['lineHeight'][1] ) && is_numeric( $h5_font['lineHeight'][1] ) ) {
				$css->add_property( 'line-height', $h5_font['lineHeight'][1] . ( isset( $h5_font['lineType'] ) && ! empty( $h5_font['lineType'] ) ? $h5_font['lineType'] : 'px' ) );
			}
			if ( isset( $h5_font['letterSpacing'] ) && isset( $h5_font['letterSpacing'][1] ) && is_numeric( $h5_font['letterSpacing'][1] ) ) {
				$css->add_property( 'letter-spacing', $h5_font['letterSpacing'][1] . ( isset( $h5_font['letterSpacingType'] ) && ! empty( $h5_font['letterSpacingType'] ) ? $h5_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h6 Typography.
		if ( isset( $attributes['enableH6'] ) && true === $attributes['enableH6'] && isset( $attributes['h6Typography'] ) && is_array( $attributes['h6Typography'] ) && isset( $attributes['h6Typography'][0] ) && is_array( $attributes['h6Typography'][0] ) ) {
			$h6_font = $attributes['h6Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h6' );
			if ( isset( $h6_font['size'] ) && isset( $h6_font['size'][1] ) && is_numeric( $h6_font['size'][1] ) ) {
				$css->add_property( 'font-size', $h6_font['size'][1] . ( isset( $h6_font['sizeType'] ) && ! empty( $h6_font['sizeType'] ) ? $h6_font['sizeType'] : 'px' ) );
			}
			if ( isset( $h6_font['lineHeight'] ) && isset( $h6_font['lineHeight'][1] ) && is_numeric( $h6_font['lineHeight'][1] ) ) {
				$css->add_property( 'line-height', $h6_font['lineHeight'][1] . ( isset( $h6_font['lineType'] ) && ! empty( $h6_font['lineType'] ) ? $h6_font['lineType'] : 'px' ) );
			}
			if ( isset( $h6_font['letterSpacing'] ) && isset( $h6_font['letterSpacing'][1] ) && is_numeric( $h6_font['letterSpacing'][1] ) ) {
				$css->add_property( 'letter-spacing', $h6_font['letterSpacing'][1] . ( isset( $h6_font['letterSpacingType'] ) && ! empty( $h6_font['letterSpacingType'] ) ? $h6_font['letterSpacingType'] : 'px' ) );
			}
		}
		$css->stop_media_query();
		// Mobile.
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '.wp-block-kadence-dynamichtml.kb-dynamic-html-id-' . $unique_id . '.kb-dynamic-html:not(.added-for-specificity)' );
		if ( isset( $attributes['mobilePadding'] ) && isset( $attributes['mobilePadding'][0] ) ) {
			if ( is_numeric( $attributes['mobilePadding'][0] ) ) {
				$css->add_property( 'padding-top', $attributes['mobilePadding'][0] . ( isset( $attributes['paddingType'] ) && ! empty( $attributes['paddingType'] ) ? $attributes['paddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['mobilePadding'][1] ) ) {
				$css->add_property( 'padding-right', $attributes['mobilePadding'][1] . ( isset( $attributes['paddingType'] ) && ! empty( $attributes['paddingType'] ) ? $attributes['paddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['mobilePadding'][2] ) ) {
				$css->add_property( 'padding-bottom', $attributes['mobilePadding'][2] . ( isset( $attributes['paddingType'] ) && ! empty( $attributes['paddingType'] ) ? $attributes['paddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['mobilePadding'][3] ) ) {
				$css->add_property( 'padding-left', $attributes['mobilePadding'][3] . ( isset( $attributes['paddingType'] ) && ! empty( $attributes['paddingType'] ) ? $attributes['paddingType'] : 'px' ) );
			}
		}
		if ( isset( $attributes['mobileMargin'] ) && isset( $attributes['mobileMargin'][0] ) ) {
			if ( is_numeric( $attributes['mobileMargin'][0] ) ) {
				$css->add_property( 'margin-top', $attributes['mobileMargin'][0] . ( isset( $attributes['marginType'] ) && ! empty( $attributes['marginType'] ) ? $attributes['marginType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['mobileMargin'][1] ) ) {
				$css->add_property( 'margin-right', $attributes['mobileMargin'][1] . ( isset( $attributes['marginType'] ) && ! empty( $attributes['marginType'] ) ? $attributes['marginType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['mobileMargin'][2] ) ) {
				$css->add_property( 'margin-bottom', $attributes['mobileMargin'][2] . ( isset( $attributes['marginType'] ) && ! empty( $attributes['marginType'] ) ? $attributes['marginType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['mobileMargin'][3] ) ) {
				$css->add_property( 'margin-left', $attributes['mobileMargin'][3] . ( isset( $attributes['marginType'] ) && ! empty( $attributes['marginType'] ) ? $attributes['marginType'] : 'px' ) );
			}
		}
		// Text Typography.
		$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ', .kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' p' );
		if ( isset( $attributes['textTypography'] ) && is_array( $attributes['textTypography'] ) && isset( $attributes['textTypography'][0] ) && is_array( $attributes['textTypography'][0] ) ) {
			$text_font = $attributes['textTypography'][0];
			if ( isset( $text_font['size'] ) && isset( $text_font['size'][2] ) && is_numeric( $text_font['size'][2] ) ) {
				$css->add_property( 'font-size', $text_font['size'][2] . ( isset( $text_font['sizeType'] ) && ! empty( $text_font['sizeType'] ) ? $text_font['sizeType'] : 'px' ) );
			}
			if ( isset( $text_font['lineHeight'] ) && isset( $text_font['lineHeight'][2] ) && is_numeric( $text_font['lineHeight'][2] ) ) {
				$css->add_property( 'line-height', $text_font['lineHeight'][2] . ( isset( $text_font['lineType'] ) && ! empty( $text_font['lineType'] ) ? $text_font['lineType'] : 'px' ) );
			}
			if ( isset( $text_font['letterSpacing'] ) && isset( $text_font['letterSpacing'][2] ) && is_numeric( $text_font['letterSpacing'][2] ) ) {
				$css->add_property( 'letter-spacing', $text_font['letterSpacing'][2] . ( isset( $text_font['letterSpacingType'] ) && ! empty( $text_font['letterSpacingType'] ) ? $text_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h1 Typography.
		if ( isset( $attributes['enableH1'] ) && true === $attributes['enableH1'] && isset( $attributes['h1Typography'] ) && is_array( $attributes['h1Typography'] ) && isset( $attributes['h1Typography'][0] ) && is_array( $attributes['h1Typography'][0] ) ) {
			$h1_font = $attributes['h1Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h1' );
			if ( isset( $h1_font['size'] ) && isset( $h1_font['size'][2] ) && is_numeric( $h1_font['size'][2] ) ) {
				$css->add_property( 'font-size', $h1_font['size'][2] . ( isset( $h1_font['sizeType'] ) && ! empty( $h1_font['sizeType'] ) ? $h1_font['sizeType'] : 'px' ) );
			}
			if ( isset( $h1_font['lineHeight'] ) && isset( $h1_font['lineHeight'][2] ) && is_numeric( $h1_font['lineHeight'][2] ) ) {
				$css->add_property( 'line-height', $h1_font['lineHeight'][2] . ( isset( $h1_font['lineType'] ) && ! empty( $h1_font['lineType'] ) ? $h1_font['lineType'] : 'px' ) );
			}
			if ( isset( $h1_font['letterSpacing'] ) && isset( $h1_font['letterSpacing'][2] ) && is_numeric( $h1_font['letterSpacing'][2] ) ) {
				$css->add_property( 'letter-spacing', $h1_font['letterSpacing'][2] . ( isset( $h1_font['letterSpacingType'] ) && ! empty( $h1_font['letterSpacingType'] ) ? $h1_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h2 Typography.
		if ( isset( $attributes['enableH2'] ) && true === $attributes['enableH2'] && isset( $attributes['h2Typography'] ) && is_array( $attributes['h2Typography'] ) && isset( $attributes['h2Typography'][0] ) && is_array( $attributes['h2Typography'][0] ) ) {
			$h2_font = $attributes['h2Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h2' );
			if ( isset( $h2_font['size'] ) && isset( $h2_font['size'][2] ) && is_numeric( $h2_font['size'][2] ) ) {
				$css->add_property( 'font-size', $h2_font['size'][2] . ( isset( $h2_font['sizeType'] ) && ! empty( $h2_font['sizeType'] ) ? $h2_font['sizeType'] : 'px' ) );
			}
			if ( isset( $h2_font['lineHeight'] ) && isset( $h2_font['lineHeight'][2] ) && is_numeric( $h2_font['lineHeight'][2] ) ) {
				$css->add_property( 'line-height', $h2_font['lineHeight'][2] . ( isset( $h2_font['lineType'] ) && ! empty( $h2_font['lineType'] ) ? $h2_font['lineType'] : 'px' ) );
			}
			if ( isset( $h2_font['letterSpacing'] ) && isset( $h2_font['letterSpacing'][2] ) && is_numeric( $h2_font['letterSpacing'][2] ) ) {
				$css->add_property( 'letter-spacing', $h2_font['letterSpacing'][2] . ( isset( $h2_font['letterSpacingType'] ) && ! empty( $h2_font['letterSpacingType'] ) ? $h2_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h3 Typography.
		if ( isset( $attributes['enableH3'] ) && true === $attributes['enableH3'] && isset( $attributes['h3Typography'] ) && is_array( $attributes['h3Typography'] ) && isset( $attributes['h3Typography'][0] ) && is_array( $attributes['h3Typography'][0] ) ) {
			$h3_font = $attributes['h3Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h3' );
			if ( isset( $h3_font['size'] ) && isset( $h3_font['size'][2] ) && is_numeric( $h3_font['size'][2] ) ) {
				$css->add_property( 'font-size', $h3_font['size'][2] . ( isset( $h3_font['sizeType'] ) && ! empty( $h3_font['sizeType'] ) ? $h3_font['sizeType'] : 'px' ) );
			}
			if ( isset( $h3_font['lineHeight'] ) && isset( $h3_font['lineHeight'][2] ) && is_numeric( $h3_font['lineHeight'][2] ) ) {
				$css->add_property( 'line-height', $h3_font['lineHeight'][2] . ( isset( $h3_font['lineType'] ) && ! empty( $h3_font['lineType'] ) ? $h3_font['lineType'] : 'px' ) );
			}
			if ( isset( $h3_font['letterSpacing'] ) && isset( $h3_font['letterSpacing'][2] ) && is_numeric( $h3_font['letterSpacing'][2] ) ) {
				$css->add_property( 'letter-spacing', $h3_font['letterSpacing'][2] . ( isset( $h3_font['letterSpacingType'] ) && ! empty( $h3_font['letterSpacingType'] ) ? $h3_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h4 Typography.
		if ( isset( $attributes['enableH4'] ) && true === $attributes['enableH4'] && isset( $attributes['h4Typography'] ) && is_array( $attributes['h4Typography'] ) && isset( $attributes['h4Typography'][0] ) && is_array( $attributes['h4Typography'][0] ) ) {
			$h4_font = $attributes['h4Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h4' );
			if ( isset( $h4_font['size'] ) && isset( $h4_font['size'][2] ) && is_numeric( $h4_font['size'][2] ) ) {
				$css->add_property( 'font-size', $h4_font['size'][2] . ( isset( $h4_font['sizeType'] ) && ! empty( $h4_font['sizeType'] ) ? $h4_font['sizeType'] : 'px' ) );
			}
			if ( isset( $h4_font['lineHeight'] ) && isset( $h4_font['lineHeight'][2] ) && is_numeric( $h4_font['lineHeight'][2] ) ) {
				$css->add_property( 'line-height', $h4_font['lineHeight'][2] . ( isset( $h4_font['lineType'] ) && ! empty( $h4_font['lineType'] ) ? $h4_font['lineType'] : 'px' ) );
			}
			if ( isset( $h4_font['letterSpacing'] ) && isset( $h4_font['letterSpacing'][2] ) && is_numeric( $h4_font['letterSpacing'][2] ) ) {
				$css->add_property( 'letter-spacing', $h4_font['letterSpacing'][2] . ( isset( $h4_font['letterSpacingType'] ) && ! empty( $h4_font['letterSpacingType'] ) ? $h4_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h5 Typography.
		if ( isset( $attributes['enableH5'] ) && true === $attributes['enableH5'] && isset( $attributes['h5Typography'] ) && is_array( $attributes['h5Typography'] ) && isset( $attributes['h5Typography'][0] ) && is_array( $attributes['h5Typography'][0] ) ) {
			$h5_font = $attributes['h5Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h5' );
			if ( isset( $h5_font['size'] ) && isset( $h5_font['size'][2] ) && is_numeric( $h5_font['size'][2] ) ) {
				$css->add_property( 'font-size', $h5_font['size'][2] . ( isset( $h5_font['sizeType'] ) && ! empty( $h5_font['sizeType'] ) ? $h5_font['sizeType'] : 'px' ) );
			}
			if ( isset( $h5_font['lineHeight'] ) && isset( $h5_font['lineHeight'][2] ) && is_numeric( $h5_font['lineHeight'][2] ) ) {
				$css->add_property( 'line-height', $h5_font['lineHeight'][2] . ( isset( $h5_font['lineType'] ) && ! empty( $h5_font['lineType'] ) ? $h5_font['lineType'] : 'px' ) );
			}
			if ( isset( $h5_font['letterSpacing'] ) && isset( $h5_font['letterSpacing'][2] ) && is_numeric( $h5_font['letterSpacing'][2] ) ) {
				$css->add_property( 'letter-spacing', $h5_font['letterSpacing'][2] . ( isset( $h5_font['letterSpacingType'] ) && ! empty( $h5_font['letterSpacingType'] ) ? $h5_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h6 Typography.
		if ( isset( $attributes['enableH6'] ) && true === $attributes['enableH6'] && isset( $attributes['h6Typography'] ) && is_array( $attributes['h6Typography'] ) && isset( $attributes['h6Typography'][0] ) && is_array( $attributes['h6Typography'][0] ) ) {
			$h6_font = $attributes['h6Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h6' );
			if ( isset( $h6_font['size'] ) && isset( $h6_font['size'][2] ) && is_numeric( $h6_font['size'][2] ) ) {
				$css->add_property( 'font-size', $h6_font['size'][2] . ( isset( $h6_font['sizeType'] ) && ! empty( $h6_font['sizeType'] ) ? $h6_font['sizeType'] : 'px' ) );
			}
			if ( isset( $h6_font['lineHeight'] ) && isset( $h6_font['lineHeight'][2] ) && is_numeric( $h6_font['lineHeight'][2] ) ) {
				$css->add_property( 'line-height', $h6_font['lineHeight'][2] . ( isset( $h6_font['lineType'] ) && ! empty( $h6_font['lineType'] ) ? $h6_font['lineType'] : 'px' ) );
			}
			if ( isset( $h6_font['letterSpacing'] ) && isset( $h6_font['letterSpacing'][2] ) && is_numeric( $h6_font['letterSpacing'][2] ) ) {
				$css->add_property( 'letter-spacing', $h6_font['letterSpacing'][2] . ( isset( $h6_font['letterSpacingType'] ) && ! empty( $h6_font['letterSpacingType'] ) ? $h6_font['letterSpacingType'] : 'px' ) );
			}
		}
		$css->stop_media_query();
		self::$gfonts = $css->fonts_output();
		return $css->css_output();
	}
}
Kadence_Blocks_Pro_Dynamic_HTML_Block::get_instance();
