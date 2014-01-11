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
    protected $name = '';

    /**
     * @var array Array of ENUM Values, where enum values are keys and their readable versions are values
     * @static
     */
    protected static $choices = [];

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (is_null($value)) {
            return null;
        }

        if (!in_array($value, $this->getValues())) {
            throw new \InvalidArgumentException(sprintf('Invalid value "%s" for ENUM %s', $value, $this->getName()));
        }

        return $value;
    }

    /**
     * {@inheritdoc}
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
     * Get readable choices for the ENUM field
     *
     * @static
     *
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
     *
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
     *
     * @return string|null Value in readable format
     *
     * @throws \InvalidArgumentException
     */
    public static function getReadableValue($value)
    {
        if (!isset(static::getChoices()[$value])) {
            $message = sprintf('Invalid value "%s" for ENUM type "%s"', $value, get_called_class());

            throw new \InvalidArgumentException($message);
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
