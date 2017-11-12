<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * FreshDoctrineEnumBundle.
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class FreshDoctrineEnumBundle extends Bundle
{
    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function boot()
    {
        parent::boot();

        $doctrine = $this->container->get(Registry::class);

        /** @var \Doctrine\DBAL\Connection $connection */
        foreach ($doctrine->getConnections() as $connection) {
            /** @var AbstractPlatform $databasePlatform */
            $databasePlatform = $connection->getDatabasePlatform();

            if (!$databasePlatform->hasDoctrineTypeMappingFor('enum') || 'string' !== $databasePlatform->getDoctrineTypeMapping('enum')) {
                $databasePlatform->registerDoctrineTypeMapping('enum', 'string');
            }
        }
    }
}
