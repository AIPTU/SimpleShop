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

/**
 * Provides methods to set the submit and cancel texts for a form.
 */
trait Submit {
	/** @var string the text for the submit button */
	protected string $submit = 'Yes';

	/** @var string the text for the cancel button */
	protected string $cancel = 'Cancel';

	/**
	 * Set the submit text.
	 *
	 * @param string $submit the new submit text
	 */
	public function setSubmit(string $submit) : static {
		$this->submit = $submit;
		return $this;
	}

	/**
	 * Set the cancel text.
	 *
	 * @param string $cancel the new cancel text
	 */
	public function setCancel(string $cancel) : static {
		$this->cancel = $cancel;
		return $this;
	}
}