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

/**
 * Represents a slider element for selecting a numeric value within a range.
 */
class Slider extends CustomElement {
	/**
	 * @param string      $label   the label for the slider
	 * @param int         $min     the minimum value of the slider
	 * @param int         $max     the maximum value of the slider
	 * @param int|null    $step    the increment step of the slider (optional)
	 * @param int|null    $default the default value of the slider (optional)
	 * @param string|null $tooltip tooltip shown on hover (optional)
	 */
	public function __construct(
		string $label,
		protected int $min,
		protected int $max,
		protected ?int $step = null,
		protected ?int $default = null,
		?string $tooltip = null
	) {
		$this->setLabel($label);
		$this->setTooltip($tooltip);
	}

	/**
	 * Creates a new Slider element.
	 *
	 * @param string      $label   the label for the slider
	 * @param int         $min     the minimum value of the slider
	 * @param int         $max     the maximum value of the slider
	 * @param int|null    $step    the increment step of the slider (optional)
	 * @param int|null    $default the default value of the slider (optional)
	 * @param string|null $tooltip tooltip shown on hover (optional)
	 */
	public static function create(string $label, int $min, int $max, ?int $step = null, ?int $default = null, ?string $tooltip = null) : self {
		return new self($label, $min, $max, $step, $default, $tooltip);
	}

	/**
	 * Get the minimum value.
	 *
	 * @return int the minimum value
	 */
	public function getMin() : int {
		return $this->min;
	}

	/**
	 * Set the minimum value.
	 *
	 * @param int $min the new minimum value
	 */
	public function setMin(int $min) : self {
		$this->min = $min;
		return $this;
	}

	/**
	 * Get the maximum value.
	 *
	 * @return int the maximum value
	 */
	public function getMax() : int {
		return $this->max;
	}

	/**
	 * Set the maximum value.
	 *
	 * @param int $max the new maximum value
	 */
	public function setMax(int $max) : self {
		$this->max = $max;
		return $this;
	}

	/**
	 * Get the slider step value.
	 *
	 * @return int|null the step increment value
	 */
	public function getStep() : ?int {
		return $this->step;
	}

	/**
	 * Set the slider step value.
	 *
	 * @param int $step the new step value
	 */
	public function setStep(int $step) : self {
		$this->step = $step;
		return $this;
	}

	/**
	 * Get the default slider value.
	 *
	 * @return int|null the default value
	 */
	public function getDefault() : ?int {
		return $this->default;
	}

	/**
	 * Set the default slider value.
	 *
	 * @param int $default the default value
	 */
	public function setDefault(int $default) : self {
		$this->default = $default;
		return $this;
	}

	/**
	 * Get the element type.
	 *
	 * @return string returns "slider"
	 */
	public function getType() : string {
		return 'slider';
	}

	/**
	 * Perform pre-build checks for the slider.
	 *
	 * @throws PocketFormException if any of the numeric properties are invalid
	 */
	public function buildCheck() : void {
		match (true) {
			$this->min < 0 => $this->buildError('min must be 0 or greater.'),
			$this->max < 0 => $this->buildError('max must be 0 or greater.'),
			$this->step !== null && $this->step < 0 => $this->buildError('step must be 0 or greater.'),
			$this->default !== null && $this->default < 0 => $this->buildError('default must be 0 or greater.'),
			default => null,
		};
		parent::buildCheck();
	}

	/**
	 * Build the slider element.
	 *
	 * @param array<string, list<array<string, mixed>>> &$components The components array to add the slider to
	 */
	public function build(array &$components) : void {
		$slider = ['type' => $this->getType(), 'text' => $this->label, 'min' => $this->min, 'max' => $this->max];
		if ($this->step !== null) {
			$slider['step'] = $this->step;
		}

		if ($this->default !== null) {
			$slider['default'] = $this->default;
		}

		if ($this->tooltip !== null) {
			$slider['tooltip'] = $this->tooltip;
		}

		$components['content'][] = $slider;
	}
}