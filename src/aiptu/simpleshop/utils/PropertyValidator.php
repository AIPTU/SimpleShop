<?php

/*
 * Copyright (c) 2025 AIPTU
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/AIPTU/SimpleShop
 */

declare(strict_types=1);

namespace aiptu\simpleshop\utils;

use InvalidArgumentException;
use function is_bool;
use function is_float;
use function is_int;
use function is_string;

/**
 * @internal
 *
 * @no-named-arguments
 */
final class PropertyValidator {
	/**
	 * @param array<string, mixed> $data
	 *
	 * @throws InvalidArgumentException
	 */
	public static function getRequiredString(string $key, array $data) : string {
		if (!isset($data[$key]) || !is_string($data[$key])) {
			throw new InvalidArgumentException("Missing or invalid '{$key}' property: must be a string");
		}

		return $data[$key];
	}

	/**
	 * @param array<string, mixed> $data
	 */
	public static function getOptionalString(string $key, array $data) : ?string {
		if (!isset($data[$key])) {
			return null;
		}

		if (!is_string($data[$key])) {
			throw new InvalidArgumentException("Invalid '{$key}' property: must be a string or omitted");
		}

		return $data[$key];
	}

	/**
	 * @param array<string, mixed> $data
	 *
	 * @throws InvalidArgumentException
	 */
	public static function getRequiredInt(string $key, array $data) : int {
		if (!isset($data[$key]) || !is_int($data[$key])) {
			throw new InvalidArgumentException("Missing or invalid '{$key}' property: must be an integer");
		}

		return $data[$key];
	}

	/**
	 * @param array<string, mixed> $data
	 *
	 * @throws InvalidArgumentException
	 */
	public static function getRequiredFloat(string $key, array $data) : float {
		if (!isset($data[$key]) || (!is_int($data[$key]) && !is_float($data[$key]))) {
			throw new InvalidArgumentException("Missing or invalid '{$key}' property: must be a float or integer");
		}

		return (float) $data[$key];
	}

	/**
	 * @param array<string, mixed> $data
	 *
	 * @throws InvalidArgumentException
	 */
	public static function getRequiredBool(string $key, array $data) : bool {
		if (!isset($data[$key]) || !is_bool($data[$key])) {
			throw new InvalidArgumentException("Missing or invalid '{$key}' property: must be a boolean");
		}

		return $data[$key];
	}

	/**
	 * @param array<string, mixed> $data
	 *
	 * @throws InvalidArgumentException
	 */
	public static function validateKeys(array $data, string ...$keys) : void {
		foreach ($keys as $key) {
			if (!isset($data[$key])) {
				throw new InvalidArgumentException("Missing required property '{$key}'");
			}
		}
	}
}
