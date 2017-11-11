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

use Fresh\DoctrineEnumBundle\Exception\EnumType\EnumTypeIsNotRegisteredException;
use Fresh\DoctrineEnumBundle\Exception\EnumType\NoRegisteredEnumTypesException;
use Fresh\DoctrineEnumBundle\Exception\EnumValue\ValueIsFoundInFewRegisteredEnumTypesException;
use Fresh\DoctrineEnumBundle\Exception\EnumValue\ValueIsNotFoundInAnyRegisteredEnumTypeException;

/**
 * ReadableEnumValueExtension returns the readable variant of ENUM value.
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class ReadableEnumValueExtension extends AbstractEnumExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [new \Twig_Filter('readable_enum', [$this, 'getReadableEnumValue'])];
    }

    /**
     * @param string|null $enumValue
     * @param string|null $enumType
     *
     * @return string|null
     *
     * @throws EnumTypeIsNotRegisteredException
     * @throws NoRegisteredEnumTypesException
     * @throws ValueIsFoundInFewRegisteredEnumTypesException
     * @throws ValueIsNotFoundInAnyRegisteredEnumTypeException
     * @throws \InvalidArgumentException
     */
    public function getReadableEnumValue(?string $enumValue, ?string $enumType = null): ?string
    {
        if (!empty($this->registeredEnumTypes) && \is_array($this->registeredEnumTypes)) {
            if (null === $enumValue) {
                return $enumValue;
            }
            // If ENUM type was set, e.g. {{ player.position|readable_enum('BasketballPositionType') }}
            if (null !== $enumType) {
                if (!isset($this->registeredEnumTypes[$enumType])) {
                    throw new EnumTypeIsNotRegisteredException(\sprintf('ENUM type "%s" is not registered.', $enumType));
                }

                /** @var $enumTypeClass \Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType */
                $enumTypeClass = $this->registeredEnumTypes[$enumType];

                return $enumTypeClass::getReadableValue($enumValue);
            }

            // If ENUM type wasn't set, e.g. {{ player.position|readable_enum }}
            $occurrences = [];
            // Check if value exists in registered ENUM types
            foreach ($this->registeredEnumTypes as $registeredEnumType) {
                if ($registeredEnumType::isValueExist($enumValue)) {
                    $occurrences[] = $registeredEnumType;
                }
            }

            // If found only one occurrence, then we know exactly which ENUM type
            if (1 === \count($occurrences)) {
                $enumTypeClass = \array_pop($occurrences);

                return $enumTypeClass::getReadableValue($enumValue);
            }
            if (1 < \count($occurrences)) {
                throw new ValueIsFoundInFewRegisteredEnumTypesException(\sprintf(
                    'Value "%s" is found in few registered ENUM types. You should manually set the appropriate one',
                    $enumValue
                ));
            }

            throw new ValueIsNotFoundInAnyRegisteredEnumTypeException(\sprintf(
                'Value "%s" wasn\'t found in any registered ENUM type.',
                $enumValue
            ));
        }

        throw new NoRegisteredEnumTypesException('There are no registered ENUM types.');
    }
}
