<?php
/*
 * This file is part of the FreshDoctrineEnumBundle.
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * AbstractParentType.
 *
 * @author Arturs Vonda <github@artursvonda.lv>
 *
 * @template T of int|string
 *
 * @extends AbstractEnumType<T>
 */
abstract class AbstractParentType extends AbstractEnumType
{
}
