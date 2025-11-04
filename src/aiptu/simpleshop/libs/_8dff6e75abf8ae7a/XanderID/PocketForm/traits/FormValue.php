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

namespace aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\traits;

/**
 * Provides methods to set and get the value of a form element.
 */
trait FormValue {
	/** @var mixed the value of the element */
	protected mixed $value;

	/**
	 * Get the current value.
	 *
	 * @return mixed the current value
	 */
	public function getValue() : mixed {
		return $this->value;
	}

	/**
	 * Set the value.
	 *
	 * @param mixed $value the value to set
	 */
	public function setValue(mixed $value) : void {
		$this->value = $value;
	}
}