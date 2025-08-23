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

namespace aiptu\simpleshop\commands;

use aiptu\simpleshop\commands\args\CategoryArgument;
use aiptu\simpleshop\forms\PlayerForm;
use aiptu\simpleshop\shops\ShopCategory;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\utils\TextFormat;

/**
 * @no-named-arguments
 */
class ShopCommand extends BaseCommand {
	/** @param list<string> $aliases */
	public function __construct(
		PluginBase $plugin,
		string $name,
		string $description = '',
		array $aliases = []
	) {
		parent::__construct($plugin, $name, $description, $aliases);
	}

	public function onRun(CommandSender $sender, string $commandLabel, array $args) : void {
		if (!$sender instanceof Player) {
			throw new AssumptionFailedError(InGameRequiredConstraint::class . ' should have prevented this');
		}

		if (isset($args['category'])) {
			if (!$args['category'] instanceof ShopCategory) {
				$sender->sendMessage(TextFormat::RED . 'Invalid shop category selected.');
				return;
			}

			$category = $args['category'];

			if ($category->isHidden()) {
				$perm = $category->getPermission();
				if (!$sender->hasPermission($perm)) {
					$sender->sendMessage(TextFormat::RED . 'You do not have permission to access this shop category.');
					return;
				}
			}

			PlayerForm::sendCategoryView($sender, $category);
			return;
		}

		PlayerForm::sendMainMenu($sender);
	}

	public function prepare() : void {
		$this->addConstraint(new InGameRequiredConstraint($this));
		$this->setPermission('simpleshop.command.shop.access');
		$this->registerArgument(0, new CategoryArgument('category', true));
	}
}
