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

namespace Fresh\DoctrineEnumBundle\DBAL\Types;

/**
 * MonthFullNameType.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 *
 * @extends AbstractEnumType<string>
 */
final class MonthFullNameType extends AbstractEnumType
{
    public const JANUARY = 'january';

    public const FEBRUARY = 'february';

    public const MARCH = 'march';

    public const APRIL = 'april';

    public const MAY = 'may';

    public const JUNE = 'june';

    public const JULY = 'july';

    public const AUGUST = 'august';

    public const SEPTEMBER = 'september';

    public const OCTOBER = 'october';

    public const NOVEMBER = 'november';

    public const DECEMBER = 'december';

    /**
     * {@inheritdoc}
     */
    protected static $choices = [
        self::JANUARY => 'January',
        self::FEBRUARY => 'February',
        self::MARCH => 'March',
        self::APRIL => 'April',
        self::MAY => 'May',
        self::JUNE => 'June',
        self::JULY => 'July',
        self::AUGUST => 'August',
        self::SEPTEMBER => 'September',
        self::OCTOBER => 'October',
        self::NOVEMBER => 'November',
        self::DECEMBER => 'December',
    ];
}
