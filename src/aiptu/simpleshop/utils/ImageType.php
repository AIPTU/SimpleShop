<?php

/*
 * Copyright (c) 2021-2025 AIPTU
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/AIPTU/BlockReplacer
 */

declare(strict_types=1);

namespace aiptu\simpleshop\utils;

use XanderID\PocketForm\simple\element\ButtonImage;
use function array_map;

/**
 * @no-named-arguments
 */
enum ImageType : string {
	case URL = 'url';
	case PATH = 'path';

	/**
	 * Returns the integer representation for ButtonImage::create().
	 */
	public function toInt() : int {
		return match ($this) {
			self::PATH => 0,
			self::URL => 1,
		};
	}

	/**
	 * @return array<string>
	 */
	public static function values() : array {
		return array_map(fn (self $case) => $case->value, self::cases());
	}
}
