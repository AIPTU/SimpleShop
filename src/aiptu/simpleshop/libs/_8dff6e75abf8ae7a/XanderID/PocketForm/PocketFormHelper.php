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

namespace aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm;

use Closure;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\custom\CustomForm;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\element\Element;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\modal\ModalForm;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\simple\element\Button;
use aiptu\simpleshop\libs\_8dff6e75abf8ae7a\XanderID\PocketForm\simple\SimpleForm;

/**
 * Provides helper methods to easily create various types of forms.
 */
class PocketFormHelper {
	/**
	 * Create a menu form (SimpleForm) with buttons.
	 *
	 * @param string       $title      the title of the form
	 * @param string       $body       the body content of the form
	 * @param list<Button> $buttons    an array of Button instance
	 * @param Closure      $onResponse the callback to handle the response
	 *
	 * @return SimpleForm returns a SimpleForm instance
	 *
	 * @throws PocketFormException if any value in the $buttons array is not a Button instance
	 */
	public static function menu(string $title, string $body, array $buttons, Closure $onResponse) : SimpleForm {
		if (Utils::validateArrayValueType($buttons, function (Button $button) : void {})) {
			throw new PocketFormException('Failed to build buttons Element: Buttons array can only be Button instances!');
		}

		$form = new SimpleForm($title);
		$form->setBody($body);
		$form->onResponse($onResponse);
		$form->mergeElements($buttons);

		return $form;
	}

	/**
	 * Create a confirmation form (ModalForm) with custom submit and cancel texts.
	 *
	 * @param string  $title      the title of the confirmation form
	 * @param string  $body       the body content of the confirmation form
	 * @param Closure $onResponse the callback to handle the confirmation response
	 * @param string  $submit     the text for the submit button (default is ModalForm::DEFAULT_SUBMIT)
	 * @param string  $cancel     the text for the cancel button (default is ModalForm::DEFAULT_CANCEL)
	 *
	 * @return ModalForm returns a ModalForm instance
	 */
	public static function confirm(string $title, string $body, Closure $onResponse, string $submit = ModalForm::DEFAULT_SUBMIT, string $cancel = ModalForm::DEFAULT_CANCEL) : ModalForm {
		$form = new ModalForm($title);
		$form->setBody($body);
		$form->setSubmit($submit);
		$form->setCancel($cancel);
		$form->onResponse($onResponse);
		return $form;
	}

	/**
	 * Create a custom form (CustomForm) with the specified elements.
	 *
	 * @param string        $title      the title of the custom form
	 * @param list<Element> $elements   an array of elements to be added to the form
	 * @param Closure       $onResponse the callback to handle the custom form response
	 * @param string        $submit     the text for the submit button (default is CustomForm::DEFAULT_SUBMIT)
	 *
	 * @return CustomForm returns a CustomForm instance
	 *
	 * @throws PocketFormException if the elements array contains invalid element types
	 */
	public static function custom(string $title, array $elements, Closure $onResponse, string $submit = CustomForm::DEFAULT_SUBMIT) : CustomForm {
		Utils::validateArrayElement($elements, 'Failed to build Elements');
		$form = new CustomForm($title);
		$form->setSubmit($submit);
		$form->onResponse($onResponse);
		$form->setElements($elements);
		return $form;
	}
}