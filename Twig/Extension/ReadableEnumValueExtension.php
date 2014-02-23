<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\Bundle\DoctrineEnumBundle\Twig\Extension;

use Fresh\Bundle\DoctrineEnumBundle\Exception\EnumTypeIsNotRegisteredException;
use Fresh\Bundle\DoctrineEnumBundle\Exception\NoRegisteredEnumTypesException;
use Fresh\Bundle\DoctrineEnumBundle\Exception\ValueIsFoundInFewRegisteredEnumTypesException;
use Fresh\Bundle\DoctrineEnumBundle\Exception\ValueIsNotFoundInAnyRegisteredEnumTypeException;

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
     * @var \Fresh\Bundle\DoctrineEnumBundle\DBAL\Types\AbstractEnumType[]
     */
    protected $registeredEnumTypes = array();

    /**
     * Constructor
     *
     * @param array $registeredTypes Array of registered ENUM types
     */
    public function __construct(array $registeredTypes)
    {
        foreach ($registeredTypes as $type => $details) {
            $this->registeredEnumTypes[$type] = $details['class'];
        }
    }

    /**
     * Returns a list of filters
     *
     * @return array
     */
    public function getFilters()
    {
        return array('readable' => new \Twig_Filter_Method($this, 'getReadableEnumValue'));
    }

    /**
     * Get readable variant of ENUM value
     *
     * @param string      $enumValue ENUM value
     * @param string|null $enumType  ENUM type
     *
     * @return string
     *
     * @throws EnumTypeIsNotRegisteredException
     * @throws NoRegisteredEnumTypesException
     * @throws ValueIsFoundInFewRegisteredEnumTypesException
     * @throws ValueIsNotFoundInAnyRegisteredEnumTypeException
     */
    public function getReadableEnumValue($enumValue, $enumType = null)
    {
        if (!empty($this->registeredEnumTypes) && is_array($this->registeredEnumTypes)) {
            // If ENUM type was set, e.g. {{ player.position|readable('BasketballPositionType') }}
            if (!empty($enumType)) {
                if (!isset($this->registeredEnumTypes[$enumType])) {
                    throw new EnumTypeIsNotRegisteredException(sprintf('ENUM type "%s" is not registered', $enumType));
                }

                /** @var $enumTypeClass \Fresh\Bundle\DoctrineEnumBundle\DBAL\Types\AbstractEnumType */
                $enumTypeClass = $this->registeredEnumTypes[$enumType];

                return $enumTypeClass::getReadableValue($enumValue);
            } else {
                // If ENUM type wasn't set, e.g. {{ player.position|readable }}
                $occurrences = array();
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
     * @return string
     */
    public function getName()
    {
        return 'Readable ENUM Value';
    }
}
