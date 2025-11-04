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

namespace aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\element;

use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\traits\FormLabel;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\Utils;

/**
 * Represents a header element used to display a section title in the form.
 * WARNING: can be used on Minecraft Version 1.21.70 and above only.
 */
class Header extends UniversalElement implements ReadonlyElement {
	use FormLabel;

	/**
	 * @param string $header the text for the header element
	 */
	public function __construct(string $header) {
		$this->setLabel($header);
	}

	/**
	 * Creates a new Header element.
	 *
	 * @param string $header the text for the header element
	 */
	public static function create(string $header) : self {
		return new self($header);
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
	 * @return string returns "header"
	 */
	public function getType() : string {
		return 'header';
	}

	/**
	 * Build the header element.
	 *
	 * @param array<string, list<array<string, mixed>>> &$components The components array to add the header to
	 */
	public function build(array &$components) : void {
		$header = ['type' => $this->getType(), 'text' => $this->label];
		$this->initBuild($components, $header);
	}
}