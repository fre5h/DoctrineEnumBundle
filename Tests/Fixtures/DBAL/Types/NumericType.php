<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * NumericType.
 *
 * @author Stephan Vock <stephan.vock@gmail.com>
 */
final class NumericType extends AbstractEnumType
{
    public const ZERO = 0;
    public const ONE = 1;
    public const TWO = 2;
    public const THREE = 3;
    public const FOUR = 4;

    protected $name = 'NumericType';

    protected static $choices = [
        self::ZERO => 0,
        self::ONE => 1,
        self::TWO => 2,
        self::THREE => 3,
        self::FOUR => 4,
    ];
}
