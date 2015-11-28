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

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;
use Fresh\DoctrineEnumBundle\Exception\EnumTypeIsNotRegisteredException;
use Fresh\DoctrineEnumBundle\Exception\NoRegisteredEnumTypesException;
use Fresh\DoctrineEnumBundle\Exception\ConstantIsFoundInFewRegisteredEnumTypesException;
use Fresh\DoctrineEnumBundle\Exception\ConstantIsNotFoundInAnyRegisteredEnumTypeException;

/**
 * EnumValueExtension returns the readable variant of ENUM value
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class EnumValueExtension extends \Twig_Extension
{
    /**
     * @var AbstractEnumType[] $registeredEnumTypes Array of registered ENUM types
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
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return ['enum' => new \Twig_Filter_Method($this, 'getEnumValue')];
    }

    /**
     * Get variant of ENUM value
     *
     * @param string      $enumConst ENUM value
     * @param string|null $enumType  ENUM type
     *
     * @throws EnumTypeIsNotRegisteredException
     * @throws NoRegisteredEnumTypesException
     * @throws ConstantIsFoundInFewRegisteredEnumTypesException
     * @throws ConstantIsNotFoundInAnyRegisteredEnumTypeException
     *
     * @return string
     */
    public function getEnumValue($enumConst, $enumType=null)
    {
        if (!empty($this->registeredEnumTypes) && is_array($this->registeredEnumTypes)) {
            // If ENUM type was set, e.g. {{ player.position|readable('BasketballPositionType') }}
            if (!empty($enumType)) {
                if (!isset($this->registeredEnumTypes[$enumType])) {
                    throw new EnumTypeIsNotRegisteredException(sprintf(
                        'ENUM type "%s" is not registered',
                        $enumType
                    ));
                }

                return constant($this->registeredEnumTypes[$enumType].'::'.$enumConst);
            } else {
                // If ENUM type wasn't set, e.g. {{ player.position|readable }}
                $occurrences = [];
                // Check if value exists in registered ENUM types
                foreach ($this->registeredEnumTypes as $registeredEnumType) {
                    $refl = new \ReflectionClass($registeredEnumType);
                    if ($refl->hasConstant($enumConst)) {
                        $occurrences[] = $registeredEnumType;
                    }
                }

                // If found only one occurrence, then we know exactly which ENUM type
                if (count($occurrences) == 1) {
                    $enumClassName = array_pop($occurrences);
                    return constant($enumClassName.'::'.$enumConst);
                } elseif (count($occurrences) > 1) {
                    throw new ConstantIsFoundInFewRegisteredEnumTypesException(sprintf(
                        'Constant "%s" is found in few registered ENUM types. You should manually set the appropriate one',
                        $enumConst
                    ));
                } else {
                    throw new ConstantIsNotFoundInAnyRegisteredEnumTypeException(sprintf(
                        'Constant "%s" wasn\'t found in any registered ENUM type',
                        $enumConst
                    ));
                }
            }
        } else {
            throw new NoRegisteredEnumTypesException('There are no registered ENUM types');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ENUM Value';
    }
}
