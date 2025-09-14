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

namespace aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\traits;

use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\element\Element;
use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\Utils;
use function array_merge;
use function array_values;
use function in_array;

/**
 * Provides methods for managing a collection of elements.
 */
trait Elements {
	/** @var array<int, Element> list of elements */
	protected array $elements = [];

	/**
	 * Get an element by its index.
	 *
	 * @param int $index the index of the element
	 *
	 * @return Element|null returns the element if found, or null otherwise
	 */
	public function getElement(int $index) : ?Element {
		return $this->elements[$index] ?? null;
	}

	/**
	 * Set the entire array of elements.
	 *
	 * @param list<Element> $elements an array of Element instances
	 *
	 * @return static returns the current instance
	 *
	 * @throws \Exception if validation fails
	 */
	public function setElements(array $elements) : static {
		Utils::validateArrayElement($elements, 'Failed to set Elements');
		$this->elements = $elements;
		return $this;
	}

	/**
	 * Get all elements.
	 *
	 * @return array<int, Element> an array of Element instances
	 */
	public function getElements() : array {
		return $this->elements;
	}

	/**
	 * Add an element to the collection.
	 *
	 * @param Element $element the element to add
	 *
	 * @return static returns the current instance
	 */
	public function addElement(Element $element) : static {
		$this->elements[] = $element;
		return $this;
	}

	/**
	 * Set an element at a specific index.
	 *
	 * @param int     $index   the index to set
	 * @param Element $element the element to set
	 *
	 * @return static returns the current instance
	 */
	public function setElement(int $index, Element $element) : static {
		$this->elements[$index] = $element;
		return $this;
	}

	/**
	 * Remove an element by its index.
	 *
	 * @param int $index the index of the element to remove
	 *
	 * @return static returns the current instance
	 */
	public function removeElement(int $index) : static {
		if (isset($this->elements[$index])) {
			unset($this->elements[$index]);
			$this->elements = array_values($this->elements);
		}

		return $this;
	}

	/**
	 * Merge additional elements into the current collection.
	 *
	 * @param list<Element> $elements an array of Element instances to merge
	 *
	 * @return static returns the current instance
	 *
	 * @throws \Exception if validation fails
	 */
	public function mergeElements(array $elements) : static {
		Utils::validateArrayElement($elements, 'Failed to merge Elements');
		$this->elements = array_merge($this->elements, $elements);
		return $this;
	}

	/**
	 * Build all elements and add them to the components array.
	 *
	 * @param array<string, mixed> &$components The components array to which elements will be added
	 *
	 * @internal
	 */
	public function buildElements(array &$components) : void {
		foreach ($this->getElements() as $element) {
			$formType = $this->getType();
			if (!in_array($formType, $element->supportForm(), true)) {
				$element->buildError('You cannot use this element with the selected form type: ' . $formType);
			}

			$element->buildCheck();
			$element->build($components);
		}
	}
}