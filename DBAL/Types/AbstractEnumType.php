<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\Bundle\DoctrineEnumBundle\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * AbstractEnumType
 *
 * Provides support of MySQL ENUM type for Doctrine in Symfony applications
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
abstract class AbstractEnumType extends Type
{
    /**
     * @var string Name of this type
     */
    protected $name = 'AbstractEnumType';

    /**
     * @var array Array of ENUM Values, where enum values are keys and their readable versions are values
     * @static
     */
    protected static $choices = [];

    /**
     * Convert a value from its PHP representation to its database representation of this type
     *
     * @param mixed            $value    The value to convert
     * @param AbstractPlatform $platform The currently used database platform
     *
     * @throws \InvalidArgumentException
     * @return mixed The database representation of the value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!in_array($value, $this->getValues())) {
            throw new \InvalidArgumentException(sprintf('Invalid value "%s" for ENUM %s', $value, $this->getName()));
        }

        return $value;
    }

    /**
     * Get the SQL declaration snippet for a field of this type
     *
     * @param array            $fieldDeclaration The field declaration
     * @param AbstractPlatform $platform         The currently used database platform
     *
     * @return string The SQL declaration snippet
     */
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $values = array_map(
            function ($value) {
                return "'{$value}'";
            },
            $this->getValues()
        );

        return 'ENUM(' . implode(', ', $values) . ')';
    }

    /**
     * Get the name of this type
     *
     * @return string Name of this type
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get readable choices for the ENUM field
     *
     * @static
     * @return array Values for the ENUM field
     */
    public static function getChoices()
    {
        return static::$choices;
    }

    /**
     * Get values for the ENUM field
     *
     * @static
     * @return array Values for the ENUM field
     */
    public static function getValues()
    {
        return array_keys(static::getChoices());
    }

    /**
     * Get value in readable format
     *
     * @param string $value ENUM value
     *
     * @static
     * @return string|null Value in readable format
     *
     * @throws \InvalidArgumentException
     */
    public static function getReadableValue($value)
    {
        if (!isset(static::getChoices()[$value])) {
            throw new \InvalidArgumentException(sprintf('Invalid value "%s" for ENUM type "%s"', $value, get_called_class()));
        }

        return static::getChoices()[$value];
    }

    /**
     * Check if some string value exists in the array of ENUM values
     *
     * @param string $value ENUM value
     *
     * @return bool
     */
    public static function isValueExist($value)
    {
        return in_array($value, static::getValues());
    }
}
