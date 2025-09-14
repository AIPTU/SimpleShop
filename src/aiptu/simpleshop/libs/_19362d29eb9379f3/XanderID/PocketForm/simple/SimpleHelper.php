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

namespace aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\simple;

use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\element\Divider;
use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\element\Header;
use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\element\Label;
use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\PocketFormException;
use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\simple\element\Button;
use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\simple\element\ButtonImage;
use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\Utils;

/**
 * Provides helper methods to add buttons to a simple form.
 */
trait SimpleHelper {
	/**
	 * Add a button element.
	 *
	 * @param string   $text      the text for the button
	 * @param int|null $imageType the image type (0 for PATH, otherwise URL)
	 * @param string   $imageUri  the URI for the button image
	 */
	public function addButton(string $text, ?int $imageType = null, string $imageUri = '') : static {
		$image = $imageType !== null ? ButtonImage::create($imageType, $imageUri) : null;
		$button = new Button($text, $image);
		$this->addElement($button);
		return $this;
	}

	/**
	 * Add multiple buttons.
	 *
	 * @param list<string> $buttons an array of button texts
	 *
	 * @throws PocketFormException if any of the values in the array are not strings
	 */
	public function addButtons(array $buttons) : static {
		if (Utils::validateArrayValueType($buttons, function (string $button) : void {})) {
			throw new PocketFormException('Failed to build buttons Element: Buttons array can only be strings!');
		}

		foreach ($buttons as $text) {
			$button = new Button($text);
			$this->addElement($button);
		}

		return $this;
	}

	/**
	 * Add a label element.
	 *
	 * @param string $label the text for the label element
	 */
	public function addLabel(string $label) : static {
		$labelElement = new Label($label);
		$this->addElement($labelElement);
		return $this;
	}

	/**
	 * Add a header element.
	 *
	 * This is a non-interactive element used for labeling sections.
	 * WARNING: Only supported in Minecraft version 1.21.70 and above.
	 *
	 * @param string $text the text to display in the header
	 *
	 * @return $this
	 */
	public function addHeader(string $text) : static {
		$header = new Header($text);
		$this->addElement($header);
		return $this;
	}

	/**
	 * Add a divider element.
	 *
	 * This is a non-interactive horizontal line used to separate sections.
	 * WARNING: Only supported in Minecraft version 1.21.70 and above.
	 *
	 * @return $this
	 */
	public function addDivider() : static {
		$divider = new Divider();
		$this->addElement($divider);
		return $this;
	}
}