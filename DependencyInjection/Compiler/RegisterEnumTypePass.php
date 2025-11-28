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

namespace Fresh\DoctrineEnumBundle\DependencyInjection\Compiler;

use Doctrine\DBAL\Types\EnumType;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use Fresh\DoctrineEnumBundle\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * RegisterEnumTypePass.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class RegisterEnumTypePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $doctrine = $container->get('doctrine', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        if (!$doctrine instanceof ManagerRegistry) {
            throw new InvalidArgumentException('Service "doctrine" is missed in container');
        }

        /* @see \Doctrine\Bundle\DoctrineBundle\ConnectionFactory::createConnection */
        foreach ($doctrine->getConnectionNames() as $connectionName) {
            $definition = $container->getDefinition($connectionName);
            $lastArgument = \count($definition->getArguments()) - 1;
            $mappingTypes = (array) $definition->getArgument($lastArgument);
            $expectedType = class_exists(EnumType::class) ? Types::ENUM : 'string';
            if (!isset($mappingTypes['enum']) || $expectedType !== $mappingTypes['enum']) {
                $mappingTypes['enum'] = $expectedType;
                $definition->setArgument($lastArgument, $mappingTypes);
            }
        }
    }
}
