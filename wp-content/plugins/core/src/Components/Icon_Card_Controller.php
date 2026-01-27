<?php declare(strict_types=1);

namespace Tribe\Plugin\Components;

use Tribe\Plugin\Blocks\Helpers\Block_Animation_Attributes;
use Tribe\Plugin\Blocks\Helpers\Icon_Picker;

class Icon_Card_Controller extends Abstract_Controller {

	/**
	 * @var array <mixed>
	 */
	private array $attributes;
	private Block_Animation_Attributes $block_animation_attributes;
	private string $animation_classes;
	private string $animation_styles;
	private Icon_Picker $icon_picker;
	private string $icon_wrapper_styles;
	private string $icon_svg;
	private string $classes;
	private string $title;
	private string $description;
	private string $link_url;
	private bool $link_opens_in_new_tab;
	private string $link_text;
	private string $link_a11y_label;

	public function __construct( array $args = [] ) {
		$this->attributes                 = $args['attributes'] ?? [];
		$this->block_animation_attributes = new Block_Animation_Attributes( $this->attributes );
		$this->animation_classes          = $this->block_animation_attributes->get_classes();
		$this->animation_styles           = $this->block_animation_attributes->get_styles();
		$this->icon_picker                = new Icon_Picker( $this->attributes );
		$this->icon_wrapper_styles        = $this->icon_picker->get_icon_wrapper_styles();
		$this->icon_svg                   = $this->icon_picker->get_svg();
		$this->classes                    = 'b-icon-card';
		$this->title                      = $this->attributes['title'] ?? '';
		$this->description                = $this->attributes['description'] ?? '';
		$this->link_url                   = $this->attributes['linkUrl'] ?? '';
		$this->link_opens_in_new_tab      = $this->attributes['linkOpensInNewTab'] ?? false;
		$this->link_text                  = $this->attributes['linkText'] ?? '';
		$this->link_a11y_label            = $this->attributes['linkA11yLabel'] ?? '';
	}

	public function get_classes(): string {
		if ( '' !== $this->animation_classes ) {
			$this->classes .= ' ' . $this->animation_classes;
		}

		return $this->classes;
	}

	public function get_styles(): string {
		return $this->animation_styles;
	}

	public function get_icon_wrapper_styles(): string {
		return $this->icon_wrapper_styles;
	}

	public function has_icon(): bool {
		return ! empty( $this->icon_svg );
	}

	public function get_icon_svg(): string {
		return $this->icon_svg;
	}

	public function get_title(): string {
		return $this->title;
	}

	public function has_description(): bool {
		return (bool) $this->description;
	}

	public function get_description(): string {
		return $this->description;
	}

	public function has_link_url(): bool {
		return (bool) $this->link_url;
	}

	public function get_link_url(): string {
		return $this->link_url;
	}

	public function does_link_open_in_new_tab(): bool {
		return $this->link_opens_in_new_tab;
	}

	public function get_link_text(): string {
		return $this->link_text;
	}

	public function get_link_a11y_label(): string {
		return $this->link_a11y_label;
	}

}
