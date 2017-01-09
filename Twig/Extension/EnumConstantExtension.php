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

use Fresh\DoctrineEnumBundle\Exception\ConstantIsFoundInFewRegisteredEnumTypesException;
use Fresh\DoctrineEnumBundle\Exception\ConstantIsNotFoundInAnyRegisteredEnumTypeException;
use Fresh\DoctrineEnumBundle\Exception\EnumTypeIsNotRegisteredException;
use Fresh\DoctrineEnumBundle\Exception\NoRegisteredEnumTypesException;

/**
 * EnumConstantExtension.
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class EnumConstantExtension extends AbstractEnumExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [new \Twig_SimpleFilter('enum_constant', [$this, 'getEnumConstant'])];
    }

    /**
     * @param string      $enumConstant
     * @param string|null $enumType
     *
     * @throws EnumTypeIsNotRegisteredException
     * @throws NoRegisteredEnumTypesException
     * @throws ConstantIsFoundInFewRegisteredEnumTypesException
     * @throws ConstantIsNotFoundInAnyRegisteredEnumTypeException
     *
     * @return string
     */
    public function getEnumConstant($enumConstant, $enumType = null)
    {
        if (!empty($this->registeredEnumTypes) && is_array($this->registeredEnumTypes)) {
            // If ENUM type was set, e.g. {{ 'CENTER'|enum_constant('BasketballPositionType') }}
            if (!empty($enumType)) {
                if (!isset($this->registeredEnumTypes[$enumType])) {
                    throw new EnumTypeIsNotRegisteredException(sprintf('ENUM type "%s" is not registered.', $enumType));
                }

                return constant($this->registeredEnumTypes[$enumType].'::'.$enumConstant);
            } else {
                // If ENUM type wasn't set, e.g. {{ 'CENTER'|enum_constant }}
                $occurrences = [];
                // Check if constant exists in registered ENUM types
                foreach ($this->registeredEnumTypes as $registeredEnumType) {
                    $reflection = new \ReflectionClass($registeredEnumType);
                    if ($reflection->hasConstant($enumConstant)) {
                        $occurrences[] = $registeredEnumType;
                    }
                }

                // If found only one occurrence, then we know exactly which ENUM type
                if (1 == count($occurrences)) {
                    $enumClassName = array_pop($occurrences);

                    return constant($enumClassName.'::'.$enumConstant);
                } elseif (1 < count($occurrences)) {
                    throw new ConstantIsFoundInFewRegisteredEnumTypesException(sprintf(
                        'Constant "%s" is found in few registered ENUM types. You should manually set the appropriate one.',
                        $enumConstant
                    ));
                } else {
                    throw new ConstantIsNotFoundInAnyRegisteredEnumTypeException(sprintf(
                        'Constant "%s" wasn\'t found in any registered ENUM type.',
                        $enumConstant
                    ));
                }
            }
        } else {
            throw new NoRegisteredEnumTypesException('There are no registered ENUM types.');
        }
    }
}
