<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fresh\DoctrineEnumBundle\Twig\Extension;

use Fresh\DoctrineEnumBundle\Exception\Constant\ConstantIsFoundInFewRegisteredEnumTypesException;
use Fresh\DoctrineEnumBundle\Exception\Constant\ConstantIsNotFoundInAnyRegisteredEnumTypeException;
use Fresh\DoctrineEnumBundle\Exception\EnumType\EnumTypeIsNotRegisteredException;
use Fresh\DoctrineEnumBundle\Exception\EnumType\NoRegisteredEnumTypesException;

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
    public function getFilters(): array
    {
        return [new \Twig_Filter('enum_constant', [$this, 'getEnumConstant'])];
    }

    /**
     * @param string|null $enumConstant
     * @param string|null $enumType
     *
     * @throws EnumTypeIsNotRegisteredException
     * @throws NoRegisteredEnumTypesException
     * @throws ConstantIsFoundInFewRegisteredEnumTypesException
     * @throws ConstantIsNotFoundInAnyRegisteredEnumTypeException
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     *
     * @return string
     */
    public function getEnumConstant(?string $enumConstant, ?string $enumType = null): string
    {
        if (!empty($this->registeredEnumTypes) && \is_array($this->registeredEnumTypes)) {
            // If ENUM type was set, e.g. {{ 'CENTER'|enum_constant('BasketballPositionType') }}
            if (null !== $enumType) {
                if (!isset($this->registeredEnumTypes[$enumType])) {
                    throw new EnumTypeIsNotRegisteredException(\sprintf('ENUM type "%s" is not registered.', $enumType));
                }

                return \constant($this->registeredEnumTypes[$enumType].'::'.$enumConstant);
            }

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
            if (1 === \count($occurrences)) {
                $enumClassName = \array_pop($occurrences);

                return \constant($enumClassName.'::'.$enumConstant);
            }
            if (1 < \count($occurrences)) {
                throw new ConstantIsFoundInFewRegisteredEnumTypesException(\sprintf(
                    'Constant "%s" is found in few registered ENUM types. You should manually set the appropriate one.',
                    $enumConstant
                ));
            }

            throw new ConstantIsNotFoundInAnyRegisteredEnumTypeException(\sprintf(
                'Constant "%s" wasn\'t found in any registered ENUM type.',
                $enumConstant
            ));
        }

        throw new NoRegisteredEnumTypesException('There are no registered ENUM types.');
    }
}
