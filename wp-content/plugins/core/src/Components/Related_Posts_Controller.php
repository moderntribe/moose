<?php declare(strict_types=1);

namespace Tribe\Plugin\Components;

use Tribe\Plugin\Blocks\Helpers\Block_Animation_Attributes;
use Tribe\Plugin\Post_Types\Post\Post;

class Related_Posts_Controller extends Abstract_Controller {

	/**
	 * @var array <mixed>
	 */
	private array $attributes;

	/**
	 * @var array <mixed>
	 */
	private array $query_args;

	/**
	 * @var array <mixed>
	 */
	private array $chosen_posts;
	private string $classes;
	private Block_Animation_Attributes $animation_attributes;
	private string $animation_classes;
	private string $animation_styles;
	private int|false $post_id;
	private bool $has_automatic_selection;
	private int $posts_to_show;
	private string $block_layout;
	private \WP_Query $query;

	public function __construct( array $args = [] ) {
		$this->attributes              = $args['attributes'] ?? [];
		$this->post_id                 = get_the_ID();
		$this->classes                 = 'b-related-posts';
		$this->animation_attributes    = new Block_Animation_Attributes( $this->attributes );
		$this->animation_classes       = $this->animation_attributes->get_classes();
		$this->animation_styles        = $this->animation_attributes->get_styles();
		$this->has_automatic_selection = $this->attributes['hasAutomaticSelection'] ?? true;
		$this->chosen_posts            = $this->attributes['chosenPosts'] ?? [];
		$this->posts_to_show           = $this->attributes['postsToShow'] ? intval( $this->attributes['postsToShow'] ) : 3;
		$this->block_layout            = $this->attributes['layout'] ?? 'grid';
		$this->query_args              = [];
		$this->query                   = new \WP_Query();

		$this->set_query_args();
		$this->set_query();
	}

	public function should_bail_early(): bool {
		return ! $this->has_automatic_selection && empty( $this->chosen_posts );
	}

	public function get_query(): \WP_Query {
		return $this->query;
	}

	public function get_classes(): string {
		$this->classes .= " b-related-posts--layout-$this->block_layout";

		if ( '' !== $this->animation_classes ) {
			$this->classes .= " $this->animation_classes";
		}

		return $this->classes;
	}

	public function get_styles(): string {
		return $this->animation_styles;
	}

	private function set_query_args(): void {
		$this->query_args = [
			'post_type'      => Post::NAME,
			'post_status'    => 'publish',
			'posts_per_page' => (int) $this->posts_to_show,
			'post__not_in'   => [ $this->post_id ],
			'tax_query'      => [],
		];

		if ( ! $this->has_automatic_selection ) {
			unset(
				$this->query_args['tax_query'],
				$this->query_args['post__not_in'],
				$this->query_args['posts_per_page']
			);

			$this->query_args['post__in'] = array_map( static fn( $post ): int => intval( $post['id'] ), $this->chosen_posts );
			$this->query_args['orderby']  = 'post__in';

			return;
		}

		$post_terms = get_the_terms( $this->post_id, 'category' );

		if ( empty( $post_terms ) || is_wp_error( $post_terms ) ) {
			return;
		}

		$term_ids = wp_list_pluck( $post_terms, 'term_id' );

		$this->query_args['tax_query'][] = [
			'taxonomy' => 'category',
			'field'    => 'term_id',
			'terms'    => $term_ids,
		];
	}

	private function set_query(): void {
		$this->query = new \WP_Query( $this->query_args );
	}

}
