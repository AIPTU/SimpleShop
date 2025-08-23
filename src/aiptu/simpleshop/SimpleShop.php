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

namespace aiptu\simpleshop;

use aiptu\simpleshop\commands\ShopAdminCommand;
use aiptu\simpleshop\commands\ShopCommand;
use aiptu\simpleshop\shops\ShopManager;
use pocketmine\plugin\DisablePluginException;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use Symfony\Component\Filesystem\Path;
use Throwable;

/**
 * @no-named-arguments
 */
class SimpleShop extends PluginBase {
	use SingletonTrait;

	private ShopManager $shopManager;

	protected function onEnable() : void {
		self::setInstance($this);

		try {
			$this->shopManager = new ShopManager(Path::join($this->getDataFolder(), 'shops.json'));
		} catch (Throwable $e) {
			$this->getLogger()->error('An error occurred while loading the shop data: ' . $e->getMessage());
			throw new DisablePluginException();
		}

		$this->getServer()->getCommandMap()->registerAll(
			$this->getName(),
			[
				new ShopAdminCommand($this, 'shopadmin', 'Shop admin commands.'),
				new ShopCommand($this, 'shop', 'Shop commands.'),
			]
		);
	}

	public function getShopManager() : ShopManager {
		return $this->shopManager;
	}

	public function saveAll() : void {
		$this->shopManager->save();
	}
}
