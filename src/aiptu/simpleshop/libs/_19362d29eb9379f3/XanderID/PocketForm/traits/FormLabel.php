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

/**
 * Provides methods to set and get the label of a form element.
 */
trait FormLabel {
	/** @var string the form label */
	protected string $label = '';

	/**
	 * Get the label.
	 *
	 * @return string the current label
	 */
	public function getLabel() : string {
		return $this->label;
	}

	/**
	 * Set the label.
	 *
	 * @param string $label the new label
	 */
	public function setLabel(string $label) : static {
		$this->label = $label;
		return $this;
	}
}