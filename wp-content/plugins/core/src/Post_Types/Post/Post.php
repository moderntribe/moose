<?php declare(strict_types=1);

namespace Tribe\Plugin\Post_Types\Post;

use Tribe\Libs\Post_Type\Post_Object;
use Tribe\Plugin\Templates\Traits\Primary_Term;

class Post extends Post_Object {

	use Primary_Term;

	public const NAME = 'post';

}
