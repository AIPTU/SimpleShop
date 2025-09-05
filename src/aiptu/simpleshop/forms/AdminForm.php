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

namespace aiptu\simpleshop\forms;

use aiptu\simpleshop\shops\AbstractCategory;
use aiptu\simpleshop\shops\ShopCategory;
use aiptu\simpleshop\shops\ShopItem;
use aiptu\simpleshop\shops\ShopSubCategory;
use aiptu\simpleshop\SimpleShop;
use aiptu\simpleshop\utils\ImageType;
use aiptu\simpleshop\utils\ItemSerializer;
use aiptu\simpleshop\utils\PermissionUtil;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use XanderID\PocketForm\custom\CustomFormResponse;
use XanderID\PocketForm\modal\ModalFormResponse;
use XanderID\PocketForm\PocketFormHelper;
use XanderID\PocketForm\simple\element\Button;
use XanderID\PocketForm\simple\element\ButtonImage;
use XanderID\PocketForm\simple\SimpleForm;
use XanderID\PocketForm\simple\SimpleFormResponse;
use function array_search;
use function str_replace;
use function strtolower;
use function trim;

/**
 * @no-named-arguments
 */
class AdminForm {
	/**
	 * Sends the main admin menu to the player.
	 */
	public static function sendAdminMainMenu(Player $player) : void {
		$categories = SimpleShop::getInstance()->getShopManager()->getCategories();

		$buttons = [];
		$buttons[] = Button::create(TextFormat::GREEN . 'Add New Category')->onClick(function (Player $player) : void {
			self::sendAddCategoryForm($player);
		});
		foreach ($categories as $category) {
			$buttonText = TextFormat::BOLD . $category->getName() . TextFormat::RESET . "\n" . TextFormat::GRAY . 'Manage Category';
			$image = ButtonImage::create($category->getImageType()->toInt(), $category->getImageSource());
			$buttons[] = Button::create($buttonText, $image);
		}

		$player->sendForm(PocketFormHelper::menu(
			TextFormat::DARK_AQUA . 'Shop Admin Panel',
			'Select a category to manage, or add a new one.',
			$buttons,
			function (SimpleFormResponse $response) use ($categories) : void {
				$player = $response->getPlayer();
				$selectedId = $response->getSelected()->getId();

				$categoryIndex = (int) $selectedId - 1;
				if (isset($categories[$categoryIndex])) {
					self::sendCategoryManagementForm($player, $categories[$categoryIndex]);
				} else {
					$player->sendMessage(TextFormat::RED . 'Invalid category selected.');
				}
			}
		));
	}

	/**
	 * Sends a form to manage a specific shop category.
	 */
	public static function sendCategoryManagementForm(Player $player, ShopCategory $category) : void {
		$buttons = [
			Button::create(TextFormat::GREEN . 'Add New Sub-Category')->onClick(function (Player $player) use ($category) : void {
				self::sendAddSubCategoryForm($player, $category);
			}),
			Button::create(TextFormat::GREEN . 'Add New Item to Category')->onClick(function (Player $player) use ($category) : void {
				self::sendAddItemForm($player, $category);
			}),
			Button::create(TextFormat::YELLOW . 'Edit Category Properties')->onClick(function (Player $player) use ($category) : void {
				self::sendEditCategoryForm($player, $category);
			}),
			Button::create(TextFormat::RED . 'Remove Category')->onClick(function (Player $player) use ($category) : void {
				self::sendRemoveCategoryConfirmation($player, $category);
			}),
		];

		foreach ($category->getSubCategories() as $subCategory) {
			$buttonText = TextFormat::BOLD . $subCategory->getName() . TextFormat::RESET . "\n" . TextFormat::GRAY . 'Manage Sub-Category';
			$image = ButtonImage::create($subCategory->getImageType()->toInt(), $subCategory->getImageSource());
			$buttons[] = Button::create($buttonText, $image)->onClick(function (Player $player) use ($subCategory) : void {
				self::sendSubCategoryManagementForm($player, $subCategory);
			});
		}

		foreach ($category->getItems() as $item) {
			$buttonText = TextFormat::BOLD . $item->getItem()->getName() . TextFormat::RESET . "\n" . TextFormat::GRAY . 'Manage Item';
			$image = ButtonImage::create($item->getImageType()->toInt(), $item->getImageSource());
			$buttons[] = Button::create($buttonText, $image)->onClick(function (Player $player) use ($category, $item) : void {
				self::sendItemManagementForm($player, $item, $category);
			});
		}

		$player->sendForm(
			SimpleForm::create(
				TextFormat::DARK_AQUA . 'Manage Category: ' . $category->getName(),
				'Select an option to manage this category, its sub-categories, or items.'
			)->mergeElements($buttons)
		);
	}

	/**
	 * Sends a form to manage a specific shop sub-category.
	 */
	public static function sendSubCategoryManagementForm(Player $player, ShopSubCategory $subCategory) : void {
		$buttons = [
			Button::create(TextFormat::GREEN . 'Add New Item to Sub-Category')->onClick(function (Player $player) use ($subCategory) : void {
				self::sendAddItemForm($player, $subCategory);
			}),
			Button::create(TextFormat::YELLOW . 'Edit Sub-Category Properties')->onClick(function (Player $player) use ($subCategory) : void {
				self::sendEditSubCategoryForm($player, $subCategory);
			}),
			Button::create(TextFormat::RED . 'Remove Sub-Category')->onClick(function (Player $player) use ($subCategory) : void {
				self::sendRemoveSubCategoryConfirmation($player, $subCategory);
			}),
		];

		foreach ($subCategory->getItems() as $item) {
			$buttonText = TextFormat::BOLD . $item->getItem()->getName() . TextFormat::RESET . "\n" . TextFormat::GRAY . 'Manage Item';
			$image = ButtonImage::create($item->getImageType()->toInt(), $item->getImageSource());
			$buttons[] = Button::create($buttonText, $image)->onClick(function (Player $player) use ($item, $subCategory) : void {
				self::sendItemManagementForm($player, $item, $subCategory);
			});
		}

		$player->sendForm(
			SimpleForm::create(
				TextFormat::DARK_AQUA . 'Manage Sub-Category: ' . $subCategory->getName(),
				'Select an option to manage this sub-category or its items.'
			)->mergeElements($buttons)
		);
	}

	/**
	 * Sends a form to manage a specific shop item.
	 */
	public static function sendItemManagementForm(Player $player, ShopItem $item, AbstractCategory $parentCategory) : void {
		$player->sendForm(
			SimpleForm::create(
				TextFormat::DARK_AQUA . 'Manage Item: ' . $item->getItem()->getName(),
				'Select an option to manage this item.'
			)->mergeElements(
				[
					Button::create(TextFormat::YELLOW . 'Edit Item Properties')->onClick(function (Player $player) use ($item, $parentCategory) : void {
						self::sendEditItemOptions($player, $item, $parentCategory);
					}),
					Button::create(TextFormat::RED . 'Remove Item')->onClick(function (Player $player) use ($item, $parentCategory) : void {
						self::sendRemoveItemConfirmation($player, $item, $parentCategory);
					}),
				]
			)
		);
	}

	/**
	 * Sends a form to add a new shop category.
	 */
	public static function sendAddCategoryForm(Player $player) : void {
		FormHelper::displayCustomForm(
			$player,
			TextFormat::DARK_AQUA . 'Add New Shop Category',
			[
				['type' => 'input', 'label' => 'Category Name', 'placeholder' => 'e.g., Building Blocks'],
				['type' => 'input', 'label' => 'Description (optional)', 'placeholder' => 'Items for construction'],
				['type' => 'input', 'label' => 'Priority (0 for default)', 'placeholder' => '0', 'default' => '0'],
				['type' => 'input', 'label' => 'Image Source (optional)', 'placeholder' => 'URL or path to image'],
				['type' => 'dropdown', 'label' => 'Image Type (optional)', 'dropdownOptions' => ImageType::values(), 'default' => 1],
				['type' => 'toggle', 'label' => 'Hidden (hide from players)', 'default' => false],
				['type' => 'input', 'label' => 'Permission (optional)', 'placeholder' => 'simpleshop.category.blocks'],
			],
			function (CustomFormResponse $response) use ($player) : void {
				[$name, $description, $priority, $imageSrc, $imageTypeIndex, $hidden, $permission] = $response->getValues();

				$name = trim((string) $name);
				if ($name === '') {
					$player->sendMessage(TextFormat::RED . 'Category Name cannot be empty.');
					return;
				}

				$id = strtolower(str_replace(' ', '_', $name));
				if (SimpleShop::getInstance()->getShopManager()->getCategory($id) !== null) {
					$player->sendMessage(TextFormat::RED . 'A category with this ID already exists. Please use a different name.');
					return;
				}

				$description = trim((string) $description);
				$priority = (int) $priority;
				$hidden = (bool) $hidden;
				$permission = trim((string) $permission);
				if ($permission === '') {
					$permission = PermissionUtil::generateCategoryPermission($id);
				}

				$imageSrc = trim((string) $imageSrc);
				$imageType = ImageType::cases()[$imageTypeIndex] ?? ImageType::PATH;

				$category = new ShopCategory($id, $name, $description, $priority, $imageSrc, $imageType, $permission, $hidden);
				SimpleShop::getInstance()->getShopManager()->addCategory($category);
				$player->sendMessage(TextFormat::GREEN . 'Category "' . $name . '" added successfully.');
				self::sendAdminMainMenu($player);
			}
		);
	}

	/**
	 * Sends a form to add a new sub-category to a given parent category.
	 */
	public static function sendAddSubCategoryForm(Player $player, ShopCategory $parentCategory) : void {
		FormHelper::displayCustomForm(
			$player,
			TextFormat::DARK_AQUA . 'Add New Sub-Category to ' . $parentCategory->getName(),
			[
				['type' => 'input', 'label' => 'Sub-Category Name', 'placeholder' => 'e.g., Wooden Blocks'],
				['type' => 'input', 'label' => 'Description (optional)', 'placeholder' => 'Various types of wooden blocks'],
				['type' => 'input', 'label' => 'Priority (0 for default)', 'placeholder' => '0', 'default' => '0'],
				['type' => 'input', 'label' => 'Image Source (optional)', 'placeholder' => 'URL or path to image'],
				['type' => 'dropdown', 'label' => 'Image Type (optional)', 'dropdownOptions' => ImageType::values(), 'default' => 1],
				['type' => 'toggle', 'label' => 'Hidden (hide from players)', 'default' => false],
				['type' => 'input', 'label' => 'Permission (optional)', 'placeholder' => 'simpleshop.subcategory.wood_blocks'],
			],
			function (CustomFormResponse $response) use ($player, $parentCategory) : void {
				[$name, $description, $priority, $imageSrc, $imageTypeIndex, $hidden, $permission] = $response->getValues();

				$name = trim((string) $name);
				if ($name === '') {
					$player->sendMessage(TextFormat::RED . 'Sub-Category Name cannot be empty.');
					return;
				}

				$id = strtolower(str_replace(' ', '_', $name));
				if ($parentCategory->getSubCategory($id) !== null) {
					$player->sendMessage(TextFormat::RED . 'A sub-category with this ID already exists in this category. Please use a different name.');
					return;
				}

				$description = trim((string) $description);
				$priority = (int) $priority;
				$hidden = (bool) $hidden;
				$permission = trim((string) $permission);
				if ($permission === '') {
					$permission = PermissionUtil::generateSubCategoryPermission($parentCategory->getId(), $id);
				}

				$imageSrc = trim((string) $imageSrc);
				$imageType = ImageType::cases()[$imageTypeIndex] ?? ImageType::PATH;

				$subCategory = new ShopSubCategory($parentCategory, $id, $name, $description, $priority, $imageSrc, $imageType, $permission, $hidden);
				$parentCategory->addSubCategory($subCategory);
				$player->sendMessage(TextFormat::GREEN . 'Sub-Category "' . $name . '" added successfully to ' . $parentCategory->getName() . '.');
				self::sendCategoryManagementForm($player, $parentCategory);
			}
		);
	}

	/**
	 * Sends a form to add a new item to a category or sub-category.
	 *
	 * @param AbstractCategory $parentCategory Can be ShopCategory or ShopSubCategory
	 */
	public static function sendAddItemForm(Player $player, AbstractCategory $parentCategory) : void {
		$heldItem = $player->getInventory()->getItemInHand();
		if ($heldItem->isNull()) {
			$player->sendMessage(TextFormat::RED . 'You must be holding an item to add to the shop.');
			return;
		}

		FormHelper::displayCustomForm(
			$player,
			TextFormat::DARK_AQUA . 'Add New Item to ' . $parentCategory->getName(),
			[
				['type' => 'label', 'label' => 'Adding item: ' . $heldItem->getName()],
				['type' => 'input', 'label' => 'Buy Price', 'placeholder' => '0.0', 'default' => '0.0'],
				['type' => 'toggle', 'label' => 'Can Buy', 'default' => true],
				['type' => 'input', 'label' => 'Sell Price', 'placeholder' => '0.0', 'default' => '0.0'],
				['type' => 'toggle', 'label' => 'Can Sell', 'default' => true],
				['type' => 'input', 'label' => 'Image Source (optional)'],
				['type' => 'dropdown', 'label' => 'Image Type (optional)', 'dropdownOptions' => ImageType::values(), 'default' => 1],
			],
			function (CustomFormResponse $response) use ($player, $parentCategory, $heldItem) : void {
				[$buyPrice, $canBuy, $sellPrice, $canSell, $imageSrc, $imageTypeIndex] = $response->getValues();

				$id = strtolower(str_replace(' ', '_', $heldItem->getVanillaName()));
				if ($parentCategory->getItem($id) !== null) {
					$player->sendMessage(TextFormat::RED . 'An item with this ID (' . $id . ') already exists. Please hold a different item.');
					return;
				}

				$nbt = ItemSerializer::serializeItem($heldItem);
				$buyPrice = (float) $buyPrice;
				$sellPrice = (float) $sellPrice;
				$canBuy = (bool) $canBuy;
				$canSell = (bool) $canSell;
				$imageSrc = trim((string) $imageSrc);
				$imageType = ImageType::cases()[$imageTypeIndex] ?? ImageType::PATH;

				$itemData = [
					'nbt' => $nbt,
					'buy' => $buyPrice,
					'sell' => $sellPrice,
					'can_buy' => $canBuy,
					'can_sell' => $canSell,
					'image_source' => $imageSrc,
					'image_type' => $imageType->value,
				];

				try {
					$shopItem = ShopItem::fromArray($id, $itemData);
				} catch (\RuntimeException $e) {
					$player->sendMessage(TextFormat::RED . 'Error creating item: ' . $e->getMessage());
					return;
				}

				$parentCategory->addItem($shopItem);
				$player->sendMessage(TextFormat::GREEN . 'Item "' . $shopItem->getItem()->getName() . '" added successfully.');

				if ($parentCategory instanceof ShopCategory) {
					self::sendCategoryManagementForm($player, $parentCategory);
				} elseif ($parentCategory instanceof ShopSubCategory) {
					self::sendSubCategoryManagementForm($player, $parentCategory);
				}
			}
		);
	}

	/**
	 * Sends a form to edit an existing category.
	 */
	public static function sendEditCategoryForm(Player $player, ShopCategory $category) : void {
		FormHelper::displayCustomForm(
			$player,
			TextFormat::DARK_AQUA . 'Edit Category: ' . $category->getName(),
			[
				['type' => 'label', 'label' => 'Editing Category ID: ' . $category->getId()],
				['type' => 'input', 'label' => 'Category Name', 'placeholder' => 'e.g., Building Blocks', 'default' => $category->getName()],
				['type' => 'input', 'label' => 'Description (optional)', 'placeholder' => 'Items for construction', 'default' => $category->getDescription()],
				['type' => 'input', 'label' => 'Priority (0 for default)', 'placeholder' => '0', 'default' => (string) $category->getPriority()],
				['type' => 'input', 'label' => 'Image Source (optional)', 'placeholder' => 'URL or path to image', 'default' => $category->getImageSource()],
				['type' => 'dropdown', 'label' => 'Image Type (optional)', 'dropdownOptions' => ImageType::values(), 'default' => array_search($category->getImageType()->value, ImageType::values(), true)],
				['type' => 'toggle', 'label' => 'Hidden (hide from players)', 'default' => $category->isHidden()],
				['type' => 'input', 'label' => 'Permission (optional)', 'placeholder' => 'simpleshop.category.blocks', 'default' => $category->getPermission()],
			],
			function (CustomFormResponse $response) use ($player, $category) : void {
				[$name, $description, $priority, $imageSrc, $imageTypeIndex, $hidden, $permission] = $response->getValues();

				$id = $category->getId();

				$name = trim((string) $name);
				if ($name === '') {
					$player->sendMessage(TextFormat::RED . 'Category Name cannot be empty.');
					self::sendEditCategoryForm($player, $category);
					return;
				}

				$description = trim((string) $description);
				$priority = (int) $priority;
				$hidden = (bool) $hidden;
				$permission = trim((string) $permission);
				if ($permission === '') {
					$permission = PermissionUtil::generateCategoryPermission($id);
				}

				$imageSrc = trim((string) $imageSrc);
				$imageType = ImageType::cases()[$imageTypeIndex] ?? ImageType::PATH;

				$updatedCategory = new ShopCategory($id, $name, $description, $priority, $imageSrc, $imageType, $permission, $hidden);
				SimpleShop::getInstance()->getShopManager()->addCategory($updatedCategory);
				$player->sendMessage(TextFormat::GREEN . 'Category "' . $name . '" updated successfully.');
				self::sendCategoryManagementForm($player, $updatedCategory);
			}
		);
	}

	/**
	 * Sends a form to edit an existing sub-category.
	 */
	public static function sendEditSubCategoryForm(Player $player, ShopSubCategory $subCategory) : void {
		FormHelper::displayCustomForm(
			$player,
			TextFormat::DARK_AQUA . 'Edit Sub-Category: ' . $subCategory->getName(),
			[
				['type' => 'label', 'label' => 'Editing Sub-Category ID: ' . $subCategory->getId()],
				['type' => 'input', 'label' => 'Sub-Category Name', 'placeholder' => 'e.g., Wooden Blocks', 'default' => $subCategory->getName()],
				['type' => 'input', 'label' => 'Description (optional)', 'placeholder' => 'Various types of wooden blocks', 'default' => $subCategory->getDescription()],
				['type' => 'input', 'label' => 'Priority (0 for default)', 'placeholder' => '0', 'default' => (string) $subCategory->getPriority()],
				['type' => 'input', 'label' => 'Image Source (optional)', 'placeholder' => 'URL or path to image', 'default' => $subCategory->getImageSource()],
				['type' => 'dropdown', 'label' => 'Image Type (optional)', 'dropdownOptions' => ImageType::values(), 'default' => array_search($subCategory->getImageType()->value, ImageType::values(), true)],
				['type' => 'toggle', 'label' => 'Hidden (hide from players)', 'default' => $subCategory->isHidden()],
				['type' => 'input', 'label' => 'Permission (optional)', 'placeholder' => 'simpleshop.subcategory.wood_blocks', 'default' => $subCategory->getPermission()],
			],
			function (CustomFormResponse $response) use ($player, $subCategory) : void {
				[$name, $description, $priority, $imageSrc, $imageTypeIndex, $hidden, $permission] = $response->getValues();

				$id = $subCategory->getId();
				$parentCategory = $subCategory->getParent();

				$name = trim((string) $name);
				if ($name === '') {
					$player->sendMessage(TextFormat::RED . 'Sub-Category Name cannot be empty.');
					self::sendEditSubCategoryForm($player, $subCategory);
					return;
				}

				$description = trim((string) $description);
				$priority = (int) $priority;
				$hidden = (bool) $hidden;
				$permission = trim((string) $permission);
				if ($permission === '') {
					$permission = PermissionUtil::generateSubCategoryPermission($subCategory->getId(), $id);
				}

				$imageSrc = trim((string) $imageSrc);
				$imageType = ImageType::cases()[$imageTypeIndex] ?? ImageType::PATH;

				$updatedSubCategory = new ShopSubCategory($parentCategory, $id, $name, $description, $priority, $imageSrc, $imageType, $permission, $hidden);
				$parentCategory->addSubCategory($updatedSubCategory);
				$player->sendMessage(TextFormat::GREEN . 'Sub-Category "' . $name . '" updated successfully.');
				self::sendSubCategoryManagementForm($player, $updatedSubCategory);
			}
		);
	}

	/**
	 * Sends a confirmation form to remove a category.
	 */
	private static function sendRemoveCategoryConfirmation(Player $player, ShopCategory $category) : void {
		FormHelper::displayModalForm(
			$player,
			TextFormat::RED . 'Confirm Removal',
			'Are you sure you want to remove category "' . $category->getName() . '"? This action cannot be undone.',
			'§cConfirm',
			'§aCancel',
			function (ModalFormResponse $response) use ($player, $category) : void {
				if ($response->getChoice()) {
					SimpleShop::getInstance()->getShopManager()->removeCategory($category->getId());
					$player->sendMessage(TextFormat::GREEN . 'Category "' . $category->getName() . '" removed.');
					self::sendAdminMainMenu($player);
				} else {
					$player->sendMessage(TextFormat::YELLOW . 'Category removal cancelled.');
					self::sendCategoryManagementForm($player, $category);
				}
			}
		);
	}

	/**
	 * Sends a confirmation form to remove a sub-category.
	 */
	private static function sendRemoveSubCategoryConfirmation(Player $player, ShopSubCategory $subCategory) : void {
		FormHelper::displayModalForm(
			$player,
			TextFormat::RED . 'Confirm Removal',
			'Are you sure you want to remove sub-category "' . $subCategory->getName() . '"? This action cannot be undone.',
			'§cConfirm',
			'§aCancel',
			function (ModalFormResponse $response) use ($player, $subCategory) : void {
				if ($response->getChoice()) {
					$parentCategory = $subCategory->getParent();
					$parentCategory->removeSubCategory($subCategory->getId());
					$player->sendMessage(TextFormat::GREEN . 'Sub-Category "' . $subCategory->getName() . '" removed.');
					self::sendCategoryManagementForm($player, $parentCategory);
				} else {
					$player->sendMessage(TextFormat::YELLOW . 'Sub-Category removal cancelled.');
					self::sendSubCategoryManagementForm($player, $subCategory);
				}
			}
		);
	}

	/**
	 * Sends a confirmation form to remove an item.
	 */
	private static function sendRemoveItemConfirmation(Player $player, ShopItem $item, AbstractCategory $parentCategory) : void {
		FormHelper::displayModalForm(
			$player,
			TextFormat::RED . 'Confirm Removal',
			'Are you sure you want to remove item "' . $item->getItem()->getName() . '"? This action cannot be undone.',
			'§cConfirm',
			'§aCancel',
			function (ModalFormResponse $response) use ($player, $item, $parentCategory) : void {
				if ($response->getChoice()) {
					$parentCategory->removeItem($item->getId());
					$player->sendMessage(TextFormat::GREEN . 'Item "' . $item->getItem()->getName() . '" removed.');

					if ($parentCategory instanceof ShopCategory) {
						self::sendCategoryManagementForm($player, $parentCategory);
					} elseif ($parentCategory instanceof ShopSubCategory) {
						self::sendSubCategoryManagementForm($player, $parentCategory);
					}
				} else {
					$player->sendMessage(TextFormat::YELLOW . 'Item removal cancelled.');
					self::sendItemManagementForm($player, $item, $parentCategory);
				}
			}
		);
	}

	/**
	 * Sends a form to choose how to edit the item.
	 */
	public static function sendEditItemOptions(Player $player, ShopItem $shopItem, AbstractCategory $parentCategory) : void {
		$player->sendForm(
			SimpleForm::create(
				TextFormat::DARK_AQUA . 'Edit Item: ' . $shopItem->getItem()->getName(),
				'Choose an editing method for this item.'
			)->mergeElements(
				[
					Button::create('Update Item Data with Held Item')->onClick(function (Player $player) use ($shopItem, $parentCategory) : void {
						self::sendEditItemForm($player, $shopItem, true, $parentCategory);
					}),
					Button::create('Edit Properties Manually')->onClick(function (Player $player) use ($shopItem, $parentCategory) : void {
						self::sendEditItemForm($player, $shopItem, false, $parentCategory);
					}),
				]
			)
		);
	}

	/**
	 * Sends a form to edit an existing shop item.
	 */
	public static function sendEditItemForm(Player $player, ShopItem $shopItem, bool $updateNbt, AbstractCategory $parentCategory) : void {
		$formElements = [
			['type' => 'label', 'label' => 'Editing Item ID: ' . $shopItem->getId()],
		];

		if ($updateNbt) {
			$heldItem = $player->getInventory()->getItemInHand();
			if ($heldItem->isNull()) {
				$player->sendMessage(TextFormat::RED . 'You must be holding an item to use this option.');
				return;
			}

			$formElements[] = ['type' => 'label', 'label' => 'Updating with held item: ' . $heldItem->getName()];
			$formElements[] = ['type' => 'label', 'label' => 'NBT data will be taken from this item.'];
		} else {
			$formElements[] = ['type' => 'label', 'label' => 'Editing properties only. NBT data will not change.'];
		}

		$formElements[] = ['type' => 'input', 'label' => 'Buy Price', 'placeholder' => '0.0', 'default' => (string) $shopItem->getBuyPrice()];
		$formElements[] = ['type' => 'toggle', 'label' => 'Can Buy', 'default' => $shopItem->canBuy()];
		$formElements[] = ['type' => 'input', 'label' => 'Sell Price', 'placeholder' => '0.0', 'default' => (string) $shopItem->getSellPrice()];
		$formElements[] = ['type' => 'toggle', 'label' => 'Can Sell', 'default' => $shopItem->canSell()];
		$formElements[] = ['type' => 'input', 'label' => 'Image Source (optional)', 'placeholder' => 'URL or path to image', 'default' => $shopItem->getImageSource()];
		$formElements[] = ['type' => 'dropdown', 'label' => 'Image Type (optional)', 'dropdownOptions' => ImageType::values(), 'default' => array_search($shopItem->getImageType()->value, ImageType::values(), true)];

		FormHelper::displayCustomForm(
			$player,
			TextFormat::DARK_AQUA . 'Edit Item: ' . $shopItem->getItem()->getName(),
			$formElements,
			function (CustomFormResponse $response) use ($player, $shopItem, $updateNbt, $parentCategory) : void {
				[$buyPrice, $canBuy, $sellPrice, $canSell, $imageSrc, $imageTypeIndex] = $response->getValues();
				$nbt = '';

				if ($updateNbt) {
					$heldItem = $player->getInventory()->getItemInHand();
					if ($heldItem->isNull()) {
						$player->sendMessage(TextFormat::RED . 'You must be holding an item to update its data.');
						return;
					}

					$nbt = ItemSerializer::serializeItem($heldItem);
				} else {
					$nbt = ItemSerializer::serializeItem($shopItem->getItem());
				}

				$buyPrice = (float) $buyPrice;
				$sellPrice = (float) $sellPrice;
				$canBuy = (bool) $canBuy;
				$canSell = (bool) $canSell;
				$imageSrc = trim((string) $imageSrc);
				$imageType = ImageType::cases()[$imageTypeIndex] ?? ImageType::PATH;

				$itemData = [
					'nbt' => $nbt,
					'buy' => $buyPrice,
					'sell' => $sellPrice,
					'can_buy' => $canBuy,
					'can_sell' => $canSell,
					'image_source' => $imageSrc,
					'image_type' => $imageType->value,
				];

				try {
					$updatedShopItem = ShopItem::fromArray($shopItem->getId(), $itemData);
				} catch (\RuntimeException $e) {
					$player->sendMessage(TextFormat::RED . 'Error updating item: ' . $e->getMessage());
					return;
				}

				$parentCategory->removeItem($shopItem->getId());
				$parentCategory->addItem($updatedShopItem);
				$player->sendMessage(TextFormat::GREEN . 'Item "' . $updatedShopItem->getItem()->getName() . '" updated successfully.');

				if ($parentCategory instanceof ShopCategory) {
					self::sendCategoryManagementForm($player, $parentCategory);
				} elseif ($parentCategory instanceof ShopSubCategory) {
					self::sendSubCategoryManagementForm($player, $parentCategory);
				}
			}
		);
	}
}
