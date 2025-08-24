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

/**
 * @no-named-arguments
 */
abstract class AbstractCategory {
	public function __construct(
		protected string $id,
		protected string $name,
		protected string $description,
		protected int $priority,
		protected string $imageSrc = '',
		protected ImageType $imageType = ImageType::PATH,
		protected string $permission,
		protected bool $hidden = false
	) {}

	public function getId() : string {
		return $this->id;
	}

	public function getName() : string {
		return $this->name;
	}

	public function getDescription() : string {
		return $this->description;
	}

	public function getPriority() : int {
		return $this->priority;
	}

	public function getImageSource() : string {
		return $this->imageSrc;
	}

	public function getImageType() : ImageType {
		return $this->imageType;
	}

	public function isHidden() : bool {
		return $this->hidden;
	}

	public function getPermission() : string {
		return $this->permission;
	}

	abstract public function addItem(ShopItem $item) : void;

	abstract public function removeItem(string $itemId) : void;

	abstract public function getItem(string $itemId) : ?ShopItem;

	/**
	 * @return list<ShopItem>
	 */
	abstract public function getItems() : array;

	/**
	 * @return array<string, mixed>
	 */
	abstract public function toArray() : array;
}
