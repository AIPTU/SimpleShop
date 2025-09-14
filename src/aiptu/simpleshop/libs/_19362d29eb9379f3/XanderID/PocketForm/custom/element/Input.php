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

namespace aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\custom\element;

use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\custom\CustomElement;

/**
 * Represents an input field element.
 */
class Input extends CustomElement {
	/**
	 * @param string      $label       the label for the input field
	 * @param string      $placeholder the placeholder text for the input
	 * @param string|null $default     the default value for the input (optional)
	 * @param string|null $tooltip     tooltip shown on hover (optional)
	 */
	public function __construct(
		string $label,
		protected string $placeholder = '',
		protected ?string $default = null,
		?string $tooltip = null
	) {
		$this->setLabel($label);
		$this->setTooltip($tooltip);
	}

	/**
	 * Creates a new Input element.
	 *
	 * @param string      $label       the label for the input field
	 * @param string      $placeholder the placeholder text for the input
	 * @param string|null $default     the default value for the input (optional)
	 * @param string|null $tooltip     tooltip shown on hover (optional)
	 */
	public static function create(string $label, string $placeholder = '', ?string $default = null, ?string $tooltip = null) : self {
		return new self($label, $placeholder, $default, $tooltip);
	}

	/**
	 * Get the element type.
	 *
	 * @return string returns "input"
	 */
	public function getType() : string {
		return 'input';
	}

	/**
	 * Get the placeholder text.
	 *
	 * @return string the placeholder text
	 */
	public function getPlaceholder() : string {
		return $this->placeholder;
	}

	/**
	 * Set the placeholder text.
	 *
	 * @param string $placeholder the new placeholder text
	 */
	public function setPlaceholder(string $placeholder) : self {
		$this->placeholder = $placeholder;
		return $this;
	}

	/**
	 * Get the default value.
	 *
	 * @return string|null the default value
	 */
	public function getDefault() : ?string {
		return $this->default;
	}

	/**
	 * Set the default value.
	 *
	 * @param string $default the default value
	 */
	public function setDefault(string $default) : self {
		$this->default = $default;
		return $this;
	}

	/**
	 * Build the input element.
	 *
	 * @param array<string, list<array<string, mixed>>> &$components The components array to add the input to
	 */
	public function build(array &$components) : void {
		$input = ['type' => $this->getType(), 'text' => $this->label, 'placeholder' => $this->placeholder];
		if ($this->default !== null) {
			$input['default'] = $this->default;
		}

		if ($this->tooltip !== null) {
			$input['tooltip'] = $this->tooltip;
		}

		$components['content'][] = $input;
	}
}