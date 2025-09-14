<?php

/*
 * Copyright (c) 2025-2025 XanderID
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/XanderID/PocketForm
 */

declare(strict_types=1);

namespace aiptu\simpleshop\libs\_19362d29eb9379f3\XanderID\PocketForm\element;

/**
 * Determines whether the element is read-only.
 *
 * A read-only element does not allow any user interaction or responses.
 * In this implementation, the element is always considered interactive,
 * hence this method always returns false.
 */
interface ReadonlyElement {}