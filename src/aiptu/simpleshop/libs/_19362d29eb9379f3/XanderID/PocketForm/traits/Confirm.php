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

use aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\modal\ModalForm;

/**
 * Provides functionality to attach a confirmation form to a form element.
 */
trait Confirm {
	/** @var ModalForm|null the confirmation form */
	protected ?ModalForm $confirmForm = null;

	/**
	 * Set up a confirmation form.
	 *
	 * @param string $title  the title of the confirmation form
	 * @param string $body   the body content of the confirmation form
	 * @param string $submit the submit button text (default is ModalForm::DEFAULT_SUBMIT)
	 * @param string $cancel the cancel button text (default is ModalForm::DEFAULT_CANCEL)
	 */
	public function confirm(string $title, string $body, string $submit = ModalForm::DEFAULT_SUBMIT, string $cancel = ModalForm::DEFAULT_CANCEL) : static {
		$form = new ModalForm($title);
		$form->setBody($body);
		$form->setSubmit($submit);
		$form->setCancel($cancel);
		$this->confirmForm = $form;
		return $this;
	}

	/**
	 * Get the confirmation form.
	 *
	 * @return ModalForm|null the attached confirmation form, or null if not set
	 */
	public function getConfirm() : ?ModalForm {
		return $this->confirmForm;
	}
}