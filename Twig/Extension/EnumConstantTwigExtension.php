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
use Fresh\DoctrineEnumBundle\Exception\Constant\ConstantIsFoundInFewRegisteredEnumTypesException;
use Fresh\DoctrineEnumBundle\Exception\Constant\ConstantIsNotFoundInAnyRegisteredEnumTypeException;
use Fresh\DoctrineEnumBundle\Exception\EnumType\EnumTypeIsNotRegisteredException;
use Fresh\DoctrineEnumBundle\Exception\EnumType\NoRegisteredEnumTypesException;
use Twig\TwigFilter;

/**
 * EnumConstantExtension.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class EnumConstantTwigExtension extends AbstractEnumTwigExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [new TwigFilter('enum_constant', [$this, 'getEnumConstant'])];
    }

    /**
     * @param string      $enumConstant
     * @param string|null $enumType
     *
     * @return string
     *
     * @throws EnumTypeIsNotRegisteredException
     * @throws NoRegisteredEnumTypesException
     * @throws ConstantIsFoundInFewRegisteredEnumTypesException
     * @throws ConstantIsNotFoundInAnyRegisteredEnumTypeException
     */
    public function getEnumConstant(string $enumConstant, ?string $enumType = null): string
    {
        if ($this->hasRegisteredEnumTypes()) {
            // If ENUM type was set, e.g. {{ 'CENTER'|enum_constant('BasketballPositionType') }}
            if (null !== $enumType) {
                $this->throwExceptionIfEnumTypeIsNotRegistered($enumType);

                if (\is_scalar($this->registeredEnumTypes[$enumType])) {
                    $result = \constant(implode('::', [(string) $this->registeredEnumTypes[$enumType], $enumConstant]));
                    if (\is_scalar($result)) {
                        return (string) $result;
                    }
                }
            }

            // If ENUM type wasn't set, e.g. {{ 'CENTER'|enum_constant }}
            $this->findOccurrences($enumConstant);

            if ($this->onlyOneOccurrenceFound() && ($firstOccurrence = array_pop($this->occurrences)) && \is_scalar($firstOccurrence)) {
                $result = \constant(implode('::', [(string) $firstOccurrence, $enumConstant]));
                if (\is_scalar($result)) {
                    return (string) $result;
                }
            }

            if ($this->moreThanOneOccurrenceFound()) {
                $exceptionMessage = \sprintf(
                    'Constant "%s" is found in few registered ENUM types. You should manually set the appropriate one.',
                    $enumConstant,
                );

                throw new ConstantIsFoundInFewRegisteredEnumTypesException($exceptionMessage);
            }

            $exceptionMessage = \sprintf(
                'Constant "%s" was not found in any registered ENUM type.',
                $enumConstant
            );

            throw new ConstantIsNotFoundInAnyRegisteredEnumTypeException($exceptionMessage);
        }

        throw $this->createNoRegisteredEnumTypesException();
    }

    /**
     * @param string $enumConstant
     *
     * @throws \ReflectionException
     */
    private function findOccurrences(string $enumConstant): void
    {
        /** @var class-string<AbstractEnumType<int|string, int|string>> $registeredEnumType */
        foreach ($this->registeredEnumTypes as $registeredEnumType) {
            $reflection = new \ReflectionClass($registeredEnumType);

            if ($reflection->hasConstant($enumConstant)) {
                $this->occurrences[] = $registeredEnumType;
            }
        }
    }
}
