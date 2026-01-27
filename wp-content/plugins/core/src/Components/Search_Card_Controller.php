<?php declare(strict_types=1);

namespace Tribe\Plugin\Components;

use Tribe\Plugin\Blocks\Helpers\Block_Animation_Attributes;

class Search_Card_Controller extends Abstract_Controller {

	private int $post_id;
	private string $classes;
	private Block_Animation_Attributes|false $animation_attributes;
	private string $animation_classes;
	private string $animation_styles;
	private string $post_type;
	private \WP_Post_Type|null $post_type_object;
	private int|false $image_id;
	private string $title;
	private int $author_id;
	private string $author;
	private string $date;
	private string $excerpt;
	private string $permalink;

	public function __construct( array $args = [] ) {
		$this->post_id              = $args['post_id'] ?? 0;
		$this->animation_attributes = $args['animation_attributes'];
		$this->animation_classes    = $this->animation_attributes !== false ? $this->animation_attributes->get_classes() : '';
		$this->animation_styles     = $this->animation_attributes !== false ? $this->animation_attributes->get_styles() : '';
		$this->classes              = 'c-search-card';
		$this->post_type            = get_post_type( $this->post_id );
		$this->post_type_object     = get_post_type_object( $this->post_type );
		$this->image_id             = get_post_thumbnail_id( $this->post_id );
		$this->title                = get_the_title( $this->post_id );
		$this->author_id            = (int) get_post_field( 'post_author', $this->post_id );
		$this->author               = get_the_author_meta( 'display_name', $this->author_id );
		$this->date                 = get_the_date( 'M j, Y', $this->post_id );
		$this->excerpt              = get_the_excerpt( $this->post_id );
		$this->permalink            = get_the_permalink( $this->post_id );
	}

	public function get_classes(): string {
		if ( '' !== $this->animation_classes ) {
			$this->classes .= " $this->animation_classes";
		}

		return $this->classes;
	}

	public function get_styles(): string {
		return $this->animation_styles;
	}

	public function has_media(): bool {
		return false !== $this->image_id && 0 !== $this->image_id;
	}

	public function get_media(): string {
		return wp_get_attachment_image( $this->image_id, 'large' );
	}

	public function has_post_type(): bool {
		return null !== $this->post_type_object;
	}

	public function get_post_type_name(): string {
		return $this->post_type_object->labels->singular_name;
	}

	public function get_title(): string {
		return $this->title;
	}

	public function has_author(): bool {
		return '' !== $this->author;
	}

	public function get_author_name(): string {
		return $this->author;
	}

	public function has_date(): bool {
		return '' !== $this->date;
	}

	public function get_date(): string {
		return $this->date;
	}

	public function has_excerpt(): bool {
		return '' !== $this->excerpt;
	}

	public function get_excerpt(): string {
		return $this->excerpt;
	}

	public function get_permalink(): string {
		return $this->permalink;
	}

}
