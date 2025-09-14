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

namespace aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\modal;

use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\PocketFormException;
use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\PocketFormResponse;
use function gettype;
use function is_bool;

/**
 * Processes the response data from a modal form.
 *
 * @extends PocketFormResponse<ModalForm>
 */
class ModalFormResponse extends PocketFormResponse {
	protected bool $choice;

	/**
	 * Process the raw response data.
	 *
	 * @param mixed $data the raw response data
	 *
	 * @throws PocketFormException if the response data is not a boolean
	 */
	public function processData(mixed $data) : void {
		if (!is_bool($data)) {
			throw new PocketFormException('Expected bool got ' . gettype($data));
		}

		$this->choice = $data;
	}

	public function getChoice() : bool {
		return $this->choice;
	}
}