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

use Closure;
use pocketmine\player\Player;
use pocketmine\utils\Utils;

/**
 * Provides functionality to attach and invoke an onClick callback for an element.
 */
trait onClick {
	/** @var Closure|null callback for onClick events */
	protected ?Closure $onClickListener = null;

	/**
	 * Set the onClick callback.
	 *
	 * @param Closure $onClick a callback that takes a Player as parameter
	 */
	public function onClick(Closure $onClick) : static {
		Utils::validateCallableSignature(function (Player $player) : void {}, $onClick);
		$this->onClickListener = $onClick;
		return $this;
	}

	/**
	 * Check if an onClick callback is set.
	 *
	 * @return bool true if an onClick callback is set, false otherwise
	 */
	public function isOnClick() : bool {
		return $this->onClickListener !== null;
	}

	/**
	 * Invoke the onClick callback.
	 *
	 * @param Player $player the player triggering the onClick event
	 */
	public function onClickCall(Player $player) : void {
		$this->onClickListener?->__invoke($player);
	}
}