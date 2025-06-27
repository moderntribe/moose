<?php declare(strict_types=1);

namespace Tribe\Plugin\Taxonomies\Category;

use Tribe\Plugin\Taxonomies\Term_Object;
use Tribe\Plugin\Templates\Traits\Primary_Term;

class Category extends Term_Object {

	use Primary_Term;

	public const string NAME = 'category';

}
