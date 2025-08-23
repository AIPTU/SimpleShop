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
class ShopCategory extends AbstractCategory {
	/** @var array<string, ShopItem> */
	private array $items = [];
	/** @var array<string, ShopSubCategory> */
	private array $subCategories = [];

	/**
	 * @param array<string, mixed> $data
	 *
	 * @phpstan-param array<string, mixed> $data
	 *
	 * @throws RuntimeException
	 */
	public static function fromArray(string $id, array $data) : self {
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

			$category = new self($id, $name, $description, $priority, $imageSrc, $imageType, $permission, $hidden);

			if (isset($data['items']) && is_array($data['items'])) {
				/** @var array<string, array<string, mixed>> $itemsData */
				$itemsData = $data['items'];
				foreach ($itemsData as $itemId => $itemData) {
					if (is_string($itemId) && is_array($itemData)) {
						$category->items[$itemId] = ShopItem::fromArray($itemId, $itemData);
					}
				}
			}

			if (isset($data['sub_categories']) && is_array($data['sub_categories'])) {
				/** @var array<string, array<string, mixed>> $subcatsData */
				$subcatsData = $data['sub_categories'];
				foreach ($subcatsData as $subId => $subData) {
					if (is_string($subId) && is_array($subData)) {
						$category->subCategories[$subId] = ShopSubCategory::fromArray($category, $subId, $subData);
					}
				}
			}
		} catch (InvalidArgumentException $e) {
			throw new RuntimeException("Invalid data for category '{$id}': {$e->getMessage()}", 0, $e);
		}

		return $category;
	}

	public function __construct(string $id, string $name, string $description, int $priority, string $imageSrc, ImageType $imageType, string $permission, bool $hidden = false) {
		parent::__construct($id, $name, $description, $priority, $imageSrc, $imageType, $permission, $hidden);
	}

	public function addItem(ShopItem $item) : void {
		$this->items[$item->getId()] = $item;
		SimpleShop::getInstance()->saveAll();
	}

	public function removeItem(string $itemId) : void {
		unset($this->items[$itemId]);
		SimpleShop::getInstance()->saveAll();
	}

	public function addSubCategory(ShopSubCategory $category) : void {
		$this->subCategories[$category->getId()] = $category;
		SimpleShop::getInstance()->saveAll();
	}

	public function removeSubCategory(string $id) : void {
		unset($this->subCategories[$id]);
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

	public function getSubCategory(string $id) : ?ShopSubCategory {
		return $this->subCategories[$id] ?? null;
	}

	/**
	 * @return list<ShopSubCategory>
	 */
	public function getSubCategories() : array {
		return array_values($this->subCategories);
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray() : array {
		$items = [];
		foreach ($this->items as $item) {
			$items[$item->getId()] = $item->toArray();
		}

		$subCategories = [];
		foreach ($this->subCategories as $subCat) {
			$subCategories[$subCat->getId()] = $subCat->toArray();
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
			'sub_categories' => $subCategories,
		];
	}
}
