<?php declare(strict_types=1);

namespace Tribe\Plugin\Components\Blocks;

use Tribe\Plugin\Components\Abstracts\Abstract_Block_Controller;
use Tribe\Plugin\Components\Traits\Post_Data;

class Search_Card_Controller extends Abstract_Block_Controller {

	use Post_Data;

	private string $post_type;
	private \WP_Post_Type|null $post_type_object;

	public function __construct( array $args = [] ) {
		parent::__construct( $args );
		$this->set_post( $args['post_id'] ?? 0 );

		$this->post_type        = get_post_type( $this->post_id );
		$this->post_type_object = get_post_type_object( $this->post_type );
	}

	public function has_post_type(): bool {
		return null !== $this->post_type_object;
	}

	public function get_post_type_name(): string {
		return $this->post_type_object->labels->singular_name;
	}

}
