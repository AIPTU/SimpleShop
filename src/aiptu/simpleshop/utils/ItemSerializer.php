<?php

/*
 * Copyright (c) 2021-2025 AIPTU
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/AIPTU/BlockReplacer
 */

declare(strict_types=1);

namespace aiptu\simpleshop\utils;

use pocketmine\item\Item;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;
use function base64_decode;
use function base64_encode;

/**
 * @no-named-arguments
 */
class ItemSerializer {
	public static function serializeItem(Item $item) : string {
		$serializer = new LittleEndianNbtSerializer();
		return base64_encode($serializer->write(new TreeRoot($item->nbtSerialize())));
	}

	public static function deserializeItem(string $data) : Item {
		$binary = base64_decode($data, true);
		if ($binary === false) {
			throw new \RuntimeException('Invalid base64 input.');
		}

		$serializer = new LittleEndianNbtSerializer();
		return Item::nbtDeserialize(
			$serializer->read($binary)->mustGetCompoundTag()
		);
	}

	public static function safeDeserializeItem(string $data) : ?Item {
		try {
			return self::deserializeItem($data);
		} catch (\Throwable $e) {
			return null;
		}
	}
}
