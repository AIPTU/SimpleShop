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

/**
 * Base class for implementing custom validators.
 */
abstract class Validator {
	public const DEFAULT_ERROR = 'Invalid input type specified.';

	/**
	 * @param mixed  $validator the underlying validation logic
	 * @param string $error     the error message to display if validation fails
	 */
	public function __construct(
		protected $validator,
		protected string $error = self::DEFAULT_ERROR
	) {}

	/**
	 * Get the error message.
	 *
	 * @return string the formatted error message
	 */
	public function error() : string {
		return TextFormat::RED . $this->error;
	}

	/**
	 * Validate the given data.
	 *
	 * @param mixed $data the data to validate
	 *
	 * @return string|null returns an error message if validation fails, or null if valid
	 */
	abstract public function validate(mixed $data) : ?string;
}