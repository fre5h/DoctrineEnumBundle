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

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->bind('$registeredTypes', '%doctrine.dbal.connection_factory.types%')
    ;

    $services->load('Fresh\DoctrineEnumBundle\Twig\Extension\\', __DIR__.'/../../Twig/Extension/');
    $services->load('Fresh\DoctrineEnumBundle\Command\\', __DIR__.'/../../Command/');
};
