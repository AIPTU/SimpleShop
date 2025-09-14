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

namespace aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\element;

use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\traits\FormLabel;
use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\Utils;

/**
 * Represents a label element used to display static text.
 */
class Label extends UniversalElement implements ReadonlyElement {
	use FormLabel;

	/**
	 * @param string $label the text for the label element
	 */
	public function __construct(string $label) {
		$this->setLabel($label);
	}

	/**
	 * Creates a new Label element.
	 *
	 * @param string $label the text for the label element
	 */
	public static function create(string $label) : self {
		return new self($label);
	}

	/**
	 * Returns the supported form type.
	 * Only works in CustomForm due to lack of key support in SimpleForm.
	 *
	 * @return array<string> The supported form type
	 */
	public function supportForm() : array {
		return [Utils::FORM_TYPES[0], Utils::FORM_TYPES[2]];
	}

	/**
	 * Get the element type.
	 *
	 * @return string returns "label"
	 */
	public function getType() : string {
		return 'label';
	}

	/**
	 * Build the label element.
	 *
	 * @param array<string, list<array<string, mixed>>> &$components The components array to add the label to
	 */
	public function build(array &$components) : void {
		$label = ['type' => $this->getType(), 'text' => $this->label];
		$this->initBuild($components, $label);
	}
}