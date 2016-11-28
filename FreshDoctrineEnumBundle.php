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
     */
    public function boot()
    {
        parent::boot();

        /** @var Registry $doctrine */
        $doctrine = $this->container->get('doctrine');

        foreach ($doctrine->getConnections() as $connection) {
            /** @var AbstractPlatform $databasePlatform */
            $databasePlatform = $connection->getDatabasePlatform();

            if (!$databasePlatform->hasDoctrineTypeMappingFor('enum') || 'string' !== $databasePlatform->getDoctrineTypeMapping('enum')) {
                $databasePlatform->registerDoctrineTypeMapping('enum', 'string');
            }
        }
    }
}
