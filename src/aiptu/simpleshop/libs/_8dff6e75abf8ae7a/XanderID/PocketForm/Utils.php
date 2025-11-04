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

namespace aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm;

use Closure;
use pocketmine\player\Player;
use pocketmine\utils\Utils as PMUtils;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\custom\CustomElement;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\custom\element\Dropdown;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\custom\element\Input;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\custom\element\Slider;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\custom\element\StepSlider;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\custom\element\Toggle;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\custom\validator\TypeValidator;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\element\Element;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\element\Label;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\element\ReadonlyElement;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\simple\SimpleFormResponse;
use function count;
use function is_numeric;
use function is_string;

/**
 * Contains utility methods for validation, array manipulation, and menu callable.
 */
class Utils {
	/**
	 * List of supported form types.
	 *
	 * Supported types include:
	 * - 'form'         : SimpleForm (basic list selection)
	 * - 'modal'        : ModalForm (Yes/No dialog)
	 * - 'custom_form'  : CustomForm (form with multiple input elements)
	 *
	 * These types are typically used for UI generation or form validation logic.
	 *
	 * @var array<string>
	 */
	public const FORM_TYPES = ['form', 'modal', 'custom_form'];

	/**
	 * Convert the value based on the specific CustomElement type.
	 *
	 * @param CustomElement|Label   $element the element for which the value is being processed
	 * @param bool|float|int|string $value   the raw value from the form response
	 *
	 * @return bool|int|string the converted value
	 */
	public static function customValue(CustomElement|Label $element, bool|float|int|string $value) : bool|int|string {
		if ($element instanceof Input) {
			$validator = $element->getValidator();
			if ($validator instanceof TypeValidator) {
				if (!is_string($value)) {
					$value = (string) $value;
				}

				return $validator->parseText($value);
			}
		}

		return match (true) {
			$element instanceof Dropdown => (string) $element->getOptions()[(int) $value],
			$element instanceof Input => is_string($value) ? $value : (string) $value,
			$element instanceof Label => (string) $element->getLabel(),
			$element instanceof Slider => is_numeric($value) ? (int) $value : (int) $value,
			$element instanceof StepSlider => (int) $element->getStep()[(int) $value],
			$element instanceof Toggle => (bool) $value,
			default => throw new PocketFormException('Could not find Element'),
		};
	}

	/**
	 * Maps the raw CustomForm response data to align with the form's elements.
	 *
	 * In some versions (e.g. 1.21.70), Label elements are not included in the response data since they are non-interactive.
	 *
	 * @param array<int, Element>     $elements the list of form elements from the CustomForm
	 * @param array<int, scalar|null> $data     the raw response data from the form
	 *
	 * @return array<int, scalar|null> the mapped response data, with `null` in place of Labels
	 */
	public static function customMap(array $elements, array $data) : array {
		if (count($elements) === count($data)) {
			return $data;
		}

		/** @var array<int, scalar|null> $mapped */
		$mapped = [];
		$valueIndex = 0;

		foreach ($elements as $i => $element) {
			if ($element instanceof ReadonlyElement) {
				$mapped[$i] = null;
			} else {
				$mapped[$i] = $data[$valueIndex] ?? null;
				++$valueIndex;
			}
		}

		return $mapped;
	}

	/**
	 * Validate the type of each value in an array using a provided closure.
	 *
	 * @param list<mixed> $array     the array whose values will be validated
	 * @param Closure     $validator a closure that accepts a value and validates it
	 *
	 * @return bool returns true if any value fails validation, otherwise false
	 */
	public static function validateArrayValueType(array $array, Closure $validator) : bool {
		foreach ($array as $k => $v) {
			try {
				$validator($v);
			} catch (\TypeError $e) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Validate that all elements in an array are instances of Element.
	 *
	 * @param list<Element> $elements the array of elements to validate
	 * @param string        $header   the error message prefix if validation fails
	 *
	 * @throws PocketFormException if any element is not an instance of Element
	 */
	public static function validateArrayElement(array $elements, string $header = 'Failed to create Elements') : void {
		if (self::validateArrayValueType($elements, function (Element $element) : void {})) {
			throw new PocketFormException($header . ': Elements array can only be Element!');
		}
	}

	/**
	 * Create a callable for a menu form that maps button selections to callbacks.
	 *
	 * @param list<callable> $callables an array of callables corresponding to each button
	 *
	 * @return Closure returns a closure that processes the form response
	 *
	 * @throws PocketFormException if any callable is invalid
	 */
	public static function createMenuCall(array $callables) : Closure {
		if (self::validateArrayValueType($callables, function (callable $callable) : void {})) {
			throw new PocketFormException('Failed to Build Closure: Callables array can only be callable!');
		}

		foreach ($callables as $callable) {
			PMUtils::validateCallableSignature(fn (Player $player) => null, $callable);
		}

		$onResponse = function (SimpleFormResponse $response) use ($callables) : void {
			$player = $response->getPlayer();
			$selected = $response->getSelected();
			$index = $selected->getId();
			if (($callable = $callables[$index] ?? null) !== null) {
				$callable($player);
			}
		};
		return $onResponse;
	}
}