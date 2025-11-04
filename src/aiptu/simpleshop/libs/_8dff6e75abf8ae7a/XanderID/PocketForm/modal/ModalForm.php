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

namespace aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\modal;

use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\PocketForm;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\traits\Body;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\traits\Submit;

/**
 * Represents a modal form with two buttons (submit and cancel).
 *
 * @extends PocketForm<ModalFormResponse>
 */
class ModalForm extends PocketForm {
	use Body;
	use Submit;

	/**
	 * Default submit text.
	 *
	 * @var string
	 */
	public const DEFAULT_SUBMIT = 'gui.yes';

	/**
	 * Default cancel text.
	 *
	 * @var string
	 */
	public const DEFAULT_CANCEL = 'gui.no';

	/**
	 * @param string $title the title of the modal form
	 */
	public function __construct(string $title) {
		$this->setSubmit(self::DEFAULT_SUBMIT);
		$this->setCancel(self::DEFAULT_CANCEL);
		parent::__construct($title);
	}

	/**
	 * Creates a new ModalForm instance.
	 *
	 * @param string $title form title
	 * @param string $body  form body (optional)
	 */
	public static function create(string $title, string $body = '') : self {
		$form = new self($title);
		$form->setBody($body);
		return $form;
	}

	/**
	 * Get the form type.
	 *
	 * @return string returns "modal"
	 */
	protected function getType() : string {
		return 'modal';
	}

	/**
	 * Get the response class name.
	 *
	 * @return string returns the class name for modal form responses
	 */
	protected function getResponseClass() : string {
		return ModalFormResponse::class;
	}

	/**
	 * Get the callback signature for modal form responses.
	 *
	 * @return callable the expected signature for the onResponse callback
	 */
	protected function getSignature() : callable {
		return function (ModalFormResponse $response) : void {};
	}

	/**
	 * Initialize modal form components.
	 *
	 * @return array<string, mixed> an associative array of components including body, submit, and cancel texts
	 */
	protected function initComponents() : array {
		return [
			'content' => $this->body,
			'button1' => $this->submit,
			'button2' => $this->cancel,
		];
	}
}