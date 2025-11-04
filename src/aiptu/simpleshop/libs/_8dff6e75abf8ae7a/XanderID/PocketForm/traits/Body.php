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

namespace aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\traits;

/**
 * Provides methods to set and get the body content for simple and modal forms.
 */
trait Body {
	/** @var string the body content */
	protected string $body = '';

	/**
	 * Set the body content.
	 *
	 * @param string $body the content to set
	 */
	public function setBody(string $body) : static {
		$this->body = $body;
		return $this;
	}

	/**
	 * Get the body content.
	 *
	 * @return string the current body content
	 */
	public function getBody() : string {
		return $this->body;
	}
}