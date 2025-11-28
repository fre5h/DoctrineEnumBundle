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
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
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
 *
 * @template TValue of int|string
 * @template TReadable of int|string
 */
abstract class AbstractEnumType extends Type
{
    protected string $name = '';

    /**
     * @var array<TValue, TReadable> Array of ENUM Values, where ENUM values are keys and their readable versions are values
     *
     * @static
     */
    protected static array $choices = [];

    /**
     * @param TValue           $value
     * @param AbstractPlatform $platform
     *
     * @return int|string
     *
     * @throws InvalidArgumentException
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if (null !== $value && !isset(static::$choices[$value])) {
            throw new InvalidArgumentException(\sprintf('Invalid value "%s" for ENUM "%s".', $value, $this->getName()));
        }

        return $value;
    }

    /**
     * @param TValue           $value
     * @param AbstractPlatform $platform
     *
     * @return mixed
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if (!isset(static::$choices[$value])) {
            return $value;
        }

        // Check whether choice list is using integers as values
        $choice = static::$choices[$value];
        $choices = array_flip(static::$choices);
        if (\is_int($choices[$choice])) {
            return (int) $value;
        }

        return $value;
    }

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array<string, string> $column   The column definition
     * @param AbstractPlatform      $platform The currently used database platform
     *
     * @return string
     */
    public function getSqlDeclaration(array $column, AbstractPlatform $platform): string
    {
        $values = implode(
            ', ',
            array_map(
                /** @var TValue $value */
                static function (int|string $value) {
                    return "'{$value}'";
                },
                static::getValues()
            )
        );

        $sqlDeclaration = match (true) {
            $platform instanceof SQLitePlatform => \sprintf('TEXT CHECK(%s IN (%s))', $column['name'], $values),
            $platform instanceof PostgreSQLPlatform, $platform instanceof SQLServerPlatform => \sprintf(
                'VARCHAR(255) CHECK(%s IN (%s))',
                $column['name'],
                $values
            ),
            default => \sprintf('ENUM(%s)', $values),
        };

        $defaultValue = static::getDefaultValue();
        if (null !== $defaultValue) {
            $sqlDeclaration .= \sprintf(' DEFAULT %s', $platform->quoteStringLiteral((string) $defaultValue));
        }

        return $sqlDeclaration;
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
        return $this->name ?: (string) array_search(static::class, self::getTypesMap(), true);
    }

    /**
     * Get readable choices for the ENUM field.
     *
     * @static
     *
     * @return array<TReadable, TValue>
     */
    public static function getChoices(): array
    {
        return array_flip(static::$choices);
    }

    /**
     * Get values for the ENUM field.
     *
     * @static
     *
     * @return array<int, TValue> Values for the ENUM field
     */
    public static function getValues(): array
    {
        return array_keys(static::$choices);
    }

    /**
     * Get random value for the ENUM field.
     *
     * @static
     *
     * @return TValue
     *
     * @throws InvalidArgumentException
     */
    public static function getRandomValue()
    {
        $values = self::getValues();

        $count = \count($values);
        if (0 === $count) {
            throw new InvalidArgumentException('There is no value in Enum type');
        }

        return $values[random_int(0, $count - 1)];
    }

    /**
     * Get array of ENUM Values, where ENUM values are keys and their readable versions are values.
     *
     * @static
     *
     * @return array<TValue, TReadable> Array of values in readable format
     */
    public static function getReadableValues(): array
    {
        return static::$choices;
    }

    /**
     * Asserts that given choice exists in the array of ENUM values.
     *
     * @param int|string $value ENUM value
     *
     * @throws InvalidArgumentException
     */
    public static function assertValidChoice(int|string $value): void
    {
        if (!isset(static::$choices[$value])) {
            throw new InvalidArgumentException(\sprintf('Invalid value "%s" for ENUM type "%s".', (string) $value, static::class));
        }
    }

    /**
     * Get value in readable format.
     *
     * @param int|string $value ENUM value
     *
     * @static
     *
     * @return TReadable Value in readable format
     */
    public static function getReadableValue(int|string $value)
    {
        static::assertValidChoice($value);

        return static::$choices[$value];
    }

    /**
     * Check if some value exists in the array of ENUM values.
     *
     * @param int|string $value ENUM value
     *
     * @static
     *
     * @return bool
     */
    public static function isValueExist(int|string $value): bool
    {
        return isset(static::$choices[$value]);
    }

    /**
     * Get default value for DDL statement.
     *
     * @static
     *
     * @return TValue|null Default value for DDL statement
     */
    public static function getDefaultValue()
    {
        return null;
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
        if ($platform instanceof MySQLPlatform) {
            return array_merge(parent::getMappedDatabaseTypes($platform), ['enum']);
        }

        return parent::getMappedDatabaseTypes($platform);
    }
}
