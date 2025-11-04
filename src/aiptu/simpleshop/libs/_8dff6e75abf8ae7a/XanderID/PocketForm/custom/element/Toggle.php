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

namespace aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\custom\element;

use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\custom\CustomElement;

/**
 * Represents a toggle element for boolean values.
 */
class Toggle extends CustomElement {
	/**
	 * @param string      $label   the label for the toggle
	 * @param bool|null   $default the default state (true for on, false for off) (optional)
	 * @param string|null $tooltip tooltip shown on hover (optional)
	 */
	public function __construct(
		string $label,
		protected ?bool $default = null,
		?string $tooltip = null
	) {
		$this->setLabel($label);
		$this->setTooltip($tooltip);
	}

	/**
	 * Creates a new Toggle element.
	 *
	 * @param string      $label   the label for the toggle
	 * @param bool|null   $default the default state (true for on, false for off) (optional)
	 * @param string|null $tooltip tooltip shown on hover (optional)
	 */
	public static function create(string $label, ?bool $default = null, ?string $tooltip = null) : self {
		return new self($label, $default, $tooltip);
	}

	/**
	 * Get the default toggle state.
	 *
	 * @return bool|null the default state
	 */
	public function getDefault() : ?bool {
		return $this->default;
	}

	/**
	 * Set the default toggle state.
	 *
	 * @param bool $default the default state
	 */
	public function setDefault(bool $default) : self {
		$this->default = $default;
		return $this;
	}

	/**
	 * Get the element type.
	 *
	 * @return string returns "toggle"
	 */
	public function getType() : string {
		return 'toggle';
	}

	/**
	 * Build the toggle element.
	 *
	 * @param array<string, list<array<string, mixed>>> &$components The components array to add the toggle to
	 */
	public function build(array &$components) : void {
		$toggle = ['type' => $this->getType(), 'text' => $this->label];
		if ($this->default !== null) {
			$toggle['default'] = $this->default;
		}

		if ($this->tooltip !== null) {
			$toggle['tooltip'] = $this->tooltip;
		}

		$components['content'][] = $toggle;
	}
}