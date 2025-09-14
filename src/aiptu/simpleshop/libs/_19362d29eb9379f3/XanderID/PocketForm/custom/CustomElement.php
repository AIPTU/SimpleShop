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

namespace aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\custom;

use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\custom\element\Input;
use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\custom\validator\TypeValidator;
use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\element\Element;
use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\PocketFormException;
use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\traits\FormLabel;
use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\traits\FormValidator;
use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\traits\FormValue;
use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\traits\Tooltip;
use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\Utils;

/**
 * Represents a custom form element with additional functionalities like validation and value storage.
 */
abstract class CustomElement extends Element {
	use FormLabel;
	use FormValidator;
	use FormValue;
	use Tooltip;

	/**
	 * Returns the supported form type for this element.
	 *
	 * @return array<string> The supported form type
	 */
	public function supportForm() : array {
		return [Utils::FORM_TYPES[2]];
	}

	/**
	 * Perform build check for the CustomElement.
	 *
	 * @throws PocketFormException if an invalid validator is used
	 */
	public function buildCheck() : void {
		$validator = $this->getValidator();
		if ($validator === null) {
			return;
		}

		if (!($this instanceof Input) && $validator instanceof TypeValidator) {
			throw new PocketFormException('This element cannot use TypeValidator!');
		}
	}
}