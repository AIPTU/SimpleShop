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

namespace aiptu\simpleshop\commands\args;

use aiptu\simpleshop\shops\ShopCategory;
use aiptu\simpleshop\SimpleShop;
use aiptu\simpleshop\libs\_19362d29eb9379f3\CortexPE\Commando\args\StringEnumArgument;
use pocketmine\command\CommandSender;
use function array_map;

/**
 * @no-named-arguments
 */
class CategoryArgument extends StringEnumArgument {
	public function getEnumValues() : array {
		return array_map(fn (ShopCategory $category) : string => $category->getName(), SimpleShop::getInstance()->getShopManager()->getCategories());
	}

	public function getTypeName() : string {
		return 'category';
	}

	public function getEnumName() : string {
		return 'category';
	}

	public function parse(string $argument, CommandSender $sender) : mixed {
		return SimpleShop::getInstance()->getShopManager()->getCategory($argument);
	}
}