<?php declare(strict_types=1);

namespace Tribe\Plugin\Components\Traits;

use Tribe\Plugin\Taxonomies\Category\Category;

trait Post_Data {

	use Primary_Term;

	protected int|null $post_id             = null;
	private int|false $image_id             = false;
	private \WP_Term|null $primary_category = null;
	private string $post_title              = '';
	private string $author_id               = '0';
	private string $author                  = '';
	private string $date                    = '';
	private string $excerpt                 = '';
	private string $permalink               = '';

	public function set_post( int $post_id ): void {
		$this->post_id          = $post_id;
		$this->image_id         = get_post_thumbnail_id( $this->post_id );
		$this->primary_category = $this->get_primary_term( $this->post_id, Category::NAME );
		$this->post_title       = get_the_title( $this->post_id );
		$this->author_id        = get_post_field( 'post_author', $this->post_id );
		$this->date             = get_the_date( 'M j, Y', $this->post_id );
		$this->excerpt          = get_the_excerpt( $this->post_id );
		$this->permalink        = get_the_permalink( $this->post_id );

		$this->set_post_author();
	}

	public function has_media(): bool {
		return false !== $this->image_id && 0 !== $this->image_id;
	}

	public function get_media( string $image_size = 'large' ): string {
		return wp_get_attachment_image( $this->image_id, $image_size );
	}

	public function has_primary_category(): bool {
		return null !== $this->primary_category;
	}

	public function get_primary_category_name(): string {
		return $this->has_primary_category() ? $this->primary_category->name : '';
	}

	public function get_post_title(): string {
		return $this->post_title;
	}

	public function has_post_author(): bool {
		return '' !== $this->author;
	}

	public function get_post_author(): string {
		return $this->author;
	}

	public function has_post_date(): bool {
		return '' !== $this->date;
	}

	public function get_post_date(): string {
		return $this->date;
	}

	public function has_post_excerpt(): bool {
		return '' !== $this->excerpt;
	}


	public function get_post_excerpt(): string {
		return $this->excerpt;
	}

	public function get_post_permalink(): string {
		return $this->permalink;
	}

	private function set_post_author(): void {
		if ( '' === $this->author_id ) {
			$this->author = '';

			return;
		}

		$this->author = get_the_author_meta( 'display_name', (int) $this->author_id );
	}

}
