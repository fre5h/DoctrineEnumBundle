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
 * MonthShortNameType.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
abstract class MonthShortNameType extends AbstractEnumType
{
    public const JANUARY = 'jan';
    public const FEBRUARY = 'feb';
    public const MARCH = 'mar';
    public const APRIL = 'apr';
    public const MAY = 'may';
    public const JUNE = 'jun';
    public const JULY = 'jul';
    public const AUGUST = 'aug';
    public const SEPTEMBER = 'sep';
    public const OCTOBER = 'oct';
    public const NOVEMBER = 'nov';
    public const DECEMBER = 'dec';

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
