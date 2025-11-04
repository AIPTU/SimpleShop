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
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\PocketFormException;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\Utils;

/**
 * Represents a dropdown element with a list of options.
 */
class Dropdown extends CustomElement {
	/**
	 * @param string       $label   the label for the dropdown
	 * @param list<string> $options an array of options to display in the dropdown
	 * @param int|null     $default the default selected index (optional)
	 * @param string|null  $tooltip tooltip shown on hover (optional)
	 */
	public function __construct(
		string $label,
		protected array $options,
		protected ?int $default = null,
		?string $tooltip = null
	) {
		$this->setLabel($label);
		$this->setTooltip($tooltip);
	}

	/**
	 * Creates a new Dropdown element.
	 *
	 * @param string       $label   the label for the dropdown
	 * @param list<string> $options an array of options to display in the dropdown
	 * @param int|null     $default the default selected index (optional)
	 * @param string|null  $tooltip tooltip shown on hover (optional)
	 */
	public static function create(string $label, array $options, ?int $default = null, ?string $tooltip = null) : self {
		return new self($label, $options, $default, $tooltip);
	}

	/**
	 * Get the element type.
	 *
	 * @return string returns "dropdown"
	 */
	public function getType() : string {
		return 'dropdown';
	}

	/**
	 * Get the dropdown options.
	 *
	 * @return list<string> the list of options
	 */
	public function getOptions() : array {
		return $this->options;
	}

	/**
	 * Set the dropdown options.
	 *
	 * @param list<string> $options the new list of options
	 */
	public function setOptions(array $options) : self {
		$this->options = $options;
		return $this;
	}

	/**
	 * Get the default index.
	 *
	 * @return int|null the default selected index
	 */
	public function getDefault() : ?int {
		return $this->default;
	}

	/**
	 * Set the default index.
	 *
	 * @param int $default the default index
	 */
	public function setDefault(int $default) : self {
		$this->default = $default;
		return $this;
	}

	/**
	 * Perform pre-build checks for the dropdown.
	 *
	 * @throws PocketFormException if options are not all strings or the default index is invalid
	 */
	public function buildCheck() : void {
		if (Utils::validateArrayValueType($this->options, function (string $option) : void {})) {
			$this->buildError('Dropdown arrays can only be string');
		}

		$index = $this->default;
		if ($index !== null) {
			if (!isset($this->options[$index])) {
				$this->buildError('Cannot find Index ' . $index . ' on Dropdown');
			}
		}

		parent::buildCheck();
	}

	/**
	 * Build the dropdown element.
	 *
	 * @param array<string, list<array<string, mixed>>> &$components The components array to add the dropdown to
	 */
	public function build(array &$components) : void {
		$dropdown = [
			'type' => $this->getType(),
			'text' => $this->label,
			'options' => $this->options,
		];
		if ($this->default !== null) {
			$dropdown['default'] = $this->default;
		}

		if ($this->tooltip !== null) {
			$dropdown['tooltip'] = $this->tooltip;
		}

		$components['content'][] = $dropdown;
	}
}