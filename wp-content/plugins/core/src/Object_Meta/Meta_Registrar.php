<?php declare(strict_types=1);

namespace Tribe\Plugin\Object_Meta;

class Meta_Registrar {

	public function register( Meta_Object $meta_object ): void {
		register_extended_field_group( [
			'key'      => 'group_' . $meta_object->get_slug(),
			'title'    => $meta_object->get_title(),
			'fields'   => $meta_object->get_fields(),
			'location' => $meta_object->get_locations(),
			'position' => $meta_object->get_position(),
			'style'    => $meta_object->get_style(),
		] );
	}

}
