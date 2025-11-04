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

namespace aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\custom\validator;

use function is_numeric;

/**
 * A validator that checks if the input data is of a specified type.
 *
 * Example usage:
 * ```
 * $userid = new Input('UserID', '9898');
 * $userid->validator(TypeValidator::NUMBER());
 *
 * // if the user enters non-numeric data,
 * // a message "Data must be numeric." will be displayed above the input.
 * ```
 */
class TypeValidator extends Validator {
	protected const TEXT = 'string';
	protected const NUMBER = 'integer';

	/**
	 * @param string $validator the type to validate against ("string" or "integer")
	 * @param string $error     the error message to return if validation fails
	 */
	public function __construct(
		string $validator,
		string $error = Validator::DEFAULT_ERROR
	) {
		$defaultErrors = [
			self::TEXT => 'Please enter a valid Text.',
			self::NUMBER => 'Please enter a valid Number.',
		];
		$error = ($error === Validator::DEFAULT_ERROR && isset($defaultErrors[$validator]))
			? $defaultErrors[$validator]
			: $error;
		parent::__construct($validator, $error);
	}

	/**
	 * Create a text validator.
	 *
	 * @param string $error the error message for invalid text
	 *
	 * @return self returns a new instance of TypeValidator for text
	 */
	public static function TEXT(string $error = Validator::DEFAULT_ERROR) : self {
		return new self(self::TEXT, $error);
	}

	/**
	 * Create a number validator.
	 *
	 * @param string $error the error message for invalid numbers
	 *
	 * @return self returns a new instance of TypeValidator for numbers
	 */
	public static function NUMBER(string $error = Validator::DEFAULT_ERROR) : self {
		return new self(self::NUMBER, $error);
	}

	/**
	 * Parse the text input based on the specified type.
	 *
	 * @param string $text the input text
	 *
	 * @return int|string returns an integer if validating for number, or the original text if validating for string
	 */
	public function parseText(string $text) : int|string {
		return $this->validator === self::NUMBER ? (int) $text : $text;
	}

	/**
	 * Validate the given data.
	 *
	 * @param mixed $data the data to validate
	 *
	 * @return string|null returns an error message if validation fails, or null if valid
	 */
	public function validate(mixed $data) : ?string {
		return match (true) {
			empty($data) => $this->error(),
			$this->validator === self::NUMBER && !is_numeric($data) => $this->error(),
			default => null,
		};
	}
}