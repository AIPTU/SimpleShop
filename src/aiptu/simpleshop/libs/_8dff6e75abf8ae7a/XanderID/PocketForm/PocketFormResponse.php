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

use pocketmine\player\Player;

/**
 * Represents a response from a form submitted by a player.
 *
 * @template F of PocketForm
 */
abstract class PocketFormResponse {
	/** @var F */
	public PocketForm $form;

	/**
	 * @param Player $player the player who submitted the response
	 * @param F      $form   the form that was submitted
	 * @param mixed  $data   the raw response data
	 */
	public function __construct(
		public Player $player,
		PocketForm $form,
		public mixed $data
	) {
		$this->form = $form;
		$this->processData($data);
	}

	/**
	 * Get the player who submitted the response.
	 */
	public function getPlayer() : Player {
		return $this->player;
	}

	/**
	 * Get the raw response data.
	 *
	 * @return mixed the unprocessed response data
	 */
	public function getRawData() : mixed {
		return $this->data;
	}

	/**
	 * Get the form that was submitted.
	 *
	 * @return F
	 */
	public function getForm() : PocketForm {
		return $this->form;
	}

	/**
	 * Process the raw response data.
	 *
	 * This method should transform or validate the data as needed.
	 *
	 * @param mixed $data the raw response data
	 */
	abstract public function processData(mixed $data) : void;
}