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

use aiptu\simpleshop\shops\ShopCategory;
use aiptu\simpleshop\shops\ShopItem;
use aiptu\simpleshop\shops\ShopSubCategory;
use aiptu\simpleshop\SimpleShop;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use XanderID\PocketForm\custom\CustomFormResponse;
use XanderID\PocketForm\modal\ModalFormResponse;
use XanderID\PocketForm\PocketFormHelper;
use XanderID\PocketForm\simple\element\Button;
use XanderID\PocketForm\simple\element\ButtonImage;
use XanderID\PocketForm\simple\SimpleForm;
use XanderID\PocketForm\simple\SimpleFormResponse;
use function array_merge;
use function array_slice;
use function count;
use function floor;

/**
 * @no-named-arguments
 */
class PlayerForm {
	private const ITEMS_PER_PAGE = 10;

	/**
	 * Sends the main shop menu to the player, listing visible categories.
	 */
	public static function sendMainMenu(Player $player) : void {
		$categories = SimpleShop::getInstance()->getShopManager()->getSortedCategories($player);

		if (count($categories) === 0) {
			$player->sendMessage(TextFormat::RED . 'There are no visible shop categories at the moment.');
			return;
		}

		$buttons = [];
		foreach ($categories as $category) {
			$buttonText = TextFormat::BOLD . $category->getName() . TextFormat::RESET . "\n" . TextFormat::GRAY . $category->getDescription();
			$image = ButtonImage::create($category->getImageType()->toInt(), $category->getImageSource());
			$buttons[] = Button::create($buttonText, $image);
		}

		$player->sendForm(PocketFormHelper::menu(
			TextFormat::GREEN . 'SimpleShop',
			'Select a category to browse its contents.',
			$buttons,
			function (SimpleFormResponse $response) use ($categories) : void {
				$player = $response->getPlayer();
				$selectedId = $response->getSelected()->getId();

				if (isset($categories[$selectedId])) {
					self::sendCategoryView($player, $categories[$selectedId]);
				}
			}
		));
	}

	/**
	 * Sends a form to view the contents of a category.
	 * It lists both sub-categories and items.
	 */
	public static function sendCategoryView(Player $player, ShopCategory $category, int $page = 0) : void {
		$subCategories = $category->getSubCategories();
		$items = $category->getItems();

		$allElements = array_merge($subCategories, $items);
		$totalElements = count($allElements);
		$totalPages = (int) floor(($totalElements - 1) / self::ITEMS_PER_PAGE);

		$offset = $page * self::ITEMS_PER_PAGE;
		$visibleElements = array_slice($allElements, $offset, self::ITEMS_PER_PAGE);

		$buttons = [];
		foreach ($visibleElements as $element) {
			$buttonText = '';
			$image = null;

			if ($element instanceof ShopSubCategory) {
				$buttonText = TextFormat::BOLD . $element->getName() . TextFormat::RESET . "\n" . TextFormat::GRAY . $element->getDescription();
				$image = ButtonImage::create($element->getImageType()->toInt(), $element->getImageSource());
			} elseif ($element instanceof ShopItem) {
				$buyPrice = $element->canBuy() ? TextFormat::GREEN . '$' . $element->getBuyPrice() : TextFormat::DARK_GRAY . 'N/A';
				$sellPrice = $element->canSell() ? TextFormat::RED . '$' . $element->getSellPrice() : TextFormat::DARK_GRAY . 'N/A';
				$buttonText = TextFormat::BOLD . $element->getItem()->getName() . TextFormat::RESET . "\n" . TextFormat::GRAY . 'Buy: ' . $buyPrice . ' | Sell: ' . $sellPrice;
				$image = ButtonImage::create($element->getImageType()->toInt(), $element->getImageSource());
			}

			$buttons[] = Button::create($buttonText, $image);
		}

		$navButtons = [];
		if ($page > 0) {
			$navButtons[] = Button::create('§l§b« Previous Page', ButtonImage::create(0, 'textures/ui/arrowLeft'))->onClick(function (Player $player) use ($category, $page) : void {
				self::sendCategoryView($player, $category, $page - 1);
			});
		}

		if ($page < $totalPages) {
			$navButtons[] = Button::create('§l§bNext Page »', ButtonImage::create(0, 'textures/ui/arrowRight'))->onClick(function (Player $player) use ($category, $page) : void {
				self::sendCategoryView($player, $category, $page + 1);
			});
		}

		$allButtons = array_merge($navButtons, $buttons);

		$player->sendForm(PocketFormHelper::menu(
			TextFormat::GREEN . 'Category: ' . $category->getName() . ' (Page ' . ($page + 1) . '/' . ($totalPages + 1) . ')',
			'Select a sub-category or an item.',
			$allButtons,
			function (SimpleFormResponse $response) use ($allElements, $page, $navButtons) : void {
				$player = $response->getPlayer();
				$selectedId = (int) $response->getSelected()->getId();
				$navButtonCount = count($navButtons);
				$elementIndex = $selectedId - $navButtonCount + ($page * self::ITEMS_PER_PAGE);

				if (isset($allElements[$elementIndex])) {
					$element = $allElements[$elementIndex];
					if ($element instanceof ShopSubCategory) {
						self::sendSubCategoryView($player, $element);
					} elseif ($element instanceof ShopItem) {
						self::sendItemDetailsForm($player, $element);
					}
				}
			}
		));
	}

	/**
	 * Sends a form to view the contents of a sub-category.
	 */
	public static function sendSubCategoryView(Player $player, ShopSubCategory $subCategory, int $page = 0) : void {
		$items = $subCategory->getItems();
		$totalItems = count($items);
		$totalPages = (int) floor(($totalItems - 1) / self::ITEMS_PER_PAGE);

		$offset = $page * self::ITEMS_PER_PAGE;
		$visibleItems = array_slice($items, $offset, self::ITEMS_PER_PAGE);

		$buttons = [];
		foreach ($visibleItems as $item) {
			$buyPrice = $item->canBuy() ? TextFormat::GREEN . '$' . $item->getBuyPrice() : TextFormat::DARK_GRAY . 'N/A';
			$sellPrice = $item->canSell() ? TextFormat::RED . '$' . $item->getSellPrice() : TextFormat::DARK_GRAY . 'N/A';
			$buttonText = TextFormat::BOLD . $item->getItem()->getName() . TextFormat::RESET . "\n" . TextFormat::GRAY . 'Buy: ' . $buyPrice . ' | Sell: ' . $sellPrice;
			$image = ButtonImage::create($item->getImageType()->toInt(), $item->getImageSource());
			$buttons[] = Button::create($buttonText, $image);
		}

		$navButtons = [];
		if ($page > 0) {
			$navButtons[] = Button::create('§l§b« Previous Page', ButtonImage::create(0, 'textures/ui/arrowLeft'))->onClick(function (Player $player) use ($subCategory, $page) : void {
				self::sendSubCategoryView($player, $subCategory, $page - 1);
			});
		}

		if ($page < $totalPages) {
			$navButtons[] = Button::create('§l§bNext Page »', ButtonImage::create(0, 'textures/ui/arrowRight'))->onClick(function (Player $player) use ($subCategory, $page) : void {
				self::sendSubCategoryView($player, $subCategory, $page + 1);
			});
		}

		$allButtons = array_merge($navButtons, $buttons);

		$player->sendForm(PocketFormHelper::menu(
			TextFormat::GREEN . 'Sub-Category: ' . $subCategory->getName() . ' (Page ' . ($page + 1) . '/' . ($totalPages + 1) . ')',
			'Select an item to buy or sell.',
			$allButtons,
			function (SimpleFormResponse $response) use ($items, $page, $navButtons) : void {
				$player = $response->getPlayer();
				$selectedId = (int) $response->getSelected()->getId();
				$navButtonCount = count($navButtons);
				$itemIndex = $selectedId - $navButtonCount + ($page * self::ITEMS_PER_PAGE);

				if (isset($items[$itemIndex])) {
					self::sendItemDetailsForm($player, $items[$itemIndex]);
				}
			}
		));
	}

	/**
	 * Sends a form with detailed information and buy/sell buttons.
	 */
	public static function sendItemDetailsForm(Player $player, ShopItem $shopItem) : void {
		$item = $shopItem->getItem();

		$buyPrice = $shopItem->canBuy() ? TextFormat::GREEN . '$' . $shopItem->getBuyPrice() : TextFormat::DARK_GRAY . 'N/A';
		$sellPrice = $shopItem->canSell() ? TextFormat::RED . '$' . $shopItem->getSellPrice() : TextFormat::DARK_GRAY . 'N/A';
		$title = TextFormat::GREEN . 'Shop: ' . $item->getName();

		$content = 'Buy Price: ' . $buyPrice . "\nSell Price: " . $sellPrice;
		if ($item->hasEnchantments()) {
			$content .= "\n" . TextFormat::GOLD . "Enchantments:\n" . TextFormat::RESET;
			foreach ($item->getEnchantments() as $enchantmentInstance) {
				$enchantmentName = $enchantmentInstance->getType()->getName();
				$content .= ' - ' . ($enchantmentName instanceof Translatable ? $player->getLanguage()->translate($enchantmentName) : $enchantmentName) . ' ' . $enchantmentInstance->getLevel() . "\n";
			}
		}

		$buttons = [];
		if ($shopItem->canBuy()) {
			$buttons[] = Button::create('§aBuy ' . $item->getName())->onClick(function (Player $player) use ($shopItem) : void {
				self::sendBuyQuantityForm($player, $shopItem);
			});
		}

		if ($shopItem->canSell()) {
			$buttons[] = Button::create('§cSell ' . $item->getName())->onClick(function (Player $player) use ($shopItem) : void {
				self::sendSellQuantityForm($player, $shopItem);
			});
		}

		$player->sendForm(
			SimpleForm::create(
				$title,
				$content,
			)->mergeElements($buttons)
		);
	}

	/**
	 * Sends a custom form to get the quantity for a buy transaction.
	 */
	public static function sendBuyQuantityForm(Player $player, ShopItem $shopItem) : void {
		FormHelper::displayCustomForm(
			$player,
			TextFormat::GREEN . 'Buy ' . $shopItem->getItem()->getName(),
			[
				['type' => 'label', 'label' => 'Select the quantity you want to buy.'],
				['type' => 'slider', 'label' => 'Quantity', 'sliderMin' => 1, 'sliderMax' => 64, 'sliderStep' => 1, 'default' => 1],
			],
			function (CustomFormResponse $response) use ($shopItem) : void {
				$player = $response->getPlayer();

				$quantity = (int) $response->getValues()[0];

				self::sendBuyConfirmationForm($player, $shopItem, $quantity);
			}
		);
	}

	/**
	 * Sends a custom form to get the quantity for a sell transaction.
	 */
	public static function sendSellQuantityForm(Player $player, ShopItem $shopItem) : void {
		FormHelper::displayCustomForm(
			$player,
			TextFormat::GREEN . 'Sell ' . $shopItem->getItem()->getName(),
			[
				['type' => 'label', 'label' => 'Select the quantity you want to sell.'],
				['type' => 'slider', 'label' => 'Quantity', 'sliderMin' => 1, 'sliderMax' => 64, 'sliderStep' => 1, 'default' => 1],
			],
			function (CustomFormResponse $response) use ($shopItem) : void {
				$player = $response->getPlayer();

				$quantity = (int) $response->getValues()[0];

				self::sendSellConfirmationForm($player, $shopItem, $quantity);
			}
		);
	}

	/**
	 * Sends a confirmation form for a buy transaction.
	 */
	public static function sendBuyConfirmationForm(Player $player, ShopItem $shopItem, int $quantity) : void {
		if (!$shopItem->canBuy()) {
			$player->sendMessage(TextFormat::RED . 'This item cannot be bought.');
			return;
		}

		// TODO: ECONOMY SUPPORT

		$buyPrice = $shopItem->getBuyPrice();
		$totalPrice = $buyPrice * $quantity;

		$title = TextFormat::GREEN . 'Confirm Purchase';
		$content = TextFormat::WHITE . 'Item: ' . TextFormat::YELLOW . $shopItem->getItem()->getName() . TextFormat::WHITE . "\n";
		$content .= 'Quantity: ' . TextFormat::YELLOW . $quantity . TextFormat::WHITE . "\n";
		$content .= 'Price per item: ' . TextFormat::GREEN . '$' . $buyPrice . TextFormat::WHITE . "\n";
		$content .= 'Subtotal: ' . TextFormat::GREEN . '$' . $totalPrice . TextFormat::WHITE . "\n";

		FormHelper::displayModalForm(
			$player,
			$title,
			$content,
			'§aConfirm',
			'§cCancel',
			function (ModalFormResponse $response) use ($shopItem, $quantity, $totalPrice) : void {
				$player = $response->getPlayer();
				$isConfirmed = $response->getChoice();

				if ($isConfirmed) {
					self::handleBuyTransaction($player, $shopItem, $quantity, $totalPrice);
				}
			}
		);
	}

	/**
	 * Handles the buy transaction logic.
	 */
	private static function handleBuyTransaction(Player $player, ShopItem $shopItem, int $quantity, float $totalPrice) : void {
		$item = $shopItem->getItem()->setCount($quantity);

		if (!$player->getInventory()->canAddItem($item)) {
			$player->sendMessage(TextFormat::RED . 'Your inventory is full. Please make space.');
			return;
		}

		$player->getInventory()->addItem($item);
		// TODO: ECONOMY SUPPORT
		$player->sendMessage(TextFormat::GREEN . 'You successfully bought ' . $quantity . 'x ' . $shopItem->getItem()->getName() . ' for $' . $totalPrice . '.');
	}

	/**
	 * Sends a confirmation form for a sell transaction.
	 */
	public static function sendSellConfirmationForm(Player $player, ShopItem $shopItem, int $quantity) : void {
		if (!$shopItem->canSell()) {
			$player->sendMessage(TextFormat::RED . 'This item cannot be sold.');
			return;
		}

		// TODO: ECONOMY SUPPORT

		$sellPrice = $shopItem->getSellPrice();
		$totalPrice = $sellPrice * $quantity;

		$title = TextFormat::GREEN . 'Confirm Sale';
		$content = TextFormat::WHITE . 'Item: ' . TextFormat::YELLOW . $shopItem->getItem()->getName() . TextFormat::WHITE . "\n";
		$content .= 'Quantity: ' . TextFormat::YELLOW . $quantity . TextFormat::WHITE . "\n";
		$content .= 'Price per item: ' . TextFormat::RED . '$' . $sellPrice . TextFormat::WHITE . "\n";
		$content .= 'Subtotal: ' . TextFormat::RED . '$' . $totalPrice . TextFormat::WHITE . "\n";

		FormHelper::displayModalForm(
			$player,
			$title,
			$content,
			'§aConfirm',
			'§cCancel',
			function (ModalFormResponse $response) use ($shopItem, $quantity, $totalPrice) : void {
				$player = $response->getPlayer();
				$isConfirmed = $response->getChoice();

				if ($isConfirmed) {
					self::handleSellTransaction($player, $shopItem, $quantity, $totalPrice);
				}
			}
		);
	}

	/**
	 * Handles the sell transaction logic.
	 */
	private static function handleSellTransaction(Player $player, ShopItem $shopItem, int $quantity, float $totalPrice) : void {
		$item = $shopItem->getItem()->setCount($quantity);

		if (!$player->getInventory()->contains($item)) {
			$player->sendMessage(TextFormat::RED . 'You do not have ' . $quantity . 'x ' . $shopItem->getItem()->getName() . ' to sell.');
			return;
		}

		$player->getInventory()->removeItem($item);
		// TODO: ECONOMY SUPPORT
		$player->sendMessage(TextFormat::GREEN . 'You successfully sold ' . $quantity . 'x ' . $shopItem->getItem()->getName() . ' for $' . $totalPrice . '.');
	}
}
