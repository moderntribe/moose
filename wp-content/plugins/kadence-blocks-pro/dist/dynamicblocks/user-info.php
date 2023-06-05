<?php
/**
 * User Info Render
 *
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the User Info
 *
 * @category class
 */
class Kadence_Blocks_Pro_User_Info {
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
		add_action( 'init', array( $this, 'user_info' ), 20 );
		add_action( 'enqueue_block_assets', array( $this, 'blocks_assets' ) );
		add_action( 'wp_head', array( $this, 'frontend_gfonts' ), 85 );
		add_action( 'rest_api_init', array( $this, 'register_rest_user_data' ) );
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
	 * Registers WooCommerce specific user data to the WordPress user API.
	 */
	public function register_rest_user_data() {
		register_rest_field(
			'user',
			'kb',
			array(
				'get_callback'    => array( $this, 'get_user_rest_info' ),
				'update_callback' => null,
				'schema'          => null,
			)
		);
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
		wp_register_style( 'kadence-blocks-user-info', KBP_URL . 'dist/blocks/userinfo.style.build.css', array(), KBP_VERSION );		
	}
	/**
	 * Get user info for the rest field
	 *
	 * @param object $object Post Object.
	 * @param string $field_name Field name.
	 * @param object $request Request Object.
	 */
	public function get_user_rest_info( $object, $field_name, $request ) {
		$udata           = wp_get_current_user();
		$registered      = $udata->user_registered;
		$registered_date = gmdate( get_option( 'date_format' ), strtotime( $registered ) );
		$data = array(
			'avatar'     => get_avatar_url( $udata, array( 'size' => '300' ) ),
			'registered' => $registered_date,
		);
		return apply_filters( 'kadence_blocks_pro_rest_user_data', $data );
	}

	/**
	 * Register the dynamic block.
	 *
	 * @since 1.0.5
	 *
	 * @return void
	 */
	public function user_info() {

		// Only load if Gutenberg is available.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		// Hook server side rendering into render callback.
		register_block_type(
			'kadence/userinfo',
			array(
				'attributes' => array(
					'uniqueID' => array(
						'type' => 'string',
					),
					'layout' => array(
						'type' => 'array',
						'default' => array( 'left', '', '' ),
						'items'   => array(
							'type' => 'string',
						),
					),
					'enableAvatar'=> array(
						'type' => 'boolean',
						'default' => true,
					),
					'avatarWidth' => array(
						'type' => 'number',
						'default' => 80,
					),
					'avatarBorderRadius'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 0, 0 ),
						'items'   => array(
							'type' => 'integer',
						),
					),
					'avatarBorderColor' => array(
						'type' => 'string',
					),
					'avatarBorderWidth'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 0, 0 ),
						'items'   => array(
							'type' => 'integer',
						),
					),
					'avatarPadding'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 0, 0 ),
						'items'   => array(
							'type' => 'integer',
						),
					),
					'avatarPaddingType' => array(
						'type' => 'string',
						'default' => 'px',
					),
					'avatarGap' => array(
						'type' => 'number',
						'default' => 30,
					),
					// Container.
					'background' => array(
						'type' => 'string',
					),
					'borderColor' => array(
						'type' => 'string',
					),
					'padding' => array(
						'type' => 'array',
						'default' => array( 20, 20, 20, 20 ),
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
					'borderWidth'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 0, 0 ),
						'items'   => array(
							'type' => 'integer',
						),
					),
					'borderRadius'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 0, 0 ),
						'items'   => array(
							'type' => 'integer',
						),
					),
					'displayShadow' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'shadow'=> array(
						'type' => 'array',
						'default' => array(
							array(
								'color' => 'rgba(0,0,0,0.2)',
								'spread' => 0,
								'blur' => 14,
								'hOffset' => 0,
								'vOffset' => 0,
								'inset' => false,
							)
						),
						'items'   => array(
							'type' => 'object',
						),
					),
					// Text Align.
					'textAlign'=> array(
						'type' => 'string',
					),
					// Name.
					'enableName' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'nameTag'=> array(
						'type' => 'string',
						'default' => 'h2',
					),
					'namePreText'=> array(
						'type' => 'string',
						'default' => '',
					),
					'nameColor'=> array(
						'type' => 'string',
					),
					'nameFont'=> array(
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
					'namePadding'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 0, 0 ),
						'items'   => array(
							'type' => 'integer',
						),
					),
					'namePaddingType' => array(
						'type' => 'string',
						'default' => 'px',
					),
					'nameMargin'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 0, 0 ),
						'items'   => array(
							'type' => 'integer',
						),
					),
					'nameMarginType' => array(
						'type' => 'string',
						'default' => 'px',
					),
					// Date.
					'enableDate' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'datePreText' => array(
						'type'    => 'string',
						'default' => __( 'Joined on', 'kadence-blocks-pro' ),
					),
					'dateColor'=> array(
						'type' => 'string',
					),
					'dateFont'=> array(
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
					'datePadding'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 0, 0 ),
						'items'   => array(
							'type' => 'integer',
						),
					),
					'datePaddingType' => array(
						'type' => 'string',
						'default' => 'px',
					),
					'dateMargin'=> array(
						'type' => 'array',
						'default' => array( 0, 0, 0, 0 ),
						'items'   => array(
							'type' => 'integer',
						),
					),
					'dateMarginType' => array(
						'type' => 'string',
						'default' => 'px',
					),
				),
				'render_callback' => array( $this, 'render_user_info' ),
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
	public function render_user_info( $attributes ) {
		if ( ! is_user_logged_in() ) {
			return false;
		}
		$current_user = wp_get_current_user();
		if ( ! $current_user instanceof WP_User ) {
			return false;
		}
		if ( ! wp_style_is( 'kadence-blocks-user-info', 'enqueued' ) ) {
			wp_enqueue_style( 'kadence-blocks-user-info' );
		}
		$avatar_size = ( isset( $attributes['avatarWidth'] ) && ! empty( $attributes['avatarWidth'] ) ? $attributes['avatarWidth'] : 80 );
		ob_start();
		echo '<div class="wp-block-kadence-user-info kb-user-info-' . ( isset( $attributes['uniqueID'] ) ? esc_attr( $attributes['uniqueID'] ) : 'block-id' ) . ' kb-user-info-layout-' . ( isset( $attributes['layout'] ) && isset( $attributes['layout'][0] ) ? esc_attr( $attributes['layout'][0] ) : 'left' ) . ' kb-user-info-tablet-layout-' . ( isset( $attributes['layout'] ) && isset( $attributes['layout'][1] ) && ! empty( $attributes['layout'][1] ) ? esc_attr( $attributes['layout'][1] ) : 'inherit' ) . ' kb-user-info-mobile-layout-' . ( isset( $attributes['layout'] ) && isset( $attributes['layout'][2] ) && ! empty( $attributes['layout'][2] ) ? esc_attr( $attributes['layout'][2] ) : 'inherit' ) . ( isset( $attributes['className'] ) && ! empty( $attributes['className'] ) ? ' ' . esc_attr( $attributes['className'] ) : '' ) . '">';
		echo '<div class="kb-user-info-wrap">';
		if ( ! isset( $attributes['enableAvatar'] ) || ( isset( $attributes['enableAvatar'] ) && true == $attributes['enableAvatar'] ) ) {
			echo '<div class="kb-user-info-avatar">';
			echo get_avatar( $current_user->ID, $avatar_size );
			echo '</div>';
		}
		echo '<div class="kb-user-info-content">';
		if ( ! isset( $attributes['enableName'] ) || ( isset( $attributes['enableName'] ) && true == $attributes['enableName'] ) ) {
			$name_tag = ( isset( $attributes['nameTag'] ) && ! empty( $attributes['nameTag'] ) ? $attributes['nameTag'] : 'h2' );
			echo '<' . esc_attr( $name_tag ) . ' class="kb-user-info-name">';
			if ( isset( $attributes['namePreText'] ) && ! empty( $attributes['namePreText'] ) ) {
				echo esc_html( $attributes['namePreText'] ) . ' ';
			}
			echo esc_html( $current_user->display_name );
			echo '</' . esc_attr( $name_tag ) . '>';
		}
		if ( ! isset( $attributes['enableDate'] ) || ( isset( $attributes['enableDate'] ) && true == $attributes['enableDate'] ) ) {
			echo '<div class="kb-user-info-joined">';
			if ( isset( $attributes['datePreText'] ) && ! empty( $attributes['datePreText'] ) ) {
				echo esc_html( $attributes['datePreText'] ) . ' ';
			}
			echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $current_user->user_registered ) ) );
			echo '</div>';
		}
		echo '</div>';
		echo '</div>';
		echo '</div>';

		$content = ob_get_contents();
		ob_end_clean();
		if ( isset( $attributes['uniqueID'] ) ) {
			$unique_id = $attributes['uniqueID'];
			$style_id = 'kt-blocks' . esc_attr( $unique_id );
			if ( ! wp_style_is( $style_id, 'enqueued' ) && apply_filters( 'kadence_blocks_render_inline_css', true, 'userinfo', $unique_id ) ) {
				if ( ! doing_filter( 'the_content' ) ) {
					if ( ! wp_style_is( 'kadence-blocks-user-info', 'done' ) ) {
						wp_print_styles( 'kadence-blocks-user-info' );
					}
				} else {
					if ( ! wp_style_is( 'kadence-blocks-user-info', 'done' ) ) {
						ob_start();
							wp_print_styles( 'kadence-blocks-user-info' );
						$content = ob_get_clean() . $content;
					}
				}
				$css = $this->output_css( $attributes, $unique_id );
				if ( ! empty( $css ) ) {
					if ( doing_filter( 'the_content' ) || apply_filters( 'kadence_blocks_force_render_inline_css_in_content', false, 'userinfo', $unique_id ) ) {
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
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-wrap' );
		if ( isset( $attributes['background'] ) && ! empty( $attributes['background'] ) ) {
			$css->add_property( 'background', $css->render_color( $attributes['background'] ) );
		}
		if ( isset( $attributes['borderColor'] ) && ! empty( $attributes['borderColor'] ) ) {
			$css->add_property( 'border-color', $css->render_color( $attributes['borderColor'] ) );
		}
		if ( isset( $attributes['borderRadius'] ) && isset( $attributes['borderRadius'][0] ) ) {
			if ( is_numeric( $attributes['borderRadius'][0] ) ) {
				$css->add_property( 'border-top-left-radius', $attributes['borderRadius'][0] . 'px' );
			}
			if ( is_numeric( $attributes['borderRadius'][1] ) ) {
				$css->add_property( 'border-top-right-radius', $attributes['borderRadius'][1] . 'px' );
			}
			if ( is_numeric( $attributes['borderRadius'][2] ) ) {
				$css->add_property( 'border-bottom-right-radius', $attributes['borderRadius'][2] . 'px' );
			}
			if ( is_numeric( $attributes['borderRadius'][3] ) ) {
				$css->add_property( 'border-bottom-left-radius', $attributes['borderRadius'][3] . 'px' );
			}
		}
		if ( isset( $attributes['borderWidth'] ) && isset( $attributes['borderWidth'][0] ) ) {
			if ( is_numeric( $attributes['borderWidth'][0] ) ) {
				$css->add_property( 'border-top-width', $attributes['borderWidth'][0] . 'px' );
			}
			if ( is_numeric( $attributes['borderWidth'][1] ) ) {
				$css->add_property( 'border-right-width', $attributes['borderWidth'][1] . 'px' );
			}
			if ( is_numeric( $attributes['borderWidth'][2] ) ) {
				$css->add_property( 'border-bottom-width', $attributes['borderWidth'][2] . 'px' );
			}
			if ( is_numeric( $attributes['borderWidth'][3] ) ) {
				$css->add_property( 'border-left-width', $attributes['borderWidth'][3] . 'px' );
			}
		}
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
		if ( isset( $attributes['displayShadow'] ) && true == $attributes['displayShadow'] ) {
			if ( isset( $attributes['shadow'] ) && is_array( $attributes['shadow'] ) && isset( $attributes['shadow'][0] ) && is_array( $attributes['shadow'][0] ) ) {
				$css->add_property( 'box-shadow', $css->render_shadow( $attributes['shadow'][0] ) );
			} else {
				$css->add_property( 'box-shadow', 'rgba(0, 0, 0, 0.2) 0px 0px 14px 0px' );
			}
		}
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-avatar' );
		if ( isset( $attributes['avatarPadding'] ) && isset( $attributes['avatarPadding'][0] ) ) {
			if ( is_numeric( $attributes['avatarPadding'][0] ) ) {
				$css->add_property( 'padding-top', $attributes['avatarPadding'][0] . ( isset( $attributes['avatarPaddingType'] ) && ! empty( $attributes['avatarPaddingType'] ) ? $attributes['avatarPaddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['avatarPadding'][1] ) ) {
				$css->add_property( 'padding-right', $attributes['avatarPadding'][1] . ( isset( $attributes['avatarPaddingType'] ) && ! empty( $attributes['avatarPaddingType'] ) ? $attributes['avatarPaddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['avatarPadding'][2] ) ) {
				$css->add_property( 'padding-bottom', $attributes['avatarPadding'][2] . ( isset( $attributes['avatarPaddingType'] ) && ! empty( $attributes['avatarPaddingType'] ) ? $attributes['avatarPaddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['avatarPadding'][3] ) ) {
				$css->add_property( 'padding-left', $attributes['avatarPadding'][3] . ( isset( $attributes['avatarPaddingType'] ) && ! empty( $attributes['avatarPaddingType'] ) ? $attributes['avatarPaddingType'] : 'px' ) );
			}
		}
		if ( isset( $attributes['avatarGap'] ) && is_numeric( $attributes['avatarGap'] ) ) {
			if ( isset( $attributes['layout'] ) && isset( $attributes['layout'][0] ) && ! empty( $attributes['layout'][0] ) ) {
				if ( 'center' === $attributes['layout'][0] ) {
					$css->add_property( 'margin-bottom', $attributes['avatarGap'] . 'px' );
				} elseif ( 'right' === $attributes['layout'][0] ) {
					$css->add_property( 'margin-left', $attributes['avatarGap'] . 'px' );
				} else {
					$css->add_property( 'margin-right', $attributes['avatarGap'] . 'px' );
				}
			} else {
				$css->add_property( 'margin-right', $attributes['avatarGap'] . 'px' );
			}
		}
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-avatar img' );
		if ( isset( $attributes['avatarBorderColor'] ) && ! empty( $attributes['avatarBorderColor'] ) ) {
			$css->add_property( 'border-color', $css->render_color( $attributes['avatarBorderColor'] ) );
		}
		if ( isset( $attributes['avatarWidth'] ) && is_numeric( $attributes['avatarWidth'] ) ) {
			$css->add_property( 'max-width', $attributes['avatarWidth'] . 'px' );
		}
		if ( isset( $attributes['avatarBorderRadius'] ) && isset( $attributes['avatarBorderRadius'][0] ) ) {
			if ( is_numeric( $attributes['avatarBorderRadius'][0] ) ) {
				$css->add_property( 'border-top-left-radius', $attributes['avatarBorderRadius'][0] . 'px' );
			}
			if ( is_numeric( $attributes['avatarBorderRadius'][1] ) ) {
				$css->add_property( 'border-top-right-radius', $attributes['avatarBorderRadius'][1] . 'px' );
			}
			if ( is_numeric( $attributes['avatarBorderRadius'][2] ) ) {
				$css->add_property( 'border-bottom-right-radius', $attributes['avatarBorderRadius'][2] . 'px' );
			}
			if ( is_numeric( $attributes['avatarBorderRadius'][3] ) ) {
				$css->add_property( 'border-bottom-left-radius', $attributes['avatarBorderRadius'][3] . 'px' );
			}
		}
		if ( isset( $attributes['avatarBorderWidth'] ) && isset( $attributes['avatarBorderWidth'][0] ) ) {
			if ( is_numeric( $attributes['avatarBorderWidth'][0] ) ) {
				$css->add_property( 'border-top-width', $attributes['avatarBorderWidth'][0] . 'px' );
			}
			if ( is_numeric( $attributes['avatarBorderWidth'][1] ) ) {
				$css->add_property( 'border-right-width', $attributes['avatarBorderWidth'][1] . 'px' );
			}
			if ( is_numeric( $attributes['avatarBorderWidth'][2] ) ) {
				$css->add_property( 'border-bottom-width', $attributes['avatarBorderWidth'][2] . 'px' );
			}
			if ( is_numeric( $attributes['avatarBorderWidth'][3] ) ) {
				$css->add_property( 'border-left-width', $attributes['avatarBorderWidth'][3] . 'px' );
			}
		}
		// Name.
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-content .kb-user-info-name' );
		if ( isset( $attributes['namePadding'] ) && isset( $attributes['namePadding'][0] ) ) {
			if ( is_numeric( $attributes['namePadding'][0] ) ) {
				$css->add_property( 'padding-top', $attributes['namePadding'][0] . ( isset( $attributes['namePaddingType'] ) && ! empty( $attributes['namePaddingType'] ) ? $attributes['namePaddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['namePadding'][1] ) ) {
				$css->add_property( 'padding-right', $attributes['namePadding'][1] . ( isset( $attributes['namePaddingType'] ) && ! empty( $attributes['namePaddingType'] ) ? $attributes['namePaddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['namePadding'][2] ) ) {
				$css->add_property( 'padding-bottom', $attributes['namePadding'][2] . ( isset( $attributes['namePaddingType'] ) && ! empty( $attributes['namePaddingType'] ) ? $attributes['namePaddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['namePadding'][3] ) ) {
				$css->add_property( 'padding-left', $attributes['namePadding'][3] . ( isset( $attributes['namePaddingType'] ) && ! empty( $attributes['namePaddingType'] ) ? $attributes['namePaddingType'] : 'px' ) );
			}
		}
		if ( isset( $attributes['nameMargin'] ) && isset( $attributes['nameMargin'][0] ) ) {
			if ( is_numeric( $attributes['nameMargin'][0] ) ) {
				$css->add_property( 'margin-top', $attributes['nameMargin'][0] . ( isset( $attributes['nameMarginType'] ) && ! empty( $attributes['nameMarginType'] ) ? $attributes['nameMarginType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['nameMargin'][1] ) ) {
				$css->add_property( 'margin-right', $attributes['nameMargin'][1] . ( isset( $attributes['nameMarginType'] ) && ! empty( $attributes['nameMarginType'] ) ? $attributes['nameMarginType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['nameMargin'][2] ) ) {
				$css->add_property( 'margin-bottom', $attributes['nameMargin'][2] . ( isset( $attributes['nameMarginType'] ) && ! empty( $attributes['nameMarginType'] ) ? $attributes['nameMarginType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['nameMargin'][3] ) ) {
				$css->add_property( 'margin-left', $attributes['nameMargin'][3] . ( isset( $attributes['nameMarginType'] ) && ! empty( $attributes['nameMarginType'] ) ? $attributes['nameMarginType'] : 'px' ) );
			}
		}
		if ( isset( $attributes['nameColor'] ) && ! empty( $attributes['nameColor'] ) ) {
			$css->add_property( 'color', $css->render_color( $attributes['nameColor'] ) );
		}
		if ( isset( $attributes['nameFont'] ) && is_array( $attributes['nameFont'] ) && isset( $attributes['nameFont'][0] ) && is_array( $attributes['nameFont'][0] ) ) {
			$name_font = $attributes['nameFont'][0];
			if ( isset( $name_font['size'] ) && isset( $name_font['size'][0] ) && is_numeric( $name_font['size'][0] ) ) {
				$css->add_property( 'font-size', $name_font['size'][0] . ( isset( $name_font['sizeType'] ) && ! empty( $name_font['sizeType'] ) ? $name_font['sizeType'] : 'px' ) );
			}
			if ( isset( $name_font['lineHeight'] ) && isset( $name_font['lineHeight'][0] ) && is_numeric( $name_font['lineHeight'][0] ) ) {
				$css->add_property( 'line-height', $name_font['lineHeight'][0] . ( isset( $name_font['lineType'] ) && ! empty( $name_font['lineType'] ) ? $name_font['lineType'] : 'px' ) );
			}
			if ( isset( $name_font['letterSpacing'] ) && isset( $name_font['letterSpacing'][0] ) && is_numeric( $name_font['letterSpacing'][0] ) ) {
				$css->add_property( 'letter-spacing', $name_font['letterSpacing'][0] . ( isset( $name_font['letterSpacingType'] ) && ! empty( $name_font['letterSpacingType'] ) ? $name_font['letterSpacingType'] : 'px' ) );
			}
			if ( isset( $name_font['family'] ) && ! empty( $name_font['family'] ) ) {
				$google = isset( $name_font['google'] ) && $name_font['google'] ? true : false;
				$google = $google && ( isset( $name_font['loadGoogle'] ) && $name_font['loadGoogle'] || ! isset( $name_font['loadGoogle'] ) ) ? true : false;
				$css->add_property( 'font-family', $css->render_font_family( $name_font['family'], $google, ( isset( $name_font['variation'] ) ? $name_font['variation'] : '' ), ( isset( $name_font['subset'] ) ? $name_font['subset'] : '' ) ) );
			}
			if ( isset( $name_font['weight'] ) && ! empty( $name_font['weight'] ) ) {
				$css->add_property( 'font-weight', $css->render_string( $name_font['weight'] ) );
			}
			if ( isset( $name_font['style'] ) && ! empty( $name_font['style'] ) ) {
				$css->add_property( 'font-style', $css->render_string( $name_font['style'] ) );
			}
			if ( isset( $name_font['textTransform'] ) && ! empty( $name_font['textTransform'] ) ) {
				$css->add_property( 'text-transform', $css->render_string( $name_font['textTransform'] ) );
			}
		}

		// Date.
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-content .kb-user-info-joined' );
		if ( isset( $attributes['datePadding'] ) && isset( $attributes['datePadding'][0] ) ) {
			if ( is_numeric( $attributes['datePadding'][0] ) ) {
				$css->add_property( 'padding-top', $attributes['datePadding'][0] . ( isset( $attributes['datePaddingType'] ) && ! empty( $attributes['datePaddingType'] ) ? $attributes['datePaddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['datePadding'][1] ) ) {
				$css->add_property( 'padding-right', $attributes['datePadding'][1] . ( isset( $attributes['datePaddingType'] ) && ! empty( $attributes['datePaddingType'] ) ? $attributes['datePaddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['datePadding'][2] ) ) {
				$css->add_property( 'padding-bottom', $attributes['datePadding'][2] . ( isset( $attributes['datePaddingType'] ) && ! empty( $attributes['datePaddingType'] ) ? $attributes['datePaddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['datePadding'][3] ) ) {
				$css->add_property( 'padding-left', $attributes['datePadding'][3] . ( isset( $attributes['datePaddingType'] ) && ! empty( $attributes['datePaddingType'] ) ? $attributes['datePaddingType'] : 'px' ) );
			}
		}
		if ( isset( $attributes['dateMargin'] ) && isset( $attributes['dateMargin'][0] ) ) {
			if ( is_numeric( $attributes['dateMargin'][0] ) ) {
				$css->add_property( 'margin-top', $attributes['dateMargin'][0] . ( isset( $attributes['dateMarginType'] ) && ! empty( $attributes['dateMarginType'] ) ? $attributes['dateMarginType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['dateMargin'][1] ) ) {
				$css->add_property( 'margin-right', $attributes['dateMargin'][1] . ( isset( $attributes['dateMarginType'] ) && ! empty( $attributes['dateMarginType'] ) ? $attributes['dateMarginType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['dateMargin'][2] ) ) {
				$css->add_property( 'margin-bottom', $attributes['dateMargin'][2] . ( isset( $attributes['dateMarginType'] ) && ! empty( $attributes['dateMarginType'] ) ? $attributes['dateMarginType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['dateMargin'][3] ) ) {
				$css->add_property( 'margin-left', $attributes['dateMargin'][3] . ( isset( $attributes['dateMarginType'] ) && ! empty( $attributes['dateMarginType'] ) ? $attributes['dateMarginType'] : 'px' ) );
			}
		}
		if ( isset( $attributes['dateColor'] ) && ! empty( $attributes['dateColor'] ) ) {
			$css->add_property( 'color', $css->render_color( $attributes['dateColor'] ) );
		}
		if ( isset( $attributes['dateFont'] ) && is_array( $attributes['dateFont'] ) && isset( $attributes['dateFont'][0] ) && is_array( $attributes['dateFont'][0] ) ) {
			$date_font = $attributes['dateFont'][0];
			if ( isset( $date_font['size'] ) && isset( $date_font['size'][0] ) && is_numeric( $date_font['size'][0] ) ) {
				$css->add_property( 'font-size', $date_font['size'][0] . ( isset( $date_font['sizeType'] ) && ! empty( $date_font['sizeType'] ) ? $date_font['sizeType'] : 'px' ) );
			}
			if ( isset( $date_font['lineHeight'] ) && isset( $date_font['lineHeight'][0] ) && is_numeric( $date_font['lineHeight'][0] ) ) {
				$css->add_property( 'line-height', $date_font['lineHeight'][0] . ( isset( $date_font['lineType'] ) && ! empty( $date_font['lineType'] ) ? $date_font['lineType'] : 'px' ) );
			}
			if ( isset( $date_font['letterSpacing'] ) && isset( $date_font['letterSpacing'][0] ) && is_numeric( $date_font['letterSpacing'][0] ) ) {
				$css->add_property( 'letter-spacing', $date_font['letterSpacing'][0] . ( isset( $date_font['letterSpacingType'] ) && ! empty( $date_font['letterSpacingType'] ) ? $date_font['letterSpacingType'] : 'px' ) );
			}
			if ( isset( $date_font['family'] ) && ! empty( $date_font['family'] ) ) {
				$google = isset( $date_font['google'] ) && $date_font['google'] ? true : false;
				$google = $google && ( isset( $date_font['loadGoogle'] ) && $date_font['loadGoogle'] || ! isset( $date_font['loadGoogle'] ) ) ? true : false;
				$css->add_property( 'font-family', $css->render_font_family( $date_font['family'], $google, ( isset( $date_font['variation'] ) ? $date_font['variation'] : '' ), ( isset( $date_font['subset'] ) ? $date_font['subset'] : '' ) ) );
			}
			if ( isset( $date_font['weight'] ) && ! empty( $date_font['weight'] ) ) {
				$css->add_property( 'font-weight', $css->render_string( $date_font['weight'] ) );
			}
			if ( isset( $date_font['style'] ) && ! empty( $date_font['style'] ) ) {
				$css->add_property( 'font-style', $css->render_string( $date_font['style'] ) );
			}
			if ( isset( $date_font['textTransform'] ) && ! empty( $date_font['textTransform'] ) ) {
				$css->add_property( 'text-transform', $css->render_string( $date_font['textTransform'] ) );
			}
		}
		// Tablet.
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-wrap' );
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
		// Avatar.
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-avatar' );
		if ( isset( $attributes['avatarGap'] ) && is_numeric( $attributes['avatarGap'] ) ) {
			if ( isset( $attributes['layout'] ) && isset( $attributes['layout'][1] ) && ! empty( $attributes['layout'][1] ) ) {
				if ( 'center' === $attributes['layout'][1] ) {
					$css->add_property( 'margin-bottom', $attributes['avatarGap'] . 'px' );
					$css->add_property( 'margin-left', '0px' );
					$css->add_property( 'margin-right', '0px' );
				} elseif ( 'right' === $attributes['layout'][1] ) {
					$css->add_property( 'margin-left', $attributes['avatarGap'] . 'px' );
					$css->add_property( 'margin-bottom', '0px' );
					$css->add_property( 'margin-right', '0px' );
				} else {
					$css->add_property( 'margin-right', $attributes['avatarGap'] . 'px' );
					$css->add_property( 'margin-bottom', '0px' );
					$css->add_property( 'margin-left', '0px' );
				}
			}
		}
		// Name.
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-content .kb-user-info-name' );
		if ( isset( $attributes['nameFont'] ) && is_array( $attributes['nameFont'] ) && isset( $attributes['nameFont'][0] ) && is_array( $attributes['nameFont'][0] ) ) {
			$name_font = $attributes['nameFont'][0];
			if ( isset( $name_font['size'] ) && isset( $name_font['size'][1] ) && is_numeric( $name_font['size'][1] ) ) {
				$css->add_property( 'font-size', $name_font['size'][1] . ( isset( $name_font['sizeType'] ) && ! empty( $name_font['sizeType'] ) ? $name_font['sizeType'] : 'px' ) );
			}
			if ( isset( $name_font['lineHeight'] ) && isset( $name_font['lineHeight'][1] ) && is_numeric( $name_font['lineHeight'][1] ) ) {
				$css->add_property( 'line-height', $name_font['lineHeight'][1] . ( isset( $name_font['lineType'] ) && ! empty( $name_font['lineType'] ) ? $name_font['lineType'] : 'px' ) );
			}
			if ( isset( $name_font['letterSpacing'] ) && isset( $name_font['letterSpacing'][1] ) && is_numeric( $name_font['letterSpacing'][1] ) ) {
				$css->add_property( 'letter-spacing', $name_font['letterSpacing'][1] . ( isset( $name_font['letterSpacingType'] ) && ! empty( $name_font['letterSpacingType'] ) ? $name_font['letterSpacingType'] : 'px' ) );
			}
		}
		// Date.
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-content .kb-user-info-joined' );
		if ( isset( $attributes['dateFont'] ) && is_array( $attributes['dateFont'] ) && isset( $attributes['dateFont'][0] ) && is_array( $attributes['dateFont'][0] ) ) {
			$date_font = $attributes['dateFont'][0];
			if ( isset( $date_font['size'] ) && isset( $date_font['size'][1] ) && is_numeric( $date_font['size'][1] ) ) {
				$css->add_property( 'font-size', $date_font['size'][1] . ( isset( $date_font['sizeType'] ) && ! empty( $date_font['sizeType'] ) ? $date_font['sizeType'] : 'px' ) );
			}
			if ( isset( $date_font['lineHeight'] ) && isset( $date_font['lineHeight'][1] ) && is_numeric( $date_font['lineHeight'][1] ) ) {
				$css->add_property( 'line-height', $date_font['lineHeight'][1] . ( isset( $date_font['lineType'] ) && ! empty( $date_font['lineType'] ) ? $date_font['lineType'] : 'px' ) );
			}
			if ( isset( $date_font['letterSpacing'] ) && isset( $date_font['letterSpacing'][1] ) && is_numeric( $date_font['letterSpacing'][1] ) ) {
				$css->add_property( 'letter-spacing', $date_font['letterSpacing'][1] . ( isset( $date_font['letterSpacingType'] ) && ! empty( $date_font['letterSpacingType'] ) ? $date_font['letterSpacingType'] : 'px' ) );
			}
		}
		$css->stop_media_query();
		// Mobile.
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-wrap' );
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
		// Avatar.
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-avatar' );
		if ( isset( $attributes['avatarGap'] ) && is_numeric( $attributes['avatarGap'] ) ) {
			if ( isset( $attributes['layout'] ) && isset( $attributes['layout'][2] ) && ! empty( $attributes['layout'][2] ) ) {
				if ( 'center' === $attributes['layout'][2] ) {
					$css->add_property( 'margin-bottom', $attributes['avatarGap'] . 'px' );
					$css->add_property( 'margin-left', '0px' );
					$css->add_property( 'margin-right', '0px' );
				} elseif ( 'right' === $attributes['layout'][2] ) {
					$css->add_property( 'margin-left', $attributes['avatarGap'] . 'px' );
					$css->add_property( 'margin-bottom', '0px' );
					$css->add_property( 'margin-right', '0px' );
				} else {
					$css->add_property( 'margin-right', $attributes['avatarGap'] . 'px' );
					$css->add_property( 'margin-bottom', '0px' );
					$css->add_property( 'margin-left', '0px' );
				}
			}
		}
		// Name.
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-content .kb-user-info-name' );
		if ( isset( $attributes['nameFont'] ) && is_array( $attributes['nameFont'] ) && isset( $attributes['nameFont'][0] ) && is_array( $attributes['nameFont'][0] ) ) {
			$name_font = $attributes['nameFont'][0];
			if ( isset( $name_font['size'] ) && isset( $name_font['size'][2] ) && is_numeric( $name_font['size'][2] ) ) {
				$css->add_property( 'font-size', $name_font['size'][2] . ( isset( $name_font['sizeType'] ) && ! empty( $name_font['sizeType'] ) ? $name_font['sizeType'] : 'px' ) );
			}
			if ( isset( $name_font['lineHeight'] ) && isset( $name_font['lineHeight'][2] ) && is_numeric( $name_font['lineHeight'][2] ) ) {
				$css->add_property( 'line-height', $name_font['lineHeight'][2] . ( isset( $name_font['lineType'] ) && ! empty( $name_font['lineType'] ) ? $name_font['lineType'] : 'px' ) );
			}
			if ( isset( $name_font['letterSpacing'] ) && isset( $name_font['letterSpacing'][2] ) && is_numeric( $name_font['letterSpacing'][2] ) ) {
				$css->add_property( 'letter-spacing', $name_font['letterSpacing'][2] . ( isset( $name_font['letterSpacingType'] ) && ! empty( $name_font['letterSpacingType'] ) ? $name_font['letterSpacingType'] : 'px' ) );
			}
		}
		// Date.
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-content .kb-user-info-joined' );
		if ( isset( $attributes['dateFont'] ) && is_array( $attributes['dateFont'] ) && isset( $attributes['dateFont'][0] ) && is_array( $attributes['dateFont'][0] ) ) {
			$date_font = $attributes['dateFont'][0];
			if ( isset( $date_font['size'] ) && isset( $date_font['size'][2] ) && is_numeric( $date_font['size'][2] ) ) {
				$css->add_property( 'font-size', $date_font['size'][2] . ( isset( $date_font['sizeType'] ) && ! empty( $date_font['sizeType'] ) ? $date_font['sizeType'] : 'px' ) );
			}
			if ( isset( $date_font['lineHeight'] ) && isset( $date_font['lineHeight'][2] ) && is_numeric( $date_font['lineHeight'][2] ) ) {
				$css->add_property( 'line-height', $date_font['lineHeight'][2] . ( isset( $date_font['lineType'] ) && ! empty( $date_font['lineType'] ) ? $date_font['lineType'] : 'px' ) );
			}
			if ( isset( $date_font['letterSpacing'] ) && isset( $date_font['letterSpacing'][2] ) && is_numeric( $date_font['letterSpacing'][2] ) ) {
				$css->add_property( 'letter-spacing', $date_font['letterSpacing'][2] . ( isset( $date_font['letterSpacingType'] ) && ! empty( $date_font['letterSpacingType'] ) ? $date_font['letterSpacingType'] : 'px' ) );
			}
		}
		$css->stop_media_query();
		self::$gfonts = $css->fonts_output();
		return $css->css_output();
	}
}
Kadence_Blocks_Pro_User_Info::get_instance();
