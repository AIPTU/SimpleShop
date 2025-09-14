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
use CortexPE\Commando\PacketHooker;
use DaPigGuy\libPiggyEconomy\libPiggyEconomy;
use DaPigGuy\libPiggyEconomy\providers\EconomyProvider;
use pocketmine\plugin\DisablePluginException;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use Symfony\Component\Filesystem\Path;
use XanderID\PocketForm\PocketForm;
use Throwable;
use function class_exists;
use function is_array;

/**
 * @no-named-arguments
 */
class SimpleShop extends PluginBase {
	use SingletonTrait;

	private ShopManager $shopManager;

	private EconomyProvider $economyProvider;

	protected function onEnable() : void {
		self::setInstance($this);

		$this->validateVirions();

		if (!PacketHooker::isRegistered()) {
			PacketHooker::register($this);
		}

		try {
			$this->shopManager = new ShopManager(Path::join($this->getDataFolder(), 'shops.json'));
		} catch (Throwable $e) {
			$this->getLogger()->error('An error occurred while loading the shop data: ' . $e->getMessage());
			throw new DisablePluginException();
		}

		libPiggyEconomy::init();

		$economyConfig = $this->getConfig()->get('economy');
		if (!is_array($economyConfig) || !isset($economyConfig['provider'])) {
			$this->getLogger()->critical('Invalid or missing "economy" configuration. Please provide an array with the key "provider".');
			throw new DisablePluginException();
		}

		try {
			$this->economyProvider = libPiggyEconomy::getProvider($economyConfig);
		} catch (\Throwable $e) {
			$this->getLogger()->critical('Failed to get economy provider: ' . $e->getMessage());
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

	public function getEconomyProvider() : EconomyProvider {
		return $this->economyProvider;
	}

	public function saveAll() : void {
		$this->shopManager->save();
	}

	/**
	 * Checks if the required virions/libraries are present before enabling the plugin.
	 *
	 * @throws DisablePluginException
	 */
	private function validateVirions() : void {
		$requiredVirions = [
			'Commando' => PacketHooker::class,
			'PocketForm' => PocketForm::class,
			'libPiggyEconomy' => libPiggyEconomy::class,
		];

		foreach ($requiredVirions as $name => $class) {
			if (!class_exists($class)) {
				$this->getLogger()->error($name . ' virion was not found.');
				throw new DisablePluginException();
			}
		}
	}
}
