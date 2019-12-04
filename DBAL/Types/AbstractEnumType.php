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

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use Doctrine\DBAL\Types\Type;
use Fresh\DoctrineEnumBundle\Exception\InvalidArgumentException;

/**
 * AbstractEnumType.
 *
 * Provides support of ENUM type for Doctrine in Symfony applications.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 * @author Ben Davies <ben.davies@gmail.com>
 * @author Jaik Dean <jaik@fluoresce.co>
 */
abstract class AbstractEnumType extends Type
{
    /** @var string */
    protected $name = '';

    /**
     * @var array|mixed[] Array of ENUM Values, where ENUM values are keys and their readable versions are values
     *
     * @static
     */
    protected static $choices = [];

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        if (!isset(static::$choices[$value])) {
            throw new InvalidArgumentException(\sprintf('Invalid value "%s" for ENUM "%s".', $value, $this->getName()));
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (!isset(static::$choices[$value])) {
            return $value;
        }

        // Check whether choice list is using integers as values
        $choice = static::$choices[$value];
        $choices = \array_flip(static::$choices);
        if (\is_int($choices[$choice])) {
            return (int) $value;
        }

        return $value;
    }

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param mixed[]          $fieldDeclaration The field declaration
     * @param AbstractPlatform $platform         The currently used database platform
     *
     * @return string
     */
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        $values = \implode(
            ', ',
            \array_map(
                static function ($value) {
                    return "'{$value}'";
                },
                static::getValues()
            )
        );

        if ($platform instanceof SqlitePlatform) {
            return \sprintf('TEXT CHECK(%s IN (%s))', $fieldDeclaration['name'], $values);
        }

        if ($platform instanceof PostgreSqlPlatform || $platform instanceof SQLServerPlatform) {
            return \sprintf('VARCHAR(255) CHECK(%s IN (%s))', $fieldDeclaration['name'], $values);
        }

        return \sprintf('ENUM(%s)', $values);
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name ?: \array_search(\get_class($this), self::getTypesMap(), true);
    }

    /**
     * Get readable choices for the ENUM field.
     *
     * @static
     *
     * @return string[] Values for the ENUM field
     */
    public static function getChoices(): array
    {
        return \array_flip(static::$choices);
    }

    /**
     * Get values for the ENUM field.
     *
     * @static
     *
     * @return string[] Values for the ENUM field
     */
    public static function getValues(): array
    {
        return \array_keys(static::$choices);
    }

    /**
     * Get random value for the ENUM field.
     *
     * @static
     *
     * @return int|string
     */
    public static function getRandomValue()
    {
        $values = self::getValues();
        $randomKey = \random_int(0, \count($values) - 1);

        return $values[$randomKey];
    }

    /**
     * Get array of ENUM Values, where ENUM values are keys and their readable versions are values.
     *
     * @static
     *
     * @return string[] Array of values with readable format
     */
    public static function getReadableValues(): array
    {
        return static::$choices;
    }

    /**
     * Asserts that given choice exists in the array of ENUM values.
     *
     * @param string $value ENUM value
     *
     * @throws InvalidArgumentException
     */
    public static function assertValidChoice(string $value): void
    {
        if (!isset(static::$choices[$value])) {
            throw new InvalidArgumentException(\sprintf('Invalid value "%s" for ENUM type "%s".', $value, static::class));
        }
    }

    /**
     * Get value in readable format.
     *
     * @param string $value ENUM value
     *
     * @static
     *
     * @return string $value Value in readable format
     */
    public static function getReadableValue(string $value): string
    {
        static::assertValidChoice($value);

        return static::$choices[$value];
    }

    /**
     * Check if some string value exists in the array of ENUM values.
     *
     * @param string $value ENUM value
     *
     * @static
     *
     * @return bool
     */
    public static function isValueExist(string $value): bool
    {
        return isset(static::$choices[$value]);
    }

    /**
     * Gets an array of database types that map to this Doctrine type.
     *
     * @param AbstractPlatform $platform
     *
     * @return string[]
     */
    public function getMappedDatabaseTypes(AbstractPlatform $platform): array
    {
        if ($platform instanceof MySqlPlatform) {
            return \array_merge(parent::getMappedDatabaseTypes($platform), ['enum']);
        }

        return parent::getMappedDatabaseTypes($platform);
    }
}
