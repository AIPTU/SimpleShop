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

namespace aiptu\simpleshop\utils;

use function strtolower;

/**
 * @no-named-arguments
 */
class PermissionUtil {
	public static function generateCategoryPermission(string $id) : string {
		return 'simpleshop.category.' . strtolower($id);
	}

	public static function generateSubCategoryPermission(string $parentId, string $id) : string {
		return 'simpleshop.subcategory.' . strtolower($parentId) . '.' . strtolower($id);
	}
}
