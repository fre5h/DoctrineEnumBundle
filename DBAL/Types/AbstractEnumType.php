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
use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Platforms\PostgreSQL94Platform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Platforms\SQLServer2012Platform;
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
    /** @var string */
    protected $name = '';

    /**
     * @var array<TValue, TReadable> Array of ENUM Values, where ENUM values are keys and their readable versions are values
     *
     * @static
     */
    protected static $choices = [];

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     *
     * @return TValue|null
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
     *
     * @return TValue|null
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
                static function (string $value) {
                    return "'{$value}'";
                },
                static::getValues()
            )
        );

        switch (true) {
            case $platform instanceof SqlitePlatform:
                $sqlDeclaration = \sprintf('TEXT CHECK(%s IN (%s))', $fieldDeclaration['name'], $values);

                break;
            case $platform instanceof PostgreSQL94Platform:
            case $platform instanceof PostgreSQL100Platform:
            case $platform instanceof SQLServer2012Platform:
                $sqlDeclaration = \sprintf('VARCHAR(255) CHECK(%s IN (%s))', $fieldDeclaration['name'], $values);

                break;
            default:
                $sqlDeclaration = \sprintf('ENUM(%s)', $values);
        }

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
        return $this->name ?: (string) \array_search(static::class, self::getTypesMap(), true);
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
        return \array_flip(static::$choices);
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
        return \array_keys(static::$choices);
    }

    /**
     * Get random value for the ENUM field.
     *
     * @static
     *
     * @return TValue
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
    public static function assertValidChoice($value): void
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
    public static function getReadableValue($value)
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
    public static function isValueExist($value): bool
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
        if ($platform instanceof MySqlPlatform) {
            return \array_merge(parent::getMappedDatabaseTypes($platform), ['enum']);
        }

        return parent::getMappedDatabaseTypes($platform);
    }
}
