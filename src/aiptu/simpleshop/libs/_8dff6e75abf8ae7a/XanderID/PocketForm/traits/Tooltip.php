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
 * Provides methods to set and get tooltip text for UI elements.
 */
trait Tooltip {
	/** @var string|null the tooltip text */
	protected ?string $tooltip = null;

	/**
	 * Set the tooltip text.
	 *
	 * @param string|null $tooltip the tooltip text to show on hover
	 */
	public function setTooltip(?string $tooltip) : static {
		$this->tooltip = $tooltip;
		return $this;
	}

	/**
	 * Get the tooltip text.
	 *
	 * @return string|null the current tooltip text
	 */
	public function getTooltip() : ?string {
		return $this->tooltip;
	}
}