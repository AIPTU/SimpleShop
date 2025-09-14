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

namespace aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\traits;

use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\custom\validator\Validator;

/**
 * Provides methods to set and use a validator for a form element.
 */
trait FormValidator {
	/** @var Validator|null validator for the element */
	protected ?Validator $validatorType = null;

	/**
	 * Get the current validator.
	 *
	 * @return Validator|null the current validator, or null if none is set
	 */
	public function getValidator() : ?Validator {
		return $this->validatorType;
	}

	/**
	 * Set the validator.
	 *
	 * @param Validator $validator the validator to set
	 */
	public function validator(Validator $validator) : static {
		$this->validatorType = $validator;
		return $this;
	}

	/**
	 * Validate the given data using the set validator.
	 *
	 * @param mixed $data the data to validate
	 *
	 * @return string|null returns an error message if validation fails, or null if valid
	 */
	public function validate(mixed $data) : ?string {
		return $this->validatorType?->validate($data);
	}
}