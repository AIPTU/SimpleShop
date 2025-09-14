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
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use function array_filter;
use function array_values;
use function is_array;
use function is_string;
use function usort;

/**
 * @no-named-arguments
 */
class ShopManager {
	/** @var array<string, ShopCategory> */
	private array $categories = [];
	private Config $config;

	public function __construct(
		private string $filePath
	) {
		$this->config = new Config($this->filePath, Config::JSON);
		$this->load();

		$this->registerPermissions();
	}

	public function addCategory(ShopCategory $category) : void {
		$this->categories[$category->getId()] = $category;
		self::registerCategoryPermission($category);
		SimpleShop::getInstance()->saveAll();
	}

	public function removeCategory(string $id) : void {
		$category = $this->categories[$id] ?? null;
		if ($category !== null) {
			unset($this->categories[$id]);
			self::unregisterCategoryPermission($category);
		}

		SimpleShop::getInstance()->saveAll();
	}

	public function getCategory(string $id) : ?ShopCategory {
		return $this->categories[$id] ?? null;
	}

	/**
	 * @return list<ShopCategory>
	 */
	public function getCategories() : array {
		return array_values($this->categories);
	}

	/**
	 * @return list<ShopCategory>
	 */
	public function getVisibleCategories(Player $player) : array {
		return array_values(array_filter($this->categories, function (ShopCategory $cat) use ($player) : bool {
			$perm = $cat->getPermission();
			return !$cat->isHidden() || $player->hasPermission($perm);
		}));
	}

	/**
	 * @return list<ShopCategory>
	 */
	public function getSortedCategories(Player $player) : array {
		$categories = $this->getVisibleCategories($player);
		usort($categories, fn (ShopCategory $a, ShopCategory $b) => $a->getPriority() <=> $b->getPriority());
		return $categories;
	}

	private function load() : void {
		/** @var array<string, array<string, mixed>> $allData */
		$allData = $this->config->getAll();

		foreach ($allData as $catId => $catRawData) {
			if (!is_string($catId) || !is_array($catRawData)) {
				continue;
			}

			$category = ShopCategory::fromArray($catId, $catRawData);
			$this->categories[$category->getId()] = $category;
		}
	}

	public function save() : void {
		/** @var array<string, array<string, mixed>> $data */
		$data = [];
		foreach ($this->categories as $cat) {
			$data[$cat->getId()] = $cat->toArray();
		}

		$this->config->setAll($data);
		$this->config->save();
	}

	/**
	 * Registers permissions for a single ShopCategory and its subcategories.
	 */
	private static function registerCategoryPermission(ShopCategory $category) : void {
		$pm = PermissionManager::getInstance();
		$baseCategoryPerm = $pm->getPermission('simpleshop.category');

		$catPermId = $category->getPermission();
		if ($pm->getPermission($catPermId) === null) {
			$categoryPermission = new Permission($catPermId, "Allows access to category: {$category->getName()}");
			$pm->addPermission($categoryPermission);
			$baseCategoryPerm?->addChild($catPermId, true);
		}

		foreach ($category->getSubCategories() as $sub) {
			$subPermId = $sub->getPermission();
			if ($pm->getPermission($subPermId) === null) {
				$subCategoryPermission = new Permission($subPermId, "Allows access to subcategory: {$sub->getName()}");
				$pm->addPermission($subCategoryPermission);
				$pm->getPermission($catPermId)?->addChild($subPermId, true);
				$baseCategoryPerm?->addChild($subPermId, true);
			}
		}
	}

	/**
	 * Unregisters permissions for a single ShopCategory and its subcategories.
	 */
	private static function unregisterCategoryPermission(ShopCategory $category) : void {
		$pm = PermissionManager::getInstance();

		$catPermId = $category->getPermission();
		if ($pm->getPermission($catPermId) !== null) {
			$pm->removePermission($catPermId);
		}

		foreach ($category->getSubCategories() as $sub) {
			$subPermId = $sub->getPermission();
			if ($pm->getPermission($subPermId) !== null) {
				$pm->removePermission($subPermId);
			}
		}
	}

	/**
	 * Registers permissions for all currently loaded categories and their subcategories.
	 * This is primarily called during plugin startup.
	 */
	private function registerPermissions() : void {
		foreach ($this->categories as $category) {
			self::registerCategoryPermission($category);
		}
	}
}