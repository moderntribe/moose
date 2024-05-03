<?php declare(strict_types=1);

namespace Tribe\Plugin\Blocks\Filters;

use Tribe\Plugin\Blocks\Filters\Contracts\Block_Content_Filter;

class Post_Featured_Image_Filter extends Block_Content_Filter {

	public const BLOCK = 'core/post-featured-image';

	public function filter_block_content( string $block_content, array $parsed_block, object $block ): string {
		if ( $block_content === '' ) {
			$styles   = wp_style_engine_get_styles( $parsed_block['attrs']['style'] );
			$classes  = isset( $parsed_block['attrs']['align'] ) ? 'align' . $parsed_block['attrs']['align'] : '';
			$classes .= isset( $parsed_block['attrs']['className'] ) ? ' ' . $parsed_block['attrs']['className'] : '';

			return '<figure class="wp-block-post-featured-image '. $classes .'" style="'. $styles['css'] .'"><img class="wp-post-image" width="1680" height="945" src="'. esc_url( get_template_directory_uri() . '/assets/media/featured-image-placeholder.jpg' ) .'" alt="Post placeholder image" style="width: 100%; height: 100%; object-fit: cover;"></figure>';
		}

		return $block_content;
	}

}
