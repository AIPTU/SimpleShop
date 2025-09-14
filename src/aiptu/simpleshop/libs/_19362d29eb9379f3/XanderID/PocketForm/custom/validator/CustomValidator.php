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

use Closure;
use pocketmine\utils\Utils;

/**
 * Provides a custom validator that uses a Closure for validation.
 *
 * Example usage:
 * ```
 *	$bannedWords = ['spam', 'scam', 'phishing'];
 *
 *	$validator = new CustomValidator(function($data) use ($bannedWords): ?string {
 *		if (in_array($data, $bannedWords)) {
 *			return "The input contains a banned word: {$word}.";
 *		}
 *		return null;
 *	});
 *
 *	$comment = new Input('Comment', 'Input Comment.');
 *	$comment->validator($validator);
 *
 *	// If the input contains any banned word,
 *	// an error message such as "The input contains a banned word: spam." will be shown.
 * ```
 */
class CustomValidator extends Validator {
	/**
	 * @param Closure $validator the validation function that accepts mixed data and returns a string error message if validation fails,
	 *                           or null if the data is valid
	 */
	public function __construct(Closure $validator) {
		Utils::validateCallableSignature(fn ($data) : ?string => null, $validator);
		parent::__construct($validator);
	}

	/**
	 * Creates a new CustomValidator instance.
	 *
	 * @param Closure $validator the validation function that accepts mixed data and returns a string error message if validation fails,
	 *                           or null if the data is valid
	 */
	public static function create(Closure $validator) : self {
		return new self($validator);
	}

	/**
	 * Validate the given data.
	 *
	 * @param mixed $data the data to validate
	 *
	 * @return string|null returns an error message if validation fails, or null if the data is valid
	 */
	public function validate(mixed $data) : ?string {
		/** @var Closure $validator */
		$validator = $this->validator;
		return $validator($data);
	}
}