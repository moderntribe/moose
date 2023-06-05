<?php
/**
 * Dynamic_List Render
 *
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the Dynamic_List
 *
 * @category class
 */
class Kadence_Blocks_Pro_Dynamic_List {
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
		add_action( 'init', array( $this, 'dynamic_list_block' ), 20 );
		add_action( 'enqueue_block_assets', array( $this, 'blocks_assets' ) );
		add_action( 'wp_head', array( $this, 'frontend_gfonts' ), 85 );
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
		wp_register_style( 'kadence-blocks-dynamic-list', KBP_URL . 'dist/build/block-css/style-dynamic-list-styles.css', array(), KBP_VERSION );		
	}
	/**
	 * Register the dynamic block.
	 *
	 * @return void
	 */
	public function dynamic_list_block() {

		// Only load if Gutenberg is available.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		// Hook server side rendering into render callback.
		register_block_type(
			'kadence/dynamiclist',
			array(
				'attributes' => array(
					'uniqueID' => array(
						'type' => 'string',
					),
					'type' => array(
						'type' => 'string',
						'default' => 'tax',
					),
					'source' => array(
						'type' => 'string',
						'default' => '',
					),
					'tax' => array(
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
					'listDirection' => array(
						'type' => 'string',
						'default' => 'horizontal',
					),
					'listStyle' => array(
						'type' => 'string',
						'default' => 'basic',
					),
					'divider' => array(
						'type' => 'string',
						'default' => 'vline',
					),
					'alignment' => array(
						'type' => 'array',
						'default' => array( 'left', '', '' ),
						'items'   => array(
							'type' => 'string',
						),
					),
					'enableLink' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'linkStyle' => array(
						'type' => 'string',
					),
					// Color.
					'color' => array(
						'type' => 'string',
					),
					'hoverColor' => array(
						'type' => 'string',
					),
					'background' => array(
						'type' => 'string',
					),
					'hoverBackground' => array(
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
					'typography'=> array(
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
					'showAllFields' => array(
						'type' => 'boolean',
						'default' => false,
					),
				),
				'render_callback' => array( $this, 'render_dynamic_list' ),
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
	public function render_dynamic_list( $attributes ) {
		if ( ! wp_style_is( 'kadence-blocks-dynamic-list', 'enqueued' ) ) {
			wp_enqueue_style( 'kadence-blocks-dynamic-list' );
		}
		// Current || Post id.
		$source = ! empty( $attributes['source'] ) ? $attributes['source'] : get_the_ID();
		// Type: Tax, Meta.
		$type = ! empty( $attributes['type'] ) ? $attributes['type'] : 'tax';
		$enable_link = isset( $attributes['enableLink'] ) ? $attributes['enableLink'] : true;
		if ( 'meta' === $type ) {
			$field = ! empty( $attributes['metaField'] ) ? $attributes['metaField'] : '';
			if ( ! empty( $field ) ) {
				$args = array(
					'source'   => ( $source ? $source : 'current' ),
					'type'     => 'list',
					'field'    => 'post_custom_field',
					'group'    => 'post',
					'before'   => '',
					'after'    => '',
					'fallback' => '',
					'para'     => $field,
					'custom'   => ! empty( $attributes['customMeta'] ) ? $attributes['customMeta'] : '',
				);
				$dynamic_class = Kadence_Blocks_Pro_Dynamic_Content::get_instance();
				$items         = $dynamic_class->get_content( $args );
			}
		} else {
			$tax = ! empty( $attributes['tax'] ) ? $attributes['tax'] : '';
			if ( ! empty( $tax ) ) {
				$terms = get_the_terms( $source, $tax );
				if ( $terms && ! is_wp_error( $terms ) ) {
					$items = array();
					foreach( $terms as $term ) {
						$items[] = array(
							'value' => $term->term_id,
							'label' => $term->name,
						);
					}
				}
			}
		}
		// Bail if nothing to show.
		if ( empty( $items ) ) {
			return '';
		}

		$divider = ! empty( $attributes['divider'] ) ? $attributes['divider'] : 'vline';
		switch ( $divider ) {
			case 'dot':
				$separator = ' &middot; ';
				break;
			case 'slash':
				/* translators: separator between taxonomy terms */
				$separator = _x( ' / ', 'list item separator', 'kadence' );
				break;
			case 'dash':
				/* translators: separator between taxonomy terms */
				$separator = _x( ' - ', 'list item separator', 'kadence' );
				break;
			default:
				/* translators: separator between taxonomy terms */
				$separator = _x( ' | ', 'list item separator', 'kadence' );
				break;
		}
		$list_direction = ( ! empty( $attributes['listDirection'] ) ? $attributes['listDirection'] : 'horizontal' );
		$list_style     = ( ! empty( $attributes['listStyle'] ) ? $attributes['listStyle'] : 'basic' );
		$classes        = array( 'wp-block-kadence-dynamiclist', 'kb-dynamic-list' );
		if ( ! empty( $attributes['uniqueID'] ) ) {
			$classes[] = 'kb-dynamic-list-id-' . $attributes['uniqueID'];
		}
		$classes[] = 'kb-dynamic-list-layout-' . $list_direction;
		$classes[] = 'kb-dynamic-list-style-' . ( ! empty( $attributes['listStyle'] ) ? $attributes['listStyle'] : 'basic' );
		if ( ! empty( $attributes['alignment'][0] ) ) {
			$classes[] = 'kb-dynamic-list-alignment-' . $attributes['alignment'][0];
		}
		if ( ! empty( $attributes['alignment'][1] ) ) {
			$classes[] = 'kb-dynamic-list-tablet-alignment-' . $attributes['alignment'][1];
		}
		if ( ! empty( $attributes['alignment'][2] ) ) {
			$classes[] = 'kb-dynamic-list-mobile-alignment-' . $attributes['alignment'][2];
		}
		if ( ! empty( $divider ) && 'none' === $divider ) {
			$classes[] = 'kb-dynamic-list-divider-none';
		}
		if ( 'tax' === $type && $enable_link && ! empty( $attributes['linkStyle'] ) ) {
			$classes[] = 'kb-dynamic-list-link-style-' . $attributes['linkStyle'];
		}
		if ( ! empty( $attributes['className'] ) ) {
			$classes[] = $attributes['className'];
		}
		$list_tag = ( 'vertical' === $list_direction && 'numbers' === $list_style ? 'ol' : 'ul' );
		ob_start();
		echo '<' . esc_attr( $list_tag ) . ' class="' . esc_attr( implode( ' ', $classes ) ) . '">';
		$output = array();
		foreach ( $items as $key => $item ) {
			$item_string = '<li class="kb-dynamic-list-item">';
			if ( 'tax' === $type && $enable_link ) {
				$item_string .= '<a href="' . esc_url( get_term_link( $item['value'] ) ) . '" class="kb-dynamic-list-item-link">';
				$item_string .= esc_html( $item['label'] );
				$item_string .= '</a>';
			} else {
				$item_string .= esc_html( $item['label'] );
			}
			$item_string .= '</li>';
			$output[] = $item_string;
		}
		if ( 'horizontal' === $list_direction && 'pill' !== $list_style && 'none' !== $divider ) {
			echo implode( '<li class="kb-dynamic-list-item kb-dynamic-list-divider">' . $separator . '</li>', $output );
		} else {
			echo implode( '', $output );
		}
		echo '</' . esc_attr( $list_tag ) . '>';

		$content = ob_get_contents();
		ob_end_clean();
		if ( isset( $attributes['uniqueID'] ) ) {
			$unique_id = $attributes['uniqueID'];
			$style_id = 'kt-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) && apply_filters( 'kadence_blocks_render_inline_css', true, 'dynamiclist', $unique_id ) ) {
				if ( ! doing_filter( 'the_content' ) ) {
					if ( ! wp_style_is( 'kadence-blocks-dynamic-list', 'done' ) ) {
						wp_print_styles( 'kadence-blocks-dynamic-list' );
					}
				} else {
					if ( ! wp_style_is( 'kadence-blocks-dynamic-list', 'done' ) ) {
						ob_start();
							wp_print_styles( 'kadence-blocks-dynamic-list' );
						$content = ob_get_clean() . $content;
					}
				}
				$css = $this->output_css( $attributes, $unique_id );
				if ( ! empty( $css ) ) {
					if ( doing_filter( 'the_content' ) || apply_filters( 'kadence_blocks_force_render_inline_css_in_content', false, 'dynamiclist', $unique_id ) ) {
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
		$css->set_selector( '.wp-block-kadence-dynamiclist.kb-dynamic-list-id-' . $unique_id . '.kb-dynamic-list:not(.added-for-specificity)' );
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
		// Typography.
		$css->set_selector( '.kb-dynamic-list.kb-dynamic-list-id-' . $unique_id . ' .kb-dynamic-list-item' );
		if ( isset( $attributes['color'] ) && ! empty( $attributes['color'] ) ) {
			$css->add_property( 'color', $css->render_color( $attributes['color'] ) );
		}
		if ( isset( $attributes['typography'] ) && is_array( $attributes['typography'] ) && isset( $attributes['typography'][0] ) && is_array( $attributes['typography'][0] ) ) {
			$list_font = $attributes['typography'][0];
			if ( isset( $list_font['size'] ) && isset( $list_font['size'][0] ) && is_numeric( $list_font['size'][0] ) ) {
				$css->add_property( 'font-size', $list_font['size'][0] . ( isset( $list_font['sizeType'] ) && ! empty( $list_font['sizeType'] ) ? $list_font['sizeType'] : 'px' ) );
			}
			if ( isset( $list_font['lineHeight'] ) && isset( $list_font['lineHeight'][0] ) && is_numeric( $list_font['lineHeight'][0] ) ) {
				$css->add_property( 'line-height', $list_font['lineHeight'][0] . ( isset( $list_font['lineType'] ) && ! empty( $list_font['lineType'] ) ? $list_font['lineType'] : 'px' ) );
			}
			if ( isset( $list_font['letterSpacing'] ) && isset( $list_font['letterSpacing'][0] ) && is_numeric( $list_font['letterSpacing'][0] ) ) {
				$css->add_property( 'letter-spacing', $list_font['letterSpacing'][0] . ( isset( $list_font['letterSpacingType'] ) && ! empty( $list_font['letterSpacingType'] ) ? $list_font['letterSpacingType'] : 'px' ) );
			}
			if ( isset( $list_font['family'] ) && ! empty( $list_font['family'] ) ) {
				$google = isset( $list_font['google'] ) && $list_font['google'] ? true : false;
				$google = $google && ( isset( $list_font['loadGoogle'] ) && $list_font['loadGoogle'] || ! isset( $list_font['loadGoogle'] ) ) ? true : false;
				$css->add_property( 'font-family', $css->render_font_family( $list_font['family'], $google, ( isset( $list_font['variation'] ) ? $list_font['variation'] : '' ), ( isset( $list_font['subset'] ) ? $list_font['subset'] : '' ) ) );
			}
			if ( isset( $list_font['weight'] ) && ! empty( $list_font['weight'] ) ) {
				$css->add_property( 'font-weight', $css->render_string( $list_font['weight'] ) );
			}
			if ( isset( $list_font['style'] ) && ! empty( $list_font['style'] ) ) {
				$css->add_property( 'font-style', $css->render_string( $list_font['style'] ) );
			}
			if ( isset( $list_font['textTransform'] ) && ! empty( $list_font['textTransform'] ) ) {
				$css->add_property( 'text-transform', $css->render_string( $list_font['textTransform'] ) );
			}
		}
		$type = ! empty( $attributes['type'] ) ? $attributes['type'] : 'tax';
		$enable_link = isset( $attributes['enableLink'] ) ? $attributes['enableLink'] : true;
		if ( 'tax' === $type && $enable_link && isset( $attributes['hoverColor'] ) && ! empty( $attributes['hoverColor'] ) ) {
			$css->set_selector( '.kb-dynamic-list-id-' . $unique_id . '.kb-dynamic-list-style-pill .kb-dynamic-list-item:hover, .kb-dynamic-list-id-' . $unique_id . ' .kb-dynamic-list-item a:hover' );
			$css->add_property( 'color', $css->render_color( $attributes['hoverColor'] ) );
		}
		if ( isset( $attributes['background'] ) && ! empty( $attributes['background'] ) ) {
			$css->set_selector( '.kb-dynamic-list-id-' . $unique_id . '.kb-dynamic-list-style-pill .kb-dynamic-list-item' );
			$css->add_property( 'background', $css->render_color( $attributes['background'] ) );
		}
		if ( 'tax' === $type && $enable_link && isset( $attributes['hoverBackground'] ) && ! empty( $attributes['hoverBackground'] ) ) {
			$css->set_selector( '.kb-dynamic-list-id-' . $unique_id . '.kb-dynamic-list-style-pill .kb-dynamic-list-item:hover' );
			$css->add_property( 'background', $css->render_color( $attributes['hoverBackground'] ) );
		}

		// Tablet.
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '.wp-block-kadence-dynamiclist.kb-dynamic-list-id-' . $unique_id . '.kb-dynamic-list:not(.added-for-specificity)' );
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
		// Name.
		$css->set_selector( '.kb-dynamic-list.kb-dynamic-list-id-' . $unique_id . ' .kb-dynamic-list-item' );
		if ( isset( $attributes['typography'] ) && is_array( $attributes['typography'] ) && isset( $attributes['typography'][0] ) && is_array( $attributes['typography'][0] ) ) {
			$list_font = $attributes['typography'][0];
			if ( isset( $list_font['size'] ) && isset( $list_font['size'][1] ) && is_numeric( $list_font['size'][1] ) ) {
				$css->add_property( 'font-size', $list_font['size'][1] . ( isset( $list_font['sizeType'] ) && ! empty( $list_font['sizeType'] ) ? $list_font['sizeType'] : 'px' ) );
			}
			if ( isset( $list_font['lineHeight'] ) && isset( $list_font['lineHeight'][1] ) && is_numeric( $list_font['lineHeight'][1] ) ) {
				$css->add_property( 'line-height', $list_font['lineHeight'][1] . ( isset( $list_font['lineType'] ) && ! empty( $list_font['lineType'] ) ? $list_font['lineType'] : 'px' ) );
			}
			if ( isset( $list_font['letterSpacing'] ) && isset( $list_font['letterSpacing'][1] ) && is_numeric( $list_font['letterSpacing'][1] ) ) {
				$css->add_property( 'letter-spacing', $list_font['letterSpacing'][1] . ( isset( $list_font['letterSpacingType'] ) && ! empty( $list_font['letterSpacingType'] ) ? $list_font['letterSpacingType'] : 'px' ) );
			}
		}
		$css->stop_media_query();
		// Mobile.
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '.wp-block-kadence-dynamiclist.kb-dynamic-list-id-' . $unique_id . '.kb-dynamic-list:not(.added-for-specificity)' );
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
		// Name.
		$css->set_selector( '.kb-dynamic-list.kb-dynamic-list-id-' . $unique_id . ' .kb-dynamic-list-item' );
		if ( isset( $attributes['typography'] ) && is_array( $attributes['typography'] ) && isset( $attributes['typography'][0] ) && is_array( $attributes['typography'][0] ) ) {
			$list_font = $attributes['typography'][0];
			if ( isset( $list_font['size'] ) && isset( $list_font['size'][2] ) && is_numeric( $list_font['size'][2] ) ) {
				$css->add_property( 'font-size', $list_font['size'][2] . ( isset( $list_font['sizeType'] ) && ! empty( $list_font['sizeType'] ) ? $list_font['sizeType'] : 'px' ) );
			}
			if ( isset( $list_font['lineHeight'] ) && isset( $list_font['lineHeight'][2] ) && is_numeric( $list_font['lineHeight'][2] ) ) {
				$css->add_property( 'line-height', $list_font['lineHeight'][2] . ( isset( $list_font['lineType'] ) && ! empty( $list_font['lineType'] ) ? $list_font['lineType'] : 'px' ) );
			}
			if ( isset( $list_font['letterSpacing'] ) && isset( $list_font['letterSpacing'][2] ) && is_numeric( $list_font['letterSpacing'][2] ) ) {
				$css->add_property( 'letter-spacing', $list_font['letterSpacing'][2] . ( isset( $list_font['letterSpacingType'] ) && ! empty( $list_font['letterSpacingType'] ) ? $list_font['letterSpacingType'] : 'px' ) );
			}
		}
		$css->stop_media_query();
		self::$gfonts = $css->fonts_output();
		return $css->css_output();
	}
}
Kadence_Blocks_Pro_Dynamic_List::get_instance();
