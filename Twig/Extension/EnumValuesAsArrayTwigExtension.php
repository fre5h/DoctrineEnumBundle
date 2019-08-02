<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fresh\DoctrineEnumBundle\Twig\Extension;

use Fresh\DoctrineEnumBundle\Exception\EnumType\EnumTypeIsNotRegisteredException;
use Fresh\DoctrineEnumBundle\Exception\EnumType\NoRegisteredEnumTypesException;
use Twig\TwigFunction;

/**
 * EnumValuesAsArrayTwigExtension.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class EnumValuesAsArrayTwigExtension extends AbstractEnumTwigExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('enum_values', [$this, 'getEnumValuesAsArray']),
            new TwigFunction('enum_readable_values', [$this, 'getReadableEnumValuesAsArray']),
        ];
    }

    /**
     * @param string $enumType
     *
     * @throws EnumTypeIsNotRegisteredException
     * @throws NoRegisteredEnumTypesException
     *
     * @return array
     */
    public function getEnumValuesAsArray(string $enumType): array
    {
        return $this->callEnumTypeStaticMethod($enumType, 'getValues');
    }

    /**
     * @param string $enumType
     *
     * @throws EnumTypeIsNotRegisteredException
     * @throws NoRegisteredEnumTypesException
     *
     * @return array
     */
    public function getReadableEnumValuesAsArray(string $enumType): array
    {
        return $this->callEnumTypeStaticMethod($enumType, 'getReadableValues');
    }

    /**
     * @param string $enumType
     * @param string $staticMethodName
     *
     * @throws EnumTypeIsNotRegisteredException
     * @throws NoRegisteredEnumTypesException
     *
     * @return array
     */
    private function callEnumTypeStaticMethod(string $enumType, string $staticMethodName): array
    {
        if ($this->hasRegisteredEnumTypes()) {
            $this->throwExceptionIfEnumTypeIsNotRegistered($enumType);

            $function = [$this->registeredEnumTypes[$enumType], $staticMethodName];

            if (!\is_callable($function)) {
                throw new \LogicException(\sprintf('%s::%s is not a valid exception', $this->registeredEnumTypes[$enumType], $staticMethodName));
            }

            return $function();
        }

        throw $this->createNoRegisteredEnumTypesException();
    }
}
