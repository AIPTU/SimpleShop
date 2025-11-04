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

/**
 * Determines whether the element is compatible with multiple form types.
 *
 * A universal element can be used across any form type that supports it,
 * based on the implementation of the supportForm() method.
 *
 * This abstract class serves as a shared base for such elements,
 * providing common logic while allowing custom behavior through extensions.
 */
abstract class UniversalElement extends Element {
	/**
	 * Adds the element to the appropriate section of the form components.
	 *
	 * If 'elements' exists, the component is added there.
	 * If not, and 'content' exists, the component is added to 'content'.
	 * If neither exists, the component is ignored.
	 *
	 * @param array<string, list<array<string, mixed>>> &$components The form component array (by reference)
	 * @param array<string, mixed>                      $component   The component to add
	 */
	public function initBuild(array &$components, array $component) : void {
		if (isset($components['elements'])) {
			$components['elements'][] = $component;
			return;
		}

		if (isset($components['content'])) {
			$components['content'][] = $component;
		}
	}
}