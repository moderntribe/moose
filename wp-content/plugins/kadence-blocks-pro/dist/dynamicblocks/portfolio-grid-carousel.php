<?php
/**
 * Portfolio Block Render
 *
 * @since   1.3.5
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create API fields for additional info
 */
function kadence_blocks_pro_portfolio_register_rest_fields() {
	// Add featured image source
	$post_types = kadence_blocks_pro_get_post_types();
	foreach ( $post_types as $key => $post_type ) {
		// Add taxonomy info
		register_rest_field(
			$post_type['value'],
			'taxonomy_info',
			array(
				'get_callback'    => 'kadence_blocks_pro_get_taxonomy_info',
				'update_callback' => null,
				'schema'          => null,
			)
		);
	}
}
add_action( 'rest_api_init', 'kadence_blocks_pro_portfolio_register_rest_fields' );

/**
 * Get category info for the rest field
 *
 * @param object $object Post Object.
 * @param string $field_name Field name.
 * @param object $request Request Object.
 */
function kadence_blocks_pro_get_taxonomy_info( $object, $field_name, $request ) {
	$taxonomies = get_object_taxonomies( $object['type'], 'objects' );
	$taxs = array();
	foreach ( $taxonomies as $term_slug => $term ) {
		if ( ! $term->public || ! $term->show_ui ) {
			continue;
		}
		$terms = get_the_terms( $object['id'], $term_slug );
		$term_items = array();
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term_key => $term_item ) {
				$term_items[] = array(
					'value' => $term_item->term_id,
					'label' => $term_item->name,
				);
			}
			$taxs[ $term_slug ] = $term_items;
		}
	}
	return $taxs;
}


/**
 * Register the dynamic block.
 *
 * @return void
 */
function kadence_blocks_pro_portfolio_block() {

	// Only load if Gutenberg is available.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}
	// Hook server side rendering into render callback.
	register_block_type(
		'kadence/portfoliogrid',
		array(
			'attributes' => array(
				'queryType' => array(
					'type' => 'string',
					'default' => 'query',
				),
				'postIds' => array(
					'type' => 'array',
					'default' => array(),
					'items'   => array(
						'type' => 'integer',
					),
				),
				'order' => array(
					'type' => 'string',
					'default' => 'desc',
				),
				'orderBy'  => array(
					'type' => 'string',
					'default' => 'date',
				),
				'categories' => array(
					'type' => 'array',
					'default' => array(),
					'items'   => array(
						'type' => 'object',
					),
				),
				'tags' => array(
					'type' => 'array',
					'default' => array(),
					'items'   => array(
						'type' => 'object',
					),
				),
				'uniqueID' => array(
					'type' => 'string',
				),
				'postsToShow' => array(
					'type' => 'number',
					'default' => 6,
				),
				'pagination'=> array(
					'type' => 'boolean',
					'default' => false,
				),
				'postTax'=> array(
					'type' => 'boolean',
					'default' => false,
				),
				// Layout.
				'align' => array(
					'type' => 'string',
					'default' => 'none',
				),
				'style' => array(
					'type' => 'string',
					'default' => 'center-on-hover',
				),
				'layout' => array(
					'type' => 'string',
					'default' => 'grid',
				),
				'postColumns'=> array(
					'type' => 'array',
					'default' => array( 2, 2, 2, 2, 1, 1 ),
					'items'   => array(
						'type' => 'integer',
					),
				),
				'columnControl' => array(
					'type' => 'string',
					'default' => 'linked',
				),
				'carouselHeight' => array(
					'type' => 'array',
					'default' => array( 300, 300, 300 ),
					'items'   => array(
						'type' => 'integer',
					),
				),
				'carouselAlign' => array(
					'type' => 'boolean',
					'default' => true,
				),
				'columnGap' => array(
					'type' => 'number',
					'default' => 30,
				),
				'rowGap' => array(
					'type' => 'number',
					'default' => 30,
				),
				'autoPlay' => array(
					'type' => 'boolean',
					'default' => true,
				),
				'autoSpeed' => array(
					'type' => 'number',
					'default' => 7000,
				),
				'transSpeed' => array(
					'type' => 'number',
					'default' => 400,
				),
				'slidesScroll' => array(
					'type' => 'string',
					'default' => '1',
				),
				'arrowStyle' => array(
					'type' => 'string',
					'default' => 'whiteondark',
				),
				'dotStyle' => array(
					'type' => 'string',
					'default' => 'dark',
				),
				// Container.
				'backgroundColor' => array(
					'type' => 'string',
				),
				'backgroundColorOpacity' => array(
					'type' => 'number',
					'default' => 1,
				),
				'containerPadding'=> array(
					'type' => 'array',
					'default' => array( 0, 0, 0, 0 ),
					'items'   => array(
						'type' => 'integer',
					),
				),
				'borderColor' => array(
					'type' => 'string',
				),
				'borderOpacity' => array(
					'type' => 'number',
					'default' => 1,
				),
				'borderWidth'=> array(
					'type' => 'array',
					'default' => array( 0, 0, 0, 0 ),
					'items'   => array(
						'type' => 'integer',
					),
				),
				// Image.
				'displayImage' => array(
					'type' => 'boolean',
					'default' => true,
				),
				'imageRatio'=> array(
					'type' => 'string',
					'default' => '75',
				),
				// Hover Animation.
				'imgAnimation' => array(
					'type' => 'string',
					'default' => 'zoomout',
				),
				'contentAnimation' => array(
					'type' => 'string',
					'default' => 'zoomin',
				),
				// Filter.
				'displayFilter' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'filterAllText' => array(
					'type' => 'string',
				),
				'filterTaxType' => array(
					'type' => 'string',
				),
				'filterTaxSelect' => array(
					'type' => 'array',
					'default' => array(),
					'items'   => array(
						'type' => 'object',
					),
				),
				'filterBackground'=> array(
					'type' => 'string',
				),
				'filterBackgroundOpacity'=> array(
					'type' => 'number',
				),
				'filterHoverBackground'=> array(
					'type' => 'string',
				),
				'filterHoverBackgroundOpacity'=> array(
					'type' => 'number',
				),
				'filterActiveBackground'=> array(
					'type' => 'string',
				),
				'filterActiveBackgroundOpacity'=> array(
					'type' => 'number',
				),
				'filterBorder'=> array(
					'type' => 'string',
				),
				'filterBorderOpacity'=> array(
					'type' => 'number',
				),
				'filterHoverBorder'=> array(
					'type' => 'string',
				),
				'filterHoverBorderOpacity'=> array(
					'type' => 'number',
				),
				'filterActiveBorder'=> array(
					'type' => 'string',
				),
				'filterActiveBorderOpacity'=> array(
					'type' => 'number',
				),
				'filterColor' => array(
					'type' => 'string',
				),
				'filterHoverColor' => array(
					'type' => 'string',
				),
				'filterActiveColor' => array(
					'type' => 'string',
				),
				'filterBorderRadius'=> array(
					'type' => 'number',
				),
				'filterBorderWidth'=> array(
					'type' => 'array',
					'default' => array( 0, 0, 2, 0 ),
					'items'   => array(
						'type' => 'integer',
					),
				),
				'filterPadding' => array(
					'type' => 'array',
					'default' => array( 5, 8, 5, 8 ),
					'items'   => array(
						'type' => 'integer',
					),
				),
				'filterMargin'=> array(
					'type' => 'array',
					'default' => array( 0, 10, 0, 0 ),
					'items'   => array(
						'type' => 'integer',
					),
				),
				'filterFont'=> array(
					'type' => 'array',
					'default' => array(
						array(
							'size' => array( '', '', '' ),
							'sizeType' => 'px',
							'lineHeight' => array( '', '', '' ),
							'lineType' => 'px',
							'letterSpacing' => '',
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
				'filterAlign' => array(
					'type' => 'string',
					'default' => '',
				),
				// Taxonomies.
				'displayTaxonomies' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'displayTaxonomiesType' => array(
					'type' => 'string',
				),
				'taxDividerSymbol' => array(
					'type' => 'string',
					'default' => 'line',
				),
				'taxColor'=> array(
					'type' => 'string',
				),
				'taxLinkColor' => array(
					'type' => 'string',
				),
				'taxLinkHoverColor' => array(
					'type' => 'string',
				),
				'taxFont'=> array(
					'type' => 'array',
					'default' => array(
						array(
							'size' => array( '', '', '' ),
							'sizeType' => 'px',
							'lineHeight' => array( '', '', '' ),
							'lineType' => 'px',
							'letterSpacing' => '',
							'textTransform' => '',
							'family' => '',
							'google' => '',
							'style' => '',
							'weight' => '',
							'variant' => '',
							'subset' => '',
							'loadGoogle' => true,
						),
					),
					'items'   => array(
						'type' => 'object',
					),
				),
				// Title.
				'displayTitle' => array(
					'type' => 'boolean',
					'default' => true,
				),
				'titleColor'=> array(
					'type' => 'string',
				),
				'titleFont'=> array(
					'type' => 'array',
					'default' => array(
						array(
							'level' => 3,
							'size' => array( '', '', '' ),
							'sizeType' => 'px',
							'lineHeight' => array( '', '', '' ),
							'lineType' => 'px',
							'letterSpacing' => '',
							'textTransform' => '',
							'family' => '',
							'google' => '',
							'style' => '',
							'weight' => '',
							'variant' => '',
							'subset' => '',
							'loadGoogle' => true,
						),
					),
					'items'   => array(
						'type' => 'object',
					),
				),
				'titlePadding' => array(
					'type' => 'array',
					'default' => array( 5, 0, 10, 0 ),
					'items'   => array(
						'type' => 'integer',
					),
				),
				'titleMargin'=> array(
					'type' => 'array',
					'default' => array( 0, 0, 0, 0 ),
					'items'   => array(
						'type' => 'integer',
					),
				),
				// Body.
				'contentBackground'=> array(
					'type' => 'string',
				),
				'contentBackgroundOpacity'=> array(
					'type' => 'number',
					'default' => 0,
				),
				'contentBackgroundType'=> array(
					'type' => 'string',
					'default' => 'solid',
				),
				'contentBackgroundGradient'=> array(
					'type' => 'array',
					'default' => array(
						array(
							'color' => '',
							'opacity' => 1,
							'location' => 0,
							'secondLocation' => 100,
							'type' => 'linear',
							'angle' => 180,
							'position' => 'center center',
						),
					),
					'items'   => array(
						'type' => 'object',
					),
				),
				'contentHoverBackgroundGradient'=> array(
					'type' => 'array',
					'default' => array(
						array(
							'color' => '',
							'opacity' => 1,
							'location' => 0,
							'secondLocation' => 100,
							'type' => 'linear',
							'angle' => 180,
							'position' => 'center center',
						),
					),
					'items'   => array(
						'type' => 'object',
					),
				),
				'contentHoverBackground'=> array(
					'type' => 'string',
				),
				'contentHoverBackgroundOpacity'=> array(
					'type' => 'number',
					'default' => 0.5,
				),
				'contentBorderOffset'=> array(
					'type' => 'number',
					'default' => 0,
				),
				'contentBorderWidth'=> array(
					'type' => 'array',
					'default' => array( 1, 1, 1, 1 ),
					'items'   => array(
						'type' => 'integer',
					),
				),
				'contentBorder'=> array(
					'type' => 'string',
				),
				'contentBorderOpacity'=> array(
					'type' => 'number',
					'default' => 0,
				),
				'contentHoverBorder'=> array(
					'type' => 'string',
				),
				'contentHoverBorderOpacity'=> array(
					'type' => 'number',
					'default' => 0.8,
				),
				'contentHoverBorderOffset'=> array(
					'type' => 'number',
					'default' => 15,
				),
				'contentPadding'=> array(
					'type' => 'array',
					'default' => array( 0, 0, 0, 0 ),
					'items'   => array(
						'type' => 'integer',
					),
				),
				'contentMargin'=> array(
					'type' => 'array',
					'default' => array( 0, 0, 0, 0 ),
					'items'   => array(
						'type' => 'integer',
					),
				),
				// Excerpt.
				'displayExcerpt' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'excerptColor'=> array(
					'type' => 'string',
				),
				'excerptFont'=> array(
					'type' => 'array',
					'default' => array(
						array(
							'size' => array( '', '', '' ),
							'sizeType' => 'px',
							'lineHeight' => array( '', '', '' ),
							'lineType' => 'px',
							'letterSpacing' => '',
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
				'postType' => array(
					'type' => 'string',
					'default' => 'post',
				),
				'taxType' => array(
					'type' => 'string',
					'default' => '',
				),
				'textAlign' => array(
					'type' => 'string',
					'default' => '',
				),
				'offsetQuery' => array(
					'type' => 'number',
					'default' => 0,
				),
				'allowSticky' => array(
					'type' => 'boolean',
					'default' => false,
				),
				'imageFileSize'=> array(
					'type' => 'string',
					'default' => 'large',
				),
			),
			'render_callback' => 'kadence_blocks_pro_render_portfolio_block',
		)
	);

}
add_action( 'init', 'kadence_blocks_pro_portfolio_block' );

/**
 * Server rendering for Post Block
 *
 * @param array $attributes the block attributes.
 */
function kadence_blocks_pro_render_portfolio_block( $attributes ) {
	if ( ! wp_style_is( 'kadence-blocks-portfolio-grid', 'enqueued' ) ) {
		wp_enqueue_style( 'kadence-blocks-portfolio-grid' );
	}
	$css = '';
	if ( isset( $attributes['uniqueID'] ) ) {
		$css .= '<style media="all" id="kt-blocks' . esc_attr( $attributes['uniqueID'] ) . '">';
		$unique_id = $attributes['uniqueID'];
		$css .= kt_blocks_pro_portfolio_grid_css( $attributes, $unique_id );
		$css .= '</style>';
	}
	if ( isset( $attributes['layout'] ) && 'masonry' === $attributes['layout'] ) {
		wp_enqueue_script( 'kadence-blocks-pro-masonry-init' );
	} elseif ( isset( $attributes['layout'] ) && 'carousel' === $attributes['layout'] ) {
		wp_enqueue_style( 'kadence-blocks-pro-slick' );
		wp_enqueue_script( 'kadence-blocks-pro-slick-init' );
	}
	ob_start();
	if ( isset( $attributes['layout'] ) && ( 'carousel' === $attributes['layout'] || 'fluidcarousel' === $attributes['layout'] ) ) {
		$carouselclasses = ' kt-blocks-carousel';
		if ( 'fluidcarousel' === $attributes['layout'] && isset( $attributes['carouselAlign'] ) && ! $attributes['carouselAlign'] ) {
			$carouselclasses .= ' kb-carousel-mode-align-left';
		}
	} else {
		$carouselclasses = '';
	}
	if ( empty( $carouselclasses ) && isset( $attributes['displayFilter'] ) && true === $attributes['displayFilter'] && ( ! isset( $attributes['pagination'] ) || isset( $attributes['pagination'] ) && false === $attributes['pagination'] ) ) {
		$filter_class = 'kb-filter-enabled';
	} else {
		$filter_class = '';
	}
	echo '<div class="wp-block-kadence-portfoliogrid kb-blocks-portfolio-loop-block align' . ( isset( $attributes['align'] ) ? $attributes['align'] : 'none' ) . ' kb-portfolio-loop' . ( isset( $attributes['uniqueID'] ) ? $attributes['uniqueID'] : 'block-id' ) . ' kb-portfolio-grid-layout-'. ( isset( $attributes['layout'] ) ? esc_attr( $attributes['layout'] ) : 'grid' ) . esc_attr( $carouselclasses ) . ' ' . esc_attr( $filter_class ) . ( isset( $attributes['className'] ) && ! empty( $attributes['className'] ) ? ' ' . esc_attr( $attributes['className'] ) : '' ) . '">';
	if ( empty( $carouselclasses ) && isset( $attributes['displayFilter'] ) && true === $attributes['displayFilter'] && ( ! isset( $attributes['pagination'] ) || isset( $attributes['pagination'] ) && false === $attributes['pagination'] ) ) {
		kadence_blocks_pro_render_portfolio_block_filter( $attributes );
	}
		kadence_blocks_pro_render_portfolio_block_query( $attributes );
	echo '</div>';

	$output = ob_get_contents();
	ob_end_clean();
	return $css . $output;
}
/**
 * Server rendering for portfolio Block Inner Loop
 *
 * @param array $attributes the block attributes.
 */
function kadence_blocks_pro_render_portfolio_block_filter( $attributes ) {
	if ( isset( $attributes['filterTaxType'] ) && ! empty( $attributes['filterTaxType'] ) ) {
		echo '<div class="kb-portfolio-filter-container">';
		if ( isset( $attributes['filterTaxSelect'] ) && is_array( $attributes['filterTaxSelect'] ) && 1 <= count( $attributes['filterTaxSelect'] ) ) {
			echo '<button class="kb-filter-item is-active" data-filter="*">';
				echo ( isset( $attributes['filterAllText'] ) && ! empty( $attributes['filterAllText'] ) ? esc_html( $attributes['filterAllText'] ) : __( 'All', 'kadence-blocks-pro' ) );
			echo '</button>';
			foreach ( $attributes['filterTaxSelect'] as $value ) {
				$term = get_term( $value['value'], $attributes['filterTaxType'] );
				echo '<button class="kb-filter-item" data-filter=".kb-filter-' . esc_attr( $term->term_id ) . '">';
				echo esc_html( $term->name );
				echo '</button>';
			}
		} else {
			$terms = get_terms( $attributes['filterTaxType'] );
			if ( ! empty( $terms ) ) {
				echo '<button class="kb-filter-item is-active" data-filter="*">';
					echo ( isset( $attributes['filterAllText'] ) && ! empty( $attributes['filterAllText'] ) ? esc_html( $attributes['filterAllText'] ) : __( 'All', 'kadence-blocks-pro' ) );
				echo '</button>';
				foreach ( $terms as $term_key => $term_item ) {
					echo '<button class="kb-filter-item" data-filter=".kb-filter-' . esc_attr( $term_item->term_id ) . '">';
					echo esc_html( $term_item->name );
					echo '</button>';
				}
			}
		}
		echo '</div>';
	}
}
/**
 * Server rendering for portfolio Block Inner Loop
 *
 * @param array $attributes the block attributes.
 */
function kadence_blocks_pro_render_portfolio_block_query( $attributes ) {
	global $kadence_blocks_posts_not_in;
	if ( ! isset( $kadence_blocks_posts_not_in ) || ! is_array( $kadence_blocks_posts_not_in ) ) {
		$kadence_blocks_posts_not_in = array();
	}
	if ( isset( $attributes['layout'] ) && ( 'carousel' === $attributes['layout'] || 'fluidcarousel' === $attributes['layout'] ) ) {
		$carouselclasses = ' kt-post-grid-layout-carousel-wrap kt-carousel-arrowstyle-' . ( isset( $attributes['arrowStyle'] ) ? esc_attr( $attributes['arrowStyle'] ) : 'whiteondark' ) . ' kt-carousel-dotstyle-' . ( isset( $attributes['dotStyle'] ) ? esc_attr( $attributes['dotStyle'] ) : 'dark' );
		$slider_data = ' data-slider-anim-speed="' . ( isset( $attributes['transSpeed'] ) ? esc_attr( $attributes['transSpeed'] ) : '400' ) . '" data-slider-scroll="' . ( isset( $attributes['slidesScroll'] ) ? esc_attr( $attributes['slidesScroll'] ) : '1' ) . '" data-slider-dots="' . ( isset( $attributes['dotStyle'] ) && 'none' === $attributes['dotStyle'] ? 'false' : 'true' ) . '" data-slider-arrows="' . ( isset( $attributes['arrowStyle'] ) && 'none' === $attributes['arrowStyle'] ? 'false' : 'true' ) . '" data-slider-hover-pause="false" data-slider-auto="' . ( isset( $attributes['autoPlay'] ) ? esc_attr( $attributes['autoPlay'] ) : 'true' ) . '" data-slider-center-mode="' . ( isset( $attributes['carouselAlign'] ) && $attributes['carouselAlign'] ? 'true' : 'false' ) . '" data-slider-speed="' . ( isset( $attributes['autoSpeed'] ) ? esc_attr( $attributes['autoSpeed'] ) : '7000' ) . '" ';
	} elseif ( isset( $attributes['layout'] ) && 'masonry' === $attributes['layout'] ) {
		$carouselclasses = ' kb-pro-masonry-init';
		$slider_data = '';
	} else {
		$carouselclasses = '';
		$slider_data = '';
	}
	if ( apply_filters( 'kadence_blocks_pro_portfolio_block_exclude_current', true ) ) {
		if ( ! in_array( get_the_ID(), $kadence_blocks_posts_not_in, true ) ) {
			$kadence_blocks_posts_not_in[] = get_the_ID();
		}
	}
	$columns = ( isset( $attributes['postColumns'] ) && is_array( $attributes['postColumns'] ) && 6 === count( $attributes['postColumns'] ) ? $attributes['postColumns'] : array( 2, 2, 2, 2, 1, 1 ) );
	$post_type = ( isset( $attributes['postType'] ) && ! empty( $attributes['postType'] ) ? $attributes['postType'] : 'post' );
	echo '<div class="kb-portfolio-grid-wrap kb-portfolio-grid-layout-' . ( isset( $attributes['layout'] ) ? esc_attr( $attributes['layout'] ) : 'grid' ) . '-wrap' . esc_attr( $carouselclasses ) . ' kb-blocks-portfolio-img-hover-' . esc_attr( $attributes['imgAnimation'] ) . ' kb-blocks-portfolio-content-hover-' . esc_attr( $attributes['contentAnimation'] ) . '" data-columns-xxl="' . esc_attr( $columns[0] ) . '" data-columns-xl="' . esc_attr( $columns[1] ) . '" data-columns-md="' . esc_attr( $columns[2] ) . '" data-columns-sm="' . esc_attr( $columns[3] ) . '" data-columns-xs="' . esc_attr( $columns[4] ) . '" data-columns-ss="' . esc_attr( $columns[5] ) . '"' . wp_kses_post( $slider_data ) . 'data-item-selector=".kb-portfolio-masonry-item">';
	if ( isset( $attributes['queryType'] ) && 'individual' === $attributes['queryType'] ) {
		$args = array(
			'post_type'           => $post_type,
			'orderby'             => 'post__in',
			'post__in'            => ( isset( $attributes['postIds'] ) && ! empty( $attributes['postIds'] ) ? $attributes['postIds'] : 0 ),
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => -1,
		);
	} else {
		$args = array(
			'post_type'           => $post_type,
			'posts_per_page'      => ( isset( $attributes['postsToShow'] ) && ! empty( $attributes['postsToShow'] ) ? $attributes['postsToShow'] : 6 ),
			'post_status'         => 'publish',
			'order'               => ( isset( $attributes['order'] ) && ! empty( $attributes['order'] ) ? $attributes['order'] : 'desc' ),
			'orderby'             => ( isset( $attributes['orderBy'] ) && ! empty( $attributes['orderBy'] ) ? $attributes['orderBy'] : 'date' ),
			'ignore_sticky_posts' => ( isset( $attributes['allowSticky'] ) && $attributes['allowSticky'] ? 0 : 1 ),
			'post__not_in'        => ( isset( $kadence_blocks_posts_not_in ) && is_array( $kadence_blocks_posts_not_in ) ? $kadence_blocks_posts_not_in : array() ),
		);
		if ( isset( $attributes['offsetQuery'] ) && ! empty( $attributes['offsetQuery'] ) ) {
			$args['offset'] = $attributes['offsetQuery'];
		}
		if ( isset( $attributes['categories'] ) && ! empty( $attributes['categories'] ) && is_array( $attributes['categories'] ) ) {
			$categories = array();
			$i = 1;
			foreach ( $attributes['categories'] as $key => $value ) {
				$categories[] = $value['value'];
			}
		} else {
			$categories = array();
		}
		if ( 'post' !== $post_type || ( isset( $attributes['postTax'] ) && true === $attributes['postTax'] ) ) {
			if ( isset( $attributes['taxType'] ) && ! empty( $attributes['taxType'] ) ) {
				$args['tax_query'][] = array(
					'taxonomy' => ( isset( $attributes['taxType'] ) ) ? $attributes['taxType'] : 'category',
					'field'    => 'id',
					'terms'    => $categories,
					'operator' => 'IN',
				);
			}
		} else {
			if ( isset( $attributes['tags'] ) && ! empty( $attributes['tags'] ) && is_array( $attributes['tags'] ) ) {
				$tags = array();
				$i = 1;
				foreach ( $attributes['tags'] as $key => $value ) {
					$tags[] = $value['value'];
				}
			} else {
				$tags = array();
			}
			$args['category__in'] = $categories;
			$args['tag__in'] = $tags;
		}
		if ( isset( $attributes['layout'] ) && 'carousel' !== $attributes['layout'] && ( ( isset( $attributes['offsetQuery'] ) && 1 > $attributes['offsetQuery'] ) || ! isset( $attributes['offsetQuery'] ) ) && isset( $attributes['pagination'] ) && true === $attributes['pagination'] ) {
			if ( get_query_var( 'paged' ) ) {
				$args['paged'] = get_query_var( 'paged' );
			} else if ( get_query_var( 'page' ) ) {
				$args['paged'] = get_query_var( 'page' );
			} else {
				$args['paged'] = 1;
			}
		}
	}
	$args = apply_filters( 'kadence_blocks_pro_portfolio_grid_query_args', $args );
	$loop = new WP_Query( $args );
	if ( isset( $attributes['layout'] ) && 'carousel' !== $attributes['layout'] && ( ( isset( $attributes['offsetQuery'] ) && 1 > $attributes['offsetQuery'] ) || ! isset( $attributes['offsetQuery'] ) ) && isset( $attributes['pagination'] ) && true === $attributes['pagination'] ) {
		global $wp_query;
		$wp_query = $loop;
	}
	if ( $loop->have_posts() ) {
		while ( $loop->have_posts() ) {
			$loop->the_post();
			if ( isset( $attributes['showUnique'] ) && true === $attributes['showUnique'] ) {
				$kadence_blocks_posts_not_in[] = get_the_ID();
			}
			if ( isset( $attributes['layout'] ) && 'masonry' === $attributes['layout'] ) {
				$tax_filter_classes = '';
				if ( isset( $attributes['filterTaxType'] ) && ! empty( $attributes['filterTaxType'] ) ) {
					global $post;
					$terms = get_the_terms( $post->ID, $attributes['filterTaxType'] );
					if ( $terms && ! is_wp_error( $terms ) ) {
						foreach( $terms as $term ) {
							$tax_filter_classes .= ' kb-filter-' . $term->term_id;
						}
					}
				}
				echo '<div class="kb-portfolio-masonry-item' . esc_attr( $tax_filter_classes ) . '">';
			} else if ( isset( $attributes['layout'] ) && 'grid' === $attributes['layout'] && isset( $attributes['displayFilter'] ) && true === $attributes['displayFilter'] && ( ! isset( $attributes['pagination'] ) || isset( $attributes['pagination'] ) && false === $attributes['pagination'] ) ) {
				$tax_filter_classes = '';
				if ( isset( $attributes['filterTaxType'] ) && ! empty( $attributes['filterTaxType'] ) ) {
					global $post;
					$terms = get_the_terms( $post->ID, $attributes['filterTaxType'] );
					if ( $terms && ! is_wp_error( $terms ) ) {
						foreach( $terms as $term ) {
							$tax_filter_classes .= ' kb-filter-' . $term->term_id;
						}
					}
				}
				echo '<div class="kb-portfolio-masonry-item' . esc_attr( $tax_filter_classes ) . '">';
			} else if ( isset( $attributes['layout'] ) && ( 'carousel' === $attributes['layout'] || 'fluidcarousel' === $attributes['layout'] ) ) {
				echo '<div class="kb-portfolio-slider-item">';
			}
				kadence_blocks_pro_render_portfolio_block_loop( $attributes );
			if ( isset( $attributes['layout'] ) && 'grid' !== $attributes['layout'] ) {
				echo '</div>';
			}
			if ( isset( $attributes['layout'] ) && 'grid' === $attributes['layout'] && isset( $attributes['displayFilter'] ) && true === $attributes['displayFilter'] && ( ! isset( $attributes['pagination'] ) || isset( $attributes['pagination'] ) && false === $attributes['pagination'] ) ) {
				echo '</div>';
			}
		}
	} else {
		/**
		 * Kadence Blocks Portfolio get no post text.
		 *
		 * @hooked kadence_blocks_pro_portfolio_get_no_posts - 10
		 */
		do_action( 'kadence_blocks_pro_portfolio_no_posts', $attributes );
	}
	echo '</div>';
	wp_reset_postdata();
	if ( isset( $attributes['layout'] ) && 'carousel' !== $attributes['layout'] && ( ( isset( $attributes['offsetQuery'] ) && 1 > $attributes['offsetQuery'] ) || ! isset( $attributes['offsetQuery'] ) ) && isset( $attributes['pagination'] ) && true === $attributes['pagination'] ) {
		if ( $loop->max_num_pages > 1 ) {
			kadence_blocks_pro_portfolio_pagination();
		}
		wp_reset_query();
	}
}
/**
 * Server rendering for Portfolio Block pagination.
 */
function kadence_blocks_pro_portfolio_pagination() {
	$args = array();
	$args['mid_size'] = 3;
	$args['end_size'] = 1;
	$args['prev_text'] = '<span class="screen-reader-text">' . __( 'Previous Page', 'kadence-blocks-pro' ) . '</span><svg style="display:inline-block;vertical-align:middle" aria-hidden="true" class="kt-blocks-pagination-left-svg" viewBox="0 0 320 512" height="14" width="8" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M34.52 239.03L228.87 44.69c9.37-9.37 24.57-9.37 33.94 0l22.67 22.67c9.36 9.36 9.37 24.52.04 33.9L131.49 256l154.02 154.75c9.34 9.38 9.32 24.54-.04 33.9l-22.67 22.67c-9.37 9.37-24.57 9.37-33.94 0L34.52 272.97c-9.37-9.37-9.37-24.57 0-33.94z"></path></svg>';
	$args['next_text'] = '<span class="screen-reader-text">' . __( 'Next Page', 'kadence-blocks-pro' ) . '</span><svg style="display:inline-block;vertical-align:middle" class="kt-blocks-pagination-right-svg" aria-hidden="true" viewBox="0 0 320 512" height="14" width="8" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path></svg>';

	echo '<div class="kt-blocks-page-nav">';
		the_posts_pagination(
			apply_filters(
				'kadence_blocks_pagination_args',
				$args
			)
		);
	echo '</div>';
}
/**
 * Get no Posts text.
 *
 * @param array $attributes Block Attributes.
 */
function kadence_blocks_pro_portfolio_get_no_posts( $attributes ) {
	echo '<p>' . esc_html__( 'No posts', 'kadence-blocks-pro' ) . '</p>';
}

add_action( 'kadence_blocks_pro_portfolio_no_posts', 'kadence_blocks_pro_portfolio_get_no_posts', 10 );
/**
 * Server rendering for Post Block Inner Loop
 *
 * @param array $attributes the block attributes.
 */
function kadence_blocks_pro_render_portfolio_block_loop( $attributes ) {
	$image_align = ( isset( $attributes['alignImage'] ) && isset( $attributes['displayImage'] ) && true === $attributes['displayImage'] && has_post_thumbnail() ? $attributes['alignImage'] : 'none' );
	echo '<div class="kb-blocks-portfolio-grid-item">';
		do_action( 'kadence_blocks_portfolio_loop_start', $attributes );
		echo '<div class="kb-blocks-portfolio-grid-item-inner-wrap kb-feat-image-align-' . esc_attr( $image_align ) . '">';
			/**
			 * Kadence Blocks Portfolio Loop Start
			 *
			 * @hooked kb_blocks_pro_get_portfolio_image - 20
			 */
			do_action( 'kadence_blocks_portfolio_loop_image', $attributes );
			echo '<div class="kb-portfolio-grid-item-inner">';
				/**
				 * Kadence Blocks Portfolio before Hover content.
				 *
				 * @hooked kb_blocks_pro_portfolio_hover_link - 10
				 * @hooked kb_blocks_pro_portfolio_hover_divs - 20
				 */
				do_action( 'kadence_blocks_portfolio_loop_before_content', $attributes );

				echo '<div class="kb-portfolio-content-item-inner">';
					/**
					 * Kadence Blocks Portfolio Hover content.
					 *
					 * @hooked kb_blocks_pro_get_portfolio_lightbox - 20
					 * @hooked kb_blocks_pro_get_portfolio_title - 20
					 * @hooked kb_blocks_pro_get_portfolio_taxonomies - 30
					 * @hooked kb_blocks_pro_get_portfolio_excerpt - 40
					 */
					do_action( 'kadence_blocks_portfolio_loop_content_inner', $attributes );
				echo '</div>';
			echo '</div>';
		echo '</div>';
	do_action( 'kadence_blocks_portfolio_loop_end', $attributes );
	echo '</div>';
}

/**
 * Get Post Loop Image
 *
 * @param array $attributes Block Attributes.
 */
function kb_blocks_pro_get_portfolio_image( $attributes ) {
	global $post;
	if ( isset( $attributes['displayImage'] ) && true === $attributes['displayImage'] && has_post_thumbnail() ) {
		$image_ratio = ( isset( $attributes['imageRatio'] ) ? $attributes['imageRatio'] : '75' );
		$image_size = ( isset( $attributes['imageFileSize'] ) && ! empty( $attributes['imageFileSize'] ) ? $attributes['imageFileSize'] : 'large' );
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->id ), $image_size );
		$has_image = ( isset( $image[1] ) && ! empty( $image[1] ) ? true : false );
		echo '<div class="kadence-portfolio-image' . ( $has_image ? '' : ' kb-no-image-set' ) . '">';
		echo '<div class="kadence-portfolio-image-intrisic kt-image-ratio-' . esc_attr( str_replace( '.', '-', $image_ratio ) ) .'" style="padding-bottom:' . ( $has_image && ( 'nocrop' === $image_ratio || 'masonry' === $attributes['layout'] ) ? ( ( $image[2] / $image[1] ) * 100 ) . '%' : esc_attr( $image_ratio ) . '%' ) . '">';
		echo '<div class="kadence-portfolio-image-inner-intrisic">';
			the_post_thumbnail( $image_size );
		echo '</div>';
		echo '</div>';
		echo '</div>';
	} else {
		$image_ratio = ( isset( $attributes['imageRatio'] ) ? $attributes['imageRatio'] : '75' );
		echo '<div class="kadence-portfolio-image kb-no-image-set">';
		echo '<div class="kadence-portfolio-image-intrisic kt-image-ratio-' . esc_attr( str_replace( '.', '-', $image_ratio ) ) . '" style="padding-bottom:' . ( 'nocrop' === $image_ratio ? '66.67%' : esc_attr( $image_ratio ) . '%' ) . '">';
		echo '<div class="kadence-portfolio-image-inner-intrisic">';
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
}
add_action( 'kadence_blocks_portfolio_loop_image', 'kb_blocks_pro_get_portfolio_image', 20 );

/**
 * Get Portfolio divs for Hover
 *
 * @param array $attributes Block Attributes.
 */
function kb_blocks_pro_portfolio_hover_divs( $attributes ) {
	echo '<div class="kb-portfolio-overlay-color"></div>';
	echo '<div class="kb-portfolio-overlay-border"></div>';
}
add_action( 'kadence_blocks_portfolio_loop_before_content', 'kb_blocks_pro_portfolio_hover_divs', 20 );

/**
 * Get Portfolio Link for Hover
 *
 * @param array $attributes Block Attributes.
 */
function kb_blocks_pro_portfolio_hover_link( $attributes ) {
	echo '<a href="' . esc_url( get_the_permalink() ) . '" aria-label="' . esc_attr( get_the_title() ) . '" class="portfolio-hover-item-link"></a>';
}
add_action( 'kadence_blocks_portfolio_loop_before_content', 'kb_blocks_pro_portfolio_hover_link', 10 );

/**
 * Get Portfolio Lightbox Link for Hover
 *
 * @param array $attributes Block Attributes.
 */
function kb_blocks_pro_get_portfolio_lightbox( $attributes ) {
	if ( isset( $attributes['displayLightboxIcon'] ) && true === $attributes['displayLightboxIcon'] && has_post_thumbnail() ) {
		global $post;
		if ( has_post_thumbnail() || apply_filters( 'kadence_blocks_pro_portfolio_lightbox_has_link', false ) ) {
			$link = apply_filters( 'kadence_blocks_pro_portfolio_lightbox_link', get_the_post_thumbnail_url( $post, 'full' ) );
			echo '<a href="' . esc_url( $link ) . '" class="portfolio-hover-lightbox-link" aria-label="' . esc_attr( __( 'View Project Preview', 'kadence-blocks-pro' ) ) . '">';
				echo '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" class="kt-blocks-comments-svg" width="36" height="32" fill="currentColor" viewBox="0 0 36 32"><title>' . esc_attr( __( 'Zoom', 'kadence-blocks-pro' ) ) . '</title><path d="M15 4c-1.583 0-3.112 0.248-4.543 0.738-1.341 0.459-2.535 1.107-3.547 1.926-1.876 1.518-2.91 3.463-2.91 5.474 0 1.125 0.315 2.217 0.935 3.247 0.646 1.073 1.622 2.056 2.821 2.842 0.951 0.624 1.592 1.623 1.761 2.748 0.028 0.187 0.051 0.375 0.068 0.564 0.085-0.079 0.169-0.16 0.254-0.244 0.754-0.751 1.771-1.166 2.823-1.166 0.167 0 0.335 0.011 0.503 0.032 0.605 0.077 1.223 0.116 1.836 0.116 1.583 0 3.112-0.248 4.543-0.738 1.341-0.459 2.535-1.107 3.547-1.926 1.876-1.518 2.91-3.463 2.91-5.474s-1.033-3.956-2.91-5.474c-1.012-0.819-2.206-1.467-3.547-1.926-1.431-0.49-2.96-0.738-4.543-0.738zM15 0v0c8.284 0 15 5.435 15 12.139s-6.716 12.139-15 12.139c-0.796 0-1.576-0.051-2.339-0.147-3.222 3.209-6.943 3.785-10.661 3.869v-0.785c2.008-0.98 3.625-2.765 3.625-4.804 0-0.285-0.022-0.564-0.063-0.837-3.392-2.225-5.562-5.625-5.562-9.434 0-6.704 6.716-12.139 15-12.139zM31.125 27.209c0 1.748 1.135 3.278 2.875 4.118v0.673c-3.223-0.072-6.181-0.566-8.973-3.316-0.661 0.083-1.337 0.126-2.027 0.126-2.983 0-5.732-0.805-7.925-2.157 4.521-0.016 8.789-1.464 12.026-4.084 1.631-1.32 2.919-2.87 3.825-4.605 0.961-1.84 1.449-3.799 1.449-5.825 0-0.326-0.014-0.651-0.039-0.974 2.268 1.873 3.664 4.426 3.664 7.24 0 3.265-1.88 6.179-4.82 8.086-0.036 0.234-0.055 0.474-0.055 0.718z"></path></svg>';
			echo '</a>';
		}
	}
}
add_action( 'kadence_blocks_portfolio_loop_content_inner', 'kb_blocks_pro_get_portfolio_lightbox', 10 );

/**
 * Get Portfolio Loop Title
 *
 * @param array $attributes Block Attributes.
 */
function kb_blocks_pro_get_portfolio_title( $attributes ) {
	if ( isset( $attributes['displayTitle'] ) && true === $attributes['displayTitle'] ) {
		echo ( isset( $attributes['titleFont'] ) && isset( $attributes['titleFont'][0] ) && isset( $attributes['titleFont'][0]['level'] ) && ! empty( $attributes['titleFont'][0]['level'] ) ? '<h' . esc_attr( $attributes['titleFont'][0]['level'] ) . ' class="entry-title kb-portfolio-loop-title">' : '<h3 class="entry-title kb-portfolio-loop-title">' );
			the_title();
		echo ( isset( $attributes['titleFont'] ) && isset( $attributes['titleFont'][0] ) && isset( $attributes['titleFont'][0]['level'] ) && ! empty( $attributes['titleFont'][0]['level'] ) ? '</h' . esc_attr( $attributes['titleFont'][0]['level'] ) . '>' : '</h3>' );
	}
}
add_action( 'kadence_blocks_portfolio_loop_content_inner', 'kb_blocks_pro_get_portfolio_title', 20 );

/**
 * Get Post Loop Above Categories
 *
 * @param array $attributes Block Attributes.
 */
function kb_blocks_pro_get_portfolio_taxonomies( $attributes ) {
	if ( isset( $attributes['displayTaxonomies'] ) && true === $attributes['displayTaxonomies'] && isset( $attributes['displayTaxonomiesType'] ) && ! empty( $attributes['displayTaxonomiesType'] ) ) {
		global $post;
		$terms = get_the_terms( $post->ID, $attributes['displayTaxonomiesType'] );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$sep_name = ( isset( $attributes['taxDividerSymbol'] ) ? $attributes['taxDividerSymbol'] : 'line' );
			if ( 'dash' === $sep_name ) {
				$sep = '&#8208;';
			} else if ( 'line' === $sep_name ) {
				$sep = '&#124;';
			} else if ( 'dot' === $sep_name ) {
				$sep = '&#183;';
			} else if ( 'bullet' === $sep_name ) {
				$sep = '&#8226;';
			} else if ( 'tilde' === $sep_name ) {
				$sep = '&#126;';
			} else {
				$sep = '';
			}
			$output = array();
			foreach( $terms as $term ) {
				$output[] = $term->name;
			}
			echo '<div class="kb-blocks-portfolio-taxonomies">';
			echo implode( ' ' . $sep . ' ', $output );
			echo '</div>';
		}
	}
}
add_action( 'kadence_blocks_portfolio_loop_content_inner', 'kb_blocks_pro_get_portfolio_taxonomies', 30 );

/**
 * Get Post Loop Excerpt
 *
 * @param array $attributes Block Attributes.
 */
function kb_blocks_pro_get_portfolio_excerpt( $attributes ) {
	if ( isset( $attributes['displayExcerpt'] ) && true === $attributes['displayExcerpt'] ) {
		echo '<div class="entry-content kb-portfolio-loop-excerpt">';
			echo get_the_excerpt();
		echo '</div>';
	}
}
add_action( 'kadence_blocks_portfolio_loop_content_inner', 'kb_blocks_pro_get_portfolio_excerpt', 40 );


/**
 * Builds CSS for Post Grid block.
 *
 * @param array  $attr the blocks attr.
 * @param string $unique_id the blocks attr ID.
 */
function kt_blocks_pro_portfolio_grid_css( $attr, $unique_id ) {
	$css = '';
	// Columns.
	if ( isset( $attr['columnGap'] ) && isset( $attr['layout'] ) && 'carousel' === $attr['layout'] ) {
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-slider-item {';
			$css .= 'padding:0 ' . $attr['columnGap'] / 2 . 'px;';
		$css .= '}';
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-layout-carousel-wrap {';
			$css .= 'margin-left:-' . $attr['columnGap'] / 2 . 'px;';
			$css .= 'margin-right:-' . $attr['columnGap'] / 2 . 'px;';
		$css .= '}';
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-layout-carousel-wrap .slick-prev {';
			$css .= 'left:' . $attr['columnGap'] / 2 . 'px;';
		$css .= '}';
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-layout-carousel-wrap .slick-next {';
			$css .= 'right:' . $attr['columnGap'] / 2 . 'px;';
		$css .= '}';
	}
	if ( isset( $attr['columnGap'] ) && isset( $attr['layout'] ) && 'fluidcarousel' === $attr['layout'] ) {
		$css .= '.kb-portfolio-loop' . $unique_id . '.kb-portfolio-grid-layout-fluidcarousel .slick-slider .slick-slide {';
			$css .= 'padding:4px ' . $attr['columnGap'] / 2 . 'px;';
		$css .= '}';
		$css .= '.kb-portfolio-loop' . $unique_id . '.kb-portfolio-grid-layout-fluidcarousel.kb-carousel-mode-align-left .slick-slider .slick-slide {';
			$css .= 'padding:4px ' . $attr['columnGap'] . 'px 4px 0;';
		$css .= '}';
	}
	if ( isset( $attr['columnGap'] ) && isset( $attr['layout'] ) && 'masonry' === $attr['layout'] ) {
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-layout-masonry-wrap .kb-portfolio-masonry-item {';
			$css .= 'padding-left:' . $attr['columnGap'] / 2 . 'px;';
			$css .= 'padding-right:' . $attr['columnGap'] / 2 . 'px;';
		$css .= '}';
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-layout-masonry-wrap {';
			$css .= 'margin-left:-' . $attr['columnGap'] / 2 . 'px;';
			$css .= 'margin-right:-' . $attr['columnGap'] / 2 . 'px;';
		$css .= '}';
	}
	if ( isset( $attr['rowGap'] ) && isset( $attr['layout'] ) && 'masonry' === $attr['layout'] ) {
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-layout-masonry-wrap .kb-portfolio-masonry-item {';
			$css .= 'padding-bottom:' . $attr['rowGap'] . 'px;';
		$css .= '}';
	}
	if ( isset( $attr['columnGap'] ) && isset( $attr['layout'] ) && 'grid' === $attr['layout'] ) {
		$rowgap = ( isset( $attr['rowGap'] ) ? $attr['rowGap'] : '30' );
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-wrap {';
			$css .= 'grid-gap:' . $rowgap . 'px ' . $attr['columnGap'] . 'px;';
		$css .= '}';
	}
	if ( isset( $attr['columnGap'] ) && isset( $attr['layout'] ) && 'grid' === $attr['layout'] && isset( $attr['displayFilter'] ) && true === $attr['displayFilter'] && ( ! isset( $attr['pagination'] ) || isset( $attr['pagination'] ) && false === $attr['pagination'] ) ) {
		$css .= '.kb-portfolio-loop' . $unique_id . '.kb-portfolio-grid-layout-grid.kb-filter-enabled .kb-portfolio-masonry-item {';
			$css .= 'padding-left:' . $attr['columnGap'] / 2 . 'px;';
			$css .= 'padding-right:' . $attr['columnGap'] / 2 . 'px;';
		$css .= '}';
		$css .= '.kb-portfolio-loop' . $unique_id . '.kb-portfolio-grid-layout-grid.kb-filter-enabled .kb-portfolio-grid-wrap {';
			$css .= 'margin-left:-' . $attr['columnGap'] / 2 . 'px;';
			$css .= 'margin-right:-' . $attr['columnGap'] / 2 . 'px;';
		$css .= '}';
	}
	if ( isset( $attr['rowGap'] ) && isset( $attr['layout'] ) && 'grid' === $attr['layout'] && isset( $attr['displayFilter'] ) && true === $attr['displayFilter'] && ( ! isset( $attr['pagination'] ) || isset( $attr['pagination'] ) && false === $attr['pagination'] ) ) {
		$rowgap = ( isset( $attr['rowGap'] ) ? $attr['rowGap'] : '30' );
		$css .= '.kb-portfolio-loop' . $unique_id . '.kb-portfolio-grid-layout-grid.kb-filter-enabled .kb-portfolio-masonry-item {';
			$css .= 'padding-bottom:' . $attr['rowGap'] . 'px;';
		$css .= '}';
	}
	if ( isset( $attr['layout'] ) && 'fluidcarousel' === $attr['layout'] && isset( $attr['carouselHeight'] ) && is_array( $attr['carouselHeight'] ) ) {
		if ( isset( $attr['carouselHeight'][0] ) && is_numeric( $attr['carouselHeight'][0] ) ) {
			$css .= '.kb-portfolio-loop' . $unique_id . '.kb-portfolio-grid-layout-fluidcarousel.kt-blocks-carousel .kadence-portfolio-image, .kb-portfolio-loop' . $unique_id . '.kb-portfolio-grid-layout-fluidcarousel.kt-blocks-carousel .kadence-portfolio-image .kadence-portfolio-image-intrisic .kadence-portfolio-image-inner-intrisic img {';
			$css .= 'height:' . $attr['carouselHeight'][0] . 'px;';
			$css .= '}';
		}
		if ( isset( $attr['carouselHeight'][1] ) && is_numeric( $attr['carouselHeight'][1] ) ) {
			$css .= '@media (min-width: 767px) and (max-width: 1024px) {';
			$css .= '.kb-portfolio-loop' . $unique_id . '.kb-portfolio-grid-layout-fluidcarousel.kt-blocks-carousel .kadence-portfolio-image, .kb-portfolio-loop' . $unique_id . '.kb-portfolio-grid-layout-fluidcarousel.kt-blocks-carousel .kadence-portfolio-image .kadence-portfolio-image-intrisic .kadence-portfolio-image-inner-intrisic img {';
			$css .= 'height:' . $attr['carouselHeight'][1] . 'px;';
			$css .= '}';
			$css .= '}';
		}
		if ( isset( $attr['carouselHeight'][2] ) && is_numeric( $attr['carouselHeight'][2] ) ) {
			$css .= '@media (max-width: 767px) {';
			$css .= '.kb-portfolio-loop' . $unique_id . '.kb-portfolio-grid-layout-fluidcarousel.kt-blocks-carousel .kadence-portfolio-image, .kb-portfolio-loop' . $unique_id . '.kb-portfolio-grid-layout-fluidcarousel.kt-blocks-carousel .kadence-portfolio-image .kadence-portfolio-image-intrisic .kadence-portfolio-image-inner-intrisic img {';
			$css .= 'height:' . $attr['carouselHeight'][2] . 'px;';
			$css .= '}';
			$css .= '}';
		}
	}
	// Container.
	if ( isset( $attr['backgroundColor'] ) || isset( $attr['borderColor'] ) || isset( $attr['borderWidth'] ) ) {
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item {';
		if ( isset( $attr['backgroundColor'] ) ) {
			$css .= 'background-color:' . kbp_post_grid_color_output( $attr['backgroundColor'] ) . ';';
		}
		if ( isset( $attr['borderColor'] ) ) {
			$bcoloralpha = ( isset( $attr['borderOpacity'] ) ? $attr['borderOpacity'] : 1 );
			$bcolor = kbp_post_grid_color_output( $attr['borderColor'], $bcoloralpha );
			$css .= 'border-color:' . $bcolor . ';';
		}
		if ( isset( $attr['borderWidth'] ) && is_array( $attr['borderWidth'] ) ) {
			$css .= 'border-width:' . $attr['borderWidth'][0] . 'px ' . $attr['borderWidth'][1] . 'px ' . $attr['borderWidth'][2] . 'px ' . $attr['borderWidth'][3] . 'px;';
		}
		$css .= '}';
	}
	if ( isset( $attr['containerPadding'] ) ) {
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item {';
		if ( isset( $attr['containerPadding'] ) && is_array( $attr['containerPadding'] ) ) {
			$css .= 'padding:' . $attr['containerPadding'][0] . 'px ' . $attr['containerPadding'][1] . 'px ' . $attr['containerPadding'][2] . 'px ' . $attr['containerPadding'][3] . 'px;';
		}
		$css .= '}';
	}
	// Content.
	if ( isset( $attr['textAlign'] ) && ! empty( $attr['textAlign'] ) ) {
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-item-inner {';
		$css .= 'text-align:' . $attr['textAlign'] . ';';
		$css .= '}';
		if ( 'right' === $attr['textAlign'] ) {
			$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-item-inner {';
			$css .= 'justify-content: flex-end;';
			$css .= '}';
		}
		if ( 'left' === $attr['textAlign'] ) {
			$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-item-inner {';
			$css .= 'justify-content: flex-start;';
			$css .= '}';
		}
	}
	if ( isset( $attr['contentBackground'] ) || isset( $attr['contentBackgroundOpacity'] ) ) {
		$overcoloralpha = ( isset( $attr['contentBackgroundOpacity'] ) ? $attr['contentBackgroundOpacity'] : 0 );
		$overcolorhex = ( isset( $attr['contentBackground'] ) ? $attr['contentBackground'] : '#1768ea' );
		$overcolor = kbp_post_grid_color_output( $overcolorhex, $overcoloralpha );
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-overlay-color {';
		$css .= 'background-color:' . $overcolor . ';';
		$css .= '}';
	}
	if ( isset( $attr['contentBorder'] ) || isset( $attr['contentBorderOpacity'] ) || isset( $attr['contentBorderWidth'] ) || isset( $attr['contentBorderWidth'] ) ) {
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-overlay-border {';
		if ( isset( $attr['contentBorder'] ) || isset( $attr['contentBorderOpacity'] ) ) {
			$bcoloralpha = ( isset( $attr['contentBorderOpacity'] ) ? $attr['contentBorderOpacity'] : 0 );
			$bcolorhex = ( isset( $attr['contentBorder'] ) ? $attr['contentBorder'] : '#ffffff' );
			$bcolor = kbp_post_grid_color_output( $bcolorhex, $bcoloralpha );
			$css .= 'border-color:' . $bcolor . ';';
		}
		if ( isset( $attr['contentBorderWidth'] ) && is_array( $attr['contentBorderWidth'] ) ) {
			$css .= 'border-width:' . $attr['contentBorderWidth'][0] . 'px ' . $attr['contentBorderWidth'][1] . 'px ' . $attr['contentBorderWidth'][2] . 'px ' . $attr['contentBorderWidth'][3] . 'px;';
		}
		if ( isset( $attr['contentBorderOffset'] ) && is_numeric( $attr['contentBorderOffset'] ) ) {
			$css .= 'top:' . $attr['contentBorderOffset'] . 'px;';
			$css .= 'right:' . $attr['contentBorderOffset'] . 'px;';
			$css .= 'bottom:' . $attr['contentBorderOffset'] . 'px;';
			$css .= 'left:' . $attr['contentBorderOffset'] . 'px;';
		}
		$css .= '}';
	}
	if ( isset( $attr['contentHoverBackground'] ) || isset( $attr['contentHoverBackgroundOpacity'] ) ) {
		$overcoloralpha = ( isset( $attr['contentHoverBackgroundOpacity'] ) ? $attr['contentHoverBackgroundOpacity'] : 0.5 );
		$overcolorhex = ( isset( $attr['contentHoverBackground'] ) ? $attr['contentHoverBackground'] : '#1768ea' );
		$overcolor = kbp_post_grid_color_output( $overcolorhex, $overcoloralpha );
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item:hover .kb-portfolio-overlay-color {';
		$css .= 'background-color:' . $overcolor . ';';
		$css .= '}';
	}
	if ( isset( $attr['contentHoverBorder'] ) || isset( $attr['contentHoverBorderOpacity'] ) || isset( $attr['contentHoverBorderOffset'] ) ) {
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item:hover .kb-portfolio-overlay-border {';
		if ( isset( $attr['contentHoverBorder'] ) || isset( $attr['contentHoverBorderOpacity'] ) ) {
			$bcoloralpha = ( isset( $attr['contentHoverBorderOpacity'] ) ? $attr['contentHoverBorderOpacity'] : 0.8 );
			$bcolorhex = ( isset( $attr['contentHoverBorder'] ) ? $attr['contentHoverBorder'] : '#ffffff' );
			$bcolor = kbp_post_grid_color_output( $bcolorhex, $bcoloralpha );
			$css .= 'border-color:' . $bcolor . ';';
		}
		if ( isset( $attr['contentHoverBorderOffset'] ) && is_numeric( $attr['contentHoverBorderOffset'] ) ) {
			$css .= 'top:' . $attr['contentHoverBorderOffset'] . 'px;';
			$css .= 'right:' . $attr['contentHoverBorderOffset'] . 'px;';
			$css .= 'bottom:' . $attr['contentHoverBorderOffset'] . 'px;';
			$css .= 'left:' . $attr['contentHoverBorderOffset'] . 'px;';
		}
		$css .= '}';
	}
	// Title.
	if ( isset( $attr['titleColor'] ) || isset( $attr['titleFont'] ) ) {
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item .entry-title {';
		if ( isset( $attr['titleColor'] ) && ! empty( $attr['titleColor'] ) ) {
			$css .= 'color:' . kbp_post_grid_color_output( $attr['titleColor'] ) . ';';
		}
		if ( isset( $attr['titlePadding'] ) && is_array( $attr['titlePadding'] ) ) {
			$css .= 'padding:' . $attr['titlePadding'][0] . 'px ' . $attr['titlePadding'][1] . 'px ' . $attr['titlePadding'][2] . 'px ' . $attr['titlePadding'][3] . 'px;';
		}
		if ( isset( $attr['titleMargin'] ) && is_array( $attr['titleMargin'] ) ) {
			$css .= 'margin:' . $attr['titleMargin'][0] . 'px ' . $attr['titleMargin'][1] . 'px ' . $attr['titleMargin'][2] . 'px ' . $attr['titleMargin'][3] . 'px;';
		}
		if ( isset( $attr['titleFont'] ) && is_array( $attr['titleFont'] ) && isset( $attr['titleFont'][0] ) && is_array( $attr['titleFont'][0] ) ) {
			$title_font = $attr['titleFont'][0];
			if ( isset( $title_font['size'] ) && is_array( $title_font['size'] ) && ! empty( $title_font['size'][0] ) ) {
				$css .= 'font-size:' . $title_font['size'][0] . ( ! isset( $title_font['sizeType'] ) ? 'px' : $title_font['sizeType'] ) . ';';
			}
			if ( isset( $title_font['lineHeight'] ) && is_array( $title_font['lineHeight'] ) && ! empty( $title_font['lineHeight'][0] ) ) {
				$css .= 'line-height:' . $title_font['lineHeight'][0] . ( ! isset( $title_font['lineType'] ) ? 'px' : $title_font['lineType'] ) . ';';
			}
			if ( isset( $title_font['letterSpacing'] ) && ! empty( $title_font['letterSpacing'] ) ) {
				$css .= 'letter-spacing:' . $title_font['letterSpacing'] .  'px;';
			}
			if ( isset( $title_font['textTransform'] ) && ! empty( $title_font['textTransform'] ) ) {
				$css .= 'text-transform:' . $title_font['textTransform'] .  ';';
			}
			if ( isset( $title_font['family'] ) && ! empty( $title_font['family'] ) ) {
				$css .= 'font-family:' . $title_font['family'] .  ';';
			}
			if ( isset( $title_font['style'] ) && ! empty( $title_font['style'] ) ) {
				$css .= 'font-style:' . $title_font['style'] .  ';';
			}
			if ( isset( $title_font['weight'] ) && ! empty( $title_font['weight'] ) ) {
				$css .= 'font-weight:' . $title_font['weight'] .  ';';
			}
		}
		$css .= '}';
		if ( isset( $attr['titleFont'] ) && is_array( $attr['titleFont'] ) && isset( $attr['titleFont'][0] ) && is_array( $attr['titleFont'][0] ) && ( ( isset( $attr['titleFont'][0]['size'] ) && is_array( $attr
		['titleFont'][0]['size'] ) && isset( $attr['titleFont'][0]['size'][1] ) && ! empty( $attr['titleFont'][0]['size'][1] ) ) || ( isset( $attr['titleFont'][0]['lineHeight'] ) && is_array( $attr
		['titleFont'][0]['lineHeight'] ) && isset( $attr['titleFont'][0]['lineHeight'][1] ) && ! empty( $attr['titleFont'][0]['lineHeight'][1] ) ) ) ) {
			$css .= '@media (min-width: 767px) and (max-width: 1024px) {';
			$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item .entry-title {';
			if ( isset( $attr['titleFont'][0]['size'][1] ) && ! empty( $attr['titleFont'][0]['size'][1] ) ) {
				$css .= 'font-size:' . $attr['titleFont'][0]['size'][1] . ( ! isset( $attr['titleFont'][0]['sizeType'] ) ? 'px' : $attr['titleFont'][0]['sizeType'] ) . ';';
			}
			if ( isset( $attr['titleFont'][0]['lineHeight'][1] ) && ! empty( $attr['titleFont'][0]['lineHeight'][1] ) ) {
				$css .= 'line-height:' . $attr['titleFont'][0]['lineHeight'][1] . ( ! isset( $attr['titleFont'][0]['lineType'] ) ? 'px' : $attr['titleFont'][0]['lineType'] ) . ';';
			}
			$css .= '}';
			$css .= '}';
		}
		if ( isset( $attr['titleFont'] ) && is_array( $attr['titleFont'] ) && isset( $attr['titleFont'][0] ) && is_array( $attr['titleFont'][0] ) && ( ( isset( $attr['titleFont'][0]['size'] ) && is_array( $attr
		['titleFont'][0]['size'] ) && isset( $attr['titleFont'][0]['size'][2] ) && ! empty( $attr['titleFont'][0]['size'][2] ) ) || ( isset( $attr['titleFont'][0]['lineHeight'] ) && is_array( $attr
		['titleFont'][0]['lineHeight'] ) && isset( $attr['titleFont'][0]['lineHeight'][2] ) && ! empty( $attr['titleFont'][0]['lineHeight'][2] ) ) ) ) {
			$css .= '@media (max-width: 767px) {';
			$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item .entry-title {';
				if ( isset( $attr['titleFont'][0]['size'][2] ) && ! empty( $attr['titleFont'][0]['size'][2] ) ) {
					$css .= 'font-size:' . $attr['titleFont'][0]['size'][2] . ( ! isset( $attr['titleFont'][0]['sizeType'] ) ? 'px' : $attr['titleFont'][0]['sizeType'] ) . ';';
				}
				if ( isset( $attr['titleFont'][0]['lineHeight'][2] ) && ! empty( $attr['titleFont'][0]['lineHeight'][2] ) ) {
					$css .= 'line-height:' . $attr['titleFont'][0]['lineHeight'][2] . ( ! isset( $attr['titleFont'][0]['lineType'] ) ? 'px' : $attr['titleFont'][0]['lineType'] ) . ';';
				}
			$css .= '}';
			$css .= '}';
		}
	}
	// Tax
	if ( isset( $attr['taxColor'] ) || isset( $attr['taxFont'] ) ) {
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item .kb-blocks-portfolio-taxonomies {';
		if ( isset( $attr['taxColor'] ) && ! empty( $attr['taxColor'] ) ) {
			$css .= 'color:' . kbp_post_grid_color_output( $attr['taxColor'] ) . ';';
		}
		if ( isset( $attr['taxFont'] ) && is_array( $attr['taxFont'] ) && isset( $attr['taxFont'][0] ) && is_array( $attr['taxFont'][0] ) ) {
			$title_font = $attr['taxFont'][0];
			if ( isset( $title_font['size'] ) && is_array( $title_font['size'] ) && ! empty( $title_font['size'][0] ) ) {
				$css .= 'font-size:' . $title_font['size'][0] . ( ! isset( $title_font['sizeType'] ) ? 'px' : $title_font['sizeType'] ) . ';';
			}
			if ( isset( $title_font['lineHeight'] ) && is_array( $title_font['lineHeight'] ) && ! empty( $title_font['lineHeight'][0] ) ) {
				$css .= 'line-height:' . $title_font['lineHeight'][0] . ( ! isset( $title_font['lineType'] ) ? 'px' : $title_font['lineType'] ) . ';';
			}
			if ( isset( $title_font['letterSpacing'] ) && ! empty( $title_font['letterSpacing'] ) ) {
				$css .= 'letter-spacing:' . $title_font['letterSpacing'] .  'px;';
			}
			if ( isset( $title_font['textTransform'] ) && ! empty( $title_font['textTransform'] ) ) {
				$css .= 'text-transform:' . $title_font['textTransform'] .  ';';
			}
			if ( isset( $title_font['family'] ) && ! empty( $title_font['family'] ) ) {
				$css .= 'font-family:' . $title_font['family'] .  ';';
			}
			if ( isset( $title_font['style'] ) && ! empty( $title_font['style'] ) ) {
				$css .= 'font-style:' . $title_font['style'] .  ';';
			}
			if ( isset( $title_font['weight'] ) && ! empty( $title_font['weight'] ) ) {
				$css .= 'font-weight:' . $title_font['weight'] .  ';';
			}
		}
		$css .= '}';
		if ( isset( $attr['taxFont'] ) && is_array( $attr['taxFont'] ) && isset( $attr['taxFont'][0] ) && is_array( $attr['taxFont'][0] ) && ( ( isset( $attr['taxFont'][0]['size'] ) && is_array( $attr
		['taxFont'][0]['size'] ) && isset( $attr['taxFont'][0]['size'][1] ) && ! empty( $attr['taxFont'][0]['size'][1] ) ) || ( isset( $attr['taxFont'][0]['lineHeight'] ) && is_array( $attr
		['taxFont'][0]['lineHeight'] ) && isset( $attr['taxFont'][0]['lineHeight'][1] ) && ! empty( $attr['taxFont'][0]['lineHeight'][1] ) ) ) ) {
			$css .= '@media (min-width: 767px) and (max-width: 1024px) {';
			$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item .kb-blocks-portfolio-taxonomies {';
			if ( isset( $attr['taxFont'][0]['size'][1] ) && ! empty( $attr['taxFont'][0]['size'][1] ) ) {
				$css .= 'font-size:' . $attr['taxFont'][0]['size'][1] . ( ! isset( $attr['taxFont'][0]['sizeType'] ) ? 'px' : $attr['taxFont'][0]['sizeType'] ) . ';';
			}
			if ( isset( $attr['taxFont'][0]['lineHeight'][1] ) && ! empty( $attr['taxFont'][0]['lineHeight'][1] ) ) {
				$css .= 'line-height:' . $attr['taxFont'][0]['lineHeight'][1] . ( ! isset( $attr['taxFont'][0]['lineType'] ) ? 'px' : $attr['taxFont'][0]['lineType'] ) . ';';
			}
			$css .= '}';
			$css .= '}';
		}
		if ( isset( $attr['taxFont'] ) && is_array( $attr['taxFont'] ) && isset( $attr['taxFont'][0] ) && is_array( $attr['taxFont'][0] ) && ( ( isset( $attr['taxFont'][0]['size'] ) && is_array( $attr
		['taxFont'][0]['size'] ) && isset( $attr['taxFont'][0]['size'][2] ) && ! empty( $attr['taxFont'][0]['size'][2] ) ) || ( isset( $attr['taxFont'][0]['lineHeight'] ) && is_array( $attr
		['taxFont'][0]['lineHeight'] ) && isset( $attr['taxFont'][0]['lineHeight'][2] ) && ! empty( $attr['taxFont'][0]['lineHeight'][2] ) ) ) ) {
			$css .= '@media (max-width: 767px) {';
			$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item .kb-blocks-portfolio-taxonomies {';
				if ( isset( $attr['taxFont'][0]['size'][2] ) && ! empty( $attr['taxFont'][0]['size'][2] ) ) {
					$css .= 'font-size:' . $attr['taxFont'][0]['size'][2] . ( ! isset( $attr['taxFont'][0]['sizeType'] ) ? 'px' : $attr['taxFont'][0]['sizeType'] ) . ';';
				}
				if ( isset( $attr['taxFont'][0]['lineHeight'][2] ) && ! empty( $attr['taxFont'][0]['lineHeight'][2] ) ) {
					$css .= 'line-height:' . $attr['taxFont'][0]['lineHeight'][2] . ( ! isset( $attr['taxFont'][0]['lineType'] ) ? 'px' : $attr['taxFont'][0]['lineType'] ) . ';';
				}
			$css .= '}';
			$css .= '}';
		}
	}
	if ( isset( $attr['taxLinkColor'] ) && ! empty( $attr['taxLinkColor'] ) ) {
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item .kb-blocks-portfolio-taxonomies a {';
			$css .= 'color:' . kbp_post_grid_color_output( $attr['taxLinkColor'] ) . ';';
		$css .= '}';
	}
	if ( isset( $attr['taxLinkHoverColor'] ) && ! empty( $attr['taxLinkHoverColor'] ) ) {
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item .kb-blocks-portfolio-taxonomies a:hover {';
			$css .= 'color:' . kbp_post_grid_color_output( $attr['taxLinkHoverColor'] ) . ';';
		$css .= '}';
	}
	// Excerpt.
	if ( isset( $attr['excerptColor'] ) || isset( $attr['excerptFont'] ) ) {
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item .kb-portfolio-loop-excerpt {';
		if ( isset( $attr['excerptColor'] ) && ! empty( $attr['excerptColor'] ) ) {
			$css .= 'color:' . kbp_post_grid_color_output( $attr['excerptColor'] ) . ';';
		}
		if ( isset( $attr['excerptFont'] ) && is_array( $attr['excerptFont'] ) && isset( $attr['excerptFont'][0] ) && is_array( $attr['excerptFont'][0] ) ) {
			$excerpt_font = $attr['excerptFont'][0];
			if ( isset( $excerpt_font['size'] ) && is_array( $excerpt_font['size'] ) && ! empty( $excerpt_font['size'][0] ) ) {
				$css .= 'font-size:' . $excerpt_font['size'][0] . ( ! isset( $excerpt_font['sizeType'] ) ? 'px' : $excerpt_font['sizeType'] ) . ';';
			}
			if ( isset( $excerpt_font['lineHeight'] ) && is_array( $excerpt_font['lineHeight'] ) && ! empty( $excerpt_font['lineHeight'][0] ) ) {
				$css .= 'line-height:' . $excerpt_font['lineHeight'][0] . ( ! isset( $excerpt_font['lineType'] ) ? 'px' : $excerpt_font['lineType'] ) . ';';
			}
			if ( isset( $excerpt_font['letterSpacing'] ) && ! empty( $excerpt_font['letterSpacing'] ) ) {
				$css .= 'letter-spacing:' . $excerpt_font['letterSpacing'] .  'px;';
			}
			if ( isset( $excerpt_font['family'] ) && ! empty( $excerpt_font['family'] ) ) {
				$css .= 'font-family:' . $excerpt_font['family'] .  ';';
			}
			if ( isset( $excerpt_font['style'] ) && ! empty( $excerpt_font['style'] ) ) {
				$css .= 'font-style:' . $excerpt_font['style'] .  ';';
			}
			if ( isset( $excerpt_font['weight'] ) && ! empty( $excerpt_font['weight'] ) ) {
				$css .= 'font-weight:' . $excerpt_font['weight'] .  ';';
			}
		}
		$css .= '}';
		if ( isset( $attr['excerptFont'] ) && is_array( $attr['excerptFont'] ) && isset( $attr['excerptFont'][0] ) && is_array( $attr['excerptFont'][0] ) && ( ( isset( $attr['excerptFont'][0]['size'] ) && is_array( $attr
		['excerptFont'][0]['size'] ) && isset( $attr['excerptFont'][0]['size'][1] ) && ! empty( $attr['excerptFont'][0]['size'][1] ) ) || ( isset( $attr['excerptFont'][0]['lineHeight'] ) && is_array( $attr
		['excerptFont'][0]['lineHeight'] ) && isset( $attr['excerptFont'][0]['lineHeight'][1] ) && ! empty( $attr['excerptFont'][0]['lineHeight'][1] ) ) ) ) {
			$css .= '@media (min-width: 767px) and (max-width: 1024px) {';
			$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item .kb-portfolio-loop-excerpt {';
			if ( isset( $attr['excerptFont'][0]['size'][1] ) && ! empty( $attr['excerptFont'][0]['size'][1] ) ) {
				$css .= 'font-size:' . $attr['excerptFont'][0]['size'][1] . ( ! isset( $attr['excerptFont'][0]['sizeType'] ) ? 'px' : $attr['excerptFont'][0]['sizeType'] ) . ';';
			}
			if ( isset( $attr['excerptFont'][0]['lineHeight'][1] ) && ! empty( $attr['excerptFont'][0]['lineHeight'][1] ) ) {
				$css .= 'line-height:' . $attr['excerptFont'][0]['lineHeight'][1] . ( ! isset( $attr['excerptFont'][0]['lineType'] ) ? 'px' : $attr['excerptFont'][0]['lineType'] ) . ';';
			}
			$css .= '}';
			$css .= '}';
		}
		if ( isset( $attr['excerptFont'] ) && is_array( $attr['excerptFont'] ) && isset( $attr['excerptFont'][0] ) && is_array( $attr['excerptFont'][0] ) && ( ( isset( $attr['excerptFont'][0]['size'] ) && is_array( $attr
		['excerptFont'][0]['size'] ) && isset( $attr['excerptFont'][0]['size'][2] ) && ! empty( $attr['excerptFont'][0]['size'][2] ) ) || ( isset( $attr['excerptFont'][0]['lineHeight'] ) && is_array( $attr
		['excerptFont'][0]['lineHeight'] ) && isset( $attr['excerptFont'][0]['lineHeight'][2] ) && ! empty( $attr['excerptFont'][0]['lineHeight'][2] ) ) ) ) {
			$css .= '@media (max-width: 767px) {';
			$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item .kb-portfolio-loop-excerpt {';
				if ( isset( $attr['excerptFont'][0]['size'][2] ) && ! empty( $attr['excerptFont'][0]['size'][2] ) ) {
					$css .= 'font-size:' . $attr['excerptFont'][0]['size'][2] . ( ! isset( $attr['excerptFont'][0]['sizeType'] ) ? 'px' : $attr['excerptFont'][0]['sizeType'] ) . ';';
				}
				if ( isset( $attr['excerptFont'][0]['lineHeight'][2] ) && ! empty( $attr['excerptFont'][0]['lineHeight'][2] ) ) {
					$css .= 'line-height:' . $attr['excerptFont'][0]['lineHeight'][2] . ( ! isset( $attr['excerptFont'][0]['lineType'] ) ? 'px' : $attr['excerptFont'][0]['lineType'] ) . ';';
				}
			$css .= '}';
			$css .= '}';
		}
	}
	// Filter.
	if ( isset( $attr['displayFilter'] ) && true === $attr['displayFilter'] && isset( $attr['filterAlign'] ) && ! empty( $attr['filterAlign'] ) ) {
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-filter-container {';
		$css .= 'text-align:' . $attr['filterAlign'] . ';';
		$css .= '}';
		if ( 'right' === $attr['filterAlign'] ) {
			$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-filter-container {';
			$css .= 'justify-content: flex-end;';
			$css .= '}';
		}
		if ( 'left' === $attr['filterAlign'] ) {
			$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-filter-container {';
			$css .= 'justify-content: flex-start;';
			$css .= '}';
		}
	}
	// Filter Font.
	if ( isset( $attr['filterColor'] ) || isset( $attr['filterBorderRadius'] ) || isset( $attr['filterFont'] ) || isset( $attr['filterBorder'] ) || isset( $attr['filterBackground'] ) || isset( $attr['filterBorderWidth'] ) || isset( $attr['filterPadding'] ) || isset( $attr['filterMargin'] )  ) {
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-filter-item {';
		if ( isset( $attr['filterColor'] ) && ! empty( $attr['filterColor'] ) ) {
			$css .= 'color:' . kbp_post_grid_color_output( $attr['filterColor'] ) . ';';
		}
		if ( isset( $attr['filterBorderRadius'] ) && is_numeric( $attr['filterBorderRadius'] ) ) {
			$css .= 'border-radius:' . $attr['filterBorderRadius'] . 'px;';
		}
		if ( isset( $attr['filterBackground'] ) && ! empty( $attr['filterBackground'] ) ) {
			$bcoloralpha = ( isset( $attr['filterBackgroundOpacity'] ) ? $attr['filterBackgroundOpacity'] : 1 );
			$bcolorhex = ( isset( $attr['filterBackground'] ) ? $attr['filterBackground'] : '#ffffff' );
			$bcolor = kbp_post_grid_color_output( $bcolorhex, $bcoloralpha );
			$css .= 'background:' . $bcolor . ';';
		}
		if ( isset( $attr['filterBorder'] ) && ! empty( $attr['filterBorder'] ) ) {
			$bcoloralpha = ( isset( $attr['filterBorderOpacity'] ) ? $attr['filterBorderOpacity'] : 1 );
			$bcolorhex = ( isset( $attr['filterBorder'] ) ? $attr['filterBorder'] : '#ffffff' );
			$bcolor = kbp_post_grid_color_output( $bcolorhex, $bcoloralpha );
			$css .= 'border-color:' . $bcolor . ';';
		}
		if ( isset( $attr['filterBorderWidth'] ) && is_array( $attr['filterBorderWidth'] ) && isset( $attr['filterBorderWidth'][0] ) && is_numeric( $attr['filterBorderWidth'][0] ) ) {
			$css .= 'border-width:' . $attr['filterBorderWidth'][0] . 'px ' . $attr['filterBorderWidth'][1] . 'px ' . $attr['filterBorderWidth'][2] . 'px ' . $attr['filterBorderWidth'][3] . 'px;';
		}
		if ( isset( $attr['filterPadding'] ) && is_array( $attr['filterPadding'] ) ) {
			$css .= 'padding:' . $attr['filterPadding'][0] . 'px ' . $attr['filterPadding'][1] . 'px ' . $attr['filterPadding'][2] . 'px ' . $attr['filterPadding'][3] . 'px;';
		}
		if ( isset( $attr['filterMargin'] ) && is_array( $attr['filterMargin'] ) ) {
			$css .= 'margin:' . $attr['filterMargin'][0] . 'px ' . $attr['filterMargin'][1] . 'px ' . $attr['filterMargin'][2] . 'px ' . $attr['filterMargin'][3] . 'px;';
		}
		if ( isset( $attr['filterFont'] ) && is_array( $attr['filterFont'] ) && isset( $attr['filterFont'][0] ) && is_array( $attr['filterFont'][0] ) ) {
			$filter_font = $attr['filterFont'][0];
			if ( isset( $filter_font['size'] ) && is_array( $filter_font['size'] ) && ! empty( $filter_font['size'][0] ) ) {
				$css .= 'font-size:' . $filter_font['size'][0] . ( ! isset( $filter_font['sizeType'] ) ? 'px' : $filter_font['sizeType'] ) . ';';
			}
			if ( isset( $filter_font['lineHeight'] ) && is_array( $filter_font['lineHeight'] ) && ! empty( $filter_font['lineHeight'][0] ) ) {
				$css .= 'line-height:' . $filter_font['lineHeight'][0] . ( ! isset( $filter_font['lineType'] ) ? 'px' : $filter_font['lineType'] ) . ';';
			}
			if ( isset( $filter_font['letterSpacing'] ) && ! empty( $filter_font['letterSpacing'] ) ) {
				$css .= 'letter-spacing:' . $filter_font['letterSpacing'] .  'px;';
			}
			if ( isset( $filter_font['textTransform'] ) && ! empty( $filter_font['textTransform'] ) ) {
				$css .= 'text-transform:' . $filter_font['textTransform'] . ';';
			}
			if ( isset( $filter_font['family'] ) && ! empty( $filter_font['family'] ) ) {
				$css .= 'font-family:' . $filter_font['family'] . ';';
			}
			if ( isset( $filter_font['style'] ) && ! empty( $filter_font['style'] ) ) {
				$css .= 'font-style:' . $filter_font['style'] . ';';
			}
			if ( isset( $filter_font['weight'] ) && ! empty( $filter_font['weight'] ) ) {
				$css .= 'font-weight:' . $filter_font['weight'] . ';';
			}
		}
		$css .= '}';
		if ( isset( $attr['filterFont'] ) && is_array( $attr['filterFont'] ) && isset( $attr['filterFont'][0] ) && is_array( $attr['filterFont'][0] ) && ( ( isset( $attr['filterFont'][0]['size'] ) && is_array( $attr
		['filterFont'][0]['size'] ) && isset( $attr['filterFont'][0]['size'][1] ) && ! empty( $attr['filterFont'][0]['size'][1] ) ) || ( isset( $attr['filterFont'][0]['lineHeight'] ) && is_array( $attr
		['filterFont'][0]['lineHeight'] ) && isset( $attr['filterFont'][0]['lineHeight'][1] ) && ! empty( $attr['filterFont'][0]['lineHeight'][1] ) ) ) ) {
			$css .= '@media (min-width: 767px) and (max-width: 1024px) {';
			$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-filter-item {';
			if ( isset( $attr['filterFont'][0]['size'][1] ) && ! empty( $attr['filterFont'][0]['size'][1] ) ) {
				$css .= 'font-size:' . $attr['filterFont'][0]['size'][1] . ( ! isset( $attr['filterFont'][0]['sizeType'] ) ? 'px' : $attr['filterFont'][0]['sizeType'] ) . ';';
			}
			if ( isset( $attr['filterFont'][0]['lineHeight'][1] ) && ! empty( $attr['filterFont'][0]['lineHeight'][1] ) ) {
				$css .= 'line-height:' . $attr['filterFont'][0]['lineHeight'][1] . ( ! isset( $attr['filterFont'][0]['lineType'] ) ? 'px' : $attr['filterFont'][0]['lineType'] ) . ';';
			}
			$css .= '}';
			$css .= '}';
		}
		if ( isset( $attr['filterFont'] ) && is_array( $attr['filterFont'] ) && isset( $attr['filterFont'][0] ) && is_array( $attr['filterFont'][0] ) && ( ( isset( $attr['filterFont'][0]['size'] ) && is_array( $attr
		['filterFont'][0]['size'] ) && isset( $attr['filterFont'][0]['size'][2] ) && ! empty( $attr['filterFont'][0]['size'][2] ) ) || ( isset( $attr['filterFont'][0]['lineHeight'] ) && is_array( $attr
		['filterFont'][0]['lineHeight'] ) && isset( $attr['filterFont'][0]['lineHeight'][2] ) && ! empty( $attr['filterFont'][0]['lineHeight'][2] ) ) ) ) {
			$css .= '@media (max-width: 767px) {';
			$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-filter-item {';
				if ( isset( $attr['filterFont'][0]['size'][2] ) && ! empty( $attr['filterFont'][0]['size'][2] ) ) {
					$css .= 'font-size:' . $attr['filterFont'][0]['size'][2] . ( ! isset( $attr['filterFont'][0]['sizeType'] ) ? 'px' : $attr['filterFont'][0]['sizeType'] ) . ';';
				}
				if ( isset( $attr['filterFont'][0]['lineHeight'][2] ) && ! empty( $attr['filterFont'][0]['lineHeight'][2] ) ) {
					$css .= 'line-height:' . $attr['filterFont'][0]['lineHeight'][2] . ( ! isset( $attr['filterFont'][0]['lineType'] ) ? 'px' : $attr['filterFont'][0]['lineType'] ) . ';';
				}
			$css .= '}';
			$css .= '}';
		}
	}
	if ( isset( $attr['filterHoverColor'] ) || isset( $attr['filterHoverBorder'] ) || isset( $attr['filterHoverBackground'] ) ) {
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-filter-item:hover, .kb-portfolio-loop' . $unique_id . ' .kb-filter-item:focus {';
		if ( isset( $attr['filterHoverColor'] ) && ! empty( $attr['filterHoverColor'] ) ) {
			$css .= 'color:' . kbp_post_grid_color_output( $attr['filterHoverColor'] ) . ';';
		}
		if ( isset( $attr['filterHoverBackground'] ) && ! empty( $attr['filterHoverBackground'] ) ) {
			$bcoloralpha = ( isset( $attr['filterHoverBackgroundOpacity'] ) ? $attr['filterHoverBackgroundOpacity'] : 1 );
			$bcolorhex = ( isset( $attr['filterHoverBackground'] ) ? $attr['filterHoverBackground'] : '#ffffff' );
			$bcolor = kbp_post_grid_color_output( $bcolorhex, $bcoloralpha );
			$css .= 'background:' . $bcolor . ';';
		}
		if ( isset( $attr['filterHoverBorder'] ) && ! empty( $attr['filterHoverBorder'] ) ) {
			$bcoloralpha = ( isset( $attr['filterHoverBorderOpacity'] ) ? $attr['filterHoverBorderOpacity'] : 1 );
			$bcolorhex = ( isset( $attr['filterHoverBorder'] ) ? $attr['filterHoverBorder'] : '#ffffff' );
			$bcolor = kbp_post_grid_color_output( $bcolorhex, $bcoloralpha );
			$css .= 'border-color:' . $bcolor . ';';
		}
		$css .= '}';
	}
	if ( isset( $attr['filterActiveColor'] ) || isset( $attr['filterActiveBorder'] ) || isset( $attr['filterActiveBackground'] ) ) {
		$css .= '.kb-portfolio-loop' . $unique_id . ' .kb-filter-item.is-active {';
		if ( isset( $attr['filterActiveColor'] ) && ! empty( $attr['filterActiveColor'] ) ) {
			$css .= 'color:' . kbp_post_grid_color_output( $attr['filterActiveColor'] ) . ';';
		}
		if ( isset( $attr['filterActiveBackground'] ) && ! empty( $attr['filterActiveBackground'] ) ) {
			$bcoloralpha = ( isset( $attr['filterActiveBackgroundOpacity'] ) ? $attr['filterActiveBackgroundOpacity'] : 1 );
			$bcolorhex = ( isset( $attr['filterActiveBackground'] ) ? $attr['filterActiveBackground'] : '#ffffff' );
			$bcolor = kbp_post_grid_color_output( $bcolorhex, $bcoloralpha );
			$css .= 'background:' . $bcolor . ';';
		}
		if ( isset( $attr['filterActiveBorder'] ) && ! empty( $attr['filterActiveBorder'] ) ) {
			$bcoloralpha = ( isset( $attr['filterActiveBorderOpacity'] ) ? $attr['filterActiveBorderOpacity'] : 1 );
			$bcolorhex = ( isset( $attr['filterActiveBorder'] ) ? $attr['filterActiveBorder'] : '#ffffff' );
			$bcolor = kbp_post_grid_color_output( $bcolorhex, $bcoloralpha );
			$css .= 'border-color:' . $bcolor . ';';
		}
		$css .= '}';
	}
	return $css;
}
/**
 * Get Color Output
 *
 * @param string $color the color string
 * @param string $opacity the alpha level
 */
function kbp_post_grid_color_output( $color, $opacity = null ) {
	if ( strpos( $color, 'palette' ) === 0 ) {
		$color = 'var(--global-' . $color . ')';
	} else if ( isset( $opacity ) && is_numeric( $opacity ) ) {
		$color = kadence_blocks_pro_hex2rgba( $color, $opacity );
	}
	return $color;
}
function kadence_blocks_pro_hex2rgba( $hex, $alpha ) {
	if ( empty( $hex ) ) {
		return '';
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


