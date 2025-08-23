<?php

/*
 * Copyright (c) 2025 AIPTU
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/AIPTU/SimpleShop
 */

declare(strict_types=1);

namespace aiptu\simpleshop\forms;

use Closure;
use pocketmine\player\Player;
use RuntimeException;
use XanderID\PocketForm\custom\CustomForm;
use XanderID\PocketForm\custom\CustomFormResponse;
use XanderID\PocketForm\custom\element\Dropdown;
use XanderID\PocketForm\custom\element\Input;
use XanderID\PocketForm\custom\element\Slider;
use XanderID\PocketForm\custom\element\Toggle;
use XanderID\PocketForm\element\Label;
use XanderID\PocketForm\modal\ModalForm;
use XanderID\PocketForm\modal\ModalFormResponse;

/**
 * @no-named-arguments
 */
class FormHelper {
	/**
	 * @param array<int, array<string, mixed>>            $inputs
	 * @param Closure(CustomFormResponse $response): void $onSubmit
	 */
	public static function displayCustomForm(Player $player, string $title, array $inputs, callable $onSubmit) : void {
		$form = new CustomForm($title);

		foreach ($inputs as $input) {
			$type = (string) $input['type'];
			$label = $input['label'];

			switch ($type) {
				case 'label':
					$form->addElement(new Label((string) $label));
					break;
				case 'input':
					$placeholder = $input['placeholder'] ?? '';
					$default = $input['default'] ?? '';
					$form->addElement(new Input((string) $label, (string) $placeholder, (string) $default));
					break;
				case 'slider':
					$sliderMin = $input['sliderMin'] ?? 1;
					$sliderMax = $input['sliderMax'] ?? 3;
					$sliderStep = $input['sliderStep'] ?? 1;
					$default = $input['default'] ?? 1;
					$form->addElement(new Slider((string) $label, (int) $sliderMin, (int) $sliderMax, (int) $sliderStep, (int) $default));
					break;
				case 'toggle':
					$default = $input['default'] ?? false;
					$form->addElement(new Toggle((string) $label, (bool) $default));
					break;
				case 'dropdown':
					$default = $input['default'] ?? 0;
					$dropdownOptions = $input['dropdownOptions'] ?? [];
					$form->addElement(new Dropdown((string) $label, (array) $dropdownOptions, (int) $default));
					break;
				default:
					throw new RuntimeException("Unknown form element type: {$type}");
			}
		}

		$form->onResponse($onSubmit);
		$player->sendForm($form);
	}

	/**
	 * @param Closure(ModalFormResponse $response): void $onSubmit
	 */
	public static function displayModalForm(Player $player, string $title, string $content, string $button1, string $button2, Closure $onSubmit) : void {
		$form = new ModalForm($title);
		$form->setBody($content);
		$form->setSubmit($button1);
		$form->setCancel($button2);
		$form->onResponse($onSubmit);

		$player->sendForm($form);
	}
}
