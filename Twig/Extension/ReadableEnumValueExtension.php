<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle\Twig\Extension;

use Fresh\DoctrineEnumBundle\Exception\EnumTypeIsNotRegisteredException;
use Fresh\DoctrineEnumBundle\Exception\NoRegisteredEnumTypesException;
use Fresh\DoctrineEnumBundle\Exception\ValueIsFoundInFewRegisteredEnumTypesException;
use Fresh\DoctrineEnumBundle\Exception\ValueIsNotFoundInAnyRegisteredEnumTypeException;

/**
 * ReadableEnumValueExtension returns the readable variant of ENUM value
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class ReadableEnumValueExtension extends \Twig_Extension
{
    /**
     * Array of registered ENUM types
     *
     * @var \Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType[] $registeredEnumTypes Registered ENUM types
     */
    protected $registeredEnumTypes = [];

    /**
     * Constructor
     *
     * @param array $registeredTypes Array of registered ENUM types
     */
    public function __construct(array $registeredTypes)
    {
        foreach ($registeredTypes as $type => $details) {
            if (is_subclass_of($details['class'], '\Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType')) {
                $this->registeredEnumTypes[$type] = $details['class'];
            }
        }
    }

    /**
     * Returns a list of filters
     *
     * @return array
     */
    public function getFilters()
    {
        return ['readable' => new \Twig_Filter_Method($this, 'getReadableEnumValue')];
    }

    /**
     * Get readable variant of ENUM value
     *
     * @param string      $enumValue ENUM value
     * @param string|null $enumType  ENUM type
     *
     * @throws EnumTypeIsNotRegisteredException
     * @throws NoRegisteredEnumTypesException
     * @throws ValueIsFoundInFewRegisteredEnumTypesException
     * @throws ValueIsNotFoundInAnyRegisteredEnumTypeException
     *
     * @return string
     */
    public function getReadableEnumValue($enumValue, $enumType = null)
    {
        if (!empty($this->registeredEnumTypes) && is_array($this->registeredEnumTypes)) {
            // If ENUM type was set, e.g. {{ player.position|readable('BasketballPositionType') }}
            if (!empty($enumType)) {
                if (!isset($this->registeredEnumTypes[$enumType])) {
                    throw new EnumTypeIsNotRegisteredException(sprintf('ENUM type "%s" is not registered', $enumType));
                }

                /** @var $enumTypeClass \Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType */
                $enumTypeClass = $this->registeredEnumTypes[$enumType];

                return $enumTypeClass::getReadableValue($enumValue);
            } else {
                // If ENUM type wasn't set, e.g. {{ player.position|readable }}
                $occurrences = [];
                // Check if value exists in registered ENUM types
                foreach ($this->registeredEnumTypes as $registeredEnumType) {
                    if ($registeredEnumType::isValueExist($enumValue)) {
                        $occurrences[] = $registeredEnumType;
                    }
                }

                // If found only one occurrence, then we know exactly which ENUM type
                if (count($occurrences) == 1) {
                    $enumTypeClass = array_pop($occurrences);

                    return $enumTypeClass::getReadableValue($enumValue);
                } elseif (count($occurrences) > 1) {
                    $message = sprintf(
                        'Value "%s" is found in few registered ENUM types. You should manually set the appropriate one',
                        $enumValue
                    );

                    throw new ValueIsFoundInFewRegisteredEnumTypesException($message);
                } else {
                    $message = sprintf('Value "%s" wasn\'t found in any registered ENUM type', $enumValue);

                    throw new ValueIsNotFoundInAnyRegisteredEnumTypeException($message);
                }
            }
        } else {
            throw new NoRegisteredEnumTypesException('There are no registered ENUM types');
        }
    }

    /**
     * Get name of this extension
     *
     * @return string Name of extension
     */
    public function getName()
    {
        return 'Readable ENUM Value';
    }
}
