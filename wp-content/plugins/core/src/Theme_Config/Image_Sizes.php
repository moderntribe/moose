<?php declare(strict_types=1);

namespace Tribe\Plugin\Theme_Config;

class Image_Sizes {

	public const SQUARE_XSMALL      = 'square-xsmall';
	public const SQUARE_MEDIUM      = 'square-medium';
	public const SQUARE_LARGE       = 'square-large';
	public const SIXTEEN_NINE       = 'sixteen-nine';
	public const SIXTEEN_NINE_SMALL = 'sixteen-nine-small';
	public const SIXTEEN_NINE_LARGE = 'sixteen-nine-large';
	public const NEWS_FEATURED      = 'news-featured';

	/**
	 * @var array<string, array<string, int|bool>>
	 */
	protected array $sizes = [
		self::SQUARE_XSMALL      => [
			'width'  => 150,
			'height' => 150,
			'crop'   => true,
		],
		self::SQUARE_MEDIUM      => [
			'width'  => 376,
			'height' => 376,
			'crop'   => true,
		],
		self::SQUARE_LARGE       => [
			'width'  => 650,
			'height' => 650,
			'crop'   => true,
		],
		self::SIXTEEN_NINE_SMALL => [
			'width'  => 680,
			'height' => 383,
			'crop'   => true,
		],
		self::SIXTEEN_NINE       => [
			'width'  => 1280,
			'height' => 720,
			'crop'   => true,
		],
		self::SIXTEEN_NINE_LARGE => [
			'width'  => 1920,
			'height' => 1080,
			'crop'   => true,
		],
		self::NEWS_FEATURED      => [
			'width'  => 1619,
			'height' => 1080,
			'crop'   => true,
		],
	];

	/**
	 * @action after_setup_theme
	 */
	public function register_sizes(): void {
		foreach ( $this->sizes as $key => $attributes ) {
			add_image_size( $key, $attributes['width'], $attributes['height'], $attributes['crop'] );
		}
	}

}
