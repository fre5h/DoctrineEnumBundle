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

namespace Fresh\DoctrineEnumBundle;

use Doctrine\Persistence\ConnectionRegistry;
use Fresh\DoctrineEnumBundle\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * FreshDoctrineEnumBundle.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class FreshDoctrineEnumBundle extends Bundle
{
    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function boot(): void
    {
        parent::boot();

        $doctrine = $this->container->get('doctrine', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        if (!$doctrine instanceof ConnectionRegistry) {
            throw new InvalidArgumentException('Service "doctrine" is missed in container');
        }

        /** @var \Doctrine\DBAL\Connection $connection */
        foreach ($doctrine->getConnections() as $connection) {
            /** @var \Doctrine\DBAL\Platforms\AbstractPlatform $databasePlatform */
            $databasePlatform = $connection->getDatabasePlatform();

            if (!$databasePlatform->hasDoctrineTypeMappingFor('enum') || 'string' !== $databasePlatform->getDoctrineTypeMapping('enum')) {
                $databasePlatform->registerDoctrineTypeMapping('enum', 'string');
            }
        }
    }
}
