<?php declare(strict_types=1);

namespace Tribe\Plugin\Components;

use Tribe\Plugin\Blocks\Helpers\Block_Animation_Attributes;

class Image_Overlay_Card_Controller extends Abstract_Controller {

	/**
	 * @var array <mixed>
	 */
	private array $attributes;
	private Block_Animation_Attributes $block_animation_attributes;
	private string $animation_classes;
	private string $animation_styles;
	private string $classes;
	private int $media_id;
	private string $media_url;
	private string $overlay_color;
	private string $overlay_hover_color;
	private bool $card_uses_dark_theme;
	private string $title;
	private string $link_url;
	private bool $link_opens_in_new_tab;
	private string $link_text;
	private string $link_a11y_label;

	public function __construct( array $args = [] ) {
		$this->attributes                 = $args['attributes'] ?? [];
		$this->block_animation_attributes = new Block_Animation_Attributes( $this->attributes );
		$this->animation_classes          = $this->block_animation_attributes->get_classes();
		$this->animation_styles           = $this->block_animation_attributes->get_styles();
		$this->classes                    = 'b-image-overlay-card';
		$this->media_id                   = $this->attributes['mediaId'] ? (int) $this->attributes['mediaId'] : 0;
		$this->media_url                  = $this->attributes['mediaUrl'] ?? '';
		$this->overlay_color              = $this->attributes['overlayColor'] ?? '#0000001C';
		$this->overlay_hover_color        = $this->attributes['overlayHoverColor'] ?? '#00000033';
		$this->card_uses_dark_theme       = $this->attributes['cardUsesDarkTheme'] ?? false;
		$this->title                      = $this->attributes['title'] ?? '';
		$this->link_url                   = $this->attributes['linkUrl'] ?? '';
		$this->link_opens_in_new_tab      = $this->attributes['linkOpensInNewTab'] ?? false;
		$this->link_text				  = $this->attributes['linkText'] ?? '';
		$this->link_a11y_label            = $this->attributes['linkA11yLabel'] ?? '';
	}

	public function get_classes(): string {
		if ( $this->card_uses_dark_theme ) {
			$this->classes .= ' b-image-overlay-card--dark-theme';
		}

		if ( '' !== $this->animation_classes ) {
			$this->classes .= ' ' . $this->animation_classes;
		}

		return $this->classes;
	}

	public function get_styles(): string {
		$styles  = $this->animation_styles;
		$styles .= "--card-image-overlay-color: {$this->overlay_color};";
		$styles .= "--card-image-overlay-hover-color: {$this->overlay_hover_color};";

		return $styles;
	}

	public function has_media(): bool {
		return 0 !== $this->media_id || '' !== $this->media_url;
	}

	public function get_media(): string {
		if ( 0 !== $this->media_id ) {
			return wp_get_attachment_image( $this->media_id, 'large' );
		}

		if ( '' !== $this->media_url ) {
			return '<img src="' . esc_url( $this->media_url ) . '" alt="' . esc_attr__( 'Block placeholder image', 'tribe' ) . '">';
		}

		return '';
	}

	public function get_title(): string {
		return $this->title;
	}

	public function has_link_url(): bool {
		return '' !== $this->link_url;
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
