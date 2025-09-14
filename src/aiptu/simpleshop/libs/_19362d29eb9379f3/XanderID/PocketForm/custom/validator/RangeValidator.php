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

namespace aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\custom\validator;

use pocketmine\utils\TextFormat;
use function is_numeric;

/**
 * A validator that checks if the input data falls within a specified numeric range.
 *
 * Example usage:
 * ```php
 * $age = new Input('Age', '25');
 * $age->validator(new RangeValidator(18, 65));
 *
 * // If the user enters a value outside the range of 18 to 65,
 * // a message "Please enter a value between 18 and 65." will be displayed above the input.
 * ```
 */
class RangeValidator extends Validator {
	/**
	 * @param int    $min   The minimum acceptable value (inclusive)
	 * @param int    $max   The maximum acceptable value (inclusive)
	 * @param string $error The error message to return if validation fails
	 */
	public function __construct(
		protected int $min,
		protected int $max,
		string $error = Validator::DEFAULT_ERROR
	) {
		$defaultError = "Please enter a value between {$min} and {$max}.";
		$error = ($error === Validator::DEFAULT_ERROR) ? $defaultError : $error;
		parent::__construct("range:{$min}-{$max}", $error);
	}

	/**
	 * Creates a new RangeValidator instance.
	 *
	 * @param int    $min   The minimum acceptable value (inclusive)
	 * @param int    $max   The maximum acceptable value (inclusive)
	 * @param string $error The error message to return if validation fails
	 *
	 * @return self Returns a new instance of RangeValidator
	 */
	public static function create(
		int $min,
		int $max,
		string $error = Validator::DEFAULT_ERROR
	) : self {
		return new self($min, $max, $error);
	}

	/**
	 * Validate the given data to ensure it falls within the specified range.
	 *
	 * @param mixed $data The data to validate
	 *
	 * @return string|null Returns an error message if validation fails, or null if valid
	 */
	public function validate(mixed $data) : ?string {
		if (!is_numeric($data)) {
			return TextFormat::RED . 'The input must be a number.';
		}

		$value = (int) $data;
		if ($value < $this->min || $value > $this->max) {
			return $this->error();
		}

		return null;
	}
}