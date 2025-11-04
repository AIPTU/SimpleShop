<?php

/*
 * Copyright (c) 2025-2025 XanderID
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/XanderID/PocketForm
 */

declare(strict_types=1);

namespace aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\simple\element;

/**
 * Represents an image associated with a button element.
 */
class ButtonImage {
	public const IMAGE_PATH = 'path';
	public const IMAGE_URL = 'url';

	/**
	 * @param string $type the image type (either "path" or "url")
	 * @param string $uri  the URI or path to the image
	 */
	public function __construct(
		public string $type,
		public string $uri
	) {}

	/**
	 * Create a new instance of ButtonImage.
	 *
	 * @param int    $type the image type (0 for IMAGE_PATH, otherwise IMAGE_URL)
	 * @param string $uri  the URI of the image
	 *
	 * @return self returns a new ButtonImage instance
	 */
	public static function create(int $type, string $uri) : self {
		$imageType = $type === 0 ? self::IMAGE_PATH : self::IMAGE_URL;
		return new self($imageType, $uri);
	}

	/**
	 * Create a Url ButtonImage.
	 *
	 * @param string $uri the Url of the image
	 *
	 * @return self returns a new ButtonImage instance
	 */
	public static function url(string $uri) : self {
		return new self(self::IMAGE_URL, $uri);
	}

	/**
	 * Create a Path ButtonImage.
	 *
	 * @param string $uri the Path of the image
	 *
	 * @return self returns a new ButtonImage instance
	 */
	public static function path(string $uri) : self {
		return new self(self::IMAGE_PATH, $uri);
	}

	/**
	 * Get the image type.
	 *
	 * @return string the image type
	 */
	public function getType() : string {
		return $this->type;
	}

	/**
	 * Get the image URI.
	 *
	 * @return string the image URI
	 */
	public function getUri() : string {
		return $this->uri;
	}

	/**
	 * Build the image data into the button array.
	 *
	 * @param array<string, mixed> &$button The button array to add image data to
	 */
	public function build(array &$button) : void {
		$image = [
			'type' => $this->getType(),
			'data' => $this->getUri(),
		];
		$button['image'] = $image;
	}
}