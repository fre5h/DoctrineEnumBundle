<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use Doctrine\DBAL\Types\Type;
use Fresh\DoctrineEnumBundle\Util\LegacyFormHelper;

/**
 * AbstractEnumType.
 *
 * Provides support of MySQL ENUM type for Doctrine in Symfony applications.
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 * @author Ben Davies <ben.davies@gmail.com>
 * @author Jaik Dean <jaik@fluoresce.co>
 */
abstract class AbstractEnumType extends Type
{
    /**
     * @var string $name Name of this type
     */
    protected $name = '';

    /**
     * @var array $choices Array of ENUM Values, where ENUM values are keys and their readable versions are values
     * @static
     */
    protected static $choices = [];

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        if (!isset(static::$choices[$value])) {
            throw new \InvalidArgumentException(sprintf('Invalid value "%s" for ENUM "%s".', $value, $this->getName()));
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $values = implode(
            ', ',
            array_map(
                function ($value) {
                    return "'{$value}'";
                },
                static::getValues()
            )
        );

        if ($platform instanceof SqlitePlatform) {
            return sprintf('TEXT CHECK(%s IN (%s))', $fieldDeclaration['name'], $values);
        }

        if ($platform instanceof PostgreSqlPlatform || $platform instanceof SQLServerPlatform) {
            return sprintf('VARCHAR(255) CHECK(%s IN (%s))', $fieldDeclaration['name'], $values);
        }

        return sprintf('ENUM(%s)', $values);
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name ?: (new \ReflectionClass(get_class($this)))->getShortName();
    }

    /**
     * Get readable choices for the ENUM field.
     *
     * @static
     *
     * @return array Values for the ENUM field
     */
    public static function getChoices()
    {
        // Compatibility with Symfony <3.0
        if (LegacyFormHelper::isLegacy()) {
            return static::$choices;
        }

        return array_flip(static::$choices);
    }

    /**
     * Get values for the ENUM field.
     *
     * @static
     *
     * @return array Values for the ENUM field
     */
    public static function getValues()
    {
        return array_keys(static::$choices);
    }

    /**
     * Get value in readable format.
     *
     * @param string $value ENUM value
     *
     * @static
     *
     * @return string|null $value Value in readable format
     *
     * @throws \InvalidArgumentException
     */
    public static function getReadableValue($value)
    {
        if (!isset(static::$choices[$value])) {
            throw new \InvalidArgumentException(sprintf('Invalid value "%s" for ENUM type "%s".', $value, get_called_class()));
        }

        return static::$choices[$value];
    }

    /**
     * Check if some string value exists in the array of ENUM values.
     *
     * @param string $value ENUM value
     *
     * @return bool
     */
    public static function isValueExist($value)
    {
        return isset(static::$choices[$value]);
    }
}
