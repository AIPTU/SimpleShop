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

use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\Utils;

/**
 * Represents a divider element used to display a horizontal line in the form.
 * WARNING: can be used on Minecraft Version 1.21.70 and above only.
 */
class Divider extends UniversalElement implements ReadonlyElement {
	/**
	 * Creates a new Divider element.
	 */
	public static function create() : self {
		return new self();
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
	 * @return string Returns "divider"
	 */
	public function getType() : string {
		return 'divider';
	}

	/**
	 * Build the divider element.
	 *
	 * @param array<string, list<array<string, mixed>>> &$components The components array to add the divider to
	 */
	public function build(array &$components) : void {
		$divider = ['type' => $this->getType(), 'text' => ''];
		$this->initBuild($components, $divider);
	}
}