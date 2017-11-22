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

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;
use Fresh\DoctrineEnumBundle\Exception\EnumType\EnumTypeIsNotRegisteredException;
use Fresh\DoctrineEnumBundle\Exception\EnumType\NoRegisteredEnumTypesException;
use Twig\Extension\AbstractExtension;

/**
 * AbstractEnumTwigExtension.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
abstract class AbstractEnumTwigExtension extends AbstractExtension
{
    /** @var AbstractEnumType[] */
    protected $registeredEnumTypes = [];

    /** @var array */
    protected $occurrences = [];

    /**
     * @param array $registeredTypes
     */
    public function __construct(array $registeredTypes)
    {
        foreach ($registeredTypes as $type => $details) {
            if (\is_subclass_of($details['class'], AbstractEnumType::class)) {
                $this->registeredEnumTypes[$type] = $details['class'];
            }
        }
    }

    /**
     * @return bool
     */
    protected function hasRegisteredEnumTypes(): bool
    {
        return !empty($this->registeredEnumTypes) && \is_array($this->registeredEnumTypes);
    }

    /**
     * @return bool
     */
    protected function onlyOneOccurrenceFound(): bool
    {
        return 1 === \count($this->occurrences);
    }

    /**
     * @return bool
     */
    protected function moreThanOneOccurrenceFound(): bool
    {
        return 1 < \count($this->occurrences);
    }

    /**
     * @return NoRegisteredEnumTypesException
     */
    protected function createNoRegisteredEnumTypesException(): NoRegisteredEnumTypesException
    {
        return new NoRegisteredEnumTypesException('There are no registered ENUM types.');
    }

    /**
     * @param string $enumType
     *
     * @throws EnumTypeIsNotRegisteredException
     */
    protected function throwExceptionIfEnumTypeIsNotRegistered(string $enumType): void
    {
        if (!isset($this->registeredEnumTypes[$enumType])) {
            throw new EnumTypeIsNotRegisteredException(\sprintf('ENUM type "%s" is not registered.', $enumType));
        }
    }
}
