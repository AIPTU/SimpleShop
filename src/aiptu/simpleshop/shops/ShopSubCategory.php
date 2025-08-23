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

namespace aiptu\simpleshop\shops;

use aiptu\simpleshop\SimpleShop;
use aiptu\simpleshop\utils\ImageType;
use aiptu\simpleshop\utils\PropertyValidator;
use InvalidArgumentException;
use RuntimeException;
use function array_values;
use function implode;
use function is_array;
use function is_string;

/**
 * @no-named-arguments
 */
class ShopSubCategory extends AbstractCategory {
	private ShopCategory $parent;
	/** @var array<string, ShopItem> */
	private array $items = [];

	/**
	 * @param array<string, mixed> $data
	 *
	 * @phpstan-param array<string, mixed> $data
	 *
	 * @throws RuntimeException
	 */
	public static function fromArray(ShopCategory $parent, string $id, array $data) : self {
		try {
			PropertyValidator::validateKeys($data, 'name', 'description', 'priority', 'permission', 'hidden');

			$name = PropertyValidator::getRequiredString('name', $data);
			$description = PropertyValidator::getRequiredString('description', $data);
			$priority = PropertyValidator::getRequiredInt('priority', $data);
			$permission = PropertyValidator::getRequiredString('permission', $data);
			$hidden = PropertyValidator::getRequiredBool('hidden', $data);

			$imageSrc = PropertyValidator::getOptionalString('image_source', $data) ?? '';
			$imageType = ImageType::PATH;
			if (isset($data['image_type'])) {
				$parsedImageType = ImageType::tryFrom(PropertyValidator::getRequiredString('image_type', $data));
				if ($parsedImageType === null) {
					throw new RuntimeException('Invalid image type. Supported types are: ' . implode(', ', ImageType::values()));
				}

				$imageType = $parsedImageType;
			}

			$sub = new self($parent, $id, $name, $description, $priority, $imageSrc, $imageType, $permission, $hidden);

			if (isset($data['items']) && is_array($data['items'])) {
				/** @var array<string, array<string, mixed>> $subItemsData */
				$subItemsData = $data['items'];
				foreach ($subItemsData as $subItemId => $subItemData) {
					if (is_string($subItemId) && is_array($subItemData)) {
						$sub->items[$subItemId] = ShopItem::fromArray($subItemId, $subItemData);
					}
				}
			}
		} catch (InvalidArgumentException $e) {
			throw new RuntimeException("Invalid data for subcategory '{$id}': {$e->getMessage()}", 0, $e);
		}

		return $sub;
	}

	public function __construct(ShopCategory $parent, string $id, string $name, string $description, int $priority, string $imageSrc, ImageType $imageType, string $permission, bool $hidden = false) {
		parent::__construct($id, $name, $description, $priority, $imageSrc, $imageType, $permission, $hidden);
		$this->parent = $parent;
	}

	public function getParent() : ShopCategory {
		return $this->parent;
	}

	public function addItem(ShopItem $item) : void {
		$this->items[$item->getId()] = $item;
		SimpleShop::getInstance()->saveAll();
	}

	public function removeItem(string $itemId) : void {
		unset($this->items[$itemId]);
		SimpleShop::getInstance()->saveAll();
	}

	public function getItem(string $itemId) : ?ShopItem {
		return $this->items[$itemId] ?? null;
	}

	/**
	 * @return list<ShopItem>
	 */
	public function getItems() : array {
		return array_values($this->items);
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray() : array {
		$items = [];
		foreach ($this->items as $item) {
			$items[$item->getId()] = $item->toArray();
		}

		return [
			'name' => $this->name,
			'description' => $this->description,
			'priority' => $this->priority,
			'image_source' => $this->imageSrc,
			'image_type' => $this->imageType->value,
			'hidden' => $this->hidden,
			'permission' => $this->permission,
			'items' => $items,
		];
	}
}
