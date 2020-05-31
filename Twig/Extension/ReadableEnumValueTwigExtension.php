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

namespace Fresh\DoctrineEnumBundle\Twig\Extension;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;
use Fresh\DoctrineEnumBundle\Exception\EnumType\EnumTypeIsNotRegisteredException;
use Fresh\DoctrineEnumBundle\Exception\EnumType\NoRegisteredEnumTypesException;
use Fresh\DoctrineEnumBundle\Exception\EnumValue\ValueIsFoundInFewRegisteredEnumTypesException;
use Fresh\DoctrineEnumBundle\Exception\EnumValue\ValueIsNotFoundInAnyRegisteredEnumTypeException;
use Twig\TwigFilter;

/**
 * ReadableEnumValueExtension returns the readable variant of ENUM value.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class ReadableEnumValueTwigExtension extends AbstractEnumTwigExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [new TwigFilter('readable_enum', [$this, 'getReadableEnumValue'])];
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
     */
    public function getReadableEnumValue(?string $enumValue, ?string $enumType = null): ?string
    {
        if ($this->hasRegisteredEnumTypes()) {
            if (null === $enumValue) {
                return null;
            }

            // If ENUM type was set, e.g. {{ player.position|readable_enum('BasketballPositionType') }}
            if (null !== $enumType) {
                $this->throwExceptionIfEnumTypeIsNotRegistered($enumType);

                return $this->registeredEnumTypes[$enumType]::getReadableValue($enumValue);
            }

            // If ENUM type wasn't set, e.g. {{ player.position|readable_enum }}
            $this->findOccurrences($enumValue);

            if ($this->onlyOneOccurrenceFound()) {
                $occurrence = \array_pop($this->occurrences);

                if (null !== $occurrence && \is_subclass_of($occurrence, AbstractEnumType::class)) {
                    return $occurrence::getReadableValue($enumValue);
                }
            }

            if ($this->moreThanOneOccurrenceFound()) {
                $exceptionMessage = \sprintf(
                    'Value "%s" is found in few registered ENUM types. You should manually set the appropriate one',
                    $enumValue
                );

                throw new ValueIsFoundInFewRegisteredEnumTypesException($exceptionMessage);
            }

            $exceptionMessage = \sprintf(
                'Value "%s" was not found in any registered ENUM type.',
                $enumValue
            );

            throw new ValueIsNotFoundInAnyRegisteredEnumTypeException($exceptionMessage);
        }

        throw $this->createNoRegisteredEnumTypesException();
    }

    /**
     * @param string $enumValue
     */
    private function findOccurrences(string $enumValue): void
    {
        foreach ($this->registeredEnumTypes as $registeredEnumType) {
            if ($registeredEnumType::isValueExist($enumValue)) {
                $this->occurrences[] = $registeredEnumType;
            }
        }
    }
}
