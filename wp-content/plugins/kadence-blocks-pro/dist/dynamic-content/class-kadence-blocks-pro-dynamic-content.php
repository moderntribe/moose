<?php
/**
 * Output dynamic content.
 *
 * @since   1.4.0
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Output dynamic content.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Dynamic_Content {
	/**
	 * Instance of this class
	 *
	 * @var null
	 */
	private static $instance = null;

	const POST_GROUP = 'post';

	const ARCHIVE_GROUP = 'archive';

	const AUTHOR_GROUP = 'author';

	const SITE_GROUP = 'site';

	const USER_GROUP = 'user';

	const COMMENTS_GROUP = 'comments';

	const MEDIA_GROUP = 'media';

	const RELATIONSHIP_GROUP = 'relationship';

	const OTHER_GROUP = 'other';

	const TEXT_CATEGORY = 'text';

	const NUMBER_CATEGORY = 'number';

	const IMAGE_CATEGORY = 'image';

	const DATE_CATEGORY = 'date';

	const AUDIO_CATEGORY = 'audio';

	const VIDEO_CATEGORY = 'video';

	const URL_CATEGORY = 'url';

	const HTML_CATEGORY = 'html';

	const EMBED_CATEGORY = 'embed';

	const VALUE_SEPARATOR = '#+*#';

	const CUSTOM_POST_TYPE_REGEXP = '/"(custom_post_type\|[^\|]+\|\d+)"/';

	const SHORTCODE = 'kb-dynamic';

	/**
	 * Block ids to render inline.
	 *
	 * @var array
	 */
	public static $render_inline = array();

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
	}
	/**
	 * On init
	 */
	public function on_init() {
		if ( is_admin() ) {
			add_action( 'enqueue_block_editor_assets', array( $this, 'script_enqueue' ), 30 );
			//add_action( 'admin_init', array( $this, 'script_enqueue' ), 30 );
			// This will break blocks :(
			//add_action( 'admin_init', array( $this, 'rest_filter' ), 10 );
		}

		add_shortcode( self::SHORTCODE, array( $this, 'dynamic_shortcode_render' ) );
		add_filter( 'render_block', array( $this, 'render_blocks' ), 12, 3 );
		//add_action( 'wp_enqueue_scripts', array( $this, 'frontend_head_css' ), 5 );
		add_filter( 'kadence_blocks_column_render_block_attributes', array( $this, 'update_background_image' ) );
		add_filter( 'kadence_blocks_rowlayout_render_block_attributes', array( $this, 'update_background_image' ) );
		add_filter( 'kadence_blocks_infobox_render_block_attributes', array( $this, 'update_image_properties' ) );
		add_filter( 'kadence_blocks_image_overlay_render_block_attributes', array( $this, 'update_image_properties' ) );
		add_filter( 'kadence_blocks_split_content_render_block_attributes', array( $this, 'update_image_properties' ) );
		add_filter( 'kadence_blocks_video_render_block_attributes', array( $this, 'update_custom_ratio_video_popup_image' ) );
		add_filter( 'kadence_blocks_render_head_css', array( $this, 'prevent_render_in_head_for_query_blocks' ), 10, 3 );
		add_filter( 'kadence_blocks_force_render_inline_css_in_content', array( $this, 'prevent_css_enqueuing_blocks_in_query' ), 10, 3 );
		add_filter( 'kadence_blocks_build_render_unique_id', array( $this, 'update_unique_id_for_blocks_in_query' ), 10, 3 );
	}
	/**
	 * Outputs extra css for blocks.
	 *
	 * @param $post_object object of WP_Post.
	 */
	public function frontend_build_exclude_array( $post_object ) {
		if ( ! is_object( $post_object ) ) {
			return;
		}
		if ( ! method_exists( $post_object, 'post_content' ) ) {
			$blocks = $this->kadence_parse_blocks( $post_object->post_content );
			if ( ! is_array( $blocks ) || empty( $blocks ) ) {
				return;
			}
			foreach ( $blocks as $indexkey => $block ) {
				$block = apply_filters( 'kadence_blocks_frontend_build_css', $block );
				if ( ! is_object( $block ) && is_array( $block ) && isset( $block['blockName'] ) ) {
					if ( 'core/query' === $block['blockName'] ) {
						if ( isset( $block['innerBlocks'] ) && ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
							$this->blocks_cycle_through_query( $block['innerBlocks'] );
						}
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
	public function blocks_cycle_through_query( $inner_blocks ) {
		foreach ( $inner_blocks as $in_indexkey => $inner_block ) {
			if ( ! is_object( $inner_block ) && is_array( $inner_block ) && isset( $inner_block['blockName'] ) ) {
				$trigger_blocks = array( 'kadence/videopopup', 'kadence/rowlayout', 'kadence/column', 'kadence/infobox', 'kadence/modal', 'kadence/show-more', 'kadence/imageoverlay', 'kadence/splitcontent' );
				if ( in_array( $inner_block['blockName'], $trigger_blocks ) ) {
					if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) && ! empty( $inner_block['attrs']['uniqueID'] ) ) {
						self::$render_inline[] = $inner_block['attrs']['uniqueID'];
					}
				}
				if ( isset( $inner_block['innerBlocks'] ) && ! empty( $inner_block['innerBlocks'] ) && is_array( $inner_block['innerBlocks'] ) ) {
					$this->blocks_cycle_through_query( $inner_block['innerBlocks'] );
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
	public function frontend_head_css() {
		if ( function_exists( 'has_blocks' ) && has_blocks( get_the_ID() ) ) {
			global $post;
			if ( ! is_object( $post ) ) {
				return;
			}
			$this->frontend_build_exclude_array( $post );
		}
	}
	/**
	 * Prevent rendering CSS in header for some blocks.
	 */
	public function prevent_render_in_head_for_query_blocks( $bool, $name, $attributes ) {
		if ( isset( $attributes['inQueryBlock'] ) && $attributes['inQueryBlock'] && ( ( isset( $attributes['kadenceDynamic'] ) && is_array( $attributes['kadenceDynamic'] ) ) || 'modal' == $name || ( isset( $attributes['inQueryBlock'] ) && $attributes['inQueryBlock'] && 'show-more' == $name ) ) ) {
			self::$render_inline[] = $attributes['uniqueID'];
			return false;
		}
		return $bool;
	}
	/**
	 * Prevent enqueuing CSS for some blocks.
	 */
	public function prevent_css_enqueuing_blocks_in_query( $bool, $name, $unique_id ) {
		if ( ! empty( $unique_id ) && in_array( $unique_id, self::$render_inline ) ) {
			// return true so that the css is loaded inline instead of as print.
			return true;
		}
		return $bool;
	}
	/**
	 * Prevent enqueuing CSS for some blocks.
	 */
	public function update_unique_id_for_blocks_in_query( $unique_id, $name, $attributes ) {
		if ( ! empty( $unique_id ) && in_array( $unique_id, self::$render_inline ) ) {
			$unique_id = $attributes['uniqueID'] . get_the_ID();
		}
		return $unique_id;
	}
	/**
	 * Add filter for admin rest calls.
	 */
	public function rest_filter() {
		$args = array(
			'public'       => true,
			'show_in_rest' => true,
		);
		$post_types = get_post_types( $args, 'names' );
		foreach ( $post_types as $post_type ) {
			add_filter( 'rest_prepare_' . $post_type, array( $this, 'update_dynamic_content_on_rest_call' ), 5, 3 );
		}
	}
	/**
	 * Add the dynamic content to blocks.
	 *
	 * @param string $attributes the block attributes.
	 */
	public function update_dynamic_content_on_rest_call( $response, $post, $request ) {
		if ( isset( $response->data ) && is_array( $response->data ) && $response->data['content'] && is_array( $response->data['content'] ) && $response->data['content']['raw'] ) {
			$response->data['content']['raw'] = preg_replace_callback(
				'/<span\s+((?:data-[\w\-]+=["\']+.*["\']+[\s]+)+)class=["\'].*kb-inline-dynamic.*["\']\s*>(.*)<\/span>/U',
				function ( $matches ) {
					$options = explode( ' ', str_replace( 'data-', '', $matches[1] ) );
					$args = array();
					foreach ( $options as $key => $value ) {
						if ( empty( $value ) ) {
							continue;
						}
						$data_split = explode( '=', $value, 2 );
						if ( $data_split[0] === 'field' ) {
							$field_split = explode( '|',  str_replace( '"', '', $data_split[1] ), 2 );
							$args['group'] = ( isset( $field_split[0] ) && ! empty( $field_split[0] ) ? $field_split[0] : 'post' );
							$args['field'] = ( isset( $field_split[1] ) && ! empty( $field_split[1] ) ? $field_split[1] : '' );
						} else {
							$args[ $data_split[0] ] = str_replace( '"', '', $data_split[1] );
						}
					}
					$update = $this->get_content( $args, $post );
					if ( empty ( $update ) ) {
						$update = ( isset( $matches[2] ) && ! empty( $matches[2] ) ? $matches[2] : __( 'No Content' ) );
					}
					return '<span ' . $matches[1] . ' class="kb-inline-dynamic">' . $update . '</span>';
				},
				$response->data['content']['raw']
			);
		}
		return $response;
	}
	/**
	 * This is a special hack for video popup.
	 *
	 * @param string $attributes the block attributes.
	 */
	public function update_custom_ratio_video_popup_image( $attributes ) {
		if ( is_admin() ) {
			return $attributes;
		}
		if ( isset( $attributes ) && isset( $attributes['kadenceDynamic'] ) && is_array( $attributes['kadenceDynamic'] ) ) {
			foreach ( $attributes['kadenceDynamic'] as $attr_slug => $data ) {
				if ( 'background:0:img' !== $attr_slug ) {
					continue;
				}
				if ( isset( $data['enable'] ) && $data['enable'] ) {
					if ( ! empty( $attributes['ratio'] ) && 'custom' === $attributes['ratio'] ) {
						if ( ! empty( $data['field'] ) && strpos( $data['field'], '|' ) !== false ) {
							$field_split = explode( '|', $data['field'], 2 );
							$group = ( isset( $field_split[0] ) && ! empty( $field_split[0] ) ? $field_split[0] : 'post' );
							$field = ( isset( $field_split[1] ) && ! empty( $field_split[1] ) ? $field_split[1] : '' );
						} else {
							$field = '';
							$group = '';
						}
						$args = array(
							'source'       => $data['source'],
							'origin'       => 'core',
							'group'        => $group,
							'type'         => 'image',
							'field'        => $field,
							'custom'       => $data['custom'],
							'para'         => $data['para'],
							'force-string' => false,
							'before'       => $data['before'],
							'after'        => $data['after'],
							'fallback'     => $data['fallback'],
							'relate'       => ( isset( $data['relate'] ) ? $data['relate'] : '' ),
							'relcustom'    => ( isset( $data['relcustom'] ) ? $data['relcustom'] : '' ),
						);
						$image_data = $this->get_content( $args );
						if ( $image_data && is_array( $image_data ) ) {
							if ( ! empty( $attr_slug ) && strpos( $attr_slug, ':' ) !== false ) {
								$slug_split = explode( ':', $attr_slug, 3 );
								if ( isset( $attributes[ $slug_split[0] ] ) && is_array( $attributes[ $slug_split[0] ] ) ) {
									$attributes[ $slug_split[0] ][ absint( $slug_split[1] ) ][ $slug_split[2] ] = $image_data[0];
									$attributes[ $slug_split[0] ][ absint( $slug_split[1] ) ]['imgWidth'] = $image_data[1];
									$attributes[ $slug_split[0] ][ absint( $slug_split[1] ) ]['imageHeight'] = $image_data[2];
								}
							}
						}
					}
				}
			}
		}
		return $attributes;
	}
	/**
	 * Add the dynamic content to blocks.
	 *
	 * @param string $attributes the block attributes.
	 */
	public function update_image_properties( $attributes ) {
		if ( is_admin() ) {
			return $attributes;
		}
		if ( isset( $attributes ) && isset( $attributes['kadenceDynamic'] ) && is_array( $attributes['kadenceDynamic'] ) ) {
			foreach ( $attributes['kadenceDynamic'] as $attr_slug => $data ) {
				if ( 'mediaImage:0:url' !== $attr_slug && 'imgURL' !== $attr_slug && 'mediaUrl' !== $attr_slug ) {
					continue;
				}
				if ( isset( $data['enable'] ) && $data['enable'] ) {
					if ( ! empty( $data['field'] ) && strpos( $data['field'], '|' ) !== false ) {
						$field_split = explode( '|', $data['field'], 2 );
						$group = ( isset( $field_split[0] ) && ! empty( $field_split[0] ) ? $field_split[0] : 'post' );
						$field = ( isset( $field_split[1] ) && ! empty( $field_split[1] ) ? $field_split[1] : '' );
					} else {
						$field = '';
						$group = '';
					}
					$args = array(
						'source'       => $data['source'],
						'origin'       => 'core',
						'group'        => $group,
						'type'         => 'image',
						'field'        => $field,
						'custom'       => $data['custom'],
						'para'         => $data['para'],
						'force-string' => false,
						'before'       => $data['before'],
						'after'        => $data['after'],
						'fallback'     => $data['fallback'],
						'relate'       => ( isset( $data['relate'] ) ? $data['relate'] : '' ),
						'relcustom'    => ( isset( $data['relcustom'] ) ? $data['relcustom'] : '' ),
					);
					$image_data = $this->get_content( $args );
					if ( $image_data && is_array( $image_data ) ) {
						if ( ! empty( $attr_slug ) && strpos( $attr_slug, ':' ) !== false ) {
							$slug_split = explode( ':', $attr_slug, 3 );
							if ( isset( $attributes[ $slug_split[0] ] ) && is_array( $attributes[ $slug_split[0] ] ) ) {
								$attributes[ $slug_split[0] ][ absint( $slug_split[1] ) ][ $slug_split[2] ] = $image_data[0];
								$attributes[ $slug_split[0] ][ absint( $slug_split[1] ) ]['width'] = $image_data[1];
								$attributes[ $slug_split[0] ][ absint( $slug_split[1] ) ]['height'] = $image_data[2];
							}
						} else if ( ! empty( $attr_slug ) && strpos( $attr_slug, 'media' ) !== false ) {
							$attributes[$attr_slug] = $image_data[0];
							$attributes['mediaWidth'] = $image_data[1];
							$attributes['mediaHeight'] = $image_data[2];
						} else {
							$attributes[$attr_slug] = $image_data[0];
							$attributes['imgWidth'] = $image_data[1];
							$attributes['imgHeight'] = $image_data[2];
						}
					}
				}
			}
		}
		return $attributes;
	}
	/**
	 * Add the dynamic content to blocks.
	 *
	 * @param string $attributes the block attributes.
	 */
	public function update_background_image( $attributes ) {
		if ( is_admin() ) {
			return $attributes;
		}
		if ( isset( $attributes ) && isset( $attributes['kadenceDynamic'] ) && is_array( $attributes['kadenceDynamic'] ) ) {
			foreach ( $attributes['kadenceDynamic'] as $attr_slug => $data ) {
				if ( isset( $data['enable'] ) && $data['enable'] ) {
					if ( ! empty( $data['field'] ) && strpos( $data['field'], '|' ) !== false ) {
						$field_split = explode( '|', $data['field'], 2 );
						$group = ( isset( $field_split[0] ) && ! empty( $field_split[0] ) ? $field_split[0] : 'post' );
						$field = ( isset( $field_split[1] ) && ! empty( $field_split[1] ) ? $field_split[1] : '' );
					} else {
						$field = '';
						$group = '';
					}
					$args = array(
						'source'       => $data['source'],
						'origin'       => 'core',
						'group'        => $group,
						'type'         => 'background',
						'field'        => $field,
						'custom'       => $data['custom'],
						'para'         => $data['para'],
						'force-string' => false,
						'before'       => $data['before'],
						'after'        => null,
						'fallback'     => $data['fallback'],
						'relate'       => ( isset( $data['relate'] ) ? $data['relate'] : '' ),
						'relcustom'    => ( isset( $data['relcustom'] ) ? $data['relcustom'] : '' ),
					);
					$image_url = $this->get_content( $args );
					if ( is_array( $image_url ) ) {
						if ( isset( $image_url['url'] ) ) {
							$image_url = $image_url['url'];
						} else if ( isset( $image_url[0] ) ) {
							$image_url = $image_url[0];
						} else {
							$image_url = '';
						}
					}
					if ( ! empty( $attr_slug ) && strpos( $attr_slug, ':' ) !== false ) {
						$slug_split = explode( ':', $attr_slug, 3 );
						$attributes[ $slug_split[0] ][ absint( $slug_split[1] ) ][ $slug_split[2] ] = $image_url;
					} else {
						$attributes[$attr_slug] = $image_url;
					}
				}
			}
		}
		return $attributes;
	}

	/**
	 * Add the dynamic content to blocks.
	 *
	 * @param string $block_content The block content.
	 * @param array  $block The block info.
	 * @param object $wp_block The block class object.
	 */
	public function render_blocks( $block_content, $block, $wp_block ) {
		if ( is_admin() ) {
			return $block_content;
		}
		global $kb_media_context;
		if ( ! empty( $block['attrs']['kadenceConditional']['postData'] ) && isset( $block['attrs']['kadenceConditional']['postData']['enable'] ) && $block['attrs']['kadenceConditional']['postData']['enable'] ) {
			$conditional_data = $block['attrs']['kadenceConditional']['postData'];
			$hide = true;
			if ( ! empty( $conditional_data['field'] ) && strpos( $conditional_data['field'], '|' ) !== false ) {
				$field_split = explode( '|', $conditional_data['field'], 2 );
				$group = ( isset( $field_split[0] ) && ! empty( $field_split[0] ) ? $field_split[0] : 'post' );
				$field = ( isset( $field_split[1] ) && ! empty( $field_split[1] ) ? $field_split[1] : '' );
			} else {
				$field = '';
				$group = '';
			}
			$args = array(
				'source'       => $conditional_data['source'],
				'origin'       => 'core',
				'group'        => $group,
				'type'         => 'conditional',
				'field'        => $field,
				'custom'       => $conditional_data['custom'],
				'para'         => $conditional_data['para'],
				'force-string' => true,
			);
			$condition_data = $this->get_content( $args );
			switch ( $conditional_data['compare'] ) {
				case 'not_empty':
					if ( ! empty( $condition_data ) ) {
						$hide = false;
					}
					break;
				case 'is_empty':
					if ( empty( $condition_data ) ) {
						$hide = false;
					}
					break;
				case 'is_true':
					if ( $condition_data == true ) {
						$hide = false;
					}
					break;
				case 'is_false':
					if ( $condition_data == false ) {
						$hide = false;
					}
					break;
				case 'equals':
					if ( $condition_data == $conditional_data['condition'] ) {
						$hide = false;
					}
					break;
				case 'not_equals':
					if ( $condition_data != $conditional_data['condition'] ) {
						$hide = false;
					}
					break;
				case 'equals_or_greater':
					if ( $condition_data >= $conditional_data['condition'] ) {
						$hide = false;
					}
					break;
				case 'equals_or_less':
					if ( $condition_data <= $conditional_data['condition'] ) {
						$hide = false;
					}
					break;
				case 'greater':
					if ( $condition_data > $conditional_data['condition'] ) {
						$hide = false;
					}
					break;
				case 'less':
					if ( $condition_data < $conditional_data['condition'] ) {
						$hide = false;
					}
					break;
			}
			if ( $hide ) {
				return '';
			}
		}
		if ( 'kadence/rowlayout' === $block['blockName'] ) {
			if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
				$blockattr = $block['attrs'];
				if ( isset( $blockattr['inQueryBlock'] ) && $blockattr['inQueryBlock'] && isset( $blockattr['kadenceDynamic'] ) && is_array( $blockattr['kadenceDynamic'] ) ) {
					$block_content = str_replace( 'kt-layout-id' . $blockattr['uniqueID'], 'kt-layout-id' . $blockattr['uniqueID'] . get_the_ID(), $block_content );
					// 3.0 support.
					$block_content = str_replace( 'kb-row-layout-id' . $blockattr['uniqueID'], 'kb-row-layout-id' . $blockattr['uniqueID'] . get_the_ID(), $block_content );
				}
			}
		} else if ( 'kadence/column' === $block['blockName'] ) {
			if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
				$blockattr = $block['attrs'];
				if ( isset( $blockattr['inQueryBlock'] ) && $blockattr['inQueryBlock'] && isset( $blockattr['kadenceDynamic'] ) && is_array( $blockattr['kadenceDynamic'] ) ) {
					$block_content = str_replace( 'kadence-column' . $blockattr['uniqueID'], 'kadence-column' . $blockattr['uniqueID'] . get_the_ID(), $block_content );
				}
			}
		} else if ( 'kadence/show-more' === $block['blockName'] ) {
			if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
				$blockattr = $block['attrs'];
				if ( isset( $blockattr['inQueryBlock'] ) && $blockattr['inQueryBlock'] ) {
					$block_content = str_replace( 'kb-block-show-more-container' . $blockattr['uniqueID'], 'kb-block-show-more-container' . $blockattr['uniqueID'] . get_the_ID(), $block_content );
				}
			}
		} else if ( 'kadence/modal' === $block['blockName'] ) {
			if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
				$blockattr = $block['attrs'];
				if ( isset( $blockattr['inQueryBlock'] ) && $blockattr['inQueryBlock'] ) {
					$block_content = str_replace( 'kt-modal' . $blockattr['uniqueID'], 'kt-modal' . $blockattr['uniqueID'] . get_the_ID(), $block_content );
					$block_content = str_replace( 'kt-target-modal' . $blockattr['uniqueID'], 'kt-target-modal' . $blockattr['uniqueID'] . get_the_ID(), $block_content );
				}
			}
		} elseif ( 'kadence/imageoverlay' === $block['blockName'] ) {
			if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
				$blockattr = $block['attrs'];
				if ( isset( $blockattr['inQueryBlock'] ) && $blockattr['inQueryBlock'] && isset( $blockattr['kadenceDynamic'] ) && is_array( $blockattr['kadenceDynamic'] ) ) {
					$block_content = str_replace( 'kt-img-overlay' . $blockattr['uniqueID'], 'kt-img-overlay' . $blockattr['uniqueID'] . get_the_ID(), $block_content );
				}
				if ( isset( $blockattr['kadenceDynamic'] ) && is_array( $blockattr['kadenceDynamic'] ) && isset( $blockattr['kadenceDynamic']['imgURL'] ) && is_array( $blockattr['kadenceDynamic']['imgURL'] ) && isset( $blockattr['kadenceDynamic']['imgURL']['enable'] ) && $blockattr['kadenceDynamic']['imgURL']['enable'] ) {
					$block_content = preg_replace_callback(
						'/<img.*?class=["\'].*kt-img-overlay.*["\']\/>/U',
						function ( $matches ) use ( $blockattr ) {
							$content = '';
							if ( ! empty( $blockattr['kadenceDynamic']['imgURL']['field'] ) && strpos( $blockattr['kadenceDynamic']['imgURL']['field'], '|' ) !== false ) {
								$field_split = explode( '|', $blockattr['kadenceDynamic']['imgURL']['field'], 2 );
								$group = ( isset( $field_split[0] ) && ! empty( $field_split[0] ) ? $field_split[0] : 'post' );
								$field = ( isset( $field_split[1] ) && ! empty( $field_split[1] ) ? $field_split[1] : '' );
							} else {
								$field = '';
								$group = '';
							}
							$args = array(
								'source'       => $blockattr['kadenceDynamic']['imgURL']['source'],
								'origin'       => 'core',
								'group'        => $group,
								'type'         => 'image',
								'field'        => $field,
								'custom'       => $blockattr['kadenceDynamic']['imgURL']['custom'],
								'para'         => $blockattr['kadenceDynamic']['imgURL']['para'],
								'force-string' => false,
								'before'       => $blockattr['kadenceDynamic']['imgURL']['before'],
								'after'        => null,
								'fallback'     => $blockattr['kadenceDynamic']['imgURL']['fallback'],
								'relate'       => ( isset( $blockattr['kadenceDynamic']['imgURL']['relate'] ) ? $blockattr['kadenceDynamic']['imgURL']['relate'] : '' ),
								'relcustom'    => ( isset( $blockattr['kadenceDynamic']['imgURL']['relcustom'] ) ? $blockattr['kadenceDynamic']['imgURL']['relcustom'] : '' ),
							);
							$update = $this->get_content( $args );
							if ( $update ) {
								$content = '<img src="' . $update[0] . '" alt="" width="' . $update[1] . '" height="' . $update[2] . '" class="kt-img-overlay">';
							}
							return $content;
						},
						$block_content
					);
				}
			}
		} elseif ( 'kadence/image' === $block['blockName'] ) {
			if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
				$blockattr = $block['attrs'];
				if ( isset( $blockattr['kadenceDynamic'] ) && is_array( $blockattr['kadenceDynamic'] ) && isset( $blockattr['kadenceDynamic']['url'] ) && is_array( $blockattr['kadenceDynamic']['url'] ) && isset( $blockattr['kadenceDynamic']['url']['enable'] ) && $blockattr['kadenceDynamic']['url']['enable'] ) {
					$regx = '/<img.*?class=["\'].*kb-img.*["\'].*\/>/U';
					$block_content = preg_replace_callback(
						$regx,
						function ( $matches ) use ( $blockattr ) {
							$content = '';
							if ( ! empty( $blockattr['kadenceDynamic']['url']['field'] ) && strpos( $blockattr['kadenceDynamic']['url']['field'], '|' ) !== false ) {
								$field_split = explode( '|', $blockattr['kadenceDynamic']['url']['field'], 2 );
								$group = ( isset( $field_split[0] ) && ! empty( $field_split[0] ) ? $field_split[0] : 'post' );
								$field = ( isset( $field_split[1] ) && ! empty( $field_split[1] ) ? $field_split[1] : '' );
							} else {
								$field = '';
								$group = '';
							}
							$args = array(
								'source'       => $blockattr['kadenceDynamic']['url']['source'],
								'origin'       => 'core',
								'group'        => $group,
								'type'         => 'image',
								'field'        => $field,
								'custom'       => $blockattr['kadenceDynamic']['url']['custom'],
								'para'         => $blockattr['kadenceDynamic']['url']['para'],
								'force-string' => false,
								'before'       => $blockattr['kadenceDynamic']['url']['before'],
								'after'        => null,
								'fallback'     => $blockattr['kadenceDynamic']['url']['fallback'],
								'relate'       => ( isset( $blockattr['kadenceDynamic']['url']['relate'] ) ? $blockattr['kadenceDynamic']['url']['relate'] : '' ),
								'relcustom'    => ( isset( $blockattr['kadenceDynamic']['url']['relcustom'] ) ? $blockattr['kadenceDynamic']['url']['relcustom'] : '' ),
							);
							$update = $this->get_content( $args );
							if ( ! empty( $update[5] ) ) {
								global $kb_media_context;
								$kb_media_context = $update[5];
							}
							if ( $update ) {
								$content = '<img src="' . esc_attr( $update[0] ) . '" alt="' . esc_attr( ! empty( $update[4] ) ? $update[4] : '' ) . '" width="' . esc_attr( $update[1] ) . '" height="' . esc_attr( $update[2] ) . '" class="kb-img wp-image-' . esc_attr( ! empty( $update[5] ) ? $update[5] : '' ) . '">';
							}
							return $content;
						},
						$block_content
					);
				}
			}
		} elseif ( 'kadence/advancedgallery' === $block['blockName'] ) {
			if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
				$blockattr = $block['attrs'];
				if ( isset( $blockattr['kadenceDynamic'] ) && is_array( $blockattr['kadenceDynamic'] ) && isset( $blockattr['kadenceDynamic']['images'] ) && is_array( $blockattr['kadenceDynamic']['images'] ) && isset( $blockattr['kadenceDynamic']['images']['enable'] ) && $blockattr['kadenceDynamic']['images']['enable'] ) {
					$styles = '';
					if ( preg_match( '/<style id="kb-advancedgallery' . $blockattr['uniqueID'] . '">(.*?)<\/style>/', $block_content, $match ) == 1 ) {
						$styles = '<style>' . $match[1] . '</style>';
					} elseif ( preg_match( '/<style>(.*?)<\/style>/', $block_content, $match ) == 1 ) {
						$styles = '<style>' . $match[1] . '</style>';
					}
					$content = '';
					if ( ! empty( $blockattr['kadenceDynamic']['images']['field'] ) && strpos( $blockattr['kadenceDynamic']['images']['field'], '|' ) !== false ) {
						$field_split = explode( '|', $blockattr['kadenceDynamic']['images']['field'], 2 );
						$group = ( isset( $field_split[0] ) && ! empty( $field_split[0] ) ? $field_split[0] : 'post' );
						$field = ( isset( $field_split[1] ) && ! empty( $field_split[1] ) ? $field_split[1] : '' );
					} else {
						$field = '';
						$group = '';
					}
					$args = array(
						'source'       => $blockattr['kadenceDynamic']['images']['source'],
						'origin'       => 'core',
						'group'        => $group,
						'type'         => 'gallery',
						'field'        => $field,
						'custom'       => $blockattr['kadenceDynamic']['images']['custom'],
						'para'         => $blockattr['kadenceDynamic']['images']['para'],
						'force-string' => false,
						'before'       => $blockattr['kadenceDynamic']['images']['before'],
						'after'        => null,
						'fallback'     => false,
						'relate'       => ( isset( $blockattr['kadenceDynamic']['images']['relate'] ) ? $blockattr['kadenceDynamic']['images']['relate'] : '' ),
						'relcustom'    => ( isset( $blockattr['kadenceDynamic']['images']['relcustom'] ) ? $blockattr['kadenceDynamic']['images']['relcustom'] : '' ),
					);
					$update = $this->get_content( $args );
					if ( $update ) {
						$unique_id    = ( ! empty( $blockattr['uniqueID'] ) ? $blockattr['uniqueID'] : 'dynamic' );
						$type         = ( ! empty( $blockattr['type'] ) ? $blockattr['type'] : 'masonry' );
						$image_filter = ( ! empty( $blockattr['imageFilter'] ) ? $blockattr['imageFilter'] : 'none' );
						$dot_style    = ( ! empty( $blockattr['dotStyle'] ) ? $blockattr['dotStyle'] : 'dark' );
						$arrow_style  = ( ! empty( $blockattr['arrowStyle'] ) ? $blockattr['arrowStyle'] : 'dark' );
						$link_to      = ( ! empty( $blockattr['linkTo'] ) ? $blockattr['linkTo'] : 'none' );
						$lightbox     = ( ! empty( $blockattr['lightbox'] ) ? $blockattr['lightbox'] : 'none' );
						$lightbox_cap = ( isset( $blockattr['lightboxCaption'] ) && ! $blockattr['lightboxCaption'] ? false : true );
						$autoplay     = ( ! empty( $blockattr['autoPlay'] ) && $blockattr['autoPlay'] ? true : false );
						$trans_speed  = ( ! empty( $blockattr['transSpeed'] ) ? $blockattr['transSpeed'] : 400 );
						$auto_speed   = ( ! empty( $blockattr['autoSpeed'] ) ? $blockattr['autoSpeed'] : 7000 );
						$slides_sc    = ( ! empty( $blockattr['slidesScroll'] ) ? $blockattr['slidesScroll'] : '1' );
						$columns_xxl  = ( ! empty( $blockattr['columns'][0] ) ? $blockattr['columns'][0] : '3' );
						$columns_xl   = ( ! empty( $blockattr['columns'][1] ) ? $blockattr['columns'][1] : '3' );
						$columns_md   = ( ! empty( $blockattr['columns'][2] ) ? $blockattr['columns'][2] : '3' );
						$columns_sm   = ( ! empty( $blockattr['columns'][3] ) ? $blockattr['columns'][3] : '2' );
						$columns_xs   = ( ! empty( $blockattr['columns'][4] ) ? $blockattr['columns'][4] : '1' );
						$columns_ss   = ( ! empty( $blockattr['columns'][5] ) ? $blockattr['columns'][5] : '1' );
						$tcolumns_xxl = ( ! empty( $blockattr['thumbnailColumns'][0] ) ? $blockattr['thumbnailColumns'][0] : '4' );
						$tcolumns_xl  = ( ! empty( $blockattr['thumbnailColumns'][1] ) ? $blockattr['thumbnailColumns'][1] : '4' );
						$tcolumns_md  = ( ! empty( $blockattr['thumbnailColumns'][2] ) ? $blockattr['thumbnailColumns'][2] : '4' );
						$tcolumns_sm  = ( ! empty( $blockattr['thumbnailColumns'][3] ) ? $blockattr['thumbnailColumns'][3] : '4' );
						$tcolumns_xs  = ( ! empty( $blockattr['thumbnailColumns'][4] ) ? $blockattr['thumbnailColumns'][4] : '4' );
						$tcolumns_ss  = ( ! empty( $blockattr['thumbnailColumns'][5] ) ? $blockattr['thumbnailColumns'][5] : '4' );
						$car_align    = ( isset( $blockattr['carouselAlign'] ) && false === $blockattr['carouselAlign'] ? false : true );
						$gap          = ( isset( $blockattr['gutter'][0] ) && is_numeric( $blockattr['gutter'][0] ) ? $blockattr['gutter'][0] : '10' );
						$tablet_gap   = ( isset( $blockattr['gutter'][1] ) && is_numeric( $blockattr['gutter'][1] ) ? $blockattr['gutter'][1] : $gap );
						$mobile_gap   = ( isset( $blockattr['gutter'][2] ) && is_numeric( $blockattr['gutter'][2] ) ? $blockattr['gutter'][2] : $tablet_gap );
						$gap_unit     = ( ! empty( $blockattr['gutterUnit'] ) ? $blockattr['gutterUnit'] : 'px' );

						// Gallery Class.
						$gallery_classes = array( 'kb-gallery-ul' );
						$gallery_classes[] = 'kb-gallery-type-' . esc_attr( $type );
						if ( 'masonry' === $type ) {
							$gallery_classes[] = 'kb-masonry-init';
						}
						if ( isset( $blockattr['mobileForceHover'] ) && true === $blockattr['mobileForceHover'] ) {
							$gallery_classes[] = 'kb-mobile-force-hover';
						}
						$gallery_classes[] = 'kb-gallery-id-' . esc_attr( $unique_id );
						$gallery_classes[] = 'kb-gallery-caption-style-' . ( ! empty( $blockattr['captionStyle'] ) ? esc_attr( $blockattr['captionStyle'] ) : 'bottom-hover' );
						$gallery_classes[] = 'kb-gallery-filter-' . ( ! empty( $blockattr['imageFilter'] ) ? esc_attr( $blockattr['imageFilter'] ) : 'none' );
						if ( 'media' === $link_to && 'magnific' === $lightbox ) {
							$gallery_classes[] = 'kb-gallery-magnific-init';
						}
						$content = '<div class="wp-block-kadence-advancedgallery kb-gallery-wrap-id-' . esc_attr( $unique_id ) . '">';
						switch ( $type ) {
							case 'carousel':
								$content .= '<div class="' . esc_attr( implode( ' ', $gallery_classes ) ) . '" data-image-filter="' . esc_attr( $image_filter ) . '" data-lightbox-caption="' . ( $lightbox_cap ? 'true' : 'false' ) . '">';
								$content .= '<div class="kt-blocks-carousel kt-carousel-container-dotstyle-' . esc_attr( $dot_style ) . '">';
								$content .= '<div class="kt-blocks-carousel-init kb-gallery-carousel kt-carousel-arrowstyle-' . esc_attr( $arrow_style ) . ' kt-carousel-dotstyle-' . esc_attr( $dot_style ) . '" data-columns-xxl="' . esc_attr( $columns_xxl ) . '" data-columns-xl="' . esc_attr( $columns_xl ) . '" data-columns-md="' . esc_attr( $columns_md ) . '" data-columns-sm="' . esc_attr( $columns_sm ) . '" data-columns-xs="' . esc_attr( $columns_xs ) . '" data-columns-ss="' . esc_attr( $columns_ss ) . '" data-slider-anim-speed="' . esc_attr( $trans_speed ) . '" data-slider-scroll="' . esc_attr( $slides_sc ) . '" data-slider-arrows="' . esc_attr( 'none' === $arrow_style ? 'false' : 'true' ) . '" data-slider-dots="' . esc_attr( 'none' === $dot_style ? 'false' : 'true' ) . '" data-slider-hover-pause="false" data-slider-auto="' . esc_attr( $autoplay ) . '" data-slider-speed="' . esc_attr( $auto_speed ) . '" data-slider-gap="' . esc_attr( $gap . $gap_unit ) . '" data-slider-gap-tablet="' . esc_attr( $tablet_gap . $gap_unit ) . '" data-slider-gap-mobile="' . esc_attr( $mobile_gap . $gap_unit ) . '">';
								foreach ( $update as $key => $image ) {
									$content .= '<div class="kb-slide-item kb-gallery-carousel-item">';
									$content .= $this->render_gallery_images( $image, $blockattr );
									$content .= '</div>';
								}
								$content .= '</div>';
								$content .= '</div>';
								$content .= '</div>';
								break;
							case 'fluidcarousel':
								$content .= '<div class="' . esc_attr( implode( ' ', $gallery_classes ) ) . '" data-image-filter="' . esc_attr( $image_filter ) . '" data-lightbox-caption="' . ( $lightbox_cap ? 'true' : 'false' ) . '">';
								$content .= '<div class="kt-blocks-carousel kt-carousel-container-dotstyle-' . esc_attr( $dot_style ) . '">';
								$content .= '<div class="kt-blocks-carousel-init kb-blocks-fluid-carousel kt-carousel-arrowstyle-' . esc_attr( $arrow_style ) . ' kt-carousel-dotstyle-' . esc_attr( $dot_style ) . ( $car_align ? '' : ' kb-carousel-mode-align-left' ) . '" data-slider-anim-speed="' . esc_attr( $trans_speed ) . '" data-slider-scroll="1" data-slider-arrows="' . esc_attr( 'none' === $arrow_style ? 'false' : 'true' ) . '" data-slider-dots="' . esc_attr( 'none' === $dot_style ? 'false' : 'true' ) . '" data-slider-hover-pause="false" data-slider-auto="' . esc_attr( $autoplay ) . '" data-slider-speed="' . esc_attr( $auto_speed ) . '" data-slider-type="fluidcarousel" data-slider-center-mode="' . esc_attr( ( $car_align ? 'true' : 'false' ) ) . '" data-slider-gap="' . esc_attr( $gap . $gap_unit ) . '" data-slider-gap-tablet="' . esc_attr( $tablet_gap . $gap_unit ) . '" data-slider-gap-mobile="' . esc_attr( $mobile_gap . $gap_unit ) . '">';
								foreach ( $update as $key => $image ) {
									$content .= '<div class="kb-slide-item kb-gallery-carousel-item">';
									$content .= $this->render_gallery_images( $image, $blockattr );
									$content .= '</div>';
								}
								$content .= '</div>';
								$content .= '</div>';
								$content .= '</div>';
								break;
							case 'slider':
								$content .= '<div class="' . esc_attr( implode( ' ', $gallery_classes ) ) . '" data-image-filter="' . esc_attr( $image_filter ) . '" data-lightbox-caption="' . ( $lightbox_cap ? 'true' : 'false' ) . '">';
								$content .= '<div class="kt-blocks-carousel kt-carousel-container-dotstyle-' . esc_attr( $dot_style ) . '">';
								$content .= '<div class="kt-blocks-carousel-init kb-blocks-slider kt-carousel-arrowstyle-' . esc_attr( $arrow_style ) . ' kt-carousel-dotstyle-' . esc_attr( $dot_style ) . '" data-slider-anim-speed="' . esc_attr( $trans_speed ) . '" data-slider-scroll="1" data-slider-arrows="' . esc_attr( 'none' === $arrow_style ? 'false' : 'true' ) . '" data-slider-dots="' . esc_attr( 'none' === $dot_style ? 'false' : 'true' ) . '"data-slider-type="slider"  data-slider-hover-pause="false" data-slider-auto="' . esc_attr( $autoplay ) . '" data-slider-speed="' . esc_attr( $auto_speed ) . '">';
								foreach ( $update as $key => $image ) {
									$content .= '<div class="kb-slide-item kb-gallery-slide-item">';
									$content .= $this->render_gallery_images( $image, $blockattr );
									$content .= '</div>';
								}
								$content .= '</div>';
								$content .= '</div>';
								$content .= '</div>';
								break;
							case 'thumbslider':
								$content .= '<div class="' . esc_attr( implode( ' ', $gallery_classes ) ) . '" data-image-filter="' . esc_attr( $image_filter ) . '" data-lightbox-caption="' . ( $lightbox_cap ? 'true' : 'false' ) . '">';
								$content .= '<div class="kt-blocks-carousel kt-carousel-container-dotstyle-' . esc_attr( $dot_style ) . '">';
								$content .= '<div id="kb-slider-' . esc_attr( $unique_id ) . '" class="kt-blocks-carousel-init kb-blocks-slider kt-carousel-arrowstyle-' . esc_attr( $arrow_style ) . ' kt-carousel-dotstyle-' . esc_attr( $dot_style ) . '" data-columns-xxl="' . esc_attr( $tcolumns_xxl ) . '" data-columns-xl="' . esc_attr( $tcolumns_xl ) . '" data-columns-md="' . esc_attr( $tcolumns_md ) . '" data-columns-sm="' . esc_attr( $tcolumns_sm ) . '" data-columns-xs="' . esc_attr( $tcolumns_xs ) . '" data-columns-ss="' . esc_attr( $tcolumns_ss ) . '" data-slider-anim-speed="' . esc_attr( $trans_speed ) . '" data-slider-scroll="1" data-slider-arrows="' . esc_attr( 'none' === $arrow_style ? 'false' : 'true' ) . '" data-slider-dots="' . esc_attr( 'none' === $dot_style ? 'false' : 'true' ) . '" data-slider-hover-pause="false" data-slider-type="thumbnail" data-slider-nav="kb-thumb-slider-' . esc_attr( $unique_id ) . '"  data-slider-auto="' . esc_attr( $autoplay ) . '" data-slider-speed="' . esc_attr( $auto_speed ) . '" data-slider-gap="' . esc_attr( $gap . $gap_unit ) . '" data-slider-gap-tablet="' . esc_attr( $tablet_gap . $gap_unit ) . '" data-slider-gap-mobile="' . esc_attr( $mobile_gap . $gap_unit ) . '">';
								foreach ( $update as $key => $image ) {
									$content .= '<div class="kb-slide-item kb-gallery-carousel-item">';
									$content .= $this->render_gallery_images( $image, $blockattr );
									$content .= '</div>';
								}
								$content .= '</div>';
								$content .= '<div id="kb-thumb-slider-' . esc_attr( $unique_id ) . '" class="kb-blocks-slider kt-carousel-arrowstyle-' . esc_attr( $arrow_style ) . ' kt-carousel-dotstyle-' . esc_attr( $dot_style ) . '" data-slider-anim-speed="' . esc_attr( $trans_speed ) . '" data-slider-scroll="1" data-slider-arrows="' . esc_attr( 'none' === $arrow_style ? 'false' : 'true' ) . '" data-slider-dots="' . esc_attr( 'none' === $dot_style ? 'false' : 'true' ) . '" data-slider-hover-pause="false" data-slider-auto="' . esc_attr( $autoplay ) . '" data-slider-speed="' . esc_attr( $auto_speed ) . '" data-slider-type="thumbnail" data-slider-nav="kb-slider-' . esc_attr( $unique_id ) . '">';
								foreach ( $update as $key => $image ) {
									$content .= '<div class="kb-slide-item kb-gallery-carousel-item">';
									$content .= $this->render_gallery_thumb_images( $image, $blockattr );
									$content .= '</div>';
								}
								$content .= '</div>';
								$content .= '</div>';
								$content .= '</div>';
								break;
							case 'tiles':
								$content .= '<ul class="' . esc_attr( implode( ' ', $gallery_classes ) ) . '" data-image-filter="' . esc_attr( $image_filter ) . '" data-lightbox-caption="' . ( $lightbox_cap ? 'true' : 'false' ) . '">';
								foreach ( $update as $key => $image ) {
									$content .= $this->render_gallery_images( $image, $blockattr );
								}
								$content .= '</ul>';
								break;
							default:
								$content .= '<ul class="' . esc_attr( implode( ' ', $gallery_classes ) ) . '" data-image-filter="' . esc_attr( $image_filter ) . '" data-item-selector=".kadence-blocks-gallery-item" data-lightbox-caption="' . ( $lightbox_cap ? 'true' : 'false' ) . '" data-columns-xxl="' . esc_attr( $columns_xxl ) . '" data-columns-xl="' . esc_attr( $columns_xl ) . '" data-columns-md="' . esc_attr( $columns_md ) . '" data-columns-sm="' . esc_attr( $columns_sm ) . '" data-columns-xs="' . esc_attr( $columns_xs ) . '" data-columns-ss="' . esc_attr( $columns_ss ) . '">';
								foreach ( $update as $key => $image ) {
									$content .= $this->render_gallery_images( $image, $blockattr );
								}
								$content .= '</ul>';
								break;
						}
						$content .= '</div>';
					}
					$block_content = $content . $styles;
				}
			}
		} elseif ( 'kadence/videopopup' === $block['blockName'] ) {
			if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
				$blockattr = $block['attrs'];
				if ( isset( $blockattr['inQueryBlock'] ) && $blockattr['inQueryBlock'] && isset( $blockattr['kadenceDynamic'] ) && is_array( $blockattr['kadenceDynamic'] ) ) {
					$block_content = str_replace( 'kadence-video-popup' . $blockattr['uniqueID'], 'kadence-video-popup' . $blockattr['uniqueID'] . get_the_ID(), $block_content );
				}
				if ( isset( $blockattr['kadenceDynamic'] ) && is_array( $blockattr['kadenceDynamic'] ) && isset( $blockattr['kadenceDynamic']['background:0:img'] ) && is_array( $blockattr['kadenceDynamic']['background:0:img'] ) && isset( $blockattr['kadenceDynamic']['background:0:img']['enable'] ) && $blockattr['kadenceDynamic']['background:0:img']['enable'] ) {
					$block_content = preg_replace_callback(
						'/<img.*?class=["\'].*kadence-video-poster.*["\']\/>/U',
						function ( $matches ) use ( $blockattr ) {
							$content = '';
							if ( ! empty( $blockattr['kadenceDynamic']['background:0:img']['field'] ) && strpos( $blockattr['kadenceDynamic']['background:0:img']['field'], '|' ) !== false ) {
								$field_split = explode( '|', $blockattr['kadenceDynamic']['background:0:img']['field'], 2 );
								$group = ( isset( $field_split[0] ) && ! empty( $field_split[0] ) ? $field_split[0] : 'post' );
								$field = ( isset( $field_split[1] ) && ! empty( $field_split[1] ) ? $field_split[1] : '' );
							} else {
								$field = '';
								$group = '';
							}
							$args = array(
								'source'       => $blockattr['kadenceDynamic']['background:0:img']['source'],
								'origin'       => 'core',
								'group'        => $group,
								'type'         => 'image',
								'field'        => $field,
								'custom'       => $blockattr['kadenceDynamic']['background:0:img']['custom'],
								'para'         => $blockattr['kadenceDynamic']['background:0:img']['para'],
								'force-string' => false,
								'before'       => $blockattr['kadenceDynamic']['background:0:img']['before'],
								'after'        => null,
								'fallback'     => $blockattr['kadenceDynamic']['background:0:img']['fallback'],
								'relate'       => ( isset( $blockattr['kadenceDynamic']['background:0:img']['relate'] ) ? $blockattr['kadenceDynamic']['background:0:img']['relate'] : '' ),
								'relcustom'    => ( isset( $blockattr['kadenceDynamic']['background:0:img']['relcustom'] ) ? $blockattr['kadenceDynamic']['background:0:img']['relcustom'] : '' ),
							);
							$update = $this->get_content( $args );
							if ( $update ) {
								$content = '<img src="' . $update[0] . '" alt="" width="' . $update[1] . '" height="' . $update[2] . '" class="kadence-video-poster">';
							}
							return $content;
						},
						$block_content
					);
				}
			}
		} elseif ( 'kadence/splitcontent' === $block['blockName'] ) {
			if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
				$blockattr = $block['attrs'];
				if ( isset( $blockattr['kadenceDynamic'] ) && is_array( $blockattr['kadenceDynamic'] ) && isset( $blockattr['kadenceDynamic']['mediaUrl'] ) && is_array( $blockattr['kadenceDynamic']['mediaUrl'] ) && isset( $blockattr['kadenceDynamic']['mediaUrl']['enable'] ) && $blockattr['kadenceDynamic']['mediaUrl']['enable'] ) {
					$block_content = preg_replace_callback(
						'/<img.*?class=["\'].*kt-split-content-img.*["\']\/>/U',
						function ( $matches ) use ( $blockattr ) {
							$content = '';
							if ( ! empty( $blockattr['kadenceDynamic']['mediaUrl']['field'] ) && strpos( $blockattr['kadenceDynamic']['mediaUrl']['field'], '|' ) !== false ) {
								$field_split = explode( '|', $blockattr['kadenceDynamic']['mediaUrl']['field'], 2 );
								$group = ( isset( $field_split[0] ) && ! empty( $field_split[0] ) ? $field_split[0] : 'post' );
								$field = ( isset( $field_split[1] ) && ! empty( $field_split[1] ) ? $field_split[1] : '' );
							} else {
								$field = '';
								$group = '';
							}
							$args = array(
								'source'       => $blockattr['kadenceDynamic']['mediaUrl']['source'],
								'origin'       => 'core',
								'group'        => $group,
								'type'         => 'image',
								'field'        => $field,
								'custom'       => $blockattr['kadenceDynamic']['mediaUrl']['custom'],
								'para'         => $blockattr['kadenceDynamic']['mediaUrl']['para'],
								'force-string' => false,
								'before'       => $blockattr['kadenceDynamic']['mediaUrl']['before'],
								'after'        => null,
								'fallback'     => $blockattr['kadenceDynamic']['mediaUrl']['fallback'],
								'relate'       => ( isset( $blockattr['kadenceDynamic']['mediaUrl']['relate'] ) ? $blockattr['kadenceDynamic']['mediaUrl']['relate'] : '' ),
								'relcustom'    => ( isset( $blockattr['kadenceDynamic']['mediaUrl']['relcustom'] ) ? $blockattr['kadenceDynamic']['mediaUrl']['relcustom'] : '' ),
							);
							$update = $this->get_content( $args );
							if ( $update ) {
								$content = '<img src="' . $update[0] . '" alt="" width="' . $update[1] . '" height="' . $update[2] . '" class="kt-split-content-img">';
							}
							return $content;
						},
						$block_content
					);
				}
			}
		} elseif ( 'kadence/infobox' === $block['blockName'] ) {
			if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
				$blockattr = $block['attrs'];
				if ( isset( $blockattr['inQueryBlock'] ) && $blockattr['inQueryBlock'] && isset( $blockattr['kadenceDynamic'] ) && is_array( $blockattr['kadenceDynamic'] ) ) {
					$block_content = str_replace( 'kt-info-box' . $blockattr['uniqueID'], 'kt-info-box' . $blockattr['uniqueID'] . get_the_ID(), $block_content );
				}
				if ( isset( $blockattr['kadenceDynamic'] ) && is_array( $blockattr['kadenceDynamic'] ) && isset( $blockattr['kadenceDynamic']['mediaImage:0:url'] ) && is_array( $blockattr['kadenceDynamic']['mediaImage:0:url'] ) && isset( $blockattr['kadenceDynamic']['mediaImage:0:url']['enable'] ) && $blockattr['kadenceDynamic']['mediaImage:0:url']['enable'] ) {
					if ( isset( $blockattr['inQueryBlock'] ) && $blockattr['inQueryBlock'] ) {
						$block_content = str_replace( 'kt-info-box' . $blockattr['uniqueID'], 'kt-info-box' . $blockattr['uniqueID'] . get_the_ID(), $block_content );
					}
					$block_content = preg_replace_callback(
						'/<img.*?class=["\'].*kt-info-box-image.*["\']\/>/U',
						function ( $matches ) use ( $blockattr ) {
							$content = '';
							if ( ! empty( $blockattr['kadenceDynamic']['mediaImage:0:url']['field'] ) && strpos( $blockattr['kadenceDynamic']['mediaImage:0:url']['field'], '|' ) !== false ) {
								$field_split = explode( '|', $blockattr['kadenceDynamic']['mediaImage:0:url']['field'], 2 );
								$group = ( isset( $field_split[0] ) && ! empty( $field_split[0] ) ? $field_split[0] : 'post' );
								$field = ( isset( $field_split[1] ) && ! empty( $field_split[1] ) ? $field_split[1] : '' );
							} else {
								$field = '';
								$group = '';
							}
							$args = array(
								'source'       => $blockattr['kadenceDynamic']['mediaImage:0:url']['source'],
								'origin'       => 'core',
								'group'        => $group,
								'type'         => 'image',
								'field'        => $field,
								'custom'       => $blockattr['kadenceDynamic']['mediaImage:0:url']['custom'],
								'para'         => $blockattr['kadenceDynamic']['mediaImage:0:url']['para'],
								'force-string' => false,
								'before'       => $blockattr['kadenceDynamic']['mediaImage:0:url']['before'],
								'after'        => null,
								'fallback'     => $blockattr['kadenceDynamic']['mediaImage:0:url']['fallback'],
								'relate'       => ( isset( $blockattr['kadenceDynamic']['mediaImage:0:url']['relate'] ) ? $blockattr['kadenceDynamic']['mediaImage:0:url']['relate'] : '' ),
								'relcustom'    => ( isset( $blockattr['kadenceDynamic']['mediaImage:0:url']['relcustom'] ) ? $blockattr['kadenceDynamic']['mediaImage:0:url']['relcustom'] : '' ),
							);
							$update = $this->get_content( $args );
							if ( $update ) {
								$content = '<img src="' . $update[0] . '" alt="" width="' . $update[1] . '" height="' . $update[2] . '" class="kt-info-box-image wp-image-offsite">';
							}
							return $content;
						},
						$block_content
					);
				}
			}
		}
		// We need to render shortcodes for blocks in query block to get the correct info.
		if ( ! empty( $block['attrs']['kadenceDynamic'] ) ) {
			foreach ( $block['attrs']['kadenceDynamic'] as $dynamic_key => $dynamic_setting ) {
				if ( ! empty( $dynamic_key ) && strpos( $dynamic_key, ':' ) !== false ) {
					$slug_split = explode( ':', $dynamic_key, 3 );
					if ( isset( $slug_split[2] ) ) {
						$slug_key = $slug_split[2];
					} else {
						$slug_key = '';
					}
				} else {
					$slug_key = $dynamic_key;
				}
				if ( 'link' !== $slug_key && 'url' !== $slug_key ) {
					continue;
				}
				if ( isset( $block['attrs']['inQueryBlock'] ) && $block['attrs']['inQueryBlock'] ) {
					$block_content = do_shortcode( $block_content );
				}
			}
		}
		if ( ! empty( $block['attrs']['kadenceAnimation'] ) ) {
			if ( wp_script_is( 'kadence-aos', 'registered' ) && ! wp_script_is( 'kadence-aos', 'enqueued' ) ) {
				wp_enqueue_script( 'kadence-aos' );
			}
			if ( wp_style_is( 'kadence-blocks-pro-aos', 'registered' ) && ! wp_style_is( 'kadence-blocks-pro-aos', 'enqueued' ) ) {
				wp_enqueue_style( 'kadence-blocks-pro-aos' );
			}
		}
		if ( ! empty( $block_content ) ) {
			$replaced_block_content = preg_replace_callback(
				'/<span\s+((?:data-[\w\-]+=["\']+.*["\']+[\s]+)+)class=["\'].*kb-inline-dynamic.*["\']\s*>(.*)<\/span>/U',
				function ( $matches ) {
					if ( empty( $matches[1] ) ) {
						return '';
					}
					$options = explode( '" ', str_replace( 'data-', '', $matches[1] ) );
					$args = array( 'force-string' => true );
					foreach ( $options as $key => $value ) {
						if ( empty( $value ) ) {
							continue;
						}
						$data_split = explode( '=', $value, 2 );
						if ( $data_split[0] === 'field' ) {
							$field_split = explode( '|',  str_replace( '"', '', $data_split[1] ), 2 );
							$args['group'] = ( isset( $field_split[0] ) && ! empty( $field_split[0] ) ? $field_split[0] : 'post' );
							$args['field'] = ( isset( $field_split[1] ) && ! empty( $field_split[1] ) ? $field_split[1] : '' );
						} else {
							$args[ $data_split[0] ] = str_replace( '"', '', $data_split[1] );
						}
					}
					$update = $this->get_content( $args );
					return $update;
				},
				$block_content
			);
			// if the regex errored out, don't replace the $block_content.
			$block_content = is_null( $replaced_block_content ) ? $block_content : $replaced_block_content;
		}
		$kb_media_context = '';
		return $block_content;
	}
	/**
	 * Output Gallery image markeup.
	 */
	public function render_gallery_thumb_images( $image, $attributes ) {
		$type          = ( ! empty( $attributes['type'] ) ? $attributes['type'] : 'masonry' );
		$image_ratio   = ( ! empty( $attributes['thumbnailRatio'] ) ? $attributes['thumbnailRatio'] : 'land32' );
		$image_id = ( ! empty( $image['ID'] ) ? $image['ID'] : '' );
		if ( empty( $image_id ) ) {
			$image_id = ( ! empty( $image['id'] ) ? $image['id'] : '' );
		}
		$image_src = ( ! empty( $image['url'] ) ? $image['url'] : '' );
		$image_alt = ( ! empty( $image['alt'] ) ? $image['alt'] : get_post_meta( $image_id, '_wp_attachment_image_alt', true ) );
		$image_full = ( ! empty( $image['fullUrl'] ) ? $image['fullUrl'] : $image['url'] );
		$image_contain_classes = array( 'kb-gallery-image-contain kadence-blocks-gallery-intrinsic' );
		if ( ! empty( $image_ratio ) ) {
			$image_contain_classes[] = 'kb-gallery-image-ratio-' . $image_ratio;
		}
		$fig_classes = array( 'kb-gallery-figure' );
		if ( ! empty( $image_ratio ) ) {
			$image_contain_classes[] = 'kb-has-image-ratio-' . $image_ratio;
		}
		$padding_bottom = '';
		$img = '<div class="' . esc_attr( implode( ' ', $image_contain_classes ) ) . '" ' . ( ! empty( $padding_bottom ) ? 'style="padding-bottom:' . $padding_bottom . '%;"' : '' ) . '><img src="' . esc_attr( $image_src ) . '" ' . ( ! empty( $image['width'] ) ? 'width="' . $image['width'] . '"' : '' ) . ' ' . ( ! empty( $image['height'] ) ? 'height="' . $image['height'] . '"' : '' ) . ' alt="' . esc_attr( $image_alt ) . '" data-full-image="' . esc_attr( $image_full ) . '" data-light-image="' . esc_attr( $image_full ) . '" data-id="' . esc_attr( $image_id ) . '" class="wp-image-' . esc_attr( $image_id ) . '"/></div>';
		$output = '<div class="kadence-blocks-gallery-thumb-item">';
		$output .= '<div class="kadence-blocks-gallery-thumb-item-inner">';
		$output .= '<figure class="' . esc_attr( implode( ' ', $fig_classes ) ) . '">';
		$output .= '<div class="kb-gal-image-radius" ' . ( ! empty( $padding_bottom ) ? 'style="max-width:' . $image['width'] . 'px;"' : '' ) . '>';
		$output .= $img;
		$output .= '</div>';
		$output .= '</figure>';
		$output .= '</div>';
		$output .= '</div>';
		return $output;
	}
	/**
	 * Output Gallery image markeup.
	 */
	public function render_gallery_images( $image, $attributes ) {
		$type          = ( ! empty( $attributes['type'] ) ? $attributes['type'] : 'masonry' );
		$image_ratio   = ( ! empty( $attributes['imageRatio'] ) ? $attributes['imageRatio'] : 'land32' );
		$show_caption  = ( ! empty( $attributes['showCaption'] ) && $attributes['showCaption'] ? true : false );
		$caption_style = ( ! empty( $attributes['captionStyle'] ) ? $attributes['captionStyle'] : 'bottom-hover' );
		$link_to       = ( ! empty( $attributes['linkTo'] ) ? $attributes['linkTo'] : 'none' );
		$lazy_load     = ( ! empty( $attributes['lazyLoad'] ) && $attributes['lazyLoad'] ? true : false );
		$link_target   = apply_filters( 'kadence_blocks_pro_dynamic_gallery_link_target', '', $image, $attributes );
		$lightbox      = ( ! empty( $attributes['lightbox'] ) ? $attributes['lightbox'] : 'none' );
		$lightbox_cap  = ( isset( $attributes['lightboxCaption'] ) && ! $attributes['lightboxCaption'] ? false : true );
		$caption       = ( ! empty( $image['caption'] ) ? $image['caption'] : '' );
		$href = '';
		$image_id = ( ! empty( $image['ID'] ) ? $image['ID'] : '' );
		if ( empty( $image_id ) ) {
			$image_id = ( ! empty( $image['id'] ) ? $image['id'] : '' );
		}
		if ( empty( $caption ) && ! empty( $image_id ) ) {
			$caption_source = wp_get_attachment_caption( $image_id );
			if ( $caption_source ) {
				$caption = $caption_source;
			}
		}
		$caption       = apply_filters( 'kadence_blocks_pro_dynamic_gallery_caption', $caption, $image, $attributes );
		$image_src  = ( ! empty( $image['url'] ) ? $image['url'] : '' );
		$image_full = ( ! empty( $image['fullUrl'] ) ? $image['fullUrl'] : $image['url'] );
		$image_alt  = ( ! empty( $image['alt'] ) ? $image['alt'] : get_post_meta( $image_id, '_wp_attachment_image_alt', true ) );
		switch ( $link_to ) {
			case 'media':
				$href = ( ! empty( $image_full ) ? $image_full : '' );
				break;
			case 'custom':
				if ( ! empty( $image_id ) && isset( $attributes['kadenceDynamic'] ) && is_array( $attributes['kadenceDynamic'] ) && isset( $attributes['kadenceDynamic']['link'] ) && is_array( $attributes['kadenceDynamic']['link'] ) && isset( $attributes['kadenceDynamic']['link']['enable'] ) && $attributes['kadenceDynamic']['link']['enable'] ) {
					if ( ! empty( $attributes['kadenceDynamic']['link']['field'] ) && strpos( $attributes['kadenceDynamic']['link']['field'], '|' ) !== false ) {
						$field_split = explode( '|', $attributes['kadenceDynamic']['link']['field'], 2 );
						$group = ( isset( $field_split[0] ) && ! empty( $field_split[0] ) ? $field_split[0] : 'post' );
						$field = ( isset( $field_split[1] ) && ! empty( $field_split[1] ) ? $field_split[1] : '' );
					} else {
						$field = '';
						$group = '';
					}
					$args = array(
						'source'       => $image_id,
						'origin'       => 'core',
						'group'        => $group,
						'type'         => 'link',
						'field'        => $field,
						'custom'       => $attributes['kadenceDynamic']['link']['custom'],
						'para'         => $attributes['kadenceDynamic']['link']['para'],
						'force-string' => false,
						'before'       => $attributes['kadenceDynamic']['link']['before'],
						'after'        => null,
						'fallback'     => false,
						'relate'       => ( isset( $blockattr['kadenceDynamic']['link']['relate'] ) ? $blockattr['kadenceDynamic']['link']['relate'] : '' ),
						'relcustom'    => ( isset( $blockattr['kadenceDynamic']['link']['relcustom'] ) ? $blockattr['kadenceDynamic']['link']['relcustom'] : '' ),
					);
					$href = $this->get_content( $args );
				}
				break;
			case 'attachment':
				if ( ! empty( $image_id ) ) {
					$href = get_permalink( $image_id );
				}
				break;
		}
		$rel_attr = '';
		if ( 'custom' === $link_to && '_blank' === $link_target ) {
			$rel_attr = 'noopener noreferrer';
		}
		if ( 'media' === $link_to && 'new_tab' === $lightbox ) {
			$rel_attr = 'noopener noreferrer';
		}
		if ( isset( $image['linkSponsored'] ) && true == $image['linkSponsored'] ) {
			$rel_attr .= ( ! empty( $rel_attr ) ? ' sponsored' : 'sponsored' );
		}
		$image_contain_classes = array( 'kb-gallery-image-contain' );
		if ( ( ( 'grid' === $type || 'carousel' === $type || 'slider' === $type || 'thumbslider' === $type ) && ! empty( $image_ratio ) ) || ( 'fluidcarousel' !== $type && 'tiles' !== $type && ! empty( $image['width'] ) && ! empty( $image['height'] ) ) ) {
			$image_contain_classes[] = 'kadence-blocks-gallery-intrinsic';
		}
		if ( ! empty( $image_ratio ) && ( 'grid' === $type || 'carousel' === $type || 'slider' === $type || 'thumbslider' === $type ) ) {
			$image_contain_classes[] = 'kb-gallery-image-ratio-' . $image_ratio;
		}
		$fig_classes = array( 'kb-gallery-figure' );
		if ( ! empty( $href ) ) {
			$fig_classes[] = 'kb-gallery-item-has-link';
		}
		if ( $show_caption ) {
			if ( ! empty( $caption ) ) {
				$fig_classes[] = 'kadence-blocks-gallery-item-has-caption';
			}
		} else {
			$fig_classes[] = 'kadence-blocks-gallery-item-hide-caption';
		}
		if ( ! empty( $image_ratio ) && ( 'grid' === $type || 'carousel' === $type || 'slider' === $type || 'thumbslider' === $type ) ) {
			$image_contain_classes[] = 'kb-has-image-ratio-' . $image_ratio;
		}
		$image_classes = array( 'wp-image-' . $image_id );
		if ( 'carousel' === $type || 'slider' === $type || 'thumbslider' === $type || 'fluidcarousel' === $type ) {
			$image_classes[] = 'skip-lazy';
		}
		$item_tag = ( ( 'carousel' === $type || 'slider' === $type || 'thumbslider' === $type || 'fluidcarousel' === $type ) ? 'div' : 'li' );
		$fig_tag = ( empty( $href ) && 'below' === $caption_style ? 'figcaption' : 'div' );
		$figcap = '<' . $fig_tag . ' class="kadence-blocks-gallery-item__caption">' . ( ! empty( $caption ) ? $caption : '' ) . '</' . $fig_tag . '>';
		$padding_bottom = '';
		if ( ( 'masonry' === $type ) && ! empty( $image['width'] ) && ! empty( $image['height'] ) ) {
			$padding_bottom = floor( ( $image['height'] / $image['width'] ) * 100 );
		} else if ( ! empty( $image_ratio ) && 'inherit' === $image_ratio && 'grid' === $type && ! empty( $image['width'] ) && ! empty( $image['height'] ) ) {
			$padding_bottom = floor( ( $image['height'] / $image['width'] ) * 100 );
		}
		if ( $lazy_load && ( 'carousel' === $type || 'slider' === $type || 'thumbslider' === $type || 'fluidcarousel' === $type ) ) {
			$img = '<div class="' . esc_attr( implode( ' ', $image_contain_classes ) ) . '" ' . ( ! empty( $padding_bottom ) ? 'style="padding-bottom:' . $padding_bottom . '%;"' : '' ) . '><img src="' . "data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%201000%20667'%3E%3C/svg%3E" . '"  data-splide-lazy="' . esc_attr( $image_src ) . '" ' . ( ! empty( $image['width'] ) ? 'width="' . $image['width'] . '"' : '' ) . ' ' . ( ! empty( $image['height'] ) ? 'height="' . $image['height'] . '"' : '' ) . ' alt="' . esc_attr( $image_alt ) . '" data-full-image="' . esc_attr( $image_full ) . '" data-light-image="' . esc_attr( $image_full ) . '" ' . $this->get_image_srcset_output( $image_id, $image_src, $image['width'], $image['height'] ) . 'data-id="' . esc_attr( $image_id ) . '" class="' . esc_attr( implode( ' ', $image_classes ) ) . '"/></div>';
		} else {
			$img = '<div class="' . esc_attr( implode( ' ', $image_contain_classes ) ) . '" ' . ( ! empty( $padding_bottom ) ? 'style="padding-bottom:' . $padding_bottom . '%;"' : '' ) . '><img src="' . esc_attr( $image_src ) . '" ' . ( ! empty( $image['width'] ) ? 'width="' . $image['width'] . '"' : '' ) . ' ' . ( ! empty( $image['height'] ) ? 'height="' . $image['height'] . '"' : '' ) . ' alt="' . esc_attr( $image_alt ) . '" data-full-image="' . esc_attr( $image_full ) . '" data-light-image="' . esc_attr( $image_full ) . '" data-id="' . esc_attr( $image_id ) . '" class="' . esc_attr( implode( ' ', $image_classes ) ) . '"/></div>';
		}
		$output = '<' . $item_tag . ' class="kadence-blocks-gallery-item">';
		$output .= '<div class="kadence-blocks-gallery-item-inner">';
		$output .= '<figure class="' . esc_attr( implode( ' ', $fig_classes ) ) . '">';
		if ( ! empty( $href ) ) {
			$output .= '<a href="' . esc_url( $href ) . '"' . ( $link_to === 'media' && $lightbox === 'magnific' && $lightbox_cap && ! empty( $caption ) && is_string( $caption ) ? ' data-description="' . esc_attr( $caption ) . '"' : '' ) . '' . ( $link_to === 'media' && $lightbox === 'magnific' && ! empty( $image_alt ) && is_string( $image_alt ) ? ' data-alt="' . esc_attr( $image_alt ) . '"' : '' ) . ' class="kb-gallery-item-link" ' . ( ( $link_to === 'custom' && '_blank' === $link_target ) || ( $link_to === 'media' && $lightbox === 'new_tab' ) ? 'target="_blank"' : '' ) . ' ' . ( ( $link_to === 'custom' && ! empty( $rel_attr ) ) || ( $link_to === 'media' && ! empty( $rel_attr ) ) ? 'rel="' . esc_attr( $rel_attr ) . '"' : '' ) . '>';
		}
		$output .= '<div class="kb-gal-image-radius" ' . ( ! empty( $padding_bottom ) ? 'style="max-width:' . $image['width'] . 'px;"' : '' ) . '>';
		$output .= $img;
		if ( $show_caption && ! empty( $caption ) && 'below' !== $caption_style ) {
			$output .= $figcap;
		}
		$output .= '</div>';
		if ( $show_caption && ! empty( $caption ) && 'below' === $caption_style ) {
			$output .= $figcap;
		}
		if ( ! empty( $href ) ) {
			$output .= '</a>';
		}
		$output .= '</figure>';
		$output .= '</div>';
		$output .= '</' . $item_tag . '>';
		return $output;
	}
	/**
	 * Get the image srcset output.
	 *
	 * @param integer $id the image ID.
	 * @param string  $url the image url.
	 * @param string  $width the image width.
	 * @param string  $height the image height.
	 */
	public function get_image_srcset_output( $id = null, $url = null, $width = null, $height = null ) {
		$img_srcset = $this->get_image_srcset( $id, $url, $width, $height );
		if ( ! empty( $img_srcset ) ) {
			$output = 'data-splide-lazy-srcset="' . esc_attr( $img_srcset ) . '" sizes="(max-width: ' . esc_attr( $width ) . 'px) 100vw, ' . esc_attr( $width ) . 'px"';
		} else {
			$output = '';
		}
		return $output;
	}
	/**
	 * Get the image srcset.
	 *
	 * @param integer $id the image ID.
	 * @param string  $url the image url.
	 * @param string  $width the image width.
	 * @param string  $height the image height.
	 */
	public function get_image_srcset( $id = null, $url = null, $width = null, $height = null ) {
		if ( empty( $id ) || empty( $url ) || empty( $width ) || empty( $height ) ) {
			return '';
		}
		$image_meta = wp_get_attachment_metadata( $id );
		if ( ! $image_meta ) {
			return '';
		}
		if ( function_exists( 'wp_calculate_image_srcset' ) ) {
			$output = wp_calculate_image_srcset( array( $width, $height ), $url, $image_meta, $id );
		} else {
			$output = '';
		}

		return $output;
	}
	/**
	 * Enqueue Script for Meta options
	 */
	public function script_enqueue() {
		wp_localize_script(
			'kadence-blocks-pro-js',
			'kadenceDynamicParams',
			array(
				'textFields' => $this->get_text_fields(),
				'linkFields' => $this->get_link_fields(),
				'urlFields' => $this->get_url_fields(),
				'backgroundFields' => $this->get_background_fields(),
				'imageFields' => $this->get_image_fields(),
				'galleryFields' => $this->get_gallery_fields(),
				'listFields' => $this->get_list_fields(),
				'htmlFields' => $this->get_html_fields(),
				'inputFields' => $this->get_input_fields(),
				'conditionalFields' => $this->get_conditional_fields(),
				'imageSizes' => $this->get_image_sizes(),
				'dynamicRenderEndpoint' => '/kbp-dynamic/v1/render',
				'dynamicLinkLabelEndpoint' => '/kbp-dynamic/v1/link_label',
				'dynamicBackgroundEndpoint' => '/kbp-dynamic/v1/image_render',
				'dynamicImageEndpoint' => '/kbp-dynamic/v1/image_data',
				'dynamicGalleryEndpoint' => '/kbp-dynamic/v1/gallery_data',
				'dynamicFieldsEndpoint' => '/kbp-dynamic/v1/custom_fields',
				'dynamicListEndpoint' => '/kbp-dynamic/v1/list_data',
				'dynamicHTMLEndpoint' => '/kbp-dynamic/v1/html_data',
				'dynamicImageFallback'  => apply_filters( 'kadence_blocks_pro_dynamic_image_no_content', KBP_URL . 'dist/assets/images/no-image-found.jpg' ),
				'dynamicGalleryFallback'  => apply_filters( 'kadence_blocks_pro_dynamic_gallery_no_content', array( array( 'url' => KBP_URL . 'dist/assets/images/no-image-found.jpg' ) ) ),
			)
		);
	}
	/**
	 * Setup the post type taxonomies for post blocks.
	 *
	 * @return array
	 */
	public function get_conditional_taxonomies() {
		$post_types = kadence_blocks_pro_get_post_types();
		$output = array();
		foreach ( $post_types as $key => $post_type ) {
			$taxonomies = get_object_taxonomies( $post_type['value'], 'objects' );
			foreach ( $taxonomies as $term_slug => $term ) {
				if ( ! $term->public || ! $term->show_ui ) {
					continue;
				}
				$terms = get_terms( $term_slug );
				$term_items = array();
				if ( ! empty( $terms ) ) {
					foreach ( $terms as $term_key => $term_item ) {
						$term_items[] = array(
							'value' => $term_slug . '|' . $term_item->term_id,
							'label' => $term_item->name,
						);
					}
				}
				$output[] = array(
					'label' => $term->label,
					'options' => $term_items,
				);
			}
		}
		return apply_filters( 'kadence_blocks_conditional_taxonomies', $output );
	}
	/**
	 * On init
	 */
	public function get_image_sizes() {
		$wp_additional_image_sizes = wp_get_additional_image_sizes();
		$sizes = array(
			array(
				'value' => '',
				'label' => 'Inherit',
			),
			array(
				'value' => 'full',
				'label' => 'full',
			),
		);
		$get_intermediate_image_sizes = get_intermediate_image_sizes();

		// Create the full array with sizes and crop info.
		foreach ( $get_intermediate_image_sizes as $_size ) {
			// Exclude woocommerce deprecated.
			if ( in_array( $_size, array( 'shop_catalog', 'shop_single', 'shop_thumbnail' ) ) ) {
				continue;
			}
			if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
				$sizes[] = array(
					'value' => $_size,
					'label' => $_size,
				);
			} elseif ( isset( $wp_additional_image_sizes[ $_size ] ) ) {
				$height = ( $wp_additional_image_sizes[ $_size ]['height'] === 0 ? 'uncropped' : $wp_additional_image_sizes[ $_size ]['height'] );
				$the_dimensions = $wp_additional_image_sizes[ $_size ]['width'] . 'x' . $height;
				if ( $_size === $the_dimensions ) {
					continue;
				}
				$sizes[] = array(
					'value' => $_size,
					'label' => $_size . ' - ' . $the_dimensions,
				);
			}
		}
		return apply_filters( 'kadence_blocks_conditional_image_sizes', $sizes );
	}
	/**
	 * On init
	 */
	public function get_text_fields() {
		$options = array(
			array(
				'label' => __( 'Post', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::POST_GROUP . '|post_title',
						'label' => esc_attr__( 'Post Title', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_url',
						'label' => esc_attr__( 'Post URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_excerpt',
						'label' => esc_attr__( 'Post Excerpt', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_id',
						'label' => esc_attr__( 'Post ID', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_date',
						'label' => esc_attr__( 'Post Date', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_date_modified',
						'label' => esc_attr__( 'Post Last Modified Date', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_featured_image_url',
						'label' => esc_attr__( 'Featured Image URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_type',
						'label' => esc_attr__( 'Post Type', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_status',
						'label' => esc_attr__( 'Post Status', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_custom_field',
						'label' => esc_attr__( 'Post Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Archive', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::ARCHIVE_GROUP . '|archive_title',
						'label' => esc_attr__( 'Archive Title', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::ARCHIVE_GROUP . '|archive_url',
						'label' => esc_attr__( 'Archive URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::ARCHIVE_GROUP . '|archive_description',
						'label' => esc_attr__( 'Archive Description', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::ARCHIVE_GROUP . '|archive_custom_field',
						'label' => esc_attr__( 'Archive Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Site', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::SITE_GROUP . '|site_title',
						'label' => esc_attr__( 'Site Title', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::SITE_GROUP . '|site_tagline',
						'label' => esc_attr__( 'Site Tagline', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::SITE_GROUP . '|site_url',
						'label' => esc_attr__( 'Site URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::SITE_GROUP . '|page_title',
						'label' => esc_attr__( 'Page Title', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::SITE_GROUP . '|user_info',
						'label' => esc_attr__( 'Current User Display Name', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::SITE_GROUP . '|custom_setting',
						'label' => esc_attr__( 'Site Custom Setting', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Media', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::MEDIA_GROUP . '|media_url',
						'label' => esc_attr__( 'Media URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::MEDIA_GROUP . '|media_title',
						'label' => esc_attr__( 'Media Title', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::MEDIA_GROUP . '|media_caption',
						'label' => esc_attr__( 'Media Caption', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::MEDIA_GROUP . '|media_description',
						'label' => esc_attr__( 'Media Description', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::MEDIA_GROUP . '|media_alt_text',
						'label' => esc_attr__( 'Media Alt Text', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::MEDIA_GROUP . '|media_filename',
						'label' => esc_attr__( 'Media filename', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::MEDIA_GROUP . '|media_post_url',
						'label' => esc_attr__( 'Media Attachment URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::MEDIA_GROUP . '|media_custom_field',
						'label' => esc_attr__( 'Media Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Author', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::AUTHOR_GROUP . '|author_name',
						'label' => esc_attr__( 'Author Display Name', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::AUTHOR_GROUP . '|author_first_name',
						'label' => esc_attr__( 'Author First Name', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::AUTHOR_GROUP . '|author_last_name',
						'label' => esc_attr__( 'Author Last Name', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::AUTHOR_GROUP . '|author_info',
						'label' => esc_attr__( 'Author Bio Info', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::AUTHOR_GROUP . '|author_custom_field',
						'label' => esc_attr__( 'Author Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Meta Relationship', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_title',
						'label' => esc_attr__( 'Post Title', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_url',
						'label' => esc_attr__( 'Post URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_excerpt',
						'label' => esc_attr__( 'Post Excerpt', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_featured_image_url',
						'label' => esc_attr__( 'Featured Image URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_type',
						'label' => esc_attr__( 'Post Type', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_custom_field',
						'label' => esc_attr__( 'Post Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			// array(
			// 	'label' => __( 'Comments', 'kadence-blocks-pro' ),
			// 	'options' => array(
			// 		array(
			// 			'value' => self::COMMENTS_GROUP . '|count',
			// 			'label' => esc_attr__( 'Comments Count', 'kadence-blocks-pro' ),
			// 		),
			// 	),
			// ),
		);
		return apply_filters( 'kadence_block_pro_dynamic_text_fields_options', $options );
	}
	/**
	 * On init
	 */
	public function get_html_fields() {
		$options = array(
			array(
				'label' => __( 'Post', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::POST_GROUP . '|post_title',
						'label' => esc_attr__( 'Post Title', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_content',
						'label' => esc_attr__( 'Post Content', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_excerpt',
						'label' => esc_attr__( 'Post Excerpt', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_date',
						'label' => esc_attr__( 'Post Date', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_date_modified',
						'label' => esc_attr__( 'Post Last Modified Date', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_custom_field',
						'label' => esc_attr__( 'Post Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Archive', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::ARCHIVE_GROUP . '|archive_title',
						'label' => esc_attr__( 'Archive Title', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::ARCHIVE_GROUP . '|archive_description',
						'label' => esc_attr__( 'Archive Description', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::ARCHIVE_GROUP . '|archive_custom_field',
						'label' => esc_attr__( 'Archive Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Site', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::SITE_GROUP . '|site_title',
						'label' => esc_attr__( 'Site Title', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::SITE_GROUP . '|site_tagline',
						'label' => esc_attr__( 'Site Tagline', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::SITE_GROUP . '|site_url',
						'label' => esc_attr__( 'Site URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::SITE_GROUP . '|page_title',
						'label' => esc_attr__( 'Page Title', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::SITE_GROUP . '|user_info',
						'label' => esc_attr__( 'Current User Display Name', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::SITE_GROUP . '|custom_setting',
						'label' => esc_attr__( 'Site Custom Setting', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Author', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::AUTHOR_GROUP . '|author_name',
						'label' => esc_attr__( 'Author Display Name', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::AUTHOR_GROUP . '|author_first_name',
						'label' => esc_attr__( 'Author First Name', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::AUTHOR_GROUP . '|author_last_name',
						'label' => esc_attr__( 'Author Last Name', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::AUTHOR_GROUP . '|author_info',
						'label' => esc_attr__( 'Author Bio Info', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::AUTHOR_GROUP . '|author_custom_field',
						'label' => esc_attr__( 'Author Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Meta Relationship', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_title',
						'label' => esc_attr__( 'Post Title', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_excerpt',
						'label' => esc_attr__( 'Post Content', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_excerpt',
						'label' => esc_attr__( 'Post Excerpt', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_custom_field',
						'label' => esc_attr__( 'Post Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			// array(
			// 	'label' => __( 'Comments', 'kadence-blocks-pro' ),
			// 	'options' => array(
			// 		array(
			// 			'value' => self::COMMENTS_GROUP . '|count',
			// 			'label' => esc_attr__( 'Comments Count', 'kadence-blocks-pro' ),
			// 		),
			// 	),
			// ),
		);
		return apply_filters( 'kadence_block_pro_dynamic_html_fields_options', $options );
	}
	/**
	 * On init
	 */
	public function get_input_fields() {
		$options = array(
			array(
				'label' => __( 'Current User', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::USER_GROUP . '|first_name',
						'label' => esc_attr__( 'First Name', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::USER_GROUP . '|last_name',
						'label' => esc_attr__( 'Last Name', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::USER_GROUP . '|display_name',
						'label' => esc_attr__( 'Display Name', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::USER_GROUP . '|email',
						'label' => esc_attr__( 'Email', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::USER_GROUP . '|bio',
						'label' => esc_attr__( 'User Description', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::USER_GROUP . '|website',
						'label' => esc_attr__( 'Website', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::USER_GROUP . '|username',
						'label' => esc_attr__( 'Username', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::USER_GROUP . '|id',
						'label' => esc_attr__( 'User ID', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::USER_GROUP . '|user_custom_field',
						'label' => esc_attr__( 'User Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Post', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::POST_GROUP . '|post_title',
						'label' => esc_attr__( 'Post Title', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_url',
						'label' => esc_attr__( 'Post URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_excerpt',
						'label' => esc_attr__( 'Post Excerpt', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_id',
						'label' => esc_attr__( 'Post ID', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_date',
						'label' => esc_attr__( 'Post Date', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_date_modified',
						'label' => esc_attr__( 'Post Last Modified Date', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_featured_image_url',
						'label' => esc_attr__( 'Featured Image URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_type',
						'label' => esc_attr__( 'Post Type', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_status',
						'label' => esc_attr__( 'Post Status', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_custom_field',
						'label' => esc_attr__( 'Post Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Site', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::SITE_GROUP . '|site_title',
						'label' => esc_attr__( 'Site Title', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::SITE_GROUP . '|site_tagline',
						'label' => esc_attr__( 'Site Tagline', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::SITE_GROUP . '|site_url',
						'label' => esc_attr__( 'Site URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::SITE_GROUP . '|page_title',
						'label' => esc_attr__( 'Page Title', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::SITE_GROUP . '|user_info',
						'label' => esc_attr__( 'Current User Display Name', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::SITE_GROUP . '|custom_setting',
						'label' => esc_attr__( 'Site Custom Setting', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Meta Relationship', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_title',
						'label' => esc_attr__( 'Post Title', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_url',
						'label' => esc_attr__( 'Post URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_excerpt',
						'label' => esc_attr__( 'Post Excerpt', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_type',
						'label' => esc_attr__( 'Post Type', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_custom_field',
						'label' => esc_attr__( 'Post Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
		);
		return apply_filters( 'kadence_block_pro_dynamic_input_fields_options', $options );
	}
	/**
	 * Get conditional fields.
	 */
	public function get_conditional_fields() {
		$options = array(
			array(
				'label' => __( 'Post', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::POST_GROUP . '|post_custom_field',
						'label' => esc_attr__( 'Post Custom Field', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_title',
						'label' => esc_attr__( 'Post Title', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_url',
						'label' => esc_attr__( 'Post URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_id',
						'label' => esc_attr__( 'Post ID', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_date',
						'label' => esc_attr__( 'Post Date', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_date_modified',
						'label' => esc_attr__( 'Post Last Modified Date', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_featured_image_url',
						'label' => esc_attr__( 'Featured Image URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_type',
						'label' => esc_attr__( 'Post Type', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_status',
						'label' => esc_attr__( 'Post Status', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|has_taxonomy',
						'label' => esc_attr__( 'Post Has Taxonomy', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Archive', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::ARCHIVE_GROUP . '|archive_title',
						'label' => esc_attr__( 'Archive Title', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::ARCHIVE_GROUP . '|archive_url',
						'label' => esc_attr__( 'Archive URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::ARCHIVE_GROUP . '|archive_custom_field',
						'label' => esc_attr__( 'Archive Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Author', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::AUTHOR_GROUP . '|author_name',
						'label' => esc_attr__( 'Author Display Name', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::AUTHOR_GROUP . '|author_first_name',
						'label' => esc_attr__( 'Author First Name', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::AUTHOR_GROUP . '|author_last_name',
						'label' => esc_attr__( 'Author Last Name', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::AUTHOR_GROUP . '|author_custom_field',
						'label' => esc_attr__( 'Author Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Comments', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::COMMENTS_GROUP . '|count',
						'label' => esc_attr__( 'Comments Count', 'kadence-blocks-pro' ),
					),
				),
			),
		);
		return apply_filters( 'kadence_block_pro_dynamic_conditional_fields_options', $options );
	}
	/**
	 * Get the link fields
	 */
	public function get_link_fields() {
		$options = array(
			array(
				'label' => __( 'Post', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::POST_GROUP . '|post_url',
						'label' => esc_attr__( 'Post URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_featured_image_url',
						'label' => esc_attr__( 'Featured Image URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_custom_field',
						'label' => esc_attr__( 'Post Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Archive', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::ARCHIVE_GROUP . '|archive_url',
						'label' => esc_attr__( 'Archive URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::ARCHIVE_GROUP . '|archive_custom_field',
						'label' => esc_attr__( 'Archive Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Site', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::SITE_GROUP . '|site_url',
						'label' => esc_attr__( 'Site URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::SITE_GROUP . '|custom_setting',
						'label' => esc_attr__( 'Site Custom Setting', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Media', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::MEDIA_GROUP . '|media_url',
						'label' => esc_attr__( 'Media URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::MEDIA_GROUP . '|media_post_url',
						'label' => esc_attr__( 'Media Attachment URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::MEDIA_GROUP . '|media_custom_field',
						'label' => esc_attr__( 'Media Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Author', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::AUTHOR_GROUP . '|author_url',
						'label' => esc_attr__( 'Author Archive URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::AUTHOR_GROUP . '|author_custom_field',
						'label' => esc_attr__( 'Author Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Meta Relationship', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_url',
						'label' => esc_attr__( 'Post URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_featured_image_url',
						'label' => esc_attr__( 'Featured Image URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_custom_field',
						'label' => esc_attr__( 'Post Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			// self::COMMENTS_GROUP => array(
			// 	'label' => __( 'Comments', 'kadence-blocks-pro' ),
			// ),
		);
		return apply_filters( 'kadence_block_pro_dynamic_link_fields_options', $options );
	}
	/**
	 * Get the link fields
	 */
	public function get_url_fields() {
		$options = array(
			array(
				'label' => __( 'Post', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::POST_GROUP . '|post_custom_field',
						'label' => esc_attr__( 'Post Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Archive', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::ARCHIVE_GROUP . '|archive_custom_field',
						'label' => esc_attr__( 'Archive Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Media', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::MEDIA_GROUP . '|media_custom_field',
						'label' => esc_attr__( 'Media Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Meta Relationship', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_custom_field',
						'label' => esc_attr__( 'Post Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
		);
		return apply_filters( 'kadence_block_pro_dynamic_url_fields_options', $options );
	}
	/**
	 * Get the image background fields
	 */
	public function get_background_fields() {
		$options = array(
			array(
				'label' => __( 'Post', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::POST_GROUP . '|post_featured_image_url',
						'label' => esc_attr__( 'Featured Image URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_custom_field',
						'label' => esc_attr__( 'Post Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Archive', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::ARCHIVE_GROUP . '|archive_custom_field',
						'label' => esc_attr__( 'Archive Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Site', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::SITE_GROUP . '|logo_url',
						'label' => esc_attr__( 'Logo Image URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::SITE_GROUP . '|custom_setting',
						'label' => esc_attr__( 'Site Custom Setting', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Media', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::MEDIA_GROUP . '|media_url',
						'label' => esc_attr__( 'Media URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::MEDIA_GROUP . '|media_custom_field',
						'label' => esc_attr__( 'Media Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Author', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::AUTHOR_GROUP . '|author_image_url',
						'label' => esc_attr__( 'Author Image URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::AUTHOR_GROUP . '|author_custom_field',
						'label' => esc_attr__( 'Author Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Meta Relationship', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_featured_image_url',
						'label' => esc_attr__( 'Featured Image URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_custom_field',
						'label' => esc_attr__( 'Post Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			// self::COMMENTS_GROUP => array(
			// 	'label' => __( 'Comments', 'kadence-blocks-pro' ),
			// ),
		);
		return apply_filters( 'kadence_block_pro_dynamic_background_field_options', $options );
	}
	/**
	 * Get the list fields
	 */
	public function get_list_fields() {
		$options = array(
			array(
				'label' => __( 'Post', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::POST_GROUP . '|post_custom_field',
						'label' => esc_attr__( 'Post Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Archive', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::ARCHIVE_GROUP . '|archive_custom_field',
						'label' => esc_attr__( 'Archive Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Site', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::SITE_GROUP . '|custom_setting',
						'label' => esc_attr__( 'Site Custom Setting', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Meta Relationship', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_custom_field',
						'label' => esc_attr__( 'Post Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
		);
		return apply_filters( 'kadence_block_pro_dynamic_list_fields_options', $options );
	}
	/**
	 * Get the gallery fields
	 */
	public function get_gallery_fields() {
		$options = array(
			array(
				'label' => __( 'Post', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::POST_GROUP . '|post_custom_field',
						'label' => esc_attr__( 'Post Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Archive', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::ARCHIVE_GROUP . '|archive_custom_field',
						'label' => esc_attr__( 'Archive Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Site', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::SITE_GROUP . '|custom_setting',
						'label' => esc_attr__( 'Site Custom Setting', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Meta Relationship', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_custom_field',
						'label' => esc_attr__( 'Post Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
		);
		return apply_filters( 'kadence_block_pro_dynamic_gallery_fields_options', $options );
	}
	/**
	 * Get the image fields
	 */
	public function get_image_fields() {
		$options = array(
			array(
				'label' => __( 'Post', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::POST_GROUP . '|post_featured_image',
						'label' => esc_attr__( 'Featured Image', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::POST_GROUP . '|post_custom_field',
						'label' => esc_attr__( 'Post Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Archive', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::ARCHIVE_GROUP . '|archive_custom_field',
						'label' => esc_attr__( 'Archive Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Site', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::SITE_GROUP . '|logo',
						'label' => esc_attr__( 'Logo Image', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::SITE_GROUP . '|custom_setting',
						'label' => esc_attr__( 'Site Custom Setting', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Media', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::MEDIA_GROUP . '|media_url',
						'label' => esc_attr__( 'Media URL', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::MEDIA_GROUP . '|media_custom_field',
						'label' => esc_attr__( 'Media Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Author', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::AUTHOR_GROUP . '|author_image',
						'label' => esc_attr__( 'Author Image', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::AUTHOR_GROUP . '|author_custom_field',
						'label' => esc_attr__( 'Author Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			array(
				'label' => __( 'Meta Relationship', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_featured_image',
						'label' => esc_attr__( 'Featured Image', 'kadence-blocks-pro' ),
					),
					array(
						'value' => self::RELATIONSHIP_GROUP . '|post_custom_field',
						'label' => esc_attr__( 'Post Custom Field', 'kadence-blocks-pro' ),
					),
				),
			),
			// self::COMMENTS_GROUP => array(
			// 	'label' => __( 'Comments', 'kadence-blocks-pro' ),
			// ),
		);
		return apply_filters( 'kadence_block_pro_dynamic_image_fields_options', $options );
	}
	/**
	 * Render the dynamic content.
	 *
	 * @param array $args the content args
	 */
	public function get_content( $args, $post = null ) {
		$defaults = array(
			'source'       => 'current',
			'origin'       => 'core',
			'group'        => 'post',
			'type'         => 'text',
			'field'        => '',
			'custom'       => '',
			'para'         => '',
			'force-string' => '',
			'before'       => null,
			'after'        => null,
			'fallback'     => null,
		);
		$args           = wp_parse_args( $args, $defaults );
		$args['source'] = apply_filters( 'kadence_dynamic_item_id', $args['source'], $args, $post );
		$output         = $this->get_field_content( $args, $post );
		if ( $args['force-string'] && is_array( $output ) ) {
			if ( 'first' === $args['force-string'] ) {
				$output = reset( $output );
			}
			if ( is_array( $output ) ) {
				$output = implode( ', ', $output );
			}
		}
		if ( ! $output && $args['fallback'] !== null && $args['fallback'] !== '' ) {
			if ( 'image' === $args['type'] ) {
				$output = array( $args['fallback'], '', '' );
			} else {
				$output = $args['fallback'];
			}
		}
		if ( ! is_array( $output ) && 'background' !== $args['type'] && 'image' !== $args['type'] && $args['before'] ) {
			$output = $args['before'] . $output;
		}
		if ( ! is_array( $output ) && $args['after'] ) {
			$output = $output . $args['after'];
		}
		// Confirm data for Gallery.
		if ( 'gallery' === $args['type'] ) {
			if ( is_array( $output ) ) {
				$first_key = array_key_first( $output );
				if ( ! isset( $output[ $first_key ]['url'] ) ) {
					return '';
				}
			} else {
				return '';
			}
		}
		// Confirm data for List.
		if ( 'list' === $args['type'] ) {
			if ( is_array( $output ) ) {
				$first_key = array_key_first( $output );
				if ( ! isset( $output[ $first_key ]['label'] ) ) {
					return '';
				}
			} else {
				return '';
			}
		}
		return $output;
	}
	/**
	 * Get the content output.
	 *
	 * @param array $args the args.
	 * @param object/null $post the post.
	 */
	public function get_field_content( $args, $post = null ) {
		$defaults = array(
			'source'       => 'current',
			'origin'       => 'core',
			'group'        => 'post',
			'type'         => 'text',
			'field'        => '',
			'custom'       => '',
			'para'         => '',
			'before'       => null,
			'after'        => null,
			'relate'       => '',
			'relcustom'    => '',
		);
		$args      = wp_parse_args( $args, $defaults );
		$item_id   = $args['source'];
		$origin    = $args['origin'];
		$group     = $args['group'];
		$field     = $args['field'];
		$para      = $args['para'];
		$custom    = $args['custom'];
		$type      = $args['type'];
		$before    = $args['before'];
		$relate    = $args['relate'];
		$relcustom = $args['relcustom'];
		$output   = '';
		if ( 'core' === $origin ) {
			// Render Core.
			if ( self::RELATIONSHIP_GROUP === $group ) {
				if ( 'current' === $item_id || '' === $item_id ) {
					if ( $post && is_object( $post ) ) {
						$item_id = $post->ID;
					} else {
						$item_id = get_the_ID();
					}
				} else {
					$item_id = intval( $item_id );
				}
				if ( ! $post ) {
					$post = get_post( $item_id );
				}
				$new_source = '';
				if ( ! empty( $relate ) && is_object( $post ) ) {
					if ( 'kb_custom_input' === $relate ) {
						if ( ! empty( $relcustom ) ) {
							$output = get_post_meta( $post->ID, $relcustom, true );
						}
					} else if ( strpos( $relate, '|' ) !== false ) {
						list( $meta_type, $actual_key ) = explode( '|', $relate );
						switch ( $meta_type ) {
							case 'mb_meta':
							case 'mb_option':
								$new_source = kbp_dynamic_content_metabox( $actual_key, $meta_type, 'relationship', $post->ID, $args );
								break;
							case 'pod_meta':
							case 'pod_option':
								$new_source = kbp_dynamic_content_pods( $actual_key, $meta_type, 'relationship', $post->ID, $args );
								break;
							case 'acf_meta':
							case 'acf_option':
								$new_source = kbp_dynamic_content_acf( $actual_key, $meta_type, 'relationship', $post->ID, $args );
								break;
						}
					} else {
						$new_source = get_post_meta( $post->ID, $relcustom, true );
					}
					if ( ! empty( absint( $new_source ) ) ) {
						$group   = self::POST_GROUP;
						$item_id = absint( $new_source );
						$post    = null;
					}
				}
			}
			if ( self::POST_GROUP === $group ) {
				if ( 'current' === $item_id || '' === $item_id ) {
					if ( $post && is_object( $post ) ) {
						$item_id = $post->ID;
					} else {
						$item_id = get_the_ID();
					}
				} else {
					$item_id = intval( $item_id );
				}
				if ( ! $post ) {
					$post = get_post( $item_id );
				}
				if ( $post && is_object( $post ) && ( ( function_exists( 'is_post_publicly_viewable' ) ? is_post_publicly_viewable( $post ) : 'inherit' === $post->post_status || 'publish' === $post->post_status ) || current_user_can( 'read_post', $post->ID ) ) && apply_filters( 'kadence_dynamic_enable_password_content', empty( $post->post_password ) ) ) {
					switch ( $field ) {
						case 'post_title':
							$output = wp_kses_post( get_the_title( $post ) );
							break;
						case 'post_date':
							$output = get_the_date( '', $post );
							break;
						case 'post_date_modified':
							$output = get_the_modified_date( '', $post );
							break;
						case 'post_type':
							$output = get_post_type( $post );
							break;
						case 'post_status':
							$output = get_post_status( $post );
							break;
						case 'post_id':
							$output = $post->ID;
							break;
						case 'post_url':
							$output = get_permalink( $post );
							break;
						case 'post_excerpt':
							// Perhaps a way to prevent excerpt inside excerpt endless loop.
							if ( ! doing_filter( 'get_the_excerpt' ) ) {
								$output = get_the_excerpt( $post );
							}
							break;
						case 'post_content':
							// Need a way to prevent looping.
							$output = apply_filters( 'the_content', get_the_content( '', false, $post ) );
							break;
						case 'has_taxonomy':
							$output = false;
							if ( strpos( $para, '|' ) !== false ) {
								list( $the_tax, $the_term ) = explode( '|', $para );
								if ( has_term( $the_term, $the_tax, $post ) ) {
									$output = true;
								}
							} else {
								if ( has_term( '', $para, $post ) ) {
									$output = true;
								}
							}
							break;
						case 'post_custom_field':
							$output = '';
							if ( ! empty( $para ) ) {
								if ( 'kb_custom_input' === $para ) {
									if ( ! empty( $custom ) ) {
										$output = get_post_meta( $post->ID, $custom, true );
									}
								} else if ( strpos( $para, '|' ) !== false ) {
									list( $meta_type, $actual_key ) = explode( '|', $para );
									switch ( $meta_type ) {
										case 'mb_meta':
										case 'mb_option':
											$output = kbp_dynamic_content_metabox( $actual_key, $meta_type, $type, $post->ID, $args );
											break;
										case 'pod_meta':
										case 'pod_option':
											$output = kbp_dynamic_content_pods( $actual_key, $meta_type, $type, $post->ID, $args );
											break;
										case 'acf_meta':
										case 'acf_option':
											$output = kbp_dynamic_content_acf( $actual_key, $meta_type, $type, $post->ID, $args );
											break;
										case 'woo':
											$output = kbp_dynamic_content_woo( $actual_key, $meta_type, $type, $post->ID, $args );
											break;
									}
								} else {
									$output = get_post_meta( $post->ID, $para, true );
								}
							}
							break;
						case 'post_featured_image_url':
							if ( 'background' === $type && ! empty( $before ) ) {
								$output = ( has_post_thumbnail( $post ) ? get_the_post_thumbnail_url( $post, $before ) : '' );
							} else {
								$output = ( has_post_thumbnail( $post ) ? get_the_post_thumbnail_url( $post, 'full' ) : '' );
							}
							break;
						case 'post_featured_image':
							if ( 'image' === $type && ! empty( $before ) ) {
								$output = ( has_post_thumbnail( $post ) ? wp_get_attachment_image_src( get_post_thumbnail_id( $post ), $before ) : '' );
								if ( ! empty( $output ) ) {
									$output[4] = get_post_meta( get_post_thumbnail_id( $post ), '_wp_attachment_image_alt', true );
									$output[5] = get_post_thumbnail_id( $post );
								}
							} else {
								$output = ( has_post_thumbnail( $post ) ? wp_get_attachment_image_src( get_post_thumbnail_id( $post ), 'full' ) : '' );
								if ( ! empty( $output ) ) {
									$output[4] = get_post_meta( get_post_thumbnail_id( $post ), '_wp_attachment_image_alt', true );
									$output[5] = get_post_thumbnail_id( $post );
								}
							}
							break;
						default:
							$output = apply_filters( "kadence_dynamic_content_core_post_{$field}_render", '', $item_id, $origin, $group, $field, $para, $custom, $relate, $relcustom );
							break;
					}
				} else {
					$output = apply_filters( "kadence_dynamic_content_core_post_{$field}_render", '', $item_id, $origin, $group, $field, $para, $custom, $relate, $relcustom );
				}
			} elseif ( self::AUTHOR_GROUP === $group ) {
				if ( 'current' === $item_id || '' === $item_id ) {
					if ( $post && is_object( $post ) ) {
						$item_id = $post->ID;
					} elseif ( is_author() ) {
						$author = get_queried_object();
					} else {
						$item_id = get_the_ID();
					}
				} else {
					$item_id = intval( $item_id );
				}
				if ( ! $post && ! isset( $author ) ) {
					$post = get_post( $item_id );
				}
				if ( $post ) {
					$post_type_obj = get_post_type_object( get_post_type( $post ) );
				}
				if ( ( $post && is_object( $post ) && 'publish' === $post->post_status && apply_filters( 'kadence_dynamic_enable_password_content', empty( $post->post_password ) ) && post_type_supports( $post_type_obj->name, 'author' ) ) || ( isset( $author ) && is_object( $author) ) ) {
					if ( isset( $author ) && is_object( $author ) ) {
						$author_id = $author->ID;
					} else {
						$author_id = get_post_field( 'post_author', $item_id );
					}
					if ( $author_id ) {
						switch ( $field ) {
							case 'author_name':
								$output = esc_html( get_the_author_meta( 'display_name', $author_id ) );
								break;
							case 'author_first_name':
								$output = esc_html( get_the_author_meta( 'first_name', $author_id ) );
								break;
							case 'author_last_name':
								$output = esc_html( get_the_author_meta( 'last_name', $author_id ) );
								break;
							case 'author_info':
								$output = esc_html( get_the_author_meta( 'description', $author_id ) );
								break;
							case 'author_url':
								$output = esc_url( get_author_posts_url( $author_id ) );
								break;
							case 'author_image_url':
								if ( 'background' === $type && $before && in_array( $before, array( 'large', 'medium', 'thumbnail', 'medium_large' ), true ) ) {
									$args = array( 'size' => 96 );
									if ( 'large' === $before ) {
										$args['size'] = 1024;
									} elseif ( 'medium_large' === $before ) {
										$args['size'] = 768;
									} elseif ( 'medium_large' === $before ) {
										$args['size'] = 300;
									} elseif ( 'medium_large' === $before ) {
										$args['size'] = 150;
									}
									$output = get_avatar_url( $author_id, $args );
								} else {
									$output = get_avatar_url( $author_id );
								}
								break;
							case 'author_image':
								if ( 'image' === $type && $before && in_array( $before, array( 'large', 'medium', 'thumbnail', 'medium_large' ), true ) ) {
									$args = array( 'size' => 96 );
									if ( 'large' === $before ) {
										$args['size'] = 1024;
									} elseif ( 'medium_large' === $before ) {
										$args['size'] = 768;
									} elseif ( 'medium_large' === $before ) {
										$args['size'] = 300;
									} elseif ( 'medium_large' === $before ) {
										$args['size'] = 150;
									}
									$output = array( get_avatar_url( $author_id, $args ), $args['size'], $args['size'], true, get_the_author_meta( 'display_name', $author_id ) );
								} else {
									$output = array( get_avatar_url( $author_id ), 96, 96, true, get_the_author_meta( 'display_name', $author_id ) );
								}
								break;
							case 'author_custom_field':
								$output = '';
								if ( ! empty( $para ) ) {
									if ( 'kb_custom_input' === $para ) {
										if ( ! empty( $custom ) ) {
											$output = get_the_author_meta( $custom, $author_id );
										}
									} else if ( strpos( $para, '|' ) !== false ) {
										list( $meta_type, $actual_key ) = explode( '|', $para );
										switch ( $meta_type ) {
											case 'mb_meta':
												$output = kbp_dynamic_content_metabox( $actual_key, $meta_type, $type, $author_id, $args );
												break;
											case 'pod_meta':
												$output = kbp_dynamic_content_pods( $actual_key, $meta_type, $type, $author_id, $args );
												break;
											case 'acf_meta':
												$output = kbp_dynamic_content_acf( $actual_key, $meta_type, $type, 'user_' . $author_id, $args );
												break;
										}
									} else {
										$output = get_the_author_meta( $para, $author_id );
									}
								}
								break;
							default:
								$output = apply_filters( "kadence_dynamic_content_core_author_{$field}_render", '', $item_id, $origin, $group, $field, $para, $custom, $relate, $relcustom );
								break;
						}
					} else {
						$output = apply_filters( "kadence_dynamic_content_core_author_{$field}_render", '', $item_id, $origin, $group, $field, $para, $custom, $relate, $relcustom );
					}
				} else {
					$output = apply_filters( "kadence_dynamic_content_core_author_{$field}_render", '', $item_id, $origin, $group, $field, $para, $custom, $relate, $relcustom );
				}
			} elseif ( self::ARCHIVE_GROUP === $group ) {
				if ( 'current' === $item_id || '' === $item_id ) {
					$item_id = get_queried_object_id();
				} else {
					$item_id = intval( $item_id );
				}
				switch ( $field ) {
					case 'archive_title':
						// This needs updated, won't get anything but the current archive title.
						$output = wp_kses_post( get_the_archive_title() );
						break;
					case 'archive_description':
						remove_filter( 'term_description', 'wpautop' );
						$output = wp_kses_post( get_the_archive_description() );
						add_filter( 'term_description', 'wpautop' );
						break;
					case 'archive_url':
						$output = get_the_permalink( $item_id );
						break;
					case 'archive_custom_field':
						$output = '';
						if ( ! empty( $para ) ) {
							if ( 'kb_custom_input' === $para ) {
								if ( ! empty( $custom ) ) {
									$output = get_term_meta( $item_id, $custom, true );
								}
							} else if ( strpos( $para, '|' ) !== false ) {
								list( $meta_type, $actual_key ) = explode( '|', $para );
								switch ( $meta_type ) {
									case 'mb_meta':
										$output = kbp_dynamic_content_metabox( $actual_key, $meta_type, $type, 'term:' . $item_id, $args );
										break;
									case 'pod_meta':
										$output = kbp_dynamic_content_pods( $actual_key, $meta_type, $type, 'term:' . $item_id, $args );
										break;
									case 'acf_meta':
										$term = get_queried_object();
										if ( is_object( $term ) && isset( $term->taxonomy ) ) {
											$output = kbp_dynamic_content_acf( $actual_key, $meta_type, $type, $term->taxonomy . '_' . $item_id, $args );
										}
										break;
								}
							} else {
								$output = get_term_meta( $item_id, $para, true );
							}
						}
						break;
					default:
						$output = apply_filters( "kadence_dynamic_content_core_archive_{$field}_render", '', $item_id, $origin, $group, $field, $para, $custom, $relate, $relcustom );
						break;
				}
			} elseif ( self::SITE_GROUP === $group ) {
				switch ( $field ) {
					case 'site_title':
						$output = wp_kses_post( get_bloginfo( 'name' ) );
						break;
					case 'site_tagline':
						$output = wp_kses_post( get_bloginfo( 'description' ) );
						break;
					case 'logo_url':
						$logo      = get_theme_mod( 'custom_logo' );
						if ( 'background' === $type && $before && in_array( $before, array( 'large', 'medium', 'thumbnail', 'medium_large' ), true ) ) {
							$image     = wp_get_attachment_image_src( $logo, $before );
							$image_url = ( $image && $image[0] ? $image[0] : '' );
						} else {
							$image     = wp_get_attachment_image_src( $logo, 'full' );
							$image_url = ( $image && $image[0] ? $image[0] : '' );
						}
						$output = $image_url;
						break;
					case 'logo':
						$logo   = get_theme_mod( 'custom_logo' );
						$image  = wp_get_attachment_image_src( $logo, 'full' );
						$output = $image;
						break;
					case 'site_url':
						$output = get_home_url();
						break;
					case 'page_title':
						$output = wp_kses_post( $this->get_the_title() );
						break;
					case 'custom_setting':
						$output = '';
						if ( ! empty( $para ) ) {
							if ( 'kb_custom_input' === $para ) {
								if ( ! empty( $custom ) ) {
									$output = get_option( $custom );
								}
							} else if ( strpos( $para, '|' ) !== false ) {
								list( $meta_type, $actual_key ) = explode( '|', $para );
								switch ( $meta_type ) {
									case 'mb_option':
										$output = kbp_dynamic_content_metabox( $actual_key, $meta_type, $type, null, $args );
										break;
									case 'pod_option':
										$output = kbp_dynamic_content_pods( $actual_key, $meta_type, $type, null, $args );
										break;
									case 'acf_option':
										$output = kbp_dynamic_content_acf( $actual_key, $meta_type, $type, null, $args );
										break;
								}
							}
						}
						break;
					case 'user_info':
						$user = wp_get_current_user();
						if ( 0 === $user->ID ) {
							$output = '';
							break;
						}
						if ( empty( $custom ) ) {
							$output = isset( $user->display_name ) ? $user->display_name : '';
							break;
						}
						switch ( $custom ) {
							case 'id':
								$output = isset( $user->ID ) ? $user->ID : '';
								break;
							case 'username':
								$output = isset( $user->user_login ) ? $user->user_login : '';
								break;
							case 'first_name':
								$output = isset( $user->first_name ) ? $user->first_name : '';
								break;
							case 'last_name':
								$output = isset( $user->last_name ) ? $user->last_name : '';
								break;
							case 'bio':
								$output = isset( $user->description ) ? $user->description : '';
								break;
							case 'email':
								$output = isset( $user->user_email ) ? $user->user_email : '';
								break;
							case 'website':
								$output = isset( $user->user_url ) ? $user->user_url : '';
								break;
							case 'meta':
								if ( ! empty( $para ) ) {
									if ( strpos( $para, '|' ) !== false ) {
										list( $meta_type, $actual_key ) = explode( '|', $para );
										switch ( $meta_type ) {
											case 'mb_meta':
												$output = kbp_dynamic_content_metabox( $actual_key, $meta_type, $type, $user->ID, $args );
												break;
											case 'pod_meta':
												$output = kbp_dynamic_content_pods( $actual_key, $meta_type, $type, $user->ID, $args );
												break;
											case 'acf_meta':
												$output = kbp_dynamic_content_acf( $actual_key, $meta_type, $type, 'user_' . $user->ID, $args );
												break;
											case 'kb_custom_input':
												$output = get_user_meta( $user->ID, $actual_key, true );
												break;
										}
									} else {
										$output = get_user_meta( $user->ID, $para, true );
									}
								}
								break;
							default:
								// display name.
								$output = isset( $user->display_name ) ? $user->display_name : '';
								break;
						}
						break;
					default:
						$output = apply_filters( "kadence_dynamic_content_core_site_{$field}_render", '', $item_id, $origin, $group, $field, $para, $custom, $relate, $relcustom );
						break;
				}
			} elseif ( self::USER_GROUP === $group ) {
				$user = wp_get_current_user();
				if ( 0 === $user->ID ) {
					$output = '';
				} else {
					switch ( $field ) {
						case 'id':
							$output = isset( $user->ID ) ? $user->ID : '';
							break;
						case 'username':
							$output = isset( $user->user_login ) ? $user->user_login : '';
							break;
						case 'first_name':
							$output = isset( $user->first_name ) ? $user->first_name : '';
							break;
						case 'last_name':
							$output = isset( $user->last_name ) ? $user->last_name : '';
							break;
						case 'display_name':
							$output = isset( $user->display_name ) ? $user->display_name : '';
							break;
						case 'bio':
							$output = isset( $user->description ) ? $user->description : '';
							break;
						case 'email':
							$output = isset( $user->user_email ) ? $user->user_email : '';
							break;
						case 'website':
							$output = isset( $user->user_url ) ? $user->user_url : '';
							break;
						case 'user_custom_field':
							if ( ! empty( $para ) ) {
								if ( 'kb_custom_input' === $para ) {
									if ( ! empty( $custom ) ) {
										$output = get_user_meta( $user->ID, $custom, true );
									}
								} else if ( strpos( $para, '|' ) !== false ) {
									list( $meta_type, $actual_key ) = explode( '|', $para );
									switch ( $meta_type ) {
										case 'mb_meta':
											$output = kbp_dynamic_content_metabox( $actual_key, $meta_type, $type, $user->ID, $args );
											break;
										case 'pod_meta':
											$output = kbp_dynamic_content_pods( $actual_key, $meta_type, $type, $user->ID, $args );
											break;
										case 'acf_meta':
											$output = kbp_dynamic_content_acf( $actual_key, $meta_type, $type, 'user_' . $user->ID, $args );
											break;
										case 'kb_custom_input':
											$output = get_user_meta( $user->ID, $actual_key, true );
											break;
									}
								} else {
									$output = get_user_meta( $user->ID, $para, true );
								}
							}
							break;
						default:
							$output = apply_filters( "kadence_dynamic_content_core_user_{$field}_render", '', $user->ID, $origin, $group, $field, $para, $custom, $relate, $relcustom );
							break;
					}
				}
			} elseif ( self::MEDIA_GROUP === $group ) {
				global $kb_media_context;
				if ( 'current' === $item_id || '' === $item_id ) {
					if ( ! empty( $kb_media_context ) ) {
						$item_id = $kb_media_context;
					} else if ( $post && is_object( $post ) ) {
						$item_id = $post->ID;
					} else {
						$item_id = get_the_ID();
					}
				} else {
					$item_id = intval( $item_id );
				}
				if ( ! $post ) {
					$post = get_post( $item_id );
				}
				if ( $post && is_object( $post ) && ( 'inherit' === $post->post_status || 'publish' === $post->post_status ) && apply_filters( 'kadence_dynamic_enable_password_content', empty( $post->post_password ) ) ) {
					$post_type = get_post_type( $post );
					if ( 'attachment' !== $post_type ) {
						$post_id = ( has_post_thumbnail( $post ) ? get_post_thumbnail_id( $post ) : '' );
						if ( $post_id ) {
							$post = get_post( $post_id );
						}
					}
					if ( $post && is_object( $post ) && ( 'inherit' === $post->post_status || 'publish' === $post->post_status ) && apply_filters( 'kadence_dynamic_enable_password_content', empty( $post->post_password ) ) ) {
						switch ( $field ) {
							case 'media_url':
								$attachment_id = $output;
								if ( ! empty( $before ) ) {
									$image_array = wp_get_attachment_image_src( $post->ID, $before );
								} else {
									$image_array = wp_get_attachment_image_src( $post->ID, 'full' );
								}
								if ( $image_array ) {
									$output = $image_array[0];
								}
								break;
							case 'media_title':
								$output = wp_kses_post( get_the_title( $post ) );
								break;
							case 'media_post_url':
								$output = get_permalink( $post );
								break;
							case 'media_caption':
								$output = $post->post_excerpt;
								break;
							case 'media_description':
								$output = $post->post_content;
								break;
							case 'media_alt_text':
								$output = get_post_meta( $post->ID, '_wp_attachment_image_alt', true );
								break;
							case 'media_filename':
								$output = basename( get_attached_file( $post->ID ) );
								break;
							case 'media_custom_field':
								$output = '';
								if ( ! empty( $para ) ) {
									if ( 'kb_custom_input' === $para ) {
										if ( ! empty( $custom ) ) {
											$output = get_post_meta( $post->ID, $custom, true );
										}
									} else if ( strpos( $para, '|' ) !== false ) {
										list( $meta_type, $actual_key ) = explode( '|', $para );
										switch ( $meta_type ) {
											case 'mb_meta':
												$output = kbp_dynamic_content_metabox( $actual_key, $meta_type, $type, $post->ID, $args );
												break;
											case 'pod_meta':
												$output = kbp_dynamic_content_pods( $actual_key, $meta_type, $type, $post->ID, $args );
												break;
											case 'acf_meta':
												$output = kbp_dynamic_content_acf( $actual_key, $meta_type, $type, $post->ID, $args );
												break;
										}
									} else {
										$output = get_post_meta( $post->ID, $para, true );
									}
								}
								break;
							default:
								$output = apply_filters( "kadence_dynamic_content_core_media_{$field}_render", '', $item_id, $origin, $group, $field, $para, $custom, $relate, $relcustom );
								break;
						}
					}
				}
			} elseif ( self::COMMENTS_GROUP === $group ) {
				if ( 'current' === $item_id || '' === $item_id ) {
					if ( $post && is_object( $post ) ) {
						$item_id = $post->ID;
					} else {
						$item_id = get_the_ID();
					}
				} else {
					$item_id = intval( $item_id );
				}
				if ( ! $post ) {
					$post = get_post( $item_id );
				}
				switch ( $field ) {
					case 'count':
						$output = get_comments_number( $post );
						break;
					default:
						$output = apply_filters( "kadence_dynamic_content_core_comments_{$field}_render", '', $item_id, $origin, $group, $field, $para, $custom, $relate, $relcustom );
						break;
				}
			}
		} else {
			$output = apply_filters( "kadence_dynamic_content_{$origin}_render", $item_id, $origin, $group, $field, $para, $custom, $relate, $relcustom );
		}
		return apply_filters( 'kadence_dynamic_content_render', $output, $item_id, $origin, $group, $field, $para, $custom, $relate, $relcustom );
	}
	/**
	 * Get the title output.
	 */
	public function get_the_title() {
		$output = '';
		if ( is_404() ) {
			$output = esc_html_e( 'Oops! That page can&rsquo;t be found.', 'kadence-blocks-pro' );
		} elseif ( is_home() && ! have_posts() ) {
			$output = esc_html_e( 'Nothing Found', 'kadence-blocks-pro' );
		} elseif ( is_home() && ! is_front_page() ) {
			$output = single_post_title();
		} elseif ( is_search() ) {
			$output = sprintf(
				/* translators: %s: search query */
				esc_html__( 'Search Results for: %s', 'kadence-blocks-pro' ),
				'<span>' . get_search_query() . '</span>'
			);
		} elseif ( is_archive() || is_home() ) {
			$output = get_the_archive_title();
		}
		return $output;
	}
	/**
	 * Render the dynamic shortcode.
	 *
	 * @param array $attributes the shortcode attributes.
	 */
	public function dynamic_shortcode_render( $attributes ) {
		$atts = shortcode_atts(
			array(
				'source'       => 'current',
				'origin'       => 'core',
				'type'         => 'text',
				'field'        => '',
				'custom'       => '',
				'para'         => '',
				'force-string' => true,
				'before'       => null,
				'after'        => null,
				'fallback'     => null,
				'relate'       => '',
				'relcustom'    => '',
			),
			$attributes
		);
		// Sanitize Attributes.
		$field = sanitize_text_field( $atts['field'] );
		$group = 'post';
		if ( ! empty( $field ) && strpos( $field, '|' ) !== false ) {
			$field_split = explode( '|', $field, 2 );
			$group = ( isset( $field_split[0] ) && ! empty( $field_split[0] ) ? $field_split[0] : 'post' );
			$field = ( isset( $field_split[1] ) && ! empty( $field_split[1] ) ? $field_split[1] : '' );
		}
		$args = array(
			'source'       => ! empty( $atts['source'] ) ? sanitize_text_field( $atts['source'] ) : 'current',
			'origin'       => ! empty( $atts['origin'] ) ? sanitize_text_field( $atts['origin'] ) : 'core',
			'group'        => $group,
			'type'         => sanitize_text_field( $atts['type'] ),
			'field'        => $field,
			'custom'       => sanitize_text_field( $atts['custom'] ),
			'para'         => sanitize_text_field( $atts['para'] ),
			'before'       => sanitize_text_field( $atts['before'] ),
			'after'        => sanitize_text_field( $atts['after'] ),
			'relate'       => sanitize_text_field( $atts['relate'] ),
			'relcustom'    => sanitize_text_field( $atts['relcustom'] ),
		);

		$fallback       = sanitize_text_field( $atts['fallback'] );
		$args['source'] = apply_filters( 'kadence_dynamic_item_id', $args['source'], $args );
		$output         = $this->get_field_content( $args );
		if ( $atts['force-string'] && is_array( $output ) ) {
			if ( 'first' === $atts['force-string'] ) {
				$output = reset( $output );
			}
			if ( is_array( $output ) ) {
				$output = implode( ',', $output );
			}
		}
		if ( ! $output && null !== $fallback ) {
			$output = $fallback;
		}
		if ( ! is_array( $output ) && 'background' !== $args['type'] && $args['before'] ) {
			$output = $args['before'] . $output;
		}
		if ( ! is_array( $output ) && $args['after'] ) {
			$output = $output . $args['after'];
		}
		return $output;
	}
}
Kadence_Blocks_Pro_Dynamic_Content::get_instance();
