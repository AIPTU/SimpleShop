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
use function number_format;

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
		$elements = array_merge($category->getSubCategories(), $category->getItems());
		self::sendPaginatedForm($player, $elements, $category->getName(), 'sub-category or an item.', $page, function (Player $player, $element) : void {
			if ($element instanceof ShopSubCategory) {
				self::sendSubCategoryView($player, $element);
			} elseif ($element instanceof ShopItem) {
				self::sendItemDetailsForm($player, $element);
			}
		});
	}

	/**
	 * Sends a form to view the contents of a sub-category.
	 */
	public static function sendSubCategoryView(Player $player, ShopSubCategory $subCategory, int $page = 0) : void {
		$items = $subCategory->getItems();
		self::sendPaginatedForm($player, $items, $subCategory->getName(), 'item to buy or sell.', $page, function (Player $player, ShopItem $item) : void {
			self::sendItemDetailsForm($player, $item);
		});
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

		$player->sendForm(SimpleForm::create($title, $content)->mergeElements($buttons));
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
		$economyProvider = SimpleShop::getInstance()->getEconomyProvider();

		$economyProvider->getMoney($player, function (float $currentMoney) use ($player, $shopItem, $quantity, $totalPrice, $item, $economyProvider) : void {
			if ($currentMoney < $totalPrice) {
				$difference = $totalPrice - $currentMoney;
				$player->sendMessage(TextFormat::RED . 'You don\'t have enough money to buy this item. You need $' . number_format($totalPrice) . ' but you only have $' . number_format($currentMoney) . '. You are short $' . number_format($difference) . '.');
				return;
			}

			if (!$player->getInventory()->canAddItem($item)) {
				$player->sendMessage(TextFormat::RED . 'Your inventory is full! You need at least one empty slot to buy this item.');
				return;
			}

			$economyProvider->takeMoney($player, $totalPrice, function (bool $success) use ($item, $quantity, $player, $totalPrice, $shopItem, $economyProvider) : void {
				if ($success) {
					$player->getInventory()->addItem($item);
					$player->sendMessage(TextFormat::GREEN . 'Purchase successful! You have bought ' . $quantity . 'x ' . $shopItem->getItem()->getName() . ' for ' . $economyProvider->getMonetaryUnit() . number_format($totalPrice));
				} else {
					$player->sendMessage(TextFormat::RED . 'An unexpected error occurred during the transaction. Your money has not been deducted.');
				}
			});
		});
	}

	/**
	 * Sends a confirmation form for a sell transaction.
	 */
	public static function sendSellConfirmationForm(Player $player, ShopItem $shopItem, int $quantity) : void {
		if (!$shopItem->canSell()) {
			$player->sendMessage(TextFormat::RED . 'This item cannot be sold.');
			return;
		}

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
		$inventory = $player->getInventory();

		$heldItems = $inventory->all($shopItem->getItem());
		$heldCount = 0;
		foreach ($heldItems as $heldItem) {
			$heldCount += $heldItem->getCount();
		}

		if ($heldCount < $quantity) {
			$player->sendMessage(TextFormat::RED . 'You don\'t have enough of this item to sell. You need ' . $quantity . 'x ' . $shopItem->getItem()->getName() . ' but you only have ' . $heldCount . '.');
			return;
		}

		$economyProvider = SimpleShop::getInstance()->getEconomyProvider();

		$inventory->removeItem($item);

		$economyProvider->giveMoney($player, $totalPrice, function (bool $success) use ($item, $quantity, $player, $totalPrice, $shopItem, $economyProvider) : void {
			if ($success) {
				$player->sendMessage(TextFormat::GREEN . 'Sale successful! You have sold ' . $quantity . 'x ' . $shopItem->getItem()->getName() . ' for ' . $economyProvider->getMonetaryUnit() . number_format($totalPrice));
			} else {
				$player->getInventory()->addItem($item);
				$player->sendMessage(TextFormat::RED . 'An unexpected error occurred during the transaction. Your items have been returned.');
			}
		});
	}

	/**
	 * Creates a button for a shop element (category, sub-category, or item).
	 *
	 * @param mixed $element
	 */
	private static function createShopElementButton($element) : Button {
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

		return Button::create($buttonText, $image);
	}

	/**
	 * A reusable method to handle paginated forms.
	 */
	private static function sendPaginatedForm(Player $player, array $elements, string $menuName, string $prompt, int $page, callable $callback) : void {
		$totalElements = count($elements);
		$totalPages = (int) floor(($totalElements - 1) / self::ITEMS_PER_PAGE);

		$offset = $page * self::ITEMS_PER_PAGE;
		$visibleElements = array_slice($elements, $offset, self::ITEMS_PER_PAGE);

		$buttons = [];
		foreach ($visibleElements as $element) {
			$buttons[] = self::createShopElementButton($element);
		}

		$navButtons = [];
		if ($page > 0) {
			$navButtons[] = Button::create('§l§b« Previous Page', ButtonImage::create(0, 'textures/ui/arrowLeft'))->onClick(function (Player $player) use ($elements, $menuName, $prompt, $page, $callback) : void {
				self::sendPaginatedForm($player, $elements, $menuName, $prompt, $page - 1, $callback);
			});
		}

		if ($page < $totalPages) {
			$navButtons[] = Button::create('§l§bNext Page »', ButtonImage::create(0, 'textures/ui/arrowRight'))->onClick(function (Player $player) use ($elements, $menuName, $prompt, $page, $callback) : void {
				self::sendPaginatedForm($player, $elements, $menuName, $prompt, $page + 1, $callback);
			});
		}

		$allButtons = array_merge($navButtons, $buttons);

		$player->sendForm(PocketFormHelper::menu(
			TextFormat::GREEN . ($menuName === '' ? 'Shop' : 'Category: ' . $menuName) . ' (Page ' . ($page + 1) . '/' . ($totalPages + 1) . ')',
			'Select a ' . $prompt,
			$allButtons,
			function (SimpleFormResponse $response) use ($elements, $page, $navButtons, $callback) : void {
				$player = $response->getPlayer();
				$selectedId = (int) $response->getSelected()->getId();
				$navButtonCount = count($navButtons);
				$elementIndex = $selectedId - $navButtonCount + ($page * self::ITEMS_PER_PAGE);

				if (isset($elements[$elementIndex])) {
					$callback($player, $elements[$elementIndex]);
				}
			}
		));
	}
}
