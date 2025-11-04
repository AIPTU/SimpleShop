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

namespace aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\custom;

use pocketmine\player\Player;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\custom\element\Dropdown;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\custom\element\Input;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\custom\element\Slider;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\custom\element\StepSlider;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\custom\element\Toggle;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\element\ErrorLabel;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\element\Label;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\element\ReadonlyElement;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\modal\ModalFormResponse;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\PocketForm;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\PocketFormException;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\traits\Confirm;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\traits\Submit;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\Utils;
use function count;
use function gettype;
use function is_array;

/**
 * Represents a custom form that can contain various custom elements.
 *
 * @extends PocketForm<CustomFormResponse>
 */
class CustomForm extends PocketForm {
	use Submit;
	use Confirm;
	use CustomHelper;

	/**
	 * Default submit text.
	 *
	 * @var string
	 */
	public const DEFAULT_SUBMIT = 'gui.submit';

	/**
	 * @param string $title the title of the custom form
	 */
	public function __construct(string $title) {
		$this->setSubmit(self::DEFAULT_SUBMIT);
		parent::__construct($title);
	}

	/**
	 * Creates a new CustomForm instance.
	 *
	 * @param string $title the title of the custom form
	 */
	public static function create(string $title) : self {
		return new self($title);
	}

	/**
	 * Get the form type.
	 *
	 * @return string returns "custom_form"
	 */
	protected function getType() : string {
		return 'custom_form';
	}

	/**
	 * Get the response class name.
	 *
	 * @return string returns the class name for processing custom form responses
	 */
	protected function getResponseClass() : string {
		return CustomFormResponse::class;
	}

	/**
	 * Get the callback signature for the custom form response.
	 *
	 * @return callable the expected signature for the onResponse callback
	 */
	protected function getSignature() : callable {
		return function (CustomFormResponse $response) : void {};
	}

	/**
	 * Process the custom form response data.
	 *
	 * @param Player $player the player who submitted the form
	 * @param mixed  $data   the raw response data; expected to be an array with int keys and values of type bool|int|string|null
	 *
	 * @throws PocketFormException if $data is not iterable
	 */
	public function callOnResponse(Player $player, mixed $data) : void {
		if (!is_array($data)) {
			throw new PocketFormException('Expected response data to be an array, got ' . gettype($data));
		}

		/** @var array<int, scalar|null> $data */
		$values = [];
		$elements = $this->getElements();
		$mapData = Utils::customMap($elements, $data);
		$errorLabels = [];
		$isError = false;

		foreach ($mapData as $index => $value) {
			/** @var CustomElement|Label $element */
			$element = $elements[$index];
			if ($element instanceof ReadonlyElement) {
				continue;
			}

			/** @var bool|float|int|string $value */
			$parsed = Utils::customValue($element, $value);
			$validated = $element->validate($parsed);
			$indexInt = (int) $index;
			$previous = $elements[$indexInt - 1] ?? null;
			if ($validated !== null) {
				if (!$previous instanceof ErrorLabel) {
					$errorLabels[$indexInt] = new ErrorLabel($validated);
				}

				$isError = true;
			} else {
				if ($previous instanceof ErrorLabel) {
					unset($elements[$indexInt - 1]);
				}
			}

			$values[] = $parsed;
			$element->setValue($parsed);

			/**
			 * @var Dropdown|Input|Slider|StepSlider|Toggle $element
			 * @var bool|int|string $parsed
			 */
			$element->setDefault($parsed);
		}

		if ($isError) {
			$newElements = [];
			foreach ($elements as $index => $element) {
				if (isset($errorLabels[$index])) {
					$newElements[] = $errorLabels[$index];
				}

				$newElements[] = $element;
			}

			$this->setElements($newElements);
			$player->sendForm($this);
			return;
		}

		if (null !== ($confirm = $this->getConfirm())) {
			$confirm->onResponse(function (ModalFormResponse $response) use ($values) : void {
				$player = $response->getPlayer();
				if ($response->getChoice()) {
					parent::callOnResponse($player, $values);
				}
			});
			$player->sendForm($confirm);
			return;
		}

		parent::callOnResponse($player, $values);
	}

	/**
	 * Initialize form components specific to custom forms.
	 *
	 * @return array<string, mixed> an array containing custom components
	 */
	protected function initComponents() : array {
		return [
			'submit' => $this->submit,
			'content' => [],
		];
	}

	/**
	 * Serialize the form to an array suitable for JSON encoding.
	 *
	 * @return array<string, mixed> the array representation of the form
	 *
	 * @throws PocketFormException If the CustomForm cannot be built
	 */
	public function jsonSerialize() : array {
		$elements = $this->getElements();
		if (count($elements) < 1) {
			throw new PocketFormException('Failed to build Custom Form: Please add at least 1 element');
		}

		return parent::jsonSerialize();
	}
}