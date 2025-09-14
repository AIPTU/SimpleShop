<?php

/*
 * Copyright (c) 2025 AIPTU
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/AIPTU/SimpleShop
 */

declare(strict_types=1);

namespace aiptu\simpleshop\shops;

use aiptu\simpleshop\utils\ImageType;
use aiptu\simpleshop\utils\ItemSerializer;
use aiptu\simpleshop\utils\PropertyValidator;
use InvalidArgumentException;
use pocketmine\item\Item;
use RuntimeException;
use function implode;

/**
 * @no-named-arguments
 */
readonly class ShopItem {
	public function __construct(
		private string $id,
		private Item $item,
		private float $buyPrice,
		private float $sellPrice,
		private bool $canBuy = true,
		private bool $canSell = true,
		private string $imageSrc = '',
		private ImageType $imageType = ImageType::PATH
	) {}

	public function getId() : string {
		return $this->id;
	}

	public function getItem() : Item {
		return clone $this->item;
	}

	public function getBuyPrice() : float {
		return $this->buyPrice;
	}

	public function getSellPrice() : float {
		return $this->sellPrice;
	}

	public function canBuy() : bool {
		return $this->canBuy;
	}

	public function canSell() : bool {
		return $this->canSell;
	}

	public function getImageSource() : string {
		return $this->imageSrc;
	}

	public function getImageType() : ImageType {
		return $this->imageType;
	}

	/**
	 * @param array<string, mixed> $data
	 *
	 * @phpstan-param array<string, mixed> $data
	 *
	 * @throws RuntimeException
	 */
	public static function fromArray(string $id, array $data) : self {
		try {
			PropertyValidator::validateKeys($data, 'nbt', 'buy', 'sell', 'can_buy', 'can_sell');

			$nbt = PropertyValidator::getRequiredString('nbt', $data);
			$item = ItemSerializer::safeDeserializeItem($nbt);
			if ($item === null) {
				throw new RuntimeException('Failed to deserialize item NBT');
			}

			$buyPrice = PropertyValidator::getRequiredFloat('buy', $data);
			$sellPrice = PropertyValidator::getRequiredFloat('sell', $data);
			$canBuy = PropertyValidator::getRequiredBool('can_buy', $data);
			$canSell = PropertyValidator::getRequiredBool('can_sell', $data);

			$imageSrc = PropertyValidator::getOptionalString('image_source', $data) ?? '';
			$imageType = ImageType::PATH;
			if (isset($data['image_type'])) {
				$imageTypeString = PropertyValidator::getRequiredString('image_type', $data);
				$parsedImageType = ImageType::tryFrom($imageTypeString);
				if ($parsedImageType === null) {
					throw new RuntimeException("Invalid image type '{$imageTypeString}'. Supported types are: " . implode(', ', ImageType::values()));
				}

				$imageType = $parsedImageType;
			}
		} catch (InvalidArgumentException $e) {
			throw new RuntimeException("Invalid data for item '{$id}': {$e->getMessage()}", 0, $e);
		}

		return new self(
			$id,
			$item,
			$buyPrice,
			$sellPrice,
			$canBuy,
			$canSell,
			$imageSrc,
			$imageType
		);
	}

	/**
	 * @return array<string, mixed>
	 *
	 * @phpstan-return array{nbt: string, buy: float, sell: float, can_buy: bool, can_sell: bool, image_source?: string, image_type?: string}
	 */
	public function toArray() : array {
		return [
			'nbt' => ItemSerializer::serializeItem($this->item),
			'buy' => $this->buyPrice,
			'sell' => $this->sellPrice,
			'can_buy' => $this->canBuy,
			'can_sell' => $this->canSell,
			'image_source' => $this->imageSrc,
			'image_type' => $this->imageType->value,
		];
	}
}